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

/*Programma per la visualizzazione dell'elenco delle tbl_classi.*/

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Inserimento nuova classe";
$script = "<SCRIPT>
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
	                 $('#data').datepicker({ dateFormat: 'dd/mm/yy' });

	             });
	             </SCRIPT>";
stampa_head($titolo, "", $script, "MSP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_ritcla.php'>ELENCO ENTRATE POSTICIPATE CLASSI</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("\n<h1> Connessione al server fallita </h1>");
    exit;
};

//Connessione al database
$DB = true;
if (!$DB)
{
    print("\n<h1> Connessione al database fallita </h1>");
    exit;
};

//Esecuzione query
print "<form name='form1' action='reg_ritcla.php' method='POST'>";
print "<CENTER><table border ='0'>";
print "<tr> <td>";


//TABELLA SEZIONE nome=tbl_sezioni
print "<tr><td   ALIGN='CENTER'> Classe </td>";
$q1 = "SELECT * FROM tbl_classi ORDER BY anno, sezione, specializzazione";
if (!($reply = mysqli_query($con, inspref($q1))))
{
    print "<td>Query fallita nelle tbl_sezioni</td>";
}
else
{
    print "<td> <SELECT NAME='idclasse'>";

    //Controlla se esiste le tbl_sezioni e stampa l'elenco altrimenti da l'errore
    if (mysqli_num_rows($reply) > 0)
    {
        while ($d1 = mysqli_fetch_array($reply))
        {
            print "<option  value='" . $d1['idclasse'] . "'> " . $d1['anno'] . " " . $d1['sezione'] . " " . $d1['specializzazione'] . " ";
        }
    }
    else
    {
        print "<option  value=0> Nessuna classe trovata";
    }
    print "</SELECT>";
}
print    "</td></tr>";


print "<tr><td>Data</td><td><input type='text' id='data'  class='datepicker' size='8' maxlength='10' name='data'></td></tr>";

print "<tr><td   ALIGN='CENTER'> Ora entrata </td>";
$q1 = "SELECT DISTINCT inizio FROM tbl_orario
     WHERE inizio > (SELECT min(inizio) FROM tbl_orario where valido) and valido ORDER BY inizio";
if (!($reply = mysqli_query($con, inspref($q1))))
{
    print "<td>Query fallita nelle tbl_sezioni</td>";
}
else
{
    print "<td> <SELECT NAME='ora'>";

    //Controlla se esiste le tbl_sezioni e stampa l'elenco altrimenti da l'errore
    if (mysqli_num_rows($reply) > 0)
    {
        while ($d1 = mysqli_fetch_array($reply))
        {
            print "<option> " . substr($d1['inizio'], 0, 5) . " ";
        }
    }

    print "</SELECT>";
}
print    "</td></tr>";

print "<tr><td COLSPAN='2'><br/><CENTER>";
print "<input type='submit' name='registra' value='Registra'> </CENTER>";
print "</CENTER></td></TR><TR><TD COLSPAN='2'>&nbsp;</TD></TR>";
print "</form>";

print "</table></CENTER>";

stampa_piede("");
mysqli_close($con);


