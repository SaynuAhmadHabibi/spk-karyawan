<?php
class Kriteria {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM kriteria ORDER BY id");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM kriteria WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($nama, $bobot, $atribut) {
        $stmt = $this->pdo->prepare("INSERT INTO kriteria (nama_kriteria, bobot, atribut) VALUES (?, ?, ?)");
        return $stmt->execute([$nama, $bobot, $atribut]);
    }
    
    public function update($id, $nama, $bobot, $atribut) {
        $stmt = $this->pdo->prepare("UPDATE kriteria SET nama_kriteria=?, bobot=?, atribut=? WHERE id=?");
        return $stmt->execute([$nama, $bobot, $atribut, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM kriteria WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>