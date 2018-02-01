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
$titolo = "Elenco collegamenti web";
$script = "";
stampa_head($titolo, "", $script,"PMSDAT");
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
if ($tipoutente!="P")
    $sql = "SELECT * FROM tbl_collegamenti WHERE LOCATE('$tipoutente',destinatari)<>0 ORDER BY descrizione";
else
    $sql = "SELECT * FROM tbl_collegamenti ORDER BY descrizione";
$result = mysqli_query($con, inspref($sql)) or die("Errore:" . inspref($sql, false));


print("<CENTER><br>");


$w = mysqli_num_rows($result);
if ($w > 0)
{
    while ($Data = mysqli_fetch_array($result))
    {
        print "<a href='".$Data['link']."' target='_blank'>".$Data['descrizione']."</a><br><br>";
    }
}
else
{
    print("<tr BGCOLOR='#cccccc'><td colspan='11'> <center>Nessun collegamento trovato</center></td></tr>");
}


stampa_piede("");
mysqli_close($con);

