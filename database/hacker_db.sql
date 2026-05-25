-- ============================================================
--  HACKER GROUP "GHOST PROTOCOL" — Database (hacker_db)
--  NOTA: login VOLUTAMENTE VULNERABILE a SQL Injection
-- ============================================================

CREATE DATABASE IF NOT EXISTS hacker_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hacker_db;

-- -----------------------------------------------------------
-- UTENTI — query di login NON usa prepared statements
-- -----------------------------------------------------------
CREATE TABLE utenti (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(100),
    ruolo    VARCHAR(30) DEFAULT 'membro'
);

-- -----------------------------------------------------------
-- IDENTITÀ REALI DEGLI HACKER (il premio del gioco)
-- -----------------------------------------------------------
CREATE TABLE membri (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    alias            VARCHAR(50),
    nome_reale       VARCHAR(100),
    cognome_reale    VARCHAR(100),
    codice_fiscale   VARCHAR(20),
    indirizzo        VARCHAR(200),
    telefono         VARCHAR(20),
    email            VARCHAR(100),
    ruolo_nel_gruppo VARCHAR(50)
);

-- -----------------------------------------------------------
-- LOG OPERAZIONI CRIMINALI
-- -----------------------------------------------------------
CREATE TABLE operazioni (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    nome_operazione     VARCHAR(100),
    data_operazione     DATETIME,
    target              VARCHAR(200),
    tecnica_usata       VARCHAR(100),
    membro_responsabile INT,
    stato               VARCHAR(30),
    note                TEXT
);

-- -----------------------------------------------------------
-- DOSSIER VITTIME
-- -----------------------------------------------------------
CREATE TABLE dossier (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    codice       VARCHAR(30),
    tipo_target  VARCHAR(50),
    nome_ente    VARCHAR(100),
    dati_rubati  TEXT,
    data_attacco DATETIME
);


-- ===========================================================
--  POPOLAMENTO DATI
-- ===========================================================

-- Utenti hacker
INSERT INTO utenti VALUES
(1,'ghost',  MD5('gh0st_s3cr3t'), 'admin'),
(2,'phantom',MD5('ph4nt0m'),      'membro');

-- Identità reali (rivelate nella dashboard dopo il login)
INSERT INTO membri VALUES
(1,'Ghost_X',   'Davide', 'Marini',   'MRNDVD95H12F839K','Via Torino 42, Milano',       '+39 02 1234567',  'd.marini95@protonmail.com',       'Leader & Strategist'),
(2,'Phantom_Z', 'Luca',   'Esposito', 'SPSLCU91E30H501P','Via Roma 18, Napoli',          '+39 081 9876543', 'lucaesposito.dev@protonmail.com',  'Penetration Tester'),
(3,'Cipher_Y',  'Elena',  'Russo',    'RSSLEN98C44A944V','Via Garibaldi 7, Roma',        '+39 06 5551234',  'erusso.cipher@pm.me',             'Malware Developer'),
(4,'Vector_K',  'Francesco','Bruno',  'BRNFNC93A02G273F','Corso Francia 99, Torino',     '+39 011 7654321', 'fb.vector@tutanota.com',          'Social Engineer');

-- Operazioni (questa tabella verrà "distrutta" dal giocatore)
INSERT INTO operazioni VALUES
(1,'OPERATION_BLUEFORCE', '2024-11-15 00:00:00','Polizia di Stato — Portale Centrale','SQL Injection + Privilege Escalation',1,'completata','Accesso ottenuto. Dati esfiltrati. Records corrotti. SUCCESSO.'),
(2,'OPERATION_GHOSTNET',  '2024-10-01 14:30:00','Ministero dell\'Interno',              'Phishing + Lateral Movement',        2,'completata','Email dirigenti compromesse. 847 contatti esfiltrati.'),
(3,'OPERATION_DARKWEB',   '2024-12-01 00:00:00','[CLASSIFICATO] — Prossimo Obiettivo','TBD',                                 NULL,'pianificata','Dettagli disponibili solo per Ghost_X.');

-- Dossier
INSERT INTO dossier VALUES
(1,'DSS-001','Forze dell\'Ordine','Polizia di Stato',      'Anagrafica 847 agenti, 234 mandati attivi, coordinate operative','2024-11-15 00:15:00'),
(2,'DSS-002','Governo',           'Ministero dell\'Interno','Email classificate, rubrica completa dirigenti',                 '2024-10-01 18:45:00');
