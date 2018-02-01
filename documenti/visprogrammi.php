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

$visualizzabili = array("image/jpeg", "application/pdf","image/pjpeg", "image/gif", "image/png");

$titolo="Visualizzazione programmi svolti";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

$maxcomp=20;

$cattedra = stringa_html('cattedra');
$idmateria="" ;
$idclasse="" ; 

print ("
   
   
   <p align='center'>
   <table align='center' border='1'>
   <tr class='prima'>
      <td><b>Cattedra</b></td>
      <td><b>Docenti</b></td>
      <td><b>File</b></td>
      <td><b>Azione</b></td>");
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
$query="select tbl_classi.idclasse, anno, sezione, specializzazione, 
        denominazione,tbl_materie.idmateria,tbl_docenti.iddocente,cognome,nome,
        iddocumento,docnome,docsize,doctype 
        from tbl_cattnosupp, tbl_classi, tbl_materie, tbl_docenti, tbl_documenti 
        where tbl_cattnosupp.idclasse=tbl_classi.idclasse 
        and tbl_cattnosupp.idmateria = tbl_materie.idmateria 
        and tbl_cattnosupp.iddocente = tbl_docenti.iddocente
        and tbl_cattnosupp.idclasse = tbl_documenti.idclasse
        and tbl_cattnosupp.idmateria = tbl_documenti.idmateria
        and tbl_cattnosupp.idalunno = 0
        and tbl_cattnosupp.iddocente<>1000000000
        and idtipodocumento=1000000002
        order by anno, sezione, specializzazione, denominazione,cognome,nome";
// print $query;          
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
    
    
    print "<tr><td>".$nom['anno']." ".$nom['sezione']." ".$nom['specializzazione']." ".$nom['denominazione']."</td>";
    print "<td>".$nom['cognome']." ".$nom['nome']."</td>";
    
  
        print ("<td>");
        echo  $nom["docnome"]." ";
        echo "<font size=1>(" . $nom["docsize"] . " bytes)</font></td><td> ";
        echo "<a href='actionsdocum.php?action=download&Id=" . $nom["iddocumento"] . "' target='_blank'><img src='../immagini/download.jpg' alt='scarica'></a> ";
       
        if(in_array($nom["doctype"], $visualizzabili)) 
        {
           echo "  <a href='actionsdocum.php?action=view&Id=" . $nom["iddocumento"]."' ";
           echo "target='_blank'><img src='../immagini/view.jpg' alt='visualizza'></a>  ";
		  }
	 print "</td></tr>";  
    
   
  }
  
print "</table>";
         
mysqli_close($con);
stampa_piede(""); 

