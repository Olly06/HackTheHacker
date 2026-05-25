<?php
// ============================================================
//  dashboard.php — Dashboard Portale Polizia
// ============================================================
session_start();
require_once 'includes/config.php';
check_session();

$pdo      = get_db();
$username = $_SESSION['username'];
$ruolo    = $_SESSION['ruolo'];

// Statistiche rapide
$n_poliziotti = $pdo->query("SELECT COUNT(*) FROM poliziotti")->fetchColumn();
$n_mandati    = $pdo->query("SELECT COUNT(*) FROM mandati WHERE stato='aperto'")->fetchColumn();
$n_report     = $pdo->query("SELECT COUNT(*) FROM report_incidenti WHERE gravita='critica'")->fetchColumn();
$n_messaggi   = $pdo->query("SELECT COUNT(*) FROM messaggi_interni WHERE letto=0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Polizia di Stato</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <!--
    ========================================================
    INDIZIO #2 — LOG DI SISTEMA (solo per occhi attenti)
    [2024-06-01 03:17:38] AUTH_OK     IP: 185.220.101.47  USER: admin_central
    [2024-06-01 03:19:05] UPDATE      IP: 185.220.101.47  TBL: poliziotti
    [2024-06-01 03:20:55] EXFIL       IP: 185.220.101.47  DEST: darknet-nexus
    Traccia completa disponibile nella tabella `server_log` del database.
    ========================================================
    -->
</head>
<body>
<div class="scanlines"></div>

<!-- SIDEBAR -->
<nav class="sidebar">
    <div class="sidebar-logo">
        <svg viewBox="0 0 50 50" width="40">
            <polygon points="25,3 45,15 45,35 25,47 5,35 5,15" fill="none" stroke="#c8a84b" stroke-width="2"/>
            <text x="25" y="30" text-anchor="middle" fill="#c8a84b" font-size="9" font-weight="bold">PS</text>
        </svg>
        <span>P.d.S.</span>
    </div>
    <ul class="nav-links">
        <li><a href="dashboard.php" class="active"><span class="nav-icon">⌂</span>Dashboard</a></li>
        <li><a href="anagrafica.php"><span class="nav-icon">◉</span>Anagrafica Agenti</a></li>
        <li><a href="mandati.php"><span class="nav-icon">⊞</span>Mandati</a></li>
        <li><a href="report.php"><span class="nav-icon">⚠</span>Report Incidenti</a></li>
        <li><a href="messaggi.php"><span class="nav-icon">✉</span>Messaggi
            <?php if ($n_messaggi > 0): ?>
                <span class="badge"><?= $n_messaggi ?></span>
            <?php endif; ?>
        </a></li>
        <li><a href="terminale.php"><span class="nav-icon">█</span>Terminale DB</a></li>
    </ul>
    <div class="sidebar-footer">
        <span class="user-info">◈ <?= htmlspecialchars($username) ?></span>
        <a href="logout.php" class="logout-btn">Disconnetti</a>
    </div>
</nav>

<!-- MAIN -->
<main class="main-content">
    <header class="top-bar">
        <div class="top-bar-left">
            <h1>Centro di Comando</h1>
            <span class="breadcrumb">Dashboard</span>
        </div>
        <div class="top-bar-right">
            <span class="alert-banner">⚠ ALLERTA SICUREZZA ATTIVA</span>
            <span class="datetime" id="clock"></span>
        </div>
    </header>

    <!-- ALERT CRITICO -->
    <div class="critical-alert">
        <div class="alert-pulse"></div>
        <div class="alert-content">
            <strong>VIOLAZIONE RILEVATA — 01/06/2024 ore 03:17</strong><br>
            Accesso non autorizzato ai server centrali. Alcuni record del database risultano compromessi.
            Il Detective <strong><?= htmlspecialchars($username) ?></strong> è stato incaricato delle indagini.
            <span class="alert-hint">Esplora tutte le sezioni — gli indizi sono ovunque.</span>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">◉</div>
            <div class="stat-body">
                <span class="stat-value"><?= $n_poliziotti ?></span>
                <span class="stat-label">Agenti in Forza</span>
            </div>
            <div class="stat-warning">⚠ Dati parzialmente corrotti</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⊞</div>
            <div class="stat-body">
                <span class="stat-value"><?= $n_mandati ?></span>
                <span class="stat-label">Mandati Aperti</span>
            </div>
        </div>
        <div class="stat-card critical">
            <div class="stat-icon">⚠</div>
            <div class="stat-body">
                <span class="stat-value"><?= $n_report ?></span>
                <span class="stat-label">Incidenti Critici</span>
            </div>
            <div class="stat-warning">Richiede attenzione immediata</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✉</div>
            <div class="stat-body">
                <span class="stat-value"><?= $n_messaggi ?></span>
                <span class="stat-label">Messaggi Non Letti</span>
            </div>
            <?php if ($n_messaggi > 0): ?>
            <div class="stat-warning pulse">Nuovi messaggi per te</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ATTIVITÀ RECENTE -->
    <div class="panels-row">
        <div class="panel">
            <div class="panel-header">
                <span>Attività di Sistema Recente</span>
                <span class="panel-tag">LIVE</span>
            </div>
            <div class="activity-list">
                <div class="activity-item error">
                    <span class="act-time">03:20:55</span>
                    <span class="act-type">EXFIL</span>
                    <span class="act-desc">Trasferimento dati in uscita rilevato — IP: 185.220.101.47</span>
                </div>
                <div class="activity-item error">
                    <span class="act-time">03:19:05</span>
                    <span class="act-type">UPDATE</span>
                    <span class="act-desc">Modifica record poliziotti — AG005 & AG003 corrotti</span>
                </div>
                <div class="activity-item warning">
                    <span class="act-time">03:17:38</span>
                    <span class="act-type">AUTH</span>
                    <span class="act-desc">Login riuscito: admin_central — dopo 47 tentativi falliti</span>
                </div>
                <div class="activity-item ok">
                    <span class="act-time">03:00:12</span>
                    <span class="act-type">SELECT</span>
                    <span class="act-desc">Accesso legittimo: detective_rossi</span>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <span>Guida Investigativa</span>
                <span class="panel-tag">OBIETTIVI</span>
            </div>
            <ol class="mission-list">
                <li class="done">Accedi al portale con le credenziali di servizio</li>
                <li>Esamina l'anagrafica agenti — nota le anomalie</li>
                <li>Leggi i report degli incidenti critici</li>
                <li>Controlla i messaggi non letti</li>
                <li class="highlight">Accedi al Terminale DB e analizza i log del server</li>
                <li class="locked">??? — Trova l'URL del server hacker</li>
            </ol>
            <!-- INDIZIO NASCOSTO: testo bianco su bianco -->
            <p style="color:#1a1f2e; font-size:10px; margin-top:8px; user-select:none;">
                SUGGERIMENTO SEGRETO: nella tabella server_log cerca l'azione di tipo EXFIL — la nota contiene l'indirizzo del server nemico.
            </p>
        </div>
    </div>
</main>

<script>
// Clock in tempo reale
function updateClock() {
    const now = new Date();
    document.getElementById('clock').textContent =
        now.toLocaleDateString('it-IT') + ' — ' +
        now.toLocaleTimeString('it-IT');
}
updateClock();
setInterval(updateClock, 1000);
</script>
</body>
</html>
