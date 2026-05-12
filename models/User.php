<?php
class User {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id");
        return $stmt->fetchAll();
    }
    
    public function create($username, $password, $role) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $hash, $role]);
    }
    
    public function update($id, $username, $role, $password = null) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE users SET username=?, role=?, password=? WHERE id=?");
            return $stmt->execute([$username, $role, $hash, $id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE users SET username=?, role=? WHERE id=?");
            return $stmt->execute([$username, $role, $id]);
        }
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>