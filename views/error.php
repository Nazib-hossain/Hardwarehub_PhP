<?php
/**
 * Error View
 * HardwareHub E-Commerce
 */
$pageTitle = 'Error';
require_once __DIR__ . '/layout/header.php';
?>

<div class="error-wrapper">
    <div class="error-card">
        <div class="error-code-badge"><?= http_response_code() ?: 404 ?></div>
        <h1 class="error-title">Page or Resource Not Found</h1>
        <p class="error-desc">
            <?= isset($errorMessage) ? e($errorMessage) : 'The hardware product, page, or action you requested could not be found or you may not have sufficient permissions to access it.' ?>
        </p>

        <div class="error-actions">
            <a href="index.php?page=catalog" class="btn btn-primary btn-lg">
                Return to Hardware Catalog
            </a>
            <a href="index.php?page=cart" class="btn btn-outline btn-lg">
                View Shopping Cart
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
