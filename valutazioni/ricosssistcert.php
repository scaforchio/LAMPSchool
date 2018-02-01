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
$titolo="Ricerca osservazioni sistematiche";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 


       
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 
$idcattedra=stringa_html('idcattedra');

if ($tipoutente!='P')
   $iddocente=$_SESSION['idutente']; 


/*
if ($giorno=='')
   $giorno=date('d');
if ($mese=='')
   $mese=date('m');
if ($anno=='')
   $anno=date('Y');
*/

print ('
   <form method="post" action="ricosssistcert.php" name="tbl_osssist">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Alunno/materia</b></p></td>
      <td width="50%">
      <SELECT NAME="idcattedra" ONCHANGE="tbl_osssist.submit()">
      <option value="">&nbsp;  '); 
	  
  

$query="select idcattedra, tbl_cattnosupp.idmateria, tbl_cattnosupp.idclasse, tbl_cattnosupp.idalunno, cognome, nome, datanascita 
                 from tbl_cattnosupp, tbl_alunni
                 where iddocente=$iddocente 
                 and tbl_cattnosupp.idalunno = tbl_alunni.idalunno
                 order by cognome, nome, datanascita";
          
          
           
         
          $ris=mysqli_query($con,inspref($query));
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
      </td></tr></table></form>");
   
if ($idcattedra!="")
{
print (' 
    <table align="center">
      <td>');
   //     <p align="center"><input type="submit" value="Visualizza" name="b"></p>
echo('</form></td>
   
</table><hr>
 
    ');
 
/*  
  if ($mese=="")
     $m=0;
  else
     $m=$mese; 
  if ($giorno=="") 
     $g=0;
  else
     $g=$giorno; 

  if ($anno=="") 
     $a=0;
  else
     $a=$anno; 
*/  

 // print($nome." -   ". $m.$g.$a.$giornosettimana);
  
  
       
       $stringaricerca=$stringaricerca." tbl_osssist.iddocente=$iddocente ";
       $stringaricerca=$stringaricerca." and tbl_osssist.idalunno=$idalunno ";
       $stringaricerca=$stringaricerca." and tbl_osssist.idmateria=$idmateria ";
    
     
   
 //   $query='select * from tbl_classi where idclasse="'.$idclasse.'" ';
 //   $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
 //   if($val=mysqli_fetch_array($ris))
 //      $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];
  //  print $iddocente;
    $query="select idosssist, data, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, specializzazione, sezione,anno, tbl_alunni.datanascita, testo 
            from tbl_osssist,tbl_classi, tbl_alunni, tbl_docenti 
            where tbl_osssist.idclasse=tbl_classi.idclasse and  tbl_osssist.iddocente=tbl_docenti.iddocente  and tbl_osssist.idalunno=tbl_alunni.idalunno  
            and $stringaricerca 
            order by data";
   // print $query."<br/>";
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query di selezione osservazione: ". mysqli_error($con));

    $c=mysqli_num_rows($ris);
   
    
    if ($c==0) 
    {
       echo "<center><b>Nessuna osservazione da visualizzare!</b></center>";
    }
    else
    {
       print "<table border=1  align='center'>";
       print "<tr class='prima'><td>Data</td><td>Osservazione</td><td>Modif.</td></tr>";
       while ($rec=mysqli_fetch_array($ris))
       {   
           print("<tr>");
           print("<td>");
           print(data_italiana($rec['data'])); 
           print("</td>");
           print("<td>");
           print("<i>".$rec['testo']."</i>"); 
           print("</td>");
           
           print("<td>");
           print("<center><a href='osssistcert.php?idosssist=".$rec['idosssist']."' title='Modifica'><img src='../immagini/modifica.png' alt='Modifica'></a>"); 
           print("</td>");
           print("</tr>"); 

       }
       print "</table>";
       
    }
  
 } 
mysqli_close($con);
stampa_piede(""); 

