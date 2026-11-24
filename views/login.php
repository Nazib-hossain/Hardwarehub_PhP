<?php
/**
 * Login & Registration View
 * HardwareHub E-Commerce
 * 
 * Provides:
 * - Login form with role awareness
 * - Registration form with password confirmation
 * - Academic Demo Credentials Quick-Fill Cards
 */
$pageTitle = 'Sign In / Register';
require_once __DIR__ . '/layout/header.php';

$activeTab = $activeTab ?? ($_GET['tab'] ?? 'login');
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <!-- Auth Navigation Tabs -->
        <div class="auth-tabs">
            <button type="button" 
                    class="auth-tab-btn <?= $activeTab === 'login' ? 'active' : '' ?>" 
                    onclick="switchAuthTab('login')">
                Sign In
            </button>
            <button type="button" 
                    class="auth-tab-btn <?= $activeTab === 'register' ? 'active' : '' ?>" 
                    onclick="switchAuthTab('register')">
                Create Account
            </button>
        </div>

        <!-- Tab 1: Login Form -->
        <div id="loginTabContent" class="tab-pane <?= $activeTab === 'login' ? 'active' : '' ?>">
            <div class="auth-header">
                <h2>Welcome to HardwareHub</h2>
                <p>Sign in to your customer account or administrator portal.</p>
            </div>

            <form action="index.php?action=auth_login" method="POST" class="auth-form" id="loginForm">
                <?php if (!empty($_GET['redirect'])): ?>
                    <input type="hidden" name="redirect" value="<?= e($_GET['redirect']) ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="loginEmail">Email Address</label>
                    <input type="email" id="loginEmail" name="email" required placeholder="you@example.com" autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" name="password" required placeholder="••••••••" autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    Sign In
                </button>
            </form>

            <!-- Quick-Fill Academic Test Accounts -->
            <div class="demo-accounts-box">
                <h4>🧪 Academic Demo Accounts (Click to Fill):</h4>
                <div class="demo-buttons-grid">
                    <button type="button" class="btn-demo-fill" onclick="fillLogin('admin@hardwarehub.com', 'Admin@123')">
                        <strong>🛡️ Admin Account</strong>
                        <code>admin@hardwarehub.com</code>
                    </button>
                    <button type="button" class="btn-demo-fill" onclick="fillLogin('customer@hardwarehub.com', 'Customer@123')">
                        <strong>👤 Customer Account</strong>
                        <code>customer@hardwarehub.com</code>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab 2: Register Form -->
        <div id="registerTabContent" class="tab-pane <?= $activeTab === 'register' ? 'active' : '' ?>">
            <div class="auth-header">
                <h2>Join HardwareHub</h2>
                <p>Create an account to track orders and save your cart.</p>
            </div>

            <form action="index.php?action=auth_register" method="POST" class="auth-form" id="registerForm">
                <div class="form-group">
                    <label for="regName">Full Name *</label>
                    <input type="text" id="regName" name="name" required placeholder="Alex Carter">
                </div>

                <div class="form-group">
                    <label for="regEmail">Email Address *</label>
                    <input type="email" id="regEmail" name="email" required placeholder="alex@example.com">
                </div>

                <div class="form-group">
                    <label for="regPassword">Password * (minimum 6 characters)</label>
                    <input type="password" id="regPassword" name="password" minlength="6" required placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label for="regConfirmPassword">Confirm Password *</label>
                    <input type="password" id="regConfirmPassword" name="confirm_password" minlength="6" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    Register Customer Account
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function switchAuthTab(tabName) {
    document.querySelectorAll('.auth-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    
    if (tabName === 'login') {
        document.querySelectorAll('.auth-tab-btn')[0].classList.add('active');
        document.getElementById('loginTabContent').classList.add('active');
    } else {
        document.querySelectorAll('.auth-tab-btn')[1].classList.add('active');
        document.getElementById('registerTabContent').classList.add('active');
    }
}

function fillLogin(email, pass) {
    document.getElementById('loginEmail').value = email;
    document.getElementById('loginPassword').value = pass;
}
</script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
