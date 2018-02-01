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


/*programma per la modifica dei tbl_docenti
riceve in ingresso i dati del docente*/


// istruzioni per tornare alla pagina di login 
	////session_start();
    $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
	if ($tipoutente=="")
	   {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
	   }
	   
	   
	@require_once("../php-ini".$_SESSION['suffisso'].".php");
	@require_once("../lib/funzioni.php");
	
	$titolo="Modifica docente";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_doc.php'>ELENCO DOCENTI</a> - $titolo","","$nome_scuola","$comune_scuola");
    
	
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
	
	$err=0;
	$b=0;
	$flag=0;
	$mes="";  
	$iddocente = stringa_html('codice');
	$cognome = stringa_html('cognome');
	$nome = stringa_html('nome');
	$aa = stringa_html('datadinasca');
	$gg = stringa_html('datadinascg');
	$mm = stringa_html('datadinascm');
	$comnasc = stringa_html('idcomn');
	$indirizzo = stringa_html('indirizzo');
	$comresi = stringa_html('idcomr');
	$email = stringa_html('email');
	$sostegno = stringa_html('sostegno');
	$telefono = stringa_html('telefono');
	$cellulare = stringa_html('telcel');
   $s="UPDATE tbl_docenti SET cognome='$cognome',nome='$nome',datanascita='$aa-$mm-$gg',idcomnasc='$comnasc',indirizzo='$indirizzo',idcomres='$comresi',telefono='$telefono',telcel='$cellulare',email='$email',sostegno='$sostegno' WHERE iddocente=$iddocente";
	
	if (!$cognome)
	{
		$err=1;
		$mes="Il cognome non &egrave; stato inserito <br/>";	
	}
	else
	{
		if (controlla_stringa($cognome)==1)
		{
			$err=1;
			$mes="Il cognome non pu&ograve; contenere valori numerici <br/>";	
		}
	}
		
	if (!$nome)
	{
		$err=1;
		$mes=$mes." Il nome non &egrave; stato inserito <br/>";	
	}
	else
	{
		if (controlla_stringa($nome)==1)
		{
			$err=1;
			$mes="Il nome non pu&ograve; contenere valori numerici <br/>";	
		}
	}
	
	/*
	if (!$datadinascg)
	{
		$err=1;
		$mes=$mes."Il giorno di nascita non &egrave; stato inserito <br/>";	
	}
	else
	{
		if (is_numeric($datadinascg)==false)
		{
		$err=1;
		$mes=$mes."Il giorno di nascita pu&ograve; contenere solo valori numerici <br/>";	
		}
	else
		{
		if (($datadinascg<1) or ($datadinascg>31))
			{
			$err=1;
			$mes=$mes." Il giorno di nascita deve essere compreso tra 1 e 31 <br/>";	
			}
		else
			{
				if ((($datadinascm==4) or ($datadinascm==6) or ($datadinascm==9) or ($datadinascm==11)) and ($datadinascg>30)) 
				{
					$err=1;
					$mes=$mes." Il giorno di nascita deve essere compreso tra 1 e 30 <br/>";
				}
				else
				{
					if (($datadinascm==2) and ($datadinascg>29))
					{
					$err=1;
					$mes=$mes." Il giorno di nascita deve essere compreso tra 1 e 29 <br/>";
					}
	  			}
			}
		}
	}
	if (!$datadinascm)
	{
		$err=1;
		$mes=$mes." Il mese di nascita non &egrave; stato inserito <br/>";	
	}
	else
	{
		if (is_numeric($datadinascm)==false)
		{
		$err=1;
		$mes=$mes." Il mese di nascita pu&ograve; contenere solo valori numerici <br/>";	
		}
	else
		{
		if (($datadinascm>12) or ($datadinascm<1))
			{
			$err=1;
			$mes=$mes." Il mese di nascita deve essere compreso tra 1 e 12 <br/>";	
			}
		}
	}
	if (!$datadinasca)
	{
		$err=1;
		$mes=$mes." L'anno di nascita non &egrave; stato inserito <br/>";	
	}
	else
	{
		if (is_numeric($datadinasca)==false)
		{
		$err=1;
		$mes=$mes." L'anno di nascita pu&ograve; contenere solo valori numerici <br/>";	
	 	}

	}
	if (!$idcomn)
	{
		$err=1;
		$mes=$mes." Il comune di nascita non &egrave; stato inserito <br/>";	
	}
	if (!$indirizzo)
	{
		$err=1;
		$mes=$mes." L'indirizzo non &egrave; stato inserito <br/>";	
	}
	if (!$idcomr)
	{
		$err=1;
		$mes=$mes."Il comune di residenza non &egrave; stato inserito <br/>";	
	}
	IF (!$telefono)
	{
	$app=1;
	}
	IF (!$telcel)
	{
	$app1=1;
	}
	if (($app==1)and($app1==1))
	{
		$err=1;
		$mes=$mes."Inserire il telefono o il cellulare <br/>";	
	}
	else
	{	
		if ($app==0)
		{
			if (is_numeric($telefono)==false)
			{
			$err=1;
			$mes=$mes." Il telefono pu&ograve; contenere solo valori numerici <br/>";	
	 		}

		}
		if ($app1==0)
		{
		
			if (is_numeric($telcel)==false)
			{
			$err=1;
			$mes=$mes." Il cellulare pu&ograve; contenere solo valori numerici <br/>";	
	 		}
			}
	}
	*/
	if($err==1) 
	{
	   print("<center><font size='3' color='red'><b>Correzioni:</b></font></center>");
	   print("$mes");
	   print("<FORM NAME='hid' action='mod_doc.php' method='POST'>");
       
		print(" <input type ='hidden' size='20' name='codi' value= '$codice'>");
		print(" <input type ='hidden' size='20' name='cog' value= '$cognome'>");
		print(" <input type ='hidden' size='20' name='no' value= '$nome'>");
		print(" <input type ='hidden' size='2' maxlength='2' name='datag' value=$gg><input type ='hidden' size='2' maxlength='2'name='datam' value=$mm><input type ='hidden' size='4' maxlength='4'name='dataa' value=$aa>");
   	print(" <input type ='hidden' size='20' name='idcomn' value= '$idcomn'>");
   	print(" <input type ='hidden' size='20' name='ind' value= '$indirizzo'> ");
		print(" <input type ='hidden' size='20' name='idcomr' value= '$idcomr'>");
		print("  <input type ='hidden' size='20' name='tel' value= '$telefono'>");
		print(" <input type ='hidden' size='20' name='telc' value= '$telcel'>");
		print(" <input type ='hidden' size='20' name='em' value= '$email'>");
		print(" <input type ='hidden' size='20' name='flag' value= '1'>");
			
      print("<INPUT TYPE='SUBMIT' VALUE='<< Indietro'>");	
      print("</form><br/>");
	}
	else
	{
        print "\n<FONT SIZE='+2'><CENTER>";
                    
        if ($res=mysqli_query($con,inspref($s)))
            print "Modifica eseguita";
        else
	        print "ERRORE NELLA MODIFICA DEI DATI DEL DOCENTE!";
        
        print "</CENTER></FONT>";
	}
	
	mysqli_close($con);
	stampa_piede("");

