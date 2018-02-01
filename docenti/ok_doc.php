<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
GNU Affero General Public License come pubblicata 
dalla Free Software Foundation; sia la versione 3, 
sia (a vostra scelta) ogni versione successiva.

Questo programma é distribuito nella speranza che sia utile 
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
	   
	$titolo="Inserimento docente";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_doc.php'>ELENCO DOCENTI</a> - $titolo","","$nome_scuola","$comune_scuola");
       
	$iddocente = stringa_html('codice');
	$cognome = stringa_html('cognome');
	//print $cognome;
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
	// $query="insert into tbl_docenti (cognome,nome)values ('$cognome','$nome')";
	
	// VERIFICO SE E' IL PRIMO DOCENTE, IN QUESTO CASO AGGIUNGO 1000000 ALL'IDDOCENTE
	
	$que="select * from tbl_docenti where iddocente>1000000000";
	$res=mysqli_query($con,inspref($que));
	if (mysqli_num_rows ( $res) == 0)
	{
	    $iddocente=1000000001;
	    
	    $query="insert into tbl_docenti (iddocente,cognome,nome,datanascita,idcomnasc,indirizzo,idcomres,telefono,telcel,email,sostegno,idutente) values ('$iddocente','$cognome','$nome','$aa-$mm-$gg','$comnasc','$indirizzo','$comresi','$telefono','$cellulare','$email','$sostegno','$iddocente')";
	}
	else
	    $query="insert into tbl_docenti (iddocente,cognome,nome,datanascita,idcomnasc,indirizzo,idcomres,telefono,telcel,email,sostegno) values ('$iddocente','$cognome','$nome','$aa-$mm-$gg','$comnasc','$indirizzo','$comresi','$telefono','$cellulare','$email','$sostegno')";
    $err=0;
	$b=0;
	$flag=0;
	$mes=""; 
	 
	if ($cognome=="")
	{
		$err=1;
		$mes="Il cognome non è stato inserito <br/>";	
	}
	else
	{
		if (controlla_stringa($cognome)==1)
		{
			$err=1;
			$mes="Il cognome non può contenere valori numerici <br/>";	
		}
	}
		
	if ($nome=="")
	{
		$err=1;
		$mes=$mes." Il nome non è stato inserito <br/>";	
	}
	else
	{
		if (controlla_stringa($nome)==1)
		{
			$err=1;
			$mes="Il nome non può contenere valori numerici <br/>";	
		}
	}
/**
	if (!$datadinascg)
	{
		$err=1;
		$mes=$mes."Il giorno di nascita non � stato inserito <br/>";	
	}
	else
	{
		if (is_numeric($datadinascg)==false)
		{
			$err=1;
			$mes=$mes."Il giorno di nascita pu� contenere solo valori numerici <br/>";	
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
		$mes=$mes." Il mese di nascita non � stato inserito <br/>";	
	}
	else
	{
		if (is_numeric($datadinascm)==false)
		{
		$err=1;
		$mes=$mes." Il mese di nascita pu� contenere solo valori numerici <br/>";	
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
		$mes=$mes." L'anno di nascita non � stato inserito <br/>";	
	}
	else
	{
		if (is_numeric($datadinasca)==false)
		{
		$err=1;
		$mes=$mes." L'anno di nascita pu� contenere solo valori numerici <br/>";	
	 	}

	}
	
	if (!$idcomn)
	{
		$err=1;
		$mes=$mes." Il comune di nascita non � stato inserito <br/>";	
	}
	if (!$indirizzo)
	{
		$err=1;
		$mes=$mes." L'indirizzo non � stato inserito <br/>";	
	}
	if (!$idcomr)
	{
		$err=1;
		$mes=$mes."Il comune di residenza non � stato inserito <br/>";	
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
			$mes=$mes." Il telefono pu� contenere solo valori numerici <br/>";	
	 		}

		}
		if ($app1==0)
		{
		
			if (is_numeric($telcel)==false)
			{
			$err=1;
			$mes=$mes." Il cellulare pu� contenere solo valori numerici <br/>";	
	 		}
			}
	}
	*/
	if($err==1) 
	{
        print("<center><font size='3' color='red'><b>Correzioni:</b></font><br/>");
        print("$mes");
        print("<br/><form NAME='hid' action='ins_doc.php' method='POST'>");

        print(" <input type ='hidden' size='20' name='codi' value= '$iddocente'>");
        print(" <input type ='hidden' size='20' name='cog' value= '$cognome'>");
        print(" <input type ='hidden' size='20' name='no' value= '$nome'>");

        print(" <input type ='hidden' size='2'maxlength='2' name='datag' value=$gg><input type ='hidden' size='2' maxlength='2'name='datam' value=$mm><input type ='hidden' size='4' maxlength='4'name='dataa' value=$aa>");
        print(" <input type ='hidden' size='20' name='idcomn' value= '$comnasc'>");
        print(" <input type ='hidden' size='20' name='idcomr' value= '$comresi'>");

        print(" <input type ='hidden' size='20' name='ind' value= '$indirizzo'> ");
        print("  <input type ='hidden' size='20' name='tel' value= '$telefono'>");
        print(" <input type ='hidden' size='20' name='telc' value= '$cellulare'>");
        print(" <input type ='hidden' size='20' name='em' value= '$email'>");
        print(" <input type ='hidden' size='20' name='flag' value= '1'>");
        print("<INPUT TYPE='SUBMIT' VALUE='<< Indietro'>");	
        print("</form></center>");

	}
	else
	{
        $res=mysqli_query($con,inspref($query));
	
        if (!$res)
        {
            print("<h2>Il docente non &eacute; stato inserito</h2>$query");
        }
        else
        {
            $iddocenteinserito=mysqli_insert_id($con);
            // Aggiorno l'idutente del docente
            $query="update tbl_docenti set idutente=$iddocenteinserito where iddocente=$iddocenteinserito";
            if (!$res=mysqli_query($con,inspref($query))) die("Errore aggiornamento id utente del docente!");
            // INSERISCO ANCHE IL RECORD NELLA TABELLA DEGLI tbl_utenti
            $utente="doc".($iddocenteinserito-1000000000);
            $password=creapassword();
            $sqlt="insert into tbl_utenti(idutente,userid,password,tipo) values ('$iddocenteinserito','$utente',md5('".md5($password)."'),'D')";
            $res=mysqli_query($con,inspref($sqlt));

            // print "risultato inserimento $iddocenteinserito<br/>"; 
            print "<FONT SIZE='+2'><CENTER>Inserimento eseguito</CENTER></FONT>";
            print "<p align='center'>Dati di autenticazione per $nome $cognome";
            print "<br/>Utente: $utente<br/>Password:$password </p>";
            print "<br><br><center>";
            print "<form target='_blank' name='stampa' action='stampa_pass_doc.php' method='POST'>
                   <input type='hidden' name='iddoc1' value='$iddocenteinserito'> 
                   <input type='hidden' name='utdoc1' value='$utente'> 
                   <input type='hidden' name='pwdoc1' value='$password'> 
                   <input type='hidden' name='numpass' value='1'> 
                   
                   <input type='submit' value='STAMPA'>
                   </form>";
            print "</center>";
        }
	}
	
	mysqli_close($con);
	stampa_piede("");

