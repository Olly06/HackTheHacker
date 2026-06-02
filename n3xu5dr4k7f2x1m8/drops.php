<?php
// ============================================================
//  drops.php — Archivio File Rubati NEXUS
// ============================================================
session_start();
require_once './includes/config.php';
check_session();

$pdo     = get_db();
$allowed = [50, 100, 200, 500];
$raw     = (int)($_GET['limit'] ?? 50);
$limit   = in_array($raw, $allowed) ? $raw : 50;
$total   = (int)$pdo->query("SELECT COUNT(*) FROM h_drops")->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM h_drops ORDER BY data_furto DESC LIMIT ?");
$stmt->execute([$limit]);
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>File Rubati — NEXUS</title>
    <link rel="stylesheet" href="./css/hacker-dash.css">
</head>
<body>
<canvas id="matrix-canvas"></canvas>
<div class="hacker-layout">
    <?php include './includes/h-sidebar.php'; ?>
    <main class="h-main">
        <div class="h-topbar">
            <span class="h-title">// File Rubati //</span>
            <span class="h-status"><span class="blink-dot"></span> <?= count($rows) ?> FILE IN ARCHIVIO</span>
        </div>

        <div class="h-panel full">
            <div class="h-panel-title" style="display:flex;align-items:center;justify-content:space-between">
                <span>&gt; Drop Archive — <?= count($rows) ?> / <?= $total ?> file</span>
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
                        <th>Nome File</th>
                        <th>Provenienza</th>
                        <th>Dimensione</th>
                        <th>Data Furto</th>
                        <th>Checksum</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td class="mono"><?= htmlspecialchars($r['nome_file']) ?></td>
                        <td><?= htmlspecialchars($r['provenienza']) ?></td>
                        <td class="mono"><?= htmlspecialchars($r['dimensione']) ?></td>
                        <td class="mono"><?= htmlspecialchars($r['data_furto']) ?></td>
                        <td class="mono" style="color:var(--muted);font-size:11px"><?= htmlspecialchars($r['checksum']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="h-panel full" style="margin-top:24px;border-color:rgba(255,60,60,.3)">
            <div class="h-panel-title" style="color:var(--red)">
                &gt; /nexus/.shadow/ — Partizione Riservata
                <span style="font-size:10px;font-weight:normal;color:var(--muted);margin-left:12px">
                    [NOTA PHANTOM_Z — 14/11/2024 02:58]
                </span>
            </div>
            <div style="padding:12px 16px;font-size:12px;color:var(--muted);border-bottom:1px solid var(--border);line-height:1.6">
                Tre file archiviati prima dell'operazione principale.
                <span style="color:var(--red)">Accesso ristretto.</span>
            </div>
            <table class="h-table">
                <thead>
                    <tr>
                        <th>Nome File</th>
                        <th>Tipo</th>
                        <th>Note</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="mono">sistema_backup.dat</td>
                        <td class="mono" style="color:var(--muted)">?</td>
                        <td style="color:var(--muted);font-size:11px">Archiviato da PHANTOM_Z prima dell'op. L'estensione è cambiabile</td>
                        <td>
                            <form method="POST" action="download.php" style="margin:0">
                                <input type="hidden" name="f" value="backup">
                                <button type="submit" class="dl-btn">&#x2193; SCARICA</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td class="mono">msg_cifrato.b64</td>
                        <td class="mono" style="color:var(--muted)">?</td>
                        <td style="color:var(--muted);font-size:11px">Preparato da CIPHER_Y.</td>
                        <td>
                            <form method="POST" action="download.php" style="margin:0">
                                <input type="hidden" name="f" value="msg">
                                <button type="submit" class="dl-btn">&#x2193; SCARICA</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td class="mono">coordinate.enc</td>
                        <td class="mono" style="color:var(--muted)">?</td>
                        <td style="color:var(--muted);font-size:11px">PrepaRato da C1PH3R_Y.</td>
                        <td>
                            <form method="POST" action="download.php" style="margin:0">
                                <input type="hidden" name="f" value="coord">
                                <button type="submit" class="dl-btn">&#x2193; SCARICA</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</div>
<style>
.dl-btn {
    background: transparent;
    border: 1px solid rgba(255,60,60,.5);
    color: var(--red);
    font-family: var(--mono);
    font-size: 11px;
    padding: 5px 12px;
    border-radius: 2px;
    cursor: pointer;
    white-space: nowrap;
    transition: all .15s;
}
.dl-btn:hover {
    background: rgba(255,60,60,.1);
    border-color: var(--red);
}
</style>
<script src="./js/matrix.js"></script>
</body>
</html>
