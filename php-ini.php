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
$db_server = "localhost";
$db_nome = "ls2017";
$db_user = "root";
$db_password = "passroot";
$prefisso_tabelle = "itt_";


include_once './lib/inc_inizializzazione.php';
include_once '../lib/inc_inizializzazione.php';
