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
  
	$titolo="Inserimento subobiettivo";
	$script="";
    stampa_head($titolo,"",$script,"MA");
	stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 


 $maxsubob=10;
 
 //$materia = stringa_html('materia');
 //$anno = stringa_html('anno');
 $idobiettivo = stringa_html('idobiettivo');
  
 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
  
 for ($no=1;$no<=$maxsubob;$no++)
 {
    $sintsubob = stringa_html("sintab$no");
    $subob = stringa_html("ab$no");
    $idsubob = stringa_html("idabil$no");
   
    if ($sintsubob!="" & $idsubob=="")
    {   		 
		  
		   $query="insert into tbl_compsubob(idobiettivo, numeroordine, sintsubob, subob)
		           values($idobiettivo,$no,'$sintsubob', '$subob')";
           $ris2=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". inspref($query));
	     
    }
    if ($sintsubob!="" & $idsubob!="")
    {    
		  
		  $query="update tbl_compsubob set sintsubob='$sintsubob',subob='$subob' where idsubob=$idsubob";
        $ris2=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con)); 
    }
    if ($sintsubob=="" & $idsubob!="")
    {    
		  
		  $query="delete from tbl_compsubob where idsubob=$idsubob";
        $ris2=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con)); 
    }
 } 
  


   
  //  codice per richiamare il form delle abilità e conoscenze;
  print ("
   <form method='post' action='subobiettivi.php'>
   <p align='center'>

     <input type='hidden' name='idobiettivo' value='$idobiettivo'>
      
     <input type='submit' value='OK' name='b'></p>
     </form>
  ");
  
  mysqli_close($con);
  stampa_piede("");

