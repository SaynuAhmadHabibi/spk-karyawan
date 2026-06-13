<?php
/**
 * Helper Functions - Utility functions used throughout the application
 * 
 * This file contains reusable helper functions to reduce code duplication
 * and promote consistency across the application.
 * 
 * @author Development Team
 * @version 1.0
 */

/**
 * Redirect to a URL with optional message
 * 
 * @param string $url Target URL
 * @param string|null $message Optional message to store in session
 * @param string $messageType Type of message (success, error, warning)
 * @return never
 */
function redirect(string $url, ?string $message = null, string $messageType = 'info'): never {
    if ($message) {
        $_SESSION[$messageType] = $message;
    }
    header('Location: ' . $url);
    exit;
}

/**
 * Get session message and clear it
 * 
 * @param string $type Message type (success, error, warning)
 * @return string|null Message content or null if not set
 */
function getSessionMessage(string $type = 'success'): ?string {
    $message = $_SESSION[$type] ?? null;
    if ($message) {
        unset($_SESSION[$type]);
    }
    return $message;
}

/**
 * Check if user is authenticated
 * 
 * @return bool True if user is logged in
 */
function isAuthenticated(): bool {
    return isset($_SESSION[SESSION_USER]) && !empty($_SESSION[SESSION_USER]);
}

/**
 * Get current user from session
 * 
 * @return array|null Current user data or null
 */
function getCurrentUser(): ?array {
    return $_SESSION[SESSION_USER] ?? null;
}

/**
 * Check if current user has specific role
 * 
 * @param string|array $role Role(s) to check
 * @return bool True if user has required role
 */
function hasRole(string|array $role): bool {
    if (!isAuthenticated()) {
        return false;
    }
    
    $userRole = $_SESSION[SESSION_USER]['role'] ?? null;
    
    if (is_array($role)) {
        return in_array($userRole, $role);
    }
    
    return $userRole === $role;
}

/**
 * Require authentication, redirect to login if not authenticated
 * 
 * @return never If not authenticated
 */
function requireAuth(): void {
    if (!isAuthenticated()) {
        redirect('index.php?act=login');
    }
}

/**
 * Require specific role(s), redirect to dashboard if unauthorized
 * 
 * @param string|array $role Role(s) required
 * @return never If user doesn't have required role
 */
function requireRole(string|array $role): void {
    if (!hasRole($role)) {
        $_SESSION[SESSION_ERROR] = 'Akses ditolak: Anda tidak memiliki permission untuk fitur ini.';
        redirect('index.php?act=dashboard');
    }
}

/**
 * Sanitize user input to prevent XSS
 * 
 * @param string $input User input to sanitize
 * @return string Sanitized input
 */
function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Display escaped HTML (for use in views)
 * 
 * @param string $text Text to escape and display
 * @return string Escaped HTML
 */
function escape(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Format date for display (Indonesian format)
 * 
 * @param string $date Date string (YYYY-MM-DD)
 * @return string Formatted date (DD-MM-YYYY)
 */
function formatDate(string $date): string {
    if (!$date) {
        return '-';
    }
    
    try {
        $dateTime = new DateTime($date);
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $day = $dateTime->format('d');
        $month = $months[(int)$dateTime->format('m')];
        $year = $dateTime->format('Y');
        
        return "$day $month $year";
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Format number with thousand separator
 * 
 * @param float $number Number to format
 * @param int $decimals Number of decimal places
 * @return string Formatted number
 */
function formatNumber(float $number, int $decimals = 2): string {
    return number_format($number, $decimals, ',', '.');
}

/**
 * Format percentage
 * 
 * @param float $number Number to format as percentage
 * @param int $decimals Number of decimal places
 * @return string Formatted percentage
 */
function formatPercent(float $number, int $decimals = 2): string {
    return formatNumber($number * 100, $decimals) . '%';
}

/**
 * Check if request is POST
 * 
 * @return bool True if request method is POST
 */
function isPost(): bool {
    return $_SERVER['REQUEST_METHOD'] === METHOD_POST;
}

/**
 * Get query parameter with type casting
 * 
 * @param string $key Parameter name
 * @param mixed $default Default value if not set
 * @param string $type Type to cast (int, string, bool, float)
 * @return mixed Parameter value or default
 */
function getParam(string $key, mixed $default = null, string $type = 'string'): mixed {
    $value = $_GET[$key] ?? $default;
    
    if ($value === null) {
        return $default;
    }
    
    return match ($type) {
        'int' => (int)$value,
        'float' => (float)$value,
        'bool' => (bool)$value,
        'string' => (string)$value,
        default => $value
    };
}

/**
 * Get POST parameter with type casting
 * 
 * @param string $key Parameter name
 * @param mixed $default Default value if not set
 * @param string $type Type to cast (int, string, bool, float)
 * @return mixed Parameter value or default
 */
function postParam(string $key, mixed $default = null, string $type = 'string'): mixed {
    $value = $_POST[$key] ?? $default;
    
    if ($value === null) {
        return $default;
    }
    
    return match ($type) {
        'int' => (int)$value,
        'float' => (float)$value,
        'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
        'string' => trim((string)$value),
        default => $value
    };
}

/**
 * Log error message to file
 * 
 * @param string $message Error message
 * @param string|null $context Additional context
 * @return void
 */
function logError(string $message, ?string $context = null): void {
    $logFile = __DIR__ . '/../logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message";
    
    if ($context) {
        $logMessage .= "\nContext: $context";
    }
    
    $logMessage .= "\n" . str_repeat('-', 80) . "\n";
    
    // Create logs directory if not exists
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    error_log($logMessage, 3, $logFile);
}

/**
 * Convert array to JSON with pretty printing
 * 
 * @param array $data Data to convert
 * @return string JSON string
 */
function toJson(array $data): string {
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * Generate random string
 * 
 * @param int $length Length of random string
 * @return string Random string
 */
function generateRandomString(int $length = 32): string {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Check if given role can perform action
 * 
 * @param string $role User role
 * @param string $action Action to check
 * @return bool True if role can perform action
 */
function canPerformAction(string $role, string $action): bool {
    $permissions = [
        ROLE_ADMIN => ['create', 'edit', 'delete', 'view', 'export'],
        ROLE_MANAGER => ['create', 'edit', 'view', 'export'],
        ROLE_DIREKTUR => ['view', 'export'],
    ];
    
    return in_array($action, $permissions[$role] ?? []);
}

/**
 * Generate HTML attributes from array
 * 
 * @param array $attributes Attributes to generate
 * @return string HTML attributes string
 */
function generateAttributes(array $attributes): string {
    $html = '';
    foreach ($attributes as $key => $value) {
        if ($value === true) {
            $html .= " $key";
        } elseif ($value !== false && $value !== null) {
            $html .= " $key=\"" . escape($value) . "\"";
        }
    }
    return $html;
}

/**
 * Generate session token for CSRF protection
 * 
 * @return string CSRF token
 */
function generateCsrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateRandomString();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool True if token is valid
 */
function verifyCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get status badge HTML
 * 
 * @param string $status Status to display
 * @return string HTML badge
 */
function getStatusBadge(string $status): string {
    $badgeClass = match ($status) {
        'aktif' => 'badge-success',
        'nonaktif' => 'badge-danger',
        'cuti' => 'badge-warning',
        default => 'badge-secondary'
    };
    
    return '<span class="badge ' . $badgeClass . '">' . escape($status) . '</span>';
}

/**
 * Validate email address
 * 
 * @param string $email Email to validate
 * @return bool True if email is valid
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * 
 * @param string $password Password to validate
 * @return array Array with 'valid' bool and 'message' string
 */
function validatePassword(string $password): array {
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return [
            'valid' => false,
            'message' => 'Password harus minimal ' . PASSWORD_MIN_LENGTH . ' karakter'
        ];
    }
    
    return ['valid' => true, 'message' => 'Password valid'];
}

?>
