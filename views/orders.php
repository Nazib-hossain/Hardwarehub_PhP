<?php
/**
 * Customer Orders History View
 * HardwareHub E-Commerce
 */
$pageTitle = 'My Order History';
require_once __DIR__ . '/layout/header.php';
?>

<div class="orders-page-header">
    <h1>Your Order History</h1>
    <p>Track all past PC hardware component purchases linked to your account.</p>
</div>

<?php if (isset($_GET['placed'])): ?>
    <div class="order-placed-banner">
        <div class="banner-icon">🎉</div>
        <div class="banner-text">
            <h3>Thank You! Your Order Was Successfully Placed</h3>
            <p>Your hardware order has been committed to our system. Our fulfillment team is preparing the components for dispatch.</p>
        </div>
    </div>
<?php endif; ?>

<?php if (empty($orders)): ?>
    <div class="empty-state">
        <div class="empty-icon">📦</div>
        <h2>No Orders Found Yet</h2>
        <p>You haven't placed any hardware orders with us yet.</p>
        <a href="index.php?page=catalog" class="btn btn-primary">Browse Catalog</a>
    </div>
<?php else: ?>
    <div class="orders-list">
        <?php foreach ($orders as $ord): ?>
            <div class="order-card">
                <div class="order-card-header">
                    <div>
                        <span class="order-tag">Order Reference</span>
                        <h3 class="order-number">#ORD-<?= str_pad((string)$ord['order_id'], 6, '0', STR_PAD_LEFT) ?></h3>
                    </div>
                    <div class="order-date-status">
                        <span class="order-date"><?= e(date('F j, Y - g:i A', strtotime($ord['order_date']))) ?></span>
                        <span class="badge-stock badge-in">Status: Confirmed</span>
                    </div>
                </div>

                <div class="order-card-body">
                    <div class="order-product-row">
                        <img src="<?= e($ord['image_url'] ?: 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=150') ?>" 
                             alt="<?= e($ord['product_name']) ?>" 
                             class="order-thumb">
                        <div class="order-product-details">
                            <h4 class="order-product-name">
                                <a href="index.php?page=product&id=<?= (int) $ord['product_id'] ?>">
                                    <?= e($ord['product_name']) ?>
                                </a>
                            </h4>
                            <span class="order-product-cat"><?= e($ord['category']) ?></span>
                            <div class="order-price-calc">
                                <?= (int) $ord['quantity'] ?> unit(s) &times; <?= formatPrice($ord['price']) ?>
                            </div>
                        </div>
                        <div class="order-item-total">
                            <span class="total-label">Subtotal</span>
                            <span class="total-val"><?= formatPrice($ord['total_item_price']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="order-card-footer">
                    <a href="index.php?page=product&id=<?= (int) $ord['product_id'] ?>" class="btn btn-secondary btn-sm">
                        View Product Again
                    </a>
                    <span class="order-tracking-info">🚚 Tracking: <code>HH-TRK-<?= (int)$ord['order_id'] * 1234 ?></code></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
