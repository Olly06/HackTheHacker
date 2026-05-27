-- ============================================================
--  update_c2_server.sql
--  Aggiorna il riferimento al server NEXUS in server_log.
--  Eseguire su polizia_db DOPO aver importato polizia_dbbulk.sql.
-- ============================================================

USE polizia_db;

UPDATE server_log
SET note = 'C2_SERVER=http://localhost/HackTheHacker/n3xu5dr4k7f2x1m8/ | Base operativa identificata.'
WHERE azione = 'EXFIL';
