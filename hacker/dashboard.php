<?php
// ============================================================
//  dashboard.php — Dashboard Hacker (Fase 3)
// ============================================================
session_start();
require_once 'includes/config.php';
check_session();

$pdo      = get_db();
$alias    = $_SESSION['hacker_user'];
$livello  = $_SESSION['hacker_level'];

$n_ops    = $pdo->query("SELECT COUNT(*) FROM h_operazioni")->fetchColumn();
$n_drops  = $pdo->query("SELECT COUNT(*) FROM h_drops")->fetchColumn();
$identita = $pdo->query("SELECT * FROM h_identita ORDER BY id")->fetchAll();
$ops      = $pdo->query("SELECT o.*, u.username FROM h_operazioni o LEFT JOIN h_utenti u ON o.responsabile=u.id ORDER BY o.data_op DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>NEXUS — Command Center</title>
    <link rel="stylesheet" href="css/hacker-dash.css">
</head>
<body>
<canvas id="matrix-canvas"></canvas>
<div class="hacker-layout">

    <!-- SIDEBAR -->
    <nav class="h-sidebar">
        <div class="h-logo">
            <span class="logo-bracket">[</span>NEXUS<span class="logo-bracket">]</span>
        </div>
        <div class="h-user">
            <span class="h-alias">> <?= htmlspecialchars($alias) ?></span>
            <span class="h-level">LVL <?= $livello === 2 ? 'ADMIN' : 'MEMBER' ?></span>
        </div>
        <ul class="h-nav">
            <li><a href="dashboard.php" class="active"><span>//</span> Dashboard</a></li>
            <li><a href="identita.php"><span>//</span> Identità Reali</a></li>
            <li><a href="operazioni.php"><span>//</span> Operazioni</a></li>
            <li><a href="drops.php"><span>//</span> File Rubati</a></li>
            <?php if ($livello == 2): ?>
            <li class="admin-only"><a href="admin.php"><span>//</span> Admin Console</a></li>
            <?php endif; ?>
        </ul>
        <a href="logout.php" class="h-logout">[ DISCONNETTI ]</a>
    </nav>

    <!-- MAIN -->
    <main class="h-main">
        <div class="h-topbar">
            <span class="h-title">// NEXUS Command Center //</span>
            <span class="h-status"><span class="blink-dot"></span> ONLINE — CONNESSIONE SICURA</span>
        </div>

        <!-- WELCOME -->
        <div class="welcome-banner">
            <span class="wb-prefix">&gt;&gt;&gt;</span>
            Bentornato, <strong><?= htmlspecialchars($alias) ?></strong>.
            Il sistema è operativo. <?= $n_ops ?> operazioni registrate. <?= $n_drops ?> file in archivio.
        </div>

        <!-- STAT CARDS -->
        <div class="h-stats">
            <div class="h-stat-card">
                <div class="h-stat-val"><?= $n_ops ?></div>
                <div class="h-stat-lbl">Operazioni</div>
            </div>
            <div class="h-stat-card">
                <div class="h-stat-val"><?= $n_drops ?></div>
                <div class="h-stat-lbl">File Rubati</div>
            </div>
            <div class="h-stat-card">
                <div class="h-stat-val"><?= count($identita) ?></div>
                <div class="h-stat-lbl">Identità Registrate</div>
            </div>
            <div class="h-stat-card alert-card">
                <div class="h-stat-val">⚠</div>
                <div class="h-stat-lbl">Intruso Rilevato</div>
            </div>
        </div>

        <!-- PANELS -->
        <div class="h-panels">
            <div class="h-panel">
                <div class="h-panel-title">&gt; Operazioni Recenti</div>
                <table class="h-table">
                    <thead>
                        <tr><th>Codice</th><th>Descrizione</th><th>Stato</th><th>Responsabile</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ops as $op): ?>
                        <tr>
                            <td class="mono"><?= htmlspecialchars($op['codice']) ?></td>
                            <td><?= htmlspecialchars($op['descrizione']) ?></td>
                            <td><span class="op-stato <?= $op['stato'] ?>"><?= strtoupper($op['stato']) ?></span></td>
                            <td><?= htmlspecialchars($op['username'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="h-panel">
                <div class="h-panel-title">&gt; Obiettivo Detective</div>
                <div class="mission-box">
                    <p>Sei riuscito ad infiltrarti nel sistema NEXUS.</p>
                    <ol class="h-mission-list">
                        <li class="done">Trovato l'URL del server hacker</li>
                        <li class="done">Accesso eseguito con successo</li>
                        <li>Scopri le <a href="identita.php">identità reali</a> degli hacker</li>
                        <li class="locked">Accedi alla <a href="admin.php">Admin Console</a> e distruggi il database</li>
                    </ol>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="js/matrix.js"></script>
</body>
</html>
