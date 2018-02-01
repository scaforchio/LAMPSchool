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

    $titolo="Orario lezioni";
    $script="";
    stampa_head($titolo,"",$script,"SDMAP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 


$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

print "<form action='insorario.php' method='post'>";
print "<table border=1 align='center'>";
print "<tr class='prima'><td align='right'>Ora:</td>";
for ($h=1;$h<=$numeromassimoore;$h++)
{
	print "<td align='center'>$h</td>";
}
print "</tr>";	

for ($g=1;$g<=$giornilezsett;$g++)
{
	print "<tr><td>".giornodanum($g)."</td>";
	for ($h=1;$h<=$numeromassimoore;$h++)
	{
		$query="select * from tbl_orario where giorno=$g and ora=$h and valido";
		
		if ($ris=mysqli_query($con,inspref($query)))
		{
			$rec=mysqli_fetch_array($ris);
			$valini=substr($rec['inizio'],0,5);
			$valfin=substr($rec['fine'],0,5);
		}
		else
		{
			$valini="";
			$valfin="";
		}  
		$ora="_".$g."_".$h; 
		print "<td><input type='time' name='inizio$ora' maxlength='5' size=5 value='$valini'><br><input type='time'  maxlength='5' size=5 name='fine$ora' value='$valfin'></td>";
		
	}
	print "</tr>";
}
print "</table><br>";
print "<center><input type='submit' value='Registra'></center>";
print "</form>";

stampa_piede("");
mysqli_close($con);
 


