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


$idobiettivo = stringa_html('idobiettivo');

if ($tipoutente=="")
{
   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
   die;
} 

//
//    Parte iniziale della pagina
//

$titolo="Correggi obiettivo di comportamento";
$script=""; 

stampa_head($titolo,"",$script,"MA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='./modiobiettivo.php'>Scelta obiettivo</a> - $titolo","","$nome_scuola","$comune_scuola");
 




$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 


if ($idobiettivo!="")
{

   print ("
       <form method='post' action='insupdobiettivo.php' name='valcomp'>
   
      <p align='center'>
      <table align='center'>");
   
      //
      //   Leggo i dati della voce da modificare
      //

   
      $query="select * from tbl_compob where idobiettivo=$idobiettivo";
   
      $ris=mysqli_query($con,inspref($query));
   
   
   
   
      if($nom=mysqli_fetch_array($ris))
      {
         $sintesi=$nom["sintob"];
         $descri=$nom["obiettivo"];
         
         
      }
             
      print("    
                <tr>
                 <td><b>Sintesi</b></td>

                 <td>
                   <INPUT TYPE='text' VALUE='$sintesi' name='sintesi' maxlength='80' size='80'>
                   <input type='hidden' value='$idobiettivo' name='idob'>
                 </td></tr>");
   
      print("    
                <tr>
                <td><b>Descrizione</b></td>

                <td>
                <textarea name='descrizione' cols='80' rows='10'>$descri</textarea>
          
               </td></tr>");
      
   
 
     
       print("</table>");

       print "<center><input type='submit' value='Modifica obiettivo'></center>";
       print("</form>");        
        
}
else
{ 
	
	print "<center><font size=4 color=red>Selezionare una voce da modificare!</font>
	       <form action='modiobiettivo.php' method='post'>

	       <input type='submit' value='OK!'></form></center>";
}
	   
mysqli_close($con);
stampa_piede(""); 

      

