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


	//Programma per la visualizzazione dell'elenco delle tbl_classi

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
	
	$titolo="Aggiornamento classe";
    $script=""; 
    stampa_head($titolo,"",$script,"MAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_ritcla.php'>ELENCO CLASSI</a> - $titolo","","$nome_scuola","$comune_scuola");
 

	//Connessione al server SQL
	$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
	if(!$con)
	{
		print("\n<h1> Connessione al server fallita </h1>");
		exit;
	};
	
	//Connessione al database
	$DB=true;
	if(!$DB)
	{
		print("\n<h1> Connessione al database fallita </h1>");
		exit;
	};	



		//Prelevamento del testo delle tbl_sezioni e delle tbl_specializzazioni
		$query1="SELECT * FROM tbl_sezioni
					WHERE ". stringa_html('tbl_sezioni'). "=idsezione";		
		$a1=mysqli_query($con,inspref($query1));
		$d1=mysqli_fetch_array($a1);
		
		$query2="SELECT * FROM tbl_specializzazioni	
					WHERE ". stringa_html('spec'). "=idspecializzazione";
		$a2=mysqli_query($con,inspref($query2));
		$d2=mysqli_fetch_array($a2);
					
		//Esecuzione query finale
    	$sql  = "UPDATE tbl_classi SET anno=". stringa_html('anno');
        $sql .= ",sezione='". $d1['denominazione']. "', specializzazione='". $d2['denominazione'];
        $sql .= "', oresett='". stringa_html('ore'). "', idcoordinatore=".stringa_html('coord');
        $sql .= " WHERE idclasse=". stringa_html('idclasse');
		
        if (!($ris=mysqli_query($con,inspref($sql))))
		  {  
	   	 	print("\n<FONT SIZE='+2'> <CENTER>".inspref($sql)."</CENTER> </FONT>");
    	  }
		else 
		{
	   	//	print("\n<FONT SIZE='+2'> <CENTER>Modifica eseguita</CENTER> </FONT>");
	   	print "
                 <form method='post' id='formdoc' action='../classi/vis_ritcla.php'>
                 
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formdoc').submit();
                 }
                 </SCRIPT>";
		}   
		
		stampa_piede("");
	   	mysqli_close($con);
	
	

