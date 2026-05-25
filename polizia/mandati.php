<?php
// ============================================================
//  mandati.php — Elenco Mandati
// ============================================================
session_start();
require_once 'includes/config.php';
check_session();

$pdo  = get_db();
$rows = $pdo->query("
    SELECT m.*, p.nome, p.cognome, p.matricola
    FROM mandati m
    LEFT JOIN poliziotti p ON m.responsabile = p.id
    ORDER BY m.data_apertura DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Mandati — Polizia di Stato</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<div class="scanlines"></div>
<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <div class="top-bar-left">
            <h1>Registro Mandati</h1>
            <span class="breadcrumb">Dashboard → Mandati</span>
        </div>
    </header>

    <div class="panel full-width">
        <div class="panel-header">
            <span>Mandati Attivi e Archiviati</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Codice</th>
                        <th>Descrizione</th>
                        <th>Stato</th>
                        <th>Data Apertura</th>
                        <th>Responsabile</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr class="<?= $r['stato'] === 'aperto' ? 'row-open' : '' ?>">
                        <td class="mono"><?= htmlspecialchars($r['codice']) ?></td>
                        <td><?= htmlspecialchars($r['descrizione']) ?></td>
                        <td>
                            <?php
                            $cls = match($r['stato']) {
                                'aperto'  => 'badge-status active',
                                'chiuso'  => 'badge-status inactive',
                                default   => 'badge-status corrupted'
                            };
                            ?>
                            <span class="<?= $cls ?>"><?= strtoupper($r['stato']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($r['data_apertura']) ?></td>
                        <td>
                            <?php if ($r['matricola']): ?>
                                <span class="mono"><?= htmlspecialchars($r['matricola']) ?></span>
                                — <?= htmlspecialchars($r['nome'] . ' ' . $r['cognome']) ?>
                            <?php else: ?>
                                <span class="null-value">Non assegnato</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
