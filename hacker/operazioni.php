<?php
// ============================================================
//  operazioni.php — Operazioni Hacker
// ============================================================
session_start();
require_once 'includes/config.php';
check_session();

$pdo  = get_db();
$rows = $pdo->query("
    SELECT o.*, u.username
    FROM h_operazioni o
    LEFT JOIN h_utenti u ON o.responsabile = u.id
    ORDER BY o.data_op DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Operazioni — NEXUS</title>
    <link rel="stylesheet" href="css/hacker-dash.css">
</head>
<body>
<canvas id="matrix-canvas"></canvas>
<div class="hacker-layout">
    <?php include 'includes/h-sidebar.php'; ?>
    <main class="h-main">
        <div class="h-topbar">
            <span class="h-title">// Registro Operazioni //</span>
        </div>
        <div class="h-panel full">
            <div class="h-panel-title">&gt; Archivio Operazioni — <?= count($rows) ?> record</div>
            <table class="h-table">
                <thead>
                    <tr><th>Codice</th><th>Descrizione</th><th>Data</th><th>Stato</th><th>Responsabile</th></tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td class="mono"><?= htmlspecialchars($r['codice']) ?></td>
                        <td><?= htmlspecialchars($r['descrizione']) ?></td>
                        <td class="mono"><?= htmlspecialchars($r['data_op']) ?></td>
                        <td><span class="op-stato <?= $r['stato'] ?>"><?= strtoupper($r['stato']) ?></span></td>
                        <td><?= htmlspecialchars($r['username'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<script src="js/matrix.js"></script>
</body>
</html>
