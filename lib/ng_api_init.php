<?php
/*
  Copyright (C) 2024 Vittorio Lo Mele
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


function json_response($status, $data){
    http_response_code($status);
    header("Content-Type: application/json");
    $response = array(
        'status' => $status == 200 ? 'success' : 'error', 
        'data' => $data
    );
    die(json_encode($response));
}

function die_rc($code) {
    http_response_code($code);
    die();
}

function error_response($code, $error) {
    global $NG_API_JSON;
    if ($NG_API_JSON) {
        json_response($code, $error);
    } else {
        die_rc($code);
    }
}

session_start();
if (!isset($_SESSION['prefisso']))
{
    error_response(400, "Sessione invalida");
}

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

$tipoutente = $_SESSION["tipoutente"];
if ($tipoutente == "") {
    error_response(401, "Non autorizzato");
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    error_response(500, "Connessione server fallita");
}

$_SESSION['tempotrascorso'] = 0;

if(!strstr($NG_API_ABIL, $tipoutente) && $NG_API_ABIL != "SKIP") {
    error_response(403, "Ruolo non autorizzato");
}