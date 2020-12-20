<?php

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

/* Programma per reset PWD */

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
//session_unset();
//session_destroy();
//session_start();

$_SESSION["prefisso"] = $prefisso_tabelle;

$_SESSION["suffisso"] = $suffisso;

$_SESSION["alias"] = false;


$json = leggeFileJSON('../lampschool.json');
$_SESSION['versione'] = $json['versione'];

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);


$titolo = "Richiesta reset pwd.";

$script = "<script src='../lib/js/crypto.js'></script>\n";
$script .= "<script>

function codifica()
{
    seme='$seme';
   
    document.getElementById('passwordmd5').value = hex_md5(hex_md5(hex_md5(document.getElementById('password').value))+seme);
    document.getElementById('password').value = '';
    return true;
}
   

</script>\n";
stampa_head($titolo, "", $script, "", false);
stampa_testata("Richiesta reset password", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$messaggio = stringa_html('messaggio');

if (strlen($messaggio) > 0)
{
    $mex = '<center><font color="red"><br><b>';
    if ($messaggio == 'err1')
        $mex .= "Utente non presente!";
    if ($messaggio == 'err3')
        $mex .= "OTP errata!";
    if ($messaggio == 'err2')
        $mex .= "OTP scaduta!";
    if ($messaggio == 'err4')
        $mex .= "OTP non generata!";
    if ($messaggio == 'err5')
        $mex .= "Funzione non abilitata agli alunni. Rivolgersi a un docente referente!";
    if ($messaggio == 'err6')
        $mex .= "Non sono impostate email per l'utente!";

    echo $mex . '</b><br></font></center>';
}

print "<center>
    <form id='formLogin' action='resetpwd.php?suffisso=$suffisso' method='POST' onsubmit='return codifica();'>
        <table border='0'>
            <tr>
                <td> Utente</td>
                <td><input type='text' name='utente' id='utente'></td>
            </tr>
            
            <tr>
                <td colspan='2' align='center'><br/><input type='submit' name='OK' value='Chiedi reset pwd'></td>
            </tr>
        </table>
        
    </form>
    <br/>


</center>";


//$json = leggeFileJSON('../lampschool.json');

stampa_piede($_SESSION['versioneprecedente']);
?>
