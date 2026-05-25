# 🔍 Detective Informatico — Progetto GPOI
**Avventura investigativa web-based a tema Cybersecurity**

---

## 📁 Struttura Directory

```
detective-game/
├── database/
│   ├── polizia_db.sql          ← Schema + dati DB Polizia
│   ├── hacker_db.sql           ← Schema + dati DB Hacker
│   └── query_guida_fase2.sql   ← Query per la Fase 2 (uso docente)
│
├── polizia/                    ← SITO POLIZIA (Fase 1 + 2)
│   ├── index.php               ← Login
│   ├── dashboard.php           ← Dashboard principale
│   ├── anagrafica.php          ← Tabella agenti (con corruzione visibile)
│   ├── mandati.php             ← Registro mandati
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
└── hacker/                     ← SITO HACKER (Fase 3)
    ├── index.php               ← Login hacker
    ├── dashboard.php           ← Dashboard NEXUS
    ├── identita.php            ← Nomi reali degli hacker
    ├── operazioni.php          ← Registro operazioni
    ├── drops.php               ← File rubati
    ├── admin.php               ← Console finale (colpo di grazia)
    ├── logout.php
    ├── css/
    │   ├── hacker-login.css
    │   ├── hacker-dash.css
    │   └── admin-console.css
    ├── js/
    │   └── matrix.js           ← Animazione Matrix
    └── includes/
        ├── config.php          ← Connessione DB hacker
        └── h-sidebar.php       ← Navigazione comune hacker
```

---

## ⚙️ Requisiti di Sistema

| Componente | Versione consigliata |
|------------|---------------------|
| PHP        | 8.0+                |
| MySQL      | 8.0+                |
| Web Server | Apache / Nginx      |
| XAMPP      | 8.x (sviluppo locale)|

---

## 🚀 Installazione

### 1. Copia i file
```
Copia le cartelle `polizia/` e `hacker/` in:
- XAMPP: C:\xampp\htdocs\detective-game\
- Linux: /var/www/html/detective-game/
```

### 2. Crea i database
Apri **phpMyAdmin** oppure il client MySQL e lancia:
```sql
SOURCE /percorso/detective-game/database/polizia_db.sql;
SOURCE /percorso/detective-game/database/hacker_db.sql;
```

### 3. Configura le connessioni
Modifica entrambi i file `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // tuo utente MySQL
define('DB_PASS', '');          // tua password MySQL
```

### 4. Avvia i siti
- **Sito Polizia:** `http://localhost/detective-game/polizia/`
- **Sito Hacker:**  `http://localhost/detective-game/hacker/`

Per simulare due porte diverse (più realistico):
```
Polizia: http://localhost/detective-game/polizia/
Hacker:  http://localhost/detective-game/hacker/
```

---

## 🎮 Guida al Gioco

### FASE 1 — Indagine sul Portale Polizia
**URL:** `http://localhost/detective-game/polizia/`

**Credenziali detective:**
```
Username: detective_rossi
Password: Falcon2077!
```

**Percorso investigativo:**
1. Accedi → Dashboard (nota l'allerta di sicurezza)
2. Vai su **Anagrafica Agenti** → noti AG003 e AG005 corrotti
3. Leggi i **Report Incidenti** → trovi dettagli sull'intrusione notturna
4. Apri i **Messaggi** (2 non letti) → il tecnico IT ti suggerisce di guardare `server_log`
5. **Guarda il sorgente HTML** delle pagine → ci sono commenti nascosti con indizi
6. Passa alla Fase 2

**Indizi nascosti (per il docente):**
- Commento HTML in `index.php`: credenziali in chiaro
- Commento HTML in `dashboard.php`: estratto dei log di sistema
- Commento HTML in `report.php`: suggerimento tecnico IT sull'azione EXFIL
- Testo invisibile in `dashboard.php`: suggerimento sulla tabella server_log

---

### FASE 2 — Terminale DB
**URL:** `http://localhost/detective-game/polizia/terminale.php`

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
C2_SERVER=http://localhost/detective-game/hacker | Base operativa identificata.
```
→ Il giocatore ha trovato l'URL del server hacker!

---

### FASE 3 — Infiltrazione NEXUS
**URL:** `http://localhost/detective-game/hacker/`

**Credenziali hacker** (trovate nel sorgente HTML della login page):
```
Username: ph4ntom
Password: gh0stInTh3M4ch1n3
```

**Percorso:**
1. Login → Dashboard NEXUS
2. Vai su **Identità Reali** → scopri i nomi veri degli hacker
3. Accedi alla **Admin Console** (solo per livello ADMIN)
4. Lancia il comando finale:
```
DROP TABLE h_identita
```
oppure:
```
DROP DATABASE hacker_db
```
5. **SCHERMATA DI VITTORIA** con i nomi degli arrestati!

---

## 🔒 Note di Sicurezza (per la presentazione)

| Aspetto | Implementazione |
|---------|----------------|
| **Login Polizia** | Prepared statements PDO — sicuro |
| **Login Hacker** | Prepared statements PDO — sicuro |
| **Terminale DB** | Whitelist SELECT/SHOW/DESCRIBE — solo lettura |
| **Admin Console** | Comandi simulati — nessun DROP reale eseguito |
| **Password** | MD5 (didattico) — in produzione usare password_hash() |
| **Sessioni** | session_regenerate_id() + timeout 30 min |

> ⚠️ **NOTA DIDATTICA:** Tutti i comandi "distruttivi" nella Admin Console
> sono **completamente simulati** — nessuna query DROP viene mai eseguita
> sul database reale. Il gioco mostra solo animazioni e output fittizi.

---

## 📚 Concetti di Cybersecurity Trattati

1. **Autenticazione sicura** — prepared statements, session management
2. **Log forensi** — analisi di server_log per ricostruire un attacco
3. **Privilege escalation** — l'hacker usa account admin_central compromesso
4. **Data exfiltration** — concetto di EXFIL e C2 server
5. **SQL per l'investigazione** — uso di SELECT/WHERE/GROUP BY in ambito forense
6. **OSINT sul codice sorgente** — trovare indizi nei commenti HTML
7. **Brute force** — 47 tentativi di login prima dell'accesso riuscito
8. **Data tampering** — sovrascrittura record con dati corrotti

---

## 👨‍🎓 Crediti

**Progetto scolastico GPOI** — Gestione Progetto e Organizzazione d'Impresa
Tema: Sicurezza Informatica e Sviluppo Web Full-Stack

Tecnologie: PHP 8 · MySQL 8 · HTML5 · CSS3 · JavaScript ES6
