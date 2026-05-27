<?php
// ============================================================
//  soluzione.php — Debriefing Finale
//  Il giocatore inserisce gli indizi chiave raccolti durante
//  l'investigazione. Il risultato viene salvato in polizia_db
//  con nickname, punteggio, data e ora.
// ============================================================
session_start();

// ---- Connessione DB (polizia_db — nessuna sessione richiesta) ----
define('SOL_DB_HOST', 'localhost');
define('SOL_DB_USER', 'root');
define('SOL_DB_PASS', '');
define('SOL_DB_NAME', 'polizia_db');

function get_sol_db(): ?PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . SOL_DB_HOST . ";dbname=" . SOL_DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, SOL_DB_USER, SOL_DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            return null;
        }
    }
    return $pdo;
}

// ---- Risposte corrette ----
// Fase 1/2: IP attaccante trovato in server_log
define('ANSWER_IP', '185.220.101.47');
// Fase 3 — file puzzle coordinate.enc (decifrare con ROT13)
define('ANSWER_PASSWORD', 'Bl@ckH4t_2024!');
// Fase 3 — Admin Console (uno dei due comandi validi)
$ANSWER_COMMANDS = ['DROP TABLE h_identita', 'DROP DATABASE hacker_db'];

// ---- Leaderboard (caricata sempre) ----
$leaderboard = [];
$pdo_lb = get_sol_db();
if ($pdo_lb) {
    try {
        $leaderboard = $pdo_lb->query("
            SELECT nickname, punteggio, risolto_completamente, data_gioco
            FROM soluzioni_giocatori
            ORDER BY risolto_completamente DESC, punteggio DESC, data_gioco ASC
            LIMIT 10
        ")->fetchAll();
    } catch (PDOException $e) {}
}

// ---- Gestione form ----
$submitted  = false;
$result     = null;
$form_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname  = mb_substr(trim($_POST['nickname']  ?? ''), 0, 50);
    $clue_ip   = mb_substr(trim($_POST['clue_ip']   ?? ''), 0, 60);
    $clue_pass = mb_substr(trim($_POST['clue_pass'] ?? ''), 0, 100);
    $clue_cmd  = mb_substr(trim($_POST['clue_cmd']  ?? ''), 0, 100);

    if (mb_strlen($nickname) < 2) {
        $form_error = 'Il nickname deve contenere almeno 2 caratteri.';
    } else {
        $ip_ok   = ($clue_ip === ANSWER_IP);
        $pass_ok = ($clue_pass === ANSWER_PASSWORD);
        $cmd_ok  = in_array(strtoupper(trim($clue_cmd)), array_map('strtoupper', $ANSWER_COMMANDS));

        $score  = ($ip_ok ? 1 : 0) + ($pass_ok ? 1 : 0) + ($cmd_ok ? 1 : 0);
        $solved = ($score === 3);
        $saved  = false;
        $ts     = date('d/m/Y H:i:s');

        $pdo = get_sol_db();
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO soluzioni_giocatori
                        (nickname, clue_ip, clue_password, clue_comando,
                         ip_corretto, password_corretta, comando_corretto,
                         punteggio, risolto_completamente, indirizzo_ip_client)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $nickname,
                    $clue_ip,
                    $clue_pass,
                    $clue_cmd,
                    $ip_ok   ? 1 : 0,
                    $pass_ok ? 1 : 0,
                    $cmd_ok  ? 1 : 0,
                    $score,
                    $solved  ? 1 : 0,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                ]);
                $saved = true;

                // Ricarica classifica aggiornata
                $leaderboard = $pdo->query("
                    SELECT nickname, punteggio, risolto_completamente, data_gioco
                    FROM soluzioni_giocatori
                    ORDER BY risolto_completamente DESC, punteggio DESC, data_gioco ASC
                    LIMIT 10
                ")->fetchAll();
            } catch (PDOException $e) {}
        }

        $submitted = true;
        $result = compact('nickname', 'clue_ip', 'clue_pass', 'clue_cmd',
                          'ip_ok', 'pass_ok', 'cmd_ok', 'score', 'solved', 'saved', 'ts');
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Debriefing Finale — Detective Informatico</title>
    <link rel="stylesheet" href="css/soluzione.css">
</head>
<body>
<canvas id="matrix-canvas"></canvas>

<div class="sol-wrapper">

    <!-- INTESTAZIONE -->
    <div class="sol-header">
        <div class="sol-badge">// DETECTIVE INFORMATICO — DEBRIEFING FINALE //</div>
        <h1 class="sol-title">VERIFICA SOLUZIONE</h1>
        <p class="sol-subtitle">
            Inserisci le tre prove chiave raccolte durante l'indagine.<br>
            Il tuo risultato verrà registrato con data e ora.
        </p>
    </div>

    <?php if (!$submitted): ?>
    <!-- ============ FORM ============ -->
    <div class="sol-card">
        <div class="sol-card-title">&gt; INSERIMENTO PROVE INVESTIGATIVE</div>

        <?php if ($form_error): ?>
        <div class="sol-error-msg"><?= htmlspecialchars($form_error) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off" spellcheck="false">

            <div class="sol-field">
                <label for="nickname">NICKNAME DETECTIVE</label>
                <input type="text" id="nickname" name="nickname"
                       placeholder="es. detective_rossi"
                       value="<?= htmlspecialchars($_POST['nickname'] ?? '') ?>"
                       required maxlength="50">
            </div>

            <div class="sol-separator"></div>

            <div class="phase-label">FASE 1 &amp; 2 — Portale Polizia / Terminale DB</div>

            <div class="sol-field">
                <label for="clue_ip">INDIRIZZO IP DELL'ATTACCANTE</label>
                <div class="field-hint">
                    &gt; Trovato analizzando server_log con la query EXFIL — formato: xxx.xxx.xxx.xxx
                </div>
                <input type="text" id="clue_ip" name="clue_ip"
                       placeholder="es. 0.0.0.0"
                       value="<?= htmlspecialchars($_POST['clue_ip'] ?? '') ?>"
                       maxlength="60">
            </div>

            <div class="sol-separator"></div>

            <div class="phase-label">FASE 3 — File Puzzle (coordinate.enc)</div>

            <div class="sol-field">
                <label for="clue_pass">PASSWORD TEMPORANEA DEL SERVER NEXUS</label>
                <div class="field-hint">
                    &gt; Ottenuta decifrando coordinate.enc con ROT13 — attenzione a maiuscole e caratteri speciali
                </div>
                <input type="text" id="clue_pass" name="clue_pass"
                       placeholder="es. P@ssw0rd"
                       value="<?= htmlspecialchars($_POST['clue_pass'] ?? '') ?>"
                       maxlength="100">
            </div>

            <div class="sol-separator"></div>

            <div class="phase-label">FASE 3 — Admin Console NEXUS</div>

            <div class="sol-field">
                <label for="clue_cmd">COMANDO FINALE ESEGUITO NELLA CONSOLE</label>
                <div class="field-hint">
                    &gt; Il comando SQL che ha neutralizzato il database del gruppo NEXUS
                </div>
                <input type="text" id="clue_cmd" name="clue_cmd"
                       placeholder="es. DROP ..."
                       value="<?= htmlspecialchars($_POST['clue_cmd'] ?? '') ?>"
                       maxlength="100">
            </div>

            <div class="sol-submit-row">
                <button type="submit" class="sol-btn">INVIA SOLUZIONE ↵</button>
            </div>
        </form>
    </div>

    <?php else: ?>
    <!-- ============ RISULTATO ============ -->
    <?php
        $r = $result;
        if ($r['solved'])       { $cls = 'success'; }
        elseif ($r['score'] > 0){ $cls = 'partial'; }
        else                    { $cls = 'fail'; }
    ?>
    <div class="sol-result">
        <div class="result-header">
            <?php if ($r['solved']): ?>
            <div class="result-title success">CASO CHIUSO</div>
            <?php elseif ($r['score'] > 0): ?>
            <div class="result-title partial">INDAGINE INCOMPLETA</div>
            <?php else: ?>
            <div class="result-title fail">SOLUZIONE ERRATA</div>
            <?php endif; ?>

            <div class="result-saved">
                Detective: <span><?= htmlspecialchars($r['nickname']) ?></span>
                <?php if ($r['saved']): ?>
                &nbsp;|&nbsp; Registrato il <span><?= htmlspecialchars($r['ts']) ?></span>
                <?php else: ?>
                &nbsp;|&nbsp; <span style="color:var(--red)">DB non raggiungibile — salvataggio non riuscito</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="score-row">
            <div class="score-num <?= $cls ?>">
                <?= $r['score'] ?><span class="score-max">/3</span>
            </div>
            <div class="score-label">
                INDIZI<br>CORRETTI<?= $r['solved'] ? '<br><span style="color:var(--green)">★ RISOLTO</span>' : '' ?>
            </div>
        </div>

        <div class="clue-list">
            <!-- IP attaccante -->
            <div class="clue-row">
                <span class="clue-icon <?= $r['ip_ok'] ? 'ok' : 'err' ?>"><?= $r['ip_ok'] ? '✓' : '✗' ?></span>
                <div class="clue-body">
                    <div class="clue-label">IP ATTACCANTE (FASE 1/2)</div>
                    <div class="clue-value <?= $r['ip_ok'] ? 'ok' : 'err' ?>">
                        <?= $r['clue_ip'] !== '' ? htmlspecialchars($r['clue_ip']) : '<em style="opacity:.5">non fornito</em>' ?>
                    </div>
                </div>
                <span class="clue-badge <?= $r['ip_ok'] ? 'ok' : 'err' ?>"><?= $r['ip_ok'] ? 'CORRETTO' : 'ERRATO' ?></span>
            </div>

            <!-- Password -->
            <div class="clue-row">
                <span class="clue-icon <?= $r['pass_ok'] ? 'ok' : 'err' ?>"><?= $r['pass_ok'] ? '✓' : '✗' ?></span>
                <div class="clue-body">
                    <div class="clue-label">PASSWORD SERVER NEXUS (FASE 3 — coordinate.enc)</div>
                    <div class="clue-value <?= $r['pass_ok'] ? 'ok' : 'err' ?>">
                        <?= $r['clue_pass'] !== '' ? htmlspecialchars($r['clue_pass']) : '<em style="opacity:.5">non fornita</em>' ?>
                    </div>
                </div>
                <span class="clue-badge <?= $r['pass_ok'] ? 'ok' : 'err' ?>"><?= $r['pass_ok'] ? 'CORRETTA' : 'ERRATA' ?></span>
            </div>

            <!-- Comando finale -->
            <div class="clue-row">
                <span class="clue-icon <?= $r['cmd_ok'] ? 'ok' : 'err' ?>"><?= $r['cmd_ok'] ? '✓' : '✗' ?></span>
                <div class="clue-body">
                    <div class="clue-label">COMANDO FINALE ADMIN CONSOLE (FASE 3)</div>
                    <div class="clue-value <?= $r['cmd_ok'] ? 'ok' : 'err' ?>">
                        <?= $r['clue_cmd'] !== '' ? htmlspecialchars($r['clue_cmd']) : '<em style="opacity:.5">non fornito</em>' ?>
                    </div>
                </div>
                <span class="clue-badge <?= $r['cmd_ok'] ? 'ok' : 'err' ?>"><?= $r['cmd_ok'] ? 'CORRETTO' : 'ERRATO' ?></span>
            </div>
        </div>

        <!-- Messaggio finale -->
        <?php if ($r['solved']): ?>
        <div class="result-msg success">
            Complimenti, Detective <strong><?= htmlspecialchars($r['nickname']) ?></strong>!<br>
            Hai risolto correttamente tutte e tre le fasi dell'indagine. Il gruppo NEXUS è stato smantellato
            e le identità dei criminali consegnate alle autorità. Il tuo risultato è stato certificato.
        </div>
        <?php elseif ($r['score'] > 0): ?>
        <div class="result-msg partial">
            Qualcosa non torna, Detective <strong><?= htmlspecialchars($r['nickname']) ?></strong>.<br>
            <?php
            $wrong = [];
            if (!$r['ip_ok'])   $wrong[] = "l'IP dell'attaccante (torna al Terminale DB — query EXFIL)";
            if (!$r['pass_ok']) $wrong[] = "la password del server NEXUS (riprova a decifrare coordinate.enc con ROT13)";
            if (!$r['cmd_ok'])  $wrong[] = "il comando finale (controlla la Admin Console — digitando <em>help</em> vedi i comandi disponibili)";
            echo 'Rivedere: ' . implode('; ', $wrong) . '.';
            ?>
            <br>Il tuo tentativo è stato registrato.
        </div>
        <?php else: ?>
        <div class="result-msg fail">
            Nessuna delle risposte fornite è corretta, Detective <strong><?= htmlspecialchars($r['nickname']) ?></strong>.<br>
            Ricomincia l'indagine dall'inizio: accedi al Portale Polizia, analizza i log nel Terminale DB
            e risolvi i file puzzle nel sito NEXUS. Il tuo tentativo è stato registrato.
        </div>
        <?php endif; ?>

        <div class="result-actions">
            <?php if (!$r['solved']): ?>
            <a href="soluzione.php" class="sol-btn-sm retry">↺ RIPROVA</a>
            <?php endif; ?>
            <a href="../polizia/index.php" class="sol-btn-sm home">↩ PORTALE POLIZIA</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============ CLASSIFICA ============ -->
    <div class="sol-leaderboard">
        <div class="lb-title">&gt; HALL OF FAME — TOP 10 DETECTIVE</div>

        <?php if (empty($leaderboard)): ?>
        <div class="lb-empty">Nessuna soluzione registrata ancora. Sii il primo!</div>
        <?php else: ?>
        <table class="lb-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>NICKNAME</th>
                    <th style="text-align:center">SCORE</th>
                    <th style="text-align:center">STATO</th>
                    <th>DATA E ORA</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($leaderboard as $i => $row):
                $rank = $i + 1;
                $rankCls = $rank === 1 ? 'gold' : ($rank === 2 ? 'silver' : ($rank === 3 ? 'bronze' : ''));
                $scoreCls = $row['punteggio'] == 3 ? 'full' : ($row['punteggio'] > 0 ? 'partial' : 'zero');
            ?>
            <tr>
                <td class="lb-rank <?= $rankCls ?>"><?= $rank ?></td>
                <td class="lb-nick"><?= htmlspecialchars($row['nickname']) ?></td>
                <td class="lb-score <?= $scoreCls ?>">
                    <?= $row['punteggio'] ?>/3<?= $row['punteggio'] == 3 ? ' <span class="lb-star">★</span>' : '' ?>
                </td>
                <td style="text-align:center">
                    <span class="lb-solved-badge <?= $row['risolto_completamente'] ? 'yes' : 'no' ?>">
                        <?= $row['risolto_completamente'] ? 'RISOLTO' : 'PARZIALE' ?>
                    </span>
                </td>
                <td class="lb-date">
                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($row['data_gioco']))) ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</div><!-- /sol-wrapper -->

<script src="../hacker/js/matrix.js"></script>
</body>
</html>
