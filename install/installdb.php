<?php session_start();
/**
 * Nuova installazione, inserimento dei dati per la connessione alla base dati
 *
 * @copyright  Copyright (C) 2015 Angelo Scarnà, Renato Tamilio
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */

require_once '../lib/funzioni.php';
require_once 'funzioni_install.php';

$json = leggeFileJSON('../lampschool.json');
$titolo = $json['titolo'] . ' ' . $json['versione'] . ' Installazione';
////session_start();

$script = getCssJavascript() . "
<script>
function validazione() {
    host = document.getElementById('host').value.trim();
    db = document.getElementById('db').value.trim();
    user = document.getElementById('user').value.trim();
    msg = '';
    
    if (host.length == 0) {
        msg += 'Inserire il Nome host\\n';
    }
    if (db.length == 0) {
        msg += 'Inserire il Nome database\\n';
    }
    if (user.length == 0) {
        msg += 'Inserire il Nome utente';
    }
    if (msg.length > 0) {
        alert(msg);
        return false;
    }
    document.getElementById('validated').value = '1';
    
    return true;
}

function setAction(url) {
    
    if (url == 'installprerequisiti.php' || validazione()) {
        objForm = document.getElementById('formInstall');
        objForm.action = url;
        objForm.submit();
    }
}
</script>
";
stampa_head('Installazione Lampschool', '', $script, '', false);
stampa_testata_installer($titolo, '', '');

$par_db_server = stringa_html('par_db_server');
$par_db_nome = stringa_html('par_db_nome');
$par_prefisso_tabelle = stringa_html('par_prefisso_tabelle');
$par_suffisso_installazione = stringa_html('par_suffisso_installazione');
$par_db_user = stringa_html('par_db_user');
$par_db_password = stringa_html('par_db_password');
$par_nomescuola = stringa_html('par_nomescuola');
$par_passwordadmin = stringa_html('par_passwordadmin');
$validated = stringa_html('validated');
$messaggio = 'Il database deve essere gi&agrave; creato.<br/>Altrimenti, provvedere a crearlo prima di procedere.';
$paginaAvanti = 'installdb';
$stile = "";
if ($validated == "1")
{
    $err = check_db($par_db_server, $par_db_user, $par_db_password, $par_db_nome, $par_prefisso_tabelle);
    print "<center>";

    if ($err == 1)
    {
        $stile = "importante";
        $messaggio = "<h3 class='$stile'>Connessione al server fallita o database inesistente !</h3>";
    }
    else
    {
        if ($err == 3)
        {
            $stile="importanteverde";
            // Sostituisce nel file php-ini.php i dati della base dati
            $str = file_get_contents('php-ini.php');
            $str = str_replace("{DBHOST}", "$par_db_server", $str);
            $str = str_replace("{DBNAME}", "$par_db_nome", $str);
            $str = str_replace("{DBUSER}", "$par_db_user", $str);
            $str = str_replace("{DBPWD}", "$par_db_password", $str);
            $str = str_replace("{DBPREFIX}", "$par_prefisso_tabelle", $str);

            file_put_contents('newphp-ini.php', $str);
            $messaggio = "Le tabelle di LAMPSchool non esistono. Clicca su [Avanti] per procedere";
            $paginaAvanti = 'installparametri';
        }
    }
    print "</center>";
}
print "
<center>
    <h3 class='$stile'>$messaggio</h3>
    <h3>Inserire i dati per la connessione al database</h3>
    <form id='formInstall' method='post' onsubmit='return validazione();'>
        <input type='hidden' id='validated' name='validated' value=''>
        <table border='1'>
            <tr class='prima'>
                <td>Variabile</td>
                <td>Valore</td>
            </tr>
            <tr>
                <td>Nome host <span>*</span></td>
                <td><input type='text' id='host' name='par_db_server' size='25' value='$par_db_server'></td>
            </tr>
            <tr>
                <td>Nome database <span>*</span></td>
                <td><input type='text' id='db' name='par_db_nome' size='25' value='$par_db_nome'></td>
            </tr>
            <tr>
                <td>Nome utente <span>*</span></td>
                <td><input type='text' id='user' name='par_db_user' size='25' value='$par_db_user'></td>
            </tr>
            <tr>
                <td>Password</td>
                <td><input type='password' name='par_db_password' size='25' value='$par_db_password'></td>
            </tr>
            <tr>
                <td>Prefisso tabelle</td>
                <td><input type='text' name='par_prefisso_tabelle' size='25' value='$par_prefisso_tabelle'></td>
            </tr>
            <tr>
                <td>Suffisso installazione<br><small>(lasciare vuoto SOLO se non si prevedono installazioni multiple)</small></td>
                <td><input type='text' name='par_suffisso_installazione' size='25' value='$par_suffisso_installazione'></td>
            </tr>
            <tr>
                <td>Nome scuola</td>
                <td><input type='text' name='par_nomescuola' size='25' value='$par_nomescuola'></td>
            </tr>
            <tr>
                <td>Password amministratore<br><small>Lasciare vuoto per confermare 'admin'</small></td>
                <td><input type='text' name='par_passwordadmin' size='25' value='$par_passwordadmin'></td>
            </tr>
        </table>
    </form>
</center>";

stampaPulsanti("installprerequisiti", "$paginaAvanti");

stampa_piede('', false);

