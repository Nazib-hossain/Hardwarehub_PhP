<?php
/**
 * HardwareHub Database Configuration (PDO)
 * CIS324 Academic Guideline: Secure Database Access Layer
 * 
 * Uses PHP Data Objects (PDO) with:
 * - Parameterized Prepared Statements (100% SQL injection prevention)
 * - Emulated prepares turned OFF (native prepared statements)
 * - Strict Exception Error Mode
 * - Associative array fetch mode
 */

class Database {
    private static string $host = '127.0.0.1';
    private static string $db   = 'hardwarehub_db';
    private static string $user = 'root';
    private static string $pass = '';
    private static string $port = '3306';
    private static string $charset = 'utf8mb4';

    private static ?PDO $instance = null;

    /**
     * Get or initialize the singleton PDO connection
     *
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                self::$host,
                self::$port,
                self::$db,
                self::$charset
            );

            $options = [
                // Throw exceptions on SQL errors for reliable transaction handling
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Default fetch mode as associative array for clean model mapping
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Disable emulation of prepared statements to guarantee real SQLi prevention
                PDO::ATTR_EMULATE_PREPARES   => false,
                // Ensure persistent or quick connection timeout
                PDO::ATTR_TIMEOUT            => 5,
            ];

            try {
                self::$instance = new PDO($dsn, self::$user, self::$pass, $options);
            } catch (PDOException $e) {
                // Return a friendly academic diagnostic error instead of leaking credentials
                error_log("Database Connection Error: " . $e->getMessage());
                die(self::renderConnectionError($e->getMessage()));
            }
        }

        return self::$instance;
    }

    /**
     * Render a clean, helpful error screen if XAMPP MySQL is not started
     * or the database has not been imported yet.
     */
    private static function renderConnectionError(string $error): string {
        $cleanError = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Notice - HardwareHub</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 2rem; display: flex; justify-content: center; align-items: center; min-height: 80vh; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 2.5rem; max-width: 650px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h1 { color: #38bdf8; margin-top: 0; font-size: 1.75rem; }
        .badge { background: #ef4444; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
        pre { background: #0b1120; padding: 1rem; border-radius: 8px; color: #f43f5e; overflow-x: auto; font-size: 0.9rem; }
        ol { padding-left: 1.25rem; line-height: 1.7; color: #cbd5e1; }
        code { background: #334155; padding: 2px 6px; border-radius: 4px; color: #38bdf8; }
        .footer { margin-top: 1.5rem; font-size: 0.85rem; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">DATABASE CONNECTION REQUIRED</span>
        <h1>HardwareHub Database Setup</h1>
        <p>The application could not connect to MySQL using the standard XAMPP configuration.</p>
        <pre>{$cleanError}</pre>
        <h3>Quick Setup in XAMPP:</h3>
        <ol>
            <li>Open the <strong>XAMPP Control Panel</strong> and click <strong>Start</strong> next to <strong>MySQL</strong> and <strong>Apache</strong>.</li>
            <li>Open <strong>phpMyAdmin</strong> (<a href="http://localhost/phpmyadmin" target="_blank" style="color:#38bdf8;">http://localhost/phpmyadmin</a>).</li>
            <li>Click <strong>Import</strong> and select the <code>database.sql</code> file included in this repository.</li>
            <li>Refresh this page.</li>
        </ol>
        <p class="footer">XAMPP default settings: Host: <code>127.0.0.1</code> | User: <code>root</code> | Password: <em>(blank)</em> | Database: <code>hardwarehub_db</code></p>
    </div>
</body>
</html>
HTML;
    }
}
