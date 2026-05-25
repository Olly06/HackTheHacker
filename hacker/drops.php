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
        <div class="h-panels">
        <div class="h-panel full">
            <div class="h-panel-title">&gt; File Esfiltrati — <?= count($rows) ?> elementi</div>
            <table class="h-table">
                <thead>
                    <tr><th>Nome File</th><th>Provenienza</th><th>Dimensione</th><th>Data Furto</th><th>Checksum</th></tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $i => $r): ?>
                    <tr<?= $i >= 50 ? ' class="h-extra-drop" style="display:none"' : '' ?>>
                        <td class="mono"><?= htmlspecialchars($r['nome_file']) ?></td>
                        <td><?= htmlspecialchars($r['provenienza']) ?></td>
                        <td class="mono"><?= htmlspecialchars($r['dimensione']) ?></td>
                        <td class="mono"><?= date('d/m/Y H:i', strtotime($r['data_furto'])) ?></td>
                        <td class="mono small"><?= htmlspecialchars($r['checksum']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (count($rows) > 50): ?>
            <div class="show-more-bar">
                <button class="btn-show-more" onclick="showMore(this, 'h-extra-drop')">
                    [ MOSTRA ALTRI <?= count($rows) - 50 ?> FILE ]
                </button>
            </div>
            <?php endif; ?>
        </div>

        <!-- ===== PUZZLE FILES ===== -->
        <div class="h-panel full anomaly-panel">
            <div class="h-panel-title anomaly-title">
                &gt; [ANOMALIA] Partizione Nascosta — <span class="glitch-path">/nexus/.shadow/</span>
            </div>
            <div class="anomaly-notice">
                ⚠ Tre file rilevati in una partizione non indicizzata. Estensioni non standard.
                Contenuto illeggibile con i tool normali. <em>Qualcuno non voleva che li trovaste.</em>
            </div>
            <table class="h-table anomaly-table">
                <thead>
                    <tr>
                        <th>Nome File</th>
                        <th>Dimensione</th>
                        <th>Stato</th>
                        <th>Note</th>
                        <th>Azione</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="anomaly-row">
                        <td class="mono file-name">sistema_backup<span class="ext">.dat</span></td>
                        <td class="mono">2.1 KB</td>
                        <td><span class="badge-corrupt">CORROTTO?</span></td>
                        <td class="hint-cell">Il tuo editor di testo sa leggere più di quello che pensi.</td>
                        <td>
                            <form method="post" action="download.php" style="margin:0">
                                <input type="hidden" name="f" value="backup">
                                <button type="submit" class="btn-dl">[ SCARICA ]</button>
                            </form>
                        </td>
                    </tr>
                    <tr class="anomaly-row">
                        <td class="mono file-name">msg_cifrato<span class="ext">.b64</span></td>
                        <td class="mono">0.8 KB</td>
                        <td><span class="badge-enc">CIFRATO</span></td>
                        <td class="hint-cell">Ogni blocco di caratteri nasconde due facce. Cerca lo schema.</td>
                        <td>
                            <form method="post" action="download.php" style="margin:0">
                                <input type="hidden" name="f" value="msg">
                                <button type="submit" class="btn-dl">[ SCARICA ]</button>
                            </form>
                        </td>
                    </tr>
                    <tr class="anomaly-row pulse-row">
                        <td class="mono file-name">coordinate<span class="ext">.enc</span></td>
                        <td class="mono">1.4 KB</td>
                        <td><span class="badge-enc">CIFRATO</span></td>
                        <td class="hint-cell">Tredici passi separano la verità dalla menzogna.</td>
                        <td>
                            <form method="post" action="download.php" style="margin:0">
                                <input type="hidden" name="f" value="coord">
                                <button type="submit" class="btn-dl">[ SCARICA ]</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        </div><!-- /.h-panels -->
    </main>
</div>
<script src="js/matrix.js"></script>
<script>
function showMore(btn, cls) {
    document.querySelectorAll('.' + cls).forEach(function(r) { r.style.display = ''; });
    btn.parentElement.style.display = 'none';
}
</script>
</body>
</html>
