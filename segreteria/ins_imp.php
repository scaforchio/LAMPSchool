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



/*programma per l'inserimento di un amministrativo
riceve in ingresso idamministrativo*/
	@require_once("../php-ini".$_SESSION['suffisso'].".php");
	@require_once("../lib/funzioni.php");

	
	// istruzioni per tornare alla pagina di login 
	////session_start();
    $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
	if ($tipoutente=="")
	   {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
	   }
	
	$titolo="Inserimento amministrativo";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_imp.php'>ELENCO amministrativi</a> - $titolo","","$nome_scuola","$comune_scuola");
    
	$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
	if(!$con)
		{print("<H1>connessione al server mysql fallita</H1>");
	 	exit;
		}
	$DB=true;
	if(!$DB)
		{print("<H1>connessione al database stage fallita</H1>");
		 exit;
		}

	
 
	print("<CENTER>");
	print("<form name='mod' action='ok_imp.php' method='POST'>");
	print("<table>");
	
	print(" <tr><td><input type ='hidden' size='20' name='codice'></td></tr>");
	print("<tr><td> Cognome <font color='red'><b>*</b></font></td>");
	print(" <td><input type ='text' size='20' name='cognome'></td></tr>");
	print("<tr><td> Nome<font color='red'><b>*</b></font></td>");
	print(" <td><input type ='text' size='20' name='nome'></td></tr>");
	print("<tr><td> Data di nascita</td>");
	print(" <td><input type ='text' size='2'maxlength='2' name='datadinascg'> / <input type ='text' size='2' maxlength='2'name='datadinascm'> / <input type ='text' size='4' maxlength='4'name='datadinasca'></td></tr>");
	
	$sqla= "SELECT * FROM tbl_comuni ORDER BY denominazione";
   $resa=mysqli_query($con,inspref($sqla));
   if(!$resa)
   {
   	print ("<br/> <br/> <br/> <h2>a Impossibile visualizzare i dati </h2>");
   }
   else
   {
   	print   ("<tr> <td> Comune di nascita</td> <td> <select name='idcomn'>");
		print("<option>");
   	while ($datal=mysqli_fetch_array($resa))
   	{
			
				print("<option value='".$datal['idcomune']."'> ".$datal['denominazione']."");
		}
		print("</select> </td> </tr>"); 
   }
		print("<tr><td> Indirizzo</td>");
		print(" <td><input type ='text' size='20' name='indirizzo'> </td></tr>");
	
		$sqlb="SELECT * FROM tbl_comuni ORDER BY denominazione";
   		$resb=mysqli_query($con,inspref($sqlb));
   		if(!$resb)
   		{
    		print ("<br/> <br/> <br/> <h2> b Impossibile visualizzare i dati </h2>");
   		}                            
   		else
   		{
    		print  ("<tr> <td> Comune di residenza </td> <td> <select name='idcomr'><option value=''>");
    		while ($datbl_=mysqli_fetch_array($resb))
    		{
				print("<option value='".$datbl_['idcomune']."'> ".$datbl_['denominazione']."");
			}
    		print("</select> </td> </tr>"); 
   	}


		print("<tr><td> Telefono</td>");
		print(" <td> <input type ='text' size='20' name='telefono'></td></tr>");
		print("<tr><td> Cellulare</td>");
		print(" <td> <input type ='text' size='20' name='telcel'></td></tr>");
		print("<tr><td> Email</td>");
		print(" <td><input type ='text' size='20' name='email'></td></tr>");
			
		
   		
			
	print("</table><br/>");
	print("<INPUT TYPE='SUBMIT' VALUE='Inserisci'>");
	print("</form></CENTER>");
	
	print("<center><b><font color='red'>*</b></font>(Campi obbligatori)</center>"); 
	
	stampa_piede("");

	mysqli_close($con);

