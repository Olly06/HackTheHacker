<?php
// Questo nodo è stato rimosso dalla rete. Il server NEXUS si è spostato.
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>NEXUS — Nodo non trovato</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=VT323&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #000;
            color: #3a6a3a;
            font-family: 'Share Tech Mono', monospace;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 560px;
            width: 90%;
            text-align: center;
            padding: 40px;
            border: 1px solid #003a10;
            background: #050a05;
        }

        .err-code {
            font-family: 'VT323', monospace;
            font-size: 80px;
            color: #ff0040;
            text-shadow: 0 0 20px rgba(255,0,64,.4);
            letter-spacing: 8px;
            line-height: 1;
            margin-bottom: 8px;
        }

        .err-title {
            font-size: 12px;
            letter-spacing: 4px;
            color: #ff0040;
            margin-bottom: 32px;
            opacity: .8;
        }

        .sep {
            width: 120px;
            height: 1px;
            background: linear-gradient(to right, transparent, #003a10, transparent);
            margin: 0 auto 28px;
        }

        .err-msg {
            font-size: 11px;
            line-height: 2;
            color: #2a4a2a;
        }

        .err-msg .hl { color: #3a6a3a; }

        .err-detail {
            margin-top: 28px;
            padding: 14px 16px;
            background: #020802;
            border: 1px solid #003a10;
            border-left: 3px solid #ff0040;
            text-align: left;
            font-size: 10px;
            line-height: 2;
            color: #2a4a2a;
        }

        .err-detail .lbl { color: #1a3a1a; }
        .err-detail .val { color: #3a5a3a; }

        .blink {
            animation: blink 1s step-start infinite;
        }
        @keyframes blink { 50% { opacity: 0; } }
    </style>
</head>
<body>
<div class="container">
    <div class="err-code">404</div>
    <div class="err-title">ERR_NODE_NOT_FOUND</div>
    <div class="sep"></div>

    <div class="err-msg">
        <span class="hl">Nodo non raggiungibile.</span><br>
        Questo indirizzo non è più attivo sulla rete NEXUS.<br>
        Il server è stato <span class="hl">spostato</span> o è offline.<br><br>
        Verifica di avere l'indirizzo corretto<br>
        prima di tentare nuovamente la connessione.
    </div>

    <div class="err-detail">
        <div><span class="lbl">HOST       </span> <span class="val">nexus-node-01 [OFFLINE]</span></div>
        <div><span class="lbl">ERRORE     </span> <span class="val">Connection refused — 0x00000003</span></div>
        <div><span class="lbl">STATO      </span> <span class="val">NODE_RELOCATED</span></div>
        <div><span class="lbl">SUGGERIMENTO</span> <span class="val">Consultare i log di rete per il nuovo indirizzo.</span></div>
    </div>
</div>
</body>
</html>
