<?php

print "1";
session_start();


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


if (isset($_GET['suffisso']))
    $suffisso = $_GET['suffisso'];
else
    $suffisso = "";

@require_once("../php-ini" . $suffisso . ".php");
@require_once("../lib/funzioni.php");

$utente = stringa_html('utente');
$newpass1 = stringa_html('newpass1');
$newpass2 = stringa_html('newpass2');
$token = stringa_html('token');


$_SESSION["prefisso"] = $prefisso_tabelle;
$_SESSION["annoscol"] = $annoscol;
$_SESSION["suffisso"] = $suffisso;
$_SESSION["versioneprecedente"] = $versioneprecedente;
$_SESSION["nomefilelog"] = $nomefilelog;
$_SESSION["alias"] = false;


$json = leggeFileJSON('../lampschool.json');
$_SESSION['versione'] = $json['versione'];

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore: " . mysqli_error($con));

$query = "select * from tbl_utenti
                  where userid='$utente'";
$ris = eseguiQuery($con, $query);
$rec = mysqli_fetch_array($ris);
$oraregistrazionetoken = $rec['oracreazionetoken'];
$tokenregistrato = $rec['tokenresetpwd'];
$numeroutilizzi = $rec['numutilizzitoken'];
$data = new DateTime();
$tsnow = $data->getTimestamp();

if ($tsnow - $oraregistrazionetoken > 1800)
{
    header("location: richresetpwd.php?suffisso=$suffisso&messaggio=err2");
    die();
}
if ($token != $tokenregistrato)
{
    header("location: richresetpwd.php?suffisso=$suffisso&messaggio=err3");
    die();
}
if ($tokenregistrato=='0')
{
    header("location: richresetpwd.php?suffisso=$suffisso&messaggio=err4");
    die();
}
$query = "update tbl_utenti
        set password = md5(md5('$newpass1')),
            numutilizzitoken=0,
            tokenresetpwd='0'
        where userid='$utente'";

eseguiQuery($con, $query);

header("location: login.php?suffisso=$suffisso&messaggio=Password correttamente cambiata");

mysqli_close($con);
stampa_piede("");

