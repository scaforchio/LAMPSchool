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



/*programma per l'inserimento di un docente
riceve in ingresso iddocente*/
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
	$titolo="Inserimento preside";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
    
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
	$sql="SELECT * from tbl_docenti where iddocente=1000000000";
	$result=mysqli_query($con,inspref($sql));
	
	if(mysqli_num_rows($result)>0)
		print("<br/><br/><center><b>Il preside è stato già inserito!</center>");
	else
	{
	    
 
	print("<CENTER>");
	print("<FORM NAME='mod'action='ok_pre.php' method='post'>");
		print("\n<table>\n");
		
		print(" <tr><td><input type ='hidden' size='20' name='codice'></td>\n</tr>\n");
		print("<tr>\n\t<td> Cognome <font color='red'><b>*</b></font></td>\n\t");
		print(" <td><input type ='text' size='20' name='cognome'></td>\n</tr>\n");
		print("<tr>\n\t<td> Nome<font color='red'><b>*</b></font></td>\n\t");
		print(" <td><input type ='text' size='20' name='nome'></td>\n</tr>\n");
		print("<tr>\n\t<td> Data di nascita</td>\n\t");
		print(" <td><input type ='text' size='2'maxlength='2' name='datadinascg'> / <input type ='text' size='2' maxlength='2'name='datadinascm'> / <input type ='text' size='4' maxlength='4'name='datadinasca'></td>\n</tr>\n");
	//	$idcomn=$Data['idcomnasc'];
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
		print("<tr>\n\t<td> Indirizzo</td>\n\t");
		print(" <td><input type ='text' size='20' name='indirizzo'> </td>\n</tr>\n");
	
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


		print("<tr>\n\t<td> Telefono</td>\n\t");
		print(" <td> <input type ='text' size='20' name='telefono'></td>\n</tr>\n");
		print("<tr>\n\t<td> Cellulare\n\t");
		print(" <td> <input type ='text' size='20' name='telcel'></td>\n</tr>\n");
		print("<tr>\n\t<td> Email</td>\n\t");
		print(" <td><input type ='text' size='20' name='email'></td>\n</tr>\n");
			
	print("</table><br/>");
	print("<INPUT TYPE='SUBMIT' VALUE='Inserisci'>");
	print("\n</FORM>");
	} 
	
	
	stampa_piede("");
	mysqli_close($con);

