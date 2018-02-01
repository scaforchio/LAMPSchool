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
  
	$titolo="Inserimento obiettivi alunni certificati";
	$script="";
    stampa_head($titolo,"",$script,"SDMAP");
	stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 


 $maxabil=10;
 $maxcono=10;
 //$materia = stringa_html('materia');
 //$anno = stringa_html('anno');
 $idcompetenza = stringa_html('idcompetenza');
 $idcattedra = stringa_html('idcattedra');
 
 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
  
 
 $query="delete from tbl_abilalu where idcompetenza=$idcompetenza";
 $ris=mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query));


 

 for ($no=1;$no<=$maxabil;$no++)
 {
    $abilita = stringa_html("ab$no");
    
    $sintabilcono = stringa_html("sintab$no");
    if ($sintabilcono!="")
    {    
        
        if (is_stringa_html("chkab$no"))
            $abobmin=1;
        else
            $abobmin=0;    
        
        $query="insert into tbl_abilalu(idcompetenza, numeroordine, sintabilcono, abilcono, obminimi, abil_cono) 
                values($idcompetenza,$no,'$sintabilcono', '$abilita', $abobmin, 'A')";
              
        $ris2=mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query)); 
    }
 }
    
    
  for ($no=1;$no<=$maxcono;$no++)
 {
	 
    $conoscenza = stringa_html("co$no");
    $sintabilcono = stringa_html("sintco$no");
    
    if ($sintabilcono!="")
    {    
		if (is_stringa_html("chkco$no"))
            $abobmin=1;
        else
            $abobmin=0;    
      
        $query="insert into tbl_abilalu(idcompetenza, numeroordine, sintabilcono, abilcono, obminimi, abil_cono) 
                values($idcompetenza,$no,'$sintabilcono', '$conoscenza', $abobmin, 'C')";
        $ris2=mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query)); 
    }
 }
 
   
  //  codice per richiamare il form delle competenze;
  print ("
   <form method='post' action='abcoalu.php'>
   <p align='center'>

     <input type='hidden' name='idcompetenza' value='$idcompetenza'>
     <input type='hidden' name='idcattedra' value='$idcattedra'> 
     <input type='submit' value='OK' name='b'></p>
     </form>
  ");
  
  mysqli_close($con);
  stampa_piede("");

