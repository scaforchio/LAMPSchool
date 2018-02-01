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

	//Programma per la modifica dell'elenco delle tbl_classi

	@require_once("../php-ini".$_SESSION['suffisso'].".php");
	@require_once("../lib/funzioni.php");
	
	$tipo=stringa_html('tipo');
	$iddocumento=stringa_html('iddocumento');
	$idclasse=stringa_html('idclasse'); 

	// istruzioni per tornare alla pagina di login 
	////session_start();
    $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
	if ($tipoutente=="")
	   {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
	   }	
	
	$titolo="Modifica descrizione documento";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
   

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

	//Esecuzione query
    $sql="select descrizione,pei from tbl_documenti where iddocumento=". $iddocumento;
	if (!($ris=mysqli_query($con,inspref($sql))))
	{  
	    print("\n<h1> Query fallita </h1>");
		exit;
    }
	else 
	{
	   $dati=mysqli_fetch_array($ris) ;	   
       print "<form action='agg_doc_classe.php' method='POST'>";
	    print "<input type='hidden' name='iddocumento' value='".$iddocumento."'>";
	    print "<input type='hidden' name='tipo' value='".$tipo."'>";
	    print "<input type='hidden' name='idclasse' value='".$idclasse."'>";

	    print "<CENTER><table border='0'>";
	    print "<tr><td ALIGN='CENTER'> Descrizione </td> <td ALIGN='CENTER'> 
	                 <input type='text' maxlength=255 size=50 name='descrizione' value='".$dati['descrizione']."'></td></tr>"; 
	  

		 print "<td COLSPAN='2' ALIGN='CENTER'><input type='submit' value='Aggiorna'></td> ";
		 print "</form></tr>";
		     
	    print "</table></CENTER>";   
	} 
	stampa_piede("");
	mysqli_close($con);

