<?php
// ============================================================
//  terminale.php — Terminale MySQL Simulato (Fase 2)
//  Il giocatore lancia query reali sul DB polizia
// ============================================================
session_start();
require_once 'includes/config.php';
check_session();

$output   = [];
$query_in = '';
$error    = '';
$victory  = false;

// Whitelist di keyword SQL consentite (sicurezza didattica)
$allowed_keywords = ['SELECT', 'SHOW', 'DESCRIBE', 'DESC', 'EXPLAIN'];

// Query chiave che sblocca la Fase 3
$victory_query_pattern = '/SELECT.*FROM\s+server_log.*WHERE.*azione\s*=\s*[\'"]EXFIL[\'"]/i';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query_in = trim($_POST['query'] ?? '');

    if ($query_in !== '') {
        $first_word = strtoupper(strtok($query_in, " \t\n"));

        if (!in_array($first_word, $allowed_keywords, true)) {
            $error = 'ACCESSO NEGATO: Solo query di lettura (SELECT, SHOW, DESCRIBE) sono consentite su questo terminale.';
        } else {
            try {
                $pdo  = get_db();
                $stmt = $pdo->query($query_in);
                $rows = $stmt->fetchAll();

                // Salva nello storico sessione
                $_SESSION['terminal_history'][] = $query_in;

                // Controlla se è la query vincente
                if (preg_match($victory_query_pattern, $query_in) && count($rows) > 0) {
                    $victory = true;
                    // Salva il flag di progresso
                    $_SESSION['fase2_completata'] = true;
                }

                $cols = $rows ? array_keys($rows[0]) : [];
                $output = ['cols' => $cols, 'rows' => $rows, 'count' => count($rows)];

            } catch (PDOException $e) {
                $error = 'ERRORE SQL: ' . htmlspecialchars($e->getMessage());
            }
        }
    }
}

$history = $_SESSION['terminal_history'] ?? [];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Terminale DB — Polizia di Stato</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/terminal.css">
</head>
<body class="terminal-page">
<div class="scanlines"></div>
<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <div class="top-bar-left">
            <h1>Terminale Database</h1>
            <span class="breadcrumb">Dashboard → Terminale DB</span>
        </div>
        <div class="top-bar-right">
            <span class="alert-banner">MySQL 8.0 — polizia_db</span>
        </div>
    </header>

    <?php if ($victory): ?>
    <!-- SCHERMATA VITTORIA FASE 2 -->
    <div class="victory-overlay">
        <div class="victory-box">
            <div class="victory-icon">⚡</div>
            <h2>TRACCIA TROVATA</h2>
            <p>Hai individuato l'azione <strong>EXFIL</strong> nei log del server.<br>
            La nota contiene l'indirizzo del server Command &amp; Control degli hacker.</p>
            <div class="found-url">
                <?php
                // Mostra la riga EXFIL con l'URL
                foreach ($output['rows'] as $r) {
                    if (isset($r['note']) && strpos($r['note'], 'C2_SERVER') !== false) {
                        echo '<div class="exfil-row">';
                        echo '<span class="exfil-label">C2_SERVER trovato:</span> ';
                        // Evidenzia l'URL nella nota
                        echo '<span class="exfil-url">' . htmlspecialchars($r['note']) . '</span>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <p class="url-hint">Raggiungere l'URL indicato per infiltrarsi nel server hacker.<br>
            <strong>Porta predefinita del sito hacker: :8081/hacker/</strong></p>
            <a href="<?= htmlspecialchars('http://localhost:8081/hacker/') ?>" class="btn-proceed" target="_blank">
                ▶ PROCEDI ALLA FASE 3
            </a>
        </div>
    </div>
    <?php endif; ?>

    <div class="terminal-layout">
        <!-- PANNELLO SINISTRO: guida query -->
        <div class="query-guide">
            <div class="panel-header"><span>Guida Investigativa — Query Suggerite</span></div>
            <div class="guide-body">
                <p>Esplora il database per trovare le tracce dell'intrusione.</p>

                <div class="hint-group">
                    <div class="hint-title">1. Quali tabelle esistono?</div>
                    <code class="hint-code" onclick="fillQuery(this)">SHOW TABLES;</code>
                </div>
                <div class="hint-group">
                    <div class="hint-title">2. Struttura dei log</div>
                    <code class="hint-code" onclick="fillQuery(this)">DESCRIBE server_log;</code>
                </div>
                <div class="hint-group">
                    <div class="hint-title">3. Tutti i log del server</div>
                    <code class="hint-code" onclick="fillQuery(this)">SELECT * FROM server_log ORDER BY timestamp;</code>
                </div>
                <div class="hint-group">
                    <div class="hint-title">4. Solo gli accessi riusciti</div>
                    <code class="hint-code" onclick="fillQuery(this)">SELECT * FROM server_log WHERE esito = 'successo';</code>
                </div>
                <div class="hint-group">
                    <div class="hint-title">5. IP sospetto</div>
                    <code class="hint-code" onclick="fillQuery(this)">SELECT * FROM server_log WHERE ip_sorgente = '185.220.101.47';</code>
                </div>
                <div class="hint-group highlight-hint">
                    <div class="hint-title">⭐ Azione critica da cercare</div>
                    <code class="hint-code" onclick="fillQuery(this)">SELECT * FROM server_log WHERE azione = 'EXFIL';</code>
                </div>
            </div>
        </div>

        <!-- PANNELLO DESTRO: terminale -->
        <div class="terminal-panel">
            <div class="terminal-titlebar">
                <span class="t-dot red"></span>
                <span class="t-dot yellow"></span>
                <span class="t-dot green"></span>
                <span class="t-title">mysql> polizia_db — Detective Rossi</span>
            </div>

            <!-- STORICO -->
            <div class="terminal-output" id="termOutput">
                <div class="term-line welcome">
                    MySQL 8.0.36 — Polizia di Stato — Database Investigativo<br>
                    Connesso come: <span class="term-user"><?= htmlspecialchars($_SESSION['username']) ?></span><br>
                    Database: <span class="term-db">polizia_db</span><br>
                    <br>
                    <span class="term-hint">Usa i suggerimenti a sinistra o scrivi la tua query.</span>
                </div>

                <?php if ($query_in): ?>
                <div class="term-block">
                    <div class="term-prompt">mysql&gt; <span class="term-query"><?= htmlspecialchars($query_in) ?></span></div>

                    <?php if ($error): ?>
                        <div class="term-error"><?= $error ?></div>
                    <?php elseif ($output): ?>
                        <?php if (empty($output['rows'])): ?>
                            <div class="term-empty">Empty set (0 rows)</div>
                        <?php else: ?>
                            <div class="term-table-wrap">
                                <table class="term-table">
                                    <thead>
                                        <tr><?php foreach ($output['cols'] as $col): ?>
                                            <th><?= htmlspecialchars($col) ?></th>
                                        <?php endforeach; ?></tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($output['rows'] as $row): ?>
                                        <tr>
                                        <?php foreach ($row as $key => $val): ?>
                                            <td class="<?= ($val === null) ? 'null-cell' : '' ?>
                                                        <?= ($key === 'note' && strpos((string)$val, 'C2_SERVER') !== false) ? 'highlight-cell' : '' ?>">
                                                <?= ($val === null) ? '<em>NULL</em>' : htmlspecialchars((string)$val) ?>
                                            </td>
                                        <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="term-rowcount"><?= $output['count'] ?> row(s) in set</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- INPUT QUERY -->
            <form method="POST" class="terminal-input-form">
                <div class="prompt-line">
                    <span class="prompt-label">mysql&gt;</span>
                    <input type="text" name="query" id="queryInput"
                           value="<?= htmlspecialchars($query_in) ?>"
                           placeholder="SELECT * FROM server_log WHERE ..."
                           autocomplete="off" spellcheck="false">
                    <button type="submit">ESEGUI ↵</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function fillQuery(el) {
    document.getElementById('queryInput').value = el.textContent.trim().replace(/;$/, '');
    document.getElementById('queryInput').focus();
}
// Scroll output in fondo
const out = document.getElementById('termOutput');
if (out) out.scrollTop = out.scrollHeight;
</script>
</body>
</html>
