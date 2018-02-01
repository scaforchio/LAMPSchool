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


/*programma per la cancellazione di un avviso
riceve in ingresso idavviso*/
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

$titolo = "Cancellazione collegamento web";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_collegamenti.php'>ELENCO COLLEGAMENTI</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$idcollegamento = stringa_html('idavviso');
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<H1>connessione al server mysql fallita</H1>");
    exit;
}
$DB = true;
if (!$DB)
{
    print("<H1>connessione al database stage fallita</H1>");
    exit;
}
$sql = "SELECT * from tbl_collegamenti where idcollegamento=$idcollegamento";
$result = mysqli_query($con, inspref($sql)) or die("errore:".inspref($sql));
$data = mysqli_fetch_array($result);
if (!($result))
{
    print("Query fallita");
}
else
{

    print("<CENTER>");
    print("\n<table>\n");
    print("<tr><td align='right'> Cancellazione collegamento:</td>");
    print("<td align='left'><b> " . $data['descrizione'] . "</td>");
    print("</tr></table>");
    print("<br/>");
    print("<table>");
    print("<tr><td align='left'><FORM NAME='CA2' ACTION='ca2_coll.php' method='POST'>");
    print("<INPUT TYPE='hidden' name='idavviso' value='$idcollegamento'>");
    print("<INPUT TYPE='SUBMIT' VALUE='    SI    '></FORM></td>");
    print("<td>&nbsp;</td>");
    print("<td><FORM NAME='vis' action='vis_collegamenti.php' method='POST'>");
    print("<INPUT TYPE='SUBMIT' VALUE='    NO    '></FORM></td>");
    print("</tr>");
    print("\n</table>");
    print("\n</CENTER>");


}
stampa_piede("");
mysqli_close($con);

