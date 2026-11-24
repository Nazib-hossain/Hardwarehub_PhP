<?php
/**
 * HardwareHub - Main Entry Point & Router
 * Architecture: Model-View-Controller (MVC)
 * Tech Stack: PHP, MySQL (PDO), Vanilla JS, CSS3
 * Academic Guideline: CIS324 Academic Requirements
 * 
 * Features:
 * - Session initialization & state management
 * - Security headers & XSS protection
 * - Centralized controller dispatch
 */

// Initialize PHP Session
if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session cookie params where applicable
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Global helper functions
require_once __DIR__ . '/includes/helpers.php';

// Load MVC Controllers
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/ProductController.php';
require_once __DIR__ . '/controllers/CartController.php';
require_once __DIR__ . '/controllers/AdminController.php';

// Route action requests (POST submissions or specific command triggers)
$action = $_REQUEST['action'] ?? null;

if ($action !== null) {
    switch ($action) {
        // Authentication Actions
        case 'auth_login':
            (new AuthController())->login();
            break;
        case 'auth_register':
            (new AuthController())->register();
            break;

        // Shopping Cart Actions
        case 'cart_add':
            (new CartController())->add();
            break;
        case 'cart_update':
            (new CartController())->update();
            break;
        case 'cart_remove':
            (new CartController())->remove();
            break;
        case 'cart_clear':
            (new CartController())->clear();
            break;

        // Admin Inventory Actions (Full CRUD)
        case 'admin_product_add':
            (new AdminController())->addProduct();
            break;
        case 'admin_product_update':
            (new AdminController())->updateProduct();
            break;
        case 'admin_product_delete':
            (new AdminController())->deleteProduct();
            break;

        default:
            http_response_code(400);
            $errorMessage = "Unrecognized system action: " . htmlspecialchars($action, ENT_QUOTES, 'UTF-8');
            require_once __DIR__ . '/views/error.php';
            exit;
    }
}

// Route Page Views
$page = $_GET['page'] ?? 'catalog';

switch ($page) {
    case 'catalog':
        (new ProductController())->index();
        break;

    case 'product':
        (new ProductController())->show();
        break;

    case 'cart':
        (new CartController())->index();
        break;

    case 'checkout':
        (new CartController())->checkout();
        break;

    case 'orders':
        (new CartController())->orders();
        break;

    case 'admin':
        (new AdminController())->dashboard();
        break;

    case 'login':
        (new AuthController())->showLogin();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    default:
        http_response_code(404);
        $errorMessage = "The page requested ('" . htmlspecialchars($page, ENT_QUOTES, 'UTF-8') . "') does not exist.";
        require_once __DIR__ . '/views/error.php';
        break;
}
