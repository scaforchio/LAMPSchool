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

	/*Programma per la visualizzazione dell'elenco delle tbl_classi.*/

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
	
	$titolo="Inserimento nuova classe";
    $script=""; 
    stampa_head($titolo,"",$script,"MAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_cla.php'>ELENCO CLASSI</a> - $titolo","","$nome_scuola","$comune_scuola");
 
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
	print "<form name='form1' action='reg_cla.php' method='POST'>";
	print "<CENTER><table border ='0'>"; 
	print "<tr> <td>"; 
	print "<tr><td ALIGN='CENTER'> Anno </td> <td> <SELECT name='anno'>"; 
		  print "<option value='0'>Anno";
		  for ($i=1;$i<=$numeroanni;$i++)
		     print "<option value='$i'>$i";
		  
		  print "</td></tr>"; 
		  
		//TABELLA SEZIONE nome=tbl_sezioni		  
		  print "<tr><td   ALIGN='CENTER'> Sezione </td>";   
	      $q1="select * from tbl_sezioni ORDER BY denominazione";
		  if (!($reply=mysqli_query($con,inspref($q1))))
		  {
		  	print "<td>Query fallita nelle tbl_sezioni</td>";
		  }
		  else
		  {
		  		print "<td> <SELECT NAME='tbl_sezioni'>";				
				print "<option  value=0> Sezione";
				//Controlla se esiste le tbl_sezioni e stampa l'elenco altrimenti da l'errore
				if (mysqli_num_rows($reply)>0)
				{
					while ($d1=mysqli_fetch_array($reply)) 
					{
						print "<option  value='".$d1['idsezione']."'> ".$d1['denominazione']."";	
					}
				}
				else
				{
					print "<option  value=0> Nessuna classe trovata";					
				}		
				print "</SELECT>";
		  }
		  print	"</td></tr>";  	
		  
		  
		  //TABELLA SPECIALIZZAZIONE nome=spec		  
		  print "<tr><td> $plesso_specializzazione &nbsp;</td>";   
	      $q2="select * from tbl_specializzazioni";
		  if (!($reply1=mysqli_query($con,inspref($q2))))
		  {
		  	print "<td   ALIGN='CENTER'>Query fallita nelle specializzazioni</td>";
		  }
		  else
		  {
		  		print "<td   ALIGN='CENTER'> <SELECT NAME='spec'>";				
				//Controlla se esiste le tbl_sezioni e stampa l'elenco altrimenti da l'errore
				if (mysqli_num_rows($reply1)>0)
				{
					while ($d2=mysqli_fetch_array($reply1)) 
					{
						print "<option  value='".$d2['idspecializzazione']."'> ".$d2['denominazione']."";	
					}
				}
				else
				{
					print "<option  value=0> Nessuna classe trovata";					
				}		
				print "</SELECT>";
		  }
		  print	"</td></tr>"; 	

	print "</td> </tr>";
	
	print "<tr><td>Ore settimanali</td><td><input type='text' name='ore' maxlength='2' size='2'></td></tr>";
	
	print "<tr><td COLSPAN='2'><br/><CENTER>";
	print "<input type='submit' name='registra' value='Registra'> </CENTER>";
	print "</CENTER></td></TR><TR><TD COLSPAN='2'>&nbsp;</TD></TR>";
	print "</form>"; 
	  
	print "</table></CENTER>";
	
	stampa_piede("");			
	mysqli_close($con);


