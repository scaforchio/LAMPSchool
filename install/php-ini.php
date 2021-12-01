<?php

//session_start();
/**
 * File di configurazione di LAMPSchool
 * 
 * @copyright  Copyright (C) 2015 Pietro Tamburrano
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */
if (!isset($_SERVER['HTTP_HOST']))
{
    exit('Questo script va eseguito solo tramite browser.');
}
date_default_timezone_set("Europe/Rome");
// VARIABILI DEL DATABASE
$db_server = "{DBHOST}";
$db_nome = "{DBNAME}";
$db_user = "{DBUSER}";
$db_password = "{DBPWD}";
$prefisso_tabelle = "{DBPREFIX}";

include_once './lib/inc_inizializzazione.php';
include_once '../lib/inc_inizializzazione.php';