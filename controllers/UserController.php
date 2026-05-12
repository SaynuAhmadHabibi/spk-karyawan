<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;
    private $role;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
        $this->role = $_SESSION['user']['role'] ?? '';
    }

    public function index() {
        if ($this->role !== 'admin') {
            $_SESSION['error'] = 'Akses ditolak: Hanya Admin.';
            header('Location: index.php?act=dashboard');
            exit;
        }
        $page_title = 'Manajemen Pengguna';
        $users = $this->userModel->getAll();
        $currentUserId = $_SESSION['user']['id'];
        include __DIR__ . '/../views/user/index.php';
    }

    public function store() {
        if ($this->role !== 'admin') {
            header('Location: index.php?act=dashboard');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $userRole = trim($_POST['role'] ?? '');

            if ($this->userModel->create($username, $password, $userRole)) {
                $_SESSION['success'] = 'User berhasil ditambahkan.';
            } else {
                $_SESSION['error'] = 'Gagal menambahkan user.';
            }
            header('Location: index.php?act=user');
            exit;
        }
    }

    public function update() {
        if ($this->role !== 'admin') {
            header('Location: index.php?act=dashboard');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $userRole = trim($_POST['role'] ?? '');

            if ($this->userModel->update($id, $username, $userRole, $password ? $password : null)) {
                $_SESSION['success'] = 'User berhasil diperbarui.';
            } else {
                $_SESSION['error'] = 'Gagal mengupdate user.';
            }
            header('Location: index.php?act=user');
            exit;
        }
    }

    public function delete($id) {
        if ($this->role !== 'admin') {
            header('Location: index.php?act=dashboard');
            exit;
        }
        // Mencegah hapus diri sendiri
        if ($id == $_SESSION['user']['id']) {
            $_SESSION['error'] = 'Tidak bisa menghapus akun sendiri.';
        } else {
            if ($this->userModel->delete($id)) {
                $_SESSION['success'] = 'User berhasil dihapus.';
            } else {
                $_SESSION['error'] = 'Gagal menghapus user.';
            }
        }
        header('Location: index.php?act=user');
        exit;
    }
}
