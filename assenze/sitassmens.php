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


$titolo="Situazione mensile assenze";
$script=""; 
stampa_head($titolo,"",$script,"MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 


$nome = stringa_html('cl');
$but = stringa_html('visass');
$meseanno = stringa_html('mese');  // In effetti contiene sia il mese che l'anno
// Divido il mese dall'anno
$mese=substr($meseanno,0,2);
$anno=substr($meseanno,5,4);

// $giornosettimana=giorno_settimana($anno."-".$mese."-".$giorno);


if ($mese=='')
   $mese=date('m');
if ($anno=='')
   $anno=date('Y');




print ('
   <form method="post" action="sitassmens.php" name="tbl_assenze">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="cl" ONCHANGE="tbl_assenze.submit()"> <option value="">&nbsp '); 
	  
  
       
          $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
           
          $query="select idclasse,anno,sezione,specializzazione from tbl_classi order by specializzazione, sezione, anno";
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
      </td></tr>

      <tr>
      <td width="50%"><p align="center"><b>Data (gg/mm/aaaa)</b></p></td>');

//
//   Inizio visualizzazione della data
//

    

    echo('   <td width="50%">');
    

    echo('   <select name="mese" ONCHANGE="tbl_assenze.submit()">');
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
    echo("</select>");

echo "</td></tr></table>";
  

/*
  echo('   <select name="anno">');
    for($a=$annoscol;$a<=($annoscol+1);$a++)
    {
      if ($a==$anno)
         echo("<option selected>$a");
      else
         echo("<option>$a");
    } 
    echo("</select>");  
*/


//
//  Fine visualizzazione della data
//
if ($nome!="")
{

echo('      </td></tr>
    </table>
 
    <table align="center">
      <td>');
    //    <p align="center"><input type="submit" value="Visualizza assenze" name="b"></p>
echo('     </form></td>
   
</table><hr>
 
    ');
   
  if ($mese=="")
     $m=0;
  else
     $m=$mese; 
  
  if ($anno=="") 
     $a=0;
  else
     $a=$anno; 
  

  // print($nome." -   ". $g.$m.$a.$giornosettimana);
  
    $idclasse=$nome;
    $classe="";
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
     
   
    $query='select * from tbl_classi where idclasse="'.$idclasse.'" ';
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    if($val=mysqli_fetch_array($ris))
       $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];

    $query='select * from tbl_alunni where idclasse="'.$idclasse.'" order by cognome,nome,datanascita';
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));

    $c=mysqli_fetch_array($ris);
    if ($c==NULL) {echo '
                    <p align="center">
		    <font size=4 color="black">Nessun alunno presente nella classe </font>
                   '; exit;
                  }
    echo '<p align="center">
          <font size=4 color="black">Assenze della classe '.$classe.' Mese '.$m.' - '.$a.'</font>
          
          <table border=2 align="center">';
            
    echo'
          <tr class="prima">
          
          <td><font size=1><b> Cognome </b></td>
          <td><font size=1><b> Nome  </b></td>
          <td><font size=1><b> Data di nascita </b></td>';
    for ($gi=1;$gi<=31;$gi++)
    {
       $giornosettimana=giorno_settimana($a."-".$m."-".$gi);
       if (checkdate($m,$gi,$a) & !($giornosettimana=="Dom")) 
       {
          print ("<td><font size=1><center>$gi<br/>$giornosettimana</td>");
       }
    }	          
     
 
    $query='select * from tbl_alunni where idclasse="'.$idclasse.'" order by cognome,nome,datanascita';
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    while($val=mysqli_fetch_array($ris))
    {
      echo ' 
             <tr>
                <td><font size=1><b> '.$val["cognome"].' </b></td>
                <td><font size=1><b> '.$val["nome"].'    </b></td>
                <td><font size=1><b> '.data_italiana($val["datanascita"]).' </b></td>
                ';
      

      // Codice per ricerca assenze già inserite
      $queryass='select data from tbl_assenze where idalunno = '.$val["idalunno"].' and month(data) = "'.$m.'" and year(data)= "'.$a.'" order by data';
      $queryrit='select data from tbl_ritardi where idalunno = '.$val["idalunno"].' and month(data) = "'.$m.'" and year(data)= "'.$a.'" order by data';
      $queryusc='select data from tbl_usciteanticipate where idalunno = '.$val["idalunno"].' and month(data) = "'.$m.'" and year(data)= "'.$a.'" order by data';
 
      $risass=mysqli_query($con,inspref($queryass)) or die ("Errore nella query: ". mysqli_error($con));
      $risrit=mysqli_query($con,inspref($queryrit)) or die ("Errore nella query: ". mysqli_error($con));      
      $risusc=mysqli_query($con,inspref($queryusc)) or die ("Errore nella query: ". mysqli_error($con));   
      $dateass=array(); 
      $daterit=array();
      $dateusc=array(); 
      while ($ass=mysqli_fetch_array($risass))
      {
       
         $dateass[]=$ass['data'];
      }  
      while ($rit=mysqli_fetch_array($risrit))
      {
        
         $daterit[]=$rit['data'];
      }
      while ($usc=mysqli_fetch_array($risusc))
      {
        
         $dateusc[]=$usc['data'];
      }
      for ($gi=1;$gi<=31;$gi++)
      {
         
         $giornosettimana=giorno_settimana($a."-".$m."-".$gi);
         if (checkdate($m,$gi,$a) & !($giornosettimana=="Dom")) 
         {
            if ($gi<10)
               $gi="0".$gi; 
            print("<td><font size=1>"); 
      
            if (in_array($a."-".$m."-".$gi,$dateass))
               print("<center>A");
            else
               if (in_array($a."-".$m."-".$gi,$dateusc))
                  print("<center>U");
               else
                  if (in_array($a."-".$m."-".$gi,$daterit))
                     print("<center>R");
                  else
                     print("&nbsp;");
       
            
            
 
            print ("</td>");
         }
      }	 
      


         
      // Fine codice per ricerca tbl_assenze gi� inserite

           print"</tr>";
  
    }

    echo'</table>';
 
  
}
  
 // fine if
  
mysqli_close($con);
stampa_piede(""); 

