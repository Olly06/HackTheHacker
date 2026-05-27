# Detective Informatico — Progetto GPOI
**Avventura investigativa web-based a tema Cybersecurity**

---

## Struttura Directory

```
HackTheHacker/
├── database/
│   ├── polizia_db.sql          ← Schema DB Polizia
│   ├── hacker_db.sql           ← Schema DB Hacker
│   ├── polizia_dbbulk.sql      ← Dati di esempio Polizia
│   ├── hacker_dbbulk.sql       ← Dati di esempio Hacker
│   └── query_guida_fase2.sql   ← Query guida per la Fase 2 (uso docente)
│
├── polizia/                    ← SITO POLIZIA (Fase 1 + 2)
│   ├── index.php               ← Login
│   ├── dashboard.php           ← Dashboard principale
│   ├── anagrafica.php          ← Tabella agenti (con corruzione visibile)
│   ├── mandati.php             ← Registro mandati (primi 50 + "mostra altri")
│   ├── report.php              ← Report incidenti critici
│   ├── messaggi.php            ← Messaggi interni (con indizi)
│   ├── terminale.php           ← Terminale MySQL simulato (Fase 2)
│   ├── logout.php
│   ├── css/
│   │   ├── login.css
│   │   ├── dashboard.css
│   │   └── terminal.css
│   └── includes/
│       ├── config.php          ← Connessione DB
│       └── sidebar.php         ← Navigazione comune
│
├── hacker/                     ← SITO HACKER (Fase 3)
│   ├── index.php               ← Login hacker
│   ├── dashboard.php           ← Dashboard NEXUS
│   ├── identita.php            ← Alias dei membri (solo alias, niente dati reali)
│   ├── operazioni.php          ← Registro operazioni (primi 50 + "mostra altri")
│   ├── drops.php               ← File rubati (primi 50 + "mostra altri") + file puzzle
│   ├── download.php            ← Serve i file puzzle cifrati/offuscati
│   ├── admin.php               ← Console finale (colpo di grazia)
│   ├── logout.php
│   ├── css/
│   │   ├── hacker-login.css
│   │   ├── hacker-dash.css
│   │   └── admin-console.css
│   ├── js/
│   │   └── matrix.js           ← Animazione Matrix
│   └── includes/
│       ├── config.php          ← Connessione DB hacker
│       └── h-sidebar.php       ← Navigazione comune hacker
│
└── index.php                   ← Pagina di scelta (Polizia / Hacker)
```

---

## Requisiti di Sistema

| Componente | Versione consigliata |
|------------|---------------------|
| PHP        | 7.4+                |
| MySQL      | 8.0+                |
| Web Server | Apache / Nginx      |
| XAMPP      | 8.x (sviluppo locale)|

---

## Installazione

### 1. Copia i file
```
XAMPP: C:\xampp\htdocs\HackTheHacker\
Linux: /var/www/html/HackTheHacker/
```

### 2. Crea i database
Apri **phpMyAdmin** oppure il client MySQL e lancia in ordine:
```sql
SOURCE /percorso/HackTheHacker/database/polizia_db.sql;
SOURCE /percorso/HackTheHacker/database/polizia_dbbulk.sql;
SOURCE /percorso/HackTheHacker/database/hacker_db.sql;
SOURCE /percorso/HackTheHacker/database/hacker_dbbulk.sql;
SOURCE /percorso/HackTheHacker/database/add_soluzioni_table.sql;
SOURCE /percorso/HackTheHacker/database/update_c2_server.sql;
```

### 3. Configura le connessioni
Modifica entrambi i file `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // tuo utente MySQL
define('DB_PASS', '');          // tua password MySQL
```

### 4. Avvia i siti
- **Sito Polizia:** `http://localhost/HackTheHacker/polizia/`
- **Sito Hacker:**  `http://localhost/HackTheHacker/n3xu5dr4k7f2x1m8/` *(URL da scoprire tramite il gioco)*

---

## Guida al Gioco

### FASE 1 — Indagine sul Portale Polizia
**URL:** `http://localhost/HackTheHacker/polizia/`

**Credenziali detective:**
```
Username: detective_rossi
Password: Falcon2077!
```

**Percorso investigativo:**
1. Accedi → Dashboard (nota l'allerta di sicurezza)
2. Vai su **Anagrafica Agenti** → noti AG003 e AG005 corrotti
3. Leggi i **Report Incidenti** → trovi dettagli sull'intrusione notturna
4. Apri i **Messaggi** (2 non letti) → il tecnico IT suggerisce di guardare `server_log`
5. **Guarda il sorgente HTML** delle pagine → ci sono commenti nascosti con indizi
6. Passa alla Fase 2

**Indizi nascosti (per il docente):**
- Commento HTML in `index.php`: credenziali in chiaro
- Commento HTML in `dashboard.php`: estratto dei log di sistema
- Commento HTML in `report.php`: suggerimento tecnico IT sull'azione EXFIL
- Testo invisibile in `dashboard.php`: suggerimento sulla tabella server_log

---

### FASE 2 — Terminale DB
**URL:** `http://localhost/HackTheHacker/polizia/terminale.php`

**Query da eseguire in ordine:**
```sql
-- 1. Esplora le tabelle
SHOW TABLES;

-- 2. Studia la struttura dei log
DESCRIBE server_log;

-- 3. Visualizza tutti i log
SELECT * FROM server_log ORDER BY timestamp;

-- 4. Filtra per IP sospetto
SELECT * FROM server_log WHERE ip_sorgente = '185.220.101.47';

-- 5. QUERY VINCENTE — trova l'URL hacker
SELECT * FROM server_log WHERE azione = 'EXFIL';
```

**Risultato della query vincente:**
La colonna `note` della riga EXFIL contiene:
```
C2_SERVER=http://localhost/HackTheHacker/hacker | Base operativa identificata.
```
→ Il giocatore ha trovato l'URL del server hacker.

---

### FASE 3 — Infiltrazione NEXUS
**URL:** `http://localhost/HackTheHacker/n3xu5dr4k7f2x1m8/` *(scoperto nella Fase 2)*

**Credenziali hacker** (trovate nel sorgente HTML della login page):
```
Username: ph4ntom
Password: gh0stInTh3M4ch1n3
```

**Percorso:**
1. Login → Dashboard NEXUS
2. Vai su **Membri** → visualizza gli alias attivi del gruppo
3. Vai su **Operazioni** → studia le operazioni (lista con "mostra altri")
4. Vai su **File Rubati** → esplora i drop e risolvi i file puzzle (vedi sotto)
5. Accedi alla **Admin Console** → lancia il comando finale

**Comando finale (Admin Console):**
```
DROP TABLE h_identita
```
oppure:
```
DROP DATABASE hacker_db
```
→ **Schermata di vittoria** con i nomi degli arrestati.

Al termine, il giocatore può premere **"VERIFICA LA TUA SOLUZIONE"** per accedere al
modulo di debriefing (`solution/soluzione.php`) dove inserisce i tre indizi chiave:
- IP dell'attaccante trovato in `server_log` → `185.220.101.47`
- Password temporanea da `coordinate.enc` (ROT13) → `Bl@ckH4t_2024!`
- Comando finale eseguito nella Admin Console

Il risultato (nickname, punteggio, data/ora) viene salvato in `polizia_db.soluzioni_giocatori`
e compare nella classifica "Hall of Fame" in fondo alla pagina.

---

## File Puzzle — Sezione "File Rubati"

Nella pagina **File Rubati** (`drops.php`), oltre all'archivio dei file esfiltrati, compare una sezione nascosta chiamata **"Partizione Nascosta — /nexus/.shadow/"** con tre file anomali scaricabili. Ogni file richiede una tecnica diversa per essere letto.

### File 1 — `sistema_backup.dat` (Facile)
**Tecnica:** cambiare estensione da `.dat` a `.txt` e aprirlo con un editor di testo.

**Contenuto rivelato:** log interno di sessione NEXUS con i callsign dei membri connessi e un riferimento a `coordinate.enc`.

**Hint sulla pagina:** *"Il tuo editor di testo sa leggere più di quello che pensi."*

---

### File 2 — `msg_cifrato.b64` (Medio)
**Tecnica:** aprire il file come testo e decodificarlo con Base64.

```bash
# Linux / macOS
base64 -d msg_cifrato.b64

# PowerShell
[System.Text.Encoding]::UTF8.GetString([System.Convert]::FromBase64String((Get-Content msg_cifrato.b64)))

# Online: base64decode.org
```

**Contenuto rivelato:** messaggio cifrato tra membri del gruppo NEXUS con luogo e orario della riunione (Via Morgagni 7, Milano — Stanza B-03).

**Hint sulla pagina:** *"Ogni blocco di caratteri nasconde due facce. Cerca lo schema."*

---

### File 3 — `coordinate.enc` (Difficile — INDIZIO CRUCIALE)
**Tecnica:** decifrare con ROT13 (sostituzione di 13 lettere dell'alfabeto).

```bash
# Linux / macOS
cat coordinate.enc | tr 'A-Za-z' 'N-ZA-Mn-za-m'

# Python
import codecs; codecs.decode(open('coordinate.enc').read(), 'rot_13')

# Online: rot13.com
```

**Contenuto rivelato:** posizione del server principale, codice di accesso alla stanza, orario della riunione e password temporanea del server.

```
POSIZIONE SERVER PRINCIPALE:
  Indirizzo : Via Morgagni 7, Milano
  Piano     : Seminterrato
  Stanza    : B-03
  Codice    : 7734

PASSWORD SERVER TEMPORANEA: Bl@ckH4t_2024!
```

**Hint sulla pagina:** *"Tredici passi separano la verità dalla menzogna."*

---

## Funzionalità UI

### "Mostra altri" — Paginazione lazy
Le pagine con grandi volumi di dati mostrano i primi **50 record** e nascondono il resto. Un pulsante in fondo alla tabella rivela tutti i record rimanenti senza ricaricare la pagina.

Pagine coinvolte:
- `polizia/mandati.php` — Registro Mandati
- `hacker/operazioni.php` — Registro Operazioni
- `hacker/drops.php` — File Rubati

---

## Note di Sicurezza (per la presentazione)

| Aspetto | Implementazione |
|---------|----------------|
| **Login Polizia** | Prepared statements PDO — sicuro |
| **Login Hacker** | Prepared statements PDO — sicuro |
| **Terminale DB** | Whitelist SELECT/SHOW/DESCRIBE — solo lettura |
| **Admin Console** | Comandi simulati — nessun DROP reale eseguito |
| **Download puzzle** | Autenticazione richiesta — solo utenti loggati |
| **Password** | MD5 (didattico) — in produzione usare password_hash() |
| **Sessioni** | session_regenerate_id() + timeout 30 min |

> **NOTA DIDATTICA:** Tutti i comandi "distruttivi" nella Admin Console
> sono completamente simulati — nessuna query DROP viene mai eseguita
> sul database reale. Il gioco mostra solo animazioni e output fittizi.

---

## Concetti di Cybersecurity Trattati

1. **Autenticazione sicura** — prepared statements, session management
2. **Log forensi** — analisi di server_log per ricostruire un attacco
3. **Privilege escalation** — l'hacker usa account admin_central compromesso
4. **Data exfiltration** — concetto di EXFIL e C2 server
5. **SQL per l'investigazione** — uso di SELECT/WHERE/GROUP BY in ambito forense
6. **OSINT sul codice sorgente** — trovare indizi nei commenti HTML
7. **Brute force** — 47 tentativi di login prima dell'accesso riuscito
8. **Data tampering** — sovrascrittura record con dati corrotti
9. **Encoding e cifratura base** — Base64, ROT13, estensioni file come offuscamento
10. **ARG / puzzle investigativi** — file nascosti con tecniche di decodifica reali

---

## Crediti

**Progetto scolastico GPOI** — Gestione Progetto e Organizzazione d'Impresa
Tema: Sicurezza Informatica e Sviluppo Web Full-Stack

Tecnologie: PHP 7.4+ · MySQL 8 · HTML5 · CSS3 · JavaScript ES6
