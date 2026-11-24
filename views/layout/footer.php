    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container footer-container">
            <div class="footer-col brand-col">
                <div class="brand-logo">
                    <span class="logo-icon">⚡</span>
                    <span class="logo-text">Hardware<span class="highlight">Hub</span></span>
                </div>
                <p class="footer-desc">
                    Next-generation PC hardware, processors, graphics cards, and gaming components. Built with academic-grade PHP MVC, PDO, and real-time Fetch API.
                </p>
                <div class="tech-badges">
                    <span class="tech-badge">PHP 8.x MVC</span>
                    <span class="tech-badge">MySQL PDO</span>
                    <span class="tech-badge">Vanilla JS Fetch</span>
                    <span class="tech-badge">XAMPP Ready</span>
                </div>
            </div>

            <div class="footer-col">
                <h4>Quick Navigation</h4>
                <ul class="footer-links">
                    <li><a href="index.php?page=catalog">Hardware Catalog</a></li>
                    <li><a href="index.php?page=cart">Shopping Cart</a></li>
                    <li><a href="index.php?page=orders">Order History</a></li>
                    <li><a href="index.php?page=admin">Admin Dashboard</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Security & Standards</h4>
                <ul class="footer-links">
                    <li>✓ PDO Prepared Statements (SQLi Safe)</li>
                    <li>✓ Passwords hashed with Bcrypt</li>
                    <li>✓ XSS Sanitization (`htmlspecialchars`)</li>
                    <li>✓ Session Fixation Protection</li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Academic Testing</h4>
                <p class="demo-creds">
                    <strong>Admin Demo:</strong><br>
                    <code>admin@hardwarehub.com</code> / <code>Admin@123</code><br><br>
                    <strong>Customer Demo:</strong><br>
                    <code>customer@hardwarehub.com</code> / <code>Customer@123</code>
                </p>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container footer-bottom-content">
                <p>&copy; <?= date('Y') ?> HardwareHub. Academic Web Engineering Project (CIS324 MVC Blueprint).</p>
                <p class="footer-status"><span class="status-indicator"></span> Database: MySQL / PDO Active</p>
            </div>
        </div>
    </footer>

    <!-- Vanilla JavaScript for Real-time Stock & UI Enhancements -->
    <script src="public/js/script.js"></script>
</body>
</html>
