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

//Programma per la modifica dell'elenco delle tbl_classi

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

$titolo = "Modifica parametro";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';



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
                $('#valoredata').datepicker({ dateFormat: 'yy-mm-dd' });
			 });




         //-->
         </script>";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='paramedit.php'>ELENCO PARAMETRI</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("\n<h1> Connessione al server fallita </h1>");
    exit;
};


//Esecuzione query
$sql = "SELECT * FROM tbl_parametri WHERE idparametro=" . stringa_html('idpar');
if (!($ris = mysqli_query($con, inspref($sql))))
{
    print("\n<h1> Query fallita </h1>");
    exit;
}
else
{
    $dati = mysqli_fetch_array($ris);

    $valamm = array();
    $valamm = explode("|", $dati["valoriammessi"]);
    $numval = count($valamm);


    print "<form action='agg_par.php' method='POST'>";

    print "<input type='hidden' name='nomeparametro' value='" . $dati['parametro'] . "'>";
    print "<CENTER><table border='0'>";
    // print "<tr><td ALIGN='CENTER'><b>".$dati['parametro']."</b></td></tr>";
    print "<tr><td ALIGN='CENTER'><b>" . $dati['descrizione'] . "</b></td></tr>";
    if ($numval == 1)
    {
        if ($dati['parametro'] != "chiaveuniversale")
        {
            if ($dati['valoriammessi']=="Data")

                print "<tr><td ALIGN='CENTER'><br> <input type='text' name='valore' id='valoredata' size='10' maxlenght='10' value='" . $dati['valore'] . "'></td></tr>";

            else
               print "<tr><td ALIGN='CENTER'><br> <input type='text' name='valore' value='" . $dati['valore'] . "'></td></tr>";
        }
        else
        {
            print "<tr><td ALIGN='CENTER'><br> <input type='text' name='valore' value='Inserisci nuova password o annulla modifica!'></td></tr>";
        }
    }
    else
    {
        print "<tr><td align='center'><select name='valore'>";
        for ($i = 0; $i < $numval; $i++)
        {
            print "<option value='" . $valamm[$i] . "' ";
            if ($valamm[$i] == $dati['valore'])
            {
                print "selected";
            }
            print ">" . $valamm[$i] . "</option>";
        }
        print "</select></td></tr>";
    }
    print "<tr><td ALIGN='CENTER'><br> <input type='hidden' name='idpar' value='" . $dati['idparametro'] . "'></td></tr>";
    print "<tr><td ALIGN='CENTER'> <input type='submit' value='REGISTRA'></td></tr>";
    print "</table></CENTER></form>";
}
stampa_piede("");
mysqli_close($con);


