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


$titolo="Stampa diario di classe";
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


print "<center><b>".decodifica_classe($classe,$con)."</b><br>";


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
        $query="select data, tbl_docenti.iddocente, testo , oraultmod
             from tbl_diariocl, tbl_docenti
             where tbl_diariocl.iddocente = tbl_docenti.iddocente $periodo
             and idclasse = $classe
             order by data,tbl_diariocl.oraultmod";
     else
        $query="select data, tbl_docenti.iddocente, testo , oraultmod
             from tbl_diariocl, tbl_docenti
             where tbl_diariocl.idclasse = tbl_docenti.iddocente $periodo
             and idclasse = $classe
             order by data,tbl_diariocl.oraultmod";
    

    $ris=mysqli_query($con,inspref($query));

 
    $c=mysqli_num_rows($ris);
   
    
    if ($c==0) 
    {
       echo "<center><b><br/>Nessuna osservazione!</b></center><br/>";
    }
    else
    {

		 $dat="x";
		// print "<center><br><b>Osservazioni del docente ".estrai_dati_docente($iddocente, $con)." classe ".decodifica_classe($classe, $con)."</b></center><br>";
		 
       print "<table border=1 width=95%>";
       while ($rec=mysqli_fetch_array($ris))
       {   
           $data=$rec['data'];
           
           if ($data!=$dat)
            {  
					print "<tr><td colspan='2'><center><br><b>Data: $data</b></center><br>";
		         $dat=$data;
				}



           print $rec['testo']."<br>";
           print "<p align='right'><i>".estrai_dati_docente($rec['iddocente'],$con). " - ". data_italiana(substr($rec['oraultmod'],0,10))." ".substr($rec['oraultmod'],11,5)."</i></p>";

           print("</td>");
           print("</tr>"); 

       }
       print "</table>";
    }
  
// stampa_piede("");  
mysqli_close($con);


