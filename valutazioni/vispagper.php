<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
GNU Affero General Public License come pubblicata 
dalla Free Software Foundation; sia la versione 3, 
sia (a vostra scelta) ogni versione successiva.

Questo programma è distribuito nella speranza che sia utile 
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di 
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE. 
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/
  
//
//    VISUALIZZAZIONE DELLE VALUTAZIONI 
//    PER I GENITORI 
//




 @require_once("../php-ini".$_SESSION['suffisso'].".php");
 @require_once("../lib/funzioni.php");
	
//  istruzioni per tornare alla pagina di login se non c'è una sessione valida
 ////session_start();
 $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
    if ($tipoutente=="")
       {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
       } 

$periodo=stringa_html('periodo','get');
if ($periodo=='Primo')
   $per=1;
else
   $per=2;   

$titolo="Visualizzazione pagella periodica";
$script=""; 
stampa_head($titolo,"",$script,"SDMAPT");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

$codalunno=$_SESSION['idstudente'];


$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 


  // prelevamento dati alunno

  $query="select * from tbl_alunni where idalunno=$codalunno"; 
  $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
  
  // echo '<table border=1 align="center" width="800"  >';
  
  if($val=mysqli_fetch_array($ris))
  {
   echo '<center><b>Pagella dell\'Alunno: '.$val["cognome"].' '.$val["nome"].'</b></center><br/>';
  }
  
  
  if (!scrutinio_aperto($val['idclasse'],$per,$con))
  { 
  
  // prelevamento voti
  $query="SELECT * from tbl_valutazionifinali,tbl_materie 
          where tbl_valutazionifinali.idmateria=tbl_materie.idmateria
          and idalunno=$codalunno and periodo = $per order by tbl_materie.progrpag";
  
  $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
  // print $query;
  if (mysqli_num_rows($ris)>0)
  {
     print ("<table border=2 align=center><tr class='prima'><td>Materia</td><td align=center>Scritto</td><td align=center>Orale</td><td>Pratico</td><td align=center>Unico</td><td align=center>Annotazioni</td><td align=center>Assenze</td></tr>");
     $mat="";
     while($val=mysqli_fetch_array($ris))
     {
       $query="select * from tbl_materie where idmateria = ".$val['idmateria'];
       $rismat=mysqli_query($con,inspref($query));
       $recmat=mysqli_fetch_array($rismat);
       $materia=$recmat['denominazione'];
       
       print "<tr>";
       print "<td>";
       print $materia;
       print "</td>";
       print "<td align=center>&nbsp;";
       if ($val['votoscritto']<6 | ($val['votoscritto'] >10 & $val['votoscritto'] <16)) // is_numeric($val['votoscritto']))
          print "<font color=red><b>";
       else
          print "<font color=green><b>";  
       print dec_to_vot($val['votoscritto']);
       print "</td>";
       print "<td align=center>&nbsp;";
       if ($val['votoorale']<6 | ($val['votoorale'] >10 & $val['votoorale'] <16)) // is_numeric($val['votoscritto']))
          print "<font color=red><b>";
       else
          print "<font color=green><b>";  
       print dec_to_vot($val['votoorale']);
       print "</td>";
       print "<td align=center>&nbsp;";
       if ($val['votopratico']<6 | ($val['votopratico'] >10 & $val['votopratico'] <16)) // is_numeric($val['votoscritto']))
          print "<font color=red><b>";
       else
          print "<font color=green><b>";  
       print dec_to_vot($val['votopratico']);
       print "</td>";
       print "<td align=center>&nbsp;";
       if ($val['votounico']<6 | ($val['votounico'] >10 & $val['votounico'] <16)) // is_numeric($val['votoscritto']))
          print "<font color=red><b>";
       else
          print "<font color=green><b>";   
       print dec_to_vot($val['votounico']);
       print "</td>";
       print "<td align=center>&nbsp;";
       print $val['note'];
       print "</td>";
       print "<td align=center>&nbsp;";
       print $val['assenze'];
       print "</td>";
       print "</tr>";
    }    
    
    // Cerco il giudizio
    
    $query="select * from tbl_giudizi where idalunno = $codalunno and periodo = '$per'";
    $risgiu=mysqli_query($con,inspref($query));
    if ($recgiu=mysqli_fetch_array($risgiu))
    {   
		 print "<tr class='prima'><td colspan=7 align=center><b>Giudizio complessivo</b></td></tr>";
		 print "<tr><td colspan=7 align=center>".$recgiu['giudizio']."</b></td></tr>";
    }
    print ("</table><br/>");
    print "<br><center><a href='../scrutini/stampaschedealu.php?idalunno=$codalunno&periodo=$per' target='_blank'><img src='../immagini/stampa.png'></a></center>";
  }
  else
  {
     print("<br/><big><big><center>Non ci sono voti registrati!</center><small><small><br/>");
  }
}
else
{
	print("<br/><big><big><center>Scrutinio non ancora chiuso!</center><small><small><br/>");
}
  
mysqli_close($con);
stampa_piede(""); 




