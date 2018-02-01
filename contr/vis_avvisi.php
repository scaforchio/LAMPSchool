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

/*programma per la visualizzazione e modifica degli avvisi
 */
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
$titolo = "Elenco avvisi";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<H1>connessione al server mysql fallita</H1>");
    exit;
}
$DB = true;
if (!$DB)
{
    print("<H1>connessione al database fallita</H1>");
    exit;
}
$sql = "SELECT * FROM tbl_avvisi ORDER BY inizio";
$result = mysqli_query($con, inspref($sql));
if (!($result))
{
    print("query fallita");
}
else
{

    print("<CENTER><table border=1>");

    print("<tr class='prima'><td><center><b> Data inizio</b></td>\n\t ");
    print("<td><center><b> Data fine</b></td>\n\t ");

    print("<td><center><b> Oggetto</b> </td>\n\t ");

    print("<td><center><b> Destinatari</b> </td>\n</b>\n ");
    print("<td colspan='2'><center><b> Azione</b> </td>\n</tr></b>\n ");
    $w = mysqli_num_rows($result);
    if ($w > 0)
    {
        while ($Data = mysqli_fetch_array($result))
        {
            print("<tr class='oddeven'><td>" . $Data['inizio'] . "</td>");
            print("<td>" . $Data['fine'] . "</td>");
            print("<td>" . $Data['oggetto'] . "</td>");
            print("<td>" . $Data['destinatari'] . "</td>");
            print "<td><a href='avvisi.php?idavviso=" . $Data['idavviso'] . "'><img src='../immagini/edit.png' title='Modifica'></a>";
            print "&nbsp;<a href='can_avv.php?idavviso=" . $Data['idavviso'] . "'><img src='../immagini/delete.png' title='Elimina'></a>";
            print "</td></tr>";
        }
    }
    else
    {
        print("<tr BGCOLOR='#cccccc'><td colspan='11'> <center>Nessun avviso trovato</center></td></tr>");
    }
    print("</table><br/><br/><br/>");

}
print("<form action='avvisi.php' method='POST'><center><INPUT TYPE='SUBMIT' VALUE='Inserisci nuovo avviso'");

print("</center></form><br/>");


stampa_piede("");
mysqli_close($con);

