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

$titolo="Visualizzazione voti alunno";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 



$cattedra = stringa_html('cattedra');
$alunno = stringa_html('alunno');
$periodo = stringa_html('periodo');

$nominativo="";
$idclasse="";
$idmateria="";
$idgruppo="";

$datelez=array();
$date=array();			  
$abilita=array();
$voti=array();



print ("
   <form method='post' action='sitvalalu.php' name='valabil'>
   
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
      <SELECT ID='cattedra' NAME='cattedra' ONCHANGE='valabil.submit()'> <option value=''>&nbsp;</option> "); 
	  
  
          
     //    if (!$_SESSION['sostegno'])    
     //       $query="select idcattedra,tbl_classi.idclasse,tbl_materie.idmateria, anno, sezione, specializzazione, denominazione from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria order by anno, sezione, specializzazione, denominazione";
     //    else
            $query="select idcattedra,tbl_classi.idclasse,tbl_materie.idmateria,idalunno, anno, sezione, specializzazione, denominazione from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria order by anno, sezione, specializzazione, denominazione";
         //  print inspref($query);
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
               if ($nom['idalunno']!=0) $alunno=$nom["idalunno"];
            }
            print ">";
         //   if (!$_SESSION['sostegno'])
         //   {
					print ($nom["anno"]);
					print "&nbsp;"; 
					print($nom["sezione"]); 
					print "&nbsp;";
					print($nom["specializzazione"]);
					print "&nbsp;-&nbsp;";
					print($nom["denominazione"]);
			//	}
			//	else
			   if ($nom['idalunno']!=0)
				{
					print ("&nbsp;-&nbsp;".estrai_dati_alunno($nom['idalunno'],$con));
				//	print "&nbsp;-&nbsp;"; 
				//	print($nom["denominazione"]);
					
				}
				
            
          }
        
        
        
        print "</select>";
 



if ($cattedra!="")
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

// Se è stata selezionata la cattedra riempio la select degli alunni      
if (!cattedra_sostegno($cattedra,$con))
{
	if ($idclasse!="")
	{  
			    
		print("      
			<tr>
				 <td><b>Alunno</b>
				 </td>
				 <td span>
				 <SELECT ID ='alunno' NAME='alunno' ONCHANGE='valabil.submit()'><option value=''>&nbsp");

		//	$query="select idalunno,cognome, nome, datanascita from tbl_alunni where idclasse=$idclasse order by cognome, nome";
			
			if ($idgruppo=='')
                $query="select * from tbl_alunni where idclasse='$idclasse' order by cognome,nome,datanascita";
             else
                $query="select tbl_alunni.idalunno,cognome,nome,datanascita 
                  from tbl_gruppi,tbl_gruppialunni,tbl_alunni
                  where
                       tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
                       and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
                       and tbl_alunni.idclasse=$idclasse
                       and tbl_gruppi.idgruppo  in (select idgruppo from tbl_gruppi where idmateria=$idmateria and iddocente=$iddocente)";//=$idgruppo";    
				 	
			
				 $ris=mysqli_query($con,inspref($query));
				 while($nom=mysqli_fetch_array($ris))
				{
					if (!alunno_certificato($nom['idalunno'],$con))
                  $cert="";
               else
                  $cert=" (*)";    
					print "<option value='";
					print ($nom["idalunno"]);
					print "'";
					if ($alunno==$nom["idalunno"])
					{
						print " selected";
						
					}
					print ">";
					print ($nom["cognome"]);
					print "&nbsp;"; 
					print($nom["nome"]); 
					print "&nbsp;";
					print(data_italiana($nom["datanascita"]));
					print $cert;
					
				 }

	}
						  
	print("</select>
				 
				 </td>
				 
			</tr>");
}

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



  
if ($alunno != "")
{
	
	// APERTURA FILE CSV PER MEMORIZZAZIONE SITUAZIONE
    $nf="sva".session_id().".csv";
    $nomefile="$cartellabuffer/".$nf;
    $fp = fopen($nomefile, 'w');
    // DEFINIZIONE ARRAY PER MEMORIZZAZIONE IN CSV
    $listadate=array();
    $listadate[]="Obiettivo";
	
	
	
	
	$idabil=array();
	$date=array();
	$idabil=array();
	$idabil=array();
	
	
	
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
	              and tbl_alunni.idalunno=$alunno
	              $perioquery
	              order by data" ;
	
	$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
	while($nomdat=mysqli_fetch_array($ris))
	{
		$datelez[]=$nomdat["data"];
		
	}
		
		
	
		
    $query="select tbl_valutazioniabilcono.idabilita, tbl_valutazioniintermedie.idalunno,tbl_valutazioniintermedie.voto as votom,tbl_valutazioniabilcono.voto as votoac,tbl_valutazioniintermedie.data
	              from tbl_valutazioniabilcono,tbl_valutazioniintermedie,tbl_alunni, tbl_classi where 
	              tbl_valutazioniabilcono.idvalint = tbl_valutazioniintermedie.idvalint and
	              tbl_valutazioniintermedie.idalunno=tbl_alunni.idalunno
	              and tbl_alunni.idclasse = tbl_classi.idclasse
	              and tbl_valutazioniintermedie.idclasse=$idclasse 
	              and tbl_valutazioniintermedie.idmateria=$idmateria 
	              and tbl_alunni.idalunno=$alunno
	              $perioquery" ;
	              
	       
	         
	$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
	while($nomval=mysqli_fetch_array($ris))
	{
		$date[]=$nomval["data"];
		$voti[]=$nomval["votoac"];
		$abilita[]=$nomval["idabilita"];
		
	}
	
	
	echo "<table border='2' align='center'>
			  <tr>
				  <td> </td>";
	for ($i=0;$i<count($datelez);$i++)
	{
		echo "<td align='center'>";
		echo "".giorno_settimana($datelez[$i])."<br/>".estraigiorno($datelez[$i])."<br/>".estraimese($datelez[$i]);
		
		$listadate[]=data_italiana($datelez[$i]);  // Aggiungo le date all'array per file csv
		
		echo "</td>";
		
	}	
	
	
	
	echo "<td><b>Med</b></td>";		
	$listadate[]="Media";
	fputcsv($fp, $listadate,";"); // Inserisco le date al file  
	echo"</tr>
		  ";
	
	
	if (!alunno_certificato_pei($alunno,$idmateria,$con))
	   $query="select * from tbl_competdoc where 
	           tbl_competdoc.idmateria=$idmateria and tbl_competdoc.idclasse=$idclasse 
	           order by numeroordine";
   else
      $query="select * from tbl_competalu where 
	           tbl_competalu.idmateria=$idmateria and tbl_competalu.idalunno=$alunno 
	           order by numeroordine"; 
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));        
    
    while($nom=mysqli_fetch_array($ris))
    {
		
		$listavoti=array();
		
		$idcompetenza=$nom['idcompetenza'];
		$sintcomp=$nom['sintcomp'];
		$numordcomp=$nom['numeroordine'];
		
		$listavoti[]=$numordcomp." ".$sintcomp;   // Aggiungo la definizione della comptenza al file
		
		print "<tr bgcolor='silver'>";
		print "<td>$numordcomp. $sintcomp</td>";
		for ($i=0;$i<=count($datelez);$i++)
  	    {
		    echo "<td align='center'>";
		    echo "&nbsp;";
		    echo "</td>";
		    
		   // $listavoti[]=$numordcomp." ".$sintcom;
		    
		    
    	}	
		
		
		print "</tr>";
		fputcsv($fp, $listavoti,";"); // Inserisco la competenza al file  
		
		
      if (!alunno_certificato_pei($alunno,$idmateria,$con)) 
         {
				if (!alunno_certificato_ob_min($alunno,$idmateria,$con))
				   $query="select * from tbl_abildoc where idcompetenza=".$idcompetenza." order by abil_cono desc, numeroordine";
				else
				   $query="select * from tbl_abildoc where idcompetenza=".$idcompetenza." and obminimi order by abil_cono desc, numeroordine";
			}	      
		else
		   $query="select * from tbl_abilalu where idcompetenza=".$idcompetenza." order by abil_cono desc, numeroordine";
		$risab=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));    
		while($nomab=mysqli_fetch_array($risab))
        { 
			$listavoti=array();
			$idabilita=$nomab['idabilita'];
			$sintabil=$nomab['sintabilcono'];
			$numordabil=$nomab['numeroordine'];
			$abilcon=$nomab['abil_cono'];
			$numvoti=0;
            $totvoti=0;
			print "<tr>";
		    print "<td>$abilcon$numordcomp.$numordabil $sintabil</td>";
		    
		    $listavoti[]=$abilcon.$numordcomp.$numordabil." ".$sintabil;   // Aggiungo la definizione dell'obiettivo al file
		    for ($i=0;$i<count($datelez);$i++)
  	        {
		        echo "<td align='center'>";
		        // RICERCA VOTO SE ESISTE IN QUESTA DATA 
		        $votinum=array();
		        $votinum=CercaVoto($datelez[$i],$idabilita,$date,$voti,$abilita);
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
	fclose($fp);
	echo "</table>";
	print ("<br/><center><a href='$cartellabuffer/$nf'><img src='../immagini/csv.png'></a></center>");
}   


  
 // fine if
  



mysqli_close($con);
stampa_piede(""); 


function CercaVoto ($dl,$idab, $datel, $vot, $abi)
{
	
	$votiinseriti=array();
	$numabil=count($abi);
		
	for ($i=0;$i<$numabil;$i++)
	 {
       
       if($dl==$datel[$i] && $idab==$abi[$i])
         $votiinseriti[]=$vot[$i];
     }     
    return $votiinseriti;
}          

