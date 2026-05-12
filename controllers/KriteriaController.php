<?php
require_once __DIR__ . '/../models/Kriteria.php';

class KriteriaController {
    private $kriteriaModel;
    private $role;

    public function __construct($pdo) {
        $this->kriteriaModel = new Kriteria($pdo);
        $this->role = $_SESSION['user']['role'] ?? '';
        if ($this->role !== 'admin') {
            $_SESSION['error'] = 'Akses ditolak: hanya admin.';
            header('Location: index.php?act=dashboard');
            exit;
        }
    }

    public function index() {
        $page_title = 'Manajemen Kriteria & Bobot';
        $kriteria = $this->kriteriaModel->getAll();
        include __DIR__ . '/../views/kriteria/index.php';
    }

    public function create() {
        $page_title = 'Tambah Kriteria';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama_kriteria'] ?? '');
            $bobot = (float)($_POST['bobot'] ?? 0);
            $atribut = $_POST['atribut'] ?? 'benefit';

            if ($this->kriteriaModel->create($nama, $bobot, $atribut)) {
                $_SESSION['success'] = 'Kriteria berhasil ditambahkan.';
                header('Location: index.php?act=kriteria');
                exit;
            }
            $error = 'Gagal menambah kriteria.';
        }

        include __DIR__ . '/../views/kriteria/create.php';
    }

    public function edit($id) {
        $page_title = 'Edit Kriteria';
        $kriteria = $this->kriteriaModel->getById($id);
        if (!$kriteria) {
            $_SESSION['error'] = 'Kriteria tidak ditemukan.';
            header('Location: index.php?act=kriteria');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama_kriteria'] ?? '');
            $bobot = (float)($_POST['bobot'] ?? 0);
            $atribut = $_POST['atribut'] ?? 'benefit';

            if ($this->kriteriaModel->update($id, $nama, $bobot, $atribut)) {
                $_SESSION['success'] = 'Kriteria berhasil diupdate.';
                header('Location: index.php?act=kriteria');
                exit;
            }
            $error = 'Gagal update.';
        }

        include __DIR__ . '/../views/kriteria/edit.php';
    }

    public function delete($id) {
        $this->kriteriaModel->delete($id);
        $_SESSION['success'] = 'Kriteria dihapus.';
        header('Location: index.php?act=kriteria');
        exit;
    }
}
