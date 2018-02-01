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
	$email=stringa_html('email');
	$telcel=stringa_html('telcel');
        $nummaxcolloqui=stringa_html('nummaxcolloqui');
	$titolo="Aggiornamento dati di contatto docente";
    $script=""; 
    stampa_head($titolo,"",$script,"DS");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

	//Connessione al server SQL
	$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
	
    $sql  = "update tbl_docenti SET email='$email', telcel='$telcel',nummaxcolloqui='$nummaxcolloqui' WHERE iddocente=".$_SESSION['idutente'];
	$ris=mysqli_query($con,inspref($sql)) or die("errore:".inspref($sql));
	print "
		
		  <center><b>Aggiornamento effettuato!</b></center> 
        <form method='post' id='formcont' action='../login/ele_ges.php'>
            <center><br><input type='submit' value='OK'></center>
        </form>
        ";
		  
		
	stampa_piede("");
	mysqli_close($con);
	
	

