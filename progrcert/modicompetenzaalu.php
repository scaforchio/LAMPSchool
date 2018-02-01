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

$titolo="Modifica competenze";

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

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
  



   
  
          
           
         $query="select idcattedra,tbl_materie.idmateria, cognome, nome, datanascita, denominazione, tbl_alunni.idalunno
                 from tbl_cattnosupp, tbl_alunni, tbl_materie, tbl_tipoprog
                 where iddocente=$iddocente 
                 and tbl_cattnosupp.idalunno=tbl_alunni.idalunno 
                 and tbl_cattnosupp.idmateria = tbl_materie.idmateria 
                 and tbl_cattnosupp.idmateria = tbl_tipoprog.idmateria
                 and tbl_cattnosupp.idalunno = tbl_tipoprog.idalunno
                 and tbl_tipoprog.tipoprogr='P'
                 order by cognome,nome,datanascita,denominazione";
          $ris=mysqli_query($con,inspref($query))  or die ("Errore: ". inspref($query));
         
       if (mysqli_num_rows($ris)>0)
      {  
			
			print ("
                 <form method='post' action='modicompetenzaalu.php' name='valabil'>
   
                 <p align='center'>
                  <table align='center'>");
   
   //
   //   Leggo il nominativo del docente e lo visualizzo
   //

   
     
               $query="select iddocente, cognome, nome from tbl_docenti where iddocente=$iddocente";
   
               $risalu=mysqli_query($con,inspref($query));
   
   
   
             if($nom=mysqli_fetch_array($risalu))
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
     
  
          while($nom=mysqli_fetch_array($ris))
         {
            print "<option value='";
            print ($nom["idcattedra"]);
            print "'";
            if ($cattedra==$nom["idcattedra"])
            {
               print " selected";
               $idmateria=$nom["idmateria"];   // Memorizzo materia e classe della cattedra selezionata
               $idalunno=$nom["idalunno"];
            }
            print ">";
            print ($nom["cognome"]);
            print "&nbsp;"; 
            print($nom["nome"]); 
            print "&nbsp;(";
            print($nom["datanascita"]);
            print ")&nbsp;-&nbsp;";
            print($nom["denominazione"]);
            
          }
        
        print "</select>";
        
 
  
    print("</table></form>");        
}
else
   print "<br><br><center><b>Nessuna cattedra per alunni con programma personalizzato!</b></center><br>";
           
        

if ($cattedra=="")
   print "";   
else
   {
        // Carico in una combobox a scelta multipla tutte le competenze della programmazione
        print "<form method='post' action='updcompprogalu.php' name='updcomp' >";
         
        print "<table align='center'>
                <tr>
                   <td valign='top'> <center><b>Competenze:</b><br/></center>";
         // Conto competenze, abilità e conoscenze per dimensionare la select multiple
         $query="select count(*) as numcomp from tbl_competalu
                 where idmateria = $idmateria and idalunno = $idalunno";
         // print $query;        
         $ris= mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query));
         $nomcomp=mysqli_fetch_array($ris);
         $numcomp=$nomcomp['numcomp'];     
          
         $totalerighe=$numcomp; 
          
         print "<select name='comp' size='$totalerighe'>" ;     
         $query="select * from tbl_competalu
                 where idmateria = $idmateria and  idalunno = $idalunno
                 order by numeroordine"; 
          $riscomp=mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query));
          
         while ($nomcomp=mysqli_fetch_array($riscomp))
         {
           $idcompetenza=$nomcomp['idcompetenza'];
           
           print "<option value='".$nomcomp['idcompetenza']."'>".$nomcomp['sintcomp']."</option>";
         }  
      print "</select>";
      print "<input type='hidden' name='idcatt' value='$cattedra'>";
      print "</td></tr>";
      
      echo "</table>";
      print "<center><input type='submit' value='Modifica competenza di programma'></center></form>";
    }

mysqli_close($con);
stampa_piede(""); 


      

