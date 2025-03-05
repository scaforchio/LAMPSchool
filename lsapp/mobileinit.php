<?php function error($msg, $code) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode(array('error' => $msg));
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== "POST") {
    error("Metodo non consentito", 405);
}

try {
    $reqbody = json_decode(file_get_contents('php://input'), true);
} catch (Exception $e) {
    error("Body non valido", 400);
}

if (!isset($reqbody['suffisso']) || !isset($reqbody['alunno']) || !isset($reqbody['token']) || !isset($reqbody['idutente'])) {
    error("Parametri mancanti", 400);
}

$suff = $reqbody['suffisso'];
$idutente = $reqbody['idutente'];

require_once '../php-ini' . $reqbody['suffisso'] . '.php';

try {
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
} catch (Exception $e) {
    error("Errore di connessione al database (1)", 500);
}

if (!$con) {
    error("Errore di connessione al database (2)", 500);
}

$alunno = mysqli_real_escape_string($con, $reqbody['alunno']);
$token = mysqli_real_escape_string($con, $reqbody['token']);

require_once '../lib/funzioni.php';
require "../lib/req_assegna_parametri_a_sessione.php";

// verifica token
$token_ok = md5($suff.",".$reqbody['alunno'].",".$_SESSION['chiaveuniversale']);
if ($token != $token_ok) {
    error("Token non valido", 401);
}

$idalunno = mysqli_real_escape_string($con, $reqbody['alunno']);
$objalunno = eseguiQuery($con, "select * from tbl_alunni where idalunno=$idalunno")->fetch_assoc();

if (!$objalunno) {
    error("Alunno non trovato", 404);
}