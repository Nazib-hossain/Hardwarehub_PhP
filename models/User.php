<?php
/**
 * User Model
 * HardwareHub MVC Architecture
 * 
 * Handles user authentication, registration, and role checking.
 * Strict Security: Uses PDO prepared statements (anti-SQLi) and password_hash (bcrypt).
 */

require_once __DIR__ . '/../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Authenticate user credentials
     * Uses PDO prepared statement and password_verify() to defeat timing attacks & SQLi.
     */
    public function authenticate(string $email, string $password): ?array {
        $sql = "SELECT user_id, name, email, password_hash, role, created_at 
                FROM Users 
                WHERE email = :email 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => trim($email)]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Remove hash from returned memory object for defense-in-depth
            unset($user['password_hash']);
            return $user;
        }

        return null;
    }

    /**
     * Register a new user
     * Hashes password using bcrypt (PASSWORD_BCRYPT) and binds params safely.
     */
    public function register(string $name, string $email, string $password, string $role = 'customer'): int {
        // Enforce valid roles only
        $role = ($role === 'admin') ? 'admin' : 'customer';

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO Users (name, email, password_hash, role) 
                VALUES (:name, :email, :password_hash, :role)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'          => trim($name),
            ':email'         => strtolower(trim($email)),
            ':password_hash' => $passwordHash,
            ':role'          => $role
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Check if an email address is already registered
     */
    public function emailExists(string $email): bool {
        $sql = "SELECT COUNT(*) FROM Users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => strtolower(trim($email))]);
        return ((int) $stmt->fetchColumn()) > 0;
    }

    /**
     * Find user by ID
     */
    public function findById(int $userId): ?array {
        $sql = "SELECT user_id, name, email, role, created_at FROM Users WHERE user_id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find user by Email
     */
    public function findByEmail(string $email): ?array {
        $sql = "SELECT user_id, name, email, role, created_at FROM Users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => strtolower(trim($email))]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Get all users for admin reporting
     */
    public function getAll(): array {
        $sql = "SELECT user_id, name, email, role, created_at FROM Users ORDER BY user_id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
