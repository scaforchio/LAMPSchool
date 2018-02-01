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


$titolo="Gestione competenze del programma";

$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

$maxcomp=20;

$materia = stringa_html('materia');
$anno = stringa_html('anno');

print ("
   <form method='post' action='compsc.php' name='comp'>
   
   <p align='center'>
   <table align='center'>
   <tr>
      <td width='50%'><p align='center'><b>Materia</b></p></td>
      <td width='50%'>
      <SELECT ID='materia' NAME='materia' ONCHANGE='comp.submit()'> <option>&nbsp "); 
	  
  
       
          $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
           
          $query="select idmateria, denominazione from tbl_materie order by denominazione";
          $ris=mysqli_query($con,inspref($query));
          while($nom=mysqli_fetch_array($ris))
	  {
            print "<option value='";
            print ($nom["idmateria"]);
            print "'";
			if ($materia==$nom["idmateria"])
			   print " selected";
			print ">";
            print ($nom["denominazione"]);
            
          }
        
   print("
      </SELECT>
      </td></tr>

      <tr>
      <td width='50%'><p align='center'><b>Anno</b></p></td>");

//
//   Inizio visualizzazione Anno
//


    
  print("<td>   <select name='anno' ONCHANGE='comp.submit()'>");
    for($a=1;$a<=($numeroanni);$a++)
    {
      if ($a==$anno)
         print("<option selected>$a");
      else
         print("<option value='$a'>$a");
    } 
    echo("</select>");  




print("    </form></td>
   
</table><hr>
 
    ");
   
 

  if (($materia!="")&&($anno!=""))
  {
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
     
   
    $query="select * from tbl_competscol where idmateria=$materia and anno=$anno order by numeroordine";
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    print "<p align='center'>
          <font size=4 color='black'>Competenze </font>
          <form method='post' action='inscompsc.php'>
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
        print "<td>&nbsp;</td>";
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
    print "<input type='hidden' name='materia' value='$materia'>";
    print "<input type='hidden' name='anno' value='$anno'>";
    print "</form>";
  }
    

         
mysqli_close($con);
stampa_piede(""); 

