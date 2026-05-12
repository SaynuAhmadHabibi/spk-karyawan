<?php
require_once __DIR__ . '/../models/Karyawan.php';
require_once __DIR__ . '/../models/Kriteria.php';
require_once __DIR__ . '/../models/Penilaian.php';
require_once __DIR__ . '/../models/TopsisCalculator.php';

class TopsisController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Halaman form pilih periode untuk punishment
    public function punishmentForm() {
        $page_title = 'Hitung Punishment - Pilih Periode';
        $periodeList = (new Penilaian($this->pdo))->getDistinctPeriode();
        include __DIR__ . '/../views/topsis/pilih_periode_punishment.php';
    }

    // Halaman form pilih periode untuk reward
    public function rewardForm() {
        $page_title = 'Hitung Reward - Pilih Periode Akhir';
        $periodeList = (new Penilaian($this->pdo))->getDistinctPeriode();
        include __DIR__ . '/../views/topsis/pilih_periode_reward.php';
    }

    /**
     * Normalisasi format periode ke YYYY-MM-DD
     * Mendukung input: YYYY-MM, YYYY-MM-DD
     * @return string|false Format YYYY-MM-DD atau false jika invalid
     */
    private function normalizePeriode($input) {
        // Format YYYY-MM → tambahkan -01
        if (preg_match('/^\d{4}-\d{2}$/', $input)) {
            return $input . '-01';
        }
        // Format YYYY-MM-DD → sudah benar
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $input)) {
            return $input;
        }
        return false;
    }

    /**
     * Bangun matriks keputusan dari data penilaian
     * @return array ['matriks' => [], 'karyawanIds' => [], 'dataFound' => int]
     */
    private function buildMatriks($karyawan, $kriteriaIds, $penilaianModel, $periode) {
        $matriks = [];
        $karyawanIds = [];
        $dataFound = 0;

        foreach ($karyawan as $k) {
            $karyawanIds[] = $k['id'];
            $row = [];
            foreach ($kriteriaIds as $kid) {
                $nilai = $penilaianModel->getNilai($k['id'], $kid, $periode);
                $val = ($nilai !== null && $nilai !== false) ? (float)$nilai : 0;
                if ($val != 0) $dataFound++;
                $row[] = $val;
            }
            $matriks[] = $row;
        }

        return [
            'matriks' => $matriks,
            'karyawanIds' => $karyawanIds,
            'dataFound' => $dataFound
        ];
    }

    /**
     * Proses hitung punishment dengan periode tertentu (1 bulan)
     */
    public function punishment() {
        $periodeRaw = $_GET['periode'] ?? date('Y-m', strtotime('-1 month'));
        $periode = $this->normalizePeriode($periodeRaw);
        
        if ($periode === false) {
            $_SESSION['error'] = 'Format periode tidak valid. Gunakan format YYYY-MM.';
            header('Location: index.php?act=hitung_punishment_form');
            exit;
        }

        $karyawanModel = new Karyawan($this->pdo);
        $kriteriaModel = new Kriteria($this->pdo);
        $penilaianModel = new Penilaian($this->pdo);

        $karyawan = $karyawanModel->getAll();
        $kriteria = $kriteriaModel->getAll();

        // Validasi data master
        if (empty($karyawan) || empty($kriteria)) {
            $missingKaryawan = empty($karyawan);
            $missingKriteria = empty($kriteria);
            include __DIR__ . '/../views/topsis/need_data.php';
            return;
        }

        $bobot = array_column($kriteria, 'bobot');
        $atribut = array_column($kriteria, 'atribut');
        $kriteriaIds = array_column($kriteria, 'id');

        // Bangun matriks keputusan
        $result = $this->buildMatriks($karyawan, $kriteriaIds, $penilaianModel, $periode);
        $matriks = $result['matriks'];
        $karyawanIds = $result['karyawanIds'];
        $dataFound = $result['dataFound'];

        // Debug info: log jumlah data yang ditemukan
        $debugInfo = [
            'periode_input' => $periodeRaw,
            'periode_normalized' => $periode,
            'jumlah_karyawan' => count($karyawan),
            'jumlah_kriteria' => count($kriteria),
            'data_penilaian_ditemukan' => $dataFound,
            'total_sel_matriks' => count($karyawan) * count($kriteria)
        ];

        // Peringatan jika tidak ada data penilaian
        if ($dataFound === 0) {
            $debugInfo['WARNING'] = 'Tidak ada data penilaian untuk periode ini! Semua nilai = 0.';
        }

        // Hitung TOPSIS
        $topsis = new TopsisCalculator($matriks, $bobot, $atribut);
        $detail = $topsis->hitung();
        $ranking = $topsis->getRanking($karyawanIds);

        // Simpan ke session
        $_SESSION['hasil_punishment'] = [
            'periode' => $periode,
            'ranking' => $ranking,
            'detail' => $detail,
            'karyawan' => $karyawan,
            'kriteria' => $kriteria,
            'bobot' => $bobot,
            'debug_info' => $debugInfo
        ];

        header('Location: index.php?act=hasil_punishment');
        exit;
    }

    /**
     * Proses hitung reward dengan periode akhir tertentu (6 bulan rata-rata)
     */
    public function reward() {
        $tanggalAkhirRaw = $_GET['periode'] ?? date('Y-m');
        $tanggalAkhir = $this->normalizePeriode($tanggalAkhirRaw);
        
        if ($tanggalAkhir === false) {
            $_SESSION['error'] = 'Format periode tidak valid.';
            header('Location: index.php?act=hitung_reward_form');
            exit;
        }
        
        $mulai = date('Y-m-01', strtotime('-5 months', strtotime($tanggalAkhir)));

        $karyawanModel = new Karyawan($this->pdo);
        $kriteriaModel = new Kriteria($this->pdo);
        $penilaianModel = new Penilaian($this->pdo);

        $karyawan = $karyawanModel->getAll();
        $kriteria = $kriteriaModel->getAll();

        if (empty($karyawan) || empty($kriteria)) {
            $missingKaryawan = empty($karyawan);
            $missingKriteria = empty($kriteria);
            include __DIR__ . '/../views/topsis/need_data.php';
            return;
        }

        $bobot = array_column($kriteria, 'bobot');
        $atribut = array_column($kriteria, 'atribut');
        $kriteriaIds = array_column($kriteria, 'id');

        $matriks = [];
        $karyawanIds = [];
        foreach ($karyawan as $k) {
            $karyawanIds[] = $k['id'];
            $row = [];
            foreach ($kriteriaIds as $kid) {
                $rata = $penilaianModel->getRataNilaiPeriode($k['id'], $kid, $mulai, $tanggalAkhir);
                $row[] = $rata;
            }
            $matriks[] = $row;
        }

        $topsis = new TopsisCalculator($matriks, $bobot, $atribut);
        $detail = $topsis->hitung();
        $ranking = $topsis->getRanking($karyawanIds);

        $_SESSION['hasil_reward'] = [
            'periode_mulai' => $mulai,
            'periode_akhir' => $tanggalAkhir,
            'ranking' => $ranking,
            'detail' => $detail,
            'karyawan' => $karyawan,
            'kriteria' => $kriteria,
            'bobot' => $bobot
        ];

        header('Location: index.php?act=hasil_reward');
        exit;
    }

    public function hasilPunishment() {
        if (!isset($_SESSION['hasil_punishment'])) {
            $_SESSION['error'] = 'Belum ada data punishment. Hitung terlebih dahulu.';
            header('Location: index.php?act=hitung_punishment_form');
            exit;
        }
        $page_title = 'Hasil Punishment';
        $data = $_SESSION['hasil_punishment'];
        include __DIR__ . '/../views/topsis/hasil_punishment.php';
    }

    public function hasilReward() {
        if (!isset($_SESSION['hasil_reward'])) {
            $_SESSION['error'] = 'Belum ada data reward. Hitung terlebih dahulu.';
            header('Location: index.php?act=hitung_reward_form');
            exit;
        }
        $page_title = 'Hasil Reward';
        $data = $_SESSION['hasil_reward'];
        include __DIR__ . '/../views/topsis/hasil_reward.php';
    }

    public function detailPerhitungan($tipe) {
        $key = 'hasil_' . $tipe;
        if (!isset($_SESSION[$key])) {
            $_SESSION['error'] = 'Data tidak tersedia.';
            header('Location: index.php?act=dashboard');
            exit;
        }
        $page_title = 'Detail Perhitungan TOPSIS - ' . ucfirst($tipe);
        $data = $_SESSION[$key];
        $data['tipe'] = $tipe;
        include __DIR__ . '/../views/topsis/detail_perhitungan.php';
    }
}