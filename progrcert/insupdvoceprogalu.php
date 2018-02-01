
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
 
@require_once("../php-ini".$_SESSION['suffisso'].".php");
@require_once("../lib/funzioni.php");
    
// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente=$_SESSION["idutente"];
if ($tipoutente=="")
{
   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
   die;
} 

$titolo="Aggiornamento voce di programma";
	$script="";
    stampa_head($titolo,"",$script,"SDMAP");
	stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 


$cattedra = stringa_html("cattedra");
print $cattedra;
$idabil = stringa_html("idabil");
$sintesi = stringa_html("sintesi");
$descrizione = stringa_html("descrizione");
if (is_stringa_html("obminimi"))
   $obminimi=1;
else
   $obminimi=0;




   $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
    
   $query="update tbl_abilalu set sintabilcono='".elimina_apici($sintesi)."', abilcono='".elimina_apici($descrizione)."', obminimi=$obminimi
           where idabilita=$idabil";
   // print inspref($query);
   if ($ris=mysqli_query($con,inspref($query)))
      // header("location: modivoceprog.php?cattedra=$cattedra");
      print "
        <form method='post' id='formvoce' action='modivoceprogalu.php'>
        <input type='hidden' name='cattedra' value='$cattedra'>
        </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formvoce').submit();
        }
        </SCRIPT>";
     
   else
       die ("Errore nella query! Contattare il sistemista.");    
   
mysqli_close($con);
stampa_piede(""); 

