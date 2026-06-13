<?php
/**
 * User Model - Handles user data and authentication
 * 
 * Manages user accounts, authentication, and user-related database operations.
 * 
 * @author Development Team
 * @version 1.0
 */
class User {
    private \PDO $pdo;
    
    /**
     * Constructor
     * 
     * @param \PDO $pdo Database connection object
     */
    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Authenticate user with username and password
     * 
     * Verifies credentials against the users table and returns user data
     * if authentication is successful.
     * 
     * @param string $username Username to authenticate
     * @param string $password Password to verify (plain text)
     * @return array|false User data array on success, false on failure
     */
    public function login(string $username, string $password): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Get all users from database
     * 
     * Retrieves all user records sorted by ID.
     * 
     * @return array Array of user records
     */
    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return array|null User data or null if not found
     */
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
    
    /**
     * Create new user account
     * 
     * Hashes the password and inserts new user record.
     * 
     * @param string $username Username for new account
     * @param string $password Plain text password (will be hashed)
     * @param string $role User role (admin, manager, direktur)
     * @return bool True on success, false on failure
     * @throws \PDOException
     */
    public function create(string $username, string $password, string $role): bool {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $hash, $role]);
    }
    
    /**
     * Update user account
     * 
     * Updates username and role. Password is optional and only updated if provided.
     * 
     * @param int $id User ID to update
     * @param string $username New username
     * @param string $role New role
     * @param string|null $password New password (optional, plain text)
     * @return bool True on success, false on failure
     * @throws \PDOException
     */
    public function update(int $id, string $username, string $role, ?string $password = null): bool {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE users SET username=?, role=?, password=? WHERE id=?");
            return $stmt->execute([$username, $role, $hash, $id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE users SET username=?, role=? WHERE id=?");
            return $stmt->execute([$username, $role, $id]);
        }
    }
    
    /**
     * Delete user account
     * 
     * Permanently removes user from database.
     * 
     * @param int $id User ID to delete
     * @return bool True on success, false on failure
     * @throws \PDOException
     */
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>