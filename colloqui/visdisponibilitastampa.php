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
 
 $iddocente=stringa_html('iddocente');
 
    if ($tipoutente=="")
       {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
       } 

    $titolo="Disponibilità ricevimento genitori";
    $script= "
	<script type='text/javascript'>
         <!--
            function printPage()
            {
               if (window.print)
                  window.print();
               else 
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');            }
         //-->
         </script>
";
    stampa_head($titolo,"",$script,"TDSPAM");
    print ('<body class="stampa" onLoad="printPage()">');

$idclasse=stringa_html("idclasse");

// scelta classe
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));



print("<center><font size='6'><b>Orario ricevimento genitori</b></font></center><br>");  
if ($idclasse!="")
   print("<center><font size='6'><b>Classe: ".decodifica_classe($idclasse,$con)."</b></font></center><br>");  
if ($idclasse!="")
	$query="select distinct cognome,nome,tbl_docenti.iddocente from tbl_cattnosupp,tbl_docenti
	        where tbl_cattnosupp.iddocente=tbl_docenti.iddocente
	        and tbl_cattnosupp.idclasse=$idclasse
	        and tbl_cattnosupp.iddocente!=1000000000
	        order by cognome,nome" ; 
else
	$query="select distinct cognome,nome,tbl_docenti.iddocente from tbl_cattnosupp,tbl_docenti
	        where tbl_cattnosupp.iddocente=tbl_docenti.iddocente
	        and tbl_cattnosupp.iddocente!=1000000000
	        order by cognome,nome" ; 	
   
	$ris=mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query)); 
	print "<table border=1 align=center><tr class='prima'><td><font size=4>Docente</font></td><td><font size=4>Ricevimento</font></td></tr>";
	while($nom=mysqli_fetch_array($ris))
	{
		 
		 
		 print "<tr><td><font size=4>".$nom['cognome']." ".$nom['nome']."</font>";
		 if ($idclasse!="")
		 {
			 //print "<br>";
			 $query="select idmateria from tbl_cattnosupp
			         where idclasse=$idclasse and iddocente=".$nom['iddocente'].
			         " and idalunno=0";
			// print inspref($query);        
			 $rismat=mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query));
			 print "<small>";
			 while ($recmat=mysqli_fetch_array($rismat))
			 {
				  print "<br>".decodifica_materia($recmat['idmateria'],$con)."  ";
			 }  
			 print "</small>";         
		 }
		 print "</td>";
		 print "<td><font size=4>";
		 $query="select giorno,inizio, fine, note from tbl_orericevimento,tbl_orario
		         where tbl_orericevimento.idorario=tbl_orario.idorario
		         and tbl_orericevimento.valido
		         and iddocente=".$nom['iddocente'];
		  
		 $ris2=mysqli_query($con,inspref($query)) or die ("Errore: ". inspref($query)); 
	    while($nom2=mysqli_fetch_array($ris2))
	    {        
		    print giornodanum($nom2['giorno'])." ".substr($nom2['inizio'],0,5)."-".substr($nom2["fine"],0,5)."  ".$nom2['note']."<br>";
		 }
	 }
		 
    print		 "</font></td>";
	  
		 print"</tr>";  
		 
		 
	  
	 
	print "</table>";
        
mysqli_close($con);
//stampa_piede("",false); 


