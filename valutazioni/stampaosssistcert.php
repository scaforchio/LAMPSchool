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
$script=""; 

stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con)); 

print ('
   <form method="post" action="staosssistcert.php" target="_blank" name="note">
   
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
      </td></tr>");

//
//   Inizio visualizzazione del combo box del periodo
//
if ($numeroperiodi==2)
   print('<tr><td width="50%"><b>Quadrimestre</b></td>');
else
   print('<tr><td width="50%"><b>Trimestre</b></td>');

echo('   <td width="50%">');
echo('   <select name="periodo">');


  echo("<option>Primo</option>");
  echo("<option>Secondo</option>");

if ($numeroperiodi==3)
     echo("<option>Terzo</option>");

  echo("<option selected>Tutti</option>");

  
echo("</select>");
echo("</td></tr>");

echo('</table>
 
    <table align="center">
      <td>
        <p align="center"><input type="submit" value="Stampa" name="b" onclick="Popup(staosssistcert.php)"></p>
     </form></td>
   
</table><hr>
 
    ');
 

  
mysqli_close($con);
stampa_piede(""); 

