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
 $tipoutente=$_SESSION["tipoutente"];
 $iddocente=$_SESSION["idutente"]; //prende la variabile presente nella sessione
    if ($tipoutente=="")
       {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
       } 


$titolo="Copia programmazione ";

	
	$script="";
    stampa_head($titolo,"",$script,"SDMAP");
	stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 
$numerocattedre=0;

print ("
   <form method='post' action='inscopiaprogdoc.php' name='copiaprog'>
   
   <p align='center'>
   
   <table align='center'>
");      

print("<tr>
      <td><p align='center'><b>Origine</p></td><td>&nbsp;</td>
      <td><p align='center'><b>Destinazione</p></td>
      </tr>
      <tr>
      <td>
      <select id='cattorig' name='cattorig'>");
      
      $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
           
         // SELEZIONO SOLO LE CATTEDRE DOVE E' PRESENTE ALMENO UN OBIETTIVO
         $query="SELECT idcattedra, tbl_classi.idclasse, anno, sezione, specializzazione, denominazione, tbl_materie.idmateria
                 FROM tbl_cattnosupp, tbl_classi, tbl_materie
                 WHERE iddocente =$iddocente
                 AND tbl_cattnosupp.idclasse = tbl_classi.idclasse
                 AND tbl_cattnosupp.idmateria = tbl_materie.idmateria
                 AND tbl_cattnosupp.idalunno=0 
                 AND EXISTS (SELECT * FROM tbl_abildoc,tbl_competdoc
                              where tbl_abildoc.idcompetenza=tbl_competdoc.idcompetenza
                              and tbl_competdoc.idmateria = tbl_cattnosupp.idmateria and  tbl_competdoc.idclasse = tbl_cattnosupp.idclasse)
                 ORDER BY anno, sezione, specializzazione, denominazione"; 
          $ris=mysqli_query($con,inspref($query));
          while($nom=mysqli_fetch_array($ris))
	      {
			
            print "<option value='";
            print ($nom["idcattedra"]);
            print "'";
            
            print ">";
            
            print ($nom["anno"]);
            
            print "&nbsp;"; 
            print($nom["sezione"]); 
            print "&nbsp;";
            print($nom["specializzazione"]);
            print "&nbsp;-&nbsp;";
            print($nom["denominazione"]);
            
          }
      
      
      
      
      
      echo ("</select>
      </td><td align=center valign=middle><font size=5><b>--->>></b></font></td>");
      $query="SELECT idcattedra, tbl_classi.idclasse, anno, sezione, specializzazione, denominazione, tbl_materie.idmateria
                  FROM tbl_cattnosupp, tbl_classi, tbl_materie
                  WHERE iddocente =$iddocente
                  AND tbl_cattnosupp.idclasse = tbl_classi.idclasse
                  AND tbl_cattnosupp.idmateria = tbl_materie.idmateria
                  AND tbl_cattnosupp.idalunno=0 
                  AND NOT EXISTS ( SELECT *
                                  FROM tbl_valutazioniabilcono, tbl_valutazioniintermedie, tbl_alunni
                                  WHERE tbl_valutazioniabilcono.idvalint = tbl_valutazioniintermedie.idvalint
                                  AND tbl_valutazioniintermedie.idalunno = tbl_alunni.idalunno
                                  AND tbl_valutazioniintermedie.idmateria = tbl_materie.idmateria
                                  AND tbl_alunni.idclasse = tbl_classi.idclasse
                                )
                  ORDER BY anno, sezione, specializzazione, denominazione";
          
      $ris=mysqli_query($con,inspref($query));
      $numerocattedre=mysqli_num_rows($ris);
      
      
      
      echo ("
      <td>
      <select multiple size='$numerocattedre' id='cattdest[]' name='cattdest[]'>");
        // Vengono selezionate le classi per le quali non sono state già inserite valutazioni legate agli obiettivi
          
          while($nom=mysqli_fetch_array($ris))
	      {
			 $numerocattedre++; 
            print "<option value='";
            print ($nom["idcattedra"]);
            print "'";
           
            print ">";
            
            print ($nom["anno"]);
            
            print "&nbsp;"; 
            print($nom["sezione"]); 
            print "&nbsp;";
            print($nom["specializzazione"]);
            print "&nbsp;-&nbsp;";
            print($nom["denominazione"]);
            
          }
      
      
      
      
      
      echo("</select>
      </td>
      
      </tr></table>");
      
      
      echo "<center><input type='submit' value='Copia programmazione'>";
    
    print "</form>";
	  
    

         
mysqli_close($con);
stampa_piede(""); 

