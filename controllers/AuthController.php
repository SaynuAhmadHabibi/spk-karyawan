<?php
/**
 * AuthController - Handles user authentication
 * 
 * Manages user login and logout operations, including session management
 * and credential verification.
 * 
 * @author Development Team
 * @version 1.0
 */

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private User $userModel;
    
    /**
     * Constructor
     * 
     * @param \PDO $pdo Database connection object
     */
    public function __construct(\PDO $pdo) {
        $this->userModel = new User($pdo);
    }

    /**
     * Display login form and handle login request
     * 
     * GET: Display login form
     * POST: Process login credentials
     * 
     * @return void
     */
    public function login(): void {
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            // Validate credentials
            $user = $this->userModel->login($username, $password);
            
            if ($user) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                $_SESSION['user'] = $user;
                header('Location: index.php?act=dashboard');
                exit;
            }
            
            $error = 'Username atau password salah!';
        }
        
        include __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Logout current user
     * 
     * Destroys session and redirects to login page.
     * 
     * @return void
     */
    public function logout(): void {
        $_SESSION = [];
        session_destroy();
        header('Location: index.php?act=login');
        exit;
    }
}
?>