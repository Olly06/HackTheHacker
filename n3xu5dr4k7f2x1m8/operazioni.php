<?php
// ============================================================
//  operazioni.php — Log Operazioni Criminali NEXUS
// ============================================================
session_start();
require_once './includes/config.php';
check_session();

$pdo    = get_db();
$allowed = [50, 100, 200, 500];
$raw     = (int)($_GET['limit'] ?? 50);
$limit   = in_array($raw, $allowed) ? $raw : 50;
$total   = (int)$pdo->query("SELECT COUNT(*) FROM h_operazioni")->fetchColumn();

$stmt = $pdo->prepare("
    SELECT o.*, u.username
    FROM h_operazioni o
    LEFT JOIN h_utenti u ON o.responsabile = u.id
    ORDER BY o.data_op DESC
    LIMIT ?
");
$stmt->execute([$limit]);
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Operazioni — NEXUS</title>
    <link rel="stylesheet" href="./css/hacker-dash.css">
</head>
<body>
<canvas id="matrix-canvas"></canvas>
<div class="hacker-layout">
    <?php include './includes/h-sidebar.php'; ?>
    <main class="h-main">
        <div class="h-topbar">
            <span class="h-title">// Operazioni //</span>
            <span class="h-status"><span class="blink-dot"></span> <?= count($rows) ?> OPERAZIONI</span>
        </div>

        <div class="h-panel full">
            <div class="h-panel-title" style="display:flex;align-items:center;justify-content:space-between">
                <span>&gt; Log Operazioni — <?= count($rows) ?> / <?= $total ?> record</span>
                <span style="font-size:11px;font-weight:normal;display:flex;gap:6px;align-items:center">
                    Mostra:
                    <?php foreach ([50,100,200,500] as $l): ?>
                    <a href="?limit=<?= $l ?>"
                       style="padding:3px 8px;border-radius:2px;text-decoration:none;font-family:var(--mono);
                              background:<?= $l===$limit?'var(--gold)':'var(--navy3)' ?>;
                              color:<?= $l===$limit?'var(--navy)':'var(--muted)' ?>;
                              border:1px solid <?= $l===$limit?'var(--gold)':'var(--border)' ?>">
                        <?= $l ?>
                    </a>
                    <?php endforeach; ?>
                </span>
            </div>
            <table class="h-table">
                <thead>
                    <tr>
                        <th>Codice</th>
                        <th>Descrizione</th>
                        <th>Data</th>
                        <th>Stato</th>
                        <th>Responsabile</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td class="mono"><?= htmlspecialchars($r['codice'] ?? $r['nome_operazione'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($r['descrizione'] ?? $r['note'] ?? '—') ?></td>
                        <td class="mono"><?= htmlspecialchars($r['data_op'] ?? ($r['data_operazione'] ? substr($r['data_operazione'], 0, 10) : '—')) ?></td>
                        <td><span class="op-stato <?= htmlspecialchars($r['stato']) ?>"><?= strtoupper(htmlspecialchars($r['stato'])) ?></span></td>
                        <td><?= htmlspecialchars($r['username'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<script src="./js/matrix.js"></script>
</body>
</html>
