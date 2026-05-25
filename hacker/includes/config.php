<?php
// ============================================================
//  config.php — Configurazione Database HACKER
// ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hacker_db');
define('SITE_NAME', 'NEXUS — Command Center');
define('SESSION_TIMEOUT', 1800);

function get_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}

function check_session(): void {
    if (!isset($_SESSION['hacker_id'])) {
        header('Location: index.php?error=access_denied');
        exit;
    }
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        session_destroy();
        header('Location: index.php?error=timeout');
        exit;
    }
    $_SESSION['last_activity'] = time();
}
