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

//
//    VISUALIZZAZIONE DELLE ASSEMBLEE DI CLASSE PER I GENITORI
//	  E
//	  RICHIESTA DI ASSEMBLEE DI CLASSE PER GLI ALUNNI 
//


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

//  istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

//prendo la data odierna
$giorno = date('d');
$mese = date('m');
$anno = date('Y');
$dataodierna=date('Y-m-d');
$dataminass= aggiungi_giorni($dataodierna, $distanza_assemblee);
$dataminassemblea=substr($dataminass,8,2)."/".substr($dataminass,5,2)."/".substr($dataminass,2,2);
// print $dataminassemblea;
$titolo = "Richiesta assemblea di classe";
//script utilizzato per la data dell'assemblea
$script = "<SCRIPT>
           function abildisabdoc2()
           {
               var oreass=document.getElementById('oreass').value;
               if (oreass.substr(0,1)==oreass.substr(2,1))
               {
                   document.getElementById('doc2').disabled=true;
               }
               else
               {
                   document.getElementById('doc2').disabled=false;
               }
           }
jQuery(function($){
	$.datepicker.regional['it'] = {
		clearText: 'Svuota', clearStatus: 'Annulla',
		closeText: 'Chiudi', closeStatus: 'Chiudere senza modificare',
		prevText: '&#x3c;Prec', prevStatus: 'Mese precedente',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: 'Mostra l\'anno precedente',
		nextText: 'Succ&#x3e;', nextStatus: 'Mese successivo',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: 'Mostra l\'anno successivo',
		currentText: 'Oggi', currentStatus: 'Mese corrente',
		monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
		'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'],
		monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu',
		'Lug','Ago','Set','Ott','Nov','Dic'],
		monthStatus: 'Seleziona un altro mese', yearStatus: 'Seleziona un altro anno',
		weekHeader: 'Sm', weekStatus: 'Settimana dell\'anno',
		dayNames: ['Domenica','Luned&#236','Marted&#236','Mercoled&#236','Gioved&#236','Venerd&#236','Sabato'],
		dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'],
		dayNamesMin: ['Do','Lu','Ma','Me','Gio','Ve','Sa'],
		dayStatus: 'Usa DD come primo giorno della settimana', dateStatus: '\'Seleziona\' D, M d',
		dateFormat: 'dd/mm/yy', firstDay: 1,
		minDate: $dataminassemblea, 
		beforeShowDay: function(date)
		{
			var day = date.getDay();
			return [day != 0,''];
		},
		initStatus: 'Scegliere una data', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['it']);
});
$(document).ready(function(){
	                 $('#data').datepicker({ dateFormat: 'dd/mm/yy' });

	             });
	             </SCRIPT>";
$idalunno = $_SESSION['idstudente'];
$idclasse = stringa_html('idclasse');
stampa_head($titolo, "", $script,"L");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='assricgen.php?idclasse=$idclasse'>Assemblee di classe</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));
//flag per controllare l'unicità dei rappresentanti, del presidente e del segretario
$f = stringa_html('f');
if(!isset($f))
{
	$f = 0;
}
// print "<form name='form' action='ricgen.php?idclasse=$idclasse&f=$f' method='POST'>";
print "<form name='invia' action='insass.php?idclasse=$idclasse' method='POST'>";
print "<CENTER><table border ='0' cellpadding='5'>";
//DATA RICHIESTA
print "<tr>
		<td><b>Data richiesta</b></td>
		<td><input DISABLED name ='datarichiesta' type='text' value='$giorno/$mese/$anno'></td>
	   </tr>";
	   
//DATA ASSEMBLEA
$dataassemblea = stringa_html('data');
print "<tr>
		<td><b>Data assemblea</b></td>";
		
			print" <td><input type='text' id='data' class='datepicker' size='8' maxlength='10' name='data' required></td>";
		
print" </tr>";
	   
//ORA INIZIO-FINE
$ore = stringa_html('ora_inizio');
print "<tr>
		<td><b>Ore assemblea (prima-ultima):</b></td>
		<td><select name='oreass' id='oreass' ONCHANGE='abildisabdoc2()' required>";
for ($i = 1; $i <= ($numeromassimoore-$numeromassimooreassemblea+1); $i++)
{
	for ($j = $i; $j <= $i+$numeromassimooreassemblea-1; $j++)
	{
		$strore = "$i-$j";
		if($ore == $strore)
		{
			print "<option selected>$strore</option>";
		}
		else
		{
			print "<option>$strore</option>";
		}
	}
}
print "</select></td></tr>";

$ora_inizio = substr($ore,0,1);
$ora_fine = substr($ore,2,1);

//controllo se le ore richieste sono 1 o 2
$ore_richieste = 0;
if($ora_inizio==$ora_fine)
{
	$ore_richieste = 1;
}

$docenteconcedente1 = stringa_html('docenteconcedente1');
$docenteconcedente2 = stringa_html('docenteconcedente2');
//$materia1 = stringa_html('materia1');
//$materia2 = stringa_html('materia2');
//se viene richiesta l'assemblea di 1 ora, il docente concedente sarà uno solo
print "<tr><td>";
$sqld = "SELECT DISTINCT tbl_docenti.cognome AS cognome, tbl_docenti.nome AS nome, tbl_docenti.iddocente AS iddocente_doc, tbl_cattnosupp.iddocente AS iddocente_cat, tbl_cattnosupp.idclasse AS idclasse 
			 FROM tbl_docenti,tbl_cattnosupp 
			 WHERE (tbl_docenti.iddocente=tbl_cattnosupp.iddocente) AND (tbl_cattnosupp.iddocente!=1000000000) AND (idclasse=$idclasse) ORDER BY cognome, nome";
print "<b>Docente concedente (prima ora)</b>";
print "</td><td>";	
$resd = mysqli_query($con, inspref($sqld));
if (!$resd)
{
	print ("<br/> <br/> <br/> <h2>Impossibile visualizzare i dati </h2>");
}
else
{
	print ("<select name='docenteconcedente1' required>");
	print "<option>";
	while ($datal = mysqli_fetch_array($resd))
	{
		print("<option value='");
		print($datal['iddocente_doc']."'");
		if($docenteconcedente1==$datal['iddocente_doc'])
		{
			print " selected";
		}
		print(">");
		print($datal['cognome']);
		print("&nbsp;");
		print($datal['nome']);
		print("</option>");
	}
}
print "</select>";
print "</td></tr>";
//se le ore sono due, viene visualizzata la select del secondo docente concedente
if ($numeromassimooreassemblea==2)
{
print("<tr><td>");

	print "<b>Docente concedente (seconda ora)</b>";
	print "</td><td>";
	$resd = mysqli_query($con, inspref($sqld));
	if (!$resd)
	{
		print ("<br/> <br/> <br/> <h2>Impossibile visualizzare i dati </h2>");
	}
	else
	{
		print ("<select name='docenteconcedente2' id='doc2' disabled>");
		print "<option>";
		while ($datal = mysqli_fetch_array($resd))
		{
			print("<option value='");
			print($datal['iddocente_doc']."'");
			if($docenteconcedente2==$datal['iddocente_doc'])
			{
				print " selected";
			}
			print(">");
			print($datal['cognome']);
			print("&nbsp;");
			print($datal['nome']);
			print("</option>");
		}
		print "</select>";
	}
        print "</td></tr>";
}
print "<tr><td><b>Rappresentante</b></td><td><input type='text' value='".decodifica_alunno($idalunno, $con)."' disabled><input type='hidden' name='rappresentante1' value='$idalunno'></td></tr>";
print "<tr><td colspan=2><center><b>Ordine del giorno</b><br><textarea cols=40 rows=10 name='odg' required>Ordine del giorno:\n1) ...\n2) ...\n3) ...</textarea></center></td></tr>";

print "<tr><td><b>Rappresentante</b></td><td><input type='text' value='".decodifica_alunno($idalunno, $con)."' disabled><input type='hidden' name='rappresentante1' value='$idalunno'></td></tr>";
$rap1 = stringa_html('rappresentante1');
print "</table>";
print "	<p align='center'><input type=submit value='Richiedi assemblea'>";
print "</form>";

//invio dati
/*
print "<p align='center'><textarea cols=80 rows=10 name='odg' WRAP='PHYSICAL'>Elencare i punti all'ordine del giorno\n1) Punto1\n2) Punto2\n3) Punto3...</textarea></p>";
print " <p align='center'><input type=hidden value='". $idclasse ."' name='idclasse'></p>";
print " <p align='center'><input type=hidden value='$anno-$mese-$giorno' name='datarichiesta'></p>";
print " <p align='center'><input type=hidden value='". $dataassemblea ."' name='dataassemblea'></p>";
print " <p align='center'><input type=hidden value='". $ora_inizio ."' name='orainizio'></p>";
print " <p align='center'><input type=hidden value='". $ora_fine ."' name='orafine'></p>";
print " <p align='center'><input type=hidden value='". $docenteconcedente1 ."' name='docenteconcedente1'></p>";
print " <p align='center'><input type=hidden value='". $docenteconcedente2 ."' name='docenteconcedente2'></p>";

print " <p align='center'><input type=hidden value='". $rap1 ."' name='rappresentante1'></p>";

print "	<p align='center'><input type=submit value='Richiedi assemblea'>";
print "</form>";

*/
	   
stampa_piede("");
mysqli_close($con);
