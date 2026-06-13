<?php
/**
 * KaryawanController - Handles employee management
 * 
 * Manages CRUD operations for employees including listing, creation,
 * editing, and deletion with appropriate access control.
 * 
 * @author Development Team
 * @version 1.0
 */

require_once __DIR__ . '/../models/Karyawan.php';

class KaryawanController {
    private Karyawan $karyawanModel;
    private string $role;

    /**
     * Constructor
     * 
     * @param \PDO $pdo Database connection object
     */
    public function __construct(\PDO $pdo) {
        $this->karyawanModel = new Karyawan($pdo);
        $this->role = $_SESSION['user']['role'] ?? '';
    }

    /**
     * Display list of all employees
     * 
     * Shows all employees including inactive ones with pagination
     * and filtering options.
     * 
     * @return void
     */
    public function index(): void {
        $page_title = 'Manajemen Karyawan';
        $karyawan = $this->karyawanModel->getAllWithNonaktif();
        include __DIR__ . '/../views/karyawan/index.php';
    }

    /**
     * Display employee creation form and handle creation
     * 
     * GET: Display form to create new employee
     * POST: Process form submission and save new employee
     * 
     * Restricted to admin and manager roles only.
     * 
     * @return void
     */
    public function create(): void {
        $page_title = 'Tambah Karyawan';
        
        // Check permission - direktur cannot create/edit
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

    /**
     * Display employee edit form and handle update
     * 
     * GET: Display form to edit employee
     * POST: Process form submission and update employee
     * 
     * Restricted to admin and manager roles only.
     * 
     * @param int|string $id Employee ID to edit
     * @return void
     */
    public function edit(int|string $id): void {
        $page_title = 'Edit Karyawan';
        
        // Check permission - direktur cannot edit
        if ($this->role === 'direktur') {
            $_SESSION['error'] = 'Akses ditolak: Anda hanya memiliki hak lihat.';
            header('Location: index.php?act=karyawan');
            exit;
        }
        
        // Check if employee exists
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
            $error = 'Gagal mengupdate karyawan.';
        }

        include __DIR__ . '/../views/karyawan/edit.php';
    }

    /**
     * Delete employee
     * 
     * Permanently removes employee from database.
     * Restricted to admin only.
     * 
     * @param int|string $id Employee ID to delete
     * @return void
     */
    public function delete(int|string $id): void {
        // Check permission - only admin can delete
        if ($this->role === 'direktur') {
            $_SESSION['error'] = 'Akses ditolak: Anda hanya memiliki hak lihat.';
            header('Location: index.php?act=karyawan');
            exit;
        }
        
        if ($this->role !== 'admin') {
            $_SESSION['error'] = 'Akses ditolak: Hanya admin yang dapat menghapus.';
            header('Location: index.php?act=karyawan');
            exit;
        }

        if ($this->karyawanModel->delete($id)) {
            $_SESSION['success'] = 'Karyawan berhasil dihapus.';
        } else {
            $_SESSION['error'] = 'Gagal menghapus karyawan.';
        }

        header('Location: index.php?act=karyawan');
        exit;
    }
}
?>
        }
        $this->karyawanModel->delete($id);
        $_SESSION['success'] = 'Karyawan dihapus.';
        header('Location: index.php?act=karyawan');
        exit;
    }
}
