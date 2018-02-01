<?php  //session_start();
/**
 * File di configurazione di LAMPSchool
 * 
 * @copyright  Copyright (C) 2015 Pietro Tamburrano
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */

if (!isset($_SERVER['HTTP_HOST'])) {
    exit('Questo script va eseguito solo tramite browser.');
}
date_default_timezone_set("Europe/Rome");
// VARIABILI DEL DATABASE
$db_server = "{DBHOST}";
$db_nome = "{DBNAME}";
$db_user = "{DBUSER}";
$db_password = "{DBPWD}";
$prefisso_tabelle = "{DBPREFIX}";

// Caricamento parametri
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Impossibile connettersi!");
$sql = "SET GLOBAL sql_mode = 'NO_ENGINE_SUBSTITUTION'";
mysqli_query($con,$sql);

$sql = "SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'";
mysqli_query($con,$sql);
$sql = "SELECT parametro,valore FROM ". $prefisso_tabelle. "tbl_parametri where parametro<>'versione'";
$result = mysqli_query($con, $sql) or die (mysqli_error($con));
$variabili="";
while ($rec = mysqli_fetch_array($result))
   $variabili = $variabili. "&". $rec['parametro']. "=". $rec['valore'];
parse_str($variabili);

mysqli_close($con); 

