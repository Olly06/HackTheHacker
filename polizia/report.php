<?php
// ============================================================
//  report.php — Report Incidenti
// ============================================================
session_start();
require_once 'includes/config.php';
check_session();

$pdo  = get_db();
$rows = $pdo->query("
    SELECT r.*, p.nome, p.cognome
    FROM report_incidenti r
    LEFT JOIN poliziotti p ON r.autore_id = p.id
    ORDER BY r.data_report DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Report Incidenti — Polizia di Stato</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <!--
    INDIZIO #4 — Nota del tecnico IT (commento nascosto):
    L'intruso ha usato l'account "admin_central" per mascherare le proprie tracce.
    L'azione EXFIL nel server_log è quella chiave. La nota di quella riga
    contiene l'indirizzo C2 (Command & Control) degli hacker.
    Usa il Terminale DB per trovarla:
    SELECT * FROM server_log WHERE azione = 'EXFIL';
    -->
</head>
<body>
<div class="scanlines"></div>
<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <div class="top-bar-left">
            <h1>Report Incidenti</h1>
            <span class="breadcrumb">Dashboard → Report</span>
        </div>
        <div class="top-bar-right">
            <span class="alert-banner critical-blink">⚠ <?= count($rows) ?> INCIDENTI REGISTRATI</span>
        </div>
    </header>

    <div class="reports-list">
    <?php foreach ($rows as $r):
        $cls = match($r['gravita']) {
            'critica' => 'report-card critica',
            'alta'    => 'report-card alta',
            default   => 'report-card'
        };
    ?>
        <div class="<?= $cls ?>">
            <div class="report-header">
                <span class="report-title"><?= htmlspecialchars($r['titolo']) ?></span>
                <span class="report-gravita <?= $r['gravita'] ?>"><?= strtoupper($r['gravita']) ?></span>
            </div>
            <div class="report-meta">
                <span>📅 <?= date('d/m/Y H:i', strtotime($r['data_report'])) ?></span>
                <span>✍ <?= htmlspecialchars($r['nome'] . ' ' . $r['cognome']) ?></span>
            </div>
            <div class="report-body">
                <?= nl2br(htmlspecialchars($r['contenuto'])) ?>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</main>
</body>
</html>
