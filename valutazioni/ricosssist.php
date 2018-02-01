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
 




$idclasse = stringa_html('idclasse');
// $but = stringa_html('visass');
$giorno = stringa_html('gio');
$iddocente = stringa_html('iddocente');
if ($tipoutente!='P')
   $iddocente=$_SESSION['idutente']; 
$idalunno = stringa_html('idalunno');
$meseanno = stringa_html('mese');  // In effetti contiene sia il mese che l'anno
// Divido il mese dall'anno
$mese=substr($meseanno,0,2);
$anno=substr($meseanno,5,4);
$data=$anno."-".$mese."-".$giorno;
$giornosettimana=giorno_settimana($data);

/*
if ($giorno=='')
   $giorno=date('d');
if ($mese=='')
   $mese=date('m');
if ($anno=='')
   $anno=date('Y');
*/

print ('
   <form method="post" action="ricosssist.php" name="tbl_osssist">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="idclasse" ONCHANGE="tbl_osssist.submit()">
      <option value="">&nbsp;  '); 
	  
  
       
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 

// Riempimento combo box tbl_classi
if ($tipoutente=="P")
   $query="select tbl_classi.idclasse,anno,sezione,specializzazione 
           from tbl_classi
           order by specializzazione, sezione, anno";
else
   $query="select tbl_classi.idclasse,anno,sezione,specializzazione 
           from tbl_classi,tbl_cattnosupp
           where tbl_classi.idclasse=tbl_cattnosupp.idclasse
           and tbl_cattnosupp.iddocente='$iddocente'
           order by specializzazione, sezione, anno";      
//print inspref($query);        
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
   print "<option value='";
   print ($nom["idclasse"]);
   print "'";
   if ($idclasse==$nom["idclasse"])
	print " selected";
   print ">";
   print ($nom["anno"]);
   print "&nbsp;"; 
   print($nom["sezione"]); 
   print "&nbsp;";
   print($nom["specializzazione"]);
}
        
echo('
      </SELECT>
      </td></tr>');




//
//   Inizio visualizzazione della data
//
echo('      <tr>
      <td width="50%"><p align="center"><b>Data (gg/mm/aaaa)</b></p></td>');


    echo('   <td width="50%">');
    echo('   <select name="gio" ONCHANGE="tbl_osssist.submit()"><option value="">&nbsp');
    for($g=1;$g<=31;$g++)
    {
      if ($g<10)
         $gs='0'.$g;
      else
         $gs=''.$g; 
      if ($gs==$giorno)
         echo("<option selected>$gs");
      else
         echo("<option>$gs");
    }
    echo("</select>");

    echo('   <select name="mese" ONCHANGE="tbl_osssist.submit()"><option value="">&nbsp');
    for($m=9;$m<=12;$m++)
    {
      if ($m<10)
         $ms='0'.$m;
      else
         $ms=''.$m; 
      if ($ms==$mese)
         echo("<option selected>$ms - $annoscol");
      else
         echo("<option>$ms - $annoscol");
    } 
    $annoscolsucc=$annoscol+1;
    for($m=1;$m<=8;$m++)
    {
      if ($m<10)
         $ms='0'.$m;
      else
         $ms=''.$m; 
      if ($ms==$mese)
         echo("<option selected>$ms - $annoscolsucc");
      else
         echo("<option>$ms - $annoscolsucc");
    } 
    echo("</select></td></tr>");

  

//
//  Fine visualizzazione della data
//

// Riempimento combo box tbl_docenti
print "<tr><td width='50%'><p align='center'><b>Docente</b></p></td><td>";
$query="select iddocente,cognome,nome from tbl_docenti order by cognome, nome";
$ris=mysqli_query($con,inspref($query));

if ($tipoutente=='P') 
   echo ("<select name='iddocente' ONCHANGE='tbl_osssist.submit()'><option value=''>&nbsp");
else
   echo ("<select name='iddocente' ONCHANGE='tbl_osssist.submit()' disabled><option value=''>&nbsp");   

// echo ("<select name='iddocente' ONCHANGE='tbl_osssist.submit()'>");
while($nom=mysqli_fetch_array($ris))
{
   print "<option value='";
   print ($nom["iddocente"]);
   print "'";
   if ($iddocente==$nom["iddocente"])
	print " selected";
   print ">";
   print ($nom["cognome"]);
   print "&nbsp;"; 
   print($nom["nome"]); 
   
}

// Riempimento combo box tbl_alunni
if ($idclasse!="")
{
  print "<tr><td width='50%'><p align='center'><b>Alunno</b></p></td><td>";
  $query="select idalunno,cognome,nome,datanascita from tbl_alunni where idclasse=$idclasse order by cognome, nome, datanascita";
  $ris=mysqli_query($con,inspref($query));
  echo ("<select name='idalunno' ONCHANGE='tbl_osssist.submit()'><option value=''>&nbsp");
  while($nom=mysqli_fetch_array($ris)) 
  {
	  if (!alunno_certificato($nom['idalunno'],$con))
        $cert="";
     else
        $cert=" (*)";  
     print "<option value='";
     print ($nom["idalunno"]);
     print "'";
     if ($idalunno==$nom["idalunno"])
 	     print " selected";
     print ">";
     print ($nom["cognome"]);
     print "&nbsp;"; 
     print($nom["nome"]); 
     print "&nbsp;&nbsp;&nbsp;"; 
     print(data_italiana($nom["datanascita"])); 
     print $cert;
  }
}
else
{
  print "<tr><td width='50%'><p align='center'><b>Alunno</b></p></td><td>";
  echo ("<select name='idalunno'><option value=''>&nbsp");
  
}
        
echo('
      </SELECT>
      </td></tr>');


//   Fine riempimento combo box tbl_alunni

echo('</table>
 
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
  
  
    $stringaricerca=" true ";
    if ($idclasse!="")
       $stringaricerca=$stringaricerca." and tbl_osssist.idclasse=$idclasse ";
    if ($iddocente!="")
       $stringaricerca=$stringaricerca." and tbl_osssist.iddocente=$iddocente ";
    if ($idalunno!="")
       $stringaricerca=$stringaricerca." and tbl_osssist.idalunno=$idalunno ";
    if ($mese!="")
    {
       $stringaricerca=$stringaricerca." and month(tbl_osssist.data)=$mese ";
       if ($giorno!="")
          $stringaricerca=$stringaricerca." and day(tbl_osssist.data)=$giorno ";
    }
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
     
   
 //   $query='select * from tbl_classi where idclasse="'.$idclasse.'" ';
 //   $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
 //   if($val=mysqli_fetch_array($ris))
 //      $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];
  //  print $iddocente;
    $query="select idosssist, data, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, specializzazione, sezione,anno, tbl_alunni.datanascita, testo 
            from tbl_osssist,tbl_classi, tbl_alunni, tbl_docenti 
            where tbl_osssist.idclasse=tbl_classi.idclasse and  tbl_osssist.iddocente=tbl_docenti.iddocente  and tbl_osssist.idalunno=tbl_alunni.idalunno  
            and $stringaricerca 
            order by tbl_classi.specializzazione, tbl_classi.sezione, tbl_classi.anno, tbl_docenti.cognome, tbl_docenti.nome, tbl_alunni.cognome, tbl_alunni.nome, tbl_alunni.datanascita, tbl_osssist.data";
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
       print "<tr class='prima'><td>Classe</td><td>Docente</td><td>Data</td><td>Alunno</td><td>Osservazione</td><td>Modif.</td></tr>";
       while ($rec=mysqli_fetch_array($ris))
       {   
           print("<tr>");
           print("<td>");
           print($rec['specializzazione']." ".$rec['sezione']." ".$rec['anno']); 
           print("</td>");
           print("<td>");
           print($rec['cogndocente']." ".$rec['nomedocente']); 
           print("</td>");
           print("<td>");
           print(data_italiana($rec['data'])); 
           print("</td>");
           print("<td>");
           print($rec['cognalunno']." ".$rec['nomealunno']." <br/> ".data_italiana($rec['dataalunno'])); 
           print("</td>");
           print("<td>");
           print("<i>".$rec['testo']."</i>"); 
           print("</td>");
           print("<td>");
           if ($tipoutente!="P")
              print("<center><a href='osssist.php?idnota=".$rec['idosssist']."' title='Modifica'><img src='../immagini/modifica.png' alt='Modifica'></a>"); 
           print("</td>");
           print("</tr>"); 

       }
       print "</table>";
       
    }
  
  
mysqli_close($con);
stampa_piede(""); 

