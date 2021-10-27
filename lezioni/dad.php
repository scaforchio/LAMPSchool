<?php

require_once '../lib/req_apertura_sessione.php';

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

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idutente = $_SESSION["idutente"];
$idalunno = stringa_html("idalunno");


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$titolo = "Inserimento giornate didattica a distanza";

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
				 $('#datainizio').datepicker({ dateFormat: 'dd/mm/yy' });
         $('#datafine').datepicker({ dateFormat: 'dd/mm/yy' });
         
    $('.giornocheckbox').change(function() {
            // Controllo su checkbox: se è selezionata imposta il valore del select al massimo, altrimenti a 0
            var elem = $('.oreselect[data-idgiorno=' + $(this).data('idgiorno') + ']');
            $(elem).val(this.checked ? 0: 0);

            // Triggera onchange per aggiornare l'input hidden
            // $(elem).change();
            $(elem).prop('disabled', this.checked);
         });

         // Cambiamento select
         $('.oreselect').change(function() {
            // Imposta l'input hidden con il valore della select
            $('input[name=ore' + $(this).data('idgiorno') + ']').val($(this).val());

            // Se è selezionato il massimo, disabilita la checkbox
            if ($(this).data('maxore') == $(this).val()) {
              $('.giornocheckbox[data-idgiorno=' + $(this).data('idgiorno') + ']').prop('checked', true);
              $(this).prop('disabled', true);
            }
         });



/*
         $('.giornocheckbox').change(function() {
            // Controllo su checkbox: se è selezionata imposta il valore del select al massimo, altrimenti a 0
            var elem = $('.oreselect[data-idgiorno=' + $(this).data('idgiorno') + ']');
            $(elem).val(this.checked ? $(elem).data('maxore') : 0);

            // Triggera onchange per aggiornare l'input hidden
            $(elem).change();
            $(elem).prop('disabled', this.checked);
         });

         // Cambiamento select
         $('.oreselect').change(function() {
            // Imposta l'input hidden con il valore della select
            $('input[name=ore' + $(this).data('idgiorno') + ']').val($(this).val());

            // Se è selezionato il massimo, disabilita la checkbox
            if ($(this).data('maxore') == $(this).val()) {
              $('.giornocheckbox[data-idgiorno=' + $(this).data('idgiorno') + ']').prop('checked', true);
              $(this).prop('disabled', true);
            }
         }); */
			 });
</script>
";
stampa_head($titolo, "", $script, "MASPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$query = "select * from tbl_classi
         
         order by anno, specializzazione, sezione";
if ($tipoutente == 'D')
    $query = "select * from tbl_classi
         
         order by anno, specializzazione, sezione
         WHERE idcoordinatore=" . $_SESSION['idutente'] ;
$ris = eseguiQuery($con, $query);

print "<form action='insdad.php' method='post'>";
print "<table align='center'>";
print "<tr class='prima'><td>Periodo</td><td>Classi</td></tr>";
print "<td>";
print "<center>
   
   Datainizio:<br> <input type='text' name='datainizio' value='" . data_italiana(date('Y-m-d')) . "' id='datainizio'>
   <br> Datafine:<br> <input type='text' name='datafine' value='" . data_italiana(date('Y-m-d')) . "' id='datafine'>
";   
print "</td>";
print "<td>";
print("<select multiple name='idclassi[]' size='8'>");
      //  print("<option>");
      //  print("<option value='ALL'>TUTTE");
       
        while ($nom = mysqli_fetch_array($ris))
        {
            print "<option value=";
            print ($nom["idclasse"]);

            
            print ">";
            print ($nom["anno"]." ".$nom["sezione"]." ".$nom["specializzazione"]);
        }
        print("</select>");
print "</table><br><br>";

print "<br><br><center><input type='submit' value='Inserisci giornate d.a.d.'></center><br><br>
   </form>";

mysqli_close($con);
stampa_piede("");
