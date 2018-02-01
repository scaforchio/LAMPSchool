<?php session_start();
/**
 * Nuova installazione, inserimento dei parametri globali di LAMPSchool
 * i dati di accesso alla base dati sono scritti nel file newphp-ini.php
 * tutti gli altri parametri saranno memorizzati nella tabella tbl_parametri
 * 
 * @copyright  Copyright (C) 2015 Angelo Scarnà, Renato Tamilio
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */

require_once '../lib/funzioni.php';
require_once 'funzioni_install.php';

$json = leggeFileJSON('../lampschool.json');
$titolo = $json['titolo']. ' '. $json['versione']. ' Installazione';
////session_start();

$script = getCssJavascript(). "
<script>
function validazione() {
    
    return true;
}
function setAction(url) {
    objForm=document.getElementById('formInstall');
    objForm.action=url;
    
    if (url == 'installsalva.php' && validazione()) {
        $('.modal').show();
        objForm.submit();
    }
    
    if (url == 'installdb.php') {
        objForm.submit();
    }
}
</script>
";
stampa_head('Installazione Lampschool', '', $script,"", false);
stampa_testata_installer($titolo, '', '');

print '<div class="modal"></div>';

$scrittura = true;
$fileIni = 'newphp-ini.php';

if (file_exists($fileIni)) {
    
    if (!is_writable($fileIni)) {
        print "<center>il file $fileIni non &egrave; scrivibile. Dare i permessi in scrittura al file.</center>";
        $scrittura = false;
    }
} else {
	
    if (!is_writable("..")) {
        print "<center>La cartella di destinazione non &egrave; scrivibile. Dare i permessi in scrittura alla cartella.</center>";
        $scrittura = false;
    }
}

$par_db_server = stringa_html('par_db_server');
$par_db_nome = stringa_html('par_db_nome');
$par_db_user = stringa_html('par_db_user');
$par_db_password = stringa_html('par_db_password');
$par_prefisso_tabelle = stringa_html('par_prefisso_tabelle');
$par_nomescuola = stringa_html('par_nomescuola');
$par_passwordadmin = stringa_html('par_passwordadmin');
$par_suffisso_installazione = stringa_html('par_suffisso_installazione');
$par_cartellabuffer = stringa_html('par_cartellabuffer');

// Preparazione del file newphp-ini.php che ha come modello il file php-ini.php
$str = file_get_contents('php-ini.php');
$str = str_replace("{DBHOST}", "$par_db_server", $str);
$str = str_replace("{DBNAME}", "$par_db_nome", $str);
$str = str_replace("{DBUSER}", "$par_db_user", $str);
$str = str_replace("{DBPWD}", "$par_db_password", $str);
$str = str_replace("{DBPREFIX}", "$par_prefisso_tabelle", $str);
file_put_contents('newphp-ini.php', $str);

// copy('php-ini.php.bkp', '../php-ini.php');
print "<center><br>File php-ini".$par_suffisso_installazione.".php correttamente creato!</center>";

print "
 <form method='post' id='formInstall'>
  <input type='hidden' name='par_db_server' value='$par_db_server'>
  <input type='hidden' name='par_db_nome' value='$par_db_nome'>
  <input type='hidden' name='par_db_user' value='$par_db_user'>
  <input type='hidden' name='par_db_password' value='$par_db_password'>
  <input type='hidden' name='par_prefisso_tabelle' value='$par_prefisso_tabelle'>
  <input type='hidden' name='par_nomescuola' value='$par_nomescuola'>
  <input type='hidden' name='par_passwordadmin' value='$par_passwordadmin'>
  <input type='hidden' name='par_suffisso_installazione' value='$par_suffisso_installazione'>
</form>
";

stampaPulsanti('installdb','installsalva');

stampa_piede('', false);

