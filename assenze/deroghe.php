<?php

session_start();

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
$idalunno = stringa_html("idalunno");


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$titolo = "Inserimento deroghe assenze";

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
stampa_head($titolo, "", $script, "MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
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
$query = "select * from tbl_alunni, tbl_classi
         where tbl_alunni.idclasse=tbl_classi.idclasse
         order by cognome,nome,anno, sezione, specializzazione";
if ($tipoutente == 'D')
    $query = "SELECT * FROM tbl_alunni LEFT JOIN tbl_classi
         ON tbl_alunni.idclasse=tbl_classi.idclasse
         WHERE tbl_alunni.idclasse IN (select distinct tbl_classi.idclasse from tbl_classi
                                       where idcoordinatore=" . $_SESSION['idutente'] . ")
         ORDER BY cognome,nome,anno, sezione, specializzazione
         ";
$ris = eseguiQuery($con, $query);

print "<form name='selealu' action='deroghe.php' method='post'>";
print "<table align='center'>";
print "<tr><td>Alunno</td>";
print "<td>";
print "<select name='idalunno' ONCHANGE='selealu.submit();'><option value=''>&nbsp;</option>";
while ($rec = mysqli_fetch_array($ris))
{
    if ($idalunno == $rec['idalunno'])
        $sele = " selected";
    else
        $sele = "";
    print ("<option value='" . $rec['idalunno'] . "'$sele>" . $rec['cognome'] . " " . $rec['nome'] . " (" . $rec['datanascita'] . ") - " . $rec['anno'] . " " . $rec['sezione'] . " " . $rec['specializzazione'] . "</option>");
}
print "
 </select>
 </td>

 </tr>

 </table></form><br><br>";


// VISUALIZZAZIONE ELENCO DOCENTI

if ($idalunno != "")
{

    print ("
   <form method='post' action='insderoga.php' name='listadistr'>
   <input type='hidden' name='idalunno' value='$idalunno'>
   <center>
   Motivo: <input type='text' name='motivo' maxlength='200' size='80'><br>
   Datainizio: <input type='text' name='datainizio' value='" . data_italiana(date('Y-m-d')) . "' id='datainizio'>
   Datafine: <input type='text' name='datafine' value='" . data_italiana(date('Y-m-d')) . "' id='datafine'>
   <div>
   <br><br>
   <b><center>Selezionare il numero di ore nella tabella sotto se nel periodo va considerato<br>un numero di ore inferiore per alcuni giorni della settimana<br>o se va escluso qualche giorno della settimana dalla deroga!</b></center>
   <br> <table border='1'>
      <tr class='prima'>
        <th>Giorno</th>
        <th>Ore</th>
      </tr>
      <tr>");

    // Array con numero ore di lezione per ogni giorno della settimana
    $ris = eseguiQuery($con,"SELECT giorno, COUNT(*) AS ore FROM tbl_orario WHERE valido = 1 GROUP BY giorno ORDER BY giorno");
    $orelez = array();
    while ($res = mysqli_fetch_assoc($ris))
    {
        array_push($orelez, $res["ore"] - 1);
    }
    $giorni = array("Luned&igrave;", "Marted&igrave;", "Mercoled&igrave;", "Gioved&igrave;", "Venerd&igrave;", "Sabato", "Domenica");

    // Maschera di inserimento dati
    for ($i = 0; $i < $giornilezsett; $i++)
    {
        //  print("<input type='hidden' name='ore" . ($i + 1) . "' value='0'>");
        print("<tr>");
        print("<td><label><input type='checkbox' name='giorno" . ($i + 1) . "' class='giornocheckbox' data-idgiorno='" . ($i + 1) . "' checked>$giorni[$i]</label></td>");
        print("<td><select class='oreselect' name='ore" . ($i + 1) . "' data-idgiorno='" . ($i + 1) . "' data-maxore='$orelez[$i]' style='width: 100%' disabled>");
        for ($j = 0; $j <= $orelez[$i]; $j++)
        {
            print("<option value='" . ($j) . "'>" . ($j) . "</option>");
        }

        print("</select></td>");
        print("</tr>");
    }


    print("</tr>
    </table>
   </div>
   <br>
   <br><br><center><input type='submit' value='Inserisci deroga'></center><br><br>
   </form>");
}
mysqli_close($con);
stampa_piede("");
