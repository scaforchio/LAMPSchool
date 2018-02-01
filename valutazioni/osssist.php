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


$titolo="Inserimento e modifica osservazioni sistematiche";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 
$idnota = stringa_html('idnota');

if ($idnota!="")   // se si arriva dalla pagina della ricerca
{
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
     

    
    $query="select * from tbl_osssist where idosssist=".$idnota."";
    $ris=mysqli_query($con,inspref($query));
    $nom=mysqli_fetch_array($ris);
    $nome=$nom['idclasse'];
    // $but = stringa_html('visass');
    $giorno=substr($nom['data'],8,2);
    $mese=substr($nom['data'],5,2);
    $anno=substr($nom['data'],0,4);
    // $but = stringa_html('visass');
    $iddocente=$nom['iddocente'];
    $idalunno=$nom['idalunno'];
}
else     
{
   $nome = stringa_html('idclasse');
   $giorno = stringa_html('gio');
   $iddocente = is_stringa_html('iddocente') ? stringa_html('iddocente') : $_SESSION['idutente'];
   $idalunno = stringa_html('idalunno');
   $meseanno = stringa_html('mese');  // In effetti contiene sia il mese che l'anno
   // Divido il mese dall'anno
   $mese=substr($meseanno,0,2);
   $anno=substr($meseanno,5,4);
}

$data=$anno."-".$mese."-".$giorno;
$giornosettimana=giorno_settimana($data);
if ($giorno=='')
   $giorno=date('d');
if ($mese=='')
   $mese=date('m');
if ($anno=='')
   $anno=date('Y');



print ('
   <form method="post" action="osssist.php" name="tbl_osssist">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="idclasse" ONCHANGE="tbl_osssist.submit()">
      <option value="">&nbsp;  '); 
	  
  
       
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 

// Riempimento combo box tbl_classi

$iddocente=$_SESSION['idutente'];
$query="select distinct tbl_classi.idclasse,anno,sezione,specializzazione 
        from tbl_classi,tbl_cattnosupp
        where tbl_classi.idclasse=tbl_cattnosupp.idclasse
        and tbl_cattnosupp.iddocente=$iddocente
        order by specializzazione, sezione, anno";
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
   print "<option value='";
   print ($nom["idclasse"]);
   print "'";
   if ($nome==$nom["idclasse"])
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

if ($nome!="")
{


//
//   Inizio visualizzazione della data
//
echo('      <tr>
      <td width="50%"><p align="center"><b>Data (gg/mm/aaaa)</b></p></td>');


    echo('   <td width="50%">');
    echo('   <select name="gio" ONCHANGE="tbl_osssist.submit()">');
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

    echo('   <select name="mese" ONCHANGE="tbl_osssist.submit()">');
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
   echo ("<select name='iddocente' ONCHANGE='tbl_osssist.submit()'>");
else
   echo ("<select name='iddocente' ONCHANGE='tbl_osssist.submit()' disabled>");   

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
print "<tr><td width='50%'><p align='center'><b>Alunno</b></p></td><td>";
$query="select idalunno,cognome,nome,datanascita from tbl_alunni where idclasse=$nome order by cognome, nome, datanascita";
$ris=mysqli_query($con,inspref($query));
echo ("<select name='idalunno' ONCHANGE='tbl_osssist.submit()'><option value=''>&nbsp;");
while($nom=mysqli_fetch_array($ris))
{
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
   
}
        
echo('
      </SELECT>
      </td></tr>');

}
//   Fine riempimento combo box tbl_docenti

echo('</table>
 
    <table align="center">
      <td>');
     //   <p align="center"><input type="submit" value="Visualizza" name="b"></p>
echo('     </form></td>
   
</table><hr>
 
    ');
   
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
  

 // print($nome." -   ". $m.$g.$a.$giornosettimana);
  
  if (($idalunno!="")&&((checkdate($m,$g,$a))& !($giornosettimana=="Dom")))
  {
    $idclasse=$nome;
    $classe="";
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
     
   
 //   $query='select * from tbl_classi where idclasse="'.$idclasse.'" ';
 //   $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
 //   if($val=mysqli_fetch_array($ris))
 //      $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];
  //  print $iddocente;
    $query="select * from tbl_osssist where idclasse=$idclasse and data='$data' and iddocente=$iddocente and idalunno=$idalunno";
  //  print $query;
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query di selezione nota: ". mysqli_error($con));

    $c=mysqli_fetch_array($ris);

    if ($c==NULL) 
    {
       echo "<form method='post' action='insosssist.php'>
             <table border=2 align='center'><tr class=prima><td align='center'><b>Osservazione</b></td></tr><tr><td>";
       echo "<textarea cols=60 rows=10 name ='notacl'>";
       echo "";
       echo "</textarea><br/>";
       echo "</td>"; 
    }
    else
    {
       echo "<form method='post' action='insosssist.php'>
             <table border=2 align='center'><tr class=prima><td align='center'><b>Osservazione</b></td></tr><tr><td>";
       echo "<textarea  cols=60 rows=10 name ='notacl'>";
       echo $c['testo'];
       echo "</textarea><br/>";
       echo "</td>";
    }
     echo'</td></tr></table>';
 
    echo'
  
          <table align="center">
          <tr> </tr>
          </table>
          <p align="center"><input type=submit name=b value="Inserisci osservazione">
          <p align="center"><input type=hidden value='.$idclasse.' name=idclasse>
	  <p align="center"><input type=hidden value='.$giorno.' name=gio>
	  <p align="center"><input type=hidden value='.$mese.' name=mese>
          <p align="center"><input type=hidden value='.$anno.' name=anno>
          <p align="center"><input type=hidden value='.$iddocente.' name=iddocente>
          <p align="center"><input type=hidden value='.$idalunno.' name=idalunno>
          </form>
         ';

  } 
  else
  {
     if ($giornosettimana=="Dom")
         print("<Center> <big><big>Il giorno selezionato &egrave; una domenica<small><small> </center>");
     else  
         if ($idalunno=="")
             print("");
         else
             print("<Center> <big><big>La data selezionata non &egrave; valida<small><small> </center>");
  }
 // fine if
  
mysqli_close($con);
stampa_piede(""); 

