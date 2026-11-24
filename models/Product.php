<?php
/**
 * Product Model
 * HardwareHub MVC Architecture
 * 
 * Manages Hardware_Products inventory, CRUD actions, categorization, and stock checks.
 * All queries utilize PDO prepared statements with parameterized inputs.
 */

require_once __DIR__ . '/../config/database.php';

class Product {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Retrieve products with optional search query, category filtering, and sorting
     */
    public function getAll(?string $search = null, ?string $category = null, string $sort = 'newest'): array {
        $sql = "SELECT product_id, name, category, price, stock_quantity, description, image_url, created_at 
                FROM Hardware_Products 
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (name LIKE :search OR description LIKE :search_desc)";
            $params[':search'] = '%' . trim($search) . '%';
            $params[':search_desc'] = '%' . trim($search) . '%';
        }

        if (!empty($category)) {
            $sql .= " AND category = :category";
            $params[':category'] = trim($category);
        }

        // Whitelist allowed sort options to prevent SQL injection in ORDER BY clause
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY price DESC";
                break;
            case 'stock_asc':
                $sql .= " ORDER BY stock_quantity ASC";
                break;
            case 'stock_desc':
                $sql .= " ORDER BY stock_quantity DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY name ASC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY product_id DESC";
                break;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get a single product by primary key ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT product_id, name, category, price, stock_quantity, description, image_url, created_at 
                FROM Hardware_Products 
                WHERE product_id = :id 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    /**
     * Get distinct product categories for filter navigation
     */
    public function getCategories(): array {
        $sql = "SELECT DISTINCT category FROM Hardware_Products ORDER BY category ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Create a new hardware product (Admin only)
     */
    public function create(array $data): int {
        $sql = "INSERT INTO Hardware_Products (name, category, price, stock_quantity, description, image_url) 
                VALUES (:name, :category, :price, :stock_quantity, :description, :image_url)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'           => trim($data['name']),
            ':category'       => trim($data['category']),
            ':price'          => (float) $data['price'],
            ':stock_quantity' => (int) $data['stock_quantity'],
            ':description'    => trim($data['description'] ?? ''),
            ':image_url'      => trim($data['image_url'] ?? '')
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing hardware product (Admin only)
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE Hardware_Products 
                SET name = :name, 
                    category = :category, 
                    price = :price, 
                    stock_quantity = :stock_quantity, 
                    description = :description, 
                    image_url = :image_url 
                WHERE product_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name'           => trim($data['name']),
            ':category'       => trim($data['category']),
            ':price'          => (float) $data['price'],
            ':stock_quantity' => (int) $data['stock_quantity'],
            ':description'    => trim($data['description'] ?? ''),
            ':image_url'      => trim($data['image_url'] ?? ''),
            ':id'             => $id
        ]);
    }

    /**
     * Delete a product by ID (Admin only)
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM Hardware_Products WHERE product_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Check real-time stock quantity for the API endpoint
     */
    public function getStock(int $id): ?array {
        $sql = "SELECT product_id, name, stock_quantity, price FROM Hardware_Products WHERE product_id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Get inventory statistics for admin dashboard
     */
    public function getStats(): array {
        $totalProducts = (int) $this->db->query("SELECT COUNT(*) FROM Hardware_Products")->fetchColumn();
        $lowStock = (int) $this->db->query("SELECT COUNT(*) FROM Hardware_Products WHERE stock_quantity <= 5 AND stock_quantity > 0")->fetchColumn();
        $outOfStock = (int) $this->db->query("SELECT COUNT(*) FROM Hardware_Products WHERE stock_quantity = 0")->fetchColumn();
        $totalInventoryValue = (float) $this->db->query("SELECT SUM(price * stock_quantity) FROM Hardware_Products")->fetchColumn();

        return [
            'total_products'   => $totalProducts,
            'low_stock'        => $lowStock,
            'out_of_stock'     => $outOfStock,
            'total_inv_value'  => $totalInventoryValue,
        ];
    }
}
