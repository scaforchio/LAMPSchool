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
$competenza = stringa_html('competenza');

$idmateria="";
$idclasse="";

print ("
   <form method='post' action='abcodo.php' name='abilcono'>
   
   <p align='center'>
   <table align='center'>
");      

print("<tr>
      <td width='50%'><p align='center'><b>Competenza</b> (Anno - Materia - Competenza)</p></td>
      <td width='50%'>
      <SELECT ID='competenza' NAME='competenza' ONCHANGE='abilcono.submit()'> <option value=''>&nbsp "); 
	 
          $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
           
          $query="SELECT numeroordine, idcompetenza, denominazione, sintcomp, anno, sezione, specializzazione,tbl_materie.idmateria,tbl_classi.idclasse
                  FROM tbl_competdoc, tbl_materie, tbl_cattnosupp, tbl_classi
                  WHERE tbl_competdoc.idmateria = tbl_cattnosupp.idmateria and  tbl_competdoc.idclasse = tbl_cattnosupp.idclasse 
                  AND tbl_cattnosupp.idclasse = tbl_classi.idclasse
                  AND tbl_cattnosupp.idmateria = tbl_materie.idmateria
                  AND tbl_cattnosupp.iddocente =$iddocente
                  and tbl_cattnosupp.idalunno=0
                  ORDER BY anno, sezione, specializzazione, denominazione, numeroordine";
          
          // print inspref($query); // TTTT
          
          $riscomp=mysqli_query($con,inspref($query));
          
           while($nom=mysqli_fetch_array($riscomp))
	       {
            print "<option value='";
            print ($nom["idcompetenza"]);
            print "'";
			if ($competenza==$nom["idcompetenza"])
			{
			   $idmateria=$nom["idmateria"];
			   $idclasse=$nom["idclasse"];  
			   print " selected";
			}   
			print ">";
            print ($nom["anno"]." - ".$nom["sezione"]." - ".$nom["specializzazione"]." - ".decodifica_materia($nom["idmateria"],$con)." - ".$nom["sintcomp"]);
            print "</option>";
           }
	     
     //   }
   print("
      </SELECT>
      </td></tr>");

   
print("</table><hr>");
print("</form>");   
if ($competenza!="")
{
    // Controllo presenza di voti per la programmazione della classe
 
  /*  $query="select count(*) as numerovoti from tbl_valutazioniabilcono, tbl_valutazioniintermedie, tbl_alunni
         where tbl_valutazioniabilcono.idvalint = tbl_valutazioniintermedie.idvalint
         and tbl_valutazioniintermedie.idalunno = tbl_alunni.idalunno
	     and tbl_valutazioniintermedie.idmateria=$idmateria 
	     and tbl_alunni.idclasse=$idclasse
	     and tbl_valutazioniintermedie.iddocente=$iddocente
	     " ;
 */
   $query="select * from tbl_valutazioniabilcono, tbl_abildoc,tbl_competdoc
              where 
                 tbl_valutazioniabilcono.pei = 0
                 and tbl_valutazioniabilcono.idabilita = tbl_abildoc.idabilita
                 and tbl_abildoc.idcompetenza=tbl_competdoc.idcompetenza
                 and tbl_competdoc.idcompetenza=$competenza";
          
	              
	              
     $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". inspref($query));
     
  
     if (mysqli_num_rows($ris)>0)
     {
	     print ("<center><b><font color=red>Attenzione! Ci sono voti collegati a questa programmazione.<br/> 
	            La modifica di alcune voci è quindi inibita!<br/>
	            Utilizzare la voce \"CORREGGI ABIL./CONO\" per correzioni!</font></b></center>");
	    // $votipresenti=true;          
     }

//	  else
//	  {


  
			 //
			 //   GESTIONE ABILITA'
			 // 
			  
			// print ("<table border=1 width='100%'><tr><td width='50%'>"); 
			  
			 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
			  
			
			 $query="select * from tbl_abildoc where idcompetenza=$competenza and abil_cono='A' order by numeroordine";
			 $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
			 print "<form method='post' action='insabcodo.php'>
					  <p align='center'>
					 <font size=4 color='black'>Abilit&agrave;</font>
					 
					 <table border=1 align='center'>";
			 $numord=0;
			 while($val=mysqli_fetch_array($ris))
			 {
				  $numord++;
				  $abilita=$val["abilcono"];
				  $sintabilcono=$val["sintabilcono"];
				  $idabilcono=$val["idabilita"];
				  $votipresenti=false;
				  $query="select * from tbl_valutazioniabilcono
                      where idabilita=$idabilcono
                      and pei=0" ;
				  $ris2=mysqli_query($con,inspref($query)) or die ("Errore: ".inspref($query));
				  if (mysqli_num_rows($ris2)>0)
				     $votipresenti=true;
				  
				  
				  
				  if (!$votipresenti)
					{
					  print "<tr><td align='center'>$numord</td><td align='center'>
							SINTESI: <input type=text name=sintab$numord value='$sintabilcono' maxlength=80 size=80>
							<input type=hidden name=idabil$numord value='$idabilcono'>";
					  if ($val["obminimi"])
							print "Ob.Min. <input type='checkbox' name='chkab$numord' checked value='$numord'>";
					  else
							print "Ob.Min. <input type='checkbox' name='chkab$numord' value='$numord'> ";   
					  print"<br/><textarea rows=3 cols=80 name='ab$numord'>$abilita</textarea></td></tr>";
					}
				  else
				  	{
					  print "<tr><td align='center'>$numord</td><td align='center'>
							SINTESI: <input type=text name=sintabdis$numord value='$sintabilcono' maxlength=80 size=80 disabled>
							         <input type=hidden name=idabil$numord value='$idabilcono'>
							         <input type='hidden'  name=sintab$numord value='$sintabilcono'>";
					  if ($val["obminimi"])
							print "Ob.Min. <input type='checkbox' name='chkab$numord' checked value='$numord'>";
					  else
							print "Ob.Min. <input type='checkbox' name='chkab$numord' value='$numord'> ";   
					  print"<br/><textarea rows=3 cols=80 name='abdis$numord' disabled>$abilita</textarea>
					             <input type='hidden' name=ab$numord value='$abilita'></td></tr>";
					}  
								  
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
				  
			 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
			  
			
			 $query="select * from tbl_abildoc where idcompetenza=$competenza and abil_cono='C' order by numeroordine";
			 $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
			 
			 print "<p align='center'>
					 <font size=4 color='black'>Conoscenze</font><br/>
					 
					 <table border=1 align='center'>";
			 $numord=0;
			 while($val=mysqli_fetch_array($ris))
			 {
				  $numord++;
				  $conoscenza=$val["abilcono"];
				  $sintabilcono=$val["sintabilcono"];
				  $idabilcono=$val["idabilita"];
				  $votipresenti=false;
				  $query="select * from tbl_valutazioniabilcono
                      where idabilita=$idabilcono
                      and pei=0" ;
				  $ris2=mysqli_query($con,inspref($query)) or die ("Errore: ".inspref($query));
				  
				  if (mysqli_num_rows($ris2)>0)
				     $votipresenti=true;
				  
				  if (!$votipresenti)
					{
					  print "<tr><td align='center'>$numord</td><td align='center'>
							SINTESI: <input type=text name=sintco$numord value='$sintabilcono' maxlength=80 size=80>
							         <input type=hidden name=idcono$numord value='$idabilcono'><input type=hidden name=idcono$numord value='$idabilcono'>";
					  if ($val["obminimi"])
							print "Ob.Min. <input type='checkbox' name='chkco$numord' checked value='$numord'>";
					  else
							print "Ob.Min. <input type='checkbox' name='chkco$numord' value='$numord'> ";   
					  print"<br/><textarea rows=3 cols=80 name='co$numord'>$conoscenza</textarea></td></tr>";
					}
				  else
				  	{
					  print "<tr><td align='center'>$numord</td><td align='center'>
							SINTESI: <input type=text name=sintcodis$numord value='$sintabilcono' maxlength=80 size=80 disabled>
							         <input type=hidden name=idcono$numord value='$idabilcono'>
							         <input type='hidden'  name=sintco$numord value='$sintabilcono'>";
					  if ($val["obminimi"])
							print "Ob.Min. <input type='checkbox' name='chkco$numord' checked value='$numord'>";
					  else
							print "Ob.Min. <input type='checkbox' name='chkco$numord' value='$numord'> ";   
					  print"<br/><textarea rows=3 cols=80 name='codis$numord' disabled>$conoscenza</textarea>
					             <input type='hidden' name=co$numord value='$conoscenza'></td></tr>";
					}  
				  
			 }   
			 for($no=$numord+1;$no<=$maxcono;$no++)
				  print "<tr><td align='center'>$no</td><td align='center'>
							 SINTESI: <input type=text name=sintco$no value='' maxlength=80 size=80>Ob.Min. <input type='checkbox' name='chkco$no' value='$no'><br/>
							 <textarea rows=3 cols=80 name='co$no'></textarea></td></tr>";
			// print "</table></p></td></tr></table>";
			 print "</table></p>";
			print "<table align='center'>
						<tr><td colspan=2 align=center><input type='submit' value='Registra abilità e conoscenze'></tr></table>";
			 print "<input type='hidden' name='competenza' value='$competenza'>";
			 //print "<input type='hidden' name='anno' value='$anno'>";
			 //print "<input type='hidden' name='materia' value='$materia'>";
			 print "</form>";
			 
		 }
		  
		  else
		  {
			  print("");
		  }
	  
		

         
mysqli_close($con);
stampa_piede(""); 

