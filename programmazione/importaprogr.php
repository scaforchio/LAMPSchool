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
    if ($tipoutente=="")
       {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
       } 


$titolo="Importazione programma scolastico nelle classi del docente";
$script="";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 
$maxcomp=20;



$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 


/*
 * Controllo che non ci siano già valutazioni inserite legate ad abilità e conoscenze
 * in tal caso non permetto l'importazione
 *  
*/ 

$query="select count(*) as numerovoti
	    from tbl_valutazioniabilcono,tbl_valutazioniintermedie
	    where tbl_valutazioniabilcono.idvalint = tbl_valutazioniintermedie.idvalint 
	    and tbl_valutazioniintermedie.iddocente = '$iddocente'";
	    
	    
// print (inspref($query));	    


           
$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
$nom=mysqli_fetch_array($ris);
if ($nom['numerovoti']>0)
{
   print("<center>Non è possibile importare la programmazione scolastica in quanto sono già presenti voti associati alla programmazione del docente!</center>");
}


else
{
	$query="select * from tbl_cattnosupp,tbl_classi where tbl_cattnosupp.idclasse=tbl_classi.idclasse and iddocente=$iddocente and tbl_cattnosupp.idalunno=0 ";
	$ris=mysqli_query($con,inspref($query));

	while($nom=mysqli_fetch_array($ris))
	{
		 $anno=$nom["anno"];
		 $idmateria=$nom["idmateria"];
		 
		 $idclasse=$nom["idclasse"];
		 
		 //
		 //  ELIMINO TUTTE LE ABILITA' DEL DOCENTE
		 //
			  
		 $query="select idcompetenza from tbl_competdoc where idmateria=$idmateria and idclasse=$idclasse";
		 $riscompdoc=mysqli_query($con,inspref($query));
		 while($nomcompdoc=mysqli_fetch_array($riscompdoc))
		 {
			$idcompdoc=$nomcompdoc["idcompetenza"];
			$query="delete from tbl_abildoc where idcompetenza=$idcompdoc";
			mysqli_query($con,inspref($query));
		}
	 //   print "<center><b>Eliminate abilit&aacute; e conoscenze per cattedra $idcattedra</b></center>";
		 //
		 //  Elimino tutte le competenze già inserite dal docente
		 //
		 
		 $query="delete from tbl_competdoc where idmateria=$idmateria and idclasse=$idclasse";
		 mysqli_query($con,inspref($query));
	  //  print "<center><b>Eliminate competenze per cattedra $idcattedra</b></center>";
		 
		 //
		 //  Importo dalla programmazione scolastica tutte le competenze e le abilità
		 //
		 
		 $query="select * from tbl_competscol where anno=$anno and idmateria=$idmateria";
		 $riscomp=mysqli_query($con,inspref($query));
		 while($nomcomp=mysqli_fetch_array($riscomp))
		 {
			$sintcomp=$nomcomp["sintcomp"];
			$numordcomp=$nomcomp["numeroordine"];
			$competenza=$nomcomp["competenza"];
			$idcompetenza=$nomcomp["idcompetenza"]; 
			$query="insert into tbl_competdoc(idmateria, idclasse, numeroordine, sintcomp, competenza) values ($idmateria, $idclasse, $numordcomp,'$sintcomp','$competenza')";    
			  mysqli_query($con,inspref($query));
			  // Rilevo l'id dell'inserimento effettuato
				print "<center><b>Importata competenza $sintcomp classe $idclasse materia $idmateria</b></center>";
			  $ultimoidcompetenza=mysqli_insert_id ($con);
			  
			  $query="select * from tbl_abilscol where idcompetenza=$idcompetenza";
			 
			  $risabil=mysqli_query($con,inspref($query));
			  while($nomabil=mysqli_fetch_array($risabil))
			  {
				$sintabil=$nomabil["sintabilcono"];
				$numordabil=$nomabil["numeroordine"]; 
				$abilcono=$nomabil["abilcono"];
				$obminimi=$nomabil["obminimi"];
				$abil_cono=$nomabil["abil_cono"];
					
					$query="insert into tbl_abildoc(idcompetenza, numeroordine, sintabilcono, abilcono, obminimi, abil_cono)
							  values ($ultimoidcompetenza,$numordabil,'$sintabil','$abilcono',$obminimi,'$abil_cono')";
					mysqli_query($con,inspref($query));
					print "<center><b>Importata abilit&aacute; e/o conoscenza $sintabil per classe $idclasse materia $idmateria</b></center>";
			}        
		 }
	}    
   
}
 

 

         
mysqli_close($con);
stampa_piede(""); 

