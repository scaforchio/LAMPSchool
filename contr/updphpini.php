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

//require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();
// istruzioni per tornare alla pagina di login 


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$titolo = "AGGIORNAMENTO php-ini.php";
$script = "";
stampa_head($titolo, "", $script, "M");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

if (!is_writable(".."))
{
    print "<center>La cartella di destinazione non &egrave; scrivibile. Dare i permessi in scrittura alla cartella.</center>";
    $scrittura = false;
    die();
}

$fileIni = '../newphp-ini' . $_SESSION['suffisso'] . '.php';


//$db_server = $stringa_html('par_db_server');
//$db_nome = stringa_html('par_db_nome');
//$db_user = stringa_html('par_db_user');
//$db_password = stringa_html('par_db_password');
//$prefisso_tabelle = stringa_html('par_prefisso_tabelle');
// Preparazione del file newphp-ini.php che ha come modello il file php-ini.php
$str = file_get_contents('../install/php-ini.php');
$str = str_replace("{DBHOST}", "$db_server", $str);
$str = str_replace("{DBNAME}", "$db_nome", $str);
$str = str_replace("{DBUSER}", "$db_user", $str);
$str = str_replace("{DBPWD}", "$db_password", $str);
$str = str_replace("{DBPREFIX}", "$prefisso_tabelle", $str);
file_put_contents('../newphp-ini' . $_SESSION['suffisso'] . '.php', $str);

if (!unlink('../php-ini' . $_SESSION['suffisso'] . '.php.bkp'))
    print "<br>Vecchio file bkp non cancellato!";
else
    print "<br>Vecchio file bkp cancellato!";

if (!rename('../php-ini' . $_SESSION['suffisso'] . '.php', '../php-ini' . $_SESSION['suffisso'] . '.php.bkp'))
    print "<br>Nuovo file bkp non creato!";
else
    print "<br>Nuovo file bkp correttamente creato!";

if (!rename('../newphp-ini' . $_SESSION['suffisso'] . '.php', '../php-ini' . $_SESSION['suffisso'] . '.php'))
    print "<br>Nuovo file ini non creato!";
else
    print "<br>File <b>php-ini" . $_SESSION['suffisso'] . ".php</b> correttamente cambiato!";

stampa_piede();

