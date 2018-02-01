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



 /*
      Programma per l'inserimento delle tbl_cattnosupp
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
$titolo="Aggiornamento tipo programmazione alunni certificati";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");

$numeromaterie=stringa_html('nummat');
$idalunno=stringa_html('idalunno'); 

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
  
 
for ($i=1;$i<=$numeromaterie;$i++)
{
	
   $strtipo = "tipo$i";
   $strmat = "mat$i";
   $tipoprogr=stringa_html($strtipo);
   $idmateria=stringa_html($strmat);
   $query="delete from tbl_tipoprog where idalunno='$idalunno' and idmateria='$idmateria'";
   mysqli_query($con,inspref($query));
   $query="insert into tbl_tipoprog(idalunno,idmateria,tipoprogr) values ('$idalunno','$idmateria','$tipoprogr')";
   mysqli_query($con,inspref($query));
  

}

print ("<center><br><br>Dati correttamente inseriti!</center>");
print ("<center><br><form action='seletipoprogr.php'><input type='hidden' name='idalunno' value='$idalunno'><input type='submit' value='Torna ai dati'></form></center>");
mysqli_close($con);
stampa_piede("");

