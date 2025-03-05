<?php
require_once 'mobileinit.php';

if (!isset($reqbody['idDocumento']) || !isset($reqbody['idCircolare'])) {
    error("Parametri mancanti", 400);
}

if (!is_numeric($reqbody['idDocumento']) || !is_numeric($reqbody['idCircolare'])) {
    error("Parametri non validi", 400);
}
$idc = mysqli_real_escape_string($con, $reqbody['idCircolare']);
$idd = mysqli_real_escape_string($con, $reqbody['idDocumento']);

$query = "select docbin, docnome, doctype,docmd5 from tbl_documenti where iddocumento = '$idd'";
$select = eseguiQuery($con, $query);
$result = mysqli_fetch_array($select);

if (!$result) {
    error("Documento non trovato", 404);
}

// segna circolare come letta
$dataoggi = data_to_db(date('d/m/Y'));				
$querylett = "update tbl_diffusionecircolari
				set datalettura='$dataoggi'
				where idcircolare=$idc
				and idutente=$idutente
				and (isnull(datalettura) or datalettura<'2000-01-01')";
eseguiQuery($con, $querylett);

$data = $result["docbin"];
$name = $result["docnome"];
$type = $result["doctype"];
$hashmd5 = $result['docmd5'];

if (strlen($data) > 0)  // Il documento è nel database altrimenti è su disco
{
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-Type: $type");
    header("Content-Disposition: inline; filename=" . $name);
    echo $data;
} else {
    $origine = "../lampschooldata/$suff/$hashmd5";

    // verifica file origine esista
    if (!file_exists($origine)) {
        error("File non trovato", 404);
    }

    $destinazione = $_SESSION['cartellabuffer'] . "/$name";
    copy($origine, $destinazione);
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-Type: $type");
    header("Content-Disposition: inline; filename=" . $name);
    readfile($destinazione);
}
