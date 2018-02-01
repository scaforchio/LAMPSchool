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
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

////session_start();
$tipoutente = $_SESSION["tipoutente"];
$userid = $_SESSION["userid"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Cambiamento password";
$script = "";
stampa_head($titolo, "", $script, "E");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

/*Programma per il cambiamento password.*/

//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

if (!$con)
{
    //imposta la tabella del titolo
    print("<table border='0' width='100%'>
		<tr>
		   <td align ='center'><strong><font size='+1'> Connessione fallita </font></strong></td>
		</tr>
		</table> <br/><br/>");

    print("\n<h1> Connessione al server fallita </h1>");
    exit;
}

//Connessione al database
$DB = true;
if (!$DB)
{
    print "NOME DATABASE:" . $db_nome;
    print("\n<h1> Connessione al database fallita </h1>");
    exit;
}

$ute = stringa_html('ute');
$pwd = stringa_html('password');

$npass = stringa_html('npass');
$rnpass = stringa_html('rnpass');


//Esecuzione query

if (md5($pwd) != $passwordesame)
{
    print "<center>Password originale errata: verificare.</center>";
}
else
{

    if ($npass != $rnpass)
    {
        print "<center>Le password inserite sono diverse tra loro!</center>";
    }
    else
    {

        $query = "UPDATE tbl_parametri SET valore = md5('" . $npass . "') WHERE parametro='passwordesame'";

        $result = mysqli_query($con, inspref($query));

        if (mysqli_affected_rows($con) == 1)
        {
            print "<center>Password cambiata correttamente.</center>";
        }
        else
        {
            print "<center>Errore nel database! Contattare il sistemista.</center>";
        }
    }

}
mysqli_close($con);
stampa_piede("");

