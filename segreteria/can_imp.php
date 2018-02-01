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



/*programma per la cancellazione di un amministrativo
riceve in ingresso idamministrativo*/
	@require_once("../php-ini".$_SESSION['suffisso'].".php");
	@require_once("../lib/funzioni.php");

	
	// istruzioni per tornare alla pagina di login 
	////session_start();
	$a = stringa_html('a');
    $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
	if ($tipoutente=="")
	   {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
	   }
	
	$titolo="Cancellazione amministrativo";
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
	$sql="SELECT * from tbl_amministrativi where idamministrativo=$a";
	$result=mysqli_query($con,inspref($sql));
	$Data=mysqli_fetch_array($result);
	if(!($result))
		print("Query fallita");
	else
	{
	$sql1="SELECT denominazione FROM tbl_comuni WHERE idcomune=".$Data['idcomnasc']."";
	$result1=mysqli_query($con,inspref($sql1));
	$Data1=mysqli_fetch_array($result1);
	$sql2="SELECT denominazione FROM tbl_comuni WHERE idcomune=".$Data['idcomres']."";
	$result2=mysqli_query($con,inspref($sql2));
	$Data2=mysqli_fetch_array($result2);
	
	print("<CENTER>");
	
	print("<FORM NAME='CA2' ACTION='ca2_imp.php' method='POST'>");
		print("\n<table>\n");
		
		print("<tr>\n\t<td align='right'> Cognome</td>\n\t");
		print(" <td align='left'><b> ".$Data['cognome']."</td>\n</tr>\n");
		print("<tr>\n\t<td align='right'> Nome</td>\n\t");
		print(" <td align='left'><b> ".$Data['nome']."</td>\n</tr>\n");
		print("<tr>\n\t<td align='right'> Data di nascita</td>\n\t");
		$d=$Data['datanascita'];
	 	$gg=substr($d,8,2);
	 	$mm=substr($d,5,2);
	 	$aa=substr($d,0,4);
	 	print("<td align='left'><b> $gg/$mm/$aa</td>\n</tr>\n");
		print("<tr>\n\t<td align='right'> Comune di nascita</td>\n\t");
		print(" <td align='left'><b> ".$Data1['denominazione']."</td>\n</tr>\n");
		print("<tr>\n\t<td align='right'> Indirizzo</td>\n\t");
		print(" <td align='left'><b> ".$Data['indirizzo']."</td>\n</tr>\n");
		print("<tr>\n\t<td align='right'> Comune di residenza</td>\n\t");
		print(" <td align='left'><b> ".$Data2['denominazione']."</td>\n</tr>\n");
		print("<tr>\n\t<td align='right'> Telefono</td>\n\t");
		print(" <td align='left'><b> ".$Data['telefono']."</td>\n</tr>\n");
		print("<tr>\n\t<td align='right'> Cellulare</td>\n\t");
		print(" <td align='left'><b> ".$Data['telcel']."</td>\n</tr>\n");
		print("<tr>\n\t<td align='right'> Email</td>\n\t");
		print(" <td align='left'><a href='mailto:".$Data['email']."'><b> ".$Data['email']."</a></td>\n</tr>\n");
		print("<tr><td></td></tr><td></td><tr><td></td></tr><tr></tr><tr><td colspan='2'> <center>Sei sicuro di voler eliminare il amministrativo ?</center></td></tr>");
			}
			
	print("</table>");
	
	print("<br><table><INPUT TYPE='hidden' name='al' value='$a'>");
	print("<tr><td align='left'><INPUT TYPE='SUBMIT' VALUE='    SI    '></td><td colspan='2'></td>");
	print("\n</FORM>"); 
	
	print("<FORM NAME='CAN' ACTION='vis_imp.php' method='POST'>");
	print("<td align='right'><INPUT TYPE='SUBMIT' VALUE='    NO    '></td></tr></table>");
	print("\n</FORM>");
	
	stampa_piede("");
	mysqli_close($con);

