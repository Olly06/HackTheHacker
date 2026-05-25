<?php
// ============================================================
//  anagrafica.php — Tabella Agenti (con dati corrotti visibili)
// ============================================================
session_start();
require_once 'includes/config.php';
check_session();

$pdo  = get_db();
$rows = $pdo->query("SELECT * FROM poliziotti ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Anagrafica Agenti — Polizia di Stato</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <!-- INDIZIO #3: AG003 e AG005 sono stati manomessi. Chi ha eseguito quelle UPDATE? -->
</head>
<body>
<div class="scanlines"></div>
<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <div class="top-bar-left">
            <h1>Anagrafica Agenti</h1>
            <span class="breadcrumb">Dashboard → Anagrafica</span>
        </div>
        <div class="top-bar-right">
            <span class="alert-banner">⚠ INTEGRITÀ DATABASE COMPROMESSA</span>
        </div>
    </header>

    <div class="panel full-width">
        <div class="panel-header">
            <span>Registro Personale — <?= count($rows) ?> record</span>
            <span class="panel-tag error-tag">⚠ ANOMALIE RILEVATE</span>
        </div>

        <div class="table-notice">
            Il sistema ha rilevato <strong>2 record corrotti</strong> (evidenziati in rosso).
            Potrebbe trattarsi di un'azione deliberata. Consultare i log del database per ulteriori dettagli.
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Matricola</th>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Grado</th>
                        <th>Reparto</th>
                        <th>Email</th>
                        <th>Stato</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r):
                    // Determina se la riga è corrotta
                    $corrotta = ($r['nome'] === null || $r['nome'] === '########');
                    $cls = $corrotta ? 'row-corrupted' : ($r['attivo'] ? '' : 'row-inactive');
                ?>
                    <tr class="<?= $cls ?>">
                        <td class="mono"><?= htmlspecialchars($r['matricola']) ?></td>
                        <td>
                            <?php if ($r['nome'] === null): ?>
                                <span class="null-value">NULL</span>
                            <?php elseif ($r['nome'] === '########'): ?>
                                <span class="corrupted-value">████████</span>
                            <?php else: ?>
                                <?= htmlspecialchars($r['nome']) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($r['cognome'] === null): ?>
                                <span class="null-value">NULL</span>
                            <?php elseif ($r['cognome'] === '########'): ?>
                                <span class="corrupted-value">████████</span>
                            <?php else: ?>
                                <?= htmlspecialchars($r['cognome']) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= $r['grado'] ? htmlspecialchars($r['grado']) : '<span class="null-value">NULL</span>' ?></td>
                        <td><?= $r['reparto'] ? htmlspecialchars($r['reparto']) : '<span class="null-value">NULL</span>' ?></td>
                        <td class="mono small"><?= $r['email'] ? htmlspecialchars($r['email']) : '<span class="null-value">NULL</span>' ?></td>
                        <td>
                            <?php if ($corrotta): ?>
                                <span class="badge-status corrupted">CORROTTO</span>
                            <?php elseif ($r['attivo']): ?>
                                <span class="badge-status active">ATTIVO</span>
                            <?php else: ?>
                                <span class="badge-status inactive">INATTIVO</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="panel-footer-hint">
            <strong>Suggerimento investigativo:</strong> Le righe AG003 e AG005 sono state modificate
            durante l'intrusione. Per sapere <em>chi</em> e <em>come</em>, accedi al
            <a href="terminale.php">Terminale DB</a> e analizza la tabella <code>server_log</code>.
        </div>
    </div>
</main>
</body>
</html>
