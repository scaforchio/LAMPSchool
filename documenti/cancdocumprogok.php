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

$tipodoc = stringa_html('tipodoc');
switch ($tipodoc)
{
    case 'pia':
        $titolo = "Cancellazione piano lavoro";
        $back = "Gestione piani lavoro";
        $tipodocumento = 1000000001;
        break;
    case 'pro':
        $titolo = "Cancellazione programma";
        $back = "Gestione programmi";
        $tipodocumento = 1000000002;
        break;
    case 'rel':
        $titolo = "Cancellazione relazione finale";
        $back = "Gestione relazioni finali";
        $tipodocumento = 1000000003;
        break;
}

$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='../documenti/pianilavoro.php'>$back</a> - $titolo","","$nome_scuola","$comune_scuola");
 


 $iddocumento = stringa_html('iddocumento');

 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
  
 
 $querycanc="delete from tbl_documenti where iddocumento=$iddocumento";
 
 $riscanc=mysqli_query($con,inspref($querycanc)) or die ("Errore nella query di cancellazione: ". mysqli_error($con));  
        	 
 
 print "<center><b><br>Cancellazione effettuata!<br></b></center> ";
       
  print ("
   <form method='post' action='documprog.php?tipodoc=$tipodoc'>
   <p align='center'>");
  
 
    print("   <input type='submit' value='OK' name='b'></p>

     </form>
  ");
  mysqli_close($con);
  stampa_piede("");

