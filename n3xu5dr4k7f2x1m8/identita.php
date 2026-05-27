<?php
// ============================================================
//  identita.php — Identità Reali degli Hacker
// ============================================================
session_start();
require_once '../hacker/includes/config.php';
check_session();

$pdo  = get_db();
$rows = $pdo->query("SELECT * FROM h_identita ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Alias Membri — NEXUS</title>
    <link rel="stylesheet" href="../hacker/css/hacker-dash.css">
</head>
<body>
<canvas id="matrix-canvas"></canvas>
<div class="hacker-layout">
    <?php include '../hacker/includes/h-sidebar.php'; ?>
    <main class="h-main">
        <div class="h-topbar">
            <span class="h-title">// Alias Membri //</span>
            <span class="h-status"><span class="blink-dot"></span> ONLINE</span>
        </div>

        <div class="h-panel full">
            <div class="h-panel-title">&gt; Alias Membri — <?= count($rows) ?> record</div>
            <table class="h-table identity-table">
                <thead>
                    <tr>
                        <th>#</th><th>Alias</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $i => $r): ?>
                    <tr class="identity-row">
                        <td class="mono"><?= $i + 1 ?></td>
                        <td class="alias-cell"><?= htmlspecialchars($r['alias']) ?></td>
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
<script src="../hacker/js/matrix.js"></script>
</body>
</html>
