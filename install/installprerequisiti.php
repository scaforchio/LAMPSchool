<?php session_start();
/**
 * Nuova installazione, prerequisiti per LAMPSchool
 * 
 * @copyright  Copyright (C) 2015 Angelo Scarnà, Renato Tamilio
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */

require_once '../lib/funzioni.php';
require_once 'funzioni_install.php';

$json = leggeFileJSON('../lampschool.json');
$titolo = $json['titolo']. ' '. $json['versione']. ' Installazione';
////session_start();

stampa_head('Installazione Lampschool', '', getCssJavascript(),"", false);
stampa_testata_installer($titolo, '', '');

$pre_error = '';
$versionephp = phpversion();
$versionephpok = version_compare(substr($versionephp, 0, 3), '5.0', '>=');
$autostart = ini_get('session.auto_start');
$estensionemysql = extension_loaded('mysqli');
$estensionezip   = extension_loaded('zip');
$fileIni = '../php-ini.php';
$rwfileIni = true;
$controlloFileini = '';

if (!file_exists($fileIni)) {
    $controlloFileini = "il file $fileIni non &egrave; presente. Sar&agrave; creato.";
} else {
    $rwfileIni = is_writable($fileIni);

    if (!$rwfileIni) {
        $controlloFileini = "il file $fileIni deve essere accessibile in scrittura !";
    }
}

$imgok = "<img src='../immagini/apply.png'>";
$imgko = "<img src='../immagini/cancel.png'>";

if (!$versionephpok) {
   $pre_error = "E' necessario utilizzare PHP5 o superiore per LAMPSchool!<br />";
}

if ($autostart) {
   $pre_error .= 'LAMPSchool potrebbe non funzionare con session.auto_start abilitato!<br />';
}

if (!$estensionemysql) {
   $pre_error .= "L'estensione di MySQL non &egrave; caricato !<br />";
}

if (!$estensionezip) {
   $pre_error .= "L'estensione ZIP non &egrave; caricata !<br />";
}
print "
 $pre_error

 <table align='center' border='1'>
  <tr class='prima'>
   <td><b>Tipo controllo</b></td>
   <td><b>Valore attuale</b></td>
   <td><b>Esito</b></td>
   <td><b>Spiegazione</b></td>
  </tr>
  
  <tr class='oddeven'>
   <td>Versione PHP >= 5.0</td>
   <td align='center'>$versionephp</td>
   <td align='center'>";
print $versionephpok ? $imgok : $imgko;
print "</td>
   <td>Controlla la versione del linguaggio PHP</td>
  </tr>

  <tr class='oddeven'>  
   <td>Avvio automatico sessione</td>
   <td align='center'>";
print $autostart ? "on" : "off";
print "</td>
   <td align='center'>";
print !$autostart ? $imgok : $imgko;
print "</td>
   <td>Verifica il valore del parametro session_auto_start presente nel file php.ini</td>
  </tr>
  
  <tr class='oddeven'>
   <td>Database</td>
   <td align='center'>";
print $estensionemysql ? "on" : "off";
print "</td>
   <td align='center'>";
print $estensionemysql ? $imgok : $imgko;
print "</td>
   <td>Controlla il supporto del database per MySQL (mysql, mysqli)</td>
  </tr>

<tr class='oddeven'>
   <td>Libreria Zip</td>
   <td align='center'>";
print $estensionezip ? "on" : "off";
print "</td>
   <td align='center'>";
print $estensionezip ? $imgok : $imgko;
print "</td>
   <td>Controlla il supporto della compressione zip</td>
  </tr>

  <tr class='oddeven'>
   <td>Permessi R/W di /php-ini.php</td>
   <td align='center'>";
print $rwfileIni ? "S&igrave;" : "No";
print "</td>
   <td align='center'>";
print $rwfileIni ? $imgok : $imgko;
print "</td>
   <td>Verifica l'accesso in scrittura del file $fileIni<br/><font color='red'>$controlloFileini</font></td>
  </tr>

 </table>";
print '
<form id="formInstall" method="post">
</form>
';

stampaPulsanti('index','installdb');

stampa_piede('', false);

