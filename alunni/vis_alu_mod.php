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

//Visualizzazione e modifica di un alunno
//parametri di ingresso: codice dell'alunno
//parametri di uscita: codice della classe
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login 

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$titolo = "Modifica dati alunno";
$script = "<script type='text/javascript'>
          <!--
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
				 $('#datacambioclasse').datepicker({ dateFormat: 'dd/mm/yy' });
			});

			//-->
            </script>";
stampa_head($titolo, "", $script, "MASP");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Modifica dati alunno", "", "$nome_scuola", "$comune_scuola");
//connessione al server
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<h1> Connessione al server fallita </h1>");
}

//connessione al database
$DB = true;
if (!$DB)
{
    print("<h1> Connessione al database fallita </h1>");
}

$c = stringa_html('idal');
$strcogn = stringa_html('strcogn');
$strnome = stringa_html('strnome');


$sql = "SELECT * FROM tbl_alunni WHERE idalunno='$c'";
$sqlpass = "SELECT dischpwd FROM tbl_utenti WHERE idutente='$c'";
//esecuzione query
$res = mysqli_query($con, inspref($sql));
if (!$res)
{
    print ("<br/> <br/> <br/> <h2> Impossibile visualizzare i dati </h2>");
}
else
{
    if ($dato = mysqli_fetch_array($res))
    {
        $respass=mysqli_query($con, inspref($sqlpass));
        $recpass=mysqli_fetch_array($respass);
        $bloccopassword=$recpass['dischpwd'];
        print("<form action='vis_alu_mod_ok.php' method='POST'>");
        print ("<div style=\"text-align: center;\">");
        print ("<table align='center'>");
        //	if ($err!=1)
        //	 {
        $idal = $dato['idalunno'];
        $cognome = $dato['cognome'];
        $nome = $dato['nome'];
        $codfiscale = $dato['codfiscale'];
        $certificato = $dato['certificato'];
        $gg = substr($dato['datanascita'], 8, 2);
        $mm = substr($dato['datanascita'], 5, 2);
        $aa = substr($dato['datanascita'], 0, 4);
        $idcomn = $dato['idcomnasc'];
        $indirizzo = $dato['indirizzo'];
        $idcomr = $dato['idcomres'];
        $sidi = $dato['codmeccanografico'];
        $tel = $dato['telefono'];
        $cel = $dato['telcel'];
        $mail = $dato['email'];
        $note = $dato['note'];
        $autentrata = $dato['autentrata'];
        $autuscita = $dato['autuscita'];
        $firmapropria = $dato['firmapropria'];
        $autorizzazioni = $dato['autorizzazioni'];
        $idcla = $dato['idclasse'];
        $numeroregistro = $dato['numeroregistro'];
        $provenienza = $dato['provenienza'];
        $titoloammissione = $dato['titoloammissione'];
        $sequenzaiscrizione = $dato['sequenzaiscrizione'];

        //	 }
        print ("\n \t <tr> <td align='left'>  <input type='hidden' name='idal' value='$idal'> </td> </tr>");
        print   ("\n \t <tr> <td><i> Cognome <span style=\"color: #cc0000; \"> <b> * </b> </span></i> </td> ");
        print("<td align='left'> <input type='text' value='$cognome' name='cognome' size='30' maxlength='30'> </td> </tr>");
        print   ("<tr> <td><i>Nome <span style=\"color: #cc0000; \"> <b> * </b> </span></i> </td> <td align='left'> <input type='text' value='$nome' name='nome' size='30' maxlength='30'> </td> </tr>");
        print   ("<tr> <td><i>Codice fiscale <span style=\"color: #cc0000; \"> <b> * </b> </span></i> </td> <td align='left'> <input type='text' value='$codfiscale' name='codfiscale' size='16' maxlength='16'> </td> </tr>");

        print ("<tr> <td> <i> Data di nascita <span style=\"color: #cc0000; \"> <b> * </b> </span></i> </td> <td align='left'> <input type='text' value='$gg' name='gg' size='1'  maxlength='2'> / <input type='text' value='$mm' name='mm' size='1'  maxlength='2'> / <input type='text' value='$aa' name='aa' size='3'  maxlength='4'> (gg/mm/aaaa) </td> </tr>");
        print   ("<tr> <td> <i>Comune o stato estero di nascita<span style=\"color: #cc0000; \"></span></i> </td> <td align='left'> <select name='idcomn'>");
        $sqla = "SELECT * FROM tbl_comuni WHERE statoestero='N' ORDER BY denominazione ";
        $resa = mysqli_query($con, inspref($sqla)) or die ("Errore:" . inspref($sqla));
        if (!$resa)
        {
            print ("<br/> <br/> <br/> <h2>a Impossibile visualizzare i dati </h2>");
        }
        else
        {
            print ("<option value='9999'>");
            print ("<optgroup label='COMUNI ITALIANI'>");

            while ($datal = mysqli_fetch_array($resa))
            {

                if ($idcomn == ($datal['idcomune']))
                {
                    print("<option value='" . $datal['idcomune'] . "' selected> " . $datal['denominazione'] . "");
                }
                else
                {
                    print("<option value='" . $datal['idcomune'] . "'> " . $datal['denominazione'] . "");
                }

            }
            print ("</optgroup>");
        }

        $sqlb = "SELECT * FROM tbl_comuni WHERE statoestero='S' ORDER BY denominazione";
        $resb = mysqli_query($con, inspref($sqlb));
        if (!$resb)
        {
            print ("<br/> <br/> <br/> <h2>a Impossibile visualizzare i dati </h2>");
        }
        else
        {
            print ("<optgroup label='STATI ESTERI'>");
            while ($datal = mysqli_fetch_array($resb))
            {

                if ($idcomn == ($datal['idcomune']))
                {
                    print("<option value='" . $datal['idcomune'] . "' selected> " . $datal['denominazione'] . "");
                }
                else
                {
                    print("<option value='" . $datal['idcomune'] . "'> " . $datal['denominazione'] . "");
                }

            }
            print ("</optgroup>");
        }

        print("</select> </td> </tr>");
        print  ("<tr> <td> <i> Indirizzo <span style=\"color: #cc0000; \"></span></i> </td> <td align='left'> <input type='text' value='$indirizzo' name='indirizzo' size='30' maxlength='30'> </td> </tr>");
        //$sqlb="SELECT * FROM tbl_comuni ORDER BY denominazione";
        mysqli_data_seek($resa, 0); // Ritorna all'inizio del resultset
        $resb = $resa; // Evita di rifare la query sui comuni : mysqli_query($con,inspref($sqlb));
        if (!$resb)
        {
            print ("<br/> <br/> <br/> <h2><b> Impossibile visualizzare i dati </b></h2>");
        }
        else
        {
            print  ("<tr> <td> <i>Comune di residenza<span style=\"color: #cc0000; \"></span></i> </td> <td align='left'> <select name='idcomr'>");
            print("<option value='9999'>");
            while ($datbl_ = mysqli_fetch_array($resb))
            {

                if ($idcomr == ($datbl_['idcomune']))
                {
                    print("<option value='" . $datbl_['idcomune'] . "' selected> " . $datbl_['denominazione'] . "");
                }
                else
                {
                    print("<option value='" . $datbl_['idcomune'] . "'> " . $datbl_['denominazione'] . "");
                }

            }
            print("</select> </td> </tr>");
        }
        print ("<tr> <td><i>Codice SIDI</i> </td> <td align='left'> <input type='text' value='$sidi' name='sidi' size='20' maxlength='20'> </td> </tr>");
        print ("<tr> <td><i>Numero di telefono</i> </td> <td align='left'> <input type='text' value='$tel' name='tel' size='30' maxlength='15'> </td> </tr>");
        print ("<tr> <td> <i>Numero cellulare<br><small>(massimo 2 separati eventualmente da virgola)</small></i> </td> <td align='left'> <input type='text' value='$cel' name='cel' size='30' maxlength='25'> </td> </tr>");
        print ("<tr> <td><i>Indirizzo E-mail</i> </td> <td align='left'> <input type='text' value='$mail' name='mail' size='50' maxlength='100'> </td> </tr>");
        print   ("<tr><td>Certificato</td><td align='left'><select name='certificato'>");
        if (!$certificato)
        {
            print ("<option value='0' selected>No</option><option value='1'>S&igrave;</option>");
        }
        else
        {
            print ("<option value='0'>No</option><option value='1' selected>S&igrave;</option>");
        }
        print ("</select></td></tr>");
        print ("<tr> <td><i>Note</i> </td> <td align='left'> <input type='text' value='$note' name='note' size='30' maxlength='50'> </td> </tr>");
        print ("<tr> <td><i>Autorizz. perm. entr. post.</i> </td> <td align='left'> <input type='text' value='$autentrata' name='autentrata' size='30' maxlength='30'> </td> </tr>");
        print ("<tr> <td><i>Autorizz. perm. usc. ant.</i> </td> <td align='left'> <input type='text' value='$autuscita' name='autuscita' size='30' maxlength='30'> </td> </tr>");
        print ("<tr> <td><i>Autorizz. ed esoneri</i> </td> <td align='left'> <textarea cols=50 rows=10 name='autorizzazioni'>$autorizzazioni</textarea></td> </tr>");

        print ("<tr> <td><i>Autorizz. firma propria</i> </td> <td align='left'> <select name='firmapropria'>");
        if (!$firmapropria)
        {
            print ("<option value='0' selected>No</option><option value='1'>S&igrave;</option>");
        }
        else
        {
            print ("<option value='0'>No</option><option value='1' selected>S&igrave;</option>");
        }
        print ("</select></td></tr>");
        print ("<tr> <td><i>Blocco cambiamento password</i> </td> <td align='left'> <select name='bloccopassword'>");

        if (!$bloccopassword)
        {
            print ("<option value='0' selected>No</option><option value='1'>S&igrave;</option>");
        }
        else
        {
            print ("<option value='0'>No</option><option value='1' selected>S&igrave;</option>");
        }
        print ("</select></td></tr>");
        print ("<tr> <td><i>Numero registro generale</i> </td>");
        print (" <td align='left'> <input type='text' name='numeroregistro' size='20' maxlength='20'value='$numeroregistro'> </td> </tr>");
        print ("<tr> <td><i>Provenienza</i> </td>");
        print (" <td align='left'> <input type='text' name='provenienza' size='50' maxlength='50'value='$provenienza'> </td> </tr>");
        print ("<tr> <td><i>Titolo ammissione</i> </td>");
        print (" <td align='left'> <input type='text' name='titoloammissione' size='50' maxlength='50'value='$titoloammissione'> </td> </tr>");
        print ("<tr> <td><i>Sequenza iscrizione</i> </td>");
        // print (" <td> <input type='number' name='sequenzaiscrizione' size='1' min='1' max='3' step='1'> </td> </tr>");
        switch ($sequenzaiscrizione)
        {
            case 1:
                $s1 = " selected";
                $s2 = "";
                $s3 = "";
                break;
            case 2:
                $s1 = "";
                $s2 = " selected";
                $s3 = "";
                break;
            case 3:
                $s1 = "";
                $s2 = "";
                $s3 = " selected";
                break;
        }
        print (" <td align='left'> <select name='sequenzaiscrizione'><option$s1>1<option$s2>2<option$s3>3</option></select></td> </tr>");


        $sqlc = "SELECT * FROM tbl_classi ORDER BY specializzazione,sezione,anno";
        $resc = mysqli_query($con, inspref($sqlc)) or die("Errore:" . inspref($sqlc, false));

        print ("<tr bgcolor='lightgrey'> <td><i>Classe </i>");

        print ("</td> <td align='left'><table><tr><td> <select name='datc'>");
        print("<option value='0'>");
        while ($datc = mysqli_fetch_array($resc))
        {

            if ($idcla == ($datc['idclasse']))
            {
                print("<option value='" . $datc['idclasse'] . "' selected> " . $datc['anno'] . " " . $datc['sezione'] . " " . $datc['specializzazione'] . "");
            }
            else
            {
                print("<option value='" . $datc['idclasse'] . "'> " . $datc['anno'] . " " . $datc['sezione'] . " " . $datc['specializzazione'] . "");
            }

        }
        print("</select></td>");
        print("<td>");
        // CERCO CAMBIAMENTI DI CLASSE
        $query = "select * from tbl_cambiamenticlasse where idalunno=$c order by datafine";
        print "<small>";
        $ris = mysqli_query($con, inspref($query));
        while ($rec = mysqli_fetch_array($ris))
        {
            if ($rec['idclasse'] != 0)
            {
                print "Classe: " . decodifica_classe($rec['idclasse'], $con) . " fino a " . data_italiana($rec['datafine']) . "<br>";
            }
            else
            {
                print "Senza classe fino a " . data_italiana($rec['datafine']) . "<br>";
            }
        }
        print "</small>";

        print("</td></tr></table></td></tr>");
        print("<tr bgcolor='lightgrey'><td>Data inizio frequenza nella classe<br><small><b><span style=\"color: red; \">(Da compilare per cambiamenti di classe durante l'anno,<br>lasciare vuota se la classe è valida da inizio anno<br>o per cancellare i cambiamenti inseriti.)</b></span>");


        print("</td>");
        print("<td align='left'><input type='text' name='datacambioclasse' id='datacambioclasse' maxlength='10' size='10'></td></tr>"); // TTTTT Da completare


        print("</tr>");


        print  (" <tr valign='bottom'><td colspan='2'  align ='center' >
   		          <input type ='hidden' name='strcogn' value='$strcogn'>
			      <input type ='hidden' name='strnome' value='$strnome'>
			      <br/><input type='submit' value='Modifica dati'></form></td></tr> ");
        if ($strcogn != "" | $strnome != "")
        {

            print(" <form action='vis_alu_ricerca.php' method='POST'>");
            print ("<input type ='hidden' name='strcogn' value='$strcogn'>");
            print ("<input type ='hidden' name='strnome' value='$strnome'>");
            print ("<tr><td colspan='2' valign='top' align ='center' ><input type='submit' value=' << Indietro '> </td></tr> ");
            print ("<tr><td colspan='2'><div align='left'>");
            print ("</table></form>");

        }
        else
        {
            print(" <form action='vis_alu.php' method='POST'>");
            print ("<input type ='hidden' name='idcla' value='" . $dato['idclasse'] . "'>");
            print ("<tr><td colspan='2' valign='top' align ='center' ><input type='submit' value=' << Indietro '> </td></tr> ");
            print ("<tr><td colspan='2'><div align='left'>");
            print  (" </table></form>");

        }
    }
    else
    {
        print(" Dati non trovati ");
    }
}

stampa_piede("");
mysqli_close($con);

