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
riceve in ingresso i dati del docente*/
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
	   
	$titolo="Cancellazione lezione sostegno";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_lez_cert.php'>ELENCO LEZIONI</a> - $titolo","","$nome_scuola","$comune_scuola");
    
	   
	   
	$idlezione = stringa_html('idlezione');
	$iddocente = stringa_html('iddocente');
	$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
	if(!$con)
		{print("<H1>connessione al server mysql fallita</H1>");
	 	exit;
		}
	
	
	
	   // ELIMINO FIRMA DA LEZIONE
	   // Estraggo dati della lezione
	   $query="select idclasse,idmateria,orainizio,numeroore,iddocente,datalezione from tbl_lezionicert
	           where idlezione=$idlezione";
	   $ris=mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query));
	   // print ("tttt ".inspref($query));
	   $rec=mysqli_fetch_array($ris);
	   $iddocente=$rec['iddocente'];
	/*   $query="select tbl_lezioni.idlezione from tbl_lezioni
	           where tbl_lezioni.idclasse=".$rec['idclasse'].
	           " and tbl_lezioni.idmateria=".$rec['idmateria'].
	           " and tbl_lezioni.orainizio=".$rec['orainizio'].
	           " and tbl_lezioni.numeroore=".$rec['numeroore'].
	           " and tbl_lezioni.datalezione='".$rec['datalezione']."'"; */
	     $query="select idlezione from tbl_lezioni
	           where idclasse=".$rec['idclasse'].
	           " and idmateria=".$rec['idmateria'].
	           " and orainizio>=".$rec['orainizio'].
	           " and (orainizio+numeroore-1)<=(".$rec['orainizio']."+".$rec['numeroore']."-1)".
	           " and datalezione='".$rec['datalezione']."'";      
	   $ris=mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query));
	   // print ("tttt ".inspref($query));
	   while ($rec=mysqli_fetch_array($ris))
	   {
			$idleznorm=$rec['idlezione'];
			
			if ($idleznorm!="")
			{
				$query="delete from tbl_firme
						  where idlezione=$idleznorm and iddocente=$iddocente";
				$ris1=mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query));
				
				// VERIFICO SE CI SONO ALTRE FIRME ALTRIMENTI CANCELLO LA LEZIONE
				$query="select * from tbl_firme
						  where idlezione=$idleznorm";
				$ris2=mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query));
				if (mysqli_num_rows($ris2)==0)
				{         
					 $query="delete from tbl_lezioni
								where idlezione=$idleznorm";
					 $ris3=mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query));
				}
			}			
	   }
		$f="DELETE FROM tbl_lezionicert WHERE idlezione='$idlezione'";
	   $ris=mysqli_query($con,inspref($f)) or die ("Cancellazione lezione fallita!");
	
	//header("location: ../lezioni/vis_lez.php?iddocente=$iddocente"); 
	print "
             <form method='post' id='formlez' action='../lezioni/vis_lez_cert.php'>
             <input type='hidden' name='iddocente' value='$iddocente'>
             </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formlez').submit();
        }
        </SCRIPT>";/* */
	stampa_piede("");
	mysqli_close($con);

