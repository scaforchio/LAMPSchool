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
  
//
//    VISUALIZZAZIONE DELLA SITUAZIONE DELLE NOTE
//    PER I GENITORI 
//




@require_once("../php-ini".$_SESSION['suffisso'].".php");
@require_once("../lib/funzioni.php");
	
//  istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente=="")
{
  header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
  die;
} 


$titolo="Situazione note alunno";
$script=""; 

stampa_head($titolo,"",$script,"TDSPAML");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");

$codalunno=$_SESSION['idstudente'];
// $codclasse = stringa_html('classe');

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 

$query="select idclasse from tbl_alunni where idalunno=$codalunno";
$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
$rec=mysqli_fetch_array($ris);
$codclasse=$rec['idclasse'];  

// prelevamento dati alunno

$query="select * from tbl_alunni,tbl_classi where tbl_alunni.idclasse=tbl_classi.idclasse and idalunno='$codalunno'";
$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
  
echo '<table border=1 align="center" width="800"  >';
  
if($val=mysqli_fetch_array($ris))
{
   echo ' 
       <tr>
          <td align=center><b> Alunno: '.$val["cognome"].' '.$val["nome"].' Classe '.$val["anno"].' '.$val["sezione"].' '.$val["specializzazione"].'</b></td>
        </tr> </table><br>
   ';
}



$query="select tbl_notealunno.idnotaalunno, data, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, tbl_alunni.datanascita, testo, provvedimenti 
            from tbl_noteindalu, tbl_notealunno,tbl_classi, tbl_alunni, tbl_docenti 
            where 
            tbl_noteindalu.idnotaalunno=tbl_notealunno.idnotaalunno
            and tbl_noteindalu.idalunno=tbl_alunni.idalunno
            and tbl_notealunno.idclasse=tbl_classi.idclasse and  tbl_notealunno.iddocente=tbl_docenti.iddocente 
            and tbl_noteindalu.idalunno=$codalunno 
            order by tbl_notealunno.data desc";
  // print inspref($query);
$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query di selezione nota: ". mysqli_error($con));

$c=mysqli_num_rows($ris);

    
    
print "<table border=1 align=center width=800>";
print "<tr class=prima><td colspan=4 align=center>Note e provvedimenti disciplinari individuali</td></tr>";
if ($c==0) 
{
   echo "<tr><td colspan=4 align=center>Nessuna nota da visualizzare!</td></tr>";
}
else
{
   print "<tr class=prima><td>Docente</td><td>Data</td><td>Nota</td><td>Provv.</td></tr>";
   while ($rec=mysqli_fetch_array($ris))
   {   
      print("<tr>");
         
      print("<td>");
      print($rec['cogndocente']." ".$rec['nomedocente']); 
      print("</td>");
      print("<td>");
      print(data_italiana($rec['data'])); 
      print("</td>");
        
      print("<td>");
      print("".$rec['testo'].""); 
      print("</td>");
      print("<td>");
      print("".$rec['provvedimenti'].""); 
      print("</td>");
         
      print("</tr>"); 

   }

       
}
print "</table><br>";

$query="select idnotaclasse, data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, testo, provvedimenti 
            from tbl_noteclasse, tbl_classi, tbl_docenti 
            where tbl_noteclasse.idclasse=tbl_classi.idclasse and  tbl_noteclasse.iddocente=tbl_docenti.iddocente  
            and tbl_classi.idclasse=$codclasse 
            and data not in (select data from tbl_assenze where idalunno = $codalunno)
            order by tbl_noteclasse.data desc";
   // print $query."<br/>";
$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query di selezione nota: ". mysqli_error($con));

$c=mysqli_num_rows($ris);

    
print "<table border=1 align=center width=800>";
print "<tr class=prima><td colspan=4 align=center>Note di classe</td></tr>";
if ($c==0) 
{
   echo "<tr><td colspan=4 align=center>Nessuna nota da visualizzare!</td></tr>";
}
else
{ 
   print "<tr class=prima><td>Docente</td><td>Data</td><td>Nota</td><td>Provv.</td></tr>";
   while ($rec=mysqli_fetch_array($ris))
   {   
       print("<tr>");
       
       print("<td>");
       print($rec['cogndocente']." ".$rec['nomedocente']); 
       print("</td>");
       print("<td>");
       print(data_italiana($rec['data'])); 
       print("</td>");
       print("<td>");
       print("".$rec['testo'].""); 
       print("</td>");
       print("<td>");
       print("".$rec['provvedimenti'].""); 
       print("</td>");
       
       print("</tr>"); 
   }
   

}
  print "</table> <br/> <br/>";   

mysqli_close($con);
stampa_piede(""); 




