<?php
/*
  Copyright (C) 2015 Pietro Tamburrano
  Copyright (C) 2022 Pietro Tamburrano, Vittorio Lo Mele
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

/* Programma per il login al registro. */

if (isset($_GET['suffisso']))
    $suffisso = $_GET['suffisso'];
else
    $suffisso = "";
//require_once '../php-ini.php';
require_once "../php-ini" . $suffisso . ".php";
require_once '../lib/funzioni.php';

// si pulisce tutto il contenuto della sessione 
// e si torna alla pagina di login

session_start();

// controlla se la sessione esistente è OIDC
if ($_SESSION["oidc-step2"]) {
    // effettua il logout anche sull'IDP OIDC
    require "../lib/oidc/OpenIDConnectClient.php";
    $oidc = new Jumbojett\OpenIDConnectClient($_SESSION["oidc_issuer"], $_SESSION["oidc_client_id"], $_SESSION["oidc_client_secret"]);
    $token = $_SESSION["oidc_idtoken"];
    $redi = $_SESSION["oidc_redirect_uri"];
    session_unset();
    session_destroy();
    session_start();
    $oidc->signOut($token, $redi);
}

session_unset();
session_destroy();
session_start();

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
require "../lib/req_assegna_parametri_a_sessione.php";
$_SESSION["prefisso"] = $prefisso_tabelle;
//$_SESSION["annoscol"] = $_SESSION['annoscol'];
$_SESSION["suffisso"] = $suffisso;

// controllo presenza file devmode
if (file_exists("../.devmode")) {
    $_SESSION["devmode"] = true;
}

//$_SESSION["versioneprecedente"]=$_SESSION['versioneprecedente'];
//$_SESSION["nomefilelog"] = $_SESSION['nomefilelog'];
$_SESSION["alias"] = false;

$json = leggeFileJSON('../lampschool.json');
$_SESSION['versione'] = $json['versione'];

if ($_SESSION["oidc_enabled"] == "exclusive") {
    header("Location: oidclogin.php");
}

$titolo = "Inserimento dati di accesso";
$seedcasuale = mt_rand(100000, 999999);
$seme = md5(date('Y-m-d') . $seedcasuale);

$script = "<script src='../lib/js/crypto.js'></script>\n";
$script .= "<script>

var isIE = /*@cc_on!@*/false || !!document.documentMode;
var isEdge = !isIE && !!window.StyleMedia;
if (isEdge)
    alert('L\'uso di Edge può portare ad anomalie nel funzionamento del registro! Usa Chrome, Firefox o Safari.');

function codifica()
{
    seme='$seme';
   
    document.getElementById('passwordmd5').value = hex_md5(hex_md5(hex_md5(document.getElementById('password').value))+seme);
    // document.getElementById('password').value = '';
    return true;
}
   

</script>\n";
stampa_head_new($titolo, "", $script, "", false);

$messaggio = stringa_html('messaggio');

$mex = "";

if (strlen($messaggio) > 0) {
    $mex = "<div class='alert alert-danger' role='alert'>";
    if ($messaggio == 'errore') {
        $mex .= 'Nome utente e/o password errati !';
    } else {
        $mex .= $messaggio;
    }
    $mex .= "</div>";
}

?>

<div class="container">
    <div class="row justify-content-center" style="margin-top: 8%; min-width: 100px">
        <div class="col col-md-auto">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="margin-bottom: 15px">
                        Benvenuto, Accedi
                    </h5>

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col col-auto">
                                    <img src="../favicons/icon.png" alt="icona registro" width="40" height="40" />
                                </div>

                                <div class="col" style="padding-left: 0px">
                                    <p class="lh-80 del-margin">
                                        <span class="fs-5"><?php echo $_SESSION['nome_scuola'] ?> </span>
                                        <br />
                                        <span class="fs-6 grey">A.S. <?php echo $_SESSION['annoscol']."/".$_SESSION['annoscol']+1 ?></span>
                                    </p>
                                </div>

                                <div class="col col-auto text-center">
                                    <button type="button" class="btn btn-outline-secondary" onclick="window.location.href = '../'">
                                        <i class="bi bi-arrow-left-right" style="margin-right: 0px"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-red">
                        <div class="card-body">
                            <form id='formLogin' action='logincheck.php' method='POST' onsubmit='return codifica();'>

                                <?php echo $mex; ?>

                                <div class="form-floating mb-3">
                                    <input type="username" class="form-control" name='utente' id='utente'>
                                    <label for="utente">Username</label>
                                </div>

                                <div class="form-floating">
                                    <input type="password" class="form-control" name='pass' id='password'>
                                    <label for="password">Password</label>
                                </div>

                                <div class="d-grid gap-2 pt-3">
                                    <input class="btn btn-outline-secondary" type="submit" name='OK' value='Accedi'>
                                </div>
                                <noscript>
                                    <input name='js_enabled' type='hidden' value='1'>
                                </noscript>
                                <input type='hidden' name='password' id='passwordmd5'>
                            </form>
                            <hr style="margin-bottom: 0 !important;">
                            <div class="d-grid gap-2 pt-3">
                            <?php if ($_SESSION["oidc_enabled"] == "yes") { ?> 
                                <button class="btn btn-outline-secondary" type="button" onclick="document.location.href = 'oidclogin.php?suffisso=<?php echo $_SESSION['suffisso']; ?> '">Accedi con <?php echo $_SESSION['oidc_provider_name']; ?> </button>
                            <?php } ?> 
                            <button class="btn btn-outline-secondary" type="button" onclick="document.location.href = 'richresetpwd.php?suffisso=<?php echo $_SESSION['suffisso']; ?> '">Password Dimenticata</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('utente').focus();
</script>
<?php
stampa_piede_new($_SESSION['versioneprecedente']);
eseguiQuery($con, "insert into " . $prefisso_tabelle . "tbl_seed(seed) values('$seme')", false, false);
?>