<?php

session_start();
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
/* Programma per la conferma del login. */
$titolo = "Conferma cambiamento password";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$ute = stringa_html('utente');
$pas = stringa_html('npas');
$pas2 = stringa_html('rnpas');

//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    //imposta la tabella del titolo
    print("<table border=0 width='100%'>
		<tr>
		   <td align ='center' ><strong><font size='+1'> Connessione fallita </font></strong></td>
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

//Esecuzione query
$sql = "select * from tbl_utenti where userid='$ute'";

$result = eseguiQuery($con, $sql);
// prelevo l'id dell'utente che coincide con quello del tutore
$val = mysqli_fetch_array($result);
$idutente = $val["idutente"];
$tipoutente = $val["tipo"];

if (mysqli_num_rows($result) <= 0)
{
    print ("<center>L'utente non risulta presente: verificare.</center>");
} else
{
    if (stringa_html('npass') != stringa_html('rnpass'))
    {
        print ("<center>Le password inserite sono diverse tra loro!</center>");
    } else
    {
        if ($tipoutente == "T")
        {
            // alunno
            $sql = "select * from tbl_alunni where idalunno=$idutente";
            $descrizioneUtente = "il genitore dell'alunno";
        } else if ($tipoutente == "D" | $tipoutente == "S")
        {
            // docente
            $sql = "select * from tbl_docenti where idutente=$idutente";
            $descrizioneUtente = "il docente";
        } else if ($tipoutente == "A")
        {
            // amministrativo
            $sql = "select * from tbl_amministrativi where idutente=$idutente";
            $descrizioneUtente = "l'impiegato";
        } else if ($tipoutente == "P")
        {
            // amministrativo
            $sql = "select * from tbl_docenti where idutente=1000000000";
            $descrizioneUtente = "il preside";
        } else if ($tipoutente == "L")
        {
            // alunno
            $idute = $idutente - 2100000000;
            $sql = "select * from tbl_alunni where idutente=$idute";
            $descrizioneUtente = "l'alunno";
        }
        // PRELEVO il nome e cognome
        $result = eseguiQuery($con, $sql);
        $val = mysqli_fetch_array($result);
        $cognome = $val["cognome"];
        $nome = $val["nome"];
        // FINE
        print "<center>";
        print "<form name='form1' action='gest_ch_pwd.php' method='POST'>";
        print "<input type='hidden' name='utente' value='$ute'>";
        print "<input type='hidden' name='npass' value='$pas'>";
        print "<input type='hidden' name='rnpass' value='$pas2'>";
        print "<table border='0'>";
        print "<tr><td><br/>Confermi il cambiamento della password per $descrizioneUtente $cognome $nome?<br/><br/>";
        print "</td></tr>";
        print "<tr><td>";
        print "<center> <input type='submit' name='OK' value='OK'></center>";
        print "</td>";
        print "</tr>";
        print "</table></form></center>";
    }
}
mysqli_close($con);
stampa_piede("");


