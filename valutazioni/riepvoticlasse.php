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
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
    
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

$titolo="Tabellone voti medi";
$script="<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>"; 

stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 
//
//    Fine parte iniziale della pagina
//

$nome = stringa_html('cl');   // 24/12/2008

$periodo = stringa_html('periodo');
//$anno = stringa_html('anno');


$idclasse = stringa_html('cl');
$id_ut_doc = $_SESSION["idutente"];

//if ($giorno=='')
//   $giorno=date('d');
//if ($mese=='')
//   $mese=date('m');
//if ($anno=='')
//   $anno=date('Y');



print ('
         <form method="post" action="riepvoticlasse.php" name="voti">
   
         <p align="center">
         <table align="center">');

         

//
//   Inizio visualizzazione del combo box del periodo
//
if ($numeroperiodi==2)
   print('<tr><td width="50%"><b>Quadrimestre</b></td>');
else
   print('<tr><td width="50%"><b>Trimestre</b></td>');

echo('   <td width="50%">');
echo('   <select name="periodo" ONCHANGE="voti.submit()">');

if ($periodo=='Primo')
  echo("<option selected>Primo</option>");
else
  echo("<option>Primo</option>");
if ($periodo=='Secondo')
  echo("<option selected>Secondo</option>");
else
  echo("<option>Secondo</option>");

if ($numeroperiodi==3)
   if ($periodo=='Terzo')
     echo("<option selected>Terzo</option>");
   else
     echo("<option>Terzo</option>");

if ($periodo=='Tutti')
  echo("<option selected>Tutti</option>");
else
  echo("<option>Tutti</option>");

  
echo("</select>");
echo("</td></tr>");


//
//  Fine visualizzazione del quadrimestre
//




//
//   Classi
//

print('
        <tr>
        <td width="50%"><b>Classe</b></p></td>
        <td width="50%">
        <SELECT ID="cl" NAME="cl" ONCHANGE="voti.submit()"><option value=""></option>  '); 
      
//
//  Riempimento combobox delle tbl_classi
//
if ($tipoutente=="S" | $tipoutente=="P")
   $query="select distinct tbl_classi.idclasse,anno,sezione,specializzazione from tbl_classi order by anno,sezione,specializzazione";
else if($id_ut_doc==1000000030)  // MODIFICA PER PROF. FINI DA ELIMINARE
   $query="select distinct tbl_classi.idclasse,anno,sezione,specializzazione from tbl_classi 
           where anno='5' and specializzazione='Informatica' order by anno,sezione,specializzazione";    
else
   $query="select distinct tbl_classi.idclasse,anno,sezione,specializzazione from tbl_classi 
           where idcoordinatore=".$_SESSION['idutente']. " order by anno,sezione,specializzazione";
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
  
print ("</select></td></tr></table></form>");      

if ($nome!="") {

print ("<table align='center' border='1'><tr align='center'><td>Alunno</td>");
$query="SELECT distinct tbl_materie.idmateria,sigla FROM tbl_cattnosupp,tbl_materie 
WHERE tbl_cattnosupp.idmateria=tbl_materie.idmateria
and tbl_cattnosupp.idclasse=$idclasse
and tbl_cattnosupp.iddocente <> 1000000000
order by tbl_materie.sigla";
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
      print ("<td>"); 
      print ($nom["sigla"]);
      $codmat[]=$nom["idmateria"];
      $sigmat[]=$nom["sigla"];
      print ("</td>");
}
print("<td><b>MEDIA</b></td></tr>");





if ($periodo=="Primo")
   $per ='data <= "'.$fineprimo.'"' ;
if ($periodo=="Secondo" & $numeroperiodi==2)
   $per ='data >  "'.$fineprimo.'"' ;
if ($periodo=="Secondo" & $numeroperiodi==3)
   $per ='data >  "'.$fineprimo.'" and data <=  "'.$finesecondo.'"';
if ($periodo=="Terzo")
   $per ='data >  "'.$finesecondo.'"';
if ($periodo=="Tutti")
   $per=' true';
   
$numeroalunno=0;

$query='select * from tbl_alunni where idclasse="'.$idclasse.'" order by cognome,nome,datanascita';
$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
while($val=mysqli_fetch_array($ris))
{
    // $esiste_voto=false;
    $idalunno=$val["idalunno"]; 
    $numeroalunno++;
    if ($numeroalunno%2==0)
     $colore='#FFFFFF';
    else
     $colore='#C0C0C0'; 
    echo "<tr bgcolor=$colore>";
     if (!alunno_certificato($val['idalunno'],$con))
         $cert="";
      else
         $cert="<img src='../immagini/apply_small.png'>";         
    echo '      <td><b>'.$val["cognome"].' '.$val["nome"].' '.data_italiana($val["datanascita"]).' '.$cert.' </b></td>';
    
    $contavoti=0;
    $sommavoti=0;
    foreach($codmat as $cm)
    {
       $query="SELECT avg(voto) as votomedio FROM tbl_valutazioniintermedie
               WHERE idalunno=$idalunno
               and idmateria=$cm 
               and voto<>99 
               and $per";
       $rismedia=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
       if($valmedia=mysqli_fetch_array($rismedia))
          {
             $outvoto=number_format ( $valmedia["votomedio"],2);
             if ($outvoto<6) $col='red'; else $col='green';
             if ($outvoto!="0.00")
                {
                   print "<td align='center'><font color='$col'>$outvoto</font></td>";
                   $contavoti++;
                   $sommavoti=$sommavoti+$valmedia["votomedio"];
                }   
             else
                print "<td align='center'> -- </td> ";
          }
       else     
          print"ERRORE! Contattare sistemista."; 
    }
    if ($contavoti!=0)
        $outmedia=number_format ( $sommavoti/$contavoti,2);
    else
        $outmedia="--";    
    if ($outmedia<6) $col='red'; else $col='green';    
    print "<td><b><center><font color='$col'>$outmedia</font></center></b></td></tr>";
}
print "</table>";

}

mysqli_close($con);

stampa_piede(""); 

