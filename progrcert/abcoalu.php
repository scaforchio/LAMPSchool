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
 $tipoutente=$_SESSION["tipoutente"];
 $iddocente=$_SESSION["idutente"]; //prende la variabile presente nella sessione
    if ($tipoutente=="")
       {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
       } 


$titolo="Gestione obiettivi del programma";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

$maxabil=10;
$maxcono=10;

//$materia = stringa_html('materia');
//$anno = stringa_html('anno');
$idcompetenza = stringa_html('idcompetenza');
$idcattedra=stringa_html("idcattedra");

// $presente=false;  // Determina se l'idcompetenza è presente nel combo box


$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

print ("
   <form method='post' action='abcoalu.php' name='abilcono'>
   
   <p align='center'>
   <table align='center'>
");      


   
           
      //    $query="select idcattedra, tbl_cattnosupp.idmateria, tbl_cattnosupp.idclasse, tbl_cattnosupp.idalunno, cognome, nome, datanascita 
      //           from tbl_cattnosupp, tbl_alunni
      //           where iddocente=$iddocente 
      //           and tbl_cattnosupp.idalunno = tbl_alunni.idalunno
      //           order by cognome, nome, datanascita";
              $query="select idcattedra, tbl_cattnosupp.idmateria, tbl_cattnosupp.idclasse, tbl_cattnosupp.idalunno, cognome, nome, datanascita 
                 from tbl_cattnosupp, tbl_alunni, tbl_tipoprog
                 where iddocente=$iddocente 
                 and tbl_cattnosupp.idalunno = tbl_alunni.idalunno
                 and tbl_cattnosupp.idmateria = tbl_tipoprog.idmateria
                 and tbl_cattnosupp.idalunno = tbl_tipoprog.idalunno
                 and tbl_tipoprog.tipoprogr='P'
                 order by cognome, nome, datanascita";
              
          // print inspref($query); // TTTT
          
          $ris=mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query));
	
	if (mysqli_num_rows($ris)>0)
      {   
			print("<tr>
                 <td><b>Cattedra</b> </td>
                 <td>
                  <SELECT NAME='idcattedra' ONCHANGE='abilcono.submit()'> <option value=''>&nbsp "); 
	  
					 while($nom=mysqli_fetch_array($ris))
					 {
						 print "<option value='";
						 print ($nom["idcattedra"]);
						 print "'";
						 if ($idcattedra==$nom["idcattedra"])
						 {   
							 print " selected";
							 $idmateria=$nom["idmateria"];
							 $idclasse=$nom["idclasse"];
							 $idalunno=$nom["idalunno"];
						 }   
						 print ">";
						
						 print (estrai_alunno_data($nom['idalunno'],$con)." - ".decodifica_materia($nom['idmateria'],$con));
						
						
					 }
				  
			print("
				</SELECT>
				</td></tr>");


		if ($idcattedra!="")
		{

				print("<tr>
						 <td><b>Competenza</b></td>
						  <td>
						  <SELECT NAME='idcompetenza' ONCHANGE='abilcono.submit()'> <option value=''>&nbsp "); 
			  
					
					  
					 $query="SELECT numeroordine, idcompetenza, sintcomp
								FROM tbl_competalu
								WHERE tbl_competalu.idalunno = $idalunno
								AND tbl_competalu.idmateria = $idmateria
								ORDER BY numeroordine";
					 
				
					 
					 $riscomp=mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query));
					 if (mysqli_num_rows($riscomp)>0)
					 {
						 $trovato=false;
						 while($nom=mysqli_fetch_array($riscomp))
						 {
							 print "<option value='";
							 print ($nom["idcompetenza"]);
							 print "'";
							 if ($idcompetenza==$nom["idcompetenza"])
							 {
								 $trovato=true;
								 print " selected";
							 }   
							 print ">";
							 print ($nom["sintcomp"]);
							 print "</option>";
						 }
						 if (!$trovato) $idcompetenza="";
					 }
					 else
						 $idcompetenza="";	     
			 
			print("
				</SELECT>
				</td></tr>");
		}
			
		print("</table><hr>");
		print("</form>");   


   }
   else
      print "<br><br><center><b>Nessuna cattedra per alunni con programma personalizzato!</b></center><br>";
 
if ($idcompetenza!="")
{
    // Controllo presenza di voti per la programmazione dell'alunno
    $query="select count(*) as numerovoti from tbl_valutazioniabilcono, tbl_valutazioniintermedie
         where tbl_valutazioniabilcono.idvalint = tbl_valutazioniintermedie.idvalint
         and tbl_valutazioniintermedie.idmateria=$idmateria 
	      and tbl_valutazioniintermedie.idalunno=$idalunno";
             
	              
	     
	              
	              
     $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
          
     $nom=mysqli_fetch_array($ris);
  
     if ($nom['numerovoti']>0)
     {
	     print ("<center><b><font color=red>Attenzione! Ci sono voti collegati a questa programmazione.<br/> 
	            La modifica della programmazione ne comporta la perdita!<br/>
	            Utilizzare la \"MODIFICA VOCI PROGRAMMAZIONE\" se non si vogliono perdere i voti!</font></b></center>");
	         
     }

	  else
	  {

    
  
			 //
			 //   GESTIONE ABILITA'
			 // 
			  
			// print ("<table border=1 width='100%'><tr><td width='50%'>"); 
			  
			 // $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
			  
			
			 $query="select * from tbl_abilalu where idcompetenza=$idcompetenza and abil_cono='A' order by numeroordine";
			 $ris=mysqli_query($con,inspref($query)) or die ("Errore: ".inspref($query));
			 print "<form method='post' action='insabcoalu.php'>
					  <p align='center'>
					 <font size=4 color='black'>Abilit&agrave;</font>
					 
					 <table border=1 align='center'>";
			 $numord=0;
			 while($val=mysqli_fetch_array($ris))
			 {
				  $numord++;
				  $abilita=$val["abilcono"];
				  $sintabilcono=$val["sintabilcono"];
				  print "<tr><td align='center'>$numord</td><td align='center'>
						SINTESI: <input type=text name=sintab$numord value='$sintabilcono' maxlength=80 size=80>";
				  if ($val["obminimi"])
						print "Ob.Min. <input type='checkbox' name='chkab$numord' checked value='$numord'>";
				  else
						print "Ob.Min. <input type='checkbox' name='chkab$numord' value='$numord'> ";   
					print"<br/><textarea rows=3 cols=80 name='ab$numord'>$abilita</textarea></td></tr>";
				  
								  
			 }   
			 for($no=$numord+1;$no<=$maxabil;$no++)
				  print "<tr><td align='center'>$no</td><td align='center'>
							SINTESI: <input type=text name=sintab$no value='' maxlength=80 size=80>Ob.Min. <input type='checkbox' name='chkab$no' value='$no'><br/>
							<textarea rows=3 cols=80 name='ab$no'></textarea></td></tr>";
			 print "</table></p>";
			 
		 //   print ("</td><td width='50%'>");
			 //
			 //  GESTIONE CONOSCENZE
			 //
				  
			 
			  
			
			 $query="select * from tbl_abilalu where idcompetenza=$idcompetenza and abil_cono='C' order by numeroordine";
			 $ris=mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query));
			 
			 print "<p align='center'>
					 <font size=4 color='black'>Conoscenze</font><br/>
					 
					 <table border=1 align='center'>";
			 $numord=0;
			 while($val=mysqli_fetch_array($ris))
			 {
				  $numord++;
				  $conoscenza=$val["abilcono"];
				  $sintabilcono=$val["sintabilcono"];
				  print "<tr><td align='center'>$numord</td><td align='center'>
						  SINTESI: <input type=text name=sintco$numord value='$sintabilcono' maxlength=80 size=80>";
				  if ($val["obminimi"])
						print "Ob.Min. <input type='checkbox' name='chkco$numord' checked value='$numord'>";
				  else
					 	print "Ob.Min. <input type='checkbox' name='chkco$numord' value='$numord'>";      
				  print "<br/><textarea rows=3 cols=80 name='co$numord'>$conoscenza</textarea></td></tr>";
					 
			 }   
			 for($no=$numord+1;$no<=$maxcono;$no++)
				  print "<tr><td align='center'>$no</td><td align='center'>
							 SINTESI: <input type=text name=sintco$no value='' maxlength=80 size=80>Ob.Min. <input type='checkbox' name='chkco$no' value='$no'><br/>
							 <textarea rows=3 cols=80 name='co$no'></textarea></td></tr>";
			// print "</table></p></td></tr></table>";
			 print "</table></p>";
 			 print "<table align='center'>
						<tr><td colspan=2 align=center><input type='submit' value='Registra abilità e conoscenze'></tr></table>";
			 print "<input type='hidden' name='idcompetenza' value='$idcompetenza'>";
			 print "<input type='hidden' name='idcattedra' value='$idcattedra'>";
			 //print "<input type='hidden' name='materia' value='$materia'>";
			 print "</form>";
			 
	 }
}		  
		  else
		  {
			  print("");
		  }

mysqli_close($con);
stampa_piede(""); 

