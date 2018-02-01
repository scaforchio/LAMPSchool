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


 /* visualizzazione per la cancellazione
parametri di ingresso: codice dell'alunno                    
parametri di uscita: codice dell'alunno, codice della classe*/
 
 	// istruzioni per tornare alla pagina di login 
	////session_start();
    $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
	if ($tipoutente=="")
	   {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
	   }
 
 @require_once("../php-ini".$_SESSION['suffisso'].".php");
 @require_once("../lib/funzioni.php");
 //Imposta i colori dei link
 $titolo="Cancellazione alunno";
 $script=""; 
 stampa_head($titolo,"",$script,"MASP");
 stampa_testata("Cancellazione alunno","","$nome_scuola","$comune_scuola");
 //connessione al server
 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
 if(!$con)
 {
  	print("<h1> Connessione al server fallita </h1>");
 }
 
 //connessione al database
 $DB=true;
 if(!$DB)
 {
  	print("<h1> Connessione al database fallita </h1>");
 }
 $c= stringa_html('idal');
 $sql="SELECT * FROM tbl_alunni WHERE idalunno='$c'";
 
 //esecuzione query
 $res=mysqli_query($con,inspref($sql));
 if(!$res)
 {
  	print ("<br/> <br/> <br/> <h2> Impossibile visualizzare i dati </h2>");
 }
 else
 {
  	if($dato=mysqli_fetch_array($res))
  	{
   		print "<form action='alu_conf.php' method='POST'>";
        print "<center>";
   		print ("<table> ");
		print ("<tr> <td> </td> <td> <input type='hidden' value='".$dato['idalunno']."' name='idal'> </td> </tr>");
   		print ("<tr> <td> </td> <td> <input type='hidden' value='".$dato['idclasse']."' name='idcla'> </td> </tr>");				
		print   ("<tr> <td><i> Cognome</i> </td> <td> <input type='text' value='".$dato['cognome']."' name='cognome' size='30' maxlength='30'> </td> </tr>");
   		print   ("<tr> <td><i>Nome</i> </td> <td> <input type='text' value='".$dato['nome']."' name='nome' size='30' maxlength='30'> </td> </tr>");
   		$g=substr($dato['datanascita'],8,2);
   		$m=substr($dato['datanascita'],5,2);
   		$a=substr($dato['datanascita'],0,4);
		$idcomn=$dato['idcomnasc'];
		$idcomr=$dato['idcomres'];
		$idcla=$dato['idclasse'];
		$idtut=$dato['idtutore'];
   		print ("<tr> <td> <i> Data di nascita </i> </td> <td> <input type='text' value='$g' name='gg' size='1'  maxlength='2'> / <input type='text' value='$m' name='mm' size='1'  maxlength='2'> / <input type='text' value='$a' name='aa' size='3'  maxlength='4'> </td> </tr>");
   		$sqa= "SELECT * FROM tbl_comuni ORDER BY denominazione";
   		$resa=mysqli_query($con,inspref($sqla));
   		if(!$resa)
   		{
    			print ("<br/> <br/> <br/> <h2>Impossibile visualizzare i dati </h2>");
   		}
   		else
   		{
    			print   ("<tr> <td> <i>Comune di nascita</i> </td>");
    			while ($datal=mysqli_fetch_array($resa))
    			{
					if ($idcomn==($datal['idcomune']))
					{
    					print("<td> <input type='text' name='idcomn' value='".$datal['denominazione']."' size='30' maxlength='30'");
    		    	}
					
				}
    		   print ("</td> </tr>");  
   		}
		print  ("<tr> <td> <i> Indirizzo </i> </td> <td> <input type='text' value='".$dato['indirizzo']."' name='indirizzo' size='30' maxlength='30'> </td> </tr>");		
		$sqb="SELECT * FROM tbl_comuni ORDER BY denominazione";
   		$resb=mysqli_query($con,inspref($sqlb));
   		if(!$resb)
   		{
    		print ("<br/> <br/> <br/> <h2> b Impossibile visualizzare i dati </h2>");
   		}                            
   		else
   		{
    		print  ("<tr> <td> <i>Comune di residenza</i> </td>");
    		while ($datbl_=mysqli_fetch_array($resb))
    		{
				
				if ($idcomr==($datbl_['idcomune']))
				{
    				print("<td> <input value='".$datbl_['denominazione']."' name='idcomr' size='30' maxlength='30'>");
  				}
			}
				print("</td> </tr>");
   		}		
   		print ("<tr> <td><i>Numero di telefono</i> </td> <td> <input type='text' value='".$dato['telefono']."' name='tel' size='30' maxlength='15'> </td> </tr>");
   		print ("<tr> <td> <i> Numero cellulare </i> </td> <td> <input type='text' value='".$dato['telcel']."' name='cel' size='30' maxlength='15'> </td> </tr>");		
		print ("<tr> <td><i>Indirizzo E-mail</i> </td> <td> <input type='text' value='".$dato['email']."' name='mail' size='30' maxlength='30'> </td> </tr>");
   		$sqc="SELECT * FROM tbl_classi ORDER BY idclasse";
   		$resc=mysqli_query($con,inspref($sqlc));
   		if(!$resc)
   		{
    		print ("<br/> <br/> <br/> <h2>Impossibile visualizzare i dati </h2>");
   		}
   		else
   		{
    		print ("<tr> <td> <i>Classe</i> </td>");
    		while ($datc=mysqli_fetch_array($resc))
    		{
				
				if ($idcla==($datc['idclasse']))
				{
    				print("<td> <input value='".$datc['anno']." ".$datc['sezione']." ".$datc['specializzazione']."' name='classe' size='30' maxlength='30'");
    			}
			} 
    		print("</td> </tr>"); 
   		}
   /*		$sqd="SELECT * FROM tbl_tutori ORDER BY cognome,nome";
   		$resd=mysqli_query($con,inspref($sqld));
   		if(!$resd)
   		{
    		print ("<br/> <br/> <br/> <h2>d Impossibile visualizzare i dati </h2>");
   		}
   		else
   		{
    		print ("<tr> <td> <i>Tutore legale</i> </td>");
    		while ($datd=mysqli_fetch_array($resd))
    		{
				
				if ($idtut==($datd['idtutore']))
				{
    				print("<td> <input value='".$datd['cognome']." ".$datd['nome']."' name='idtut' size='30' maxlength='30'");
    			}
		      
			 }
			 print ("</td> </tr>"); 
   		} */
		print  ("</table> </td> </tr>");
   		print  ("<tr> <td> <input type='submit' value='Cancella'> </form> </td> <td>");
	    print  ("</table>");
	}
  	else   
  	{
   		print("<h2> Dati non trovati </h2>");
  	}
		print(" <form action='vis_alu.php' method='POST'>");
		print ("<input type ='hidden' name='idcla' value='$idcla'>");
		print (" <input type='submit' value=' << Indietro'> </form>  ");
	mysqli_close($con);
    stampa_piede("");
}

