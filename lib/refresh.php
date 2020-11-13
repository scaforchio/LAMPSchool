<?php

session_start();
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
if ($_SESSION['tempotrascorso'] >= ($_SESSION['tempomassimosessione']-5))
{   
    if (isset($_SESSION['tempotrascorso']))
    inserisci_log("Sessione scaduta dopo ".$_SESSION['tempotrascorso']. " minuti per utente ".$_SESSION['idutente']);
    session_unset();
    session_destroy();
    die();
}
$_SESSION['tempotrascorso'] = $_SESSION['tempotrascorso'] + 5;


