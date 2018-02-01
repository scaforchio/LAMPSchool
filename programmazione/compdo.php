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


$titolo="Gestione competenze del programma";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

$maxcomp=20;

$cattedra = stringa_html('cattedra');
$idmateria="" ;
$idclasse="" ; 

print ("
   <form method='post' action='compdo.php' name='comp'>
   
   <p align='center'>
   <table align='center'>
   <tr>
      <td width='50%'><p align='center'><b>Cattedra</b></p></td>
      <td width='50%'>
      <SELECT ID='cattedra' NAME='cattedra' ONCHANGE='comp.submit()'> <option value=''>&nbsp "); 
	  
  
          $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
           
         $query="select idcattedra,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione,tbl_materie.idmateria from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria and idalunno=0 order by anno, sezione, specializzazione, denominazione";
          
          $ris=mysqli_query($con,inspref($query));
          while($nom=mysqli_fetch_array($ris))
	      {
            print "<option value='";
            print ($nom["idcattedra"]);
            print "'";
            if ($cattedra==$nom["idcattedra"])
		    {   
               print " selected";
               $idmateria=$nom["idmateria"];
               $idclasse=$nom["idclasse"];
            }   
            print ">";
            
            print ($nom["anno"]);
            
            print "&nbsp;"; 
            print($nom["sezione"]); 
            print "&nbsp;";
            print($nom["specializzazione"]);
            print "&nbsp;-&nbsp;";
            print($nom["denominazione"]);
            
          }
        
   print("
      </SELECT>
      </td></tr></table></form>");
   
 
 if (($cattedra!=""))
  {
	  
	// Controllo presenza di voti per la programmazione della classe
    $query="select count(*) as numerovoti from tbl_valutazioniabilcono, tbl_valutazioniintermedie, tbl_alunni
         where tbl_valutazioniabilcono.idvalint = tbl_valutazioniintermedie.idvalint
         and tbl_valutazioniintermedie.idalunno = tbl_alunni.idalunno
	     and tbl_valutazioniintermedie.idmateria=$idmateria 
	     and tbl_alunni.idclasse=$idclasse
	     and tbl_valutazioniintermedie.iddocente=$iddocente
	     " ;
 
    
	              
     $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
          
     $nom=mysqli_fetch_array($ris);
  $votipresenti=false;
  if ($nom['numerovoti']>0)
  {
     print ("<center><b><font color=red>Attenzione! Ci sono voti collegati a questa programmazione.<br/> 
	            La modifica di alcune voci è quindi inibita!<br/>
	            Utilizzare la voce \"CORREGGI COMPETENZA\" per correzioni!</font></b></center>");
	  $votipresenti=true;          
  }
  //else
 // {
    
   
    $query="select * from tbl_competdoc where idmateria=$idmateria and idclasse=$idclasse order by numeroordine";
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    print "<p align='center'>
          <font size=4 color='black'>Competenze </font>
          <form method='post' action='inscompdo.php'>
          <table border=1 align='center'>";
    $numord=0;
    
    while($val=mysqli_fetch_array($ris))
    {
        $numord++;
        $sintcomp=$val["sintcomp"];
        $competenza=$val["competenza"];
        $idcompetenza=$val["idcompetenza"];
        print "<tr><td>$numord</td>
               <td>";
        $votipresenti=false;
        $query="select * from tbl_valutazioniabilcono, tbl_abildoc,tbl_competdoc
              where 
                 tbl_valutazioniabilcono.pei = 0
                 and tbl_valutazioniabilcono.idabilita = tbl_abildoc.idabilita
                 and tbl_abildoc.idcompetenza=tbl_competdoc.idcompetenza
                 and tbl_competdoc.idcompetenza=$idcompetenza";
        $ris2=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". inspref($query));
        if (mysqli_num_rows($ris2)>0)
           $votipresenti=true;
               
        if (!$votipresenti)
        print "    SINTESI: <input type=text name=sint$numord value='$sintcomp' maxlength=80 size=80><br/>
                   <input type=hidden name=idcomp$numord value='$idcompetenza'>
                   <textarea cols=80 rows=3 name=est$numord>".$val['competenza']."</textarea></td>";
        else
            print "    SINTESI: <input type=text name=sintesi$numord value='$sintcomp' maxlength=80 size=80 disabled><br/>
                   <input type=hidden name=idcomp$numord value='$idcompetenza'>
                   <textarea cols=80 rows=3 name=estesa$numord disabled>".$val['competenza']."</textarea>
                   <input type=hidden name=sint$numord value='$sintcomp'>
                   <input type=hidden name=est$numord value='".$val['competenza']."'></td>";
              
        print "</tr>";           
    }   
    
    for($no=$numord+1;$no<=$maxcomp;$no++)
    {
        print "<tr><td>$no</td><td>SINTESI: <input type=text name=sint$no value='' maxlength=80 size=80><br/><textarea cols=80 rows=3 name=est$no></textarea></td><td>";
        print "Pos. ins. <select name='pos$no'>";
        print "<option value='0'>&nbsp;</option>";
        for ($i=1;$i<=$numord;$i++)
            print "<option value='$i'>$i</option>";
        print "</select>";
        print "</td></tr>";
    }
    print "<tr><td colspan=3 align=center><input type='submit' value='Registra competenze'></tr></table>";
    print "<input type='hidden' name='cattedra' value='$cattedra'>";
   
    print "</form>";
  }
 // }
         
mysqli_close($con);
stampa_piede(""); 

