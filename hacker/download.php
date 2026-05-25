<?php
session_start();
require_once 'includes/config.php';
check_session();

$key = isset($_POST['f']) ? strval($_POST['f']) : '';

$backup = "=== NEXUS INTERNAL LOG - SESSION BACKUP ===\n"
        . "Data     : 2024-11-15   03:42:17\n"
        . "Operatore: PHANTOM\n"
        . "\n"
        . "NOTE SESSIONE:\n"
        . "  - Accesso completato al server principale\n"
        . "  - Dati esfiltrati: 47 GB\n"
        . "  - Vedere coordinate.enc per dettagli riunione\n"
        . "  - ATTENZIONE: qualcuno sta monitorando i movimenti.\n"
        . "    Mantenere silenzio radio fino a nuovo ordine.\n"
        . "\n"
        . "LOG CONNESSIONI:\n"
        . "  [03:41:02] SPECTER  - connesso\n"
        . "  [03:41:18] VIPER    - connesso\n"
        . "  [03:41:55] GHOST    - connesso\n"
        . "  [03:42:09] CIPHER   - connesso\n"
        . "  [03:42:17] PHANTOM  - sessione avviata\n"
        . "\n"
        . "PHANTOM OUT.";

$msg = "DA: SPECTER\n"
     . "A: TUTTO IL GRUPPO\n"
     . "OGGETTO: Operazione BLACKOUT - AGGIORNAMENTO\n"
     . "\n"
     . "Il drop e' andato bene. Abbiamo i file.\n"
     . "PHANTOM conferma: prossima riunione SABATO 23 Novembre ore 23:00.\n"
     . "Luogo: Via Morgagni 7, Milano - Seminterrato, Stanza B-03.\n"
     . "Codice porta: 7734\n"
     . "\n"
     . "Non usate i telefoni personali. Solo canale cifrato.\n"
     . "Chi manca senza preavviso viene rimosso dal gruppo.\n"
     . "\n"
     . "- SPECTER";

$coord = "=== NEXUS CORE - DATI RISERVATI ===\n"
       . "\n"
       . "POSIZIONE SERVER PRINCIPALE:\n"
       . "  Indirizzo : Via Morgagni 7, Milano\n"
       . "  Piano     : Seminterrato\n"
       . "  Stanza    : B-03\n"
       . "  Codice    : 7734\n"
       . "\n"
       . "PROSSIMA RIUNIONE:\n"
       . "  Data      : Sabato 23 Novembre\n"
       . "  Ora       : 23:00\n"
       . "  Presenze  : Tutti i membri. Nessuna eccezione.\n"
       . "\n"
       . "PASSWORD SERVER TEMPORANEA: Bl@ckH4t_2024!\n"
       . "\n"
       . "ATTENZIONE - Se questo file cade in mani sbagliate,\n"
       . "attivare immediatamente il protocollo BURN.\n"
       . "\n"
       . "- PHANTOM";

if ($key === 'backup') {
    $filename = 'sistema_backup.dat';
    $content  = $backup;
} elseif ($key === 'msg') {
    $filename = 'msg_cifrato.b64';
    $content  = base64_encode($msg);
} elseif ($key === 'coord') {
    $filename = 'coordinate.enc';
    $content  = str_rot13($coord);
} else {
    exit('Errore: parametro non valido [' . htmlspecialchars($key) . ']');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($content));
header('Cache-Control: no-store, no-cache');
echo $content;
exit;
