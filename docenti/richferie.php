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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
//require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();
// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Richiesta astensione dal lavoro";
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
				 $('#datainizio').datepicker({ dateFormat: 'dd/mm/yy', minDate:new Date() });
			 });
                         $(document).ready(function(){
				 $('#datafine').datepicker({ dateFormat: 'dd/mm/yy' });
			 });
                         
                         $(document).ready(function(){
				 $('#giornopermessobreve').datepicker({ dateFormat: 'dd/mm/yy', minDate:new Date() });
			 });
                         $(document).ready(function(){
					$('#orainiziopermessobreve').datetimepicker({
						formatTime: 'H:i',
						format: 'H:i',
						step: 10,
						datepicker:false
					});
			});
                        $(document).ready(function(){
					$('#orafinepermessobreve').datetimepicker({
						formatTime: 'H:i',
						format: 'H:i',
						step: 10,
						datepicker:false
					});
			});


			
		</script>";
stampa_head($titolo, "", $script, "MSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$nominativo= estrai_dati_docente($_SESSION['idutente'], $con);
print "
        <style>
			
			.header {
				margin: 20px 0 20px 0;
			}
			.cenetered {
				text-align: center;
			}
			.narrow {
				width: 4em;
			}
			.wide {
				width: 40em;
			}
			.date {
				width: 10em;
			}
			label {
				display: block;
			}
			.hidden {
				display: none;
			}
			input[type=\'radio\'] {
				margin-right: 10px;
			}
			.subform {
				margin-left: 30px;
			}
			.attachment {
				margin-top: 20px;
			}
			.address {
				margin-top: 20px;
			}
			.footer {
				margin-top: 40px;
			}
			.left {
				float: left;
			}
			.right {
				float: right;
				text-align: right;
			}
		</style>
          
		<form action='prepmailrichferie.php' method='post' name='richferie'>
			<div>
				Il/La sottoscritto/a
				<input type='text' value='$nominativo' disabled><input type='hidden' name='nominativo' value='$nominativo'>,
			</div>
			<div>
				in servizio presso codesto istituto in qualit&agrave; di DOCENTE a tempo
				<select name='tempo'>
                                        <option>indeterminato</option>
					<option>determinato</option>
					
				</select>
				,
			</div>

			<div class='header cenetered'><b>CHIEDE</b></div>

			<div>
				alla S.V. di assentarsi per n.
				<input class='narrow' type='number' id='numerogiorni' name='numerogiorni' min='1' max='30'> gg dal
				<input class='date' type='text' id='datainizio' name='datainizio'> al 
				<input class='date' type='text' id='datafine' name='datafine'> per:
			</div>
				
			<label><input type='radio' name='reason' id='reason' value='0' required><b>Ferie</b> (ai sensi dell\'art. 13 CCNL)</label>

			<label><input type='radio' name='reason' id='reason' value='1'><b>Permesso retribuito</b> (ai sensi dell'art. 15 CCNL) per:</label>
			<div id='subform1' class='hidden subform'>
				<select name='motivopermesso'>
					<option>Motivi personali/familiari</option>
					<option>Concorsi ed esami</option>
					<option>Lutto</option>
					<option>Diritto allo studio</option>
					<option>Matrimonio</option>
				</select>
			</div>

			<label><input type='radio' name='reason' id='reason' value='2'><b>Malattia</b> (ai sensi dell'art. 17 CCNL) per:</label>
			<div id='subform2' class='hidden subform'>
				<select name='motivomalattia'>
                                        <option>Generica</option>
					<option>Ricovero ospedaliero</option>
					<option>Malattia del bambino</option>
					<option>Visita specialistica e/o esami diagnostici</option>
				</select>
			</div>

			<label><input type='radio' name='reason' id='reason' value='3'><b>Maternit&agrave;</b> per:</label>
			<div id='subform3' class='hidden subform'>
				<select name='motivomaternita'>
					<option>Interdizione</option>
					<option>Puerperio</option>
					<option>Astensione obbligatoria</option>
					<option>Astensione facoltativa</option>
				</select>
			</div>

			<label><input type='radio' name='reason' id='reason' value='4'><b>Aspettativa</b> (ai sensi dell'art. 18 CCNL) per:</label>
			<div id='subform4' class='hidden subform'>
				<select name='motivoaspettativa'>
					<option>Motivi familiari</option>
					<option>Motivi di lavoro</option>
					<option>Motivi personali</option>
					<option>Motivi di studio</option>
				</select>
			</div>

			<label><input type='radio' name='reason' id='reason' value='5'><b>Legge 104/92</b></label>
			<div id='subform5' class='hidden subform'>
				Giorni gi&agrave; fruiti nel mese:
				<select name='giorniprecedenti104'>
                                        <option>0</option>
					<option>1</option>
					<option>2</option>
					<option>3</option>
				</select>
			</div>

			<label><input type='radio' name='reason' id='reason' value='6'><b>Altro caso previsto dalla normativa vigente</b></label>
			<div id='subform6' class='hidden subform'>
				<input type='text' id='altromotivo' name='altromotivo'>
			</div>

			<label><input type='radio' name='reason' id='reason' value='7'><b>Permesso breve</b></label>
			<div id='subform7' class='hidden subform'>
				Per il giorno <input class='date' type='text' id='giornopermessobreve' name='giornopermessobreve'> dalle ore <input type='text' class='narrow' id='orainiziopermessobreve' name='orainiziopermessobreve'> alle ore <input type='text' class='narrow' id='orafinepermessobreve' name='orafinepermessobreve'><br>
per un totale di ore <input type='number' class='narrow' id='orepermessobreve' name='orepermessobreve' min='1' max='10'> orario di servizio nella giornata pari a ore <input type='number' class='narrow' id='oreserviziopermessobreve' name='oreserviziopermessobreve' min='1' max='10'>
			</div>

			<div class='address'>
				Durante il periodo di assenza sar&agrave; domiciliato in <input type='text' name='comunedomicilio'></input>
				alla via <input type='text' name='indirizzodomicilio'> n <input type='text' class='narrow' name='numerodomicilio'>
				tel. <input type='text' name='telefonorecapito'>
				<div>
					Si allega (da consegnare in segreteria):
					<input class='wide' type='text' name='allegati'>
				</div>
			</div>

                       <center><br><input type='submit' value='Prepara richiesta'><br>
			
		</form>
                <script>
			/*function dateFormat(d) {
				var day = d.getDate();
				var month = d.getMonth() + 1;
				var year = d.getFullYear();
				return day + '/' + month + '/' + year;
			}
                        */

                    

			$(document).ready(function() {
				$('input:radio[name=\\'reason\']').prop('checked', false);
				$('#date').text(dateFormat(new Date()));
			});

			$('input:radio[name=\'reason\']').change(function() {
				$('.subform').each(function() {
					$(this).hide();
				});
				$('#subform' + $(this).val()).show();
                                for (counter = 0; counter < richferie.reason.length; counter++){
                                    if (richferie.reason[counter].checked){
                                        selezione = richferie.reason[counter].value;    
                                    }
                                }
                                
                                console.log(selezione);
                                if (selezione!=7)
                                {
                                   document.getElementById('datainizio').setAttribute('required',true);
                                   document.getElementById('datafine').setAttribute('required',true);
                                   document.getElementById('numerogiorni').setAttribute('required',true);
                                   document.getElementById('orainiziopermessobreve').removeAttribute('required');
                                   document.getElementById('orafinepermessobreve').removeAttribute('required');
                                   document.getElementById('orepermessobreve').removeAttribute('required');
                                   document.getElementById('giornopermessobreve').removeAttribute('required');
                                   document.getElementById('oreserviziopermessobreve').removeAttribute('required');
                                   
                                   
                                   
                                }
                                else
                                {
                                   document.getElementById('datainizio').removeAttribute('required');
                                   document.getElementById('datafine').removeAttribute('required');
                                   document.getElementById('numerogiorni').removeAttribute('required');
                                   document.getElementById('orainiziopermessobreve').setAttribute('required',true);
                                   document.getElementById('orafinepermessobreve').setAttribute('required',true);
                                   document.getElementById('orepermessobreve').setAttribute('required',true);
                                   document.getElementById('giornopermessobreve').setAttribute('required',true);
                                   document.getElementById('oreserviziopermessobreve').setAttribute('required',true);
                                }
                                if (selezione==6)
                                    document.getElementById('altromotivo').setAttribute('required',true);
                                else
                                   document.getElementById('altromotivo').removeAttribute('required');
			});
		</script>
		
		";





mysqli_close($con);
stampa_piede("");
