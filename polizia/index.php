<?php
// ============================================================
//  index.php — Login Portale Polizia
// ============================================================
session_start();
require_once 'includes/config.php';

// Se già loggato, vai alla dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Inserire username e password.';
    } else {
        try {
            $pdo  = get_db();
            $stmt = $pdo->prepare("SELECT * FROM utenti WHERE username = ? AND attivo = 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && $user['password'] === md5($password)) {
                session_regenerate_id(true);
                $_SESSION['user_id']       = $user['id'];
                $_SESSION['username']      = $user['username'];
                $_SESSION['ruolo']         = $user['ruolo'];
                $_SESSION['last_activity'] = time();
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Credenziali non valide. Accesso negato.';
                // Log tentativo fallito (didattico)
                sleep(1);
            }
        } catch (PDOException $e) {
            $error = 'Errore di sistema. Contattare il supporto IT.';
        }
    }
}

$error_msg = '';
$err_param = $_GET['error'] ?? '';
if ($err_param === 'not_logged_in')   $error_msg = 'Devi effettuare il login per accedere.';
if ($err_param === 'session_expired') $error_msg = 'Sessione scaduta. Effettua nuovamente il login.';
if ($error) $error_msg = $error;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Portale Sicuro — Polizia di Stato</title>
    <link rel="stylesheet" href="css/login.css">
    <!-- INDIZIO #1: Guarda attentamente il codice sorgente di questa pagina -->
    <!-- HINT_01: Le credenziali iniziali sono nel briefing che ti è stato consegnato -->
    <!-- detective_rossi : Falcon2077! -->
</head>
<body>
    <div class="scanlines"></div>
    <div class="login-wrapper">
        <div class="badge-top">
            <svg viewBox="0 0 60 60" class="badge-icon">
                <polygon points="30,4 54,18 54,42 30,56 6,42 6,18" fill="none" stroke="#c8a84b" stroke-width="2"/>
                <polygon points="30,12 46,21 46,39 30,48 14,39 14,21" fill="none" stroke="#c8a84b" stroke-width="1" opacity=".5"/>
                <text x="30" y="34" text-anchor="middle" fill="#c8a84b" font-size="10" font-weight="bold">PS</text>
            </svg>
        </div>

        <div class="login-box">
            <div class="header-strip">
                <span class="classified-tag">RISERVATO</span>
                <span class="system-id">SYS-ID: PSN-4471</span>
            </div>

            <h1>POLIZIA DI STATO</h1>
            <p class="subtitle">Portale Accesso Sicuro — Personale Autorizzato</p>

            <?php if ($error_msg): ?>
                <div class="alert-error">
                    <span class="alert-icon">⚠</span> <?= htmlspecialchars($error_msg) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php" autocomplete="off">
                <div class="field-group">
                    <label for="username">Matricola / Username</label>
                    <div class="input-wrap">
                        <span class="input-icon">◈</span>
                        <input type="text" id="username" name="username"
                               placeholder="es. detective_rossi"
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                               required autofocus>
                    </div>
                </div>

                <div class="field-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">◈</span>
                        <input type="password" id="password" name="password"
                               placeholder="••••••••••••"
                               required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <span>ACCEDI AL SISTEMA</span>
                    <span class="btn-arrow">→</span>
                </button>
            </form>

            <div class="footer-note">
                Accesso monitorato. Tutti i tentativi vengono registrati.<br>
                Art. 615-ter c.p. — Accesso abusivo a sistema informatico.
            </div>
        </div>

        <div class="bottom-bar">
            <span>MINISTERO DELL'INTERNO</span>
            <span class="status-dot"></span>
            <span>SISTEMA OPERATIVO</span>
        </div>
    </div>
</body>
</html>
