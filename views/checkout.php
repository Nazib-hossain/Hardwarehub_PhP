<?php
/**
 * Checkout View (Page 4)
 * HardwareHub E-Commerce
 * 
 * Demonstrates:
 * - Order submission form
 * - Loops through $_SESSION['cart']
 * - Order verification & customer information input
 */
$pageTitle = 'Order Checkout';
require_once __DIR__ . '/layout/header.php';
?>

<div class="checkout-page-header">
    <h1>Complete Your Order</h1>
    <p>Please enter your shipping and billing details to finalize your purchase.</p>
</div>

<div class="checkout-layout">
    <!-- Checkout Form Section -->
    <div class="checkout-form-container">
        <form action="index.php?page=checkout" method="POST" class="checkout-form" id="checkoutForm">
            <div class="form-section">
                <h3 class="section-title">
                    <span class="step-num">1</span> Customer Information
                </h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="customerName">Full Name</label>
                        <input type="text" id="customerName" value="<?= e($_SESSION['user_name'] ?? '') ?>" readonly class="readonly-field">
                    </div>
                    <div class="form-group">
                        <label for="customerEmail">Email Address</label>
                        <input type="email" id="customerEmail" value="<?= e($_SESSION['user_email'] ?? '') ?>" readonly class="readonly-field">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">
                    <span class="step-num">2</span> Shipping & Delivery Address
                </h3>
                <div class="form-group">
                    <label for="shippingAddress">Street Address & City *</label>
                    <textarea id="shippingAddress" 
                              name="shipping_address" 
                              rows="3" 
                              placeholder="e.g. 742 Evergreen Terrace, Suite 100, Tech City, TX 75001" 
                              required></textarea>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="contactPhone">Contact Phone Number</label>
                        <input type="tel" id="contactPhone" name="contact_phone" placeholder="+1 (555) 019-2834">
                    </div>
                    <div class="form-group">
                        <label for="deliveryNotes">Special Delivery Instructions (Optional)</label>
                        <input type="text" id="deliveryNotes" name="delivery_notes" placeholder="Leave at reception / Ring bell">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">
                    <span class="step-num">3</span> Payment Method Simulation
                </h3>
                <div class="payment-options">
                    <label class="payment-card selected">
                        <input type="radio" name="payment_method" value="credit_card" checked>
                        <div class="payment-details">
                            <strong>💳 Credit / Debit Card (Mock)</strong>
                            <small>Instant processing & immediate dispatch</small>
                        </div>
                    </label>
                    <label class="payment-card">
                        <input type="radio" name="payment_method" value="cod">
                        <div class="payment-details">
                            <strong>📦 Cash on Delivery / Bank Transfer</strong>
                            <small>Pay upon package inspection</small>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-submit-row">
                <a href="index.php?page=cart" class="btn btn-outline">
                    ← Return to Cart
                </a>
                <button type="submit" class="btn btn-primary btn-lg" id="btnPlaceOrder">
                    Confirm & Place Order (<?= formatPrice($total) ?>)
                </button>
            </div>
        </form>
    </div>

    <!-- Order Review Sidebar -->
    <div class="checkout-review-sidebar">
        <div class="summary-card">
            <h3>Items in Your Order</h3>
            <div class="checkout-items-list">
                <?php foreach ($cartItems as $item): ?>
                    <?php $prod = $item['product']; ?>
                    <div class="checkout-item-row">
                        <div class="checkout-item-meta">
                            <span class="checkout-item-qty"><?= (int) $item['quantity'] ?>x</span>
                            <span class="checkout-item-name"><?= e($prod['name']) ?></span>
                        </div>
                        <span class="checkout-item-subtotal"><?= formatPrice($item['item_total']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row">
                <span>Subtotal:</span>
                <span><?= formatPrice($subtotal) ?></span>
            </div>
            <div class="summary-row">
                <span>VAT & Hardware Tax (5%):</span>
                <span><?= formatPrice($tax) ?></span>
            </div>
            <div class="summary-row">
                <span>Express Delivery:</span>
                <span class="text-success">FREE</span>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row total-row">
                <strong>Grand Total:</strong>
                <strong class="total-amount"><?= formatPrice($total) ?></strong>
            </div>

            <div class="transaction-audit-notice">
                <p>⚙️ <strong>Transactional Execution</strong></p>
                <small>Upon clicking "Confirm & Place Order", the system will atomically insert records into <code>Orders</code> and decrement corresponding product stock in <code>Hardware_Products</code> via PDO Transactions.</small>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
