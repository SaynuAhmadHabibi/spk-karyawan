<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    public function __construct($pdo) { $this->userModel = new User($pdo); }

    public function login() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $user = $this->userModel->login($username, $password);
            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user'] = $user;
                header('Location: index.php?act=dashboard');
                exit;
            }
            $error = 'Username atau password salah!';
        }
        include __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();
        header('Location: index.php');
        exit;
    }
}