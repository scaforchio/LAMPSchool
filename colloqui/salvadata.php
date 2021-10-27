<?php

require_once '../lib/req_apertura_sessione.php';

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$data = stringa_html('data');
$oraInizio = stringa_html('oraInizio');
$oraFine = stringa_html('oraFine');
$durataSlot = stringa_html('durata');


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
$query = "select * from tbl_giornatacolloqui where data='$data'";
$ris = eseguiQuery($con, $query);
if (mysqli_num_rows($ris) == 0)
{
    $query = "INSERT INTO tbl_giornatacolloqui (data,orainizio,orafine,durataslot)
			VALUES('$data', '$oraInizio', '$oraFine', '$durataSlot')";

    eseguiQuery($con, $query);
    $idgiornata = mysqli_insert_id($con);


    $query = "select idclasse from tbl_classi";
    $ris = eseguiQuery($con, $query);
    while ($rec = mysqli_fetch_array($ris))
    {
        $idclasse = $rec['idclasse'];
        $query = "insert into tbl_colloquiclasse(idclasse,idgiornatacolloqui)"
                . " values ($idclasse,$idgiornata)";
        eseguiQuery($con, $query);
    }
}
header("location: ./insgiornatecoll.php");
mysqli_close($con);


