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


$titolo="Gestione obiettivi del programma";

$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

$maxabil=10;
$maxcono=10;

//$materia = stringa_html('materia');
//$anno = stringa_html('anno');
$competenza = stringa_html('competenza');

print ("
   <form method='post' action='abcosc.php' name='abilcono'>
   
   <p align='center'>
   <table align='center'>
");      

print("<tr>
      <td width='50%'><p align='center'><b>Competenza</b> (Anno - Materia - Competenza)</p></td>
      <td width='50%'>
      <SELECT ID='competenza' NAME='competenza' ONCHANGE='abilcono.submit()'> <option value=''>&nbsp "); 
	  
     //  if ($materia!="" && $anno!="") 
     //  {
          $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
           
          $query="select numeroordine, idcompetenza, denominazione, sintcomp, anno from tbl_competscol,tbl_materie 
                  where tbl_competscol.idmateria = tbl_materie.idmateria
                   order by anno, denominazione, numeroordine";
          
          $riscomp=mysqli_query($con,inspref($query));
           while($nom=mysqli_fetch_array($riscomp))
	       {
            print "<option value='";
            print ($nom["idcompetenza"]);
            print "'";
			if ($competenza==$nom["idcompetenza"])
			   print " selected";
			print ">";
            print ($nom["anno"]." - ".$nom["denominazione"]." - ".$nom["sintcomp"]);
            print "</option>";
           }
	     
     //   }
   print("
      </SELECT>
      </td></tr>");

   
print("</table><hr>");
print("</form>");   
 

  if ($competenza!="")
  {
	 //
	 //   GESTIONE ABILITA'
	 // 
	  
	  
	  
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
     
   
    $query="select * from tbl_abilscol where idcompetenza=$competenza and abil_cono='A' order by numeroordine";
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    print "<p align='center'>
          <font size=4 color='black'>Abilit&agrave;</font>
          <form method='post' action='insabcosc.php'>
          <table border=1 align='center'>";
    $numord=0;
    while($val=mysqli_fetch_array($ris))
    {
        $numord++;
        $abilita=$val["abilcono"];
        $sintabilcono=$val["sintabilcono"];
        print "<tr><td>$numord</td><td>
            SINTESI: <input type=text name=sintab$numord value='$sintabilcono' maxlength=80 size=80><br/>
            <textarea cols=70 rows=3 name='ab$numord'>$abilita</textarea></td><td>";
        if ($val["obminimi"])
            print "Ob.Min. <input type='checkbox' name='chkab$numord' checked value='$numord'></td></tr>";
        else
            print "Ob.Min. <input type='checkbox' name='chkab$numord' value='$numord'></td></tr> ";   
                    
    }   
    for($no=$numord+1;$no<=$maxabil;$no++)
        print "<tr><td>$no</td><td>
               SINTESI: <input type=text name=sintab$no value='' maxlength=80 size=80><br/>
               <textarea cols=70 rows=3 name='ab$no'></textarea></td><td>Ob.Min. <input type='checkbox' name='chkab$no' value='$no'> </td></tr>";
    print "</table></p>";
    
    //
    //  GESTIONE CONOSCENZE
    //
        
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
     
   
    $query="select * from tbl_abilscol where idcompetenza=$competenza and abil_cono='C' order by numeroordine";
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
      
    print "
           <p align='center'>
           <font size=4 color='black'>Conoscenze</font>
          
           <table border=1 align='center'>";
    $numord=0;
    while($val=mysqli_fetch_array($ris))
    {
        $numord++;
        $conoscenza=$val["abilcono"];
        $sintabilcono=$val["sintabilcono"];
        print "<tr><td>$numord</td><td>
              SINTESI: <input type=text name=sintco$numord value='$sintabilcono' maxlength=80 size=80><br/>
              <textarea cols=70 rows=3 name='co$numord'>$conoscenza</textarea></td><td>";
        if ($val["obminimi"])
            print "Ob.Min. <input type='checkbox' name='chkco$numord' checked value='$numord'></td></tr>";
        else
            print "Ob.Min. <input type='checkbox' name='chkco$numord' value='$numord'></td></tr>";   
    }   
    for($no=$numord+1;$no<=$maxcono;$no++)
        print "<tr><td>$no</td><td>
                SINTESI: <input type=text name=sintco$no value='' maxlength=80 size=80><br/>
                <textarea cols=70 rows=3 name='co$no'></textarea></td><td>Ob.Min. <input type='checkbox' name='chkco$no' value='$no'></td></tr>";
    print "</table></p>";
	print "<table align='center'>
			   <tr><td colspan=2 align=center><input type='submit' value='Registra abilità e conoscenze'></tr></table>";
    print "<input type='hidden' name='competenza' value='$competenza'>";
    //print "<input type='hidden' name='anno' value='$anno'>";
    //print "<input type='hidden' name='materia' value='$materia'>";
    print "</form>";
  }
  else
  {
	  print("");
  }
	  
    

         
mysqli_close($con);
stampa_piede(""); 

