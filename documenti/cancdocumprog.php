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


/*programma per la cancellazione di un docente
riceve in ingresso iddocente*/
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");


// istruzioni per tornare alla pagina di login
////session_start();

$iddocumento = stringa_html('id');
$tipodoc = stringa_html('tipodoc');
switch ($tipodoc)
{
    case 'pia':
        $titolo = "Conferma cancellazione piano lavoro";
        $back = "Gestione piani lavoro";
        $tipodocumento = 1000000001;
        break;
    case 'pro':
        $titolo = "Conferma cancellazione programma";
        $back = "Gestione programmi";
        $tipodocumento = 1000000002;
        break;
    case 'rel':
        $titolo = "Conferma cancellazione relazione finale";
        $back = "Gestione relazioni finali";
        $tipodocumento = 1000000003;
        break;
}
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='../documenti/pianilavoro.php'>$back</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);


print "<center><br><b>Confermi cancellazione del documento?</b></center>";

print("<center><FORM NAME='CONF' ACTION='cancdocumprogok.php?tipodoc=$tipodoc' method='POST'>");
print("<br><br><table><INPUT TYPE='hidden' name='iddocumento' value='$iddocumento'>");
print("<tr><td align='left'><INPUT TYPE='SUBMIT' VALUE='    SI    '></td><td colspan='2'></td>");
print("\n</FORM>");

print("<FORM NAME='CAN' ACTION='documprog.php?tipodoc=$tipodoc' method='POST'>");
print("<td align='right'><INPUT TYPE='SUBMIT' VALUE='    NO    '></td></tr></table>");
print("\n</FORM></center>");

stampa_piede("");
mysqli_close($con);


