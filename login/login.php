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

/*Programma per il login al registro.*/

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
session_unset();
session_destroy();
session_start();

$_SESSION["prefisso"] = $prefisso_tabelle;
$_SESSION["annoscol"] = $annoscol;
$_SESSION["suffisso"] = $suffisso;
$_SESSION["alias"]= false;



$titolo = "Inserimento dati di accesso";
$seme=md5(date('Y-m-d'));
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
stampa_testata("Accesso al registro", "", "$nome_scuola", "$comune_scuola");

$messaggio = stringa_html('messaggio');

if (strlen($messaggio) > 0)
{
    $mex = '<center><font color="red"><br><b>';

    if ($messaggio == 'errore')
    {
        $mex .= 'Nome utente e/o password errati !';
    }
    else
    {
        $mex .= $messaggio;
    }
    echo $mex . '</b><br></font></center>';
}
?>
<center>
    <form id='formLogin' action='ele_ges.php' method='POST' onsubmit='return codifica();'>
        <table border='0'>
            <tr>
                <td> Utente</td>
                <td><input type='text' name='utente' id='utente'></td>
            </tr>
            <tr>
                <td> Password</td>
                <td><input type='password' name='pass' id='password'></td>
            </tr>
            <tr>
                <td colspan='2' align='center'><br/><input type='submit' name='OK' value='Accedi'></td>
            </tr>
        </table>
        <noscript>
            <input name='js_enabled' type='hidden' value='1'>
        </noscript>
        <input type='hidden' name='password' id='passwordmd5'>
    </form>
    <br/>
    <?php
    print "<a href='$sito_scuola' target='_top'>Ritorna ad home page</a>";
    ?>
</center>
<script>
    document.getElementById('utente').focus();
</script>
<?php
$json = leggeFileJSON('../lampschool.json');

stampa_piede($json['versione']);
?>
