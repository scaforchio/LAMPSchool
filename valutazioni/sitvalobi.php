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
$iddocente=$_SESSION["idutente"];
if ($tipoutente=="")
{
   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
   die;
} 

//
//    Parte iniziale della pagina
//

$titolo="Visualizzazione voti per obiettivo";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
$cattedra = stringa_html('cattedra');
$obiettivo = stringa_html('obiettivo');
$periodo = stringa_html('periodo');


$idgruppo="";
$nominativo="";
$idclasse="";
$idmateria="";

$datelez=array();
$date=array();			  
$alunni=array();
$voti=array();

$obmin=0;

print ("
   <form method='post' action='sitvalobi.php' name='valabil'>
   
   <p align='center'>
   <table align='center'>");
   
   //
   //   Leggo il nominativo del docente e lo visualizzo
   //

   
   $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
    
   $query="select iddocente, cognome, nome from tbl_docenti where iddocente=$iddocente";
   
   $ris=mysqli_query($con,inspref($query));
   
   
   
   if($nom=mysqli_fetch_array($ris))
   {
      $iddocente=$nom["iddocente"];
      $cognomedoc=$nom["cognome"];
      $nomedoc=$nom["nome"];
      $nominativo =$nomedoc." ".$cognomedoc;  
   }
             
   print("    
             <tr>
              <td><b>Docente</b></td>

          <td>
          <INPUT TYPE='text' VALUE='$nominativo' disabled>
          <input type='hidden' value='$iddocente' name='iddocente'>
          </td></tr>");

   
   
  print(" 
   
   <tr>
      <td width='50%'><b>Cattedra</b></p></td>
      <td width='50%'>
      <SELECT ID='cattedra' NAME='cattedra' ONCHANGE='valabil.submit()'> <option value=''>&nbsp "); 
 
           
         $query="select idcattedra,tbl_classi.idclasse,tbl_materie.idmateria, anno, sezione, specializzazione, denominazione from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idalunno=0 and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria order by anno, sezione, specializzazione, denominazione";
          $ris=mysqli_query($con,inspref($query));
          while($nom=mysqli_fetch_array($ris))
	      {
            print "<option value='";
            print ($nom["idcattedra"]);
            print "'";
            if ($cattedra==$nom["idcattedra"])
            {
               print " selected";
               $idmateria=$nom["idmateria"];   // Memorizzo materia e classe della cattedra selezionata
               $idclasse=$nom["idclasse"];
            }
            print ">";
            print ($nom["anno"]);
            print "&nbsp;"; 
            print($nom["sezione"]); 
            print "&nbsp;";
            print($nom["specializzazione"]);
            print "&nbsp;-&nbsp;";
            print($nom["denominazione"]);
            
          }
        
        print "</select>";
 





 
// Se è stata selezionata la cattedra riempio la select degli obiettivi      
if ($idclasse!="")
{   
	
   
	
	   
   print("      
      <tr>
          <td><b>Obiettivo (con./abil.)</b>
          </td>
          <td>
          <select name='obiettivo' ONCHANGE='valabil.submit()'>") ;     
	
          $query="select * from tbl_competdoc
	              where tbl_competdoc.idmateria=$idmateria
	              and tbl_competdoc.idclasse=$idclasse
	              order by numeroordine"; 

          $riscomp=mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query));
          
	      while ($nomcomp=mysqli_fetch_array($riscomp))
	      {
			  $idcompetenza=$nomcomp['idcompetenza'];
			  
			  print "<optgroup label='".$nomcomp['numeroordine'].". ".$nomcomp['sintcomp']."'>";
			  
			  //CARICO LE CONOSCENZE
			  
			  $query="select * from tbl_abildoc
	              where idcompetenza=$idcompetenza
	              and abil_cono = 'C'
	              order by numeroordine"; 
	      
              $risabil=mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query));
		
			  while ($nomabil=mysqli_fetch_array($risabil))
	          {
			     $idabilita=$nomabil['idabilita'];
			     $sintabil=$nomabil['sintabilcono'];
			     if ($obiettivo=="")
			        $obiettivo=$idabilita;
			     print "<option value='$idabilita' ";
			     if ($idabilita==$obiettivo) 
			     {
					  print "SELECTED ";
			        $obmin=$nomabil['obminimi'];
				  }
			     print ">CO.".$nomcomp['numeroordine'].".".$nomabil['numeroordine']." $sintabil</option>";
			  }
			  //CARICO LE COMPETENZE
			  $query="select * from tbl_abildoc
	              where idcompetenza=$idcompetenza
	              and abil_cono = 'A'
	              order by numeroordine"; 
	      
              $risabil=mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query));
		
			  while ($nomabil=mysqli_fetch_array($risabil))
	        {
			     $idabilita=$nomabil['idabilita'];
			     $sintabil=$nomabil['sintabilcono'];
			     
			     print "<option value='$idabilita' ";
			     if ($idabilita==$obiettivo) 
			     {
					  print "SELECTED ";
			        $obmin=$nomabil['obminimi'];
				  }
			     print "> AB.".$nomcomp['numeroordine'].".".$nomabil['numeroordine']." $sintabil</option>";
			  }
	        print "</optgroup>";
	      
	      
	      
	       
	       
	       
	         
	   }  
	   print "</select>";
          
 print "</td>
          
      </tr>";

}

        

//
//   Inizio visualizzazione del combo box del periodo
//
if ($numeroperiodi==2)
   print("<tr><td width='50%'><b>Quadrimestre</b></td>");
else
   print("<tr><td width='50%'><b>Trimestre</b></td>");

echo("   <td width='50%'>");
echo("   <select id='periodo' name='periodo' ONCHANGE='valabil.submit()'>");

if ($periodo=='Primo')
  echo("<option selected>Primo</option>");
else
  echo("<option>Primo</option>");
if ($periodo=='Secondo')
  echo("<option selected>Secondo</option>");
else
  echo("<option>Secondo</option>");

if ($numeroperiodi==3)
   if ($periodo=='Terzo')
     echo("<option selected>Terzo</option>");
   else
     echo("<option>Terzo</option>");



  
echo("</select>");
echo("</td></tr>");


print("</table></form>");        



  
if ($obiettivo != "" & $cattedra!= "")
{
	 // VERIFICO SE SI TRATTA DI UNA CATTEDRA LEGATA A UN GRUPPO
	$idcl=estrai_id_classe($cattedra,$con);
	$idmat=estrai_id_materia($cattedra,$con);
		
	$query="select distinct tbl_gruppi.idgruppo from tbl_gruppialunni,tbl_alunni,tbl_gruppi 
			  where tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
			  and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
			  and tbl_alunni.idclasse=$idcl
			  and tbl_gruppi.idmateria=$idmat
			  and tbl_gruppi.iddocente=$iddocente";
	$ris=mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query));
	if ($rec=mysqli_fetch_array($ris))
		$idgruppo=$rec['idgruppo'];
	//
	
	
	//$idalun=array();
	//$date=array();
	
	// APERTURA FILE CSV PER MEMORIZZAZIONE SITUAZIONE
    $nf="svo".session_id().".csv";
    $nomefile="$cartellabuffer/".$nf;
    $fp = fopen($nomefile, 'w');
    // DEFINIZIONE ARRAY PER MEMORIZZAZZIONE IN CSV
    $listadate=array();
    $listadate[]="Alunno";	
	
	
	// STABILISCO I LIMITI DELLE DATE IN BASE AL PERIODO
	$perioquery="and true";
    if ($periodo=="Primo")
	{
        $perioquery=" and data <= '".$fineprimo."'" ;
    } 
	if ($periodo=="Secondo" & $numeroperiodi==2)
    {     
        $perioquery=" and data > '".$fineprimo."'" ; 
    }
	if ($periodo=="Secondo" & $numeroperiodi==3)
    {     
        $perioquery=" and data > '".$fineprimo."' and data <=  '".$finesecondo."'";
    }
	if ($periodo=="Terzo")
    {    
       $perioquery=" and data > '".$finesecondo."'" ;
    }
	
	
	$query="select distinct tbl_valutazioniintermedie.data
	              from tbl_valutazioniabilcono,tbl_valutazioniintermedie,  tbl_alunni, tbl_classi where 
	              tbl_valutazioniabilcono.idvalint = tbl_valutazioniintermedie.idvalint and
	              tbl_valutazioniintermedie.idalunno=tbl_alunni.idalunno
	              and tbl_alunni.idclasse = tbl_classi.idclasse 
	              and tbl_valutazioniintermedie.idclasse=$idclasse
	              and tbl_valutazioniintermedie.idmateria=$idmateria 
	              and tbl_valutazioniabilcono.idabilita=$obiettivo
	              $perioquery
	              order by data" ;
	
	$ris=mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query));
	while($nomdat=mysqli_fetch_array($ris))
	{
		$datelez[]=$nomdat["data"];
		
	}
		
    $query="select tbl_valutazioniabilcono.idabilita, tbl_valutazioniintermedie.idalunno,tbl_valutazioniintermedie.voto as votom,tbl_valutazioniabilcono.voto as votoac,tbl_valutazioniintermedie.data
	              from tbl_valutazioniabilcono,tbl_valutazioniintermedie,  tbl_alunni, tbl_classi where 
	              tbl_valutazioniabilcono.idvalint = tbl_valutazioniintermedie.idvalint and
	              tbl_valutazioniintermedie.idalunno=tbl_alunni.idalunno
	              and tbl_alunni.idclasse = tbl_classi.idclasse 
	              and tbl_valutazioniintermedie.idclasse=$idclasse
	              and tbl_valutazioniintermedie.idmateria=$idmateria 
	              and tbl_valutazioniabilcono.idabilita=$obiettivo
	              $perioquery" ;
	         
	$ris=mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query));
	while($nomval=mysqli_fetch_array($ris))
	{
		$date[]=$nomval["data"];
		$voti[]=$nomval["votoac"];
		$alunni[]=$nomval["idalunno"];
	}
	
	
	if ($obmin) echo "<br><center><b>Obiettivo minimo!</b></center><br>"; else echo "<br>";
	
	echo "<table border='2' align='center'>
			  <tr>
				  <td> </td>";
	for ($i=0;$i<count($datelez);$i++)
	{
		echo "<td align='center'>";
		echo "".giorno_settimana($datelez[$i])."<br/>".estraigiorno($datelez[$i])."<br/>".estraimese($datelez[$i]);
		echo "</td>";
		
		$listadate[]=data_italiana($datelez[$i]);  // Aggiungo le date all'array per file csv
		
	}	
	echo "<td><b>Med</b></td>";	
	
	$listadate[]="Media";
	fputcsv($fp, $listadate,";"); // Inserisco le date al file  	  
	echo"</tr>";
	
	
//	$query="select * from tbl_alunni where idclasse=".$idclasse." order by cognome, nome, datanascita";
 
   if ($idgruppo=='')
      $query="select * from tbl_alunni where idclasse='$idclasse' order by cognome,nome,datanascita";
   else
      $query="select tbl_alunni.idalunno,cognome,nome,datanascita 
                  from tbl_gruppi,tbl_gruppialunni,tbl_alunni
                  where
                       tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
                       and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
                       and tbl_alunni.idclasse=$idclasse
                       and tbl_gruppi.idgruppo in (select idgruppo from tbl_gruppi where idmateria=$idmateria and iddocente=$iddocente)";//=$idgruppo";    
 
   $ris=mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query));
    
   while($nom=mysqli_fetch_array($ris))
   {
		 if (!alunno_certificato_pei($nom['idalunno'],$idmateria,$con))
		 {
			 if ($obmin | alunno_certificato_norm($nom['idalunno'],$idmateria,$con) | !alunno_certificato($nom['idalunno'],$con)) 
	  		 {
					$listavoti=array();
			 		$cognome=$nom['cognome'];
					$nome=$nom['nome'];
					$datanascita=$nom['datanascita'];
					$idalunno=$nom['idalunno'];
					if (!alunno_certificato($idalunno,$con))
						$cert="";
					else
						$cert="<img src='../immagini/apply_small.png'>";  
					print "<tr>";
					print "<td>$cognome $nome ".data_italiana($datanascita)." $cert</td>";
					
					$listavoti[]=$cognome." ".$nome." ".$datanascita;   // Aggiungo la definizione dell'obiettivo al file
					
					$numvoti=0;
					$totvoti=0;
					for ($i=0;$i<count($datelez);$i++)
						  {
							  echo "<td align='center'>";
							  // RICERCA VOTO SE ESISTE IN QUESTA DATA 
							  $votinum=array();
							  $votinum=CercaVoto($datelez[$i],$idalunno,$date,$voti,$alunni);
							  // $numvotivotovis=dec_to_mod();
							 
							  if (array_count_values($votinum)!=0)
								  { 
										$votigiorno="";
										foreach($votinum as $votonum)
										{ 
											 $totvoti+=$votonum;
											 $numvoti++;
											 
											 if ($votonum<5.875) echo "<font color='red'>"; else echo "<font color='green'>";
											 echo dec_to_mod($votonum);
											 $votigiorno=$votigiorno.num_to_ita($votonum)." ";
											 echo "&nbsp;</font>";
											 
										}
										$listavoti[]=$votigiorno;
								}
							  else
								{
								  echo "&nbsp;";
								  $listavoti[]="";
							  }  
							  echo "</td>";
							 }	
							 if ($numvoti>0)
							 {
								 $votomedio=$totvoti/$numvoti;
								 echo "<td><b>";
								 if ($votomedio<5.875) echo "<font color='red'>"; else echo "<font color='green'>";
								 echo dec_to_mod($votomedio);
								 $listavoti[]=num_to_ita($votomedio);
								 echo "</font></b></td>";
						}
						else
						{
							echo "<td>&nbsp;</td>";
							 $listavoti[]="";
						}
					
					fputcsv($fp, $listavoti,";");
					
						
					
					
					print "</tr>";
           }		
		}
		
        
		
			

   }
	
	fclose($fp);
	echo "</table>";
	print ("<br/><center><a href='$cartellabuffer/$nf'><img src='../immagini/csv.png'></a></center>");
	
}   
  
 // fine if
  



mysqli_close($con);
stampa_piede(""); 


function CercaVoto ($dl,$idalu, $datel, $vot, $alu)
{
	$votiinseriti=array();
	$numalu=count($alu);
	
	
	
	for ($i=0;$i<$numalu;$i++)
	 {
       
       if($dl==$datel[$i] && $idalu==$alu[$i])
         $votiinseriti[]=$vot[$i];
     }     
    return $votiinseriti;
} 
 
         

