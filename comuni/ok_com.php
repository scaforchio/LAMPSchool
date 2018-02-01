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

	@include ("../php-ini".$_SESSION['suffisso'].".php");
    @require_once("../lib/funzioni.php");
	
	// istruzioni per tornare alla pagina di login 
	////session_start();
    $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
	if ($tipoutente=="")
	   {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
	   }
	
     $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("connessione non riuscita"); 
     $db=true or die ("connessione al db fallita"); 
   
                     $eli="delete from tbl_comuni where idcomune=". stringa_html('idcom');
					 $si=mysqli_query($con,inspref($eli));
					 print"<table border=0 width='100%'>
		<tr>
		   <td align ='center' ><strong><font size='+1'>CONFERMA ELIMINAZIONE COMUNE</font></strong></td>
		</tr>
		</table cellspacing='15'> <br/><br/>";
		print"<center>";
                    if (!$si) 
					    print "ELIMINAZIONE NON EFFETTUATA";
					else
					    print "ELIMINAZIONE EFFETTUATA CORRETTAMENTE";
					 print "<form name='frm4' action='lis_com.php' method='POST'>";
   					 print "<input type='submit' value= '<<Indietro'>";
   					 print "</form>";
	   print"<center>";

