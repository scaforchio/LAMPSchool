<?php

require_once '../lib/req_apertura_sessione.php';
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
require_once("pianifunz.php");
$idcampoaula = $_POST['idaula'];
$idcampodoc = $_POST['iddoc'];

$idaula = substr($idcampoaula, 2, (strlen($idcampoaula) - 1));
$iddoc = substr($idcampodoc, 2, (strlen($idcampodoc) - 1));
//CONTROLLO NUMERO MASSIMO DOCENTI PER AULA
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
$query = "SELECT capienza from tbl_aule where idaula=$idaula";
$ris = eseguiQuery($con, $query);
$rec = mysqli_fetch_array($ris);
$capienza = $rec['capienza'];
$qas = "SELECT * FROM tbl_assocauledoc WHERE idaula=$idaula";
$ras = eseguiQuery($con, $qas);
if (mysqli_num_rows($ras) < $capienza)
{
    $qas2 = "SELECT * FROM tbl_assocauledoc WHERE iddocente=$iddoc";
    $ras2 = eseguiQuery($con, $qas2);
    if (mysqli_num_rows($ras2) == 0)
    {
        $qasIN = "INSERT INTO tbl_assocauledoc
					(iddocente,idaula) VALUES ($iddoc,$idaula)";
        $rasIN = eseguiQuery($con, $qasIN);
    } else
    {
        $qasUP = "UPDATE tbl_assocauledoc
					  SET idaula=$idaula
					  WHERE iddocente=$iddoc";
        $rasUP = eseguiQuery($con, $qasUP);
    }
}
stampaTabella($con);

