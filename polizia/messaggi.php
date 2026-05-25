<?php
// ============================================================
//  messaggi.php — Messaggi Interni
// ============================================================
session_start();
require_once 'includes/config.php';
check_session();

$pdo = get_db();

// Segna come letti i messaggi per l'utente corrente
$pdo->prepare("UPDATE messaggi_interni SET letto=1 WHERE destinatario LIKE ?")->execute(['%' . $_SESSION['username'] . '%']);

$rows = $pdo->query("SELECT * FROM messaggi_interni ORDER BY data_invio DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Messaggi — Polizia di Stato</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<div class="scanlines"></div>
<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <div class="top-bar-left">
            <h1>Messaggi Interni</h1>
            <span class="breadcrumb">Dashboard → Messaggi</span>
        </div>
    </header>

    <div class="messages-list">
    <?php foreach ($rows as $msg): ?>
        <div class="message-card <?= $msg['letto'] ? '' : 'unread' ?>">
            <div class="msg-header">
                <span class="msg-from">Da: <strong><?= htmlspecialchars($msg['mittente']) ?></strong></span>
                <span class="msg-date"><?= date('d/m/Y H:i', strtotime($msg['data_invio'])) ?></span>
            </div>
            <div class="msg-subject">✉ <?= htmlspecialchars($msg['oggetto']) ?></div>
            <div class="msg-body"><?= nl2br(htmlspecialchars($msg['corpo'])) ?></div>
        </div>
    <?php endforeach; ?>
    </div>
</main>
</body>
</html>
