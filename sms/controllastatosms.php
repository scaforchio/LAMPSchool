<?php

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
GNU Affero General Public License come pubblicata 
dalla Free Software Foundation; sia la versione 3, 
sia (a vostra scelta) ogni versione successiva.

Questo programma é distribuito nella speranza che sia utile 
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di 
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE. 
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/

$suffisso = $_GET['user_reference'];
//print "<br>tttt $suffisso";
//inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§RICEZIONE SMS $suffisso", "0000SMS",$suffisso);

@require_once("../php-ini" . $suffisso . ".php");
@require_once("../lib/funzioni.php");
@require_once("../lib/sms/php-send.php");

$idspedizione = $_GET['skebby_dispatch_id'];
$cell = $_GET['recipient'];
$stato = $_GET['status'];
$errore = $_GET['error_code'];

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$query = "update tbl_sms set esito='$stato'
         where idinvio='$idspedizione'
         and celldestinatario='$cell'";
//print inspref($query);
mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));

mysqli_close($con);
