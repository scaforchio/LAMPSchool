<?php
// enable all error reporting
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

function error($msg, $code) {
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

if (!isset($reqbody['suffisso']) || !isset($reqbody['username']) || !isset($reqbody['password'])) {
    error("Parametri mancanti", 400);
}

$suff = $reqbody['suffisso'];

require_once '../php-ini' . $reqbody['suffisso'] . '.php';

try {
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
} catch (Exception $e) {
    error("Errore di connessione al database (1)", 500);
}

if (!$con) {
    error("Errore di connessione al database (2)", 500);
}

require_once '../lib/funzioni.php';
require "../lib/req_assegna_parametri_a_sessione.php";

$username = mysqli_real_escape_string($con, $reqbody['username']);
$password = mysqli_real_escape_string($con, $reqbody['password']);

$result = eseguiQuery($con, "select * from tbl_utenti where userid='$username' and  password=md5(md5('$password'))");
$sessione = array();

if (!$val = mysqli_fetch_array($result))  // ALUNNO NON TROVATO
{
    inserisci_log($username . "§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Tentato accesso da app multipiattaforma", $_SESSION['nomefilelog'] . "ap", $suff);
    error("Credenziali non valide", 401);
} else
{
    if (((time() - $val['ultimoaccessoapp']) > 60) | ($sorgente != 2))
    //if (true)// RICHIESTA OK
    {
        inserisci_log($username . "§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§TIME " . time() . " ULTIMO " . $val['ultimoaccessoapp'], $_SESSION['nomefilelog'] . "ap", $suff);

        $sessione['idutente'] = $val['idutente'];
        if ($sessione['idutente'] > 2100000000)
            $sessione['alunno'] = $sessione['idutente'] - 2100000000;
        else
            $sessione['alunno'] = $sessione['idutente'];
        
        // AGGIORNO ULTIMO ACCESSO
        $sql = "UPDATE tbl_utenti SET ultimoaccessoapp=" . time() . " where idutente=" . $sessione['idutente'];
        eseguiQuery($con, $sql);
        inserisci_log($username . "§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Aggiornato ultimo accesso ", $_SESSION['nomefilelog'] . "ap", $suff);

        $sql = "SELECT * FROM tbl_alunni WHERE idalunno='" . $sessione['alunno'] . "'";
        $ris2 = eseguiQuery($con, $sql);

        if ($val2 = mysqli_fetch_array($ris2))
        {
            $classe = eseguiQuery($con, "select * from tbl_classi where idclasse=" . $val2['idclasse'])->fetch_assoc();

            if (!$classe || !$val2) {
                error("Alunno non trovato", 404);
            }

            $sessione['nome'] = $val2['nome'];
            $sessione['cognome'] = $val2['cognome'];
            $sessione['classe'] = $classe['anno'] . $classe['sezione'] . " " . $classe['specializzazione'];
            $sessione['datanascita'] = $val2['datanascita'];
            $sessione['suffisso'] = $suff;
            //$sessione['token_decifrato'] = $suff.",".$sessione['alunno'].",".$_SESSION['chiaveuniversale'];
            $sessione['token'] = md5($suff.",".$sessione['alunno'].",".$_SESSION['chiaveuniversale']);
        }

        header('Content-Type: application/json');
        echo json_encode($sessione);
        exit();
    } else   // RICHIESTA DEGLI STESSI DATI EFFETTUATA PRIMA DI UN MINUTO
    {
        inserisci_log($username . "§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Tempo basso ", $_SESSION['nomefilelog'] . "ap", $suff);
        error("Richiesta troppo frequente", 429);
    }
}

