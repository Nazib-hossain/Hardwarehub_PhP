<?php
/**
 * CartController
 * HardwareHub MVC Architecture
 * 
 * Manages session-based shopping cart operations:
 * - Adding items (with quantity validation against available stock)
 * - Updating quantities
 * - Removing items
 * - Checkout order placement and inventory decrement
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';

class CartController {
    private Product $productModel;
    private Order $orderModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->orderModel = new Order();

        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * View Shopping Cart (Page 3)
     */
    public function index(): void {
        $cartItems = [];
        $subtotal = 0.0;
        $totalItems = 0;

        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = $this->productModel->getById((int) $productId);
            if ($product) {
                $itemTotal = (float) $product['price'] * $quantity;
                $subtotal += $itemTotal;
                $totalItems += $quantity;

                $cartItems[] = [
                    'product'    => $product,
                    'quantity'   => $quantity,
                    'item_total' => $itemTotal,
                    'in_stock'   => $product['stock_quantity'] >= $quantity
                ];
            } else {
                // Remove stale item if deleted from database
                unset($_SESSION['cart'][$productId]);
            }
        }

        $tax = $subtotal * 0.05; // 5% standard VAT / hardware tax
        $total = $subtotal + $tax;

        require_once __DIR__ . '/../views/cart.php';
    }

    /**
     * Add item to cart
     */
    public function add(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=cart');
            exit;
        }

        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?: 1;

        if (!$productId || $quantity <= 0) {
            $_SESSION['flash_error'] = 'Invalid product or quantity specified.';
            header('Location: index.php?page=catalog');
            exit;
        }

        $product = $this->productModel->getById($productId);
        if (!$product) {
            $_SESSION['flash_error'] = 'Product not found.';
            header('Location: index.php?page=catalog');
            exit;
        }

        $currentQty = $_SESSION['cart'][$productId] ?? 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $product['stock_quantity']) {
            $_SESSION['flash_error'] = "Cannot add {$quantity} more. Only {$product['stock_quantity']} units available in stock.";
            header("Location: index.php?page=product&id={$productId}");
            exit;
        }

        $_SESSION['cart'][$productId] = $newQty;
        $_SESSION['flash_success'] = "Added '{$product['name']}' to your cart.";
        header('Location: index.php?page=cart');
        exit;
    }

    /**
     * Update cart item quantity
     */
    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=cart');
            exit;
        }

        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if (!$productId) {
            header('Location: index.php?page=cart');
            exit;
        }

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
            $_SESSION['flash_success'] = 'Item removed from cart.';
        } else {
            $product = $this->productModel->getById($productId);
            if ($product && $quantity <= $product['stock_quantity']) {
                $_SESSION['cart'][$productId] = $quantity;
                $_SESSION['flash_success'] = 'Cart updated.';
            } else {
                $_SESSION['flash_error'] = "Requested quantity exceeds available stock ({$product['stock_quantity']}).";
            }
        }

        header('Location: index.php?page=cart');
        exit;
    }

    /**
     * Remove item from cart
     */
    public function remove(): void {
        $productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($productId && isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            $_SESSION['flash_success'] = 'Item removed from your cart.';
        }
        header('Location: index.php?page=cart');
        exit;
    }

    /**
     * Clear all items in cart
     */
    public function clear(): void {
        $_SESSION['cart'] = [];
        $_SESSION['flash_success'] = 'Your cart has been cleared.';
        header('Location: index.php?page=cart');
        exit;
    }

    /**
     * Checkout Page (Page 4) & Order Placement
     */
    public function checkout(): void {
        // Enforce Login for Checkout
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = 'Please log in or create an account to complete your purchase.';
            header('Location: index.php?page=login&redirect=checkout');
            exit;
        }

        // Empty cart check
        if (empty($_SESSION['cart'])) {
            $_SESSION['flash_error'] = 'Your cart is empty. Add products before checking out.';
            header('Location: index.php?page=catalog');
            exit;
        }

        // Handle POST: Submit Order
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $shippingAddress = trim($_POST['shipping_address'] ?? '');
            $paymentMethod = trim($_POST['payment_method'] ?? 'credit_card');

            if (empty($shippingAddress)) {
                $_SESSION['flash_error'] = 'Please provide a valid shipping address.';
                header('Location: index.php?page=checkout');
                exit;
            }

            try {
                // Execute atomic transaction in Order model
                $result = $this->orderModel->createOrder((int) $_SESSION['user_id'], $_SESSION['cart']);

                // Clear cart upon successful order creation
                $_SESSION['cart'] = [];
                $_SESSION['recent_order'] = $result;
                $_SESSION['flash_success'] = 'Your order has been placed successfully! Thank you for shopping with HardwareHub.';

                header('Location: index.php?page=orders&placed=1');
                exit;
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Checkout failed: ' . $e->getMessage();
                header('Location: index.php?page=checkout');
                exit;
            }
        }

        // GET: Render Checkout Form
        $cartItems = [];
        $subtotal = 0.0;
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = $this->productModel->getById((int) $productId);
            if ($product) {
                $itemTotal = (float) $product['price'] * $quantity;
                $subtotal += $itemTotal;
                $cartItems[] = [
                    'product'    => $product,
                    'quantity'   => $quantity,
                    'item_total' => $itemTotal
                ];
            }
        }

        $tax = $subtotal * 0.05;
        $total = $subtotal + $tax;

        require_once __DIR__ . '/../views/checkout.php';
    }

    /**
     * Customer Orders History Page
     */
    public function orders(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $orders = $this->orderModel->getOrdersByUserId((int) $_SESSION['user_id']);
        require_once __DIR__ . '/../views/orders.php';
    }
}
