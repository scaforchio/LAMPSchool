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


	/*Programma per la conferma della importazione della programmazione.*/

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


    $titolo="Conferma importazione programmazione";
	
	
	$script="";
    stampa_head($titolo,"",$script,"SDMAP");
	stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 
	
	print "<form name='form1' action='importaprogr.php' method='POST'>";
    print "<table border='0' align='center'>";
	print "<tr> <td align='center'><br/><b>Confermi l'importazione della programmazione scolastica?</b><br/>
	       <font color='red'>Tale importazione canceller&aacute; eventuali personalizzazioni 
	       <br/>effettuate alla programmazione nelle proprie classi.</font><br/><br/>";
    print "</td></tr>"; 
	print "<tr><td align='center'><input type='submit' value='CONFERMA'></td></tr>";
    print " </form>"; 
	//tasto indietro	
                  
	print "<form action='../login/ele_ges.php' method='POST'>";
    print "<tr><td align='center'><input type='submit' name='Home' value='ANNULLA'></td></tr>";
    print "</table></form> "; 
          
    stampa_piede("");

 		
	
  



