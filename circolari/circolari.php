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
$iddocente = $_SESSION["idutente"];
$sostegno = $_SESSION["sostegno"];

$solocertificati = false;
/*$tipo=stringa_html("tipo");

if ($tipo=='pei')
   $solocertificati=true;
*/
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$destinatari = stringa_html('destinatari');
$titolo = "Gestione circolari";

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
stampa_head($titolo, "", $script,"MSPA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$visualizzabili = array("image/jpeg", "application/pdf", "image/pjpeg", "image/gif", "image/png");


//
//  SELEZIONE ALUNNO
//


print ("
   <form method='post' action='circolari.php' name='documenti'>
   
   <p align='center'>
   <table align='center'>
   <tr>
      <td width='50%'><input type='hidden' name='tipo' value='$tipo'><p align='center'><b>Destinatari</b></p></td>
      <td width='50%'>
      <SELECT NAME='destinatari' ONCHANGE='documenti.submit()'>");
if ($destinatari == '')
{
    print "<option value='' selected>&nbsp;</option>";
}
else
{
    print "<option value=''>&nbsp;</option>";
}
//if ($destinatari=='O')      
//   print "<option value='O' selected>Tutti</option>";
//else
//   print "<option value='O'>Tutti</option>";     
if ($destinatari == 'A')
{
    print "<option value='A' selected>Tutti gli alunni</option>";
}
else
{
    print "<option value='A'>Tutti gli alunni</option>";
}
if ($destinatari == 'D')
{
    print "<option value='D' selected>Tutti i docenti</option>";
}
else
{
    print "<option value='D'>Tutti i docenti</option>";
}
if ($destinatari == 'I')
{
    print "<option value='I' selected>Tutti gli impiegati</option>";
}
else
{
    print "<option value='I'>Tutti gli impiegati</option>";
}
if ($destinatari == 'SA')
{
    print "<option value='SA' selected>Selezione alunni</option>";
}
else
{
    print "<option value='SA'>Selezione alunni</option>";
}
if ($destinatari == 'SD')
{
    print "<option value='SD' selected>Selezione docenti</option>";
}
else
{
    print "<option value='SD'>Selezione docenti</option>";
}
if ($destinatari == 'SI')
{
    print "<option value='SI' selected>Selezione impiegati</option>";
}
else
{
    print "<option value='SI'>Selezione impiegati</option>";
}

print "   
	   </SELECT>
      </td></tr>";
print "</table></form>";

if ($destinatari != "")
{
    $dest = " and destinatari='$destinatari' ";
}
else
{
    $dest = " ";
}


print ("
		
		
		<p align='center'>
		<table align='center' border='1'>
		<tr class='prima'>
			<td><b>Circolare</b></td>
			<td><b>Destinatari</b></td>
			<td><b>Ricevuta</b></td>
			<td><b>Data inserimento</b></td>
			<td><b>File caricato</b></td>
			<td><b>Azione</b></td>");

$query = "select idcircolare,tbl_circolari.iddocumento,ricevuta,tbl_circolari.descrizione,destinatari,datainserimento, docsize,docnome,doctype
			  from tbl_circolari,tbl_documenti
			  where tbl_circolari.iddocumento=tbl_documenti.iddocumento
			  $dest
			  order by datainserimento desc";

$ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query) . mysqli_error($ris));
while ($nom = mysqli_fetch_array($ris))
{


    print "<tr><td>" . $nom['descrizione'] .
        "</td><td>" . decod_dest($nom['destinatari']) .
        "</td><td>" . (($nom['ricevuta'] == 1) ? 'Sì' : 'No') .
        "</td><td>" . data_italiana($nom['datainserimento']) .
        "</td><td>" . $nom["docnome"] .
        "<font size=1> (" . $nom["docsize"] . ") bytes</font></td>" .
        "<td><a href='actions.php?action=download&Id=" . $nom["iddocumento"] . "' target='_blank'><img src='../immagini/download.jpg' alt='scarica'></a> ";

    if (in_array($nom["doctype"], $visualizzabili))
    {
        echo "<a href='actions.php?action=view&Id=" . $nom["iddocumento"] . "' ";
        echo "target='_blank'><img src='../immagini/view.jpg' alt='visualizza'></a>  ";
    }
    // if (cancellabile($idcircolare,$con))
    echo " <a href='mod_cir.php?idcircolare=" . $nom["idcircolare"] . "&destinatari=$destinatari'><img src='../immagini/edit.png' alt='modifica descrizione'></a>";
    echo " <a href='canccircolare.php?idcircolare=" . $nom["idcircolare"] . "&destinatari=$destinatari'><img src='../immagini/delete.png' alt='cancella'></a>";
    if ($nom['destinatari'] == 'SD' | $nom['destinatari'] == 'D')
    {
        echo " <a href='seledocenti.php?idcircolare=" . $nom["idcircolare"] . "&tipo=" . $nom['destinatari'] . "'><img src='../immagini/tabella.png' alt='lista distribuzione'></a>";
    }
    if ($nom['destinatari'] == 'SI' | $nom['destinatari'] == 'I')
    {
        echo " <a href='seleimpiegati.php?idcircolare=" . $nom["idcircolare"] . "&tipo=" . $nom['destinatari'] . "'><img src='../immagini/tabella.png' alt='lista distribuzione'></a>";
    }
    if ($nom['destinatari'] == 'SA' | $nom['destinatari'] == 'A')
    {
        echo " <a href='selealunni.php?idcircolare=" . $nom["idcircolare"] . "&tipo=" . $nom['destinatari'] . "'><img src='../immagini/tabella.png' alt='lista distribuzione'></a>";
    }

}
print "</td></tr>";


print "</table>";

//
//  AGGIUNTA CIRCOLARE
//

print "<br><br>";
print "<fieldset><legend>AGGIUNGI CIRCOLARE</legend>";
print ("
		
		<form action='inscircolare.php' method='POST' enctype='multipart/form-data'>

		<p align='center'>
		<table align='center' border='1'>
		<tr class='prima'>
			<td><b>Circolare</b></td>
			<td><b>Destinatari</b></td>
			<td><b>Ricevuta</b></td>
			<td><b>Data inizio</b></td>
			<td><b>File da caricare</b></td>
			");

print "<tr>";
print "<td><input type='text' maxlength='100' size='30' name='descrizione'></td>";
print "<td><SELECT NAME='destinatari'>
                
                <option value='A'>Tutti gli alunni</option> 
                <option value='D'>Tutti i docenti</option>
                <option value='I'>Tutti gli impiegati</option>
                <option value='SA'>Selezione alunni</option> 
                <option value='SD'>Selezione docenti</option>
                <option value='SI'>Selezione impiegati</option>";
print "   
	   </SELECT>";

print "</td>";
print "<td><SELECT NAME='ricevuta'>
                <option value='0'>No</option>      
                <option value='1'>S&igrave;</option>     
                </SELECT></td><td>";
$dataoggi = date('d/m/Y');
$datascad = date('d/m/Y', strtotime('+1 months'));
print "<input type ='text' id='datainserimento' name='datainserimento' size='10' maxlength='10' value='$dataoggi'>";
print "</td>";
print ("<td><center><input type=file name='filedocumento' value='Carica file'>  </td></tr>");


print "</table>";

print "<center><br><input type='submit' value='Invia file selezionato'></center>";
print "</form>";
print "</fieldset>";


mysqli_close($con);
stampa_piede("");

function decod_dest($tipodest)
{
    //if ($tipodest=='O')
    //   return "Tutti";
    if ($tipodest == 'D')
    {
        return "Tutti i docenti";
    }
    if ($tipodest == 'A')
    {
        return "Tutti gli alunni";
    }
    if ($tipodest == 'I')
    {
        return "Tutti gli impiegati";
    }
    if ($tipodest == 'SD')
    {
        return "Selezione docenti";
    }
    if ($tipodest == 'SA')
    {
        return "Selezione alunni";
    }
    if ($tipodest == 'SI')
    {
        return "Selezione impiegati";
    }
}

