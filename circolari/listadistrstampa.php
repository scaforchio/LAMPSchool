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


 
 @require_once("../php-ini".$_SESSION['suffisso'].".php");
 @require_once("../lib/funzioni.php");
	
 // istruzioni per tornare alla pagina di login se non c'� una sessione valida
 ////session_start();
 $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
 
 
    if ($tipoutente=="")
       {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
       } 

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 
$idcircolare=stringa_html('idcircolare');
$titolo="Stampa lista di distribuzione";
$titolo="Situazione mensile tbl_lezioni";
$script= "
	<script type='text/javascript'>
         <!--
            function printPage()
            {
               if (window.print)
                  window.print();
               else 
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');            }
         //-->
         </script>
        

";
stampa_head($titolo,"",$script,"MSPA");

// $annoscolastico=$annoscol."/".($annoscol+1);

print ('<body class="stampa" onLoad="printPage()">');


//stampa_head($titolo,"",$script);
//stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

//
//  SELEZIONE CIRCOLARE
//

if ($idcircolare!="")
{
	
	$query="select * from tbl_circolari where idcircolare=$idcircolare";
	$ris=mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query));
	$rec=mysqli_fetch_array($ris);
	
	print "<center><b>Lista di distribuzione circolare:</b><br><br>";
	print "<center><b>".data_italiana($rec['datainserimento'])." - ".$rec['descrizione']."</b></center><br>";
	print "<center><b>Destinatari: ".decod_dest($rec['destinatari'])."</b></center><br>";
	$dest=$rec['destinatari'];
	print "<br><table border=1 align='center'>";
	print "<tr class='prima'><td>Destinatario</td><td>Data lettura</td>";
	if ($ricevuta)
	   print "<td>Data conferma lettura</td>";
	print "</tr>";    
   
   
   if ($dest=='D' | $dest=='SD')
       $query="select * from tbl_diffusionecircolari,tbl_docenti
               where tbl_diffusionecircolari.idutente=tbl_docenti.iddocente
               and idcircolare=$idcircolare
               order by cognome,nome";
   if ($dest=='A' | $dest=='SA')
       $query="select * from tbl_diffusionecircolari,tbl_alunni,tbl_classi
               where tbl_diffusionecircolari.idutente=tbl_alunni.idalunno
               and idcircolare=$idcircolare
               and tbl_alunni.idclasse=tbl_classi.idclasse
               order by anno,sezione,specializzazione,cognome,nome";
   if ($dest=='I' | $dest=='SI')
       $query="select * from tbl_diffusionecircolari,tbl_amministrativi
               where tbl_diffusionecircolari.idutente=tbl_amministrativi.idamministrativo
               and idcircolare=$idcircolare
               order by cognome,nome";
  // print "tttt $dest";
  // print inspref($query);
   $ris=mysqli_query($con,inspref($query)) or die("Errore:".inspref($query)." ".mysqli_error($con));
   while ($rec=mysqli_fetch_array($ris))
   {
		
	   print ("<tr><td>".$rec['cognome']."&nbsp;".$rec['nome']);
	   if ($dest=='A' | $dest=='SA')
	      print (" - ".decodifica_classe(estrai_classe_alunno($rec['idalunno'],$con),$con). " - ". data_italiana($rec['datanascita']));
	   print "</td>";
	   if ($rec['datalettura']!='0000-00-00')
		   print ("<td>".data_italiana($rec['datalettura'])."</td>");
		else
		   print ("<td>&nbsp;</td>");
		if ($ricevuta)
		{
		   if ($rec['dataconfermalettura']!='0000-00-00')
		      print ("<td>".data_italiana($rec['dataconfermalettura'])."</td>");
		   else
		      print ("<td>&nbsp;</td>");
		}
		print "</tr>";
	}	
   print "</table>";
   
   print "<b><center>Fine lista</center>";
}  


mysqli_close($con);


function decod_dest($tipodest)
{
	//if ($tipodest=='O')
	//   return "Tutti";
   if ($tipodest=='D')
	   return "Tutti i docenti";
   if ($tipodest=='A')
	   return "Tutti gli alunni";
   if ($tipodest=='I')
	   return "Tutti gli impiegati";
	if ($tipodest=='SD')
	   return "Selezione docenti";
   if ($tipodest=='SA')
	   return "Selezione alunni";
   if ($tipodest=='SI')
	   return "Selezione impiegati";   
}

