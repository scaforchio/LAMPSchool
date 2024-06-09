<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2015 Pietro Tamburrano
  Copyright (C) 2023 Michele Sacco - Flowopia Network [Rielaborazione sezione assemblee per adeguamento nuova UI]
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

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

// Pickup data odierna
$giorno = date('d');
$mese = date('m');
$anno = date('Y');
$dataodierna = date('Y-m-d');
$dataminass = aggiungi_giorni($dataodierna, $_SESSION['distanza_assemblee']);
$dataminassemblea = substr($dataminass, 8, 2) . "/" . substr($dataminass, 5, 2) . "/" . substr($dataminass, 2, 2);
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
stampa_head_new($titolo, "", $script, "L");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='assricgen.php?idclasse=$idclasse'>Assemblee di classe</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
//flag per controllare l'unicità dei rappresentanti, del presidente e del segretario
$f = stringa_html('f');
if (!isset($f))
{
    $f = 0;
}

if($idclasse == NULL){
    print("
    <div class='mb-3' style='max-width: 900px; margin: auto; position: relative;'>
        <div class='alert alert-danger text-center' role='alert'>
            <b>Errore di sistema!</b> Contattare il referente per il registro elettronico! <br>
            <i style='font-size: 10px;'>Value idclasse is equal to null. Check request and try again</i>
        </div>
    </div>
    ");
}else{
    // Estrapolo id alunni dei rapp. di classe da tbl
    $query = "SELECT * FROM tbl_classi WHERE idclasse=$idclasse";
    $ris = eseguiQuery($con, $query);
    $rdc = mysqli_fetch_array($ris);
    $rappdc1 = $rdc['rappresentante1'];
    $rappdc2 = $rdc['rappresentante2'];
}

if($idalunno == $rappdc1 || $idalunno == $rappdc2){
    // Form Richiesta Assemblea
    print("<form class='mb-3' style='max-width: 500px; margin: auto; position: relative;' name='invia' action='insass.php?idclasse=$idclasse' method='POST'>");

    // Data Richiesta
    print("
        <label for='datarichiesta' class='mt-1'>Data Richiesta</label>
        <input type='text' class='form-control' id='datarichiesta' name='datarichiesta' value='$giorno/$mese/$anno' disabled>");

    // Data Assemblea
    $dataassemblea = stringa_html('data');
    print("
        <label for='data' class='mt-1'>Data Assemblea</label>
        <input type='text' class='form-control' id='data' name='data' placeholder='dd/mm/aaaa' min='" . date('Y-m-d') . "' required>");

    // Ora dell'assemblea (Inizio - Fine)
    $ore = stringa_html('ora_inizio');
    print("
        <label for='oreass' class='mt-1'>Ora Svolgimento (Prima-Ultima)</label>
        <select class='form-select' name='oreass' id='oreass' ONCHANGE='abildisabdoc2()' required>");
        for ($i = 1; $i <= ($_SESSION['numeromassimoore'] - $_SESSION['numeromassimooreassemblea'] + 1); $i++)
        {
            for ($j = $i; $j <= $i + $_SESSION['numeromassimooreassemblea'] - 1; $j++)
            {
                $strore = "$i-$j";
                if ($ore == $strore)
                {
                    print "<option selected>$strore</option>";
                } else
                {
                    print "<option>$strore</option>";
                }
            }
        }
        print("</select>");

    $ora_inizio = substr($ore, 0, 1);
    $ora_fine = substr($ore, 2, 1);
    //controllo se le ore richieste sono 1 o 2
    $ore_richieste = 0;
    if ($ora_inizio == $ora_fine)
    {
        $ore_richieste = 1;
    }

    // Docente concedente
    $docenteconcedente1 = stringa_html('docenteconcedente1');
    $docenteconcedente2 = stringa_html('docenteconcedente2');
    //se viene richiesta l'assemblea di 1 ora, il docente concedente sarà uno solo
    $sqld = "SELECT DISTINCT tbl_docenti.cognome AS cognome, tbl_docenti.nome AS nome, tbl_docenti.iddocente AS iddocente_doc, tbl_cattnosupp.iddocente AS iddocente_cat, tbl_cattnosupp.idclasse AS idclasse 
                FROM tbl_docenti,tbl_cattnosupp 
                WHERE (tbl_docenti.iddocente=tbl_cattnosupp.iddocente) AND (tbl_cattnosupp.iddocente!=1000000000) AND (idclasse=$idclasse) ORDER BY cognome, nome";
    $resd = eseguiQuery($con, $sqld);
    if (!$resd)
    {
        print ("<br/> <br/> <br/> <h2>Impossibile visualizzare i dati </h2>");
    } else
    {
        print("
            <label for='doc1' class='mt-1'>Docente Concedente (Prima Ora)</label>
            <select class='form-select' id='doc1' name='docenteconcedente1' required>
        ");
        print("<option>");
        while ($datal = mysqli_fetch_array($resd))
        {
            print("<option value='" .$datal['iddocente_doc'] ."'");
            if ($docenteconcedente1 == $datal['iddocente_doc'])
            {
                print " selected";
            }
            print(">" .$datal['cognome'] ." " .$datal['nome'] ."</option>");
        }
    }
    print "</select>";
        //se le ore sono due, viene visualizzata la select del secondo docente concedente
        if ($_SESSION['numeromassimooreassemblea'] == 2)
        {
            $resd = eseguiQuery($con, $sqld);
            if (!$resd)
            {
                print ("<br/> <br/> <br/> <h2>Impossibile visualizzare i dati </h2>");
            } else
            {
                print("
                <label for='doc2' class='mt-1'>Docente Concedente (Seconda Ora)</label>
                <select class='form-select' id='doc2' name='docenteconcedente2' required>
                ");
                print "<option>";
                while ($datal = mysqli_fetch_array($resd))
                {
                    print("<option value='" .$datal['iddocente_doc'] ."'");
                    if ($docenteconcedente2 == $datal['iddocente_doc'])
                    {
                        print " selected";
                    }
                    print(">" .$datal['cognome'] ." " .$datal['nome'] ."</option>");
                }
                print "</select>";
            }
        }

    // Rappresentante Richiedente
    print("
        <label for='rapp1' class='mt-1'>Rappresentante Richiedente</label>
        <input type='text' id='rapp1' class='form-control' value='" . decodifica_alunno($idalunno, $con) . "' disabled>
        <input type='hidden' name='rappresentante1' value='$idalunno'>
    ");

    // Ordine del Giorno
    print("
        <label for='odg' class='mt-1'>Ordine del Giorno</label>
        <textarea class='form-control' id='odg' name='odg' rows=10>Ordine del giorno:\n1) ...\n2) ...\n3) ...</textarea>
        <input type='hidden' name='odgdef' value='Ordine del giorno:\n1) ...\n2) ...\n3) ...'>
    ");

    // Firma Rappresentante
    print("
        <label for='rapp1' class='mt-1'>Firma Rappresentante</label>
        <input type='text' id='rapp1' class='form-control' value='" . decodifica_alunno($idalunno, $con) . "' disabled>
        <input type='hidden' name='rappresentante1' value='$idalunno'>
    ");
    $rap1 = stringa_html('rappresentante1');

    print "<div class='text-center'><input class='btn btn-outline-success mt-3' role='button' type=submit value='Richiedi assemblea'></div>";
    print "</form>";
}else{
    print("
        <div class='mb-3' style='max-width: 900px; margin: auto; position: relative;'>
            <div class='alert alert-danger' role='alert'>
            ATTENZIONE! La tua utenza non risulta abilitata come Rappresentante di Classe! <a href='./assricgen.php'><- TORNA INDIETRO</a>
            </div>
        </div>
    ");
}

?>

<?php
stampa_piede_new("");
mysqli_close($con);

