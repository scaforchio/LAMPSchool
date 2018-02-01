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

//
//    Parte iniziale della pagina
//

$titolo="Correggi competenza";
$script=""; 

stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 


$idcomp = stringa_html('comp');
$idcattedra = stringa_html('idcatt');

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 


if ($idcomp!="")
{

   print ("
       <form method='post' action='insupdcompprogalu.php' name='valcomp'>
   
      <p align='center'>
      <table align='center'>");
   
      //
      //   Leggo i dati della voce da modificare
      //

   
      $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
       
      $query="select * from tbl_competalu where idcompetenza=$idcomp";
   
      $ris=mysqli_query($con,inspref($query));
   
   
   
   
      if($nom=mysqli_fetch_array($ris))
      {
         $sintesi=$nom["sintcomp"];
         $descri=$nom["competenza"];
         
         
      }
             
      print("    
                <tr>
                 <td><b>Sintesi</b></td>

                 <td>
                   <INPUT TYPE='text' VALUE='$sintesi' name='sintesi' maxlength='80' size='80'>
                   <input type='hidden' value='$idcomp' name='idcomp'>
                 </td></tr>");
   
      print("    
                <tr>
                <td><b>Descrizione</b></td>

                <td>
                <textarea name='descrizione' cols='80' rows='10'>$descri</textarea>
          
               </td></tr>");
      
   
 
     
       print("</table>");
       print "<input type='hidden' name='cattedra' value='$idcattedra'>";        
       print "<center><input type='submit' value='Modifica competenza'></center>";
       print("</form>");        
        
}
else
{ 
	
	print "<center><font size=4 color=red>Selezionare una voce da modificare!</font>
	       <form action='modicompetenzaalu.php' method='post'>
	       <input type='hidden' name='cattedra' value='$idcattedra'>
	       <input type='submit' value='OK!'></form></center>";
}
	   
mysqli_close($con);
stampa_piede(""); 

