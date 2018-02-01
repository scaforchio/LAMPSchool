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
 $sostegno=$_SESSION["sostegno"];
 if ($tipoutente=="")
 {
    header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
    die;
 } 


$titolo="Gestione competenze del programma individualizzato";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

$maxcomp=20;

//$idalunno = stringa_html('idalunno');
//$idmateria= stringa_html('idmateria');
$idcattedra=stringa_html('idcattedra'); 

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));



$query="select idcattedra, tbl_cattnosupp.idmateria, tbl_cattnosupp.idclasse, tbl_cattnosupp.idalunno, cognome, nome, datanascita 
                 from tbl_cattnosupp, tbl_alunni, tbl_tipoprog
                 where iddocente=$iddocente 
                 and tbl_cattnosupp.idalunno = tbl_alunni.idalunno
                 and tbl_cattnosupp.idmateria = tbl_tipoprog.idmateria
                 and tbl_cattnosupp.idalunno = tbl_tipoprog.idalunno
                 and tbl_tipoprog.tipoprogr='P'
                 order by cognome, nome, datanascita";
          
          
           
         
          $ris=mysqli_query($con,inspref($query));
      if (mysqli_num_rows($ris)>0)
      {   
			
			print ("
         <form method='post' action='compalu.php' name='comp'>
   
         <p align='center'>
         <table align='center'>
         <tr>
            <td width='50%'><p align='center'><b>Cattedra</b></p></td>
            <td width='50%'>
            <SELECT NAME='idcattedra' ONCHANGE='comp.submit()'> <option value=''>&nbsp "); 
	  
          while($nom=mysqli_fetch_array($ris))
	       {
				// if (alunno_certificato_pei($nom['idalunno'],$nom['idmateria'],$con))
				// {
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
            // }
            
          }
        
   print("
      </SELECT>
      </td></tr></table></form>");
   }
   else
      print "<br><br><center><b>Nessuna cattedra per alunni con programma personalizzato!</b></center><br>";
 
 if ($idcattedra!="")
  {
	  // Controllo presenza della programmazione, se non esiste, propongo importazione
    $query="select count(*) as numerocomp from tbl_competalu
            where idmateria=$idmateria 
	         and idalunno=$idalunno";
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
          
    $nom=mysqli_fetch_array($ris);
  
    if ($nom['numerocomp']==0)
    {
		 print "<fieldset><center><form action='importaprogralu.php' method='post'>
		        <input type='hidden' name='idalunno' value='$idalunno'>
		        <input type='hidden' name='idcattedra' value='$idcattedra'>
		        <input type='hidden' name='idmateria' value='$idmateria'>
		        <input type='hidden' name='idclasse' value='$idclasse'>
		        Programmazione da importare: <select name='progrimp'><option value='i'>Istituto</option><option value='d'>Docente</option></select>
		        Tipo: <select name='completa'><option value='t'>Tutta</option><option value='o'>Obiettivi minimi</option></select>
		        <input type='submit' value='importa'>
		        </form></center></fieldset>";
   }
	  
	  
	  
	// Controllo presenza di voti per la programmazione dell'alunno
    $query="select count(*) as numerovoti from tbl_valutazioniabilcono, tbl_valutazioniintermedie
         where tbl_valutazioniabilcono.idvalint = tbl_valutazioniintermedie.idvalint
         and tbl_valutazioniintermedie.idmateria=$idmateria 
	      and tbl_valutazioniintermedie.idalunno=$idalunno";
             
	              
     $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
          
     $nom=mysqli_fetch_array($ris);
  
     if ($nom['numerovoti']>0)
     {
	     print ("<center><b><font color=red>Attenzione! Ci sono voti collegati a questa programmazione.<br/> 
	            La modifica della programmazione ne comporta la perdita!<br/>
	            Utilizzare la \"MODIFICA VOCI PROGRAMMAZIONE\" se non si vogliono perdere i voti!</font></b></center>");
     }
     else
     {
     
       $query="select * from tbl_competalu where idmateria=$idmateria and idalunno=$idalunno  order by numeroordine";
       $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
       print "<p align='center'>
              <font size=4 color='black'>Competenze </font>
              <form method='post' action='inscompalu.php'>
              <table border=1 align='center'>";
       $numord=0;
       while($val=mysqli_fetch_array($ris))
       {
           $numord++;
           $sintcomp=$val["sintcomp"];
           $competenza=$val["competenza"];
           $idcompetenza=$val["idcompetenza"];
			  print "<tr><td>$numord</td>
						<td>
							 SINTESI: <input type=text name=sint$numord value='$sintcomp' maxlength=80 size=80><br/>
							 <input type=hidden name=idcomp$numord value='$idcompetenza'>
							 <textarea cols=80 rows=3 name=est$numord>".$val['competenza']."</textarea></td>";
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
		 print "<input type='hidden' name='idcattedra' value='$idcattedra'>";
		
		 print "</form>";
    }
}  
       
mysqli_close($con);
stampa_piede(""); 

