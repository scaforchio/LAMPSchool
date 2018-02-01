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

//Visualizzazione classi
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

$titolo = "Selezione dati di backup";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
print "<center><b>ATTENZIONE! Per un backup completo selezionare tutte le voci.<br> Le voci selezionate automaticamente sono quelle delle tabelle aggiornate correntemente durante l'anno scolastico.</b></center><br/><br>";
print "<form method='POST' action='backup.php'>";
print "<center>";
print "<select multiple size='9' name='tabbkp[]'>";
print "<option value='ana'>Anagrafiche</option>";
print "<option value='pro'>Programmazioni</option>";
print "<option value='ass' selected>Assenze</option>";
print "<option value='val' selected>Valutazioni</option>";
print "<option value='not' selected>Note disciplinari</option>";
print "<option value='lez' selected>Lezioni</option>";
print "<option value='avv' selected>Avvisi, colloqui, sms, log</option>";
print "<option value='scr' selected>Scrutini</option>";
print "<option value='tab'>Tabelle</option>";
print "<option value='com'>Comuni</option>";
print "<option value='pdf'>Documenti PDF</option>";
print "</select>";
print "<br><br><input type='submit' value='Esegui backup delle voci selezionate'>";

print "</table> </form>";
stampa_piede("");



