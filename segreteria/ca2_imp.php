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
riceve in ingresso i dati del amministrativo*/
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
	   
	$titolo="Cancellazione amministrativo";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_imp.php'>ELENCO amministrativi</a> - $titolo","","$nome_scuola","$comune_scuola");
    
	   
	   
	$al = stringa_html('al');
	$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
	if(!$con)
		{print("<H1>connessione al server mysql fallita</H1>");
	 	exit;
		}
	$DB=true;
	if(!$DB)
		{print("<H1>connessione al database fallita</H1>");
		 exit;
		}
	$f="DELETE FROM tbl_amministrativi WHERE idamministrativo='$al'";
	$res=mysqli_query($con,inspref($f)) or die ("Cancellazione fallita!");
	$f="DELETE FROM tbl_utenti WHERE idutente='$al'";
	$res=mysqli_query($con,inspref($f)) or die ("Cancellazione fallita!");
	
	print("<Center>CANCELLAZIONE EFFETTUATA!</Center>");
	stampa_piede("");
	mysqli_close($con);

