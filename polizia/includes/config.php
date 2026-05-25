<?php
// ============================================================
//  config.php — Configurazione Database POLIZIA
// ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Cambia con le tue credenziali
define('DB_PASS', '');            // Cambia con la tua password
define('DB_NAME', 'polizia_db');
define('SITE_NAME', 'Portale Sicuro — Polizia di Stato');
define('SESSION_TIMEOUT', 1800); // 30 minuti

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
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?error=not_logged_in');
        exit;
    }
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        session_destroy();
        header('Location: index.php?error=session_expired');
        exit;
    }
    $_SESSION['last_activity'] = time();
}
