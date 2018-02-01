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
 // @require_once("../lib/sms/php-send.php");
	
 // istruzioni per tornare alla pagina di login se non c'� una sessione valida
 ////session_start();
 $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
 $idutente=$_SESSION["idutente"];
 
 
 if ($tipoutente=="")
 {
   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
   die;
 } 

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 
$titolo="Visualizza stato SMS inviati";

$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");

$selezione = stringa_html('selezione');

print "<form name='vissms' action='visualizzasms.php' method='post'>";
print "<center>Selezione:"; 
print "<select name='selezione'  ONCHANGE='vissms.submit()'>";
//if ($selezione=="ass") $seleass=' selected';else $seleass='';
//if ($selezione=="rit") $selerit=' selected';else $selerit='';
if ($selezione=="alu") $selealu=' selected';else $selealu='';
if ($selezione=="doc") $seledoc=' selected';else $seledoc='';
if ($selezione=="tut" | $selezione=="") $seletut=' selected';else $seletut='';
//print "<option value='ass'$seleass>Assenze</option>
//       <option value='rit'$selemat>Ritardi</option>
print "<option value='alu'$selealu>Alunni</option>
       <option value='doc'$seledoc>Docenti</option>
       <option value='tut'$seletut>Tutti</option>";
print "</select></center>";
print "</form>";


if ($selezione=="tut" | $selezione=="alu" | $selezione=="")
{
	$query="select * from tbl_sms,tbl_testisms
			  where tbl_sms.idtestosms=tbl_testisms.idtestosms
			  and tipo in ('ass','rit','alu')
			  order by dataora desc";

	$ris=mysqli_query($con,inspref($query)) or die ("Errore:".inspref($query));


	if (mysqli_num_rows($ris)>0)
	{
		print "<br><table align='center' border=1>";

		print "<tr class='prima'>
					 <td>Tipo SMS</td>
					 <td>Invio</td>
					 <td>Alunno</td>
					 <td>Testo</td>
					 <td>Numero</td>
					 <td>Stato</td>
				 </tr>";



		while ($rec=mysqli_fetch_array($ris))
		{
			print "<tr>";
			print "<td>".$rec['tipo']."</td>";
			print "<td>".$rec['dataora']."</td>";
			print "<td>".decodifica_alunno($rec['iddestinatario'],$con)."</td>";
			print "<td>".$rec['testo']."</td>";
			print "<td>".$rec['celldestinatario']."</td>";
			print "<td>".$rec['esito']."</td>";
			print "</tr>";
			
		}

		print "</table>";
	}
}

if ($selezione=="tut" | $selezione=="doc" | $selezione=="")
{
	$query="select * from tbl_sms,tbl_testisms
			  where tbl_sms.idtestosms=tbl_testisms.idtestosms
			  and tipo in ('doc')
			  order by dataora desc";


	$ris=mysqli_query($con,inspref($query)) or die ("Errore:".inspref($query));

	if (mysqli_num_rows($ris)>0)
	{

		print "<br><table align='center' border=1>";

		print "<tr class='prima'>
					 <td>Tipo SMS</td>
					 <td>Invio</td>
					 <td>Docente</td>
					 <td>Testo</td>
					 <td>Numero</td>
					 <td>Stato</td>
				 </tr>";



		while ($rec=mysqli_fetch_array($ris))
		{
			print "<tr>";
			print "<td>".$rec['tipo']."</td>";
			print "<td>".$rec['dataora']."</td>";
			print "<td>".estrai_dati_docente($rec['iddestinatario'],$con)."</td>";
			print "<td>".$rec['testo']."</td>";
			print "<td>".$rec['celldestinatario']."</td>";
			print "<td>".$rec['esito']."</td>";
			print "</tr>";
			
		}
		print "</table>";
	}
	
}
	
mysqli_close($con);
stampa_piede(""); 



