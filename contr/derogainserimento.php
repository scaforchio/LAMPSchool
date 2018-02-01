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


/*
     INSERIMENTO DELLE CATTEDRE
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

$docente = stringa_html('docente');

//
//    Parte iniziale della pagina
//

$titolo = "Deroga a limite inserimento dati";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

//
//    Fine parte iniziale della pagina
//

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

print ("
   <form method='post' action='derogainserimentoins.php'>
   
   <p align='center'>
   <table align='center' border='1'>

      <tr class='prima'>
      <td colspan='2' align='center'><b>Docente</b>");

//  $sqld= "SELECT * FROM tbl_docenti WHERE NOT sostegno ORDER BY cognome, nome";
$sqld = "SELECT * FROM tbl_docenti where iddocente<>1000000000 ORDER BY cognome, nome ";
$resd = mysqli_query($con, inspref($sqld));
if (!$resd)
{
    print ("<br/> <br/> <br/> <h2>a Impossibile visualizzare i dati </h2>");
}
else
{
    print ("<select name='docente'><option value=''>&nbsp;</option>");
    print ("<option>");
    while ($datal = mysqli_fetch_array($resd))
    {
        print("<option value='");
        print($datal['iddocente']);
        print("'");

        print ">";
        print($datal['cognome']);
        print("&nbsp;");
        print($datal['nome']);
    }

}
print("</select> </td> </tr>");
print("</table>");
print("<br><center><input type='submit' value='Abilita docente ad inserimento fuori limite'><br>
             <br><b>ATTENZIONE! L'abilitazione terminerà alla mezzanotte di oggi</b></center>");
print "</form>";

mysqli_close($con);
stampa_piede("");

