<?php

session_start();
@require_once("../php-ini" . $_GET['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
@require_once("../lib/sms/php-send.php");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

@require "../lib/req_assegna_parametri_a_sessione.php";

$suffisso = stringa_html('suffisso');
$_SESSION['suffisso'] = $suffisso;


$file = "https://check.torproject.org/cgi-bin/TorBulkExitList.py?ip=1.1.1.1";

//$file = "https://www.dan.me.uk/torlist/?exit";
//$file = "https://www.lampschool.net/torlist.html";
$file_txt = file($file);
if (count($file_txt) > 100)
{
    $n = 0;
    $query = "delete from tbl_torlist where true";
    eseguiQuery($con, $query, true, false);
    
    foreach ($file_txt as $rec)
    {
        if (substr($rec, 0, 1) != "#")
        {
            $n++;
            $query = "insert into tbl_torlist(indirizzo) values ('$rec')";
            eseguiQuery($con, $query, true, false);
            print "$rec inserito <br>";
        }
    }
    print "Numero record $n";
}