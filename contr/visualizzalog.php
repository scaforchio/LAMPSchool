<?php

session_start();

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

/* Programma per la visualizzazione del menu principale. */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");


$suff = $_SESSION['suffisso'] . "/";
if ($suff == "/")
    $suff = "";
// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idesterno = "";
if ($tipoutente == "")
{
    header("location: login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "VISUALIZZAZIONE LOG";
$script = "";
$datalog = stringa_html('datalog');
if ($datalog == '')
{
    $data = date('Ymd');
    $datalog = date('Y-m-d');
}
else
    $data = substr($datalog, 0, 4) . substr($datalog, 5, 2) . substr($datalog, 8, 2);

$tipo= stringa_html('tipo');
if ($tipo=='WEB')
{
    $selweb='selected';
    $sufftipo='';
}
if ($tipo=='APP')
{
    $selapp='selected';
    $sufftipo='ap';
}
if ($tipo=='R.P.')
{
    $selrp='selected';
    $sufftipo='rp';
}
stampa_head($titolo, "", $script, "PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

/* if ($datalog=='')
  $filename="../lampschooldata/".$suff."0000$nomefilelog".date("Ymd").".log";
  else */

$filename = "../lampschooldata/" . $suff . "0000$nomefilelog" .$sufftipo. $data . ".log";
$handle = fopen($filename, "r");

print "<form name='sceglilog' method='post' action='visualizzalog.php'>";

print "<br><br><center><input type='date' name='datalog' value='$datalog' ONCHANGE=sceglilog.submit()><br>"
        . "  <select name='tipo' ONCHANGE=sceglilog.submit()><option $selweb>WEB</option><option $selapp>APP</option><option $selrp>R.P.</option></select></center>";
print "</form>";
// $contents = fread($handle, filesize($filename));
try
{
    $contents = file_get_contents($filename);
    print "<font size='1'><b><pre>" . $filename . "</pre></b></font>";
    print "<font size='1'><pre>" . $contents . "</pre></font>";
    fclose($handle);
} catch (Exception $e)
{
    print "<br><br><center>Non esiste file di log per la data specificata!";
}

stampa_piede("");
