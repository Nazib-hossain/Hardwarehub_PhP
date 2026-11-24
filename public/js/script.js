/**
 * HardwareHub - Front-End Script
 * CIS324 Academic Requirements:
 * - Real-time Stock Verification via Fetch API (/api/check_stock.php)
 * - Dynamic UI updates (disabling Add-to-Cart when stock is 0 without reloading)
 * - Admin modal handling for product CRUD
 * - Interactive Quantity Steppers
 */

document.addEventListener('DOMContentLoaded', () => {
    initRealTimeStockCheck();
    initQuantitySteppers();
    initAdminModal();
    initAlertDismissals();
});

/**
 * Specification Section 5.A: Real-Time Stock Check via Fetch API
 * 
 * Fetches JSON from /api/check_stock.php?product_id={id}
 * Updates:
 * - Stock status badge (In Stock, Low Stock, Out of Stock)
 * - Quantity input max attribute
 * - Add-to-Cart button enabled/disabled state
 */
function initRealTimeStockCheck() {
    const detailContainer = document.querySelector('.product-detail-layout');
    if (!detailContainer) return;

    const productId = detailContainer.getAttribute('data-product-id');
    if (!productId) return;

    const liveBadge = document.getElementById('liveStockBadge');
    const refreshBtn = document.getElementById('btnRefreshStock');
    const addToCartBtn = document.getElementById('btnAddToCart');
    const addToCartText = document.getElementById('btnAddToCartText');
    const quantityInput = document.getElementById('quantityInput');
    const btnMinus = document.getElementById('btnQtyMinus');
    const btnPlus = document.getElementById('btnQtyPlus');

    const checkStock = async () => {
        try {
            if (refreshBtn) {
                refreshBtn.textContent = '⏳ Checking...';
                refreshBtn.disabled = true;
            }

            // Call the specified JSON endpoint
            const response = await fetch(`api/check_stock.php?product_id=${encodeURIComponent(productId)}`);
            
            if (!response.ok) {
                throw new Error(`Server returned status: ${response.status}`);
            }

            const data = await response.json();

            if (data.status === 'error') {
                if (liveBadge) {
                    liveBadge.className = 'live-stock-badge badge-out';
                    liveBadge.textContent = 'Stock Info Unavailable';
                }
                return;
            }

            const stock = parseInt(data.stock, 10);

            // Update Badge and Button State according to stock
            if (stock <= 0) {
                if (liveBadge) {
                    liveBadge.className = 'live-stock-badge badge-out';
                    liveBadge.textContent = 'Currently Out of Stock (0 units)';
                }
                if (addToCartBtn) {
                    addToCartBtn.disabled = true;
                    addToCartBtn.classList.add('btn-disabled');
                    addToCartBtn.classList.remove('btn-primary');
                }
                if (addToCartText) {
                    addToCartText.textContent = 'Out of Stock';
                }
                if (quantityInput) {
                    quantityInput.disabled = true;
                    quantityInput.value = 0;
                }
                if (btnMinus) btnMinus.disabled = true;
                if (btnPlus) btnPlus.disabled = true;

            } else if (stock <= 5) {
                if (liveBadge) {
                    liveBadge.className = 'live-stock-badge badge-low';
                    liveBadge.textContent = `Low Stock Alert: Only ${stock} left!`;
                }
                if (addToCartBtn) {
                    addToCartBtn.disabled = false;
                    addToCartBtn.classList.remove('btn-disabled');
                    addToCartBtn.classList.add('btn-primary');
                }
                if (addToCartText) {
                    addToCartText.textContent = 'Add to Shopping Cart';
                }
                if (quantityInput) {
                    quantityInput.disabled = false;
                    quantityInput.max = stock;
                    if (parseInt(quantityInput.value, 10) > stock) {
                        quantityInput.value = stock;
                    }
                }
                if (btnMinus) btnMinus.disabled = false;
                if (btnPlus) btnPlus.disabled = false;

            } else {
                if (liveBadge) {
                    liveBadge.className = 'live-stock-badge badge-in';
                    liveBadge.textContent = `In Stock: ${stock} units available`;
                }
                if (addToCartBtn) {
                    addToCartBtn.disabled = false;
                    addToCartBtn.classList.remove('btn-disabled');
                    addToCartBtn.classList.add('btn-primary');
                }
                if (addToCartText) {
                    addToCartText.textContent = 'Add to Shopping Cart';
                }
                if (quantityInput) {
                    quantityInput.disabled = false;
                    quantityInput.max = stock;
                }
                if (btnMinus) btnMinus.disabled = false;
                if (btnPlus) btnPlus.disabled = false;
            }

        } catch (error) {
            console.error('Fetch error during stock check:', error);
            if (liveBadge) {
                liveBadge.textContent = 'Live stock verification offline';
            }
        } finally {
            if (refreshBtn) {
                refreshBtn.textContent = '🔄 Re-check Stock';
                refreshBtn.disabled = false;
            }
        }
    };

    // Execute live stock check on page load
    checkStock();

    // Attach to refresh button
    if (refreshBtn) {
        refreshBtn.addEventListener('click', checkStock);
    }
}

/**
 * Quantity stepper buttons (+ / -)
 */
function initQuantitySteppers() {
    const minusBtn = document.getElementById('btnQtyMinus');
    const plusBtn = document.getElementById('btnQtyPlus');
    const qtyInput = document.getElementById('quantityInput');

    if (!qtyInput) return;

    if (minusBtn) {
        minusBtn.addEventListener('click', () => {
            const currentVal = parseInt(qtyInput.value, 10) || 1;
            const min = parseInt(qtyInput.min, 10) || 1;
            if (currentVal > min) {
                qtyInput.value = currentVal - 1;
            }
        });
    }

    if (plusBtn) {
        plusBtn.addEventListener('click', () => {
            const currentVal = parseInt(qtyInput.value, 10) || 1;
            const max = parseInt(qtyInput.max, 10) || 999;
            if (currentVal < max) {
                qtyInput.value = currentVal + 1;
            }
        });
    }
}

/**
 * Admin Product Add/Edit Modal Handling
 */
function initAdminModal() {
    const modal = document.getElementById('productFormModal');
    if (!modal) return;

    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('productForm');
    const formProductId = document.getElementById('formProductId');
    const formName = document.getElementById('formName');
    const formCategory = document.getElementById('formCategory');
    const formPrice = document.getElementById('formPrice');
    const formStock = document.getElementById('formStock');
    const formImage = document.getElementById('formImage');
    const formDescription = document.getElementById('formDescription');

    // Attach click listeners to all Edit buttons in the table
    document.querySelectorAll('.btn-edit-product').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const category = btn.getAttribute('data-category');
            const price = btn.getAttribute('data-price');
            const stock = btn.getAttribute('data-stock');
            const desc = btn.getAttribute('data-description');
            const image = btn.getAttribute('data-image');

            modalTitle.textContent = `Edit Product #${id}: ${name}`;
            form.action = 'index.php?action=admin_product_update';
            formProductId.value = id;
            formName.value = name;
            formCategory.value = category;
            formPrice.value = price;
            formStock.value = stock;
            formImage.value = image;
            formDescription.value = desc;

            modal.style.display = 'flex';
        });
    });

    // Close modal when clicking outside dialog
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
}

/**
 * Auto-dismiss flash alerts after 6 seconds
 */
function initAlertDismissals() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 6000);
    });
}
