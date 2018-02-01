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

// Programma per la visualizzazione dell'elenco delle tbl_classi.

require_once '../php-ini'.$_SESSION['suffisso'].'.php';
require_once '../lib/funzioni.php';
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
    
 
//require_once '../lib/ db /query.php';

// $lQuery = LQuery::getIstanza();

// istruzioni per tornare alla pagina di login 
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
    die;
}	
	
$titolo = "Cancellazione sezione";
$script = ""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_sez.php'>Elenco sezioni</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

//Esecuzione query
$idse = stringa_html('idsez');

if (mysqli_query($con,inspref("delete from tbl_sezioni where idsezione=$idse"))) 
{//$lQuery->delete('tbl_sezioni', 'idsezione=?', array($idse))) {
   // print "<CENTER><FONT SIZE='+2'>\n Eliminazione effettuata</FONT></CENTER>";
    print "
                 <form method='post' id='formdoc' action='../sezioni/vis_sez.php'>
                 
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formdoc').submit();
                 }
                 </SCRIPT>";	
} 
else 
{
    print "<CENTER><FONT SIZE='+2'>\n Eliminazione non effettuata</FONT></CENTER>";
}
mysqli_close($con);	
stampa_piede("");

