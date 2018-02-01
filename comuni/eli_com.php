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
$sql='select * from tbl_comuni where idcomune='.$idcom;
if(!($result=mysqli_query($con,inspref($sql))))
   print("query fallita $sql");
 else
  {
  $data=mysqli_fetch_array($result);
  print("<table border=0 width='100%'>
		<tr>
		   <td align ='center' ><strong><font size='+1'>ELIMINA COMUNI</font></strong></td>
		</tr>
		</table> <br/><br/>");  
		print"<center> SI DESIDERA ELIMIARE I SEGUENTI DATI? </center>";
	print "<center><table border='1'>";
   print " <center> <table cellspacing='15'>";
   print " <tr> <td> ";
   print ("<center> <table cellspacing='15'>");
    print"<tr><td><input type ='hidden' name='idtbl_comuni' size='10' value='$idcomune'></td></tr>";
             print"<tr><td align='right'>Denominazione:</td> <td> <b>".$data['denominazione']."  </b> </td></tr>";
             print"<tr><td align='right'>CAP:</td><td><b> ".$data['cap']." </b></td></tr>";				  	
             print"<tr><td align='right'>Codistat:</td><td><b> ".$data['codistat']." </b></td></tr>";
             print"<tr><td align='right'>Provincia:</td><td><b> ".$data['provincia']." </b></td></tr>";
             print"<tr><td align='right'>Sigla provincia:</td><td><b> ".$data['siglaprovincia']." </b></td></tr>";
 			 print"<tr><td align='right'>Regione:</td><td><b> ".$data['regione']."</b> </td></tr>";
 			 print"<tr><td align='right'>Stato estero:</td><td><b> ".$data['statoestero']." </b></td></tr>";  
   
 
   
   print "</table>";
   print "</td> </tr>";
   print "</table> </center>"; 
   print "<center> <table>";
   print "<tr> <center> <td>";
   
   print("<form name='form1' action='ok_com.php?idcom=".$data['idcomune']."' method='POST'>");
   print("<input type='hidden' name='idcom' value='".$data['idcomune']."'>");
   print ("<input type='submit' value= '  SI '>");
   print("</form> </td> </center>"); 
   print("<td></td>");
   print("<td></td>");
   print "<center> <td> <form name='frm2' action='lis_com.php' method='POST'>";
   print "<input type='submit' value= ' NO'>";
   print "</form>";
  
   	
  print  "</td> </center> </tr>";
  print("</table> </center>");
  }
mysqli_close($con);

