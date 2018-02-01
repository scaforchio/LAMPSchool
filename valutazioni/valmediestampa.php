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


//
//    Parte iniziale della pagina
//

$titolo="Stampa voti medi";
$script= "<script type='text/javascript'>
         <!--
            function printPage()
            {
               if (window.print)
                  window.print();
               else 
                  alert('Sorry! il tuo browser non supporta!');            }
         //-->
         </script>"; 



stampa_head($titolo,"",$script,"SDMAP");

print ("<body  onLoad='printPage()' >");


//
//    Fine parte iniziale della pagina
//




$idclasse = stringa_html('classe');   // 24/12/2008
$periodo = stringa_html('periodo');
$idmateria = stringa_html('materia');
$id_ut_doc = $_SESSION["idutente"];
$classe="";
$materia="";


$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
    
$query="select iddocente, cognome, nome from tbl_docenti where idutente=$id_ut_doc";
$ris=mysqli_query($con,inspref($query));
if($nom=mysqli_fetch_array($ris))
{
   $iddocente=$nom["iddocente"];
   $cognomedoc=$nom["cognome"];
   $nomedoc=$nom["nome"];
   $nominativo =$nomedoc." ".$cognomedoc;  
}
             
print("<font size=2><center><b>Docente:&nbsp;$nominativo&nbsp;&nbsp;Classe:&nbsp;"); 
      
$query="select anno,sezione,specializzazione from tbl_classi where tbl_classi.idclasse=$idclasse";
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{ 
  $classe=$nom["anno"]." ".$nom["sezione"]." ".$nom["specializzazione"];
}
        
print "$classe&nbsp;&nbsp;";










//
//    Leggo le tbl_materie e le visualizzo
//
    
print "Materia:&nbsp;";



$query="select * from tbl_materie where idmateria=$idmateria";
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
   $materia=$nom["denominazione"];
   print $materia."";
}


//
//  Ore lezione totali
//


    if ($periodo=="Primo")
        $querylez='select sum(numeroore) as orelez from tbl_lezioni where idmateria="'.$idmateria.'" and idclasse="'.$idclasse.'" and datalezione <= "'.$fineprimo.'"' ;
    if ($periodo=="Secondo" & $numeroperiodi==2)
        $querylez='select sum(numeroore) as orelez from tbl_lezioni where idmateria="'.$idmateria.'" and idclasse="'.$idclasse.'" and datalezione >  "'.$fineprimo.'"' ;
    if ($periodo=="Secondo" & $numeroperiodi==3)
        $querylez='select sum(numeroore) as orelez from tbl_lezioni where idmateria="'.$idmateria.'" and idclasse="'.$idclasse.'" and datalezione >  "'.$fineprimo.'" and datalezione <=  "'.$finesecondo.'"';
    if ($periodo=="Terzo")
        $querylez='select sum(numeroore) as orelez from tbl_lezioni where idmateria="'.$idmateria.'" and idclasse="'.$idclasse.'" and datalezione >  "'.$finesecondo.'"';
    if ($periodo=="Tutti")
        $querylez='select sum(numeroore) as orelez from tbl_lezioni where idmateria="'.$idmateria.'" and idclasse="'.$idclasse.'" ';
   
    $rislez=mysqli_query($con,inspref($querylez));
    $vallez=mysqli_fetch_array($rislez);
    print ('<br/>Ore totale lezione:'.$vallez['orelez'].'<br/><br/>'); 
      


//





echo "<table border=1 width=98%>";
  

 
       
 

$query='select * from tbl_alunni where idclasse="'.$idclasse.'" order by cognome,nome,datanascita';
$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
while($val=mysqli_fetch_array($ris))
{
    // $esiste_voto=false;
    echo "<tr>           
              <td colspan=3><small><small>";
          
    echo '<b><center>'.$val["cognome"].' '.
          $val["nome"].' '.data_italiana($val["datanascita"]).' </b></td> 
          </tr>';
    
    // Codice per ricerca voti e calcolo medie
    $numo=0;
    $valo=0;
    $nums=0;
    $vals=0;
    $nump=0;
    $valp=0; 
    $mediao=0;
    $medias=0;
    $mediap=0;
    $riempito=false; 
      
    if ($periodo=="Primo")
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and data <= "'.$fineprimo.'" and idmateria="'.$idmateria.'" and tipo="O" order by data';
    if ($periodo=="Secondo" & $numeroperiodi==2)
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and data >  "'.$fineprimo.'" and idmateria="'.$idmateria.'" and tipo="O" order by data';
    if ($periodo=="Secondo" & $numeroperiodi==3)
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and data >  "'.$fineprimo.'" and data <=  "'.$finesecondo.'"and idmateria="'.$idmateria.'" and tipo="O" order by data';
    if ($periodo=="Terzo")
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and data >  "'.$finesecondo.'" and idmateria="'.$idmateria.'" and tipo="O" order by data';
    if ($periodo=="Tutti")
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and idmateria="'.$idmateria.'" and tipo="O" order by data';
   
    if ($risval=mysqli_query($con,inspref($queryval)))
       
    {
      if (mysqli_num_rows($risval)>0)
       print "<tr><td colspan=3 align=center><small><small><i>Orale</i></td></tr>";
      while ($valval=mysqli_fetch_array($risval))
      {
         $riempito=true;
         print ('<tr><td width=20%><small><small>'.data_italiana($valval['data']).'</td><td width=10%><small><small>'.dec_to_mod($valval['voto']).'</td><td width=70%><small><small>'.$valval['giudizio'].'</td></tr>');  
         if ($valval['voto']!=99)
         { 
            $numo++;
            $valo=$valo+$valval["voto"];
         }
      }
    }
    
      if (!$riempito)
          print"";
      else
      { 
          print"<tr>";
          $riempito=false;
      }
           
     
      if ($numo!=0)
         { 
             print"<td><small><small><b>Media orale:</td><td><small><small><b>";
            $mediao=round($valo/$numo,2);
            print ($mediao);
             print "</b></td>";
             print "</tr>";
         }
     

    
 
    
    if ($periodo=="Primo")
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and data <= "'.$fineprimo.'" and idmateria="'.$idmateria.'" and tipo="S" order by data';
    if ($periodo=="Secondo" & $numeroperiodi==2)
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and data >  "'.$fineprimo.'" and idmateria="'.$idmateria.'" and tipo="S" order by data';
    if ($periodo=="Secondo" & $numeroperiodi==3)
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and data >  "'.$fineprimo.'" and data <=  "'.$finesecondo.'"and idmateria="'.$idmateria.'" and tipo="S" order by data';
    if ($periodo=="Terzo")
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and data >  "'.$finesecondo.'" and idmateria="'.$idmateria.'" and tipo="S" order by data';
    if ($periodo=="Tutti")
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and idmateria="'.$idmateria.'" and tipo="S" order by data';
    
    if ($risval=mysqli_query($con,inspref($queryval)))
       
    {
       if (mysqli_num_rows($risval)>0)
          print "<tr><td colspan=3 align=center><small><small><i>Scritto</i></td></tr>";
      while ($valval=mysqli_fetch_array($risval))
      {
         $riempito=true;
         print ('<tr><td><small><small>'.data_italiana($valval['data']).'</td><td><small><small>'.dec_to_mod($valval['voto']).'</td><td><small><small>'.$valval['giudizio'].'</td></tr>');  
         if ($valval['voto']!=99)
         { 
            $nums++;
            $vals=$vals+$valval["voto"];
         }
      }
    }
    
       if (!$riempito)
          print"";
      else
      { 
          print"<tr>";
          $riempito=false;
      }
           
    
      if ($nums!=0)
         { 
            print"<td><small><small><b>Media scritto:</td><td><small><small><b>";
          
            $medias=round($vals/$nums,2);
            print ($medias);
             print "</b></td>";
             print "</tr>";
         }
   
   
   
    
    if ($periodo=="Primo")
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and data <= "'.$fineprimo.'" and idmateria="'.$idmateria.'" and tipo="P" order by data';
    if ($periodo=="Secondo" & $numeroperiodi==2)
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and data >  "'.$fineprimo.'" and idmateria="'.$idmateria.'" and tipo="P" order by data';
    if ($periodo=="Secondo" & $numeroperiodi==3)
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and data >  "'.$fineprimo.'" and data <=  "'.$finesecondo.'"and idmateria="'.$idmateria.'" and tipo="P" order by data';
    if ($periodo=="Terzo")
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and data >  "'.$finesecondo.'" and idmateria="'.$idmateria.'" and tipo="P" order by data';
    if ($periodo=="Tutti")
        $queryval='select * from tbl_valutazioniintermedie where idalunno = '.$val["idalunno"].' and idmateria="'.$idmateria.'" and tipo="P" order by data';
    
    if ($risval=mysqli_query($con,inspref($queryval)))
       
    { 
    
      if (mysqli_num_rows($risval)>0)
         print "<tr><td colspan=3 align=center><small><small><i>Pratico</i></td></tr>"; 
      while ($valval=mysqli_fetch_array($risval))
      {
         $riempito=true;
         print ('<tr><td><small><small>'.data_italiana($valval['data']).'</td><td><small><small>'.dec_to_mod($valval['voto']).'</td><td><small><small>'.$valval['giudizio'].'</td></td>');  
         if ($valval['voto']!=99)
         { 
            $nump++;
            $valp=$valp+$valval["voto"];
         }
      }
    }
    
       if (!$riempito)
          print"";
      else
      { 
          print"<tr>";
          $riempito=false;
      }
           
 
      if ($nump!=0)
         { 
            print"<td><small><small><b>Media pratico:</td><td><b><small><small>";
           
            $mediap=round($valp/$nump,2);
            print ($mediap);
            print "</b></td>";
            print "</tr>";
         }
         
         
       
       
      
      $numvoti=0;
      if ($numo!=0)
         $numvoti++;   
      if ($nump!=0)
         $numvoti++;   
      if ($nums!=0)
         $numvoti++;   
         
         
      print "<tr><td align=center colspan=3><small><small><b><i>Unico: &nbsp;";
      if (($numvoti)!=0)
         print round((($mediap+$medias+$mediao)/$numvoti),2);
      else
         print "-";
     

//
//   Calcolo ore assenza dell'alunno
//

    if ($periodo=="Primo")
        $queryass='select sum(oreassenza) as oreass from tbl_asslezione where idalunno = '.$val["idalunno"].' and idmateria="'.$idmateria.'" and data <= "'.$fineprimo.'"' ;
    if ($periodo=="Secondo" & $numeroperiodi==2)
        $queryass='select sum(oreassenza) as oreass from tbl_asslezione where idalunno = '.$val["idalunno"].' and idmateria="'.$idmateria.'" and data >  "'.$fineprimo.'"' ;
    if ($periodo=="Secondo" & $numeroperiodi==3)
        $queryass='select sum(oreassenza) as oreass from tbl_asslezione where idalunno = '.$val["idalunno"].' and idmateria="'.$idmateria.'" and data >  "'.$fineprimo.'" and data <=  "'.$finesecondo.'"';
    if ($periodo=="Terzo")
        $queryass='select sum(oreassenza) as oreass from tbl_asslezione where idalunno = '.$val["idalunno"].' and idmateria="'.$idmateria.'" and data >  "'.$finesecondo.'"';
    if ($periodo=="Tutti")
        $queryass='select sum(oreassenza) as oreass from tbl_asslezione where idalunno = '.$val["idalunno"].' and idmateria="'.$idmateria.'"';

   
    $risval=mysqli_query($con,inspref($queryass));
    
    $valass=mysqli_fetch_array($risval);
    print ('<br/>Ore assenza: '.$valass['oreass']); 
      
    



     print "</b></td></tr><tr><td colspan=3><small><small>&nbsp;</td></tr>";


    
      
    
  
    // Fine codice per ricerca voti gi� inseriti


   // echo '</td></tr>';
  }



  echo'</table>';
 
stampa_piede(""); 

mysqli_close($con);


