<?php

session_start();
@require_once("../lib/funzioni.php");
$suffisso = stringa_html('suffisso');
@require_once("../php-ini" . $suffisso . ".php");

if ($suffisso != "")
{
    $suff = $suffisso . "/";
} else
    $suff = "";

$indirizzoip = IndirizzoIpReale();



$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$query = "SELECT * FROM tbl_entrateclassi,tbl_classi
          WHERE tbl_entrateclassi.idclasse=tbl_classi.idclasse
          and data>='" . date('Y-m-d') . "' ORDER BY data,ora";
$ris = eseguiQuery($con, $query);

inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$indirizzoip §Richiesto elenco classi con entrata posticipata", $_SESSION['nomefilelog'] . "rp", $suff);


while ($rec = mysqli_fetch_array($ris))
{
    $classe = substr($rec['anno'], 0, 1) . substr($rec['sezione'], 0, 1) . substr($rec['specializzazione'], 0, 1);
    print "cl1=" . substr($rec['anno'], 0, 1) . substr($rec['sezione'], 0, 1) . substr($rec['specializzazione'], 0, 1) . "&";
    print "ocl1=" . substr($rec['ora'], 0, 2) . "&";
    print "mcl1=" . substr($rec['ora'], 3, 2) . "&";
    print "gcl1=" . substr($rec['data'], 8, 2) . "&";
    print "mcl1=" . substr($rec['data'], 5, 2) . "&";
    print "acl1=" . substr($rec['data'], 2, 2) . "&";
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$indirizzoip §Inviata classe $classe", $_SESSION['nomefilelog'] . "rp", $suff);
}
print "cl1=fine";


mysqli_close($con);



