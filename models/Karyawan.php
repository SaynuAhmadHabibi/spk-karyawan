<?php
class Karyawan {
    private $pdo;
    private $columnsCache = null;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
    public function getAll() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM karyawan WHERE status='aktif' ORDER BY nama");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            if ($e->getCode() === '42S22') {
                $stmt = $this->pdo->query("SELECT * FROM karyawan ORDER BY nama");
                return $stmt->fetchAll();
            }
            throw $e;
        }
    }
    
    public function getAllWithNonaktif() {
        $stmt = $this->pdo->query("SELECT * FROM karyawan ORDER BY nama");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM karyawan WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($nama, $jabatan, $divisi, $tanggal_masuk, $status) {
        $data = [
            'nama' => $nama,
            'jabatan' => $jabatan,
            'divisi' => $divisi,
            'tanggal_masuk' => $tanggal_masuk,
            'status' => $status,
        ];
        $available = $this->getColumns();
        $exclude = ['id', 'created_at', 'updated_at'];
        $useCols = array_values(array_diff(array_intersect(array_keys($data), $available), $exclude));
        if (empty($useCols)) throw new Exception('No valid columns for insert');
        $placeholders = rtrim(str_repeat('?,', count($useCols)), ',');
        $colList = implode(', ', $useCols);
        $values = array_map(fn($c) => $data[$c], $useCols);
        $stmt = $this->pdo->prepare("INSERT INTO karyawan ($colList) VALUES ($placeholders)");
        return $stmt->execute($values);
    }
    
    public function update($id, $nama, $jabatan, $divisi, $tanggal_masuk, $status) {
        $data = [
            'nama' => $nama,
            'jabatan' => $jabatan,
            'divisi' => $divisi,
            'tanggal_masuk' => $tanggal_masuk,
            'status' => $status,
        ];
        $available = $this->getColumns();
        $exclude = ['id', 'created_at', 'updated_at'];
        $useCols = array_values(array_diff(array_intersect(array_keys($data), $available), $exclude));
        if (empty($useCols)) throw new Exception('No valid columns for update');
        $sets = [];
        $values = [];
        foreach ($useCols as $c) { $sets[] = "$c = ?"; $values[] = $data[$c]; }
        $values[] = $id;
        $setList = implode(', ', $sets);
        $stmt = $this->pdo->prepare("UPDATE karyawan SET $setList WHERE id=?");
        return $stmt->execute($values);
    }

    private function getColumns() {
        if ($this->columnsCache !== null) return $this->columnsCache;
        $stmt = $this->pdo->query("DESCRIBE karyawan");
        $cols = [];
        while ($row = $stmt->fetch()) $cols[] = $row['Field'];
        $this->columnsCache = $cols;
        return $cols;
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM karyawan WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>