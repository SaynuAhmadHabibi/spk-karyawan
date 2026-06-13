<?php
/**
 * SPK TOPSIS Application Entry Point
 * 
 * This is the main entry point for the SPK TOPSIS application.
 * It handles routing, authentication, and controller dispatching.
 * 
 * @author Development Team
 * @version 1.0
 */

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// INITIALIZE APPLICATION
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

require_once 'config/database.php';
require_once 'lib/Router.php';

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// HANDLE AUTHENTICATION & PUBLIC ROUTES
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

$action = $_GET['act'] ?? 'login';
$sub = $_GET['sub'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Handle public routes (login)
if ($action === 'login') {
    require_once 'controllers/AuthController.php';
    $ctrl = new AuthController($pdo);
    $ctrl->login();
    exit;
}

// Handle logout
if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    header('Location: index.php?act=login');
    exit;
}

// Require authentication for all other routes
if (!isset($_SESSION['user'])) {
    header('Location: index.php?act=login');
    exit;
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// DISPATCH AUTHENTICATED ROUTES
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

switch ($action) {
    // ──────────────────── DASHBOARD ────────────────────
    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        $ctrl = new DashboardController($pdo);
        $ctrl->index();
        break;

    // ──────────────────── KARYAWAN (Employees) ────────────────────
    case 'karyawan':
        require_once 'controllers/KaryawanController.php';
        $ctrl = new KaryawanController($pdo);
        
        if ($sub === 'create') {
            $ctrl->create();
        } elseif ($sub === 'edit' && $id) {
            $ctrl->edit($id);
        } elseif ($sub === 'delete' && $id) {
            $ctrl->delete($id);
        } else {
            $ctrl->index();
        }
        break;

    // ──────────────────── KRITERIA (Criteria) ────────────────────
    case 'kriteria':
        require_once 'controllers/KriteriaController.php';
        $ctrl = new KriteriaController($pdo);
        
        if ($sub === 'create') {
            $ctrl->create();
        } elseif ($sub === 'edit' && $id) {
            $ctrl->edit($id);
        } elseif ($sub === 'delete' && $id) {
            $ctrl->delete($id);
        } else {
            $ctrl->index();
        }
        break;

    // ──────────────────── PENILAIAN (Evaluation) ────────────────────
    case 'penilaian_input':
        require_once 'controllers/PenilaianController.php';
        $ctrl = new PenilaianController($pdo);
        $ctrl->inputForm();
        break;

    case 'penilaian_history':
        require_once 'controllers/PenilaianController.php';
        $ctrl = new PenilaianController($pdo);
        $ctrl->history();
        break;

    case 'penilaian_edit':
        require_once 'controllers/PenilaianController.php';
        $ctrl = new PenilaianController($pdo);
        $periode = $_GET['periode'] ?? '';
        $ctrl->editPeriode($periode);
        break;

    case 'penilaian_delete':
        require_once 'controllers/PenilaianController.php';
        $ctrl = new PenilaianController($pdo);
        $periode = $_GET['periode'] ?? '';
        $ctrl->deletePeriode($periode);
        break;

    // ──────────────────── TOPSIS Analysis ────────────────────
    case 'hitung_punishment_form':
        require_once 'controllers/TopsisController.php';
        $ctrl = new TopsisController($pdo);
        $ctrl->punishmentForm();
        break;

    case 'hitung_reward_form':
        require_once 'controllers/TopsisController.php';
        $ctrl = new TopsisController($pdo);
        $ctrl->rewardForm();
        break;

    case 'hitung_punishment':
        require_once 'controllers/TopsisController.php';
        $ctrl = new TopsisController($pdo);
        $ctrl->punishment();
        break;

    case 'hitung_reward':
        require_once 'controllers/TopsisController.php';
        $ctrl = new TopsisController($pdo);
        $ctrl->reward();
        break;

    case 'hasil_punishment':
        require_once 'controllers/TopsisController.php';
        $ctrl = new TopsisController($pdo);
        $ctrl->hasilPunishment();
        break;

    case 'hasil_reward':
        require_once 'controllers/TopsisController.php';
        $ctrl = new TopsisController($pdo);
        $ctrl->hasilReward();
        break;

    case 'detail_perhitungan':
        require_once 'controllers/TopsisController.php';
        $ctrl = new TopsisController($pdo);
        $tipe = $_GET['tipe'] ?? 'reward';
        $ctrl->detailPerhitungan($tipe);
        break;

    // ──────────────────── REPORTS ────────────────────
    case 'export_excel':
        require_once 'controllers/LaporanController.php';
        $ctrl = new LaporanController($pdo);
        $tipe = in_array($_GET['tipe'] ?? '', ['reward', 'punishment']) ? $_GET['tipe'] : 'reward';
        $ctrl->exportExcel($tipe);
        break;

    case 'export_pdf':
        require_once 'controllers/LaporanController.php';
        $ctrl = new LaporanController($pdo);
        $tipe = in_array($_GET['tipe'] ?? '', ['reward', 'punishment']) ? $_GET['tipe'] : 'reward';
        $ctrl->exportPdf($tipe);
        break;

    // ──────────────────── USER MANAGEMENT ────────────────────
    case 'user':
        require_once 'controllers/UserController.php';
        $ctrl = new UserController($pdo);
        $ctrl->index();
        break;

    case 'user_store':
        require_once 'controllers/UserController.php';
        $ctrl = new UserController($pdo);
        $ctrl->store();
        break;

    case 'user_update':
        require_once 'controllers/UserController.php';
        $ctrl = new UserController($pdo);
        $ctrl->update();
        break;

    case 'user_delete':
        require_once 'controllers/UserController.php';
        $ctrl = new UserController($pdo);
        $id = $_GET['id'] ?? 0;
        $ctrl->delete($id);
        break;

    // ──────────────────── PROFILE ────────────────────
    case 'profil':
        require_once 'controllers/ProfilController.php';
        $ctrl = new ProfilController($pdo);
        $ctrl->index();
        break;

    case 'profil_upload_photo':
        require_once 'controllers/ProfilController.php';
        $ctrl = new ProfilController($pdo);
        $ctrl->uploadPhoto();
        break;

    case 'profil_change_password':
        require_once 'controllers/ProfilController.php';
        $ctrl = new ProfilController($pdo);
        $ctrl->changePassword();
        break;

    // ──────────────────── DEFAULT (Dashboard) ────────────────────
    default:
        require_once 'controllers/DashboardController.php';
        $ctrl = new DashboardController($pdo);
        $ctrl->index();
        break;
}
?>
