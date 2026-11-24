<?php
/**
 * Order Model
 * HardwareHub MVC Architecture
 * 
 * Handles order checkout processing, transactional stock decrement,
 * order history retrieval, and inventory integrity.
 */

require_once __DIR__ . '/../config/database.php';

class Order {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Process checkout and save orders using an ACID Database Transaction.
     * Decrements stock_quantity and prevents race condition negative inventory.
     *
     * @param int $userId The ID of the authenticated user
     * @param array $cart Associative array of [product_id => quantity]
     * @return array Created order details
     * @throws Exception If stock is insufficient or query fails
     */
    public function createOrder(int $userId, array $cart): array {
        if (empty($cart)) {
            throw new Exception("Cart is empty. Cannot process checkout.");
        }

        // Begin transaction for data consistency
        $this->db->beginTransaction();

        try {
            $createdOrderIds = [];
            $orderDate = date('Y-m-d H:i:s');

            // Prepared statements for order creation and stock decrement
            $stockCheckStmt = $this->db->prepare("SELECT product_id, name, price, stock_quantity FROM Hardware_Products WHERE product_id = :id FOR UPDATE");
            $stockUpdateStmt = $this->db->prepare("UPDATE Hardware_Products SET stock_quantity = stock_quantity - :qty WHERE product_id = :id AND stock_quantity >= :qty");
            $orderInsertStmt = $this->db->prepare("INSERT INTO Orders (user_id, product_id, quantity, order_date) VALUES (:user_id, :product_id, :quantity, :order_date)");

            foreach ($cart as $productId => $quantity) {
                $productId = (int) $productId;
                $quantity = (int) $quantity;

                if ($quantity <= 0) {
                    continue;
                }

                // 1. Check current stock with pessimistic row lock (FOR UPDATE)
                $stockCheckStmt->execute([':id' => $productId]);
                $product = $stockCheckStmt->fetch();

                if (!$product) {
                    throw new Exception("Product ID #{$productId} no longer exists in our catalog.");
                }

                if ($product['stock_quantity'] < $quantity) {
                    throw new Exception("Insufficient stock for '{$product['name']}'. Requested: {$quantity}, Available: {$product['stock_quantity']}.");
                }

                // 2. Decrement stock safely
                $stockUpdateStmt->execute([
                    ':qty' => $quantity,
                    ':id'  => $productId
                ]);

                if ($stockUpdateStmt->rowCount() === 0) {
                    throw new Exception("Stock allocation failed for '{$product['name']}'. Please try again.");
                }

                // 3. Insert order record
                $orderInsertStmt->execute([
                    ':user_id'    => $userId,
                    ':product_id' => $productId,
                    ':quantity'   => $quantity,
                    ':order_date' => $orderDate
                ]);

                $createdOrderIds[] = (int) $this->db->lastInsertId();
            }

            // Commit transaction after all items are safely booked
            $this->db->commit();

            return [
                'success'   => true,
                'order_ids' => $createdOrderIds,
                'date'      => $orderDate
            ];

        } catch (Exception $e) {
            // Roll back all changes if any single item fails
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Retrieve orders placed by a specific user with joined product metadata
     */
    public function getOrdersByUserId(int $userId): array {
        $sql = "SELECT o.order_id, o.user_id, o.product_id, o.quantity, o.order_date,
                       p.name AS product_name, p.category, p.price, p.image_url,
                       (o.quantity * p.price) AS total_item_price
                FROM Orders o
                JOIN Hardware_Products p ON o.product_id = p.product_id
                WHERE o.user_id = :user_id
                ORDER BY o.order_date DESC, o.order_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Retrieve all orders across the platform for admin reporting
     */
    public function getAllOrders(): array {
        $sql = "SELECT o.order_id, o.user_id, o.product_id, o.quantity, o.order_date,
                       u.name AS customer_name, u.email AS customer_email,
                       p.name AS product_name, p.category, p.price,
                       (o.quantity * p.price) AS total_item_price
                FROM Orders o
                JOIN Users u ON o.user_id = u.user_id
                JOIN Hardware_Products p ON o.product_id = p.product_id
                ORDER BY o.order_date DESC, o.order_id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get aggregate statistics for orders and sales
     */
    public function getOrderStats(): array {
        $sql = "SELECT 
                    COUNT(o.order_id) AS total_orders,
                    COALESCE(SUM(o.quantity * p.price), 0) AS total_revenue,
                    COALESCE(SUM(o.quantity), 0) AS total_items_sold
                FROM Orders o
                JOIN Hardware_Products p ON o.product_id = p.product_id";
        $stmt = $this->db->query($sql);
        return $stmt->fetch() ?: ['total_orders' => 0, 'total_revenue' => 0, 'total_items_sold' => 0];
    }
}
