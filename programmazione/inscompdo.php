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

 $titolo="Inserimento competenze";
 $script="";
 stampa_head($titolo,"",$script,"SDMAP");
 stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 


 $cattedra = stringa_html('cattedra');
 
 
 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
  
 
 $query="select idclasse,idmateria from tbl_cattnosupp where idcattedra=$cattedra";
 $ris=mysqli_query($con,inspref($query)) or die(mysqli_error($con));
 $val=mysqli_fetch_array($ris);
 $idmateria=$val['idmateria'];
 $idclasse=$val['idclasse'];
 //$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));

 for ($no=1;$no<=20;$no++)
 {
    $sintcomp = stringa_html("sint$no");
    $competenza = stringa_html("est$no");
    $idcompetenza = stringa_html("idcomp$no");
    if ($sintcomp!="" & $idcompetenza=="")
    {   		 
		  $posins = stringa_html("pos$no");
		  if ($posins!=0)
		  {    
		     $query="update tbl_competdoc set numeroordine = numeroordine+1 where idclasse=$idclasse and idmateria=$idmateria and numeroordine>=$posins";
		     $risupd=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con)); 
		     $query="insert into tbl_competdoc(idclasse, idmateria, numeroordine, sintcomp, competenza) values($idclasse,$idmateria,$posins,'$sintcomp', '$competenza')";
           $ris2=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con)); 
		  }
		  else
		  {   
		     $query="insert into tbl_competdoc(idclasse, idmateria, numeroordine, sintcomp, competenza) values($idclasse,$idmateria,$no,'$sintcomp', '$competenza')";
           $ris2=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con)); 
	     }
    }
    if ($sintcomp!="" & $idcompetenza!="")
    {    
		  
		  $query="update tbl_competdoc set sintcomp='$sintcomp',competenza='$competenza' where idcompetenza=$idcompetenza";
        $ris2=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con)); 
    }
    if ($sintcomp=="" & $idcompetenza!="")
    {    
		  
		  $query="delete from tbl_competdoc where idcompetenza=$idcompetenza";
        $ris2=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con)); 
    }
 }
    
  //  codice per richiamare il form delle competenze;
  print ("
   <form method='post' action='compdo.php'>
   <p align='center'>

     <input type='hidden' name='cattedra' value='$cattedra'>
     <input type='hidden' name='idclasse' value='$idclasse'>
     <input type='hidden' name='idmateria' value='$idmateria'>
     <input type='submit' value='OK' name='b'></p>
     </form>
  ");
  
  // 
  mysqli_close($con);
  stampa_piede("");

