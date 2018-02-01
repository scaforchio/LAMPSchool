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

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$titolo = "Visualizza circolari";

$script = "";
$script .= "<script>
               
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
	                 $('#datainserimento').datepicker({ dateFormat: 'dd/mm/yy' });
	                 
	             });
</script>";
stampa_head($titolo, "", $script,"MSAPDT");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$visualizzabili = array("image/jpeg", "application/pdf", "image/pjpeg", "image/gif", "image/png");


print ("
		
		
		<p align='center'>
		<table align='center' border='1'>
		<tr class='prima'>
			<td><b>Circolare</b></td>
			<td><b>Data inizio</b></td>
			
			<td><b>Lett.</b></td>
			<td><b>Azione</b></td>
			<td><b>Firma per conferma</b></td>");

$dataoggi = date('Y-m-d');
$query = "select tbl_diffusionecircolari.idcircolare,tbl_circolari.iddocumento,ricevuta,tbl_circolari.descrizione,datainserimento, datalettura,dataconfermalettura,docsize,docnome,doctype
			  from tbl_diffusionecircolari,tbl_circolari,tbl_documenti
			  where tbl_diffusionecircolari.idcircolare=tbl_circolari.idcircolare
			  and tbl_circolari.iddocumento=tbl_documenti.iddocumento
			  and tbl_diffusionecircolari.idutente=$idutente
			  and tbl_circolari.datainserimento<='$dataoggi'
			  order by datainserimento desc";

$ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query) . mysqli_error($ris));
while ($nom = mysqli_fetch_array($ris))
{


    print "<tr><td>" . $nom['descrizione'] .
        "</td><td>" . data_italiana($nom['datainserimento']) .
        "</td><td>";
    if ($nom['datalettura'] != "0000-00-00")
    {
        print ("<img src='../immagini/apply_small.png'>");
    }
    print "</td><td><a href='actions.php?action=download&Id=" . $nom["iddocumento"] . "&Circ=" . $nom["idcircolare"] . "&Ute=" . $idutente . "' target='_blank'><img src='../immagini/download.jpg' alt='scarica'></a> ";

    if (in_array($nom["doctype"], $visualizzabili))
    {
        echo "<a href='actions.php?action=view&Id=" . $nom["iddocumento"] . "&Circ=" . $nom["idcircolare"] . "&Ute=" . $idutente . "' target='_blank'>";
        echo "<img src='../immagini/view.jpg' alt='visualizza'></a>  ";
    }
    print "</td>";
    print "<td>&nbsp;";
    if ($nom['dataconfermalettura'] == '0000-00-00' & $nom['ricevuta'] == 1) // & $nom['datalettura']!="0000-00-00")
    {
        print "<a href='firmacirc.php?idcircolare=" . $nom['idcircolare'] . "&idutente=$idutente'><img src='../immagini/stilo.png'></a>";
    }
    if ($nom['dataconfermalettura'] != '0000-00-00' & $nom['ricevuta'] == 1)
    {
        print data_italiana($nom['dataconfermalettura']);
    }
    print "</td>";
    print "</tr>";
}

print "</table>";


mysqli_close($con);
stampa_piede("");

function decod_dest($tipodest)
{
    if ($tipodest == 'O')
    {
        return "Tutti";
    }
    if ($tipodest == 'D')
    {
        return "Docenti";
    }
    if ($tipodest == 'A')
    {
        return "Alunni";
    }
    if ($tipodest == 'I')
    {
        return "Impiegati";
    }
}

