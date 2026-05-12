<?php
require_once __DIR__ . '/../models/Penilaian.php';
require_once __DIR__ . '/../models/Karyawan.php';
require_once __DIR__ . '/../models/Kriteria.php';
require_once __DIR__ . '/../models/TopsisCalculator.php';

class PenilaianController {
    private $pdo;
    private $penilaianModel;
    private $karyawanModel;
    private $kriteriaModel;
    private $role;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->penilaianModel = new Penilaian($pdo);
        $this->karyawanModel = new Karyawan($pdo);
        $this->kriteriaModel = new Kriteria($pdo);
        $this->role = $_SESSION['user']['role'] ?? '';
    }

    public function inputForm() {
        $page_title = 'Input Penilaian Karyawan';
        if ($this->role === 'direktur') {
            $_SESSION['error'] = 'Akses ditolak: Anda hanya memiliki hak lihat.';
            header('Location: index.php?act=penilaian_history');
            exit;
        }
        $karyawan = $this->karyawanModel->getAll();
        $kriteria  = $this->kriteriaModel->getAll();

        // =====================================================================
        // PERIODE selalu dibaca dari GET — satu-satunya sumber kebenaran untuk
        // menentukan bulan yang sedang ditampilkan.
        // =====================================================================
        $periodeRaw = $_GET['periode'] ?? null;
        if ($periodeRaw && preg_match('/^\d{4}-\d{2}$/', $periodeRaw)) {
            $periode = $periodeRaw . '-01';          // format YYYY-MM-DD untuk query DB
        } elseif ($periodeRaw && preg_match('/^\d{4}-\d{2}-\d{2}$/', $periodeRaw)) {
            $periode = $periodeRaw;                   // sudah lengkap
        } else {
            $periode = date('Y-m-01');                // default bulan berjalan
        }

        // Tombol "Hapus Penilaian" periode ini
        if (isset($_GET['clear']) && $_GET['clear'] == 1) {
            $this->penilaianModel->deleteByPeriode($periode);
            $_SESSION['success'] = 'Semua data penilaian pada periode '
                . date('F Y', strtotime($periode)) . ' berhasil dikosongkan.';
            header('Location: index.php?act=penilaian_input&periode=' . substr($periode, 0, 7));
            exit;
        }

        // =====================================================================
        // PROSES SIMPAN — hanya dieksekusi saat POST
        // Periode diambil dari field hidden POST['periode'], namun WAJIB
        // cocok dengan GET periode untuk mencegah penulisan lintas-bulan.
        // =====================================================================
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $periodePost = $_POST['periode'] ?? '';
            // Normalisasi periode dari POST
            if (preg_match('/^\d{4}-\d{2}$/', $periodePost)) {
                $periodePost = $periodePost . '-01';
            } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $periodePost)) {
                // Format tidak valid: tolak dan kembali ke halaman
                $_SESSION['error'] = 'Format periode tidak valid. Silakan coba lagi.';
                header('Location: index.php?act=penilaian_input&periode=' . substr($periode, 0, 7));
                exit;
            }

            // Gunakan periode dari POST sebagai target penyimpanan
            $periodeSimpan = $periodePost;

            $nilaiInput = $_POST['nilai'] ?? [];
            $failed     = 0;
            $saved      = 0;
            foreach ($nilaiInput as $id_karyawan => $kriteriaValues) {
                foreach ($kriteriaValues as $id_kriteria => $nilai) {
                    $nilai = is_string($nilai) ? str_replace(',', '.', trim($nilai)) : $nilai;
                    // Lewati jika kosong (tidak ingin menghapus nilai yang sudah ada)
                    if ($nilai === '' || $nilai === null) {
                        continue;
                    }
                    if (!is_numeric($nilai) || (float)$nilai < 0 || (float)$nilai > 100) {
                        $failed++;
                        continue;
                    }
                    $ok = $this->penilaianModel->insertOrUpdate(
                        (int)$id_karyawan,
                        (int)$id_kriteria,
                        (float)$nilai,
                        $periodeSimpan
                    );
                    if ($ok) $saved++;
                    else $failed++;
                }
            }
            if ($failed === 0) {
                $_SESSION['success'] = 'Penilaian untuk periode '
                    . date('F Y', strtotime($periodeSimpan)) . ' berhasil disimpan (' . $saved . ' data).';
            } else {
                $_SESSION['error'] = "Terjadi $failed kesalahan saat menyimpan. $saved data berhasil tersimpan.";
            }
            header('Location: index.php?act=penilaian_input&periode=' . substr($periodeSimpan, 0, 7));
            exit;
        }

        // =====================================================================
        // TAMPIL — ambil data existing berdasarkan periode GET
        // =====================================================================
        $existing = [];
        $rows = $this->penilaianModel->getPenilaianByPeriode($periode);
        foreach ($rows as $row) {
            $existing[$row['id_karyawan']][$row['id_kriteria']] = $row['nilai'];
        }
        include __DIR__ . '/../views/penilaian/input.php';
    }

    public function history() {
        $page_title = 'History Penilaian';
        $periodeList = $this->penilaianModel->getDistinctPeriode();
        
        $periodeData = [];
        $karyawan = $this->karyawanModel->getAll();
        $kriteria = $this->kriteriaModel->getAll();
        
        foreach ($periodeList as $p) {
            $periode = $p['periode_bulan'];
            $rewardNames = [];
            $punishmentNames = [];
            
            if (!empty($karyawan) && !empty($kriteria)) {
                $bobot = array_column($kriteria, 'bobot');
                $atribut = array_column($kriteria, 'atribut');
                $kriteriaIds = array_column($kriteria, 'id');
                $matriks = [];
                $karyawanIds = [];
                foreach ($karyawan as $k) {
                    $karyawanIds[] = $k['id'];
                    $row = [];
                    foreach ($kriteriaIds as $kid) {
                        $nilai = $this->penilaianModel->getNilai($k['id'], $kid, $periode);
                        $row[] = $nilai !== null ? (float)$nilai : 0;
                    }
                    $matriks[] = $row;
                }
                $topsis = new TopsisCalculator($matriks, $bobot, $atribut);
                $topsis->hitung();
                $ranking = $topsis->getRanking($karyawanIds);
                $total = count($ranking);
                
                // Reward: 3 peringkat teratas (jika total < 3, ambil semua)
                $topCount = min(3, $total);
                $top3 = array_slice($ranking, 0, $topCount);
                
                // Punishment: 3 peringkat terbawah, hanya jika total > 3
                $bottom3 = [];
                if ($total > 3) {
                    $bottom3 = array_slice($ranking, -3, 3);
                }
                
                foreach ($top3 as $r) {
                    foreach ($karyawan as $k) {
                        if ($k['id'] == $r['id_karyawan']) {
                            $rewardNames[] = htmlspecialchars($k['nama']);
                            break;
                        }
                    }
                }
                foreach ($bottom3 as $r) {
                    foreach ($karyawan as $k) {
                        if ($k['id'] == $r['id_karyawan']) {
                            $punishmentNames[] = htmlspecialchars($k['nama']);
                            break;
                        }
                    }
                }
            }
            
            $periodeData[] = [
                'periode' => $periode,
                'periode_name' => date('F Y', strtotime($periode)),
                'reward' => $rewardNames,
                'punishment' => $punishmentNames
            ];
        }
        
        include __DIR__ . '/../views/penilaian/history.php';
    }

    public function editPeriode($periode) {
        $page_title = 'Edit Penilaian Periode';
        if ($this->role === 'direktur') {
            $_SESSION['error'] = 'Akses ditolak.';
            header('Location: index.php?act=penilaian_history');
            exit;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $periode)) {
            $_SESSION['error'] = 'Format periode tidak valid.';
            header('Location: index.php?act=penilaian_history');
            exit;
        }
        $karyawan = $this->karyawanModel->getAll();
        $kriteria = $this->kriteriaModel->getAll();
        $existing = [];
        $rows = $this->penilaianModel->getPenilaianByPeriode($periode);
        foreach ($rows as $row) {
            $existing[$row['id_karyawan']][$row['id_kriteria']] = $row['nilai'];
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nilaiInput = $_POST['nilai'] ?? [];
            $failed = 0;
            foreach ($nilaiInput as $id_karyawan => $kriteriaValues) {
                foreach ($kriteriaValues as $id_kriteria => $nilai) {
                    $nilai = str_replace(',', '.', $nilai);
                    if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
                        $failed++;
                        continue;
                    }
                    $this->penilaianModel->insertOrUpdate((int)$id_karyawan, (int)$id_kriteria, (float)$nilai, $periode);
                }
            }
            if ($failed === 0) {
                $_SESSION['success'] = "Penilaian periode " . date('F Y', strtotime($periode)) . " berhasil diperbarui.";
            } else {
                $_SESSION['error'] = "Terjadi $failed kesalahan saat menyimpan.";
            }
            header('Location: index.php?act=penilaian_history');
            exit;
        }
        
        include __DIR__ . '/../views/penilaian/edit_periode.php';
    }

    public function deletePeriode($periode) {
        if ($this->role === 'direktur') {
            $_SESSION['error'] = 'Akses ditolak.';
            header('Location: index.php?act=penilaian_history');
            exit;
        }
        if ($this->penilaianModel->deleteByPeriode($periode)) {
            $_SESSION['success'] = "Data penilaian periode " . date('F Y', strtotime($periode)) . " berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus data.";
        }
        header('Location: index.php?act=penilaian_history');
        exit;
    }
}
?>