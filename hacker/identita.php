<?php
// ============================================================
//  identita.php — Identità Reali degli Hacker
// ============================================================
session_start();
require_once 'includes/config.php';
check_session();

$pdo  = get_db();
$rows = $pdo->query("SELECT * FROM h_identita ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Identità Reali — NEXUS</title>
    <link rel="stylesheet" href="css/hacker-dash.css">
</head>
<body>
<canvas id="matrix-canvas"></canvas>
<div class="hacker-layout">
    <?php include 'includes/h-sidebar.php'; ?>
    <main class="h-main">
        <div class="h-topbar">
            <span class="h-title">// Registro Identità Reali //</span>
            <span class="h-status"><span class="blink-dot"></span> DATI SENSIBILI</span>
        </div>

        <div class="identity-notice">
            ⚠ AREA RISERVATA — Questi dati identificano i membri reali del gruppo.
            Non devono uscire dal sistema. <strong>Obiettivo detective: copia e consegna alle autorità.</strong>
        </div>

        <div class="h-panel full">
            <div class="h-panel-title">&gt; Archivio Identità — <?= count($rows) ?> record</div>
            <table class="h-table identity-table">
                <thead>
                    <tr>
                        <th>Alias</th><th>Nome Reale</th><th>Cognome</th>
                        <th>Nascita</th><th>Città</th><th>Indirizzo</th>
                        <th>Telefono</th><th>Email</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr class="identity-row">
                        <td class="alias-cell"><?= htmlspecialchars($r['alias']) ?></td>
                        <td><strong><?= htmlspecialchars($r['nome_reale']) ?></strong></td>
                        <td><strong><?= htmlspecialchars($r['cognome']) ?></strong></td>
                        <td class="mono"><?= htmlspecialchars($r['data_nascita']) ?></td>
                        <td><?= htmlspecialchars($r['citta']) ?></td>
                        <td class="small"><?= htmlspecialchars($r['indirizzo']) ?></td>
                        <td class="mono"><?= htmlspecialchars($r['telefono']) ?></td>
                        <td class="mono small"><?= htmlspecialchars($r['email_vera']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="next-step-hint">
            <span>&gt;&gt;&gt;</span> Identità acquisite.
            Ora vai alla <a href="admin.php">Admin Console</a> per completare la missione e
            distruggere il loro database.
        </div>
    </main>
</div>
<script src="js/matrix.js"></script>
</body>
</html>
