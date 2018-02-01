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

$iddocente=$_SESSION['idutente'];


$titolo="Ricerca osservazioni sistematiche";
$script= "<script>
            function printPage()
            {
               if (window.print)
                  window.print();
               else 
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');
            }
         </script>"; 

stampa_head($titolo,"",$script,"SDMAP");





print ("<body  onLoad='printPage()' >");

$classe = stringa_html('classe');
$periodo = stringa_html('periodo');


$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 

//
//  VISUALIZZO I DATI DELLA CLASSE 
//

print ("");

$query = "select * from tbl_classi where idclasse = $classe";
$ris=mysqli_query($con,inspref($query));
$cla=mysqli_fetch_array($ris); 




//
// VISUALIZZO LE NOTE INDIVIDUALI  

     $periodo=" ";
     if ($periodo=="Primo")
        $periodo=" and data <= '".$fineprimo."'";
     if ($periodo=="Secondo" & $numeroperiodi==2)
        $periodo=" and data > '".$fineprimo."'";
     if ($periodo=="Secondo"  & $numeroperiodi==3 )
        $periodo=" and  data >  '".$fineprimo."' and data <=  '".$finesecondo."'";
     if ($periodo=="Terzo")
        $periodo=" and data > '".$finesecondo."'";
      
     if ($tipoutente!="P")    
        $query="select data, tbl_alunni.idalunno, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno, testo 
             from tbl_osssist, tbl_alunni
             where tbl_osssist.idalunno=tbl_alunni.idalunno  
             and tbl_osssist.iddocente=$iddocente
             and tbl_osssist.idclasse = $classe $periodo
             order by cognalunno,nomealunno,data";
     else
        $query="select data, tbl_docenti.cognome,tbl_docenti.nome,tbl_alunni.idalunno, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno, testo 
             from tbl_osssist, tbl_alunni,tbl_docenti
             where tbl_osssist.idalunno=tbl_alunni.idalunno  
             and tbl_osssist.iddocente=tbl_docenti.iddocente
             and tbl_osssist.idclasse = $classe $periodo
             order by tbl_docenti.cognome,tbl_docenti.nome,cognalunno,nomealunno,data"; 
    

    $ris=mysqli_query($con,inspref($query));

 
    $c=mysqli_num_rows($ris);
   
    
    if ($c==0) 
    {
       echo "<center><b><br/>Nessuna osservazione!</b></center><br/>";
    }
    else
    {
		 $idalunno="";
		 $docente="x";
		// print "<center><br><b>Osservazioni del docente ".estrai_dati_docente($iddocente, $con)." classe ".decodifica_classe($classe, $con)."</b></center><br>";
		 
       print "<table border=1 width=95%>";
       while ($rec=mysqli_fetch_array($ris))
       {   
           $doc=$rec['cognome']." ".$rec['nome'];
           
           if ($doc!=$docente)
            {  
					print "<tr><td colspan='2'><center><br><b>Osservazioni del docente $doc</b></center><br></td></tr>";
		         $docente=$doc;
				}
           if ($idalunno!=$rec['idalunno'])
           {  
				  
				  $idalunno=$rec['idalunno'];
				  if (!alunno_certificato($idalunno,$con))
                 $cert="";
              else
                 $cert=" (*)";      
              print "<tr class=prima><td colspan=2><center><small><b>Alunno:&nbsp;".$rec['cognalunno']."&nbsp;".$rec['nomealunno']."&nbsp;nato il ".data_italiana($rec['dataalunno'])."$cert</td></tr>";
			  }
           print("<tr>");
           print("<td width=20%><small>");
           print("".data_italiana($rec['data']).""); 
           print("</td>");
           print("<td width=80%><small>");
           print("".$rec['testo'].""); 
           print("</td>");
           print("</tr>"); 

       }
       print "</table>";
    }
  
// stampa_piede("");  
mysqli_close($con);


