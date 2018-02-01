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


$titolo="Riepilogo argomenti";
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
stampa_head($titolo,"",$script,"SDMAP");

$annoscolastico=$annoscol."/".($annoscol+1);

print ('<body class="stampa" onLoad="printPage()">');

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 


  
   $catt = stringa_html('cattedra');
   if ($catt != "")
   {
      // RECUPERO idalunno e idmateria dalla cattedra
      // Prelevo alunno e materia dalla cattedra selezionata
       $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

        
      
       if ($catt<>"")
       {
           $query="select idalunno, idmateria from tbl_cattnosupp where idcattedra=$catt"; 
           $ris=mysqli_query($con,inspref($query));
           if($nom=mysqli_fetch_array($ris))
           {
              $idmateria=$nom['idmateria'];
              $idalunno=$nom['idalunno'];
           }
       }

   } 
   $id_ut_doc = $_SESSION["idutente"];







$query="select iddocente, cognome, nome from tbl_docenti where idutente=$id_ut_doc";

$ris=mysqli_query($con,inspref($query));
if($nom=mysqli_fetch_array($ris))
{
   $iddocente=$nom["iddocente"];
   $cognomedoc=$nom["cognome"];
   $nomedoc=$nom["nome"];
   $nominativo =$nomedoc." ".$cognomedoc;  
}

 $alunno=estrai_dati_alunno($idalunno,$con);
 $nomemateria=decodifica_materia($idmateria,$con);
 
if ($_SESSION['suffisso']!="") $suff=$_SESSION['suffisso']."/"; else $suff="";    
print ("<center><img src='../abc/".$suff."testata.jpg' width='600'></center>");

print ("<font size=2><center><br/>A.S. <i>$annoscolastico</i> <br/>Argomenti e attivit&agrave; <br/>Alunno <i>$alunno</i> - Materia: <i>$nomemateria</i>");
    
             
print("<br/>Docente: <i>$nominativo</i> ");

print ("</font>");
  
          
 
            
//
//   ESTRAZIONE DATI DELLE LEZIONI
//

    print ("<table border=1 width=100%>");
    if ($idalunno!="")
    {
      echo'
          <tr class="prima">
          
          <td width=10%>Data</td>
          <td width=90%>Argomenti e attivit&agrave;</td></tr>';
      $query="select * from tbl_lezionicert where idalunno=$idalunno and idmateria=$idmateria order by datalezione";
      $rislez=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    
	  
				   

      while ($reclez=mysqli_fetch_array($rislez))
      {       
        print "<tr><td>".data_italiana($reclez['datalezione'])."</td><td>";
        if ($reclez['argomenti']!="") print($reclez['argomenti'])."&nbsp;<br/>";
        if ($reclez['attivita']!="") print($reclez['attivita'])."&nbsp;";
        print("</td></tr>");
      } 
      print("</table>");
      
      
      print("<br/><br/><table border=0 width=100%><tr><td width=50%>&nbsp</td><td width=50% align='center'>Il docente<br/>(Prof. $nomedoc $cognomedoc)<br/><br/>______________________________</td></tr></table>");
    }
   
  
 // fine if
  




mysqli_close($con);


