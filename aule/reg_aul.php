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

	//Programma per la visualizzazione dell'elenco delle tbl_aule

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
	
	$titolo="Registrazione nuova classe";
    $script=""; 
    stampa_head($titolo,"",$script,"MPA");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_aul.php'>ELENCO CLASSI</a> - $titolo","","$nome_scuola","$comune_scuola");
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

	//Esecuzione controlli
	$errore = 0;
    $mes = '';
    $anno = stringa_html('anno');
    $sezione = stringa_html('tbl_sezioni');
    $spec = stringa_html('spec');
    $ore = stringa_html('ore');
    
    if ($anno == '0')
    {
        $errore = 1;
        $mes .= "L'anno non &egrave; stato inserito<br/>";
    }
    
    if ($sezione == '0')
    {
        $errore = 1;
        $mes .= "La sezione non &egrave; stata inserita<br/>";
    }

    if ($ore == '')
    {
        $errore = 1;
        $mes .= "Le ore non sono state inserite<br/>";
    }

    if ($errore == 0)
	{
		//Prelevamento del testo delle tbl_sezioni e delle tbl_specializzazioni
		$query1="SELECT * FROM tbl_sezioni WHERE $sezione=idsezione";		
		$a1=mysqli_query($con,inspref($query1));
		$d1=mysqli_fetch_array($a1);
		
		$query2="SELECT * FROM tbl_specializzazioni	WHERE $spec=idspecializzazione";
		$a2=mysqli_query($con,inspref($query2));
		$d2=mysqli_fetch_array($a2);
					
		$quella  = "SELECT * FROM tbl_aule WHERE anno='". stringa_html('anno');
		$quella .= "' AND sezione='".$d1['denominazione']."' AND specializzazione='".$d2['denominazione']."'";
        
		if (($ques=mysqli_query($con,inspref($quella))))
		{  
	   	 	if (mysqli_num_rows($ques)>0)
			{
				//Errore di campo esistente
				print("\n<FONT SIZE='+2'> <CENTER>Attenzione classe gi&agrave; esistente</CENTER></FONT>");
				print "<CENTER><FORM ACTION='nuo_aul.php' method='POST'>";
				print "<INPUT TYPE='SUBMIT' VALUE='<< Indietro'>";
				print "</CENTER></FORM>";
			}
			else
			{
				//Esecuzione query finale
    			$sql  = "INSERT INTO tbl_aule (anno,sezione,specializzazione,oresett) VALUES ";
                $sql .= "('$anno','".$d1['denominazione']."','".$d2['denominazione']."','$ore')";

				if (!($ris=mysqli_query($con,inspref($sql))))
				{  
	   	 			print("\n<FONT SIZE='+2'> <CENTER>Inserimento non eseguito </CENTER></FONT>");
    			}
				else 
				{
	   				// print("\n<FONT SIZE='+2'> <CENTER>Inserimento eseguito</CENTER> </FONT>");	
	   				print "
                 <form method='post' id='formdoc' action='../classi/vis_aul.php'>
                 
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formdoc').submit();
                 }
                 </SCRIPT>";
				}   
				
			}
    	}
		else 
		{
	   		print("\n<FONT SIZE='+2'><CENTER>Query di verifica fallita</CENTER> </FONT>");
            displayFormIndietro();
		}   
			
	}
    else
    {
   		print "<center><h3> Correzioni: </h3>";
		print $mes;
        print "</center><br/>";
        displayFormIndietro();
    }
    stampa_piede("");
    mysqli_close($con);

function displayFormIndietro()
{
    print "<CENTER><FORM ACTION='nuo_aul.php' method='POST'>";
    print "<INPUT TYPE='SUBMIT' VALUE='<< Indietro'>";
    print "</FORM></CENTER>";
}


