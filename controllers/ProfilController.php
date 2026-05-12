<?php
class ProfilController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {
        $page_title = 'Profil Saya';
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            header('Location: index.php?act=login');
            exit;
        }
        // Refresh user data from DB to get latest photo
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $user = $stmt->fetch();
        $_SESSION['user'] = $user;
        include 'views/profil.php';
    }

    public function uploadPhoto() {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            header('Location: index.php?act=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['photo'])) {
            header('Location: index.php?act=profil');
            exit;
        }

        $file     = $_FILES['photo'];
        $allowed  = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize  = 3 * 1024 * 1024; // 3 MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Gagal mengupload foto. Coba lagi.';
            header('Location: index.php?act=profil');
            exit;
        }
        if (!in_array($file['type'], $allowed)) {
            $_SESSION['error'] = 'Format foto tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.';
            header('Location: index.php?act=profil');
            exit;
        }
        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = 'Ukuran foto maksimal 3 MB.';
            header('Location: index.php?act=profil');
            exit;
        }

        $uploadDir = __DIR__ . '/../assets/uploads/photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Delete old photo if exists
        $oldPhoto = $user['photo'] ?? null;
        if ($oldPhoto && file_exists($uploadDir . $oldPhoto)) {
            unlink($uploadDir . $oldPhoto);
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $user['id'] . '_' . time() . '.' . strtolower($ext);
        $dest     = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $_SESSION['error'] = 'Gagal menyimpan foto. Periksa permission folder.';
            header('Location: index.php?act=profil');
            exit;
        }

        $stmt = $this->pdo->prepare("UPDATE users SET photo = ? WHERE id = ?");
        $stmt->execute([$filename, $user['id']]);

        // Update session
        $_SESSION['user']['photo'] = $filename;
        $_SESSION['success'] = 'Foto profil berhasil diperbarui!';
        header('Location: index.php?act=profil');
        exit;
    }

    public function changePassword() {
        $user = $_SESSION['user'] ?? null;
        if (!$user || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?act=profil');
            exit;
        }

        $currentPwd = $_POST['current_password'] ?? '';
        $newPwd     = $_POST['new_password'] ?? '';
        $confirmPwd = $_POST['confirm_password'] ?? '';

        // Verify current password
        $stmt = $this->pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($currentPwd, $row['password'])) {
            $_SESSION['error'] = 'Password saat ini tidak sesuai.';
            header('Location: index.php?act=profil');
            exit;
        }
        if (strlen($newPwd) < 6) {
            $_SESSION['error'] = 'Password baru minimal 6 karakter.';
            header('Location: index.php?act=profil');
            exit;
        }
        if ($newPwd !== $confirmPwd) {
            $_SESSION['error'] = 'Konfirmasi password tidak cocok.';
            header('Location: index.php?act=profil');
            exit;
        }

        $hash = password_hash($newPwd, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $user['id']]);

        $_SESSION['success'] = 'Password berhasil diubah!';
        header('Location: index.php?act=profil');
        exit;
    }
}
