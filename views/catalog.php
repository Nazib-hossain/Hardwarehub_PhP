<?php
/**
 * Catalog View (Page 1)
 * HardwareHub E-Commerce
 */
$pageTitle = 'Hardware Catalog';
require_once __DIR__ . '/layout/header.php';
?>

<div class="catalog-header-section">
    <div class="hero-banner">
        <div class="hero-content">
            <span class="hero-badge">High Performance Computing</span>
            <h1 class="hero-title">Extreme PC Hardware & Components</h1>
            <p class="hero-subtitle">Equip your battle-station with cutting-edge processors, graphic cards, lightning NVMe storage, and cooling gear.</p>
        </div>
    </div>

    <!-- Filter and Search Toolbar -->
    <div class="catalog-toolbar">
        <!-- Category Filter Pills -->
        <div class="category-pills">
            <a href="index.php?page=catalog" class="pill <?= empty($_GET['category']) ? 'active' : '' ?>">All Hardware</a>
            <?php foreach ($categories as $cat): ?>
                <a href="index.php?page=catalog&category=<?= urlencode($cat) ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>" 
                   class="pill <?= (isset($_GET['category']) && $_GET['category'] === $cat) ? 'active' : '' ?>">
                    <?= e($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Sort Control Form -->
        <form action="index.php" method="GET" class="sort-form">
            <input type="hidden" name="page" value="catalog">
            <?php if (!empty($_GET['search'])): ?>
                <input type="hidden" name="search" value="<?= e($_GET['search']) ?>">
            <?php endif; ?>
            <?php if (!empty($_GET['category'])): ?>
                <input type="hidden" name="category" value="<?= e($_GET['category']) ?>">
            <?php endif; ?>

            <label for="sort-select">Sort By:</label>
            <select name="sort" id="sort-select" onchange="this.form.submit()">
                <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Newest First</option>
                <option value="price_asc" <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price_desc" <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Name: A to Z</option>
                <option value="stock_desc" <?= ($_GET['sort'] ?? '') === 'stock_desc' ? 'selected' : '' ?>>Highest Stock</option>
            </select>
        </form>
    </div>
</div>

<!-- Active Filter Display -->
<?php if (!empty($_GET['search']) || !empty($_GET['category'])): ?>
    <div class="active-filters-bar">
        <span>Showing results for: </span>
        <?php if (!empty($_GET['search'])): ?>
            <span class="filter-tag">Keyword: "<?= e($_GET['search']) ?>"</span>
        <?php endif; ?>
        <?php if (!empty($_GET['category'])): ?>
            <span class="filter-tag">Category: "<?= e($_GET['category']) ?>"</span>
        <?php endif; ?>
        <a href="index.php?page=catalog" class="btn-clear-filters">Reset Filters</a>
    </div>
<?php endif; ?>

<!-- Product Grid -->
<?php if (empty($products)): ?>
    <div class="empty-state">
        <div class="empty-icon">🔍</div>
        <h3>No Hardware Products Found</h3>
        <p>No products match your current search or category criteria. Try resetting filters.</p>
        <a href="index.php?page=catalog" class="btn btn-primary">View All Products</a>
    </div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $item): ?>
            <?php 
                $stock = (int) $item['stock_quantity'];
                $isOutOfStock = ($stock <= 0);
                $isLowStock = ($stock > 0 && $stock <= 5);
            ?>
            <div class="product-card">
                <div class="product-image-wrap">
                    <img src="<?= e($item['image_url'] ?: 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=600') ?>" 
                         alt="<?= e($item['name']) ?>" 
                         loading="lazy"
                         onerror="this.src='https://placehold.co/600x400/1e293b/38bdf8?text=Hardware'">
                    
                    <!-- Stock Badge -->
                    <?php if ($isOutOfStock): ?>
                        <span class="badge-stock badge-out">Out of Stock</span>
                    <?php elseif ($isLowStock): ?>
                        <span class="badge-stock badge-low">Only <?= $stock ?> Left</span>
                    <?php else: ?>
                        <span class="badge-stock badge-in">In Stock (<?= $stock ?>)</span>
                    <?php endif; ?>

                    <span class="product-category-tag"><?= e($item['category']) ?></span>
                </div>

                <div class="product-info">
                    <h3 class="product-title">
                        <a href="index.php?page=product&id=<?= (int) $item['product_id'] ?>">
                            <?= e($item['name']) ?>
                        </a>
                    </h3>
                    
                    <p class="product-desc-excerpt">
                        <?= e(substr($item['description'] ?? '', 0, 95)) ?>...
                    </p>

                    <div class="product-footer">
                        <div class="price-container">
                            <span class="price-label">Price</span>
                            <span class="product-price"><?= formatPrice($item['price']) ?></span>
                        </div>

                        <div class="product-actions">
                            <a href="index.php?page=product&id=<?= (int) $item['product_id'] ?>" class="btn btn-secondary btn-sm">
                                Details
                            </a>

                            <?php if (!$isOutOfStock): ?>
                                <form action="index.php?action=cart_add" method="POST" class="inline-cart-form">
                                    <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-primary btn-sm" title="Add 1 to Cart">
                                        + Cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <button type="button" class="btn btn-disabled btn-sm" disabled>
                                    Sold Out
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
