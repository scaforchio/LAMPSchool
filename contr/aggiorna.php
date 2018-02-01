<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
GNU Affero General Public License come pubblicata 
dalla Free Software Foundation; sia la versione 3, 
sia (a vostra scelta) ogni versione successiva.

Questo programma é distribuito nella speranza che sia utile 
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di 
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE. 
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");


// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Aggiornamento da versioni precedenti";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

// VERIFICA I PREREQUISITI PER IL FUNZIONAMENTO DELLA NUOVA VERSIONE

$json = leggeFileJSON('../lampschool.json');
$pre_error = '';
$versionephp = phpversion();
$versionephpok = version_compare(substr($versionephp, 0, 3), '5.0', '>=');
$autostart = ini_get('session.auto_start');
$estensionemysql = extension_loaded('mysqli');

$versionefinale = $json['versione'];
$versioneLampschoolok = version_compare($json['versione'], $versione, '>');

$imgok = "<img src='../immagini/apply.png'>";
$imgko = "<img src='../immagini/cancel.png'>";

if (!$versionephpok)
{
    $pre_error = "E' necessario utilizzare PHP5 o superiore per LAMPSchool!<br />";
}

if ($autostart)
{
    $pre_error .= "LAMPSchool potrebbe non funzionare con session.auto_start abilitato!<br />";
}

if (!$estensionemysql)
{
    $pre_error .= "L'estensione di MySQL non &egrave; caricato !<br />";
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
   <td>Permessi R/W di /php-ini.php</td>
   <td align='center'>";
print $rwfileIni ? "S&igrave;" : "No";
print "</td>
   <td align='center'>";
print $rwfileIni ? $imgok : $imgko;
print "</td>
        <td>Verifica l'accesso in scrittura del file $fileIni<br/><font color='red'>$controlloFileini</font></td>
      </tr>

      </tr>
      <tr class='oddeven'>
        <td>Versione Lampschool</td>
        <td align='center'>$versione</td>
        <td align='center'>";
print $versioneLampschoolok ? $imgok : $imgko;
print "</td>
   <td>Controlla la versione precedente di LAMPSchool</td>
  </tr>
 </table>";

// PREPARO IL FORM PER FAR PARTIRE L'AGGIORNAMENTO
print "
<form id='formupdate' method='post' action='aggiornains.php'>
<input type='hidden' name='inizio' value='$versione'>
<input type='hidden' name='destinazione' value='$versionefinale'>
<input type='submit' value='Aggiorna'>
</form>
";


mysqli_close($con);
stampa_piede("");

