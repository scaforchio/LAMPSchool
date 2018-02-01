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
 @require_once("../lib/sms/php-send.php");
 // @require_once("php-send.php");
 
 
	
 // istruzioni per tornare alla pagina di login se non c'� una sessione valida
 ////session_start();
 $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
 
 
if ($tipoutente=="")
{
  header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
  die;
} 

$titolo="Invio SMS";
$script="<script>
function checkTutti() 
{
   with (document.listasms) 
   {
      for (var i=0; i < elements.length; i++) 
      {
         if (elements[i].type == 'checkbox')
            elements[i].checked = true;
      }
   }
}
function uncheckTutti() 
{
   with (document.listasms) 
   {
      for (var i=0; i < elements.length; i++) 
      {
         if (elements[i].type == 'checkbox')
            elements[i].checked = false;
      }
   }
} 
</script>
"; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con)); 



$rissms=verifica_numero_sms_residui($utentesms,$passsms);

$smsresidui=$rissms['classic_sms'];
$smsresidui=floor($smsresidui*($costosmsclassic/$costosmsplus));
if ($smsresidui>1000)
   $color='green';
else if ($smsresidui>500)
   $color='orange';
else
   $color='red';      
print "<center><b><font color='$color' size='4'>SMS residui: $smsresidui</font></center></b>";
/* foreach ($rissms as $rsms)
       print "<center>".$rsms."<br></center>"; */


print "<br><b><center>SMS per docenti</center></b><br>";
// print "<form action='seleinviosmsdoc.php' method='post' name='selesms'>";


print "<table align='center'>";



$dataoggi=date("Y-m-d");

print "<form name='listasms' action='sendsmsdoc.php' method='POST' id='listasms'><center>";
print "Testo: <input type='text' name='testosms' maxlength='155' size='155'>";
print "<br><center><input type='button' value='Seleziona tutti' onclick='checkTutti()'>
           <input type='button' value='Deseleziona tutti' onclick='uncheckTutti()'></center>";

if ($smsresidui>0) print "<input type='submit' value='Invia SMS'><br><br></center>";
print "<table align='center' border='1'>";
print "<tr class='prima'><td>Docente</td><td>Invio</td></tr>";

$query="select distinct tbl_docenti.iddocente,cognome,nome,telcel from tbl_cattnosupp,tbl_docenti 
	  where tbl_cattnosupp.iddocente=tbl_docenti.iddocente
	  order by cognome, nome";
$ris=mysqli_query($con,inspref($query));
while ($rec=mysqli_fetch_array($ris))
{
	 $iddocente=$rec['iddocente'];
	 
	 print "<tr class='oddeven'>";
	 print "<td>".$rec['cognome']." ".$rec['nome']."</td>";
	 $telcel=VerificaCellulare($rec['telcel']);
	 if ($telcel!="")
		 print "<td align='center'><input type='checkbox' name='sms$iddocente' id='sms$iddocente'>
				  </td>";
	 else
		 print "<td align='center'>Ins. cell.</td>";
		 
	 print "</tr>";
	
} 
 

print "</table>";
if ($smsresidui>0) print "<br><center><input type='submit' value='Invia SMS'></center></form>"; 


stampa_piede("");
mysqli_close($con);


         
/*
function VerificaCellulare($cell)
{
	// if (substr($cell,0,2)=="39")
	return $cell;
}
*/

