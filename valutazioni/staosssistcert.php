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


$titolo="Stampa osservazioni sistematiche";
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
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

$idcattedra = stringa_html('idcattedra');
$periodo = stringa_html('periodo');
$iddocente = is_stringa_html('iddocente') ? stringa_html('iddocente') : $_SESSION['idutente'];
$idalunno = estrai_alunno_da_cattedra_pei($idcattedra, $con);
$idclasse = estrai_classe_alunno($idalunno,$con);
$idmateria = estrai_id_materia($idcattedra,$con);


// VISUALIZZO LE OSSERVAZIONI  


 
    
     if ($periodo=="Primo")
        $query="select data, tbl_alunni.idalunno, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno, testo 
                from tbl_osssist, tbl_alunni
                where tbl_osssist.idalunno=tbl_alunni.idalunno  
                and tbl_osssist.iddocente=$iddocente
                and tbl_osssist.idmateria = $idmateria 
                and tbl_osssist.idalunno = $idalunno and data <= '".$fineprimo."'
                order by data";
   
    if ($periodo=="Secondo" & $numeroperiodi==2)
        $query="select data, tbl_alunni.idalunno, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno, testo 
                from tbl_osssist, tbl_alunni
                where tbl_osssist.idalunno=tbl_alunni.idalunno  
                and tbl_osssist.iddocente=$iddocente
                and tbl_osssist.idmateria = $idmateria 
                and tbl_osssist.idalunno = $idalunno and data > '".$fineprimo."'
                order by data";

    if ($periodo=="Secondo"  & $numeroperiodi==3 )
        $query="select data, tbl_alunni.idalunno, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno, testo
                from tbl_osssist, tbl_alunni
                where tbl_osssist.idalunno=tbl_alunni.idalunno  
                and tbl_osssist.iddocente=$iddocente
                and tbl_osssist.idmateria = $idmateria 
                and tbl_osssist.idalunno = $idalunno 
                and  data >  '".$fineprimo."' and data <=  '".$finesecondo."'
                order by data";
    if ($periodo=="Terzo")
        $query="select data, tbl_alunni.idalunno, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno, testo 
                from tbl_osssist, tbl_alunni
                where tbl_osssist.idalunno=tbl_alunni.idalunno  
                and tbl_osssist.iddocente=$iddocente
                and tbl_osssist.idmateria = $idmateria 
                and tbl_osssist.idalunno = $idalunno and data > '".$finesecondo."'
                order by data";
    if ($periodo=="Tutti")
        $query="select data, tbl_alunni.idalunno, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno, testo 
                from tbl_osssist, tbl_alunni
                where tbl_osssist.idalunno=tbl_alunni.idalunno  
                and tbl_osssist.iddocente=$iddocente
                and tbl_osssist.idmateria = $idmateria 
                and tbl_osssist.idalunno = $idalunno 
                order by data";

  

    $ris=mysqli_query($con,inspref($query)) or die ("Errore: ".inspref($query));

 
    $c=mysqli_num_rows($ris);
   
    
    if ($c==0) 
    {
       echo "<center><b><br/>Nessuna osservazione!</b></center><br/>";
    }
    else
    {
		
		 
		 print "<center><b>Osservazioni del docente ".estrai_dati_docente($iddocente, $con)."</b></center><br>";
		 print "<center><br><b>Materia: ".decodifica_materia($idmateria, $con)."</b></center><br>";
		 print "<center><br><b>Alunno: ".estrai_alunno_data($idalunno, $con)."</b></center><br>";
		 
       print "<table border=1 width=95% align='center'>";
       while ($rec=mysqli_fetch_array($ris))
       {   
           
           print("<tr class='prima'><td>Data</td><td>Osservazione</td></tr>");
           
           
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


