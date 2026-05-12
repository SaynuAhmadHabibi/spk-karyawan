<?php
class TopsisCalculator {
    private $matriks;
    private $bobot;
    private $atribut;
    private $normalisasi;
    private $terbobot;
    private $ideal_positif;
    private $ideal_negatif;
    private $jarak_positif;
    private $jarak_negatif;
    private $preferensi;
    private $debugLog = [];

    public function __construct($matriksNilai, $bobotKriteria, $atributKriteria) {
        // Pastikan matriks menggunakan numeric index yang benar
        $this->matriks = array_values($matriksNilai);
        $this->bobot = array_values(array_map('floatval', $bobotKriteria));
        $this->atribut = array_values($atributKriteria);
        
        $this->log('INIT', [
            'jumlah_alternatif' => count($this->matriks),
            'jumlah_kriteria' => count($this->bobot),
            'bobot_awal' => $this->bobot,
            'atribut' => $this->atribut,
            'matriks_input' => $this->matriks
        ]);
        
        $this->normalisasiBobot();
    }

    /**
     * Normalisasi bobot agar total = 1
     * FIX: Gunakan index numerik eksplisit untuk menghindari key mismatch
     */
    private function normalisasiBobot() {
        $total = array_sum($this->bobot);
        if ($total > 0) {
            for ($i = 0; $i < count($this->bobot); $i++) {
                $this->bobot[$i] = $this->bobot[$i] / $total;
            }
        }
        $this->log('NORMALISASI_BOBOT', [
            'total_bobot' => $total,
            'bobot_ternormalisasi' => $this->bobot
        ]);
    }

    /**
     * Cek apakah matriks memiliki data valid (tidak semua nol)
     */
    private function hasData() {
        if (empty($this->matriks) || empty($this->matriks[0])) {
            $this->log('HAS_DATA', 'Matriks kosong');
            return false;
        }
        
        // Cek apakah semua nilai = 0 (tidak ada data penilaian)
        $hasNonZero = false;
        foreach ($this->matriks as $row) {
            foreach ($row as $val) {
                if ((float)$val != 0) {
                    $hasNonZero = true;
                    break 2;
                }
            }
        }
        
        if (!$hasNonZero) {
            $this->log('HAS_DATA', 'PERINGATAN: Semua nilai matriks = 0. Kemungkinan data penilaian tidak ditemukan untuk periode ini.');
        }
        
        return true; // Tetap lanjut meski semua 0, agar user bisa melihat hasilnya
    }

    /**
     * STEP 1: Normalisasi matriks keputusan
     * Rumus: r_ij = x_ij / sqrt(sum(x_ij^2))
     */
    private function normalisasi() {
        $n = count($this->matriks);     // jumlah alternatif
        $m = count($this->matriks[0]);  // jumlah kriteria
        $this->normalisasi = array_fill(0, $n, array_fill(0, $m, 0));
        
        for ($j = 0; $j < $m; $j++) {
            // Hitung akar dari jumlah kuadrat untuk kolom j
            $sumSquares = 0;
            for ($i = 0; $i < $n; $i++) {
                $sumSquares += pow((float)$this->matriks[$i][$j], 2);
            }
            $denom = sqrt($sumSquares);
            
            $this->log("NORMALISASI_KOLOM_$j", [
                'sum_kuadrat' => $sumSquares,
                'pembagi (sqrt)' => $denom
            ]);
            
            // Normalisasi setiap elemen pada kolom j
            for ($i = 0; $i < $n; $i++) {
                // FIX: Proteksi pembagian dengan nol
                $this->normalisasi[$i][$j] = ($denom > 0) 
                    ? ((float)$this->matriks[$i][$j] / $denom) 
                    : 0;
            }
        }
        
        $this->log('NORMALISASI_HASIL', $this->normalisasi);
    }

    /**
     * STEP 2: Matriks normalisasi terbobot
     * Rumus: y_ij = w_j * r_ij
     */
    private function normalisasiTerbobot() {
        $n = count($this->matriks);
        $m = count($this->matriks[0]);
        $this->terbobot = array_fill(0, $n, array_fill(0, $m, 0));
        
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $m; $j++) {
                $this->terbobot[$i][$j] = $this->normalisasi[$i][$j] * $this->bobot[$j];
            }
        }
        
        $this->log('TERBOBOT_HASIL', $this->terbobot);
    }

    /**
     * STEP 3: Solusi ideal positif (A+) dan negatif (A-)
     * Benefit: A+ = max, A- = min
     * Cost:    A+ = min, A- = max
     */
    private function idealSolutions() {
        $m = count($this->matriks[0]);
        $this->ideal_positif = array_fill(0, $m, 0);
        $this->ideal_negatif = array_fill(0, $m, 0);
        
        for ($j = 0; $j < $m; $j++) {
            $values = array_column($this->terbobot, $j);
            $atribut = strtolower(trim($this->atribut[$j] ?? 'benefit'));
            
            if ($atribut === 'benefit') {
                $this->ideal_positif[$j] = max($values);
                $this->ideal_negatif[$j] = min($values);
            } else {
                // Cost: positif = minimum, negatif = maximum
                $this->ideal_positif[$j] = min($values);
                $this->ideal_negatif[$j] = max($values);
            }
        }
        
        $this->log('IDEAL_POSITIF', $this->ideal_positif);
        $this->log('IDEAL_NEGATIF', $this->ideal_negatif);
    }

    /**
     * STEP 4: Jarak euclidean ke solusi ideal
     * D+ = sqrt(sum((y_ij - A+_j)^2))
     * D- = sqrt(sum((y_ij - A-_j)^2))
     */
    private function hitungJarak() {
        $n = count($this->matriks);
        $m = count($this->matriks[0]);
        $this->jarak_positif = array_fill(0, $n, 0);
        $this->jarak_negatif = array_fill(0, $n, 0);
        
        for ($i = 0; $i < $n; $i++) {
            $sumPos = 0;
            $sumNeg = 0;
            for ($j = 0; $j < $m; $j++) {
                $sumPos += pow($this->terbobot[$i][$j] - $this->ideal_positif[$j], 2);
                $sumNeg += pow($this->terbobot[$i][$j] - $this->ideal_negatif[$j], 2);
            }
            $this->jarak_positif[$i] = sqrt($sumPos);
            $this->jarak_negatif[$i] = sqrt($sumNeg);
        }
        
        $this->log('JARAK_POSITIF', $this->jarak_positif);
        $this->log('JARAK_NEGATIF', $this->jarak_negatif);
    }

    /**
     * STEP 5: Nilai preferensi (skor akhir)
     * V_i = D- / (D+ + D-)
     */
    private function hitungPreferensi() {
        $n = count($this->matriks);
        $this->preferensi = array_fill(0, $n, 0);
        
        for ($i = 0; $i < $n; $i++) {
            $total = $this->jarak_positif[$i] + $this->jarak_negatif[$i];
            
            // FIX: Proteksi pembagian dengan nol yang lebih eksplisit
            if ($total > 0) {
                $this->preferensi[$i] = $this->jarak_negatif[$i] / $total;
            } else {
                // D+ dan D- keduanya 0 → semua alternatif identik
                $this->preferensi[$i] = 0;
                $this->log("PREFERENSI_WARNING_$i", 'D+ + D- = 0, preferensi diset 0');
            }
        }
        
        $this->log('PREFERENSI_HASIL', $this->preferensi);
    }

    /**
     * Jalankan seluruh tahapan TOPSIS
     * @return array Detail perhitungan setiap tahap
     */
    public function hitung() {
        if (!$this->hasData()) {
            $this->normalisasi = [];
            $this->terbobot = [];
            $this->ideal_positif = [];
            $this->ideal_negatif = [];
            $this->jarak_positif = [];
            $this->jarak_negatif = [];
            $this->preferensi = [];
            return [
                'matriks_awal' => [],
                'normalisasi' => [],
                'terbobot' => [],
                'ideal_positif' => [],
                'ideal_negatif' => [],
                'jarak_positif' => [],
                'jarak_negatif' => [],
                'preferensi' => [],
                'debug_log' => $this->debugLog
            ];
        }

        // Eksekusi seluruh tahapan TOPSIS secara berurutan
        $this->normalisasi();
        $this->normalisasiTerbobot();
        $this->idealSolutions();
        $this->hitungJarak();
        $this->hitungPreferensi();
        
        $this->log('SELESAI', 'Semua tahapan TOPSIS berhasil dieksekusi');
        
        return [
            'matriks_awal' => $this->matriks,
            'normalisasi' => $this->normalisasi,
            'terbobot' => $this->terbobot,
            'ideal_positif' => $this->ideal_positif,
            'ideal_negatif' => $this->ideal_negatif,
            'jarak_positif' => $this->jarak_positif,
            'jarak_negatif' => $this->jarak_negatif,
            'preferensi' => $this->preferensi,
            'debug_log' => $this->debugLog
        ];
    }

    /**
     * Buat ranking berdasarkan nilai preferensi (tinggi ke rendah)
     * @param array $karyawanIds Array ID karyawan sesuai urutan matriks
     * @return array Ranking terurut dari tertinggi ke terendah
     */
    public function getRanking($karyawanIds) {
        $ranking = [];
        $count = min(count($karyawanIds), count($this->preferensi ?? []));
        
        for ($i = 0; $i < $count; $i++) {
            $ranking[] = [
                'id_karyawan' => $karyawanIds[$i],
                'nilai' => $this->preferensi[$i] ?? 0
            ];
        }
        
        // Urutkan descending berdasarkan nilai preferensi
        usort($ranking, function ($a, $b) {
            return $b['nilai'] <=> $a['nilai'];
        });
        
        $this->log('RANKING', $ranking);
        
        return $ranking;
    }
    
    /**
     * Tambahkan log untuk debugging
     */
    private function log($step, $data) {
        $this->debugLog[] = [
            'step' => $step,
            'data' => $data,
            'timestamp' => date('H:i:s')
        ];
    }
    
    /**
     * Ambil seluruh debug log
     */
    public function getDebugLog() {
        return $this->debugLog;
    }
}
?>
