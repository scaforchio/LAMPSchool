<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2022 Vittorio Lo Mele
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

/* Programma per la modifica dei bindings tra utenti locali ed oidc */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

if ($_SESSION["tipoutente"] != "M") {
    http_response_code(401);
    die("Unauthorized");
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    http_response_code(500);
    die("Connessione al database fallita");
}

$uid = $con->real_escape_string($_POST["uid"]);
$oidc_uid = $con->real_escape_string($_POST["oidc_uid"]);
$oidc_am = $con->real_escape_string($_POST["oidc_am"]);

if($oidc_am != "d" && $oidc_am != "e" && $oidc_am != "x") {
    http_response_code(500);
    die("Parametri invalidi");
}

if(($oidc_am == "e" || $oidc_am == "x") && strlen($oidc_uid) < 36){
    http_response_code(500);
    die("Parametri invalidi");
}

$query = "UPDATE `tbl_utenti` SET `oidc_uid` = '$oidc_uid', `oidc_authmode` = '$oidc_am' WHERE `userid` = '$uid'";
$res = eseguiQuery($con, $query);

if(mysqli_errno($con) != 0){
    http_response_code(500);
    die("Errore del database: ". mysqli_errno($con));
}