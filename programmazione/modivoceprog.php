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

$titolo="Modifica voci programmazione";

	$script="";
    stampa_head($titolo,"",$script,"SDMAP");
	stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 


//
//    Fine parte iniziale della pagina
//



$cattedra = stringa_html('cattedra');

$nominativo="";

$idclasse="";
$idmateria="";





print ("
   <form method='post' action='modivoceprog.php' name='valabil'>
   
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
	  
  
          
           
         $query="select idcattedra,tbl_classi.idclasse,tbl_materie.idmateria, anno, sezione, specializzazione, denominazione from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria and tbl_cattnosupp.idalunno=0 order by anno, sezione, specializzazione, denominazione";
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
        

print("</table></form>");        
        
        

if ( $cattedra=="")
   print "";   
else
      {
		  
	      // Carico in una combobox a scelta multipla tutte le voci della programmazione
	      
	      print "<form method='post' action='updvoceprog.php' name='votiabil' >";
	      
		  print "<table align='center'>
					 <tr>
						 <td valign='top'> <center><b>Abilit&aacute; e conoscenze:</b><br/></center>";
	      // Conto competenze, abilità e conoscenze per dimensionare la select multiple
	      $query="select count(*) as numcomp from tbl_competdoc
	              where idmateria = $idmateria and  idclasse = $idclasse";
	      $ris= mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
	      $nomcomp=mysqli_fetch_array($ris);
	      $numcomp=$nomcomp['numcomp'];     
	       
	      $query="select count(*) as numabil from tbl_abildoc,tbl_competdoc
	              where tbl_abildoc.idcompetenza=tbl_competdoc.idcompetenza 
	              and idmateria = $idmateria and  idclasse = $idclasse";
	      $ris= mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
	      $nomabil=mysqli_fetch_array($ris);
	      $numabil=$nomabil['numabil'];   
	      
	      $totalerighe=$numabil+$numcomp; 
	       
	      print "<select name='abil' size='$totalerighe'>" ;     
	      $query="select * from tbl_competdoc
	              where idmateria = $idmateria and  idclasse = $idclasse
	              order by numeroordine"; 
          $riscomp=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
          
	      while ($nomcomp=mysqli_fetch_array($riscomp))
	      {
			  $idcompetenza=$nomcomp['idcompetenza'];
			  
			  print "<optgroup label='".$nomcomp['numeroordine'].". ".$nomcomp['sintcomp']."'>";
			  
			  //CARICO LE CONOSCENZE
			  
			  $query="select * from tbl_abildoc
	              where idcompetenza=$idcompetenza
	              and abil_cono = 'C'
	              order by numeroordine"; 
	      
              $risabil=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
		
			  while ($nomabil=mysqli_fetch_array($risabil))
	          {
			     $idabilita=$nomabil['idabilita'];
			     $sintabil=$nomabil['sintabilcono'];
			     print "<option value='$idabilita' ";
			     
			     print ">CO.".$nomcomp['numeroordine'].".".$nomabil['numeroordine']." $sintabil</option>";
			  }
			  //CARICO LE COMPETENZE
			  $query="select * from tbl_abildoc
	              where idcompetenza=$idcompetenza
	              and abil_cono = 'A'
	              order by numeroordine"; 
	      
              $risabil=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
		
			  while ($nomabil=mysqli_fetch_array($risabil))
	          {
			     $idabilita=$nomabil['idabilita'];
			     $sintabil=$nomabil['sintabilcono'];
			     print "<option value='$idabilita' ";
			     
			     print "> AB.".$nomcomp['numeroordine'].".".$nomabil['numeroordine']." $sintabil</option>";
			  }
	          print "</optgroup>";
	         
	   }  
	   print "</select>";
	   print "<input type='hidden' name='idcatt' value='$cattedra'>";
	   print "</td></tr>";
	   
	   echo "</table>";
	   print "<center><input type='submit' value='Modifica voce di programma'></center></form>";
    }

mysqli_close($con);
stampa_piede(""); 


