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


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");


// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idutente = $_SESSION["idutente"];
$idalunno = stringa_html('idalunno');

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$titolo = "Forzatura timbratura";

$script = "<script>



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
					initStatus: 'Scegliere una data', isRTL: false};
				$.datepicker.setDefaults($.datepicker.regional['it']);
			});

			$(document).ready(function(){
				 $('#datatimbratura').datepicker({ dateFormat: 'dd/mm/yy' });
				 $('#oratimbratura').datetimepicker({
						formatTime: 'H:i',
						format: 'H:i',
						step: 5,
						datepicker:false
					});
			 });

		//	$(document).ready(function(){

		//	});

</script>
";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
/*
$rissms=array();
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
*/

$timbratureforzate = array();


$querytimbrature = "SELECT count(*) AS numero,idalunno FROM tbl_timbrature WHERE forzata AND tbl_timbrature.tipotimbratura='I' GROUP BY idalunno ORDER BY idalunno";
$ristim = mysqli_query($con, inspref($querytimbrature));
while ($rectim = mysqli_fetch_array($ristim))
{
    $timbratureforzate[$rectim['idalunno']] = $rectim['numero'];
}


$query = "SELECT * FROM tbl_alunni, tbl_classi
         WHERE tbl_alunni.idclasse=tbl_classi.idclasse
         AND tbl_alunni.idclasse<>0
         ORDER BY cognome,nome,anno, sezione, specializzazione";

$ris = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));

print "<form name='selealu' action='selealunnitimbraturaforzata.php' method='post'>";
print "<table align='center'>";
print "<tr><td>Alunno</td>";
print "<td>";
print "<select name='idalunno' ONCHANGE='selealu.submit();'><option value=''>&nbsp;</option>";
while ($rec = mysqli_fetch_array($ris))
{
    $numtimbratureforzate = isset($timbratureforzate[$rec['idalunno']]) ? $timbratureforzate[$rec['idalunno']] : 0;

    if ($idalunno == $rec['idalunno'])
    {
        $sele = " selected";
    }
    else
    {
        $sele = "";
    }
    print ("<option value='" . $rec['idalunno'] . "'$sele>" . $rec['cognome'] . " " . $rec['nome'] . " (" . $rec['datanascita'] . ") - " . $rec['anno'] . " " . $rec['sezione'] . " " . $rec['specializzazione'] . " - Forzature: $numtimbratureforzate</option>");
}
print "
 </select>
 </td>

 </tr>

 </table></form><br><br>";

if ($idalunno != "")
{
    print ("
       <form method='post' action='insalunnitimbrature.php' name='listadistr'>
       <input type='hidden' name='idalunno' value='$idalunno'>
       <center>
       Tipo: <select name='tipotimbratura'><option>I<option>R<option>U</select>
       Data: <input type='text' name='datatimbratura' value='" . data_italiana(date('Y-m-d')) . "' id='datatimbratura' size='10'>
       Ora: <input type='text' name='oratimbratura' value='" . date('H:i') . "' id='oratimbratura' size='5'>
       <br><br><center><input type='submit' value='Registra timbrature'></center><br>
       </form>");
}
mysqli_close($con);
stampa_piede("");


