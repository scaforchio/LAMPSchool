<?php
/*
  Copyright (C) 2015 Pietro Tamburrano
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

/* Programma per il login al registro tramite OIDC. */

session_start();

if (isset($_GET['suffisso']))
    $suffisso = $_GET['suffisso'];
else
    $suffisso = "";
//require_once '../php-ini.php';
require_once "../php-ini" . $suffisso . ".php";
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
require_once '../lib/funzioni.php';

$tipiUtenti = array(
    "D" => "Docente",
    "P" => "Preside",
    "S" => "Staff",
    "A" => "Amministrativo",
    "L" => "Alunno",
    "T" => "Tutore",
    "M" => "Amministratore",
    "E" => "Esami di stato"
);

if (!isset($_SESSION["oidc-step2"]) || $_SESSION["oidc-step2"] != true) {
    // senza sessione valida non loggare utente
    header("location: login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$username = $con->real_escape_string($_GET["username"]);

// verifica che la richiesta di creazione della sessione sia autorizzata
if(!in_array($username, $_SESSION["oidc_allowedlogins"])){
    // senza sessione valida non loggare utente
    header("location: login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$query = "SELECT * FROM `tbl_utenti` WHERE `userid` = '$username'";
$result = eseguiQuery($con, $query);

$data = mysqli_fetch_array($result);
$_SESSION['userid'] = $data['userid'];
$_SESSION['tipoutente'] = $data['tipo'];
$_SESSION['sostegno'] = docente_sostegno($data['idutente'], $con);
$_SESSION['idutente'] = $data['idutente'];
$_SESSION['dischpwd'] = $data['dischpwd'];
// DATI TOKEN
$_SESSION['modoinviotoken'] = $data['modoinviotoken'];
$_SESSION['schematoken'] = $data['schematoken'];

if ($_SESSION['tipoutente'] == 'T') {
    $sql = "SELECT * FROM tbl_alunni WHERE idalunno='" . $_SESSION['idutente'] . "'";
    $ris = eseguiQuery($con, $sql);

    if ($val = mysqli_fetch_array($ris)) {
        $_SESSION['idstudente'] = $val["idalunno"];
        $_SESSION['cognome'] = $val["cognome"];
        $_SESSION['nome'] = $val["nome"];
    }
}

if ($_SESSION['tipoutente'] == 'L') {
    //print "PASSDB: $passdb";
    //  $sql = "SELECT * FROM tbl_tutori WHERE idutente='" . $_SESSION['idutente'] . "'";
    $sql = "SELECT * FROM tbl_alunni WHERE idalunno='" . ($_SESSION['idutente'] - 2100000000) . "'";

    $ris = eseguiQuery($con, $sql);

    if ($val = mysqli_fetch_array($ris)) {
        $_SESSION['idstudente'] = $val["idalunno"];
        $_SESSION['cognome'] = $val["cognome"];
        $_SESSION['nome'] = $val["nome"];
        $_SESSION['codfiscale'] = $val['codfiscale'];
    }
}

if ($_SESSION['tipoutente'] == 'D' | $_SESSION['tipoutente'] == 'S' | $_SESSION['tipoutente'] == 'P') {
    $sql = "SELECT * FROM tbl_docenti WHERE idutente='" . $_SESSION['idutente'] . "'";
    $ris = eseguiQuery($con, $sql);

    if ($val = mysqli_fetch_array($ris)) {
        $_SESSION['cognome'] = $val["cognome"];
        $_SESSION['nome'] = $val["nome"];
    }
    // VERIFICO SE C'E' UNA DEROGA PER IL LIMITE DI INSERIMENTO
    $sql = "SELECT * FROM tbl_derogheinserimento WHERE iddocente='" . $_SESSION['idutente'] . "' AND DATA='" . date('Y-m-d') . "'";
    $ris = eseguiQuery($con, $sql);

    if (mysqli_num_rows($ris) > 0) {
        $_SESSION['derogalimite'] = true;
    } else {
        $_SESSION['derogalimite'] = false;
    }
}

if ($_SESSION['tipoutente'] == 'A') {
    $sql = "SELECT * FROM tbl_amministrativi WHERE idutente='" . $_SESSION['idutente'] . "'";
    $ris = eseguiQuery($con, $sql);

    if ($val = mysqli_fetch_array($ris)) {
        $_SESSION['cognome'] = $val["cognome"];
        $_SESSION['nome'] = $val["nome"];
    }
}

if ($_SESSION['tipoutente'] == "S" | $_SESSION['tipoutente'] == "D") {
    $_SESSION['cattsost'] = cattedre_sostegno($_SESSION['idutente'], $con);
    $_SESSION['cattnorm'] = cattedre_normali($_SESSION['idutente'], $con);
}


//
//  AZIONI PRIMO ACCESSO DELLA GIORNATA
//
if ($_SESSION['modocron'] == "acc") {
    $query = "SELECT dataacc FROM tbl_logacc
                   WHERE idlog = (SELECT max(idlog) FROM tbl_logacc)";
    $ris = eseguiQuery($con, $query);
    $rec = mysqli_fetch_array($ris);
    $dataultimoaccesso = $rec['dataacc'];
    $dataultimo = substr($dataultimoaccesso, 0, 10);
    //print $dataultimo;
    $dataoggi = date("Y/m/d");
    //print $dataoggi;
    if ($dataoggi > $dataultimo) {
        daily_cron($_SESSION['suffisso'], $con, '110100');
    }
}
//
//  FINE AZIONI PRIMO ACCESSO DELLA GIORNATA
//


// Inserimento nel log dell'accesso
if ($_SESSION['suffisso'] != "") {
    $suff = $_SESSION['suffisso'] . "/";
} else
    $suff = "";
inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Accesso: $username - $password§" . $_SERVER['HTTP_USER_AGENT']);

// Ricerca ultimo accesso
$query = "select dataacc from " . $_SESSION["prefisso"] . "tbl_logacc where idlog=(select max(idlog) from " . $_SESSION["prefisso"] . "tbl_logacc where utente='$username' and comando='Accesso')";
$ris = eseguiQuery($con, $query, false);
if (mysqli_num_rows($ris) == 0) {
    $_SESSION['ultimoaccesso'] = "";
} else {
    $rec = mysqli_fetch_array($ris);
    $ultimoaccesso = $rec['dataacc'];
    $dataultaccute = substr($ultimoaccesso, 0, 10);
    $oraultaccute = substr($ultimoaccesso, 13, 5);
    $giornoultaccute = giorno_settimana($dataultaccute);
    $_SESSION['ultimoaccesso'] = $giornoultaccute . " " . data_italiana($dataultaccute) . " h. " . $oraultaccute;
}

$sql = "INSERT INTO " . $_SESSION["prefisso"] . "tbl_logacc( utente , dataacc, comando,indirizzo) values('$username','" . date('Y/m/d - H:i') . "','Accesso','$indirizzoip')";
eseguiQuery($con, $sql, false);

// Conferma ultimo step OIDC e fai il redirect alla home del registro
$_SESSION["oidc-step3"] = true;
$_SESSION["tokenok"] = true; // il token non serve siccome della 2fa se ne occupa il provider oidc
header("Location: ele_ges.php?suffisso=". $_SESSION['suffisso']);