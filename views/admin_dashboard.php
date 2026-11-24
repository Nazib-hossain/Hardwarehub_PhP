<?php
/**
 * Admin Dashboard View (Page 5: Inventory Management)
 * HardwareHub E-Commerce
 * 
 * Strict RBAC: Accessible only by users with role 'admin'.
 * Full CRUD:
 * - View all hardware products & inventory stock levels
 * - Add new hardware product with validations
 * - Update existing product specifications and stock quantity
 * - Delete product from database
 * - View real-time store analytics & recent customer orders
 */
$pageTitle = 'Admin Inventory Dashboard';
require_once __DIR__ . '/layout/header.php';
?>

<div class="admin-header-row">
    <div>
        <span class="badge-role-admin">🛡️ Role: Administrator</span>
        <h1 class="admin-title">Hardware Inventory Management</h1>
        <p class="admin-subtitle">Real-time catalog monitoring, stock adjustment, and full CRUD hardware operations.</p>
    </div>
    <div>
        <a href="#addProductSection" class="btn btn-primary" onclick="document.getElementById('productFormModal').style.display='flex';">
            + Add New Product
        </a>
    </div>
</div>

<!-- Admin KPI Metrics Cards -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon">📦</div>
        <div class="kpi-data">
            <span class="kpi-value"><?= (int) $invStats['total_products'] ?></span>
            <span class="kpi-label">Total Hardware SKUs</span>
        </div>
    </div>

    <div class="kpi-card <?= $invStats['low_stock'] > 0 ? 'kpi-warning' : '' ?>">
        <div class="kpi-icon">⚠️</div>
        <div class="kpi-data">
            <span class="kpi-value"><?= (int) $invStats['low_stock'] ?></span>
            <span class="kpi-label">Low Stock Alerts (&le; 5)</span>
        </div>
    </div>

    <div class="kpi-card <?= $invStats['out_of_stock'] > 0 ? 'kpi-danger' : '' ?>">
        <div class="kpi-icon">🚫</div>
        <div class="kpi-data">
            <span class="kpi-value"><?= (int) $invStats['out_of_stock'] ?></span>
            <span class="kpi-label">Out of Stock Items</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon">💰</div>
        <div class="kpi-data">
            <span class="kpi-value"><?= formatPrice($orderStats['total_revenue'] ?? 0) ?></span>
            <span class="kpi-label">Total Sales Revenue</span>
        </div>
    </div>
</div>

<!-- Main Inventory Table Section -->
<div class="admin-section-card">
    <div class="admin-card-header">
        <h2>Hardware Products Table (CRUD)</h2>
        <span class="table-count-badge"><?= count($products) ?> items in database</span>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>SKU ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock Level</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?>
                    <?php 
                        $stock = (int) $prod['stock_quantity']; 
                        $statusClass = ($stock <= 0) ? 'badge-out' : (($stock <= 5) ? 'badge-low' : 'badge-in');
                        $statusText = ($stock <= 0) ? 'Out of Stock' : (($stock <= 5) ? 'Low Stock' : 'Adequate');
                    ?>
                    <tr>
                        <td><code>#<?= (int) $prod['product_id'] ?></code></td>
                        <td class="admin-prod-cell">
                            <img src="<?= e($prod['image_url'] ?: 'https://placehold.co/80x80/1e293b/38bdf8?text=HW') ?>" 
                                 alt="<?= e($prod['name']) ?>" 
                                 class="admin-thumb">
                            <div>
                                <strong><?= e($prod['name']) ?></strong>
                                <small class="text-muted d-block"><?= e(substr($prod['description'] ?? '', 0, 60)) ?>...</small>
                            </div>
                        </td>
                        <td><span class="category-pill-small"><?= e($prod['category']) ?></span></td>
                        <td><strong><?= formatPrice($prod['price']) ?></strong></td>
                        <td>
                            <span class="stock-num-badge <?= $stock <= 5 ? 'text-danger font-bold' : '' ?>">
                                <?= $stock ?> units
                            </span>
                        </td>
                        <td>
                            <span class="badge-stock <?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
                        <td class="admin-action-cell">
                            <button type="button" 
                                    class="btn btn-outline btn-xs btn-edit-product"
                                    data-id="<?= (int) $prod['product_id'] ?>"
                                    data-name="<?= e($prod['name']) ?>"
                                    data-category="<?= e($prod['category']) ?>"
                                    data-price="<?= e($prod['price']) ?>"
                                    data-stock="<?= (int) $prod['stock_quantity'] ?>"
                                    data-description="<?= e($prod['description'] ?? '') ?>"
                                    data-image="<?= e($prod['image_url'] ?? '') ?>">
                                ✏️ Edit
                            </button>

                            <form action="index.php?action=admin_product_delete" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete product #<?= (int) $prod['product_id'] ?> (<?= addslashes(e($prod['name'])) ?>)? This cannot be undone.');">
                                <input type="hidden" name="product_id" value="<?= (int) $prod['product_id'] ?>">
                                <button type="submit" class="btn btn-danger-outline btn-xs">
                                    🗑️ Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Orders Section -->
<div class="admin-section-card mt-4">
    <div class="admin-card-header">
        <h2>Recent Customer Orders</h2>
        <span class="table-count-badge"><?= count($recentOrders) ?> orders logged</span>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No orders placed yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentOrders as $ord): ?>
                        <tr>
                            <td><code>#ORD-<?= (int) $ord['order_id'] ?></code></td>
                            <td><?= e(date('M d, Y H:i', strtotime($ord['order_date']))) ?></td>
                            <td>
                                <strong><?= e($ord['customer_name']) ?></strong><br>
                                <small class="text-muted"><?= e($ord['customer_email']) ?></small>
                            </td>
                            <td><?= e($ord['product_name']) ?></td>
                            <td><?= e($ord['category']) ?></td>
                            <td><?= (int) $ord['quantity'] ?></td>
                            <td><strong><?= formatPrice($ord['total_item_price']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Dialog for Add / Edit Product -->
<div id="productFormModal" class="modal-overlay" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Hardware Product</h3>
            <button type="button" class="modal-close" onclick="document.getElementById('productFormModal').style.display='none';">&times;</button>
        </div>
        <form id="productForm" action="index.php?action=admin_product_add" method="POST" class="modal-form">
            <input type="hidden" name="product_id" id="formProductId" value="">

            <div class="form-group">
                <label for="formName">Product Name *</label>
                <input type="text" name="name" id="formName" required placeholder="e.g. AMD Ryzen 9 7950X Processor">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="formCategory">Category *</label>
                    <input type="text" name="category" id="formCategory" required placeholder="Processors, Graphics Cards, etc.">
                </div>
                <div class="form-group">
                    <label for="formPrice">Price ($ USD) *</label>
                    <input type="number" name="price" id="formPrice" step="0.01" min="0" required placeholder="499.99">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="formStock">Stock Quantity *</label>
                    <input type="number" name="stock_quantity" id="formStock" min="0" required placeholder="20">
                </div>
                <div class="form-group">
                    <label for="formImage">Image URL</label>
                    <input type="url" name="image_url" id="formImage" placeholder="https://images.unsplash.com/...">
                </div>
            </div>

            <div class="form-group">
                <label for="formDescription">Product Description / Specifications</label>
                <textarea name="description" id="formDescription" rows="4" placeholder="Detailed technical specifications..."></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('productFormModal').style.display='none';">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitModal">Save Hardware Product</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
