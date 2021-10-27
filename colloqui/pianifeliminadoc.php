<?php

require_once '../lib/req_apertura_sessione.php';
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
require_once("pianifunz.php");
$idcampodoc = stringa_html('iddoc');
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
$iddoc = substr($idcampodoc, 2, (strlen($idcampodoc) - 1));
$qdel = "DELETE FROM tbl_assocauledoc WHERE iddocente=$iddoc";
$rdel = eseguiQuery($con,$qdel);
stampaTabella($con);
?>
