<?php
/**
 * Global Helper Functions
 * HardwareHub MVC Architecture
 * 
 * Provides XSS sanitization (htmlspecialchars), price formatting,
 * session cart counts, and authentication helpers.
 */

if (!function_exists('e')) {
    /**
     * Sanitize output to prevent Cross-Site Scripting (XSS)
     * Meets CIS324 security requirements
     */
    function e(?string $string): string {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatPrice')) {
    /**
     * Format a numerical price into standard BDT currency string
     */
    function formatPrice(float|int|string $price): string {
        return 'BDT ' . number_format((float) $price, 2);
    }
}

if (!function_exists('cartCount')) {
    /**
     * Get the total quantity of items currently in the session shopping cart
     */
    function cartCount(): int {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            return 0;
        }
        return array_sum($_SESSION['cart']);
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}
