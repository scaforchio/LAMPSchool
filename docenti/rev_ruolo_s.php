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
riceve in ingresso i valori del docente*/
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
	   
	$titolo="Revoca ruolo staff";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
       
	   
	$userid = stringa_html('userid');
	
	
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
	$querysel="select * from tbl_utenti where userid='$userid' and (tipo='S')";
	$result = mysqli_query($con,inspref($querysel));
    $num_tbl_docenti = mysqli_num_rows($result);
	if ($num_tbl_docenti==1)
	{
	   $query="update tbl_utenti set tipo='D' where userid='$userid'";
	
  	  
	   $res=mysqli_query($con,inspref($query));
     
	   print "<center>Variazione effettuata!";
	}
	else  
	   print "<center>Non esiste il docente con lo userid specificato!</center>"; 
	
	stampa_piede("");
	mysqli_close($con);
		

