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
$iddocprog=0;

$titolo="Visualizzazione programmazione di classe";
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
 



$cattedra = stringa_html('cattedra');


print ("
   <form method='post' action='visprogrdo.php' name='comp'>
   
   <p align='center'>
   <table align='center'>
   <tr>
      <td width='50%'><p align='center'><b>Cattedra</b></p></td>
      <td width='50%'>
      <SELECT ID='cattedra' NAME='cattedra' ONCHANGE='comp.submit()'> <option value=''>&nbsp "); 
	  
  
          $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
           
           //    if ($tipoutente!='P')
             $query="select idcattedra,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria order by anno, sezione, specializzazione, denominazione";
         /* else
             $query="SELECT idcattedra,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione
                      FROM tbl_cattnosupp, tbl_classi, tbl_materie
                      WHERE tbl_cattnosupp.idclasse = tbl_classi.idclasse
                      AND tbl_cattnosupp.idmateria = tbl_materie.idmateria
                      AND EXISTS (
                                   SELECT * FROM tbl_competdoc
                                   WHERE tbl_competdoc.idmateria = tbl_cattnosupp.idmateria and  tbl_competdoc.idclasse = tbl_cattnosupp.idclasse
                                 )
                      ORDER BY anno, sezione, specializzazione, denominazione";  */
          $ris=mysqli_query($con,inspref($query));
          while($nom=mysqli_fetch_array($ris))
	      {
            print "<option value='";
            print ($nom["idcattedra"]);
            print "'";
            if ($cattedra==$nom["idcattedra"])
               print " selected";
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
   
 

  if ($cattedra!="")
  {
	  
	  if ($tipoutente=='P')
	  {
	      $query = "select iddocente from tbl_cattnosupp where idcattedra=$cattedra";
	     
	      $risdoc=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
	      $val=mysqli_fetch_array($risdoc);
	      $iddocprog=$val['iddocente'];
	      
	  }
	  
	  
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
     
   
    if ($tipoutente=='P')
        $query="select * from tbl_docenti where iddocente=$iddocprog";
    else
        $query="select * from tbl_docenti where iddocente=$iddocente";
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    if ($val=mysqli_fetch_array($ris))
    {
		$cognome=$val["cognome"];
		$nome=$val["nome"];
	}
	$query="select idcattedra,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione from tbl_cattnosupp, tbl_classi, tbl_materie where idcattedra=$cattedra and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria order by anno, sezione, specializzazione, denominazione";
    $ris=mysqli_query($con,inspref($query));
    if($val=mysqli_fetch_array($ris))
	{
		$materia=($val["denominazione"]);
        $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];
            
	}     
		print "<center>Programmazione della classe: $classe<br/>";
		print "Materia: $materia<br/>";
		if ($tipoutente!='P')
		   print "Docente: $nome $cognome";
		print "</center>";
    
    
    $idmateria=estrai_id_materia($cattedra, $con);
    $idclasse=estrai_id_classe($cattedra, $con);
    
    
    $query="select * from tbl_competdoc where idmateria=$idmateria and idclasse=$idclasse order by numeroordine";
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    
    print "<font size=2>";
    while($val=mysqli_fetch_array($ris))
    {
		
		
        $numord=$val["numeroordine"];
        $sintcomp=$val["sintcomp"];
        $competenza=$val["competenza"];
        $idcompetenza=$val["idcompetenza"];
        print "<br/><br/><b>$numord. $sintcomp</b><br>  $competenza";
        
        $query="select * from tbl_abildoc where idcompetenza=$idcompetenza and abil_cono='C' order by numeroordine";
        $risabil=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
        print "<font size=1>";
        while($valabil=mysqli_fetch_array($risabil))
        { 
               $sintabil=$valabil["sintabilcono"];
               $numordabil=$valabil["numeroordine"];
               $abilita=$valabil["abilcono"];
               $obminimi=$valabil["obminimi"];
               // if ($numordabil==1) print "<br/><b><big><center>CONOSCENZE</center></big></b>"; 
               if (!$obminimi)
                  print "<br/><b>C $numord.$numordabil $sintabil</b><br> $abilita";
               else
                  print "<br/><i><b>C $numord.$numordabil $sintabil</b><br> $abilita</i>";
        }
        
        $query="select * from tbl_abildoc where idcompetenza=$idcompetenza and abil_cono='A' order by numeroordine";
        $risabil=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
        
        while($valabil=mysqli_fetch_array($risabil))
        { 
               $sintabil=$valabil["sintabilcono"];
               $numordabil=$valabil["numeroordine"];
               $abilita=$valabil["abilcono"];
               $obminimi=$valabil["obminimi"];
               // if ($numordabil==1) print "<br/><b><big><center>ABILITA'</center></big></b>"; 
               if (!$obminimi)
                  print "<br/><b>A $numord.$numordabil $sintabil</b><br> $abilita";
               else
                  print "<br/><i><b>A $numord.$numordabil $sintabil</b><br> $abilita</i>";
        }
        print "</font>";              
    }   
    print "<br/><br/>(Le voci in <i>corsivo</i> fanno parte degli obiettivi minimi)";
   
   print "</font>";
   
   print"<br/><center><a href=javascript:Popup('staprogrdo.php?cattedra=$cattedra')><img src='../immagini/stampa.png'></a><br/><br/>";
   
  }
    
   
   
         
mysqli_close($con);
stampa_piede(""); 

