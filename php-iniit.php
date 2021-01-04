<?php

/**
 * File di configurazione di LAMPSchool
 * 
 * @copyright  Copyright (C) 2013 Pietro Tamburrano
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */
if (!isset($_SERVER['HTTP_HOST']))
{
    exit('Questo script va eseguito solo tramite browser.');
}

// VARIABILI DEL DATABASE
$db_server = "62.149.150.198";
$db_nome = "Sql691154_1";
$db_user = "Sql691154";
$db_password = "cclm6a4y2t";
$prefisso_tabelle = "it2020_";

// require_once lib/req_carica_parametri.php;
// Caricamento parametri
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

$sql = "SELECT parametro,valore FROM " . $prefisso_tabelle . "tbl_parametri where parametro<>'versione'";
$result = mysqli_query($con, $sql);

while ($rec = mysqli_fetch_array($result))
{
    $variabile = $rec['parametro'];
    $valore=$rec['valore'];
    $$variabile=$valore;
}

mysqli_close($con);
