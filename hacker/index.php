<?php
// ============================================================
//  index.php — Login Sito Hacker (Fase 3)
//  Login normale con credenziali valide — no SQL injection
// ============================================================
session_start();
require_once 'includes/config.php';

if (isset($_SESSION['hacker_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'INPUT NON VALIDO';
    } else {
        try {
            $pdo  = get_db();
            $stmt = $pdo->prepare("SELECT * FROM h_utenti WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && $user['password'] === md5($password)) {
                session_regenerate_id(true);
                $_SESSION['hacker_id']     = $user['id'];
                $_SESSION['hacker_user']   = $user['username'];
                $_SESSION['hacker_level']  = $user['livello'];
                $_SESSION['last_activity'] = time();
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'AUTENTICAZIONE FALLITA — ACCESSO NEGATO';
            }
        } catch (PDOException $e) {
            $error = 'ERRORE DI SISTEMA';
        }
    }
}

// Il giocatore trova queste credenziali nella tabella h_utenti
// dopo aver scoperto il server. Le credenziali sono nell'hint del terminale:
// username: ph4ntom  password: gh0stInTh3M4ch1n3
$err_param = $_GET['error'] ?? '';
if ($err_param === 'access_denied') $error = 'ACCESSO NON AUTORIZZATO';
if ($err_param === 'timeout')       $error = 'SESSIONE SCADUTA — RICONNETTITI';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>NEXUS — Accesso Riservato</title>
    <link rel="stylesheet" href="css/hacker-login.css">
</head>
<body>
<canvas id="matrix-canvas"></canvas>

<div class="login-container">
    <div class="glitch-logo" data-text="NEXUS">NEXUS</div>
    <div class="login-tagline">// Sistema di Comando — Accesso Ristretto //</div>

    <div class="login-box">
        <div class="box-corner tl"></div>
        <div class="box-corner tr"></div>
        <div class="box-corner bl"></div>
        <div class="box-corner br"></div>

        <?php if ($error): ?>
        <div class="error-msg">
            <span class="err-icon">✖</span> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- INDIZIO NASCOSTO per il giocatore: credenziali nella struttura HTML -->
        <!-- ACCESS_CREDENTIAL_HINT: u=ph4ntom | p=gh0stInTh3M4ch1n3 -->

        <form method="POST" autocomplete="off">
            <div class="field">
                <label>IDENTIFICATIVO</label>
                <div class="input-row">
                    <span class="prompt">&gt;_</span>
                    <input type="text" name="username" placeholder="alias..." autofocus
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
            </div>
            <div class="field">
                <label>CHIAVE DI ACCESSO</label>
                <div class="input-row">
                    <span class="prompt">&gt;_</span>
                    <input type="password" name="password" placeholder="••••••••••••••••">
                </div>
            </div>
            <button type="submit" class="btn-enter">
                <span class="btn-text">CONNETTI AL SERVER</span>
                <span class="btn-loader" id="loader" style="display:none">AUTENTICAZIONE...</span>
            </button>
        </form>

        <div class="login-footer">
            <span class="blink">■</span> NEXUS v3.7.1 &nbsp;|&nbsp; CONNESSIONE CIFRATA &nbsp;|&nbsp; <span class="blink">■</span>
        </div>
    </div>
</div>

<script src="js/matrix.js"></script>
<script>
document.querySelector('form').addEventListener('submit', function() {
    document.querySelector('.btn-text').style.display = 'none';
    document.getElementById('loader').style.display   = 'inline';
});
</script>
</body>
</html>
