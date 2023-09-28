<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2023 Vittorio Lo Mele
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

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

require_once("../vendor/autoload.php"); //carica librerie
use OTPHP\TOTP;

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Rigenera OTP";
$style = "
<style>
    .logout{
        display: none;
    }
    #testata{
        display: none;
    }
    #help{
        display: none;
    }
    #funzione{
        display: none;
    }
    #piede{
        display: none;
    }
</style>
";

stampa_head($titolo, "", $style, "MS");
stampa_testata($titolo, "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

$idalunno = mysqli_real_escape_string($con, $_GET["idalu"]);
$otp = TOTP::create();
$code = $otp->getSecret();

$query = eseguiQuery($con, "SELECT nome, cognome, datanascita FROM tbl_alunni WHERE idalunno = $idalunno;");
$alunno = mysqli_fetch_assoc($query);

eseguiQuery($con, "UPDATE tbl_alunni SET totpgiustass = '$code' WHERE idalunno = $idalunno");

?>

<center>
    <b>Ai genitori dell'alunno</b><br>
    <table border="1" style="margin-top: 8px">
        <thead>
            <tr class="prima">
                <td>Nome</td>
                <td>Cognome</td>
                <td>Data di Nascita</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $alunno["nome"]; ?></td>
                <td><?php echo $alunno["cognome"]; ?></td>
                <td><?php echo data_italiana($alunno["datanascita"]); ?></td>
            </tr>
        </tbody>
    </table><br>

    Il seguente codice, una volta scansionato, permette l'utilizzo da parte dei genitori della funzione "Giustifica Assenze Online".
    <br><br>
    Per utilizzare il codice è richiesta l'applicazione "Google Authenticator" scaricabile <br> dal Play Store (Android) o dall'App Store (iOS),<br><br>
    Il codice può essere scansionato su più dispositivi per permettere a entrambi i genitori <br> di utilizzare la funzione, tuttavia si raccomanda di strappare il foglio dopo la scansione per motivi di sicurezza.<br>

    <img src="../lib/genqr.php?code=<?php echo $code ?>" alt="QR_code" width="200" height="200" > <br>

    CODICE SEGRETO:<br> 
    <div style="width: 250px; word-wrap: break-word;">
        <code><?php echo  $code;?></code>
    </div> <br><br>

    <button id='btn1' onclick="window.close()" class="button">Chiudi</button>
    <button id='btn2' onclick="stampa()" class="button">Stampa</button>

</center>

<script>
    function stampa() {
        document.getElementById("btn1").style.display = "none";
        document.getElementById("btn2").style.display = "none";
        window.print();
    }
</script>

<?php

stampa_piede("");
mysqli_close($con);
