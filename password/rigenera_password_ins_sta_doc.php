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

$iddoc = stringa_html('iddoc');

$titolo="Rigenerazione password docenti";
$script="";
/*
	<script type='text/javascript'>
         <!--
            function printPage()
            {
               if (window.print)
                  window.print()
               else 
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');            }
         //-->
         </script>";
*/
stampa_head($titolo,"",$script,"SMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
    

$annoscolastico=$annoscol."/".($annoscol+1);



$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 


if ($iddoc=="")
{
	print "<center><b>Elenco password per i docenti</b></center><br/><br/>"; 
	print "<form target='_blank' name='stampa' action='../docenti/stampa_pass_doc.php' method='POST'>";
	print "<table align='center' border='1'><tr><td><b>Docente</b></td><td><b>Utente</b></td><td><b>Password</b></td></tr>";


	$query="select * from tbl_docenti where iddocente>1000000000 order by cognome,nome,datanascita";
	$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));


	$numpass=0;
							 
							 
							 
					
	while($val=mysqli_fetch_array($ris)) 
	{
			$numpass++;
			print (" 
					 <tr>
						 <td>".$val['cognome']." ".$val['nome']." (".data_italiana($val['datanascita']).")"."</td>");
			
			$iddocente=$val['iddocente'];
			$utente="doc".($iddocente-1000000000);
			$pass=creapassword();
			print ("<td>$utente</td><td>$pass<input type='hidden' name='iddoc".$numpass."' value='$iddocente'> 
							 <input type='hidden' name='utdoc".$numpass."' value='$utente'> 
							 <input type='hidden' name='pwdoc".$numpass."' value='$pass'></td></tr>");
			$qupd ="update tbl_utenti set password=md5('".md5($pass)."') where idutente=$iddocente";
			$resupd=mysqli_query($con,inspref($qupd)) or die ("Errore nella query: ". mysqli_error($con));
			
							 
	}
}   
else
{
	print "<center><b>Elenco password per i docenti</b></center><br/><br/>"; 
	print "<form target='_blank' name='stampa' action='../docenti/stampa_pass_doc.php' method='POST'>";
	print "<table align='center' border='1'><tr><td><b>Docente</b></td><td><b>Utente</b></td><td><b>Password</b></td></tr>";


	$query="select * from tbl_docenti where iddocente=$iddoc";
	$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));


	$numpass=0;
							 
							 
							 
					
	while($val=mysqli_fetch_array($ris)) 
	{
			$numpass++;
			print (" 
					 <tr>
						 <td>".$val['cognome']." ".$val['nome']." (".data_italiana($val['datanascita']).")"."</td>");
			
			$iddocente=$val['iddocente'];
			$utente="doc".($iddocente-1000000000);
			$pass=creapassword();
			print ("<td>$utente</td><td>$pass<input type='hidden' name='iddoc".$numpass."' value='$iddocente'> 
							 <input type='hidden' name='utdoc".$numpass."' value='$utente'> 
							 <input type='hidden' name='pwdoc".$numpass."' value='$pass'></td></tr>");
			$qupd ="update tbl_utenti set password=md5('".md5($pass)."') where idutente=$iddocente";
			$resupd=mysqli_query($con,inspref($qupd)) or die ("Errore nella query: ". mysqli_error($con));
			
							 
	}
}     
print("</table>");

print "<input type='hidden' name='numpass' value='$numpass'> 
       <center><input type='submit' value='STAMPA COMUNICAZIONI'></center>
       </form>";

mysqli_close($con);
stampa_piede(""); 




