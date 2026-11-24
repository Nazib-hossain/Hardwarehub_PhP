<?php
/**
 * JSON Stock Checking API Endpoint
 * Specification Section 5.A: /api/check_stock.php
 * 
 * - Accepts product_id via $_GET
 * - Queries the database for stock_quantity using PDO prepared statement
 * - Returns JSON format:
 *   {"product_id": 102, "stock": 15, "status": "in_stock", "product_name": "...", "price": 999.99}
 * - Consumed asynchronously by front-end JavaScript Fetch API
 */

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../config/database.php';

// Validate input parameter
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Missing or invalid product_id parameter.'
    ]);
    exit;
}

$productId = (int) $_GET['product_id'];

try {
    $db = Database::getConnection();
    $sql = "SELECT product_id, name, price, stock_quantity FROM Hardware_Products WHERE product_id = :id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
        echo json_encode([
            'status'     => 'error',
            'product_id' => $productId,
            'message'    => 'Product not found in hardware inventory.'
        ]);
        exit;
    }

    $stock = (int) $product['stock_quantity'];

    // Determine inventory status
    if ($stock <= 0) {
        $status = 'out_of_stock';
    } elseif ($stock <= 5) {
        $status = 'low_stock';
    } else {
        $status = 'in_stock';
    }

    http_response_code(200);
    echo json_encode([
        'product_id'   => (int) $product['product_id'],
        'stock'        => $stock,
        'status'       => $status,
        'product_name' => $product['name'],
        'price'        => (float) $product['price']
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Internal server error checking inventory stock.'
    ]);
}
