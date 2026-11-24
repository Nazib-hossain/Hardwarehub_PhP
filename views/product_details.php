<?php
/**
 * Product Details View (Page 2)
 * HardwareHub E-Commerce
 * 
 * Demonstrates:
 * - Real-time Stock Check using Fetch API (/api/check_stock.php)
 * - Dynamic UI updates without page reload (disabling Add-to-Cart when stock is 0)
 * - Safe parameterized display preventing XSS
 */
$pageTitle = $product['name'] ?? 'Product Details';
require_once __DIR__ . '/layout/header.php';

$stock = (int) $product['stock_quantity'];
$isOutOfStock = ($stock <= 0);
?>

<div class="breadcrumb-trail">
    <a href="index.php?page=catalog">Catalog</a>
    <span class="separator">/</span>
    <a href="index.php?page=catalog&category=<?= urlencode($product['category']) ?>"><?= e($product['category']) ?></a>
    <span class="separator">/</span>
    <span class="current-item"><?= e($product['name']) ?></span>
</div>

<div class="product-detail-layout" data-product-id="<?= (int) $product['product_id'] ?>">
    <!-- Product Gallery / Image Preview -->
    <div class="detail-gallery">
        <div class="main-image-container">
            <img id="detailProductImage"
                 src="<?= e($product['image_url'] ?: 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=600') ?>" 
                 alt="<?= e($product['name']) ?>"
                 onerror="this.src='https://placehold.co/600x400/1e293b/38bdf8?text=Hardware'">
        </div>
        <div class="product-id-meta">
            Hardware SKU: <strong>#HH-<?= str_pad((string)$product['product_id'], 5, '0', STR_PAD_LEFT) ?></strong>
        </div>
    </div>

    <!-- Product Purchasing & Information Panel -->
    <div class="detail-content">
        <div class="detail-header">
            <span class="detail-category-badge"><?= e($product['category']) ?></span>
            <h1 class="detail-title"><?= e($product['name']) ?></h1>
        </div>

        <div class="detail-price-box">
            <span class="detail-price-label">Our Price:</span>
            <span class="detail-price-val"><?= formatPrice($product['price']) ?></span>
            <span class="detail-price-tax">Includes 5% Hardware Surcharge & VAT</span>
        </div>

        <!-- Real-Time Stock Status Panel (Manipulated by Fetch API in script.js) -->
        <div class="stock-check-panel" id="stockStatusContainer">
            <div class="stock-panel-header">
                <span class="pulse-indicator"></span>
                <strong>Live Inventory Verification</strong>
                <button type="button" class="btn-refresh-stock" id="btnRefreshStock" title="Trigger fetch() to /api/check_stock.php">
                    🔄 Re-check Stock
                </button>
            </div>
            <div class="stock-panel-body">
                <div id="liveStockBadge" class="live-stock-badge <?= $isOutOfStock ? 'badge-out' : ($stock <= 5 ? 'badge-low' : 'badge-in') ?>">
                    <?= $isOutOfStock ? 'Currently Out of Stock' : ($stock <= 5 ? "Low Stock: Only {$stock} units left" : "In Stock: {$stock} units available") ?>
                </div>
                <small id="stockApiNotice" class="stock-api-notice">
                    ✓ Verified via <code>/api/check_stock.php?product_id=<?= (int) $product['product_id'] ?></code>
                </small>
            </div>
        </div>

        <!-- Add to Cart Form -->
        <form action="index.php?action=cart_add" method="POST" class="detail-cart-form" id="addToCartForm">
            <input type="hidden" name="product_id" value="<?= (int) $product['product_id'] ?>">

            <div class="purchase-controls">
                <div class="qty-control-group">
                    <label for="quantityInput">Quantity:</label>
                    <div class="qty-stepper">
                        <button type="button" class="btn-qty-minus" id="btnQtyMinus" <?= $isOutOfStock ? 'disabled' : '' ?>>-</button>
                        <input type="number" 
                               id="quantityInput" 
                               name="quantity" 
                               value="1" 
                               min="1" 
                               max="<?= max(1, $stock) ?>" 
                               <?= $isOutOfStock ? 'disabled' : '' ?>>
                        <button type="button" class="btn-qty-plus" id="btnQtyPlus" <?= $isOutOfStock ? 'disabled' : '' ?>>+</button>
                    </div>
                </div>

                <button type="submit" 
                        id="btnAddToCart" 
                        class="btn btn-primary btn-lg btn-add-cart" 
                        <?= $isOutOfStock ? 'disabled' : '' ?>>
                    <span class="btn-cart-icon">🛒</span>
                    <span id="btnAddToCartText"><?= $isOutOfStock ? 'Out of Stock' : 'Add to Shopping Cart' ?></span>
                </button>
            </div>
        </form>

        <!-- Product Technical Specifications & Description -->
        <div class="detail-description-section">
            <h3>Product Overview & Specifications</h3>
            <div class="description-text">
                <p><?= nl2br(e($product['description'])) ?></p>
            </div>

            <div class="specs-grid">
                <div class="spec-item">
                    <span class="spec-label">Category:</span>
                    <span class="spec-val"><?= e($product['category']) ?></span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Warranty:</span>
                    <span class="spec-val">3-Year Manufacturer Limited Warranty</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Condition:</span>
                    <span class="spec-val">Brand New, Factory Sealed</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Dispatch:</span>
                    <span class="spec-val">Same Day Shipping from Warehouse</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
