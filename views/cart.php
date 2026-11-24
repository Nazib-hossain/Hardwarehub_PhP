<?php
/**
 * Shopping Cart View (Page 3)
 * HardwareHub E-Commerce
 * 
 * Session-based cart display:
 * - Lists items from $_SESSION['cart']
 * - Real-time total and tax calculations
 * - Quantity adjustment (+/-) and remove operations
 * - Link to checkout
 */
$pageTitle = 'Shopping Cart';
require_once __DIR__ . '/layout/header.php';
?>

<div class="cart-page-header">
    <h1>Your Shopping Cart</h1>
    <p>Review selected hardware components before proceeding to secure order checkout.</p>
</div>

<?php if (empty($cartItems)): ?>
    <div class="empty-state cart-empty">
        <div class="empty-icon">🛒</div>
        <h2>Your Cart is Currently Empty</h2>
        <p>You haven't added any PC components or hardware to your cart yet.</p>
        <a href="index.php?page=catalog" class="btn btn-primary btn-lg">Browse Hardware Catalog</a>
    </div>
<?php else: ?>
    <div class="cart-layout">
        <!-- Cart Items Table Section -->
        <div class="cart-table-container">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Hardware Item</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <?php 
                            $prod = $item['product']; 
                            $qty = (int) $item['quantity'];
                            $availStock = (int) $prod['stock_quantity'];
                        ?>
                        <tr>
                            <td class="cart-product-cell">
                                <img src="<?= e($prod['image_url'] ?: 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=150') ?>" 
                                     alt="<?= e($prod['name']) ?>" 
                                     class="cart-thumb">
                                <div>
                                    <a href="index.php?page=product&id=<?= (int) $prod['product_id'] ?>" class="cart-item-title">
                                        <?= e($prod['name']) ?>
                                    </a>
                                    <span class="cart-item-cat"><?= e($prod['category']) ?></span>
                                    <?php if ($availStock < $qty): ?>
                                        <span class="cart-stock-warning">⚠️ Only <?= $availStock ?> in stock! Please reduce quantity.</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="cart-price-cell">
                                <?= formatPrice($prod['price']) ?>
                            </td>
                            <td class="cart-qty-cell">
                                <form action="index.php?action=cart_update" method="POST" class="cart-qty-form">
                                    <input type="hidden" name="product_id" value="<?= (int) $prod['product_id'] ?>">
                                    <div class="qty-stepper-small">
                                        <input type="number" 
                                               name="quantity" 
                                               value="<?= $qty ?>" 
                                               min="1" 
                                               max="<?= max(1, $availStock) ?>" 
                                               onchange="this.form.submit()">
                                        <button type="submit" class="btn-update-cart" title="Update Quantity">Update</button>
                                    </div>
                                </form>
                            </td>
                            <td class="cart-subtotal-cell">
                                <?= formatPrice($item['item_total']) ?>
                            </td>
                            <td class="cart-actions-cell">
                                <a href="index.php?action=cart_remove&id=<?= (int) $prod['product_id'] ?>" 
                                   class="btn-remove-item" 
                                   title="Remove item"
                                   onclick="return confirm('Remove <?= addslashes(e($prod['name'])) ?> from cart?');">
                                    ✕ Remove
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-table-footer">
                <a href="index.php?page=catalog" class="btn btn-outline">
                    ← Continue Shopping
                </a>
                <a href="index.php?action=cart_clear" 
                   class="btn btn-danger-outline" 
                   onclick="return confirm('Clear entire shopping cart?');">
                    Clear Cart
                </a>
            </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="cart-summary-sidebar">
            <div class="summary-card">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Items Count:</span>
                    <span><?= $totalItems ?> items</span>
                </div>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span><?= formatPrice($subtotal) ?></span>
                </div>
                <div class="summary-row">
                    <span>Standard Tax (5%):</span>
                    <span><?= formatPrice($tax) ?></span>
                </div>
                <div class="summary-row">
                    <span>Standard Shipping:</span>
                    <span class="text-success">FREE</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row total-row">
                    <strong>Total Amount:</strong>
                    <strong class="total-amount"><?= formatPrice($total) ?></strong>
                </div>

                <div class="summary-actions">
                    <a href="index.php?page=checkout" class="btn btn-primary btn-block btn-lg">
                        Proceed to Checkout →
                    </a>
                </div>

                <div class="security-guarantee">
                    <p>🔒 <strong>Secure Order Guarantee</strong></p>
                    <p><small>Academic verification: Session state preserved via PHP <code>$_SESSION['cart']</code>. ACID transactional decrement upon checkout.</small></p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
