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



/*programma per la cancellazione di un docente
riceve in ingresso iddocente*/
	@require_once("../php-ini".$_SESSION['suffisso'].".php");
	@require_once("../lib/funzioni.php");

	
	// istruzioni per tornare alla pagina di login 
	////session_start();
	$a = stringa_html('a');
	$b = stringa_html('b');
   $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
	if ($tipoutente=="")
	   {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
	   }
	
	$titolo="Cancellazione lezione";
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
	$sql="SELECT * from tbl_lezionigruppi
	      where idlezionegruppo=$a ";
	$result=mysqli_query($con,inspref($sql)) or die ("Errore:".inspref($sql,false));
	$Data=mysqli_fetch_array($result);
	if(!($result))
		print("Query fallita");
	else
	{
	 
	   
		
		
		print("<center><font color='red'><br><br><b>Attenzione! La cancellazione della lezione eliminerà anche tutti i voti in essa attribuiti!</b></font><br><br>");
	   print("<FORM NAME='CAN' ACTION='vis_lez_gru.php' method='POST'>");
	   print("<input type='hidden' name='iddocente' value='$b'>");
	   print("<INPUT TYPE='SUBMIT' VALUE='    Annulla    '><br>");
	   print("</FORM><br/>");
	   print("<FORM NAME='CA2' ACTION='ca2_lez_gru.php' method='POST'>");
	   print("<INPUT TYPE='hidden' name='idlezione' value='$a'>");
	   print("<INPUT TYPE='hidden' name='iddocente' value='$b'>");
	   print("<INPUT TYPE='SUBMIT' VALUE='    Conferma   '>");
	   print("</FORM>"); 
	
	   
	}
	
	stampa_piede("");
	mysqli_close($con);

