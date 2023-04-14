<?php
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

// inzializza sessione
if(!$_SESSION["oidc-step1"]){
    //pulisci
    session_unset();
    session_destroy();
    session_start();
    // ricarica dati
    require "../lib/req_assegna_parametri_a_sessione.php";
    $_SESSION["prefisso"] = $prefisso_tabelle;
    $_SESSION["suffisso"] = $suffisso;;
    $_SESSION["alias"] = false;
    $_SESSION["oidc-step1"] = true;
    $json = leggeFileJSON('../lampschool.json');
    $_SESSION['versione'] = $json['versione'];
}

if(!$_SESSION["oidc-step2"]){
    require "../lib/oidc/OpenIDConnectClient.php";
    $oidc = new Jumbojett\OpenIDConnectClient($_SESSION["oidc_issuer"], $_SESSION["oidc_client_id"], $_SESSION["oidc_client_secret"]);
    $oidc->setRedirectURL($_SESSION["oidc_redirect_uri"]. "/login/oidclogin.php?suffisso=". $suffisso);
    $oidc->authenticate();
    $_SESSION["oidc_accesstoken"] = $oidc->getAccessToken();
    $_SESSION["oidc_idtoken"] = $oidc->getIdToken();
    $atp = (array) $oidc->getAccessTokenPayload();
    $oidc_verified_uid = $atp["sub"]; // ottieni UUID account
    $_SESSION["oidc-step2"] = true;
    $_SESSION["oidc_verified_uuid"] = $oidc_verified_uid;
    header("Location: oidclogin.php?suffisso=" . $suffisso);
}else{
    require "../lib/oidc/OpenIDConnectClient.php";
    $oidc = new Jumbojett\OpenIDConnectClient($_SESSION["oidc_issuer"], $_SESSION["oidc_client_id"], $_SESSION["oidc_client_secret"]);
    $oidc->setAccessToken($_SESSION["oidc_accesstoken"]);
    $atp = (array) $oidc->getAccessTokenPayload();
    $oidc_verified_uid = $atp["sub"]; // ottieni UUID account
    $_SESSION["oidc-step2"] = true;
    $_SESSION["oidc_verified_uuid"] = $oidc_verified_uid;
    $_SESSION["oidc_fullname"] = $atp["name"];
}

// blocca accesso da tor
$query = "select * from tbl_torlist where indirizzo LIKE '$indirizzoip%'";
$ris = eseguiQuery($con, $query);
if (mysqli_num_rows($ris) > 0) {
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . $indirizzoip . "§Bloccato Accesso TOR: $username - $password§" . $_SERVER['HTTP_USER_AGENT']);
    header("location: login.php?messaggio=Utente sconosciuto&suffisso=" . $_SESSION['suffisso']);
    die;
}

// ricerca utenti che sono collegati all'UUID openid
$query = "SELECT * FROM `tbl_utenti` WHERE (`oidc_authmode` = 'x' OR `oidc_authmode` = 'e') AND `oidc_uid` LIKE '%$oidc_verified_uid%';";
$ris = eseguiQuery($con, $query);
$utenti_abilitati = array();

$count = 0;
$_SESSION["oidc_allowedlogins"] = array();
while ($i = mysqli_fetch_array($ris, MYSQLI_ASSOC)){
    array_push($utenti_abilitati, $i);
    array_push($_SESSION["oidc_allowedlogins"], $i['userid']);
    $count++;
}

if($count > 1){
    $_SESSION["oidc_multiprofile"] = true;
    $titolo = "Scegli profilo";
    stampa_head($titolo, "", $script, "", false);
    stampa_testata("Scelta del profilo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola'], true);
    ?>

    <center>
        <h3>Profili associati a: <u><?php echo $atp["name"] ?></u></h3>
        <table border="1">
            <tr class='prima'>
                <td>Nome utente</td>
                <td>Tipo profilo</td>
                <td>Accesso</td>
            </tr>
            <?php
            foreach ($utenti_abilitati as $utente) {
                print("<tr>");
                print("<td>" . $utente["userid"] . "</td>");
                print("<td>" . $tipiUtenti[$utente["tipo"]] . "</td>");
                print("<td><a href='oidclogincheck.php?suffisso=" . $_SESSION["suffisso"] . "&username=" . $utente["userid"] . "'>Accedi</a></td>");
                print("</tr>");
            }
            ?>
        </table>
    </center>
    <?php
    
}else{

    foreach ($utenti_abilitati as $utente) {
        header("Location: oidclogincheck.php?suffisso=" . $_SESSION["suffisso"] . "&username=" . $utente["userid"]);
        die;
    }

    $_SESSION["oidc_multiprofile"] = true;
    $titolo = "Nessun profilo associato";
    stampa_head($titolo, "", $script, "", false);
    stampa_testata("Scelta del profilo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola'], true);
    ?>
    
    <center>
        <h3>Nessun profilo risulta associato a questo account SSO!</h3>
        <h3>Si prega di eseguire il logout</h3>
    </center>
    
    <?php
    
}