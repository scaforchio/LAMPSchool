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

$titolo="Copia programmazione tra classi";
	$script="";
    stampa_head($titolo,"",$script,"SDMAP");
	stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 



 $origine = stringa_html('cattorig');
 $arrdest = is_stringa_html('cattdest') ? stringa_html('cattdest') : array();
 
 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
  
  
 foreach ($arrdest as $destinazione)
 {
	 //
	 //   Cancello la precedente programmazione per la cattedra
	 //
	 
	 if ($origine!=$destinazione)
	 {
	 
	  $idmateriad=estrai_id_materia($destinazione, $con);
	  $idclassed=estrai_id_classe($destinazione, $con);
	  
	  $idmateriao=estrai_id_materia($origine, $con);
	  $idclasseo=estrai_id_classe($origine, $con);
	  
	  
	  $query="delete from tbl_competdoc where tbl_competdoc.idmateria = $idmateriad and  tbl_competdoc.idclasse = $idclassed";
     $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));

     // Estraggo tutte le competenze della cattedra origine
     $query="select * from tbl_competdoc where tbl_competdoc.idmateria = $idmateriao and  tbl_competdoc.idclasse = $idclasseo"; 
     $riscomp=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
     while($comp=mysqli_fetch_array($riscomp))            //    <-----------  ttttttt
     {
		  
        $idcompetenza=$comp['idcompetenza'];
        $numord=$comp['numeroordine'];
        $sintcomp=$comp['sintcomp'];
        $competenza=$comp['competenza'];
        
        $query="insert into tbl_competdoc(idmateria,idclasse,numeroordine, sintcomp, competenza)
                values ($idmateriad,$idclassed,$numord,'$sintcomp','$competenza')";
        
        $ris=mysqli_query($con,inspref($query)) or die(mysqli_error($con));
        $numcomp=mysqli_insert_id($con);
        
        // Estraggo tutti gli obiettivi per la competenza
        $query="select * from tbl_abildoc where idcompetenza=$idcompetenza"; 
        $risobiet=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
        while($obiet=mysqli_fetch_array($risobiet))            //    <-----------  ttttttt
        {
			   $numordob=$obiet['numeroordine'];
            $sintobiet=$obiet['sintabilcono'];
            $abilcono=$obiet['abilcono'];
            $obminimi=$obiet['obminimi'];
            $abil_cono=$obiet['abil_cono'];
            $queryab="insert into tbl_abildoc(idcompetenza, numeroordine, sintabilcono, abilcono, obminimi,abil_cono)
                values ($numcomp,$numordob,'$sintobiet','$abilcono','$obminimi','$abil_cono')";
            $risab=mysqli_query($con,inspref($queryab)) or die(mysqli_error($con));
        }
	}
    }
}

               
  //  codice per richiamare il form delle tbl_assenze;
 
  print ("
   <form method='post' action='copiaprogdoc.php'>
   <p align='center'>");
     print("<input type='submit' value='OK' name='b'></p>
     </form>");
mysqli_close($con);
stampa_piede("");

