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
    <b>Rigenerazione OTP Giustifica Assenze per alunno</b><br>
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

    Il codice appena generato pu&ograve; essere visualizzato una sola volta! <br>
    Scansionare con un telefono cellulare munito di Authenticator TOTP <br>

    <img src="../lib/genqr.php?code=<?php echo $code ?>" alt="QR_code" width="200" height="200" > <br>

    CODICE SEGRETO:<br> 
    <div style="width: 250px; word-wrap: break-word;">
        <code><?php echo  $code;?></code>
    </div> <br><br>

    <button onclick="window.close()" class="button">Chiudi</button>

</center>

<?php

stampa_piede("");
mysqli_close($con);
