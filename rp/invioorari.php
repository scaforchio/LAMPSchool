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


inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$indirizzoip §Richiesto elenco orari campanella", $nomefilelog . "rp", $suff);

$orari = array();
$giorni = array();

for ($i = 1; $i <= $giornilezsett; $i++)
{
    $orari = array();

    $query = "SELECT * FROM tbl_orario
          WHERE giorno= $i and valido=1";
    $ris = eseguiQuery($con, $query);
    while ($rec = mysqli_fetch_array($ris))
    {
        
        if ($rec['ora'] == 1)
        {
            $orainizio = togli_cinque($rec['inizio']);
            
            $orari[] = $orainizio;
        }
        $ora = substr($rec['inizio'], 0, 5);
        if (!cerca_ora($orari, $ora))
            $orari[] = $ora;
        $ora = substr($rec['fine'], 0, 5);
        if (!cerca_ora($orari, $ora))
            $orari[] = $ora;
        
       
    }
    asort($orari);
    $giorni[] = $orari;
}
foreach ($giorni as $orario)
{
    foreach ($orario as $ora)
    {
        print $ora . " ";
        
    }
    print "<br>";
}

mysqli_close($con);

function cerca_ora($orari, $ora)
{
    foreach ($orari as $o)
    {
        if ($o == $ora)
            return true;
    }
    return false;
}
function togli_cinque($ora)
{
    $o=substr($ora,0,2);
    $m=substr($ora,3,2);
    $nm=$m-5;
    if ($nm<0)
    {
        $o=$o-1;
        $m=60+$nm;
    }
    else
        $m=$nm;
    if (strlen($m)==1)
        $m="0".$m;
    if (strlen($o)==1)
        $o="0".$o;
    return $o.":".$m;
}