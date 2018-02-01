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

/*programma per la visualizzazione dei tbl_docenti
riceve in ingresso idcomnasc e idcomres Da in uscita iddocente*/
	@require_once("../php-ini".$_SESSION['suffisso'].".php");
	@require_once("../lib/funzioni.php");
	
	// istruzioni per tornare alla pagina di login 
	////session_start();
    $tipoutente=$_SESSION["tipoutente"];
    $idutente=$_SESSION["idutente"]; //prende la variabile presente nella sessione
	if ($tipoutente=="")
	   {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
	   }
	
	$titolo="Elenco lezioni";
    $script=""; 
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
    $iddocente = stringa_html('iddocente');
            
	
	$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
	if(!$con)
		{print("<H1>connessione al server mysql fallita</H1>");
	 	exit;
		}
	$DB=true;
	if(!$DB)
		{print("<H1>connessione al database fallita</H1>");
		 exit;
		}
		
	 // 
    // SELEZIONE DOCENTE
    //
    
   print ' <form method="post" name="lezioni" action="vis_lez_gru.php">
   
   <p align="center">
   <table align="center">

      <tr>
      <td colspan="2" align="center"><b>Docente</b>';
      
      $sqld= "SELECT * FROM tbl_docenti ORDER BY cognome, nome";
      $resd=mysqli_query($con,inspref($sqld)) or die (mysqli_error($con));
      if($resd)
   	{
	        if ($tipoutente=='S' | $tipoutente=='P' | $tipoutente=='M')
	           print ("<select name='iddocente' ONCHANGE='lezioni.submit()'>");
	        else
	           print ("<select name='iddocente' disabled>");   
           print ("<option value=''>&nbsp;");
           while ($datal=mysqli_fetch_array($resd))
           {
				  if ($iddocente==$datal['iddocente'])
	           {   
	              print("<option value='");
	              print($datal['iddocente']);
	              print("' selected> ");
	              print($datal['cognome']);
	              print("&nbsp;");
	              print($datal['nome']);	
				  }
				  else
				  {
					  print("<option value='");
	              print($datal['iddocente']);
	              print("'> ");
	              print($datal['cognome']);
	              print("&nbsp;");
	              print($datal['nome']);	  
				  }
           }
	    
	    }
	print("</select> </td> </tr></table></form><br>");
if ($iddocente!='')
{
	$sql="SELECT distinct tbl_lezioni.idlezionegruppo,tbl_gruppi.descrizione,tbl_lezioni.orainizio,tbl_lezioni.numeroore,tbl_lezioni.datalezione
          FROM tbl_firme,tbl_lezionigruppi ,tbl_lezioni,tbl_gruppi,tbl_materie
          WHERE tbl_firme.idlezione=tbl_lezioni.idlezione
          AND tbl_lezionigruppi.idgruppo=tbl_gruppi.idgruppo
          AND tbl_lezioni.idlezionegruppo=tbl_lezionigruppi.idlezionegruppo
          AND tbl_firme.iddocente=$iddocente
          AND tbl_lezioni.idlezionegruppo IS NOT NULL
          ORDER BY tbl_lezioni.datalezione,tbl_lezioni.orainizio";
	$result=mysqli_query($con,inspref($sql));
	if(!($result))
		print("query fallita");
	else {
		
		
		print("<CENTER><table border=1>");
		print("<tr class='prima'><td><center><b> Data lezione</b></td>");
		print("<td><center><b> Gruppo</b></td>");
		
		print("<td align='center'><b>Periodo</b> </td>");
		print("<td colspan='2'><center><b> Azione</b> </td></tr></b>");
	
	$w=mysqli_num_rows($result);
	if ($w>0)
	{
	
	
	while($Data=mysqli_fetch_array($result))
	{
	  $dl=data_italiana($Data['datalezione']);
	  $giorno=giorno_settimana($Data['datalezione']);
	  $cm=$Data['descrizione'];
	  $pe=$Data['orainizio']."->".($Data['orainizio']-1+$Data['numeroore']);	
	  $idlez=$Data['idlezionegruppo'];
	  
	 print("<tr><td>$giorno $dl</td><td>$cm</td><td>$pe</td>");
	 print("<td><center><a href='mod_lez_gru.php?a=$idlez&b=$iddocente'> Modifica</a></td>");
	 print("<td><center><a href='can_lez_gru.php?a=$idlez&b=$iddocente'> Elimina</a></td></tr>");
        }
	}
	else
	{
	print("<tr BGCOLOR='#cccccc'><td colspan='11'> <center>Nessuna lezione trovata</center></td></tr>");
	}
	print("</TABLE><br/><br/><br/>");
	
	print("</CENTER>");
}
}	
	stampa_piede("");	
	mysqli_close($con);

