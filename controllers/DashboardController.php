<?php
require_once __DIR__ . '/../models/Karyawan.php';
require_once __DIR__ . '/../models/Kriteria.php';
require_once __DIR__ . '/../models/Penilaian.php';
require_once __DIR__ . '/../models/TopsisCalculator.php';

class DashboardController {
    private $pdo;
    private $role;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->role = $_SESSION['user']['role'] ?? '';
    }

    public function index() {
        $page_title = 'Dashboard';
        $karyawanModel = new Karyawan($this->pdo);
        $kriteriaModel = new Kriteria($this->pdo);
        $penilaianModel = new Penilaian($this->pdo);

        $allKaryawan = $karyawanModel->getAllWithNonaktif();
        $totalKaryawan = count(array_filter($allKaryawan, fn($k) => ($k['status'] ?? 'aktif') === 'aktif'));
        $totalKriteria = count($kriteriaModel->getAll());
        $periodeList = $penilaianModel->getDistinctPeriode();
        $periodeTerakhir = !empty($periodeList) ? $periodeList[0]['periode_bulan'] : null;

        $karyawan = $karyawanModel->getAll();
        $kriteria = $kriteriaModel->getAll();
        $top5 = [];
        $chartData = [];

        if ($periodeTerakhir && !empty($karyawan) && !empty($kriteria)) {
            $bobot = array_column($kriteria, 'bobot');
            $atribut = array_column($kriteria, 'atribut');
            $kriteriaIds = array_column($kriteria, 'id');
            $matriks = [];
            $karyawanIds = [];

            foreach ($karyawan as $k) {
                $karyawanIds[] = $k['id'];
                $row = [];
                foreach ($kriteriaIds as $kid) {
                    $nilai = $penilaianModel->getNilai($k['id'], $kid, $periodeTerakhir);
                    $row[] = $nilai !== null ? (float)$nilai : 0;
                }
                $matriks[] = $row;
            }

            $topsis = new TopsisCalculator($matriks, $bobot, $atribut);
            $topsis->hitung();
            $ranking = $topsis->getRanking($karyawanIds);
            $top5 = array_slice($ranking, 0, 5);

            foreach ($top5 as &$item) {
                $kary = $karyawanModel->getById($item['id_karyawan']);
                $item['nama'] = $kary['nama'] ?? '-';
                $item['jabatan'] = $kary['jabatan'] ?? '';
            }
            unset($item);

            // Get Best and Worst
            $bestKaryawan = null;
            $worstKaryawan = null;
            if (!empty($ranking)) {
                $best = $ranking[0];
                $kBest = $karyawanModel->getById($best['id_karyawan']);
                $bestKaryawan = [
                    'nama' => $kBest['nama'] ?? '-',
                    'jabatan' => $kBest['jabatan'] ?? '',
                    'nilai' => $best['nilai']
                ];

                // Remove zero scores from worst consideration to avoid unfair punishment if no data
                $nonZeroRanking = array_filter($ranking, fn($r) => $r['nilai'] > 0);
                if (!empty($nonZeroRanking)) {
                    $worst = end($nonZeroRanking);
                    $kWorst = $karyawanModel->getById($worst['id_karyawan']);
                    $worstKaryawan = [
                        'nama' => $kWorst['nama'] ?? '-',
                        'jabatan' => $kWorst['jabatan'] ?? '',
                        'nilai' => $worst['nilai']
                    ];
                }
            }

            foreach ($kriteria as $kr) {
                $totalNilai = 0;
                $count = 0;
                foreach ($karyawan as $k) {
                    $nilai = $penilaianModel->getNilai($k['id'], $kr['id'], $periodeTerakhir);
                    if ($nilai !== null) {
                        $totalNilai += (float)$nilai;
                        $count++;
                    }
                }
                $chartData[] = [
                    'kriteria' => $kr['nama_kriteria'],
                    'rata' => $count > 0 ? $totalNilai / $count : 0
                ];
            }
        }

        include __DIR__ . '/../views/dashboard.php';
    }
}