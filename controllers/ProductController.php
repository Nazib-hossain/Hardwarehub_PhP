<?php
/**
 * ProductController
 * HardwareHub MVC Architecture
 * 
 * Handles catalog browsing, category filtering, search, sorting,
 * and single product details presentation.
 */

require_once __DIR__ . '/../models/Product.php';

class ProductController {
    private Product $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    /**
     * Catalog Page (Page 1)
     * Lists products with responsive search, category filtering, and sorting
     */
    public function index(): void {
        $search = trim($_GET['search'] ?? '');
        $category = trim($_GET['category'] ?? '');
        $sort = trim($_GET['sort'] ?? 'newest');

        $products = $this->productModel->getAll($search, $category, $sort);
        $categories = $this->productModel->getCategories();

        require_once __DIR__ . '/../views/catalog.php';
    }

    /**
     * Single Product Details Page (Page 2)
     * Displays product specs and triggers real-time stock check via Fetch API
     */
    public function show(): void {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $errorMessage = "Invalid product ID requested.";
            http_response_code(400);
            require_once __DIR__ . '/../views/error.php';
            return;
        }

        $product = $this->productModel->getById($id);

        if (!$product) {
            $errorMessage = "The requested hardware product (ID #{$id}) could not be found.";
            http_response_code(404);
            require_once __DIR__ . '/../views/error.php';
            return;
        }

        require_once __DIR__ . '/../views/product_details.php';
    }
}
