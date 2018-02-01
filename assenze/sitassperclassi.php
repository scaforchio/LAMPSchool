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

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Situazione assenze per classe";
$script = "<script>
            function printPage()
            {
               if (window.print)
                  window.print();
               else
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');
            }
         </script>
         <script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         
         ";
$script .= "
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
	                 $('#datainizio').datepicker({ dateFormat: 'dd/mm/yy' });
	                 
	             });
$(document).ready(function(){
	                 $('#datafine').datepicker({ dateFormat: 'dd/mm/yy' });
	                 
	             });
	             </script>";
stampa_head($titolo, "", $script, "MSPD");


stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$nome = stringa_html('cl');
$but = stringa_html('visass');


$meseanno = stringa_html('mese');  // In effetti contiene sia il mese che l'anno
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


// Divido il mese dall'anno
$mese = substr($meseanno, 0, 2);
$anno = substr($meseanno, 5, 4);

//$giornosettimana=giorno_settimana($anno."-".$mese."-".$giorno);

$datainizio = stringa_html("datainizio");


// $dataoggi = date("d/m/Y");
$dataoggi = date("Y-m-d");


if ($datainizio == "")
{
    $datainizio = data_italiana($datainiziolezioni);
}
if ($datafine == "")
{
    $datafine = $dataoggi;
}

if ($mese == '')
{
    $mese = date('m');
}
if ($anno == '')
{
    $anno = date('Y');
}


print ("
   <form method='post' action='sitassperclassi.php' name='assenze'>
   
   <p align='center'>
   <table align='center' border=1>
   <tr>
      <td width='50%'><center><b>Classe</b></center></td>
      <td>Alunni</td><td>Assenti</td><td>Perc.</td></tr>");


$query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY specializzazione, sezione, anno";

$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    $classe=$nom['idclasse'];

    $query="select count(*) as numeroalunni from tbl_alunni where idclasse=$classe";
    $risal=mysqli_query($con,inspref($query));
    $rec=mysqli_fetch_array($risal);
    $numeroalunni=$rec['numeroalunni'];


    $query="select count(*) as numeroassenti from tbl_assenze,tbl_alunni
            where tbl_assenze.idalunno=tbl_alunni.idalunno
            and data='$dataoggi'
            and idclasse=$classe";

    $risas=mysqli_query($con,inspref($query));
    $rec=mysqli_fetch_array($risas);
    $numeroassenti=$rec['numeroassenti'];

    print "<tr>";

    print "<td>";


    print ($nom["anno"]);
    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
    print "</td>";
    print "<td>$numeroalunni</td>";
    print "<td>$numeroassenti</td>";
    print "<td>".round($numeroassenti/$numeroalunni*100)." %</td>";
    print "</tr>";
}

echo '</table>';

    print "<center><img src='../immagini/stampa.png' onClick='printPage();'</center>";
    // print"<br/><center><a href=javascript:Popup('staasse.php?classe=$idclasse&datainizio=$datainizio&datafine=$datafine')><img src='../immagini/stampa.png'></a></center><br/><br/>";

// fine if

mysqli_close($con);
stampa_piede("");
