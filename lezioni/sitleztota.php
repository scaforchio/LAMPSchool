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
 $iddocente=$_SESSION["idutente"];
    if ($tipoutente=="")
       {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
       } 


$titolo="Situazione lezioni";
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
 
$cla='';
$dat='';
$mat='';


$idgruppo='';

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 



// CODICE PER GESTIONE RICHIAMO DA RIEPILOGO

$codlez = stringa_html('idlezione');

//$cla = stringa_html('classe');
//$mat = stringa_html('materia');

$per = stringa_html('periodo');
$catt = stringa_html('cl');
$nome="";

if ($codlez!="")
{
    $query="select * from tbl_lezioni where idlezione=$codlez";
    
    $ris=mysqli_query($con,inspref($query));
    $lez=mysqli_fetch_array($ris);
    $idmateria=$lez['idmateria'];
    $idclasse=$lez['idclasse'];
    $datalezione=$lez['datalezione'];
  // $anno=substr(,0,4);
  // $mese=substr($lez['datalezione'],5,2);
   if ($numeroperiodi==2)
   {
      if ($datalezione<=$fineprimo)
         $per="Primo";
      else
         $per="Secondo";
   }
   if ($numeroperiodi==3)
   {
      if ($datalezione<=$fineprimo)
         $per="Primo";
      else
      {
             if ($datalezione<=$finesecondo)
                 $per="Secondo";
             else
                 $per="Terzo";
	 }
   }

//
//   Recupero cattedra da idlezione
//
      $query="select idcattedra from tbl_cattnosupp where idclasse=$idclasse and idmateria=$idmateria and iddocente=$iddocente"; 
      $ris=mysqli_query($con,inspref($query));
      if($nom=mysqli_fetch_array($ris))
      {
          $catt=$nom['idcattedra'];
      }

//
//   Fine recupero cattedra da idlezione
//



   $id_ut_doc = $_SESSION["idutente"];
       
}

// FINE CODICE PER GESTIONE DA RIEPILOGO
else
{
   if ($per=="")
   {	
      $dataoggi=date('Y')."-".date('m')."-".date('d');
    
      if ($dataoggi>$fineprimo)	
         $per="Secondo";
      if ($numeroperiodi>2 & $dataoggi>$finesecondo)	
         $per="Terzo";
   }   
   
   
   
   if ($cla!="" & $mat!="" & $dat!="")
   {
       $idclasse=$cla;   
       $idmateria=$mat;
       $id_ut_doc = $_SESSION["idutente"]; 
       $giorno=substr($dat,8,2);
   
   }

   else
   {
     
       $catt = stringa_html('cl');
      
       if ($catt<>"")
       {
           $query="select idclasse, idmateria from tbl_cattnosupp where idcattedra=$catt"; 
           $ris=mysqli_query($con,inspref($query));
           if($nom=mysqli_fetch_array($ris))
           {
              $mat=$nom['idmateria'];
              $cla=$nom['idclasse'];
           }
       }



       $idclasse=$cla;
       $idmateria=$mat;
       $id_ut_doc = $_SESSION["idutente"];

   } 


}

print ('
   <form method="post" action="sitleztota.php" name="tbl_lezioni">
   
   <p align="center">
   <table align="center">');
   //
   //   Leggo il nominativo del docente e lo visualizzo
   //
$query="select iddocente, cognome, nome from tbl_docenti where idutente=$id_ut_doc";

$ris=mysqli_query($con,inspref($query));
if($nom=mysqli_fetch_array($ris))
{
   $iddocente=$nom["iddocente"];
   $cognomedoc=$nom["cognome"];
   $nomedoc=$nom["nome"];
   $nominativo =$nomedoc." ".$cognomedoc;  
}
             
print("    
             <tr>
              <td><b>Docente</b></td>

          <td>
          <INPUT TYPE='text' VALUE='$nominativo' disabled>
          <input type='hidden' value='$iddocente' name='iddocente'>
          </td></tr>");

//
//   Classi
//

print("
        <tr>
        <td width='50%'><b>Cattedra</b></p></td>
        <td width='50%'>
        <SELECT ID='cl' NAME='cl' ONCHANGE='tbl_lezioni.submit()'> <option value=''>&nbsp; "); 
      
//
//  Riempimento combobox delle tbl_cattnosupp
//
$query="select idcattedra,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione 
        from tbl_cattnosupp, tbl_classi, tbl_materie 
        where iddocente=$iddocente 
        and tbl_cattnosupp.idclasse=tbl_classi.idclasse 
        and tbl_cattnosupp.idmateria = tbl_materie.idmateria 
        order by anno, sezione, specializzazione, denominazione";
$strvisold="";  // Per evitare duplicati nel caso di cattedre di sostegno 
                // su stessa classe e stessa materia


$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{ 
   $strvis=$nom['anno']."&nbsp;".$nom['sezione']."&nbsp;".$nom['specializzazione']."&nbsp;-&nbsp;".$nom['denominazione'];	
   if ($strvis!=$strvisold)
   {
	  print "<option value='";
	  print ($nom["idcattedra"]);
	  print "'";
	  if ($catt==$nom["idcattedra"])
		  print " selected";
	  print ">$strvis";
	 // print ($nom["anno"]);
	 // print "&nbsp;"; 
	 // print($nom["sezione"]); 
	 // print "&nbsp;";
	 // print($nom["specializzazione"]);
	 // print "&nbsp;-&nbsp;";
	 // print($nom["denominazione"]);
	  $strvisold=$strvis;
  }
}


print("</select></td></tr>");


//echo('
//
//      <tr>
//     <td width="50%"><b>Periodo</b></p></td>');

//
//   Inizio visualizzazione del combo box del periodo
//
if ($numeroperiodi==2)
   print('<tr><td width="50%"><b>Quadrimestre</b></td>');
else
   print('<tr><td width="50%"><b>Trimestre</b></td>');

echo('   <td width="50%">');
echo('   <select id="periodo" name="periodo" ONCHANGE="tbl_lezioni.submit()">');

if ($per=='Primo')
  echo("<option selected>Primo</option>");
else
  echo("<option>Primo</option>");
if ($per=='Secondo')
  echo("<option selected>Secondo</option>");
else
  echo("<option>Secondo</option>");

if ($numeroperiodi==3)
   if ($per=='Terzo')
     echo("<option selected>Terzo</option>");
   else
     echo("<option>Terzo</option>");



  
echo("</select>");
echo("</td></tr>");


print('</table></form>');
$query='select * from tbl_classi where idclasse="'.$idclasse.'" ';
$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
if($val=mysqli_fetch_array($ris))
    $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];

$classe="";
    
if ($per != "" & $catt!= "")
{
   
    // VERIFICO SE SI TRATTA DI UNA CATTEDRA LEGATA A UN GRUPPO
   $idcl=estrai_id_classe($catt,$con);
   $idmat=estrai_id_materia($catt,$con);
   
   $query="select distinct tbl_gruppi.idgruppo from tbl_gruppialunni,tbl_alunni,tbl_gruppi 
           where tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
             and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
             and tbl_alunni.idclasse=$idcl
             and tbl_gruppi.idmateria=$idmat
             and tbl_gruppi.iddocente=$iddocente";
   $ris=mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query));
   if ($rec=mysqli_fetch_array($ris))
      $idgruppo=$rec['idgruppo'];
   //
   
   
   
    $query='select * from tbl_classi where idclasse="'.$idclasse.'" ';
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    if($val=mysqli_fetch_array($ris))
       $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];

   
    echo '<p align="center">
          <font size=4 color="black">Lezioni della classe '.$classe.'</font>
          
          ';
            
// CALCOLO E VISUALIZZO IL NUMERO TOTALE DELLE ORE DI LEZIONE

 $perioquery="and true";
    if ($per=="Primo")
	{
        $perioquery=" and datalezione <= '".$fineprimo."'" ;
    } 
	if ($per=="Secondo" & $numeroperiodi==2)
    {     
        $perioquery=" and datalezione > '".$fineprimo."'" ; 
    }
	if ($per=="Secondo" & $numeroperiodi==3)
    {     
        $perioquery=" and datalezione > '".$fineprimo."' and datalezione <=  '".$finesecondo."'";
    }
	if ($per=="Terzo")
    {    
       $perioquery=" and datalezione > '".$finesecondo."'" ;
    }

    $query="select sum(numeroore) as numtotore from tbl_lezioni where idclasse='".$idclasse."' and idmateria='".$idmateria."' ".$perioquery;
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    if($val=mysqli_fetch_array($ris))
       $oretotalilezione=$val["numtotore"];

    print (" - Totale ore di lezione: $oretotalilezione<br/>");

	




//
//   ESTRAZIONE DATI DELLE LEZIONI
//

    
    if ($idclasse!="")
    echo'
          <table border=2 align="center">
          <tr class="prima">
          <td rowspan=2><b> N.</b> </td>
          <td rowspan=2><font size=1><b> Alunno </b></td>
          
          <td rowspan=2><font size=1><b> Data di nascita </b></td>
          <td colspan=5><font size=1><b>Proposte di voto</b></td>
          ';
    

    $giornilezione=array();
  /*  $perioquery="and true";
    if ($per=="Primo")
	{
        $perioquery=" and datalezione <= '".$fineprimo."'" ;
    } 
	if ($per=="Secondo" & $numeroperiodi==2)
    {     
        $perioquery=" and datalezione > '".$fineprimo."'" ; 
    }
	if ($per=="Secondo" & $numeroperiodi==3)
    {     
        $perioquery=" and datalezione > '".$fineprimo."' and datalezione <=  '".$finesecondo."'";
    }
	if ($per=="Terzo")
    {    
       $perioquery=" and datalezione > '".$finesecondo."'" ;
    }
  */  
    
    
	// SELEZIONO LE DATE DELLE LEZIONI INSERITE
	
	
    $query="select idlezione,datalezione, numeroore, orainizio from tbl_lezioni where idclasse='".$idclasse."' and idmateria='".$idmateria."' ".$perioquery." order by datalezione,orainizio";
    
    $rislez=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    
    while ($reclez=mysqli_fetch_array($rislez))
  
    {
       $gio=substr($reclez['datalezione'],8,2);
       $mes=substr($reclez['datalezione'],5,2);
       $ann=substr($reclez['datalezione'],0,4);
       
       if (strlen($reclez['orainizio'])>1)
           $orainizio=$reclez['orainizio'];
       else
           $orainizio="0".$reclez['orainizio'];    
    //   $giorno=$ann.$mes.$gio.$reclez['orainizio'].$reclez['datalezione'].$reclez['numeroore'].$reclez['idlezione'] ;
       $giorno=$ann.$mes.$gio.$orainizio.$reclez['datalezione'].$reclez['numeroore'].$reclez['idlezione'] ;
    //  print "tttt:".$giorno."<br>";
       
       $giornilezione[]=$giorno;
      
       
    } 
    
	$perioquery="and true";
    if ($per=="Primo")
	{
        $perioquery=" and data <= '".$fineprimo."'" ;
    } 
	if ($per=="Secondo" & $numeroperiodi==2)
    {     
        $perioquery=" and data > '".$fineprimo."'" ; 
    }
	if ($per=="Secondo" & $numeroperiodi==3)
    {     
        $perioquery=" and data > '".$fineprimo."' and data <=  '".$finesecondo."'";
    }
	if ($per=="Terzo")
    {    
       $perioquery=" and data > '".$finesecondo."'" ;
    }
	
	
	
    // ORDINO I GIORNI DI LEZIONE

    sort($giornilezione);
    
    
    foreach($giornilezione as $gg)
    {
	    $strore=(substr($gg,8,2)/1).">".((substr($gg,20,1)+substr($gg,8,2)-1));
       print "<td rowspan=2><font size=1><center><a href='lez.php?idlezione=".substr($gg,21,11)."&provenienza=tabe'>".giorno_settimana(substr($gg,10,10))."<br/>".substr($gg,6,2)."<br/>".substr($gg,4,2)."<br/>$strore</a></td>";
     
    }

    print "<td rowspan=2 align='center' valign='middle'><font size=1><b>Ass.<br/>tot.</b></font></td></tr>"; 

   
    print "<tr class='Prima'>
           <td><font size=1><b>Sc</b></td>
          <td><font size=1><b>Or</b></td>
          <td><font size=1><b>Pr</b></td>
          <td><font size=1><b>Un</b></td>
          <td><font size=1><b>Co</b></td></tr> "; 

    $sost=0;
   // if ($_SESSION['sostegno'])
    if (cattedra_sostegno($catt,$con))
    {
	    $sost=1;
    }

    if (!$sost)
       if ($idgruppo=='')
          $query="select * from tbl_alunni where idclasse='$idclasse' order by cognome,nome,datanascita";
       else
          $query="select tbl_alunni.idalunno,cognome,nome,datanascita 
                  from tbl_gruppi,tbl_gruppialunni,tbl_alunni
                  where
                       tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
                       and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
                       and tbl_alunni.idclasse=$idclasse
                       and tbl_gruppi.idgruppo in (select idgruppo from tbl_gruppi where idmateria=$idmateria and iddocente=$iddocente)
                       order by cognome,nome,datanascita";
                       //=$idgruppo";   
    else
       $query="select * from tbl_alunni 
               where idclasse='$idclasse' 
                     and idalunno in (select idalunno from tbl_cattnosupp where iddocente='$id_ut_doc' and idmateria='$idmateria' and idclasse='$idclasse') order by cognome, nome, datanascita";
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    $numeroalunno=0;
    while($val=mysqli_fetch_array($ris))
    {
      if ($numeroalunno%2==1)
         print(" <tr bgcolor='f0f0f0'>");
                
      else
          print(" <tr bgcolor='ffffff'>");
      
      if (!alunno_certificato($val['idalunno'],$con))
         $cert="";
      else
         $cert="<img src='../immagini/apply_small.png'>";
        $progressivo=$numeroalunno+1;
      print ("<td>$progressivo</td><td><font size=1><a href=javascript:Popup('sitlezalu.php?alunno=".$val['idalunno']."&materia=$idmateria&periodo=$per&classe=$idclasse')><b>".$val['cognome']." ".$val['nome']."</b></a>$cert</td>
              <td align='center'><font size=1><b> ".data_italiana($val['datanascita'])." </b></td>");
      
       
      if ($per=="Primo") $perio='1';
      if ($per=="Secondo") $perio='2';
      if ($per=="Terzo") $perio='3';
      
      
      $queryprop='select * from tbl_proposte where idalunno='.$val['idalunno'].' and idmateria ='.$idmateria.' and periodo="'.$perio.'"';
      
      $risprop=mysqli_query($con,inspref($queryprop)) or die ("Errore nella query: ". mysqli_error($con));
      if ($valprop=mysqli_fetch_array($risprop))
      { 
          
          print "<td><font size=1><center><b>".dec_to_vot($valprop['scritto'])."</b></td>";
          print "<td><font size=1><center><b>".dec_to_vot($valprop['orale'])."</b></td>";
          print "<td><font size=1><center><b>".dec_to_vot($valprop['pratico'])."</b></td>";
          print "<td><font size=1><center><b>".dec_to_vot($valprop['unico'])."</b></td>";
          print "<td><font size=1><center><b>".dec_to_vot($valprop['condotta'])."</b></td>";
      }
      else
      { 
          print "<td><b>&nbsp;</b></td>";
          print "<td><b>&nbsp;</b></td>";
          print "<td><b>&nbsp;</b></td>";
          print "<td><b>&nbsp;</b></td>";
          print "<td><b>&nbsp;</b></td>";
      }
      
      
      $numeroalunno++; 
      $totoreass=0;
      // Codice per stampa dati sugli alunni
      $query="select tbl_asslezione.idlezione as idlezione,oreassenza from tbl_asslezione,tbl_lezioni 
              where tbl_asslezione.idlezione=tbl_lezioni.idlezione
              and tbl_lezioni.idmateria=$idmateria and idclasse=$idclasse and idalunno=".$val['idalunno'].$perioquery." order by idlezione";
      //print inspref($query);    
      $risass=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
      $ass = array();
      $leza = array();
      while ($recass=mysqli_fetch_array($risass))
      {
         $ass[] = $recass['oreassenza'];
         $leza[] = $recass['idlezione'];
      }
     
      $query="select idlezione,voto, giudizio, tipo,iddocente,pei from tbl_valutazioniintermedie 
              where idclasse=$idclasse and idalunno=".$val['idalunno']."
              and idmateria=$idmateria ".$perioquery." order by idlezione";
           
     // print inspref($query);
      $risvot=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
      $vot = array();   // Voti da visualizzare
      $lezv = array();  // Idlezioni
      
      while ($recvot=mysqli_fetch_array($risvot))
      {
			$visvoto="";
			//print "tttt $iddocente ttt".$recvot['iddocente'];
			if ($recvot['iddocente']!=$iddocente)
			    $visvoto.="&nbsp;<u>";
			else
			    $visvoto.="&nbsp;";
         if ($recvot['voto']>=6)
             $visvoto.= "<font color='green' size='1'>".dec_to_mod($recvot['voto'])."<sub>".$recvot['tipo']."</sub></font>";
         else
             $visvoto.= "<font color='red' size='1'>".dec_to_mod($recvot['voto'])."<sub>".$recvot['tipo']."</sub></font>"; 
         if ($recvot['iddocente']!=$iddocente)
			    $visvoto.="</u>";
         
         $vot[]=$visvoto;
         $lezv[] = $recvot['idlezione'];
      }        
      
      
      
      foreach($giornilezione as $gg)
      {
          
          print "<td align='center'>";
          
           

          // RICERCA ORE DI ASSENZA
         $assenze=ricerca_assenza($ass,$leza,substr($gg,21,11));
         if ($assenze != 0)
         {
             print "<font size='1'>A<sub>$assenze</sub>";
             $totoreass=$totoreass+$assenze;
         }
         
         // RICERCA VALUTAZIONI
         $voti=ricerca_voti($vot,$lezv,substr($gg,21,11));
         if ($voti != "")
         {
             print "$voti";
             
         }

         
          
          
    
          print "&nbsp;</td>";
      } 

      print("<td align='center'><font size=1>$totoreass</font></td>");
         
      // Fine codice per ricerca tbl_assenze già inserite

      print"</tr>";
  
    }

    echo'</table>';
 
  
       
  if (!$sost) print"<br/><center><a href=javascript:Popup('stampasitleztota.php?periodo=$per&cattedra=$catt')><img src='../immagini/stampa.png'></a><br/><br/>";
  
}   
  
 // fine if
  
function ricerca_assenza($ass,$lez,$idlez)
{
	$trovato=0;
	for ($i=0;$i<count($lez);$i++)
	   if ($lez[$i]==$idlez)
	      return $ass[$i];
	return $trovato;
}

function ricerca_voti($vot,$lez,$idlez)
{
	$trovato="";
	for ($i=0;$i<count($lez);$i++)
	   if ($lez[$i]==$idlez)
	   {
			$trovato.=$vot[$i];
		}
	     
	
	return $trovato;
}



mysqli_close($con);
stampa_piede(""); 

