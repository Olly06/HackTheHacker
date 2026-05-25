<?php
// ============================================================
//  drops.php — File Rubati
// ============================================================
session_start();
require_once 'includes/config.php';
check_session();

$pdo  = get_db();
$rows = $pdo->query("SELECT * FROM h_drops ORDER BY data_furto DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>File Rubati — NEXUS</title>
    <link rel="stylesheet" href="css/hacker-dash.css">
</head>
<body>
<canvas id="matrix-canvas"></canvas>
<div class="hacker-layout">
    <?php include 'includes/h-sidebar.php'; ?>
    <main class="h-main">
        <div class="h-topbar">
            <span class="h-title">// Archivio Drop //</span>
        </div>
        <div class="h-panel full">
            <div class="h-panel-title">&gt; File Esfiltrati — <?= count($rows) ?> elementi</div>
            <table class="h-table">
                <thead>
                    <tr><th>Nome File</th><th>Provenienza</th><th>Dimensione</th><th>Data Furto</th><th>Checksum</th></tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td class="mono"><?= htmlspecialchars($r['nome_file']) ?></td>
                        <td><?= htmlspecialchars($r['provenienza']) ?></td>
                        <td class="mono"><?= htmlspecialchars($r['dimensione']) ?></td>
                        <td class="mono"><?= date('d/m/Y H:i', strtotime($r['data_furto'])) ?></td>
                        <td class="mono small"><?= htmlspecialchars($r['checksum']) ?></td>
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
