<?php
class Penilaian {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
    public function getNilai($id_karyawan, $id_kriteria, $periode_bulan) {
        try {
            $stmt = $this->pdo->prepare("SELECT nilai FROM penilaian WHERE id_karyawan=? AND id_kriteria=? AND periode_bulan=?");
            $stmt->execute([$id_karyawan, $id_kriteria, $periode_bulan]);
            $row = $stmt->fetch();
            return $row ? $row['nilai'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    public function getRataNilaiPeriode($id_karyawan, $id_kriteria, $mulai, $akhir) {
        try {
            $stmt = $this->pdo->prepare("SELECT AVG(nilai) as rata FROM penilaian WHERE id_karyawan=? AND id_kriteria=? AND periode_bulan BETWEEN ? AND ?");
            $stmt->execute([$id_karyawan, $id_kriteria, $mulai, $akhir]);
            $row = $stmt->fetch();
            return isset($row['rata']) ? (float)$row['rata'] : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    public function insertOrUpdate($id_karyawan, $id_kriteria, $nilai, $periode_bulan) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO penilaian (id_karyawan, id_kriteria, nilai, periode_bulan) VALUES (?, ?, ?, ?) 
                                     ON DUPLICATE KEY UPDATE nilai = ?");
            return $stmt->execute([$id_karyawan, $id_kriteria, $nilai, $periode_bulan, $nilai]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getPenilaianByPeriode($periode_bulan) {
        try {
            $stmt = $this->pdo->prepare("SELECT p.*, k.nama as nama_karyawan, kr.nama_kriteria 
                                     FROM penilaian p
                                     JOIN karyawan k ON p.id_karyawan = k.id
                                     JOIN kriteria kr ON p.id_kriteria = kr.id
                                     WHERE p.periode_bulan = ?
                                     ORDER BY k.nama, kr.id");
            $stmt->execute([$periode_bulan]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function getDistinctPeriode() {
        try {
            $stmt = $this->pdo->query("SELECT DISTINCT periode_bulan FROM penilaian ORDER BY periode_bulan DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function deleteByPeriode($periode_bulan) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM penilaian WHERE periode_bulan = ?");
            return $stmt->execute([$periode_bulan]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>