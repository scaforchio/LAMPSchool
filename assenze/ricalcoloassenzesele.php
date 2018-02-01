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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idclasse= stringa_html('idclasse');
$inizio=stringa_html('inizio');
$fine=stringa_html('fine');
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));



$titolo = "Ricalcolo assenze";
$script = "<script src='../lib/js/popupjquery.js'></script>";
$script .= "<script>

$(function() {
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        showOn: 'button',
        buttonImage: '../immagini/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true
   });
});
</script>";
stampa_head($titolo, "", $script,"MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a>$goback[1] - $titolo", "", "$nome_scuola", "$comune_scuola");



print ('
         <form method="post" action="ricalcoloassenzesele.php" name="tbl_assenze">


   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="idclasse" NAME="idclasse" ONCHANGE="tbl_assenze.submit()">');



   $query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY specializzazione, sezione, anno";
    print "<option value='0'>&nbsp;";

$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
    if ($idclasse == $nom["idclasse"])
    {
        print " selected";
    }
    print ">";
    print ($nom["anno"]);
    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
}
echo('
      </SELECT>
      </td></tr>');

      print("<tr><td> Data inizio ricalcolo </td>");
    print("<td><input type ='text' name='inizio' class='datepicker' size='8' maxlength='10' value='$inizio'  ONCHANGE='tbl_assenze.submit()'></td></tr>");
    print("<tr><td> Data fine ricalcolo </td>");
    print("<td><input type ='text' name='fine' class='datepicker' size='8' maxlength='10' value='$fine' ONCHANGE='tbl_assenze.submit()'></td></tr>");
    print("</table></form>");
//
//   Inizio visualizzazione della data
//



if (($inizio != "") && ($fine != "")  && ($inizio<=$fine))
{
    print "<br><center><form action='ricalcolaassenze.php'>";
    print "<input type='hidden' name='idclasse' value='$idclasse'>
           <input type='hidden' name='inizio' value='$inizio'>
           <input type='hidden' name='fine' value='$fine'>
           <input type='submit' value='Ricalcola assenze'>
           </form></center>";
}
// fine if

mysqli_close($con);
stampa_piede("");

