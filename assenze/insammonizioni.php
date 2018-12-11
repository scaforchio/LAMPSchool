<?php

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


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

$dataammoniz = date('Y-m-d');

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$datalimiteinferiore = giorno_lezione_passata(date('Y-m-d'), $maxritardogiust, $con);
$titolo = "Inserimento ammonizioni";
$script = "";
stampa_head($titolo, "", $script, "PMASD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");



$query = "SELECT idalunno,nome,cognome, sesso FROM tbl_alunni WHERE idclasse<>0";

$ris = eseguiQuery($con,$query);


while ($rec = mysqli_fetch_array($ris))
{
    $idalunno = $rec['idalunno'];
    $datialunno = $rec['cognome'] . " " . $rec['nome'];
    $sesso = $rec['sesso'];
    $strass = "ammass" . $idalunno;

    $aludaamm = stringa_html($strass) ? "on" : "off";
    if ($aludaamm == "on")
    {
        inserisciAmmonizioneGiustAssenze($idalunno, $_SESSION['idutente'], $datalimiteinferiore, $con);
        
    }


    $strass = "ammrit" . $idalunno;
    $aludaamm = stringa_html($strass) ? "on" : "off";
    if ($aludaamm == "on")
    {

        inserisciAmmonizioneGiustRitardi($idalunno, $_SESSION['idutente'], $datalimiteinferiore, $con);
        
    }
}

print "
        <form method='post' id='formass' action='../assenze/sitgiustifiche.php'>

        </form>
        <SCRIPT language='JavaScript'>
           document.getElementById('formass').submit();
        </SCRIPT>";


stampa_piede("");
mysqli_close($con);

