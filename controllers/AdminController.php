<?php
/**
 * AdminController
 * HardwareHub MVC Architecture
 * 
 * Strict Access Control: Restricts access exclusively to users with role 'admin'.
 * Provides full inventory CRUD capabilities (Page 5: Admin Dashboard).
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/User.php';

class AdminController {
    private Product $productModel;
    private Order $orderModel;
    private User $userModel;

    public function __construct() {
        // Enforce RBAC (Role-Based Access Control)
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['flash_error'] = 'Access denied. Administrator privileges required.';
            header('Location: index.php?page=login');
            exit;
        }

        $this->productModel = new Product();
        $this->orderModel = new Order();
        $this->userModel = new User();
    }

    /**
     * Admin Dashboard (Page 5: Inventory Management)
     */
    public function dashboard(): void {
        $products = $this->productModel->getAll(null, null, 'newest');
        $categories = $this->productModel->getCategories();
        $invStats = $this->productModel->getStats();
        $orderStats = $this->orderModel->getOrderStats();
        $recentOrders = $this->orderModel->getAllOrders();

        require_once __DIR__ . '/../views/admin_dashboard.php';
    }

    /**
     * Add a new hardware product
     */
    public function addProduct(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $stock = filter_input(INPUT_POST, 'stock_quantity', FILTER_VALIDATE_INT);
        $description = trim($_POST['description'] ?? '');
        $imageUrl = trim($_POST['image_url'] ?? '');

        if (empty($name) || empty($category) || $price === false || $price < 0 || $stock === false || $stock < 0) {
            $_SESSION['flash_error'] = 'Please fill all required product fields with valid numerical values.';
            header('Location: index.php?page=admin');
            exit;
        }

        try {
            $newId = $this->productModel->create([
                'name'           => $name,
                'category'       => $category,
                'price'          => $price,
                'stock_quantity' => $stock,
                'description'    => $description,
                'image_url'      => $imageUrl
            ]);

            $_SESSION['flash_success'] = "Hardware product '{$name}' (ID #{$newId}) was added successfully.";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Failed to add product: ' . $e->getMessage();
        }

        header('Location: index.php?page=admin');
        exit;
    }

    /**
     * Update an existing product & stock
     */
    public function updateProduct(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin');
            exit;
        }

        $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $stock = filter_input(INPUT_POST, 'stock_quantity', FILTER_VALIDATE_INT);
        $description = trim($_POST['description'] ?? '');
        $imageUrl = trim($_POST['image_url'] ?? '');

        if (!$id || empty($name) || empty($category) || $price === false || $stock === false) {
            $_SESSION['flash_error'] = 'Invalid update data provided.';
            header('Location: index.php?page=admin');
            exit;
        }

        try {
            $this->productModel->update($id, [
                'name'           => $name,
                'category'       => $category,
                'price'          => $price,
                'stock_quantity' => $stock,
                'description'    => $description,
                'image_url'      => $imageUrl
            ]);

            $_SESSION['flash_success'] = "Product #{$id} updated successfully.";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Failed to update product: ' . $e->getMessage();
        }

        header('Location: index.php?page=admin');
        exit;
    }

    /**
     * Delete a product
     */
    public function deleteProduct(): void {
        $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $_SESSION['flash_error'] = 'Invalid product ID specified for deletion.';
            header('Location: index.php?page=admin');
            exit;
        }

        try {
            $this->productModel->delete($id);
            $_SESSION['flash_success'] = "Product #{$id} was permanently removed from inventory.";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Failed to delete product: ' . $e->getMessage();
        }

        header('Location: index.php?page=admin');
        exit;
    }
}
