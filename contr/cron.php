<?php
/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
GNU Affero General Public License come pubblicata
dalla Free Software Foundation; sia la versione 3,
sia (a vostra scelta) ogni versione successiva.

Questo programma è distribuito nella speranza che sia utile
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/
session_start();
@require_once("../php-ini" . $_GET['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
@require_once("../lib/sms/php-send.php");

$suffisso = stringa_html('suffisso');
$_SESSION['suffisso']=$suffisso;
$lavori = stringa_html('lavori'); // Avrà il formato: 101... in base ai lavori che devono o non devono essere lanciati
//// $lavori
// 0 - pulizia buffer
// 1 - eliminazione valutazioni anomale
// 2 - invio sms assenti

$controllo=stringa_html('controllo');

// In controllo ci deve essere una stringa contenente la codifica md5 del nome del file di log generato in fase di installazione

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

if ($controllo==md5($nomefilelog))
{
    daily_cron($suffisso, $con, $lavori, $nomefilelog);
    print "\nCron $lavori eseguito";
}
else
{
    print "Cron non eseguito per controllo MD5 fallito!";
}

mysqli_close($con);

