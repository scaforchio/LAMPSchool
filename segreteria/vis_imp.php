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

// programma per la visualizzazione dei amministrativi

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
	$modo=stringa_html('modo');
	$titolo="Elenco amministrativi";
    $script=""; 
    
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
    
	
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
	$sql="SELECT * FROM tbl_amministrativi ,tbl_utenti
       WHERE tbl_amministrativi.idamministrativo=tbl_utenti.idutente 
       ORDER BY cognome,nome";
	$result=mysqli_query($con,inspref($sql));
	if(!($result))
		print("query fallita".mysqli_error($con));
	else {
		
		print("<FORM NAME='VI2' ACTION='ins_imp.php' method='POST'>");
		
		print("<CENTER>\n<table border=1>\n");
		
		print("<tr class='prima'><td>Cognome Nome</td>
		<td>Data di nascita</td>
		<td>Id. Utente</td>
		<td>Telefono</td>
		<td>Email</td>
		
		<td>Azione</td></tr>\n");
		
	$w=mysqli_num_rows($result);
      
	if ($w>0)
	{
	while($Data=mysqli_fetch_array($result))
	{
	$cn=$Data['idcomnasc'];	
	$sql1="SELECT denominazione as den1 FROM tbl_comuni WHERE idcomune=$cn";	
	$result1=mysqli_query($con,inspref($sql1));
	$Data1=mysqli_fetch_array($result1);
    $cr=$Data['idcomres'];	
	$sql2="SELECT denominazione as den2 FROM tbl_comuni WHERE idcomune=$cr";	
	
	if($result2=mysqli_query($con,inspref($sql2)))
	    $Data2=mysqli_fetch_array($result2);
	
            print("<tr class='oddeven'><td>".$Data['cognome']."  " .$Data['nome']."</td>\n");
	 $d=$Data['datanascita'];
	 $gg=substr($d,8,2);
	 $mm=substr($d,5,2);
	 $aa=substr($d,0,4);
            print("<td align='center'>$gg/$mm/$aa</td>\n");
            print("<td align='center'>".$Data['userid']."</td>");
            
	  if ($Data['telefono']) 
     {
         print("<td align='center'>".$Data['telefono'] ."</td>");
     }
	  else
     {
         print("<td align='center'>".$Data['telcel'] ."</td>");
     }
	  print("<td align='center'><a href='mailto:".$Data['email']."'> ".$Data['email']."</a></td>\n");
     
     print "<td align='left'>";       
     if ($modo!='vis')   
     {    
        print("<a href='mod_imp.php?a=".$Data['idamministrativo']."'><img src='../immagini/edit.png' title='Modifica'></a>");
        print("&nbsp;<a href='can_imp.php?a=".$Data['idamministrativo']."'><img src='../immagini/delete.png' title='Elimina'></a>");
	  }

        if ($tipoutente == 'P')
        {
            print("&nbsp;&nbsp;&nbsp;<a href='../contr/cambiautenteok.php?nuovoutente=" . $Data['userid'] . "'><img src='../immagini/alias.png' title='Assumi identità'></a>");
        }
     print("</td></tr>\n"); 
        }
	}
	else
	{
         print("<tr><td align='center' colspan='6'>Nessun amministrativo trovato</td></tr>");
	}
	print("</TABLE>\n<br/>\n");
	if ($modo!='vis') print("<INPUT TYPE='SUBMIT' VALUE='Inserisci nuovo amministrativo'>");
	}
	print("</CENTER></FORM>");
		
	stampa_piede("");	
	mysqli_close($con);

