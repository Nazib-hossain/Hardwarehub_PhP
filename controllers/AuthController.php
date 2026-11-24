<?php
/**
 * AuthController
 * HardwareHub MVC Architecture
 * 
 * Manages user authentication, registration, session security,
 * session fixation prevention via session_regenerate_id(true),
 * and role-based redirects.
 */

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Display Login / Register page
     */
    public function showLogin(): void {
        if (isset($_SESSION['user_id'])) {
            // If already logged in, redirect based on role
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                header('Location: index.php?page=admin');
            } else {
                header('Location: index.php?page=catalog');
            }
            exit;
        }

        $activeTab = $_GET['tab'] ?? 'login';
        require_once __DIR__ . '/../views/login.php';
    }

    /**
     * Process User Login
     * Implements session_regenerate_id(true) on login as required by specification.
     */
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $redirect = trim($_POST['redirect'] ?? '');

        if (empty($email) || empty($password)) {
            $_SESSION['flash_error'] = 'Please enter both email and password.';
            header('Location: index.php?page=login');
            exit;
        }

        $user = $this->userModel->authenticate($email, $password);

        if ($user) {
            // Prevent Session Fixation attacks: regenerate session ID on privilege change
            session_regenerate_id(true);

            $_SESSION['user_id'] = (int) $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role']; // 'admin' or 'customer'

            $_SESSION['flash_success'] = "Welcome back, " . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "!";

            // Route user according to privilege or prior checkout destination
            if ($user['role'] === 'admin') {
                header('Location: index.php?page=admin');
                exit;
            }

            if (!empty($redirect) && in_array($redirect, ['cart', 'checkout'])) {
                header('Location: index.php?page=' . urlencode($redirect));
                exit;
            }

            header('Location: index.php?page=catalog');
            exit;
        } else {
            $_SESSION['flash_error'] = 'Invalid email or password. Please try again.';
            header('Location: index.php?page=login');
            exit;
        }
    }

    /**
     * Process User Registration
     */
    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=login&tab=register');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['flash_error'] = 'All registration fields are required.';
            header('Location: index.php?page=login&tab=register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Please provide a valid email address.';
            header('Location: index.php?page=login&tab=register');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['flash_error'] = 'Password must be at least 6 characters long.';
            header('Location: index.php?page=login&tab=register');
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['flash_error'] = 'Passwords do not match.';
            header('Location: index.php?page=login&tab=register');
            exit;
        }

        if ($this->userModel->emailExists($email)) {
            $_SESSION['flash_error'] = 'An account with this email address already exists.';
            header('Location: index.php?page=login&tab=register');
            exit;
        }

        try {
            $userId = $this->userModel->register($name, $email, $password, 'customer');

            // Automatically log in the user upon successful registration
            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['role'] = 'customer';

            $_SESSION['flash_success'] = 'Account created successfully! Welcome to HardwareHub.';
            header('Location: index.php?page=catalog');
            exit;
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Failed to create account. Please try again.';
            header('Location: index.php?page=login&tab=register');
            exit;
        }
    }

    /**
     * User Logout
     */
    public function logout(): void {
        // Unset session variables
        $_SESSION = [];

        // Destroy session cookie if set
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy session
        session_destroy();

        // Start a fresh session for the flash message
        session_start();
        $_SESSION['flash_success'] = 'You have been logged out successfully.';
        header('Location: index.php?page=login');
        exit;
    }
}
