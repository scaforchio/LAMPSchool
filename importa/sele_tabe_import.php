<?php session_start();

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

//Selezione dati da importare
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Selezione dati da importare";
$script = "";
stampa_head($titolo, "", $script,"MPA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

print "<form method='POST' action='importazione.php'>";

print "<center>";
print "<table>";
print "<tr><td>Server origine:&nbsp;(Lasciare vuoto se coincide con destinazione)</td><td><input type='text' name='oldserver'></td></tr>";
print "<tr><td>Database origine:&nbsp;(Lasciare vuoto se coincide con destinazione)</td><td><input type='text' name='olddb'></td></tr>";
print "<tr><td>Prefisso origine:&nbsp;</td><td><input type='text' name='oldpref'></td></tr>";
print "<tr><td>Utente:&nbsp;(Lasciare vuoto se coincide con destinazione)</td><td><input type='text' name='olduser'></td></tr>";
print "<tr><td>Password:&nbsp;(Lasciare vuoto se coincide con destinazione)</td><td><input type='text' name='oldpwd'></td></tr>";
print "</table><br>";

print "<select multiple size='9' name='tabbkp[]'>";
print "<option value='ana' selected>Anagrafiche</option>";
print "<option value='pro' selected>Programmazioni</option>";
print "<option value='tab' selected>Tabelle(classi, materie, ecc.)</option>";
print "<option value='par' selected>Parametri configurazione</option>";
print "</select>";
print "<br><br><font color='red'>ATTENZIONE! L'importazione cancellerà eventuali dati già inseriti.</font><br><br><input type='submit' value='Esegui import delle voci selezionate'>";

print "</table> </form>";
stampa_piede("");
 


