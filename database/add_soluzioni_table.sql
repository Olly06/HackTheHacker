-- ============================================================
--  add_soluzioni_table.sql — Tabella risultati giocatori
--  Eseguire su polizia_db dopo aver importato polizia_db.sql
-- ============================================================

USE polizia_db;

CREATE TABLE IF NOT EXISTS soluzioni_giocatori (
    id                    INT AUTO_INCREMENT PRIMARY KEY,
    nickname              VARCHAR(50)  NOT NULL,
    clue_ip               VARCHAR(60)  DEFAULT NULL,
    clue_password         VARCHAR(100) DEFAULT NULL,
    clue_comando          VARCHAR(100) DEFAULT NULL,
    ip_corretto           TINYINT(1)   NOT NULL DEFAULT 0,
    password_corretta     TINYINT(1)   NOT NULL DEFAULT 0,
    comando_corretto      TINYINT(1)   NOT NULL DEFAULT 0,
    punteggio             TINYINT(1)   NOT NULL DEFAULT 0,
    risolto_completamente TINYINT(1)   NOT NULL DEFAULT 0,
    data_gioco            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    indirizzo_ip_client   VARCHAR(45)  DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
