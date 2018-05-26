<?php
session_start();
@require_once("../lib/funzioni.php");
$suffisso = stringa_html('suffisso');
@require_once("../php-ini" . $suffisso . ".php");

if ($suffisso != "") {
    $suff = $suffisso . "/";
} else
    $suff = "";

$indirizzoip = IndirizzoIpReale();


$dataultagg= stringa_html('dataultagg');
$oraultagg= stringa_html('oraultagg');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));
// tttt
$query = "SELECT * FROM tbl_alunni, tbl_classi
          WHERE oraultimamodifica> '$dataultagg $oraultagg'
              AND tbl_alunni.idclasse=tbl_classi.idclasse
         ";

$ris = mysqli_query($con, inspref($query)) or die("errore query " . inspref($query));

inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$indirizzoip §Richiesto aggiornamento alunni da $dataultagg $oraultagg", $nomefilelog . "rp", $suff);


while ($rec = mysqli_fetch_array($ris))
{
    $stringa="";
    $stringa.= $rec['idalunno']."|";
    $stringa.=  $rec['cognome']."|";
    $stringa.=  $rec['nome']."|";
    
    $classe=substr($rec['anno'],0,1).substr($rec['sezione'],0,1).substr($rec['specializzazione'],0,1);
    $stringa.=  $classe."|";
    $stringa.=  "0|";
    $stringa.=  "08:10|08:10|08:10|08:10|08:10|08:10";
    $stringa.=  ";<br>";
   
inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$indirizzoip §Inviato alunno ".$rec['idalunno'], $nomefilelog . "rp", $suff);

}


$query = "SELECT * FROM tbl_alunni
          WHERE oraultimamodifica> '$dataultagg $oraultagg'
              AND (idclasse=0)
         ";

$ris = mysqli_query($con, inspref($query)) or die("errore query " . inspref($query));



while ($rec = mysqli_fetch_array($ris))
{
    $stringa="";
    $stringa.= $rec['idalunno']."|";
    $stringa.=  $rec['cognome']."|";
    $stringa.=  $rec['nome']."|";
    $stringa.=  "000|";
    $stringa.=  "0|";
    $stringa.=  "08:10|08:10|08:10|08:10|08:10|08:10";
    $stringa.=  ";<br>";
    print $stringa;
    
inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$indirizzoip §Inviato alunno ".$rec['idalunno'], $nomefilelog . "rp", $suff);

}
print "FINE";
mysqli_close($con);



