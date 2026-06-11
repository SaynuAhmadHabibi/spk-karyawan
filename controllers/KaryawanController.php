<?php
require_once __DIR__ . '/../models/Karyawan.php';

class KaryawanController {
    private Karyawan $karyawanModel;
    private string $role;

    public function __construct(\PDO $pdo) {
        $this->karyawanModel = new Karyawan($pdo);
        $this->role = $_SESSION['user']['role'] ?? '';
    }

    public function index(): void {
        $page_title = 'Manajemen Karyawan';
        $karyawan = $this->karyawanModel->getAllWithNonaktif();
        include __DIR__ . '/../views/karyawan/index.php';
    }

    public function create(): void {
        $page_title = 'Tambah Karyawan';
        if ($this->role === 'direktur') {
            $_SESSION['error'] = 'Akses ditolak: Anda hanya memiliki hak lihat.';
            header('Location: index.php?act=karyawan');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama'] ?? '');
            $jabatan = trim($_POST['jabatan'] ?? '');
            $divisi = trim($_POST['divisi'] ?? '');
            $tanggal_masuk = $_POST['tanggal_masuk'] ?? null;
            $status = $_POST['status'] ?? 'aktif';

            if ($this->karyawanModel->create($nama, $jabatan, $divisi, $tanggal_masuk, $status)) {
                $_SESSION['success'] = 'Karyawan berhasil ditambahkan.';
                header('Location: index.php?act=karyawan');
                exit;
            }
            $error = 'Gagal menambahkan karyawan.';
        }

        include __DIR__ . '/../views/karyawan/create.php';
    }

    public function edit(int|string $id): void {
        $page_title = 'Edit Karyawan';
        if ($this->role === 'direktur') {
            $_SESSION['error'] = 'Akses ditolak: Anda hanya memiliki hak lihat.';
            header('Location: index.php?act=karyawan');
            exit;
        }
        $karyawan = $this->karyawanModel->getById($id);
        if (!$karyawan) {
            $_SESSION['error'] = 'Karyawan tidak ditemukan.';
            header('Location: index.php?act=karyawan');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama'] ?? '');
            $jabatan = trim($_POST['jabatan'] ?? '');
            $divisi = trim($_POST['divisi'] ?? '');
            $tanggal_masuk = $_POST['tanggal_masuk'] ?? null;
            $status = $_POST['status'] ?? 'aktif';

            if ($this->karyawanModel->update($id, $nama, $jabatan, $divisi, $tanggal_masuk, $status)) {
                $_SESSION['success'] = 'Karyawan berhasil diupdate.';
                header('Location: index.php?act=karyawan');
                exit;
            }
            $error = 'Gagal mengupdate.';
        }

        include __DIR__ . '/../views/karyawan/edit.php';
    }

    public function delete(int|string $id): void {
        if ($this->role === 'direktur') {
            $_SESSION['error'] = 'Akses ditolak: Anda hanya memiliki hak lihat.';
            header('Location: index.php?act=karyawan');
            exit;
        }
        $this->karyawanModel->delete($id);
        $_SESSION['success'] = 'Karyawan dihapus.';
        header('Location: index.php?act=karyawan');
        exit;
    }
}
