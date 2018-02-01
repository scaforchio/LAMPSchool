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
	
    $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
	if ($tipoutente=="")
	   {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
	   }
	
	$titolo="Conferma cancellazione preside";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
    
	print ("<br><center><b>Confermi cancellazione utenza del preside? (Per riattivarla bisognerà reinserirla)</b></center><br>");
	
	print("<FORM NAME='CA2' ACTION='can_pre.php' method='POST'>");
		
	print("<br><table align='center'>");
	print("<tr><td align='left'><INPUT TYPE='SUBMIT' VALUE='    SI    '></td><td colspan='2'></td>");
	print("</FORM>"); 
	
	print("<FORM NAME='CAN' ACTION='../login/ele_ges.php' method='POST'>");
	print("<td align='right'><INPUT TYPE='SUBMIT' VALUE='    NO    '></td></tr></table>");
	print("</FORM>");
	
	stampa_piede("");
	

