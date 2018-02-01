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

$titolo="Ricaricamento proposte";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("$titolo","","$nome_scuola","$comune_scuola");
 

 $idscrutinio = stringa_html('idscrutinio');
 
 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
  
 $query="SELECT idclasse,periodo FROM tbl_scrutini WHERE idscrutinio=$idscrutinio";
 $ris=mysqli_query($con,inspref($query));
 $rec=mysqli_fetch_array($ris);
 $idclasse=$rec['idclasse'];
 $periodo=$rec['periodo'];
 
 $query="delete from tbl_valutazionifinali where idclasse=$idclasse and periodo=$periodo";
 $ris=mysqli_query($con,inspref($query));
 
 $queryins = "INSERT into tbl_valutazionifinali(idalunno,idmateria,votounico,votoscritto,votoorale,votopratico,assenze,periodo, note)
							  SELECT tbl_proposte.idalunno,idmateria,unico,scritto,orale,pratico,assenze,periodo,tbl_proposte.note from tbl_proposte,tbl_alunni 
							  where tbl_proposte.idalunno=tbl_alunni.idalunno
							  and idclasse=$idclasse and periodo=$periodo";
		        
 $risins =  mysqli_query($con,inspref($queryins)) or die(mysqli_error()); 
 
 
 print "
        <form method='post' id='formchiscr' action='../scrutini/riepvoti.php'>
        <input type='hidden' name='cl' value='$idclasse'>
        <input type='hidden' name='periodo' value='$periodo'>
        </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formchiscr').submit();
        }
        </SCRIPT>";
	   
   
  stampa_piede("");
  

