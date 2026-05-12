<?php
require_once __DIR__ . '/../models/Karyawan.php';

class KaryawanController {
    private $karyawanModel;
    private $role;

    public function __construct($pdo) {
        $this->karyawanModel = new Karyawan($pdo);
        $this->role = $_SESSION['user']['role'] ?? '';
    }

    public function index() {
        $page_title = 'Manajemen Karyawan';
        $karyawan = $this->karyawanModel->getAllWithNonaktif();
        include __DIR__ . '/../views/karyawan/index.php';
    }

    public function create() {
        $page_title = 'Tambah Karyawan';
        if ($this->role === 'direktur') {
            $_SESSION['error'] = 'Akses ditolak: Anda hanya memiliki hak lihat.';
            header('Location: index.php?act=karyawan');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nik = trim($_POST['nik'] ?? '');
            $nama = trim($_POST['nama'] ?? '');
            $jabatan = trim($_POST['jabatan'] ?? '');
            $divisi = trim($_POST['divisi'] ?? '');
            $tanggal_masuk = $_POST['tanggal_masuk'] ?? null;
            $status = $_POST['status'] ?? 'aktif';

            if ($this->karyawanModel->create($nik, $nama, $jabatan, $divisi, $tanggal_masuk, $status)) {
                $_SESSION['success'] = 'Karyawan berhasil ditambahkan.';
                header('Location: index.php?act=karyawan');
                exit;
            }
            $error = 'Gagal menambahkan karyawan.';
        }

        include __DIR__ . '/../views/karyawan/create.php';
    }

    public function edit($id) {
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
            $nik = trim($_POST['nik'] ?? '');
            $nama = trim($_POST['nama'] ?? '');
            $jabatan = trim($_POST['jabatan'] ?? '');
            $divisi = trim($_POST['divisi'] ?? '');
            $tanggal_masuk = $_POST['tanggal_masuk'] ?? null;
            $status = $_POST['status'] ?? 'aktif';

            if ($this->karyawanModel->update($id, $nik, $nama, $jabatan, $divisi, $tanggal_masuk, $status)) {
                $_SESSION['success'] = 'Karyawan berhasil diupdate.';
                header('Location: index.php?act=karyawan');
                exit;
            }
            $error = 'Gagal mengupdate.';
        }

        include __DIR__ . '/../views/karyawan/edit.php';
    }

    public function delete($id) {
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
