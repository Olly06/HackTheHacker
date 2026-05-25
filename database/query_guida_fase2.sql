-- ============================================================
--  QUERY GUIDA — Fase 2: Terminale DB
--  Questo file è per il docente / presentazione GPOI
--  Mostra le query che il giocatore deve scoprire e lanciare
-- ============================================================

-- STEP 1: Scopri le tabelle disponibili
SHOW TABLES;
-- Risultato atteso: utenti, poliziotti, mandati, report_incidenti, server_log, messaggi_interni

-- STEP 2: Esamina la struttura di server_log
DESCRIBE server_log;
-- Il giocatore vede i campi: id, timestamp, ip_sorgente, utente, azione, query_sql, esito, note

-- STEP 3: Visualizza tutti i log in ordine cronologico
SELECT * FROM server_log ORDER BY timestamp;
-- Il giocatore nota l'IP sospetto 185.220.101.47 e le azioni anomale

-- STEP 4: Filtra solo i log dell'IP sospetto
SELECT * FROM server_log WHERE ip_sorgente = '185.220.101.47';
-- Mostra AUTH_FAIL x47, AUTH_OK, SELECT dump, UPDATE corruzione, EXFIL

-- STEP 5: Trova solo gli accessi riusciti
SELECT * FROM server_log WHERE esito = 'successo' AND ip_sorgente = '185.220.101.47';

-- STEP 6 (CHIAVE — sblocca Fase 3): Cerca l'azione EXFIL
SELECT * FROM server_log WHERE azione = 'EXFIL';
-- La colonna "note" contiene: C2_SERVER=http://localhost:8081/hacker
-- Questo è l'URL del server hacker — il giocatore può procedere alla Fase 3

-- STEP BONUS: Chi ha modificato i poliziotti?
SELECT * FROM server_log WHERE azione = 'UPDATE';
-- Mostra le query UPDATE eseguite da admin_central (account compromesso)

-- STEP BONUS: Analisi temporale dell'attacco
SELECT azione, COUNT(*) as occorrenze, MIN(timestamp) as primo, MAX(timestamp) as ultimo
FROM server_log
WHERE ip_sorgente = '185.220.101.47'
GROUP BY azione
ORDER BY primo;
