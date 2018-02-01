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




/*pagina di conferma per la cancellazione di un alunno
parametri di ingresso e di uscita: codice della classe, codice dell'alunno*/							
 
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
 	
$titolo="Cancellazione alunni";
$script=""; 
stampa_head($titolo,"",$script,"MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Cancellazione alunni","","$nome_scuola","$comune_scuola");
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
		print("<center> <b> SEI SICURO DI VOLER CANCELLARE I SEGUENTI DATI? </b> </center>");
		
		print ("<center>");
   		print ("\n \t <table>");
		
   		print   ("<tr> <td align='right'><i> Cognome:</i> </td> <td align='left'><b>".$dato['cognome']."</b></td> </tr>");
   		print   ("<tr> <td align='right'><i>Nome:</i> </td> <td align='left'><b> ".$dato['nome']."</b> </td> </tr>");
   		$gg=substr($dato['datanascita'],8,2);
   		$mm=substr($dato['datanascita'],5,2);
   		$aa=substr($dato['datanascita'],0,4);
		$idcomn=$dato['idcomnasc'];
		$idcomr=$dato['idcomres'];
		$idcla=$dato['idclasse'];
		$idtut=$dato['idtutore'];
   		print ("<tr> <td align='right'> <i> Data di nascita: </i> </td> <td align='left'><b> ".$gg."/" .$mm."/".$aa." </b></td> </tr>");
   		$sqlc="SELECT * FROM tbl_classi ORDER BY idclasse";
   		$resc=mysqli_query($con,inspref($sqlc));
   		if(!$resc)
   		{
    		print ("<br/> <br/> <br/> <h2> c Impossibile visualizzare i dati </h2>");
   		}
   		else
   		{
    		print ("<tr> <td align='right'> <i>Classe:</i> </td>");
    		while ($datc=mysqli_fetch_array($resc))
    		{
				
				if ($idcla==($datc['idclasse']))
				{
    				print("<td align='left'><b>".$datc['anno']." ". $datc['sezione']." ". $datc['specializzazione']);
    			}
			} 
    		print("</b></td> </tr>"); 
   		}
   		print  ("</table> ");
		print ("</center>");
		print ("<center>");
		print("<br/>");
		print ("<table>");
		print("<tr> <td>");
		print ("<form action=alu_canc.php method='POST'>");
		print("<input type='hidden' value='".$dato['idalunno']."' name='idal'>");
		print ("<input type='hidden' value='".$dato['cognome']."' name='cognome'>");
		print ("<input type='hidden' value='".$dato['nome']."' name='nome'>");
		print ("<input type='hidden' value='$aa' name='aa'>");
		print ("<input type='hidden' value='$mm' name='mm'>");
		print ("<input type='hidden' value='$gg' name='gg'>");
		print ("<input type='hidden' value='$idcomn' name='idcomn'>");
		print ("<input type='hidden' value='".$dato['indirizzo']."' name='indirizzo'>");
		print ("<input type='hidden' value='$idcomr' name='idcomr'>");
		print ("<input type='hidden' value='".$dato['telefono']."' name='tel'>");
		print ("<input type='hidden' value='".$dato['telcel']."' name='cel'>");
   		print ("<input type='hidden' value='".$dato['email']."' name='mail'>");
		print ("<input type='hidden' value='$idcla' name='idcla'>");
		print ("<input type='hidden' value='$idtut' name='idtut'>");		
		print  (" <input type='submit' value='      Si      '>  </form> </td>");
		print ("<td>");
		print("<form action='vis_alu.php' method='POST'>");
		print ("<input type='hidden' name='idcla' value='$idcla'>");
		print(" <input type='submit' value='      No     '>  </form>");
		print ("</td> </tr>");
		print("  </table>");
	}
  	else   
  	{
   		print("<h2> Dati non trovati </h2>");		
  	}
	
}

mysqli_close($con);
stampa_piede("");
   

