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
@require_once("../lib/sms/php-send.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idutente = $_SESSION["idutente"];
$idcircolare = stringa_html("idcircolare");
// $idclasse=stringa_html("idclasse");
$destinatari = 'SA';

$anno = stringa_html('anno');
$sezione = stringa_html('sezione');
$specializzazione = stringa_html('specializzazione');


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$titolo = "Selezione alunni con presenza forzata";

$script = "<script>
function checkTutti() 
{
   with (document.listadistr) 
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
   with (document.listadistr) 
   {
      for (var i=0; i < elements.length; i++) 
      {
         if (elements[i].type == 'checkbox')
            elements[i].checked = false;
      }
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
					initStatus: 'Scegliere una data', isRTL: false};
				$.datepicker.setDefaults($.datepicker.regional['it']);
			});


			$(document).ready(function(){
				 $('#datainizio').datepicker({ dateFormat: 'dd/mm/yy' });
			 });

			 $(document).ready(function(){
				 $('#datafine').datepicker({ dateFormat: 'dd/mm/yy' });
			 });
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

print "<center>Gli alunni selezionati risulteranno presenti anche in assenza di timbratura (gite, stage, attività esterne, ecc.)</center><br>";
print "<form method='post' action='selealunnipresenza.php' name='selealu'>";

print "<table align='center'>";

// SELEZIONE SU ANNO
print "   <tr>
      <td width='50%'><b>Anno</b></td>
      <td width='50%'>
      <SELECT ID='cl' NAME='anno' ONCHANGE='selealu.submit()'><option value=''>&nbsp;</option>";

// Riempimento combo box tbl_classi
$query = "SELECT DISTINCT anno FROM tbl_classi ORDER BY anno";
$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["anno"]);
    print "'";
    if ($anno == $nom["anno"])
    {
        print " selected";
    }
    print ">";
    print ($nom["anno"]);

}

print("
      </SELECT>
      </td></tr>");

// SELEZIONE SU SEZIONE
print "   <tr>
      <td width='50%'><b>Sezione</b></td>
      <td width='50%'>
      <SELECT ID='cl' NAME='sezione' ONCHANGE='selealu.submit()'><option value=''>&nbsp;</option>";
$query = "SELECT DISTINCT sezione FROM tbl_classi ORDER BY sezione";
$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["sezione"]);
    print "'";
    if ($sezione == $nom["sezione"])
    {
        print " selected";
    }
    print ">";
    print ($nom["sezione"]);

}

print("
      </SELECT>
      </td></tr>");

// SELEZIONE SU SPECIALIZZAZIONE
print "   <tr>
      <td width='50%'><b>$plesso_specializzazione</b></td>
      <td width='50%'>
      <SELECT NAME='specializzazione' ONCHANGE='selealu.submit()'><option value=''>&nbsp;</option>";
$query = "SELECT DISTINCT specializzazione FROM tbl_classi ORDER BY specializzazione";
$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["specializzazione"]);
    print "'";
    if ($specializzazione == $nom["specializzazione"])
    {
        print " selected";
    }
    print ">";
    print ($nom["specializzazione"]);

}

print("
      </SELECT>
      </td></tr>");


print "</table></form>";


// VISUALIZZAZIONE ELENCO DOCENTI

$sele = "";
if ($anno != "")
{
    $sele .= " and anno='$anno' ";
}
if ($sezione != "")
{
    $sele .= " and sezione='$sezione' ";
}
if ($specializzazione != "")
{
    $sele .= " and specializzazione='$specializzazione' ";
}


print ("
   <form method='post' action='insalunnipresenza.php' name='listadistr'>
   <center>
   Motivo: <input type='text' name='motivo' maxlength='200' size='80'><br>
   Datainizio: <input type='text' name='datainizio' value='" . data_italiana(date('Y-m-d')) . "' id='datainizio'>
   Datafine: <input type='text' name='datafine' value='" . data_italiana(date('Y-m-d')) . "' id='datafine'>
   <br><br><center><input type='button' value='Seleziona tutti' onclick='checkTutti()'>
   <input type='button' value='Deseleziona tutti' onclick='uncheckTutti()'></center>
   <br><br><center><input type='submit' value='Inserisci presenza'></center><br><br>
   <p align='center'>
   
   <table align='center' border='1'>
   <tr class='prima'><td>Cognome Nome</td><td>Classe</td><td>Presenza</td></tr>");
//if ($idclasse=='')
//    $query="select * from tbl_docenti order by cognome,nome";
//else
$query = "select distinct tbl_alunni.idalunno,cognome, nome, datanascita, anno, sezione, specializzazione
            from tbl_alunni,tbl_classi
            where tbl_alunni.idclasse=tbl_classi.idclasse
            $sele
            order by anno,sezione,specializzazione,cognome,nome";
$ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));
while ($rec = mysqli_fetch_array($ris))
{

    print "<tr>";
    print "     <td>" . $rec['cognome'] . " " . $rec['nome']. " (" . data_italiana($rec['datanascita']) . ")</td>";
    print "<td>" . $rec['anno'] . " " . $rec['sezione'] . " " . $rec['specializzazione'] . "</td>";

    print "<td align='center'><input type='checkbox' name='pres" . $rec['idalunno'] . "'></td>";


    print "</tr>";
}
print "</table><br><center><input type='submit' value='Registra presenze'></center></p></form>";

mysqli_close($con);
stampa_piede("");


