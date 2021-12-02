<?php

session_start();

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
//@require_once("../lib/sms/php-send.php");
/*
  Copyright (C) 2015 Pietro Tamburrano
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

/* Programma per il controllo dell'accesso. */


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore connessione");

if (!$con) {
    die("<h1> Connessione al server fallita </h1>");
}

// Passaggio dei parametri nella sessione
//require "../lib/req_assegna_parametri_a_sessione.php";
//print ("Controllo sessione:".$_SESSION['idutente']);
//die();
/*
if (isset($_SESSION['idutente'])) {
    //session_destroy();

    die("<center><br><br><b>Già aperta sessione in altra scheda!<br><br>Se non ci sono altre schede attive chiudere e riaprire il browser.");
}
*/

$query = "select idseed from tbl_seed ORDER BY idseed DESC LIMIT 1";
$ris = eseguiQuery($con, $query);
$rec = mysqli_fetch_array($ris);
$ultimoseed = $rec['idseed'];
$primoseed = $ultimoseed - 30;
$query = "delete from tbl_seed where idseed<$primoseed";
eseguiQuery($con, $query);

$indirizzoip = IndirizzoIpReale();

$_SESSION['indirizzoip'] = $indirizzoip;

$_SESSION['ultimoaccesso'] = "";

//  $_SESSION['versione']=$versione;
//Connessione al server SQL


$username = stringa_html('utente');
$password = stringa_html('password');

// VERIFICO SE IP VIENE DA TOR

$query = "select * from tbl_torlist where indirizzo LIKE '$indirizzoip%'";
$ris = eseguiQuery($con, $query);
if (mysqli_num_rows($ris) > 0) {
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . $indirizzoip . "§Bloccato Accesso TOR: $username - $password§" . $_SERVER['HTTP_USER_AGENT']);
    header("location: login.php?messaggio=Utente sconosciuto&suffisso=" . $_SESSION['suffisso']);
    die;
}



$tipoaccesso = controlla_password($con, $password, $username, $_SESSION['chiaveuniversale'], $_SESSION['passwordesame']);

// print "Tipo accesso: $tipoaccesso";


if ($tipoaccesso == 0) {
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§Accesso errato: $username - $password");
    header("location: login.php?messaggio=Utente sconosciuto&suffisso=" . $_SESSION['suffisso']);
    die();
}

if ($tipoaccesso == 2) {
    $accessouniversale = true;
    $_SESSION['accessouniversale'] = true;
}

if ($tipoaccesso == 3) {// die("Sono qui!");
    $_SESSION['tipoutente'] = 'E';
    $_SESSION['userid'] = 'ESAMI';
    $_SESSION['idutente'] = 'esamedistato';

    $_SESSION['cognome'] = "Esame ";
    $_SESSION['nome'] = "di stato";

    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§Accesso ESAMI");
}

$sql = "SELECT *,unix_timestamp(ultimamodifica) AS ultmod FROM tbl_utenti WHERE userid='" . $username . "'";
$result = eseguiQuery($con, $sql);

if ($tipoaccesso == 1 | $tipoaccesso == 2) {  // UTENTE TROVATO
    $data = mysqli_fetch_array($result);
    $_SESSION['userid'] = $data['userid'];
    $_SESSION['tipoutente'] = $data['tipo'];
    $_SESSION['sostegno'] = docente_sostegno($data['idutente'], $con);
    $_SESSION['idutente'] = $data['idutente'];
    $_SESSION['dischpwd'] = $data['dischpwd'];
    // DATI TOKEN
    $_SESSION['modoinviotoken'] = $data['modoinviotoken'];
    $_SESSION['schematoken'] = $data['schematoken'];

    //$passdb = $data['password'];  // TTTT per controllo iniziale alunni
    // print "Data: $dataultimamodifica - Ora: $dataodierna";
    // print "Diff: $giornidiff";




    if ($_SESSION['tipoutente'] == 'T') {
        //  $sql = "SELECT * FROM tbl_tutori WHERE idutente='" . $_SESSION['idutente'] . "'";
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

    if ($_SESSION['tipoutente'] == 'M') {
        // $idscuola = md5($_SESSION['nomefilelog']);
        // print "<iframe style='visibility:hidden;display:none' src='http://www.lampschool.net/test/testesist.php?ids=$idscuola&nos=$_SESSION['nome_scuola']&cos=$_SESSION['comune_scuola']&ver=$_SESSION['versioneprecedente']&asc=$_SESSION['annoscol']'></iframe>";
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
    // Inserimento dell'accesso in tabella
    // $indirizzoip = IndirizzoIpReale();
    // $_SESSION['indirizzoip'] = $indirizzoip;

    if ($tipoaccesso == 1) {
        $sql = "INSERT INTO " . $_SESSION["prefisso"] . "tbl_logacc( utente , dataacc, comando,indirizzo) values('$username','" . date('Y/m/d - H:i') . "','Accesso','$indirizzoip')";
        eseguiQuery($con, $sql, false);
    }
}


//mysqli_close($con);
//header("location: ele_ges.php?suffisso=" . $_SESSION['suffisso']);
//if ($controllootp)
//if (true)

if (!$accessouniversale & ($_SESSION['modoinviotoken'] == 'S' | $_SESSION['modoinviotoken'] == 'M' | $_SESSION['modoinviotoken'] == 'T' | $_SESSION['modoinviotoken'] == 'G')) {
    $_SESSION['tentativiotp'] = 0;
    $token = rand(10000, 99999);
    $query = "update tbl_utenti set token=$token where idutente=" . $_SESSION['idutente'];
    eseguiQuery($con, $query);

    if ($_SESSION['modoinviotoken'] == "M" & ($_SESSION['tipoutente'] == 'D' | $_SESSION['tipoutente'] == 'S' | $_SESSION['tipoutente'] == 'P' )) {

        $mail = estrai_mail_docente($_SESSION['idutente'], $con);

        invia_mail($mail, "OTP: $token", "OTP per l'accesso a LampSchool: $token", $_SESSION['indirizzomailfrom']);
    }
    if ($_SESSION['modoinviotoken'] == "S" & ($_SESSION['tipoutente'] == 'D' | $_SESSION['tipoutente'] == 'S' | $_SESSION['tipoutente'] == 'P' )) {

        $cell = estrai_cell_docente($_SESSION['idutente'], $con);
        $destinatario = array();
        $destinatario[] = "39" . trim($cell);
        $result = skebbyGatewaySendSMS($_SESSION['utentesms'], $_SESSION['passsms'], $destinatario, "OTP per l'accesso a LampSchool: $token", SMS_TYPE_CLASSIC_PLUS, '', $_SESSION['testatasms'], $_SESSION['suffisso']);
    }
    if ($_SESSION['modoinviotoken'] == "G" & ($_SESSION['tipoutente'] == 'D' | $_SESSION['tipoutente'] == 'S' | $_SESSION['tipoutente'] == 'P' )) {
        $query = "select idtelegram from tbl_utenti where idutente=" . $_SESSION['idutente'];
        $ris = eseguiQuery($con, $query);
        $rec = mysqli_fetch_array($ris, MYSQLI_BOTH);
        $chat_id = $rec[0]; //ID Chat telegram
        sendTelegramMessageToken($chat_id, "Codice login: " . $token, $_SESSION['tokenbototp']);
    }
    header("location: otpcheck.php?suffisso=" . $_SESSION['suffisso']);
} else {
    //print "qui ".$_SESSION['modoinviotoken']."-";die;
    $_SESSION['tokenok'] = true;
    header("location: ele_ges.php?suffisso=" . $_SESSION['suffisso']);
}

