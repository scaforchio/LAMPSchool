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
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
//require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();
// istruzioni per tornare alla pagina di login 


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$titolo = "TEST";
$script = "";
stampa_head($titolo, "", $script, "M");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


print "Niente da testare!";

stampa_piede();

