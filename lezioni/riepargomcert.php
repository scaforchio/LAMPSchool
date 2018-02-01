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
$titolo="Riepilogo argomenti svolti";
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
 



$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 
$modo=stringa_html('modo');


// CODICE PER GESTIONE RICHIAMO DA RIEPILOGO

$codlez = stringa_html('idlezione');
$idclasse="";
$idalunno="";
$idmateria="";

if ($codlez!="")
{
    $query="select * from tbl_lezionicert where idlezione=$codlez";
    $ris=mysqli_query($con,inspref($query));
    $lez=mysqli_fetch_array($ris);
    
//
//   Recupero cattedra da idlezione
//

      $idmateria=$lez['idmateria'];
      $idalunno=$lez['idalunno'];
   
      $query="select idcattedra from tbl_cattnosupp where idalunno=$idalunno and idmateria=$idmateria"; 
          
      $ris=mysqli_query($con,inspref($query));
      if($nom=mysqli_fetch_array($ris))
      {
          $catt=$nom['idcattedra'];
      }
     
   
   
   
   $id_ut_doc = $_SESSION["idutente"];
   
     
}

// FINE CODICE PER GESTIONE DA RIEPILOGO
else

{
  
   $catt = stringa_html('cattedra');
   if ($catt != "")
   {
      // RECUPERO idclasse e idmateria dalla cattedra
      // Prelevo classe e materia dalla cattedra selezionata
       $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

        
      
       if ($catt<>"")
       {
           $query="select idalunno, idmateria from tbl_cattnosupp where idcattedra=$catt"; 
           $ris=mysqli_query($con,inspref($query));
           if($nom=mysqli_fetch_array($ris))
           {
              $idmateria=$nom['idmateria'];
              $idalunno=$nom['idalunno'];
           }
       }

   } 
   $id_ut_doc = $_SESSION["idutente"];

  // $meseanno = stringa_html('mese');  // In effetti contiene sia il mese che l'anno
   // Divido il mese dall'anno
  // $mese=substr($meseanno,0,2);
  //  $anno=substr($meseanno,5,4);


}



//if ($mese=='')
//   $mese=date('m');
//if ($anno=='')
//   $anno=date('Y');


print ('
   <form method="post" action="riepargomcert.php" name="lezioni">
   
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
          </td></tr><tr><td><b>Cattedra</b></td><td><select name='cattedra' id='cattedra' ONCHANGE='lezioni.submit()'><option value=''>&nbsp; ");


//
//  Riempimento combobox delle tbl_cattnosupp
//
if ($modo=='sost')
   $query="select idcattedra,tbl_alunni.idalunno as idalunno,tbl_cattnosupp.idmateria as idmateria, cognome, nome, datanascita, denominazione
             from tbl_cattnosupp, tbl_alunni, tbl_materie 
             where iddocente=$iddocente 
             and tbl_cattnosupp.idalunno=tbl_alunni.idalunno 
             and tbl_cattnosupp.idmateria = tbl_materie.idmateria 
             order by cognome,nome, denominazione";
else
   $query="select idcattedra,tbl_cattnosupp.idalunno as idalunno,tbl_cattnosupp.idmateria as idmateria,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione
					from tbl_cattnosupp, tbl_classi, tbl_materie
					where tbl_cattnosupp.idclasse=tbl_classi.idclasse 
					and tbl_cattnosupp.idmateria = tbl_materie.idmateria 
					and tbl_cattnosupp.idalunno<>0
					and tbl_classi.idclasse in (select idclasse from tbl_cattnosupp where iddocente=$iddocente)
					and tbl_materie.idmateria in (select idmateria from tbl_cattnosupp where iddocente=$iddocente)
					order by tbl_cattnosupp.idalunno, denominazione";  
// print "tttt: ".inspref($query);

$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{ 
  print "<option value='";
  print ($nom["idcattedra"]);
  print "'";

  if ($idalunno==$nom["idalunno"] & $idmateria==$nom["idmateria"])
     print " selected";
  print ">";
 /* print ($nom["cognome"]);
  print "&nbsp;"; 
  print($nom["nome"]); 
  print "&nbsp;(";
  print($nom["datanascita"]);
  print ")";
  */
  print estrai_alunno_data($nom['idalunno'],$con);
  print("&nbsp;-&nbsp;");
  print($nom["denominazione"]);
}
       
echo('
      </SELECT>
      </td></tr>');
      

print("</table></form>");
   
//  if ($mese=="")
//     $m=0;
//  else
//     $m=$mese; 
  
//  if ($anno=="") 
//     $a=0;
//  else
//     $a=$anno; 
  

  // print($nome." -   ". $g.$m.$a.$giornosettimana);
  
 //   $idclasse=$nome;
    $classe="";
    
if ($idalunno!="")
{   
    
    
    echo '
          <center><h3>Argomenti ed attivit&agrave; svolte con '.estrai_dati_alunno($idalunno,$con).' per '.decodifica_materia($idmateria,$con).'</h3></center>
          
          <table border=2 align="center">';
            
//
//   ESTRAZIONE DATI DELLE LEZIONI
//

    
    if ($idalunno!="")
    {
      echo'
          <tr class="prima">
          
          <td width=10%>Data</td>
          <td width=45%>Argomenti</td>
          <td width=45%>Attivit&agrave;</td>';
    

       $query="select * from tbl_lezionicert where idalunno=$idalunno and idmateria=$idmateria order by datalezione";
   
       $rislez=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    
       while ($reclez=mysqli_fetch_array($rislez))
  		 {
			 print "<tr><td><a href='lezcert.php?idlezione=".$reclez['idlezione']."&provenienza=argo'>".data_italiana($reclez['datalezione'])."</a></td><td>".$reclez['argomenti']."&nbsp;</td><td>".$reclez['attivita']."&nbsp;</td></tr>";
		 } 
		 
 	          
   
    echo'</table>';
    print"<br/><center><a href=javascript:Popup('riepargomstampacert.php?cattedra=$catt')>Stampa argomenti e attività</a><br/>";
    print"<br/><center><a href=javascript:Popup('riepprogrammacert.php?cattedra=$catt')>Stampa programma svolto (solo argomenti)</a><br/><br/>";
  }
   
  }


mysqli_close($con);
stampa_piede(""); 

