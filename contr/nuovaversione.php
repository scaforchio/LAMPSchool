<?php

require_once '../lib/req_apertura_sessione.php';
/**
 * Elenco degli indici del database
 *
 * @copyright  Copyright (C) 2014 Renato Tamilio
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */
require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';


// istruzioni per tornare alla pagina di login 

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$titolo = "Controllo nuova versione LAMPSchool";
$script = "";
stampa_head($titolo, "", $script, "PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$risultato = controlloNuovaVersione();
$esito = $risultato['esito'];
$nuovaVersione = $risultato['versione'];

if ($esito)
{
    print "<center><h3>E' disponibile sul sito di LAMPSchool la versione $nuovaVersione</h3></center>";
} else
{
    print "<center><h3>Questa versione di LAMPSchool &egrave; la pi&ugrave; recente.</h3></center>";
}

print "<br/>";

stampa_piede("");

