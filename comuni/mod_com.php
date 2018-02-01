<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
GNU Affero General Public License come pubblicata 
dalla Free Software Foundation; sia la versione 3, 
sia (a vostra scelta) ogni versione successiva.

Questo programma è distribuito nella speranza che sia utile 
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di 
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE. 
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/

@include ("../php-ini".$_SESSION['suffisso'].".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente=="")
{
	header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']);
	die;
}

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
if (!$con)
	print("<h2>connessione errata</h2>");
$DB=true;

if (!$DB)
	print("<h2>database non selezionato</h2>");
$sql="SELECT * FROM tbl_comuni WHERE idcomune=". stringa_html('idcom');

$Res=mysqli_query($con,inspref($sql));
if (!($Res=mysqli_query($con,inspref($sql))))
	print("Query fallita");
($dato=mysqli_fetch_array($Res));
print" <html>
			<head>
			<title> Trovato> </title>
			</head>
			<body>
				<form  name='form5' action='aggiorna.php' method='POST'>
				<table border=0 width='100%'>
		<tr>
		   <td align ='center' ><strong><font size='+1'>VISUALIZZAZIONE COMUNE</font></strong></td>
		</tr>
		</table cellspacing='15'> <br/><br/>
			     <center> <table>
		    
		               	<tr> <td> <input type = 'hidden' size='20' name='idtbl_comuni' value='".$dato['idcomune']."'> </td> </tr>
		               	<tr> <td><b> Denominazione<font color='#ff0000'>*</b> </td> <td> <input type = 'text' size='20' name='denominazioni' value='".$dato['denominazione']."'> </td> </tr>
		               	<tr> <td><b> CAP <font color='#ff0000'>*</b></td> <td>  <input type = 'text' size='20' name='cap'  value='".$dato['cap']."'> </td></tr>
    			       	<tr> <td> <b>Codistat <font color='#ff0000'>* </b> </td> <td> <input type = 'text' size='20' name='codice'  value='".$dato['codistat']."' maxlenght='4'> </td></tr>
		              	<tr> <td><b> Provincia <font color='#ff0000'>*</b></td> <td> <input type = 'text' size='20' name='prov'  value='".$dato['provincia']."'> </td></tr>
        				<tr> <td><b> Sigla Provincia <font color='#ff0000'>*</b> </td> <td> <input type = 'text' size='20' name='sigla'  value='".$dato['siglaprovincia']."' maxlenght='2'> </td></tr>
				        <tr> <td><b> Regione <font color='#ff0000'>*</b> </td> <td> <input type = 'text' size='20' name='reg'  value='".$dato['regione']."'> </td></tr> 
						<tr> <td> <b> Stato Estero</b> </td> <td> <input type = 'text' size='20' name='stato'  value='".$dato['statoestero']."'> </td></tr>	
						<table>
						<tr> <td>
						<center> <input type='submit' value='Modifica'> </center>
						<br/>
							 </form> </td> </tr>
							 </table>
							 </table>
							 <table>
							 <tr> <td>
						     <center> <form name='form9' action='lis_com.php' method='POST'>
		                     <input type='submit' value='<<Indietro'>
							 <br/><br/> 
							 <center> <font color='#ff0000'>* </font> (Campi obbligatori)
							 </form> </center>		
						</td> </tr>
						</table> </center>
			</body>
		</html>";
mysqli_close($con);





