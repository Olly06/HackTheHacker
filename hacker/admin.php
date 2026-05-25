<?php
// ============================================================
//  admin.php — Console di Amministrazione NEXUS (colpo finale)
//  Il giocatore "distrugge" il DB — tutto simulato, nessuna
//  query DROP reale viene eseguita sul database.
// ============================================================
session_start();
require_once 'includes/config.php';
check_session();

// Solo admin (livello 2) può accedere
if ($_SESSION['hacker_level'] != 2) {
    header('Location: dashboard.php?error=forbidden');
    exit;
}

$pdo        = get_db();
$output     = '';
$game_over  = false;
$cmd_input  = '';

// Comandi ammessi nella console (tutti simulati — nessuno eseguito realmente)
$valid_commands = [
    'ls'           => "drwxr-x  h_utenti/\ndrwxr-x  h_identita/\ndrwxr-x  h_operazioni/\ndrwxr-x  h_drops/\ndrwxr-x  admin_console/",
    'whoami'       => 'ph4ntom [root@nexus-srv]',
    'status'       => "DATABASE: nexus_db\nSTATUS: ONLINE\nRECORDS: " . $pdo->query("SELECT COUNT(*) FROM h_identita")->fetchColumn() . " identities stored\nBACKUP: DISABLED",
    'help'         => "Comandi disponibili:\n  ls          — lista tabelle\n  whoami      — utente corrente\n  status      — stato sistema\n  DROP TABLE h_identita   — ELIMINA identità\n  DROP DATABASE hacker_db — DISTRUGGI tutto",
];

// Comandi "distruttivi" — simulati, non eseguiti realmente
$destroy_table = 'DROP TABLE h_identita';
$destroy_db    = 'DROP DATABASE hacker_db';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cmd_input = trim($_POST['cmd'] ?? '');

    if ($cmd_input !== '') {
        $cmd_upper = strtoupper(trim($cmd_input));

        if (isset($valid_commands[strtolower($cmd_input)])) {
            // Comando info
            $output = $valid_commands[strtolower($cmd_input)];

        } elseif (strtoupper($cmd_input) === strtoupper($destroy_table)) {
            // Simula DROP TABLE — solo animazione, nessuna query reale
            $output    = "Query OK — 3 rows affected\nDROP TABLE h_identita — ESEGUITO\nRimosse 3 identità dal registro.";
            $game_over = true;
            $_SESSION['vittoria'] = 'table';

        } elseif (strtoupper($cmd_input) === strtoupper($destroy_db)) {
            // Simula DROP DATABASE — solo animazione, nessuna query reale
            $output    = "Query OK\nDROP DATABASE hacker_db — ESEGUITO\nDatabase rimosso dal server.";
            $game_over = true;
            $_SESSION['vittoria'] = 'database';

        } else {
            $output = "ERRORE: comando non riconosciuto — '{$cmd_input}'\nDigita 'help' per la lista comandi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Admin Console — NEXUS</title>
    <link rel="stylesheet" href="css/hacker-dash.css">
    <link rel="stylesheet" href="css/admin-console.css">
</head>
<body>
<canvas id="matrix-canvas"></canvas>
<div class="hacker-layout">
    <?php include 'includes/h-sidebar.php'; ?>
    <main class="h-main">
        <div class="h-topbar">
            <span class="h-title">// Admin Console — ROOT ACCESS //</span>
            <span class="h-status danger"><span class="blink-dot red"></span> ACCESSO PRIVILEGIATO</span>
        </div>

        <div class="admin-warning">
            ⚠ Stai operando con privilegi ROOT. I comandi qui eseguiti sono irreversibili.
        </div>

        <!-- GUIDA COMANDI -->
        <div class="cmd-guide">
            <div class="h-panel-title">&gt; Comandi Disponibili</div>
            <div class="cmd-grid">
                <div class="cmd-item info" onclick="fillCmd(this)"><code>ls</code><span>Lista tabelle</span></div>
                <div class="cmd-item info" onclick="fillCmd(this)"><code>whoami</code><span>Utente attivo</span></div>
                <div class="cmd-item info" onclick="fillCmd(this)"><code>status</code><span>Stato sistema</span></div>
                <div class="cmd-item info" onclick="fillCmd(this)"><code>help</code><span>Tutti i comandi</span></div>
                <div class="cmd-item destroy" onclick="fillCmd(this)">
                    <code>DROP TABLE h_identita</code><span>⚠ Distruggi identità</span>
                </div>
                <div class="cmd-item destroy" onclick="fillCmd(this)">
                    <code>DROP DATABASE hacker_db</code><span>💀 Distruggi tutto</span>
                </div>
            </div>
        </div>

        <!-- TERMINALE -->
        <div class="admin-terminal">
            <div class="terminal-titlebar">
                <span class="t-dot red"></span>
                <span class="t-dot yellow"></span>
                <span class="t-dot green"></span>
                <span class="t-title">root@nexus-srv:~# Admin Console v3.7.1</span>
            </div>

            <div class="terminal-output" id="adminOutput">
                <div class="term-line">
                    root@nexus-srv — NEXUS Database v3.7.1<br>
                    Connesso come: <span style="color:#ff4444">ROOT</span><br>
                    ⚠ Ogni azione è registrata e permanente.<br>
                    Digita <strong>help</strong> per la lista comandi.
                </div>

                <?php if ($cmd_input !== ''): ?>
                <div class="term-block">
                    <div class="term-prompt">root@nexus# <span class="term-query"><?= htmlspecialchars($cmd_input) ?></span></div>
                    <div class="term-result <?= $game_over ? 'destroy-result' : '' ?>"><?= nl2br(htmlspecialchars($output)) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <form method="POST" class="terminal-input-form" id="adminForm">
                <div class="prompt-line">
                    <span class="prompt-label root-prompt">root@nexus#</span>
                    <input type="text" name="cmd" id="cmdInput"
                           placeholder="digita un comando..."
                           autocomplete="off" spellcheck="false">
                    <button type="submit" class="exec-btn">ESEGUI ↵</button>
                </div>
            </form>
        </div>

    </main>
</div>

<?php if ($game_over): ?>
<!-- SCHERMATA FINALE VITTORIA -->
<div class="game-over-overlay" id="gameOverScreen">
    <div class="go-content">
        <div class="go-glitch" data-text="MISSIONE COMPIUTA">MISSIONE COMPIUTA</div>
        <div class="go-separator"></div>

        <div class="go-body">
            <p>Il database del gruppo hacker <strong>NEXUS</strong> è stato distrutto.</p>
            <p>Le identità dei criminali sono state acquisite e consegnate alle autorità.</p>
        </div>

        <div class="go-stats">
            <div class="go-stat"><span class="gs-val">3</span><span class="gs-lbl">Hacker Identificati</span></div>
            <div class="go-stat"><span class="gs-val">4</span><span class="gs-lbl">Operazioni Sventate</span></div>
            <div class="go-stat"><span class="gs-val">3</span><span class="gs-lbl">File Recuperati</span></div>
        </div>

        <div class="go-hacker-names">
            <div class="go-subtitle">&gt; Arrestati:</div>
            <?php
            // Mostra i nomi reali degli hacker come schermata finale
            try {
                $final = $pdo->query("SELECT alias, nome_reale, cognome, citta FROM h_identita")->fetchAll();
                foreach ($final as $f): ?>
                <div class="arrested-card">
                    <span class="arr-alias"><?= htmlspecialchars($f['alias']) ?></span>
                    <span class="arr-arrow">→</span>
                    <span class="arr-name"><?= htmlspecialchars($f['nome_reale'] . ' ' . $f['cognome']) ?></span>
                    <span class="arr-city">(<?= htmlspecialchars($f['citta']) ?>)</span>
                </div>
            <?php endforeach;
            } catch (PDOException $e) { echo '<p>Dati già eliminati.</p>'; }
            ?>
        </div>

        <div class="go-credits">
            Detective: <strong><?= htmlspecialchars($_SESSION['hacker_user'] ?? 'Rossi') ?></strong>
            — Progetto GPOI — Sicurezza Informatica
        </div>

        <a href="../polizia/dashboard.php" class="go-btn">↩ RITORNA AL PORTALE POLIZIA</a>
    </div>
</div>

<script>
// Animazione ritardata per effetto drammatico
setTimeout(() => {
    document.getElementById('gameOverScreen').classList.add('visible');
}, 1200);
</script>
<?php endif; ?>

<script src="js/matrix.js"></script>
<script>
function fillCmd(el) {
    document.getElementById('cmdInput').value = el.querySelector('code').textContent;
    document.getElementById('cmdInput').focus();
}
document.getElementById('adminOutput').scrollTop = 99999;
</script>
</body>
</html>
