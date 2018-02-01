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
 
 @require_once("../php-ini".$_SESSION['suffisso'].".php");
 @require_once("../lib/funzioni.php");
    
 // istruzioni per tornare alla pagina di login se non c'è una sessione valida
 ////session_start();
 $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
    if ($tipoutente=="")
       {
          header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
          die;
       } 

$titolo="Rigenerazione password alunni";
$script="<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>"; 

stampa_head($titolo,"",$script,"SMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
    
 

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 

print "<center><br/>";

print("<form action='alu_rigenera_password_ins_sta.php' method='POST'><SELECT name='idclasse'><option value='-1'>&nbsp;</option><option value='0'>Tutte</option>");
// Riempimento combo box tbl_classi
$query="select idclasse,anno,sezione,specializzazione from tbl_classi order by specializzazione, sezione, anno";
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
   print "<option value='";
   print ($nom["idclasse"]);
   print "'";
   print ">";
   print ($nom["anno"]);
   print "&nbsp;"; 
   print($nom["sezione"]); 
   print "&nbsp;";
   print($nom["specializzazione"]);
}
        
print("
      </SELECT><br/><br/>
      ");


print("<center><b><font color='red'>ATTENZIONE! Premendo il pulsante sottostante si rigenereranno le password per tutti gli alunni della classe.</font></b></center><br>");
 
   
print("<input type='submit' value='Rigenera e stampa password'></p>
     </form>
  ");

mysqli_close($con);
stampa_piede("");
  

