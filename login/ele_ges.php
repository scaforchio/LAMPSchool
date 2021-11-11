<?php

require_once '../lib/req_apertura_sessione.php';

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

/* Programma per la visualizzazione del menu principale. */

// CONTROLLO ORIGINE DELLA RICHIESTA PER IMPEDIRE ACCESSI DALL'ESTERNO
$urlorigine = $_SERVER['HTTP_REFERER'];
if (isset($_SERVER['HTTPS'])) {
    $urlattuale = 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['SERVER_NAME'];
} else {
    $urlattuale = 'http://' . $_SERVER['SERVER_NAME'];
}

if ($urlattuale == substr($urlorigine, 0, strlen($urlattuale))) {
    $origineok = true;
}


//VERIFICO CHE LA SESSIONE NON SIA SCADUTA
if (isset($_SESSION['prefisso'])) {

    require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
    require_once '../lib/funzioni.php';
} else {

    print "<br><br><b><big><center>Sessione scaduta!</center></big></b>";
    print "<br><b><big><center>Rieffettuare il <a href='../'>login</a>.</center></big></b>";
    die;
}

// VERIFICO CHE NON SIA RICHIESTO IL TOKEN
if (!$_SESSION['tokenok']) {
    header("location: login.php?suffisso=" . $_SESSION['suffisso']);
}
//print "PAR ".$_SESSION['annoscol'];

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

// Passaggio dei parametri nella sessione
//require "../lib/req_assegna_parametri_a_sessione.php";


$json = leggeFileJSON('../lampschool.json');
$_SESSION['versione'] = $json['versione'];

//$_SESSION['giustifica_ritardi'] = $_SESSION['giustifica_ritardi'];
// AZZERO LA SESIONE DEL REGISTRO
$_SESSION['classeregistro'] = "";

//$indirizzoip = IndirizzoIpReale();
//$_SESSION['indirizzoip'] = $indirizzoip;
// $seme = md5(date('Y-m-d'));


$ultimoaccesso = "";

//  $_SESSION['versione']=$versione;
//Connessione al server SQL


if (!$con) {
    die("<h1> Connessione al server fallita </h1>");
}
$JSdisab = is_stringa_html('js_enabled') ? stringa_html('js_enabled') : '0';

if ($JSdisab == 1) {
    die("<center><b>Attenzione! Abilitare Java Script per utilizzare LAMPSchool!</b></center>");
}





$cambiamentopassword = false;

// TTTT
// passwordok=verifica_password($)
// TTTT

if ($_SESSION['tipoutente'] != 'E') {
    if (!$_SESSION['accessouniversale']) {
        $sql = "SELECT unix_timestamp(ultimamodifica) AS ultmod FROM " . $_SESSION['prefisso'] . "tbl_utenti WHERE userid='" . $_SESSION['userid'] . "'";
        $data = mysqli_fetch_array(eseguiQuery($con, $sql, false));
        $dataultimamodifica = $data['ultmod'];
        $dataodierna = time();
        $giornidiff = differenza_giorni($dataultimamodifica, $dataodierna);
        // print "Differenza: $giornidiff";

        $cambiamentopassword = false;
        if (($giornidiff > $_SESSION['maxgiornipass']) & ($_SESSION['tipoutente'] == 'D' | $_SESSION['tipoutente'] == 'P' | $_SESSION['tipoutente'] == 'S' | $_SESSION['tipoutente'] == 'A')) {
            $cambiamentopassword = true;
        }
    }
}

if ($_SESSION['tipoutente'] == "S" | $_SESSION['tipoutente'] == "D") {
    $sost = $_SESSION['cattsost'];
    $norm = $_SESSION['cattnorm'];
}


// Azzero i parametri che servono in modalità registro di classe
// quando si ritorna al menu principale
$_SESSION['regcl'] = "";
$_SESSION['regma'] = "";
$_SESSION['reggi'] = "";

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idutente = $_SESSION["idutente"];
$idesterno = "";

if ($tipoutente == "" || !$origineok) {
    header("location: login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "REGISTRO ON LINE - Menu generale";
$script = "<script>\n";
$script = $script . "$(function() {\n";
$script = $script . "$( '#accordion' ).accordion({heightStyle:'content', fillspace: true, icons: {'header': 'ui-icon-eject'},collapsible: true, active:false});\n";
$script = $script . "});\n";
$script = $script . "function setAction(url) {\n";
$script = $script . "document.getElementById('formMenu').action=url;\n";
$script = $script . "}\n";
$script = $script . "</script>\n";

stampa_head($titolo, "", $script, "SDMAPTEL");
if ($_SESSION['ultimoaccesso'] != "") {
    $ult = " <b>(Ultimo accesso: " . $_SESSION['ultimoaccesso'] . ")</b>";
} else {
    $ult = "";
}
stampa_testata("MENU PRINCIPALE $ult", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

if ($_SESSION['sitoinmanutenzione'] == "yes" & $_SESSION['tipoutente'] != 'M') {
    print "<br><br><br><center><b>REGISTRO IN MANUTENZIONE!</b></center>";
    stampa_piede();
    die;
}







if ($cambiamentopassword) {
    print "<br><br><center><b><big>Password scaduta, modificarla!</big></b></center>";
    print "<br><center><a href='../password/cambpwd.php'>Cambia password</a></center>";
} else {
    print "<table border=1 width=100%>
					<tr class='prima'>
						<td width='33%'><center>MENU</center></td>
						<td width='67%'><center>AVVISI</center></td>
				  </tr>
				  <tr>";
    print "<td align='center' valign='top'>";
    menu_open();

    if ($tipoutente == 'E') {

        menu_title_begin('ESAMI DI STATO');
        menu_item('../esame3m/esmaterieclasse.php', 'MATERIE ESAME');
        menu_item('../esame3m/commissione.php', 'FORMAZIONE COMMISSIONI');
        menu_separator("");
        menu_item('../esame3m/rieptabesame.php', 'TABELLONE');
        menu_item('../esame3m/sitesami.php', 'SITUAZIONE ESAMI');
        menu_item_new_page('../esame3m/stamparegistroesame.php', 'REGISTRO ESAMI');
        menu_separator("");
        menu_item('../esame3m/esa_vis_alu_cla.php', 'ANAGRAFICHE ALUNNI');

        menu_separator("");
        menu_item('../esame3m/cambpassesame.php', 'CAMBIAMENTO PASSWORD');

        // menu_item('../esame3m/schedaalunno.php', 'SCHEDA ALUNNO');

        menu_title_end();
    }
//menu_item('../evacuazione/ricannotaz.php', 'EVACUAZIONE');

    if ($tipoutente == 'D') {

        menu_title_begin('REGISTRO DI CLASSE');
        menu_item('../regclasse/riepgiorno.php', 'VISUALIZZA GIORNATA');
        menu_item('../regclasse/riepsett.php', 'VISUALIZZA SETTIMANA');
        menu_item('../regclasse/annotaz.php', 'ANNOTAZIONI SU REGISTRO');
        menu_item('../regclasse/ricannotaz.php', 'RICERCA ANNOTAZIONI');
        menu_item('../regclasse/CRUDannotazioni.php?mod=1&can=1&ins=1', 'GESTIONE ANNOTAZIONI');
        menu_item('../classi/CRUDevacuazione.php', 'NOMINE ALUNNI PER EVACUAZIONI');
        menu_item('../evacuazione/evacuazione.php', 'MODULO EVACUAZIONE');
        // menu_item("../assemblee/assdoc.php?iddocente=$idutente", 'ASSEMBLEE DI CLASSE');
        if ($_SESSION['livello_scuola'] == '4') {
            menu_item("../assemblee/assdoc.php", 'ASSEMBLEE DI CLASSE');
        }
        //menu_item('../evacuazione/ricannotaz.php', 'EVACUAZIONE');
        menu_title_end();
        menu_title_begin('ASSENZE');
        // menu_item('../assenze/ass.php', 'ASSENZE');
        if ($_SESSION['gestcentrautorizz'] == 'no') {
            //  menu_item('../assenze/rit.php', 'RITARDI');
            //  menu_item('../assenze/usc.php', 'USCITE ANTICIPATE');
        }

        menu_item('../assenze/sitassmens.php', 'SITUAZIONE MENSILE');
        // menu_item('../assenze/sitasstota.php', 'SITUAZIONE TOTALE');
        menu_item('../assenze/visgiustifiche.php', 'ELIMINA GIUSTIFICHE');
        menu_title_end();

        menu_title_begin('LEZIONI');
        //  menu_item('../lezioni/lez.php', 'INSERIMENTO E MODIFICA');
        menu_item('../lezioni/sitleztota.php', 'TABELLONE RIEPILOGO');

        if ($norm)
            menu_item('../lezioni/riepargom.php', 'RIEPILOGO ARGOMENTI');
        menu_item('../lezioni/lezsupp.php', 'INSERIMENTO SUPPLENZA');
        if ($norm)
            menu_item('../lezionigruppo/lezgru.php', 'LEZIONI A GRUPPI DI ALUNNI');

        menu_item('../contr/verifsovrappdoc.php', 'CONTROLLO PROPRIE LEZIONI');
        menu_item("../lezioni/vis_lez.php?iddocente=$idutente", "CORREZIONE PROPRIE LEZIONI");
        menu_item("../lezionigruppo/vis_lez_gru.php?iddocente=$idutente", "CORREZIONE PROPRIE LEZIONI A GRUPPI");
        if ($norm)
            menu_item('../lezioni/riepargomcert.php?modo=norm', 'RIEPILOGO ARGOMENTI SOSTEGNO');
        menu_title_end();
        //   print "     <br/>GESTIONE E STAMPA STATINO";

        menu_title_begin('NOTE DISCIPLINARI');
        menu_item('../note/notecl.php', 'NOTE DI CLASSE');
        menu_item('../note/ricnotecl.php', 'RICERCA NOTE DI CLASSE');
        menu_item('../note/noteindmul.php', 'NOTE INDIVIDUALI');
        menu_item('../note/ricnoteind.php', 'RICERCA NOTE INDIVIDUALI');

        menu_title_end();
        menu_title_begin('OSSERVAZIONI E DIARIO DI CLASSE');

        menu_item('../valutazioni/osssist.php', 'OSSERVAZIONI SISTEMATICHE');
        menu_item('../valutazioni/ricosssist.php', 'RICERCA OSSERV. SIST.');
        menu_item('../valutazioni/stampaosssist.php', 'STAMPA OSSERV. SIST.');
        menu_item('../valutazioni/diariocl.php', 'DIARIO DI CLASSE');
        menu_item('../valutazioni/ricdiariocl.php', 'RICERCA SU DIARIO DI CLASSE');
        menu_item('../valutazioni/stampadiariocl.php', 'STAMPA DIARIO DI CLASSE');
        if ($sost) {
            menu_item('../valutazioni/osssistcert.php', 'OSSERVAZIONI SISTEMATICHE AL. CERT.');
            menu_item('../valutazioni/ricosssistcert.php', 'RICERCA OSSERV. SIST. AL. CERT.');
            menu_item('../valutazioni/stampaosssistcert.php', 'STAMPA OSSERV. SIST. AL. CERT.');
        }
        menu_title_end();
        menu_title_begin('VOTI');
        menu_item('../valutazioni/prospettovoti.php', 'PROSPETTO VOTI');
        menu_item('../valutazioni/proposte.php', 'MEDIE E PROPOSTE DI VOTO');
        menu_item('../valutazionecomportamento/valcomp.php', 'VOTO COMPORTAMENTO');
        menu_item('../valutazionecomportamento/sitvalcompalu.php', 'SITUAZIONE VOTI COMPORTAMENTO');
        menu_item('../obiettivi/obproposte.php', 'PROPOSTE VALUTAZIONI PER ALUNNI');

        menu_title_end();
        if ($_SESSION['livello_scuola'] != 4) {
            menu_title_begin('CERTIFICAZIONE COMPETENZE');
            menu_item('../obiettivi/CRUDobiettivi.php', 'GESTIONE OBIETTIVI MATERIE/CLASSE');
            menu_item('../obiettivi/scrutobiettiviintermedio.php', 'SCRUTINIO OBIETTIVI');

            menu_title_end();
        }


        if (estrai_docente_coordinatore($idutente, $con)) {
            menu_title_begin('FUNZIONI COORDINATORE');

            menu_item('../valutazioni/riepvoticlasse.php', 'SITUAZIONE VOTI MEDI PER CLASSE');
            menu_item('../valutazioni/visvalpre.php', 'SITUAZIONE ALUNNO');
            menu_item('../note/stampanote.php', 'STAMPA NOTE PER CLASSE');
            menu_item('../assenze/sitassmens.php', 'SITUAZIONE MENSILE ASSENZE');
            menu_item('../assenze/sitasstota.php', 'SITUAZIONE TOTALE ASSENZE');
            menu_item('../assenze/sitassprob.php', 'SITUAZIONI PROBLEMATICHE ASSENZE');
            menu_item('../assenze/sitassmate.php', 'SITUAZIONI ASSENZE PER MATERIA');
            menu_item('../assenze/deroghe.php', 'DEROGHE ASSENZE');
            menu_item('../alunni/CRUD_autorizzazioni.php?soloclasse=yes', 'Gestione autorizzazioni ad uscita anticipata con classe');
            menu_item('../assenze/visderoghe.php', 'SITUAZIONE DEROGHE ASSENZE');
            menu_item('../scrutini/riepvoti.php', 'TABELLONE SCRUTINI INTERMEDI');
            menu_item('../scrutini/riepvotifinali.php', 'TABELLONE SCRUTINI FINALI');
            if ($_SESSION['livello_scuola'] == '4') {
                menu_item('../scrutini/riepvotifinali.php?integrativo=yes', 'SCRUTINI INTEGRATIVI');
            }
            if ($_SESSION['livello_scuola'] == '3' || $_SESSION['livello_scuola'] == '2') {
                menu_item('../consorientativo/cotabellone.php?tipoaccesso=coordinatore', 'CONSIGLI ORIENTATIVI');
            }
            menu_item('../scrutini/riepproposte.php', 'RIEPILOGO PROPOSTE DI VOTO');
            menu_item('../documenti/stampafirmaprogrammi.php?docente=' . $idutente, 'STAMPE PER PRESA VISIONE PROGRAMMI');
            menu_item('../documenti/documenticlasse.php', 'DOCUMENTI CLASSE');
            menu_title_end();
        }

        if ($norm & $_SESSION['valutazionepercompetenze'] == 'yes') {
            menu_title_begin('VALUTAZIONE COMPETENZE');
            menu_item('../valutazioni/valabilcono.php', 'VERIFICHE');
            menu_item('../valutazioni/valaluabilcono.php?modo=norm', 'VALUTAZIONI ALUNNO');
            menu_item('../valutazioni/sitvalalu.php', 'VISUALIZZA SITUAZIONE ALUNNO');
            menu_item('../valutazioni/sitvalobi.php', 'VISUALIZZA SITUAZ. PER OBIETT.');

            menu_title_end();
        }

        if ($norm & $_SESSION['valutazionepercompetenze'] == 'yes') {
            menu_title_begin('PROGRAMMAZIONE');
            menu_item('../programmazione/compdo.php', 'GEST. COMPETENZE');
            menu_item('../programmazione/abcodo.php', 'GEST. ABIL./CONO');
            menu_item('../programmazione/confimportaprogr.php', 'IMPORTA PROGR. SCOLAST.');
            menu_item('../programmazione/copiaprogdoc.php', 'COPIA PROGRAMMAZIONE');
            menu_item('../programmazione/visprogrdo.php', 'VISUALIZZA PROGRAMMA');
            menu_item('../programmazione/modivoceprog.php', 'CORREGGI ABIL./CONO');
            menu_item('../programmazione/modicompetenza.php', 'CORREGGI COMPETENZA');
            menu_item('../programmazione/esportaprogrammazioneincsv.php', 'ESPORTA PROGRAMMAZIONE');
            menu_item('../programmazione/importaprogrammazionedacsv.php', 'IMPORTA DA CSV');
            menu_item('../progrcert/seletipoprogr.php', 'TIPO PROGR. ALUNNI');
            menu_item('../progrcert/visprogralu.php', 'VISUALIZZA PROGRAMMI ALUNNI CERTIFICATI');
            menu_title_end();
        }


        menu_title_begin('DOCUMENTI');

        menu_item('../documenti/documprog.php?tipodoc=pia', 'PIANI LAVORO');
        menu_item('../documenti/documprog.php?tipodoc=pro', 'PROGRAMMI SVOLTI');
        menu_item('../documenti/documprog.php?tipodoc=rel', 'RELAZIONI FINALI');
        if ($norm)
            menu_item('../documenti/documenti.php', 'DOCUMENTI ALUNNO');


        menu_title_end();

        if ($sost) {
            menu_separator("SOSTEGNO");

            menu_title_begin('LEZIONI');

            menu_item('../lezioni/lezcert.php', 'INSERIMENTO E MODIFICA SOST.');
            menu_item('../lezioni/riepargomcert.php?modo=sost', 'RIEPILOGO ARGOMENTI SOSTEGNO');
            menu_item("../lezioni/vis_lez_cert.php?iddocente=$idutente", "CORREZIONE PROPRIE LEZIONI");
            menu_title_end();
            //   print "     <br/>GESTIONE E STAMPA STATINO";


            menu_title_begin('VALUTAZIONE COMPETENZE');
            menu_item('../valutazioni/valaluabilcono.php?modo=sost', 'VALUTAZIONI ALUNNI CERTIFICATI');
            if (!$norm)
                menu_item('../valutazioni/sitvalalu.php', 'VISUALIZZA SITUAZIONE ALUNNO');

            menu_title_end();
            menu_title_begin('PEI');
            menu_item('../progrcert/seletipoprogr.php', 'TIPO PROGR. ALUNNI');
            menu_item('../progrcert/visprogralu.php', 'VISUALIZZA PROGRAMMI ALUNNI');
            menu_item('../progrcert/compalu.php', 'COMPETENZE PEI');
            menu_item('../progrcert/abcoalu.php', 'ABIL./CONO');
            menu_item('../progrcert/modivoceprogalu.php', 'CORREGGI ABIL./CONO ALUNNO');
            menu_item('../progrcert/modicompetenzaalu.php', 'CORREGGI COMPETENZA ALUNNO');
            menu_item('../documenti/documenti.php?tipo=pei', 'ALLEGATI PEI');
            menu_item('../pei/sele_stampa_pei.php?modo=sost', 'STAMPA PEI');
            menu_item('../pei/scarica_doc_pei.php?modo=sost', 'SCARICA DOCUMENTI PEI');
            menu_title_end();
        }
        menu_separator("&nbsp;");
        if ($_SESSION['tokenservizimoodle'] != "" & docente_gestore_moodle($idutente, $con)) {
            menu_title_begin('GESTIONE MOODLE');
            menu_item('../moodle/esporta_moodle.php', 'ESPORTA DATI PER MOODLE');
            menu_item('../moodle/creacorsimoodle.php', 'CREA E SINCRONIZZA CORSI MOODLE');
            menu_item('../moodle/creacorsimoodleclasse.php', 'CREA CORSI MOODLE PER CLASSE');
            menu_item('../moodle/sincronizzacorsimoodleclasse.php', 'SINCRONIZZA CORSI MOODLE PER CLASSE');
            menu_item('../moodle/rigenerapasswordmoodle.php', 'RIGENERA PASSWORD MOODLE ALUNNI');
            menu_item('../moodle/rigenerapasswordmoodledoc.php', 'RIGENERA PASSWORD MOODLE DOCENTI');
            menu_item('../moodle/sincronizzautenti.php', 'AGGIUNGI NUOVI UTENTI A MOODLE');
            menu_item('../moodle/seleiscrizionecorsi.php', 'ISCRIVI STUDENTI A CORSO MOODLE');
            menu_item('../moodle/seleiscrizionecorsidoc.php', 'ISCRIVI DOCENTI A CORSO MOODLE');
            menu_title_end();
        }
        menu_title_begin('GESTIONE COLLOQUI');

        menu_item('../colloqui/visappuntamentidoc.php', 'PRENOTAZIONI COLLOQUI POMERIDIANI');
        menu_item('../colloqui/visrichieste_doc.php', 'PRENOTAZIONI COLLOQUI MATTUTINI');

        menu_title_end();
        menu_title_begin('ALTRO');
        menu_item('../password/cambpwd.php', 'CAMBIAMENTO PROPRIA PASSWORD');
        menu_item('../circolari/viscircolari.php', 'LEGGI CIRCOLARI');

        menu_item("../docenti/mod_contatto.php", 'AGGIORNA DATI DI CONTATTO');
        menu_item("../collegamenti/coll.php", 'VISUALIZZA COLLEGAMENTI WEB');
        menu_item('../ferie/richferie.php', 'RICHIESTA ASTENSIONE DAL LAVORO');
        menu_item('../ferie/esameproprierichferie.php', 'ESAMINA RICHIESTE FERIE');
        menu_item('../docenti/visorario.php', 'VISUALIZZA ORARIO');
        menu_title_end();
    }

    if ($tipoutente == 'S') {
        menu_title_begin('REGISTRO DI CLASSE');
        menu_item('../regclasse/riepgiorno.php', 'VISUALIZZA GIORNATA');
        menu_item('../regclasse/riepsett.php', 'VISUALIZZA SETTIMANA');
        menu_item('../regclasse/annotaz.php', 'ANNOTAZIONI SU REGISTRO');
        menu_item('../regclasse/ricannotaz.php', 'RICERCA ANNOTAZIONI');
        menu_item('../regclasse/CRUDannotazioni.php?mod=1&can=1&ins=1', 'GESTIONE ANNOTAZIONI');
        menu_item('../regclasse/stamparegiclasse.php', 'STAMPA REGISTRI DI CLASSE');
        menu_item('../classi/CRUDevacuazione.php', 'NOMINE ALUNNI PER EVACUAZIONI');
        menu_item('../evacuazione/evacuazione.php', 'MODULO EVACUAZIONE');

        menu_title_end();
        menu_title_begin('ASSENZE');
        menu_item('../assenze/ass.php', 'ASSENZE');
        menu_item('../assenze/rit.php', 'RITARDI');
        menu_item('../assenze/usc.php', 'USCITE ANTICIPATE');
        menu_item('../assenze/selealunniautuscita.php', 'AUTORIZZAZIONE USCITE');
        menu_item('../assenze/CRUDautorizzazioniuscite.php', 'GESTIONE AUTORIZZAZIONI USCITE');
        menu_item('../rp/autorizzaritardo.php', 'AUTORIZZA ENTRATA IN RITARDO');
        menu_item('../rp/vis_ritcla.php', 'ENTRATE POSTICIPATE CLASSI');
        menu_item('../rp/vis_usccla.php', 'USCITE ANTICIPATE CLASSI');
        // menu_item('../rp/CRUDentrateposticipate.php', 'ENTRATE POSTICIPATE CLASSI');
        menu_item('../assenze/sitgiustifiche.php', 'VISUALIZZA MANCANZA GIUSTIFICHE');

        menu_separator("");
        menu_item('../assenze/sitassmens.php', 'SITUAZIONE MENSILE');
        menu_item('../assenze/sitasstota.php', 'SITUAZIONE TOTALE');
        menu_item('../assenze/sitassprob.php', 'SITUAZIONI PROBLEMATICHE');
        menu_item('../assenze/sitassperclassi.php', 'PERCENTUALI PER CLASSE');
        menu_item('../assenze/sitmensassalu.php', 'ASSENZE MENSILI ALUNNO');
        menu_item('../assenze/sitassmate.php', 'SITUAZIONE ASSENZE PER MATERIA');
        menu_separator("");
        menu_item('../rp/vistimbrature.php', 'VISUALIZZA TIMBRATURE');
        menu_item('../rp/selealunnipresenza.php', 'FORZA PRESENZA ALUNNI');
        menu_item('../rp/CRUDpresenzeforzate.php', 'VISUALIZZA PRESENZE FORZATE');
        menu_item('../rp/selealunnitimbraturaforzata.php', 'FORZA TIMBRATURE');
        menu_item('../rp/elencotimbratureforzate.php', 'REPORT FORZATURE');

        menu_separator("");
        menu_item('../assenze/visgiustifiche.php', 'ELIMINA GIUSTIFICHE');
        menu_item('../assenze/deroghe.php', 'DEROGHE ASSENZE');
        menu_item('../assenze/visderoghe.php', 'SITUAZIONE DEROGHE ASSENZE');
        //menu_separator("");
        //menu_item('../assenze/ricalcoloassenzesele.php', 'RICALCOLO ASSENZE');

        menu_title_end();

        menu_title_begin('LEZIONI');
        //menu_item('../lezioni/lez.php', 'INSERIMENTO E MODIFICA');
        menu_item('../lezioni/sitleztota.php', 'TABELLONE RIEPILOGO');
        if ($norm)
            menu_item('../lezioni/riepargom.php', 'RIEPILOGO ARGOMENTI');
        menu_item('../lezioni/lezsupp.php', 'INSERIMENTO SUPPLENZA');
        if ($norm)
            menu_item('../lezionigruppo/lezgru.php', 'LEZIONI A GRUPPI DI ALUNNI');

        menu_item('../contr/verifsovrappdoc.php', 'CONTROLLO PROPRIE LEZIONI');
        menu_item("../lezioni/vis_lez.php?iddocente=$idutente", "CORREZIONE PROPRIE LEZIONI");
        menu_item("../lezionigruppo/vis_lez_gru.php?iddocente=$idutente", "CORREZIONE PROPRIE LEZIONI A GRUPPI");
        if ($norm)
            menu_item('../lezioni/riepargomcert.php?modo=norm', 'RIEPILOGO ARGOMENTI SOSTEGNO');
        menu_title_end();

        menu_title_begin('NOTE DISCIPLINARI');
        menu_item('../note/notecl.php', 'NOTE DI CLASSE');
        menu_item('../note/ricnotecl.php', 'RICERCA NOTE DI CLASSE');
        menu_item('../note/noteindmul.php', 'NOTE INDIVIDUALI');
        menu_item('../note/ricnoteind.php', 'RICERCA NOTE INDIVIDUALI');
        menu_item('../note/stampanote.php', 'STAMPA NOTE PER CLASSE');

        menu_title_end();
        if ($_SESSION['livello_scuola'] == '4') {
            menu_title_begin('ASSEMBLEE DI CLASSE');
            // menu_item("../assemblee/assdoc.php?iddocente=$idutente", 'CONCESSIONE');
            // menu_item("../assemblee/assstaff.php?iddocente=$idutente", 'AUTORIZZAZIONE');
            // menu_item("../assemblee/contver.php?iddocente=$idutente", 'CONTROLLO VERBALI');
            menu_item("../assemblee/assdoc.php", 'ASSEMBLEE PROPRIE ORE');
            menu_item("../assemblee/assstaff.php", 'AUTORIZZAZIONE ASSEMBLEE');
            menu_item("../assemblee/contver.php", 'VERIFICA VERBALI');
            menu_item("../assemblee/visionaverbali.php", 'SITUAZIONE ASSEMBLEE');
            menu_item("../assemblee/reportassemblee.php", 'RAPPORTO PER DIRIGENTE');
            menu_item("../classi/CRUDrappresentanti.php", 'GESTIONE RAPPRESENTANTI');
            menu_title_end();
        }
        menu_title_begin('OSSERVAZIONI E DIARIO DI CLASSE');

        menu_item('../valutazioni/osssist.php', 'OSSERVAZIONI SISTEMATICHE');
        menu_item('../valutazioni/ricosssist.php', 'RICERCA OSSERV. SIST.');
        menu_item('../valutazioni/stampaosssist.php', 'STAMPA OSSERV. SIST.');
        menu_item('../valutazioni/diariocl.php', 'DIARIO DI CLASSE');
        menu_item('../valutazioni/ricdiariocl.php', 'RICERCA SU DIARIO DI CLASSE');
        menu_item('../valutazioni/stampadiariocl.php', 'STAMPA DIARIO DI CLASSE');
        if ($sost) {
            menu_item('../valutazioni/osssistcert.php', 'OSSERVAZIONI SISTEMATICHE AL. CERT.');
            menu_item('../valutazioni/ricosssistcert.php', 'RICERCA OSSERV. SIST. AL. CERT.');
            menu_item('../valutazioni/stampaosssistcert.php', 'STAMPA OSSERV. SIST. AL. CERT.');
        }
        menu_title_end();
        if (estrai_docente_coordinatore($idutente, $con)) {
            menu_title_begin('FUNZIONI COORDINATORE');

            menu_item('../valutazioni/riepvoticlasse.php', 'SITUAZIONE VOTI MEDI PER CLASSE');
            menu_item('../note/stampanote.php', 'STAMPA NOTE PER CLASSE');
            menu_item('../scrutini/riepvoti.php', 'TABELLONE SCRUTINI INTERMEDI');
            menu_item('../scrutini/riepvotifinali.php', 'TABELLONE SCRUTINI FINALI');
            menu_item('../assenze/sitassmate.php', 'SITUAZIONI ASSENZE PER MATERIA');
            menu_item('../alunni/CRUD_autorizzazioni.php?soloclasse=yes', 'Gestione autorizzazioni ad uscita anticipata con classe');
            if ($_SESSION['livello_scuola'] == '4') {
                menu_item('../scrutini/riepvotifinali.php?integrativo=yes', 'SCRUTINI INTEGRATIVI');
            }
            if ($_SESSION['livello_scuola'] == '3' || $_SESSION['livello_scuola'] == '2') {
                menu_item('../consorientativo/cotabellone.php?tipoaccesso=coordinatore', 'CONSIGLI ORIENTATIVI');
            }
            menu_item('../scrutini/riepproposte.php', 'RIEPILOGO PROPOSTE DI VOTO');
            menu_item('../documenti/stampafirmaprogrammi.php?docente=' . $idutente, 'STAMPE PER PRESA VISIONE PROGRAMMI');
            menu_item('../documenti/documenticlasse.php', 'DOCUMENTI CLASSE');

            menu_title_end();
        }
        menu_title_begin('SCRUTINI');
        menu_item('../scrutini/riepvoti.php', 'TABELLONE SCRUTINI INTERMEDI');
        menu_item('../scrutini/riepvotifinali.php', 'TABELLONE SCRUTINI FINALI');
        if ($_SESSION['livello_scuola'] == '4') {
            menu_item('../scrutini/riepvotifinali.php?integrativo=yes', 'SCRUTINI INTEGRATIVI');
        }
        menu_item('../scrutini/sitscrutini.php', 'SITUAZIONE SCRUTINI');
        menu_item('../scrutini/schedaalu.php', 'SCRUTINIO INTERMEDIO ALUNNO');
        menu_item('../scrutini/schedafinalealu.php', 'SCRUTINIO FINALE ALUNNO');
        menu_item('../obiettivi/obvalutazioni.php', 'SCRUTINIO OBIETTIVI APPRENDIMENTO');
        menu_item('../obiettivi/obstampaschede.php', 'STAMPA SCHEDE OBIETTIVI APPRENDIMENTO');
        menu_item('../scrutini/riepproposte.php', 'RIEPILOGO PROPOSTE DI VOTO');
        if ($_SESSION['livello_scuola'] == '3' || $_SESSION['livello_scuola'] == '2') {
            menu_item('../consorientativo/cotabellone.php', 'CONSIGLI ORIENTATIVI');
        }
        menu_title_end();

        menu_title_begin('VOTI');
        //menu_item('../valutazioni/val.php', 'INSERIMENTO');
        menu_item('../valutazioni/prospettovoti.php', 'PROSPETTO VOTI');
        menu_item('../valutazioni/proposte.php', 'MEDIE E PROPOSTE DI VOTO');
        menu_item('../valutazioni/visvalpre.php', 'VISUALIZZA SITUAZIONE ALUNNO');
        menu_item('../valutazionecomportamento/valcomp.php', 'VOTO COMPORTAMENTO');
        menu_item('../valutazionecomportamento/sitvalcompalu.php', 'SITUAZIONE VOTI COMPORTAMENTO');
        menu_item('../obiettivi/obproposte.php', 'PROPOSTE VALUTAZIONI PER ALUNNI');

        menu_title_end();

        if ($_SESSION['livello_scuola'] != 4) {
            menu_title_begin('CERTIFICAZIONE COMPETENZE');

            menu_item('../obiettivi/CRUDobiettivi.php', 'GESTIONE OBIETTIVI MATERIE/CLASSE');
            menu_item('../certcomp/ccproposte.php', 'PROPOSTE PER ALUNNI');

            //menu_item('../certcomp/ccvalutazioni.php', 'CERTIFICAZIONE ALUNNI');
            menu_item('../certcomp/cctabellone.php', 'TABELLONE CLASSE');

            menu_title_end();
        }

        if ($norm & $_SESSION['valutazionepercompetenze'] == 'yes') {
            menu_title_begin('VALUTAZIONE COMPETENZE');
            menu_item('../valutazioni/valabilcono.php', 'VERIFICHE');
            menu_item('../valutazioni/valaluabilcono.php?modo=norm', 'VALUTAZIONI ALUNNO');
            menu_item('../valutazioni/sitvalalu.php', 'VISUALIZZA SITUAZIONE ALUNNO');
            menu_item('../valutazioni/sitvalobi.php', 'VISUALIZZA SITUAZ. PER OBIETT.');
            menu_title_end();
        }

        if ($norm & $_SESSION['valutazionepercompetenze'] == 'yes') {
            menu_title_begin('PROGRAMMAZIONE');
            menu_item('../programmazione/compdo.php', 'GEST. COMPETENZE');
            menu_item('../programmazione/abcodo.php', 'GEST. ABIL./CONO');
            menu_item('../programmazione/confimportaprogr.php', 'IMPORTA PROGR. SCOLAST.');
            menu_item('../programmazione/copiaprogdoc.php', 'COPIA PROGRAMMAZIONE');
            menu_item('../programmazione/visprogrdo.php', 'VISUALIZZA PROGRAMMA');
            menu_item('../programmazione/modivoceprog.php', 'CORREGGI ABIL./CONO');
            menu_item('../programmazione/modicompetenza.php', 'CORREGGI COMPETENZA');
            menu_item('../programmazione/esportaprogrammazioneincsv.php', 'ESPORTA PROGRAMMAZIONE');
            menu_item('../programmazione/importaprogrammazionedacsv.php', 'IMPORTA DA CSV');
            menu_item('../progrcert/seletipoprogr.php', 'TIPO PROGR. ALUNNI');
            menu_item('../progrcert/visprogralu.php', 'VISUALIZZA PROGRAMMI ALUNNI CERTIFICATI');
            menu_title_end();
        }

        menu_title_begin('DOCUMENTI');
        menu_item('../documenti/documprog.php?tipodoc=pia', 'PIANI LAVORO');
        menu_item('../documenti/documprog.php?tipodoc=pro', 'PROGRAMMI SVOLTI');
        menu_item('../documenti/documprog.php?tipodoc=rel', 'RELAZIONI FINALI');
        menu_item('../documenti/verdocumprog.php?tipodoc=pia', 'VISUALIZZA PIANI DI LAVORO');
        menu_item('../documenti/verdocumprog.php?tipodoc=pro', 'VISUALIZZA PROGRAMMI SVOLTI');
        menu_item('../documenti/verdocumprog.php?tipodoc=rel', 'VISUALIZZA RELAZIONI FINALI');
        menu_item('../documenti/stampafirmaprogrammi.php', 'STAMPE PER PRESA VISIONE PROGRAMMI');
        if ($norm)
            menu_item('../documenti/documenti.php', 'DOCUMENTI ALUNNO');
        menu_item('../documenti/documenticlasse.php', 'DOCUMENTI CLASSE');
        menu_title_end();

        menu_title_begin('UTENTI');
        menu_item('../password/gestpwd.php', 'Cambia password utente');
        menu_item('../segreteria/vis_imp.php?modo=vis', 'VISUALIZZA AMMINISTRATIVI');
        menu_item('../docenti/vis_doc.php?modo=vis', 'VISUALIZZA DOCENTI');
        menu_item('../alunni/vis_alu_solo_vis.php', 'VISUALIZZA ALUNNI');
        menu_title_end();

        menu_title_begin('AVVISI, CIRCOLARI ED SMS');
        menu_item('../contr/vis_avvisi.php', 'AVVISI');
        menu_item('../circolari/circolari.php', 'CIRCOLARI');
        menu_item('../circolari/listadistr.php', 'VERIFICA LETTURA CIRCOLARI');
        menu_item('../sms/seleinviosms.php', 'SMS ASSENZE');
        menu_item('../sms/seleinviosmsvari.php', 'SMS VARI');
        menu_item('../sms/seleinviosmsdoc.php', 'SMS DOCENTI');
        menu_item('../sms/visualizzasms.php', 'VISUALIZZA STATO SMS INVIATI');
        menu_item('../sms/vis_sospinviosms.php', 'SOSPENSIONI INVIO AUTOMATICO SMS');
        menu_item('../collegamenti/collegamentiweb.php', 'PREPARAZIONE COLLEGAMENTI WEB');

        menu_title_end();

        if ($_SESSION['tokenservizimoodle'] != "" & docente_gestore_moodle($idutente, $con)) {
            menu_title_begin('GESTIONE MOODLE');
            menu_item('../moodle/esporta_moodle.php', 'ESPORTA DATI PER MOODLE');
            menu_item('../moodle/creacorsimoodle.php', 'CREA E SINCRONIZZA CORSI MOODLE');
            menu_item('../moodle/creacorsimoodleclasse.php', 'CREA CORSI MOODLE PER CLASSE');
            menu_item('../moodle/sincronizzacorsimoodleclasse.php', 'SINCRONIZZA CORSI MOODLE PER CLASSE');
            menu_item('../moodle/rigenerapasswordmoodle.php', 'RIGENERA PASSWORD MOODLE ALUNNI');
            menu_item('../moodle/rigenerapasswordmoodledoc.php', 'RIGENERA PASSWORD MOODLE DOCENTI');
            menu_item('../moodle/sincronizzautenti.php', 'AGGIUNGI NUOVI UTENTI A MOODLE');
            menu_item('../moodle/seleiscrizionecorsi.php', 'ISCRIVI STUDENTI A CORSO MOODLE');
            menu_item('../moodle/seleiscrizionecorsidoc.php', 'ISCRIVI DOCENTI A CORSO MOODLE');
            menu_title_end();
        }
        menu_title_begin('STATISTICHE E RIEPILOGHI');
        menu_item('../contr/statinsertot.php', 'STATISTICHE INSERIMENTO DATI');
        menu_item('../valutazioni/riepvoticlasse.php', 'RIEPILOGO MEDIE PER CLASSE');
        // menu_item('../scrutini/riepproposte.php', 'RIEPILOGO PROPOSTE DI VOTO');
        menu_item('../lezioni/vis_lez.php', 'CORREZIONE LEZIONI');
        menu_item('../contr/verifsovrapp.php', 'VERIFICA SOVRAPPOSIZIONI');
        menu_item('../contr/vissupplenze.php', 'VISUALIZZA SUPPLENZE');
        menu_item('../contr/sitorelezione.php', 'VISUALIZZA ORE LEZIONE');
        menu_title_end();

        if ($sost) {
            menu_separator("SOSTEGNO");

            menu_title_begin('LEZIONI');
            menu_item('../lezioni/lezcert.php', 'INSERIMENTO E MODIFICA SOST.');
            menu_item('../lezioni/riepargomcert.php?modo=sost', 'RIEPILOGO ARGOMENTI SOSTEGNO');
            menu_item("../lezioni/vis_lez_cert.php?iddocente=$idutente", "CORREZIONE PROPRIE LEZIONI");
            menu_title_end();
            //   print "     <br/>GESTIONE E STAMPA STATINO";


            menu_title_begin('VALUTAZIONE COMPETENZE');
            menu_item('../valutazioni/valaluabilcono.php?modo=sost', 'VALUTAZIONI ALUNNI CERTIFICATI');

            if (!$norm)
                menu_item('../valutazioni/sitvalalu.php', 'VISUALIZZA SITUAZIONE ALUNNO');

            menu_title_end();
            menu_title_begin('PEI');
            menu_item('../progrcert/seletipoprogr.php', 'TIPO PROGR. ALUNNI');
            menu_item('../progrcert/visprogralu.php', 'VISUALIZZA PROGRAMMI ALUNNI');
            menu_item('../progrcert/compalu.php', 'COMPETENZE PEI');
            menu_item('../progrcert/abcoalu.php', 'ABIL./CONO');
            menu_item('../progrcert/modivoceprogalu.php', 'CORREGGI ABIL./CONO ALUNNO');
            menu_item('../progrcert/modicompetenzaalu.php', 'CORREGGI COMPETENZA ALUNNO');
            menu_item('../documenti/documenti.php?tipo=pei', 'ALLEGATI PEI');
            menu_item('../pei/sele_stampa_pei.php?modo=sost', 'STAMPA PEI');
            menu_item('../pei/scarica_doc_pei.php?modo=sost', 'SCARICA DOCUMENTI PEI');
            menu_title_end();
        }
        menu_title_begin('ANAGRAFICHE');
        menu_item('../alunni/vis_alu_cla.php', 'ALUNNI');
        // menu_item('../alunni/carica_anagrafe_sidi.php', 'Carica alunni da file SIDI');
        //  $par=serialize(array(';','\"','1','1','2','3','4','5','6','7','8','9','10','11','12'));
        //  print $par;
        //  menu_item("../alunni/carica_alunni_da_csv.php?par=$par", "Carica alunni da file CSV generico");

        menu_item('../alunni/attr_classe.php', 'Attribuisci classe ad alunni');
        menu_item('../alunni/vis_alu_ricerca.php', 'Ricerca alunni');
        //menu_item('../alunni/autorizzazioni.php', 'Visualizza deroghe ed autorizzazioni');
        menu_item('../alunni/CRUD_autorizzazioni.php', 'Gestione autorizzazioni');
        menu_item('../segreteria/vis_imp.php', 'IMPIEGATI DI SEGRETERIA');
        menu_item('../docenti/vis_doc.php', 'DOCENTI');
        menu_item('../colloqui/disponibilita.php', 'DISPONIBIL. DOCENTI');
        menu_item('../colloqui/visdisponibilita.php', 'VISUALIZZA DISP. DOCENTI');
        menu_item('../contr/stampaelezioni.php', 'STAMPA PER ELEZIONI');
        menu_item('../docenti/CRUD_linkwebex.php', 'COLLEGAMENTI PER LEZIONI A DISTANZA');
        menu_title_end();
        menu_separator("&nbsp;");

        menu_title_begin('CATTEDRE');
        menu_item('../cattedre/cat.php', 'CATTEDRE');
        menu_item('../cattedre/cat_sost.php', 'CATTEDRE SOSTEGNO');
        menu_item('../lezionigruppo/vis_gru.php', 'CATTEDRE SPECIALI');
        menu_item('../cattedre/vis_cattedre.php', 'VISUALIZZA CATTEDRE');
        menu_title_end();

        menu_title_begin('GESTIONE PERMESSI');
        menu_item('../ferie/richferie.php', 'RICHIESTA ASTENSIONE DAL LAVORO');
        menu_item('../ferie/esamerichferie.php', 'ESAME RICHIESTE FERIE');
        menu_item('../ferie/esameproprierichferie.php', 'ESAME PROPRIE RICHIESTE FERIE');
        menu_item('../ferie/visrichferie.php', 'VISIONA ASTENSIONI APPROVATE DAL D.S.');
        menu_item('../ferie/visorepermesso.php', 'TOTALI ASTENSIONI DOCENTI');
        menu_item('../ferie/CRUDrecuperi.php', 'GESTIONE RECUPERI');
        menu_title_end();

        menu_title_begin('GESTIONE COLLOQUI');
        menu_item('../colloqui/insgiornatecoll.php', 'GESTIONE GIORNATE COLLOQUI POMERIDIANI');
        menu_item('../colloqui/gestassenzecolloqui.php', 'GESTIONE ASSENZE DOCENTI');
        menu_item('../colloqui/visappuntamentidoc.php', 'PRENOTAZIONI COLLOQUI POMERIDIANI');
        menu_item('../colloqui/visrichieste_doc.php', 'PRENOTAZIONI COLLOQUI MATTUTINI');

        menu_title_end();
        menu_title_begin('ALTRO');
        menu_item('../password/cambpwd.php', 'CAMBIAMENTO PROPRIA PASSWORD');
        menu_item('../circolari/viscircolari.php', 'LEGGI CIRCOLARI');

        menu_item("../docenti/mod_contatto.php", 'AGGIORNA DATI DI CONTATTO');
        menu_item("../collegamenti/coll.php", 'VISUALIZZA COLLEGAMENTI WEB');

        menu_item('../docenti/visorario.php', 'VISUALIZZA ORARIO');
        menu_item('../docenti/visoraridocenti.php', 'VISUALIZZA ORARIO DOCENTE');

        menu_item('../lezioni/dad.php', 'INSERISCI GIORNATE D.A.D.');
        menu_item('../lezioni/CRUDdad.php', 'ELIMINA GIORNATE D.A.D.');

        menu_title_end();
    }


    if ($tipoutente == 'P') {   // Presidenza
        menu_title_begin('REGISTRO DI CLASSE');
        menu_item('../regclasse/riepgiorno.php', 'VISUALIZZA GIORNO');
        menu_item('../regclasse/riepsett.php', 'VISUALIZZA SETTIMANA');
        menu_item('../regclasse/annotaz.php', 'ANNOTAZIONI SU REGISTRO');
        menu_item('../regclasse/ricannotaz.php', 'RICERCA ANNOTAZIONI');
        menu_item('../regclasse/CRUDannotazioni.php?mod=1&can=1&ins=1', 'GESTIONE ANNOTAZIONI');
        menu_item('../regclasse/stamparegiclasse.php', 'STAMPA REGISTRI DI CLASSE');
        if ($_SESSION['maxgiorniritardolez'] < 300) {
            menu_separator("");
            menu_item('../contr/derogainserimento.php', 'DEROGA A LIMITE INSERIMENTO');
            menu_item('../contr/cambiautente.php', 'ASSUMI ALIAS ALTRO UTENTE');
        }
        menu_title_end();

        menu_title_begin('ASSENZE');
        menu_item('../assenze/ass.php', 'ASSENZE');
        menu_item('../assenze/rit.php', 'RITARDI');
        menu_item('../assenze/usc.php', 'USCITE ANTICIPATE');
        menu_item('../assenze/selealunniautuscita.php', 'AUTORIZZAZIONE USCITE');

        menu_separator("");
        menu_item('../assenze/sitassmens.php', 'SITUAZIONE MENSILE');
        menu_item('../assenze/sitasstota.php', 'SITUAZIONE TOTALE');
        menu_item('../assenze/sitassprob.php', 'SITUAZIONI PROBLEMATICHE');
        menu_item('../assenze/sitassperclassi.php', 'PERCENTUALI PER CLASSE');
        menu_item('../assenze/sitgiustifiche.php', 'VISUALIZZA MANCANZA GIUSTIFICHE');
        menu_separator("");
        menu_item('../rp/vistimbrature.php', 'VISUALIZZA TIMBRATURE');
        menu_item('../rp/selealunnipresenza.php', 'FORZA PRESENZA ALUNNI');
        menu_item('../rp/CRUDpresenzeforzate.php', 'VISUALIZZA PRESENZE FORZATE');
        menu_item('../rp/selealunnitimbraturaforzata.php', 'FORZA TIMBRATURE');
        menu_item('../rp/autorizzaritardo.php', 'AUTORIZZA ENTRATA IN RITARDO');
        menu_item('../rp/elencotimbratureforzate.php', 'REPORT FORZATURE');

        menu_separator("");
        menu_item('../assenze/deroghe.php', 'DEROGHE ASSENZE');
        menu_item('../assenze/visderoghe.php', 'SITUAZIONE DEROGHE ASSENZE');

        // menu_item('../assenze/ricalcolaoreritardo.php', 'RICALCOLA RITARDI');
        // menu_item('../assenze/ricalcolaoreuscita.php', 'RICALCOLA USCITE ANTICIPATE');
        // menu_item('../assenze/ricalcolaassenze.php', 'RICALCOLA ASSENZE LEZIONI');
        menu_title_end();
        if ($_SESSION['livello_scuola'] == '4') {
            menu_title_begin('ASSEMBLEE DI CLASSE');
            // menu_item("../assemblee/assdoc.php?iddocente=$idutente", 'CONCESSIONE');
            // menu_item("../assemblee/assstaff.php?iddocente=$idutente", 'AUTORIZZAZIONE');
            // menu_item("../assemblee/contver.php?iddocente=$idutente", 'CONTROLLO VERBALI');

            menu_item("../assemblee/assstaff.php", 'AUTORIZZAZIONE ASSEMBLEE');
            menu_item("../assemblee/contver.php", 'VERIFICA VERBALI');
            menu_item("../assemblee/visionaverbali.php", 'SITUAZIONE ASSEMBLEE');
            menu_title_end();
        }
        menu_title_begin('LEZIONI');
        menu_item('../lezioni/sitleztota.php', 'TABELLONE RIEPILOGO');
        menu_item('../lezioni/riepargom.php', 'RIEPILOGO ARGOMENTI');
        menu_item('../lezioni/riepargomcert.php', 'RIEPILOGO LEZIONI SOSTEGNO');
        menu_title_end();

        menu_title_begin('NOTE DISCIPLINARI');
        menu_item('../note/notecl.php', 'NOTE DI CLASSE');
        menu_item('../note/ricnotecl.php', 'RICERCA NOTE DI CLASSE');
        menu_item('../note/noteindmul.php', 'NOTE INDIVIDUALI');
        menu_item('../note/ricnoteind.php', 'RICERCA NOTE INDIVIDUALI');
        menu_item('../note/stampanote.php', 'STAMPA NOTE PER CLASSE');
        menu_title_end();

        menu_title_begin('OSSERVAZIONI E DIARIO DI CLASSE');
        menu_item('../valutazioni/ricosssist.php', 'RICERCA OSSERV. SIST.');
        menu_item('../valutazioni/stampaosssist.php', 'STAMPA OSSERV. SIST.');
        menu_item('../valutazioni/ricdiariocl.php', 'RICERCA SU DIARIO DI CLASSE');
        menu_item('../valutazioni/stampadiariocl.php', 'STAMPA DIARIO DI CLASSE');
        menu_title_end();

        menu_title_begin('SCRUTINI');
        menu_item('../scrutini/riepvoti.php', 'TABELLONE SCRUTINI INTERMEDI');
        menu_item('../scrutini/riepvotifinali.php', 'TABELLONE SCRUTINI FINALI');
        menu_item('../scrutini/sitscrutini.php', 'SITUAZIONE SCRUTINI');
        menu_item('../scrutini/schedaalu.php', 'SCHEDA INTERMEDIA ALUNNO');
        if ($_SESSION['livello_scuola'] == '4') {
            menu_item('../scrutini/riepvotifinali.php?integrativo=yes', 'SCRUTINI INTEGRATIVI');
        }
        menu_item('../scrutini/schedafinalealu.php', 'SCRUTINIO FINALE ALUNNO');
        menu_item('../obiettivi/obvalutazioni.php', 'SCRUTINIO OBIETTIVI APPRENDIMENTO');
        menu_item('../obiettivi/obstampaschede.php', 'STAMPA SCHEDE OBIETTIVI APPRENDIMENTO');
        menu_item('../scrutini/riepproposte.php', 'RIEPILOGO PROPOSTE DI VOTO');
        if ($_SESSION['livello_scuola'] == '3' || $_SESSION['livello_scuola'] == '2') {
            menu_item('../consorientativo/cotabellone.php', 'CONSIGLI ORIENTATIVI');
        }

        menu_title_end();

        if ($_SESSION['livello_scuola'] != 4) {
            menu_title_begin('CERTIFICAZIONE COMPETENZE');
            menu_item('../obiettivi/scrutobiettiviintermedio.php', 'SCRUTINIO OBIETTIVI');
            //menu_item('../certcomp/ccvalutazioni.php', 'CERTIFICAZIONE ALUNNI');
            menu_item('../certcomp/cctabellone.php', 'TABELLONE CLASSE');
            menu_title_end();
        }
        menu_title_begin('PROGRAMMI');
        menu_item('../programmazione/visprogrdo.php', 'VISUALIZZA PROGRAMMI DOCENTI');
        menu_item('../programmazione/visprogrsc.php', 'VISUALIZZA PROGRAMMI SCOLAST.');
        menu_title_end();
        // menu_title_begin('GESTIONE ANAGRAFICHE');
        // menu_item('../alunni/vis_alu_cla.php', 'ALUNNI');
        // menu_item("../alunni/carica_alunni_da_csv.php?par=1!0!1!1!2!3!4!5!6!7!8!9!10!11!12", "Carica alunni da file CSV generico");
        // menu_item('../alunni/attr_classe.php', 'Attribuisci classe ad alunni');
        // menu_item('../alunni/vis_alu_ricerca.php', 'Ricerca alunni per cognome e nome');
        // menu_item('../segreteria/vis_imp.php', 'AMMINISTRATIVI');
        // menu_item('../docenti/vis_doc.php', 'DOCENTI');
        // menu_item("../docenti/carica_docenti_da_csv.php?par=1!0!1!1!2!99!99!99!99!99!99!99", "Carica docenti da file CSV generico");
        // menu_title_end();
        menu_title_begin('CATTEDRE');
        menu_item('../cattedre/cat.php', 'CATTEDRE');
        menu_item('../cattedre/cat_sost.php', 'CATTEDRE SOSTEGNO');
        menu_item('../lezionigruppo/vis_gru.php', 'CATTEDRE SPECIALI');
        menu_item('../cattedre/vis_cattedre.php', 'VISUALIZZA CATTEDRE');

        menu_title_end();
        menu_title_begin('PEI');
        menu_item('../progrcert/visprogralu.php', 'VISUALIZZA PROGRAMMI ALUNNI');
        menu_item('../documenti/documenti.php?tipo=pei', 'ALLEGATI PEI');
        menu_item('../pei/sele_stampa_pei.php', 'STAMPA PEI');
        menu_item('../pei/scarica_doc_pei.php', 'SCARICA DOCUMENTI PEI');
        menu_title_end();
        menu_title_begin('ANAGRAFICHE');
        menu_item('../alunni/vis_alu_cla.php', 'ALUNNI');
        // menu_item('../alunni/carica_anagrafe_sidi.php', 'Carica alunni da file SIDI');
        //  $par=serialize(array(';','\"','1','1','2','3','4','5','6','7','8','9','10','11','12'));
        //  print $par;
        //  menu_item("../alunni/carica_alunni_da_csv.php?par=$par", "Carica alunni da file CSV generico");
        menu_item('../alunni/attr_classe.php', 'Attribuisci classe ad alunni');
        menu_item('../alunni/vis_alu_ricerca.php', 'Ricerca alunni');
        menu_item('../alunni/CRUD_autorizzazioni.php', 'Gestione autorizzazioni');
        menu_item('../segreteria/vis_imp.php', 'IMPIEGATI DI SEGRETERIA');
        menu_item('../docenti/vis_doc.php', 'DOCENTI');
        menu_item('../docenti/attrruolos.php', 'Attribuisci ruolo Staff di presidenza a docente');
        menu_item('../docenti/revruolos.php', 'Revoca ruolo Staff a docente');
        menu_item('../colloqui/disponibilita.php', 'DISPONIBIL. DOCENTI');
        menu_item('../colloqui/visdisponibilita.php', 'VISUALIZZA DISP. DOCENTI');
        menu_item('../docenti/CRUD_linkwebex.php', 'COLLEGAMENTI PER LEZIONI A DISTANZA');
        menu_title_end();

        menu_title_begin('DOCUMENTI');
        menu_item('../documenti/verdocumprog.php?tipodoc=pia', 'VISUALIZZA PIANI DI LAVORO');
        menu_item('../documenti/verdocumprog.php?tipodoc=pro', 'VISUALIZZA PROGRAMMI');
        menu_item('../documenti/verdocumprog.php?tipodoc=rel', 'VISUALIZZA RELAZIONI FINALI');
        menu_item('../documenti/stampafirmaprogrammi.php', 'STAMPE PER PRESA VISIONE PROGRAMMI');
        menu_item('../documenti/documenti.php', 'DOCUMENTI ALUNNO');
        menu_item('../documenti/documenticlasse.php', 'DOCUMENTI CLASSE');
        menu_title_end();

        menu_title_begin('VALUTAZIONE COMPETENZE');
        menu_item('../valutazioni/sitvalalu.php', 'VISUALIZZA SITUAZIONE ALUNNO');
        menu_item('../valutazioni/sitvalobi.php', 'VISUALIZZA SITUAZ. PER OBIETT.');
        menu_title_end();

        menu_title_begin('GESTIONE UTENTI');
        menu_item('../password/gestpwd.php', 'Cambia password utente');
        menu_item('../segreteria/vis_imp.php?modo=vis', 'VISUALIZZA AMMINISTRATIVI');
        menu_item('../docenti/vis_doc.php?modo=vis', 'VISUALIZZA DOCENTI');
        menu_item('../alunni/vis_alu_solo_vis.php', 'VISUALIZZA ALUNNI');
        menu_separator("");
        menu_item('../contr/cambiautente.php', 'ASSUMI ALIAS ALTRO UTENTE');
        menu_item('../contr/derogainserimento.php', 'DEROGA A LIMITE INSERIMENTO');

        menu_title_end();

        menu_title_begin('AVVISI, CIRCOLARI ED SMS');
        menu_item('../contr/vis_avvisi.php', 'AVVISI');
        menu_item('../circolari/circolari.php', 'CIRCOLARI');
        menu_item('../circolari/listadistr.php', 'VERIFICA LETTURA CIRCOLARI');
        menu_item('../sms/seleinviosms.php', 'SMS ASSENZE');
        menu_item('../sms/seleinviosmsvari.php', 'SMS VARI');
        menu_item('../sms/seleinviosmsdoc.php', 'SMS DOCENTI');
        menu_item('../sms/visualizzasms.php', 'VISUALIZZA STATO SMS INVIATI');
        menu_item('../sms/vis_sospinviosms.php', 'SOSPENSIONI INVIO AUTOMATICO SMS');
        menu_item('../collegamenti/collegamentiweb.php', 'PREPARAZIONE COLLEGAMENTI WEB');
        menu_title_end();

        menu_title_begin('PERMESSI DOCENTI');

        menu_item('../ferie/esamerichferie.php', 'ESAMINA RICHIESTE FERIE');
        menu_item('../ferie/visorepermesso.php', 'TOTALI ASTENSIONI DOCENTI');
        menu_title_end();
        menu_title_begin('STATISTICHE E RIEPILOGHI');
        menu_item('../contr/statinsertot.php', 'STATISTICHE INSERIMENTO DATI');
        menu_item('../valutazioni/riepvoticlasse.php', 'RIEPILOGO MEDIE PER CLASSE');
        menu_item('../lezioni/vis_lez.php', 'CORREZIONE LEZIONI');
        menu_item('../contr/verifsovrapp.php', 'VERIFICA SOVRAPPOSIZIONI');
        menu_item('../contr/vissupplenze.php', 'VISUALIZZA SUPPLENZE');
        menu_item('../contr/sitorelezione.php', 'VISUALIZZA ORE LEZIONE');
        menu_item('../valutazioni/visvalpre.php', 'VISUALIZZA SITUAZIONE GLOBALE ALUNNO');

        menu_title_end();
        menu_title_begin('ALTRO');
        menu_item("../collegamenti/coll.php", 'VISUALIZZA COLLEGAMENTI WEB');
        menu_item('../password/cambpwd.php', 'CAMBIAMENTO PROPRIA PASSWORD');
        menu_item('../contr/sele_tabe_backup.php', 'BACKUP DATI');
        menu_item('../contr/scar_sit_totale.php', 'SCARICA SITUAZIONE ATTUALE');
        menu_item('../contr/solalettura_on.php', 'ABILITA SOLA LETTURA');
        menu_item('../contr/solalettura_off.php', 'DISABILITA SOLA LETTURA');
        menu_item('../docenti/visoraridocenti.php', 'VISUALIZZA ORARIO DOCENTE');

        menu_title_end();
    }

    if ($tipoutente == 'M') {  // Amministratore
        // inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§Inizio menu");
        menu_separator("STRUMENTI DI AMMINISTRAZIONE");
        menu_title_begin('DATI E CONFIGURAZIONE');
        menu_item('../password/cambpwd.php', 'CAMBIAMENTO PROPRIA PASSWORD');
        menu_item('../contr/paramedit.php', 'CAMBIAMENTO CONFIGURAZIONE');
        menu_item('../contr/sele_tabe_backup.php', 'BACKUP DATI');
        menu_item('../contr/elencoindici.php', 'ELENCO INDICI');
        menu_item('../contr/ricostruisciindici.php', 'RICOSTRUZIONE INDICI');
        menu_item('../contr/nuovaversione.php', 'CONTROLLO NUOVA VERSIONE');
        menu_item('../contr/solalettura_on.php', 'ABILITA SOLA LETTURA');
        menu_item('../contr/solalettura_off.php', 'DISABILITA SOLA LETTURA');
        menu_item('../contr/vis_avvisi.php', 'GESTISCI AVVISI');
        menu_item('../contr/testiedit.php', 'GESTISCI TESTI');
        menu_item('../contr/eseguisql.php', 'ESECUZIONE COMANDO SQL');
        menu_item('../contr/visualizzaversioni.php', 'VERIFICA AGG. DATABASE');
        menu_item('../contr/updphpini.php', 'AGGIORNA FILE PHP-INI');
        menu_item('../collegamenti/collegamentiweb.php', 'PREPARAZIONE COLLEGAMENTI WEB');

        menu_title_end();
        menu_title_begin('IMPORTAZIONE DA ANNO PRECEDENTE');
        menu_item('../importa/sele_tabe_import.php', 'IMPORTA DATI');
        menu_item('../importa/sele_classe_succ.php', 'RIASSEGNA CLASSI');
        menu_title_end();
        menu_title_begin('PASSWORD');
        menu_item('../password/rigenera_password.php', 'Rigenera e stampa password tutor');
        if ($_SESSION['gestioneutentialunni'] == 'yes')
            menu_item('../password/alu_rigenera_password.php', 'Rigenera e stampa password alunni');
        menu_item('../password/conf_rig_pass_doc.php', 'Rigenera e stampa password docenti');
        menu_item('../password/CRUDselezioneInvioOTP.php', 'Gestione modalità invio OTP');
        menu_item('../password/genschemaotp.php', 'Generazione schemi per OTP');
        menu_item('../password/gestschedaotp.php', 'Gestione scheda OTP');
        menu_item('../password/gestpwd.php', 'Cambia password utente');
        if ($_SESSION['gestioneutentialunni'] == 'yes')
            menu_item('../alunni/creautentialunni.php', 'Crea utenze per alunni');
        menu_item('../esame3m/abilitautenteesame.php', 'Abilita utente esame di stato');
        menu_title_end();

        menu_separator("STRUMENTI DI SEGRETERIA");
        menu_title_begin('ANAGRAFICHE');
        menu_item('../alunni/vis_alu_cla.php', 'ALUNNI');
        // menu_item('../alunni/carica_anagrafe_sidi.php', 'Carica alunni da file SIDI');
        //  $par=serialize(array(';','\"','1','1','2','3','4','5','6','7','8','9','10','11','12'));
        //  print $par;
        //  menu_item("../alunni/carica_alunni_da_csv.php?par=$par", "Carica alunni da file CSV generico");
        menu_item("../alunni/carica_alunni_da_csv.php?par=1!0!1!1!2!3!4!5!6!7!8!9!10!11!12", "Carica alunni da file CSV generico");
        menu_item('../alunni/attr_classe.php', 'Attribuisci classe ad alunni');

        menu_item('../alunni/vis_alu_ricerca.php', 'Ricerca alunni');
        menu_item('../segreteria/vis_imp.php', 'IMPIEGATI DI SEGRETERIA');
        menu_item('../docenti/vis_doc.php', 'DOCENTI');
        menu_item("../docenti/carica_docenti_da_csv.php?par=1!0!1!1!2!99!99!99!99!99!99!99", "Carica docenti da file CSV generico");
        menu_item('../docenti/attrruolos.php', 'Attribuisci ruolo Staff di presidenza a docente');
        menu_item('../docenti/revruolos.php', 'Revoca ruolo Staff a docente');
        menu_item('../docenti/ins_pre.php', 'INSERISCI PRESIDE');
        menu_item('../docenti/mod_pre.php', 'MODIFICA PRESIDE');
        menu_item('../docenti/conf_can_pre.php', 'ELIMINA UTENZA PRESIDE');
        menu_item('../colloqui/disponibilita.php', 'DISPONIBIL. DOCENTI');
        menu_item('../colloqui/visdisponibilita.php', 'VISUALIZZA DISP. DOCENTI');
        menu_separator("");
        menu_item('../contr/cambiautente.php', 'ASSUMI ALIAS ALTRO UTENTE');

        menu_title_end();

        menu_title_begin('TABELLE');
        if ($_SESSION['plesso_specializzazione'] == "Specializzazione") {
            menu_item('../classi/CRUDspecializzazioni.php', 'SPECIALIZZAZIONI');
        }
        if ($_SESSION['plesso_specializzazione'] == "Plesso") {
            menu_item('../classi/CRUDspecializzazioni.php', 'PLESSI');
        }
        menu_item('../classi/CRUDsezioni.php', 'SEZIONI');
        menu_item('../classi/CRUDclassi.php', 'CLASSI');
        menu_item('../materie/vis_mat.php', 'MATERIE');
        menu_item('../documenti/vis_tdoc.php', 'TIPI DOCUMENTO');
        menu_item('../materie/ordmaterie.php', 'ORDINAMENTO MATERIE');
        menu_item('../scrutini/tabesiti.php', 'TIPI ESITI');
        menu_item('../colloqui/orario.php', 'ORARIO');
        menu_item('../colloqui/festivita.php', 'FESTIVITA\'');
        menu_item('../colloqui/CRUDsospensionicolloqui.php', 'SOSPENSIONI COLLOQUI');
        menu_item('../colloqui/disponibilita.php', 'DISPONIBIL. DOCENTI');
        menu_item('../colloqui/visdisponibilita.php', 'VISUALIZZA DISP. DOCENTI');

        // menu_item('../cattedre/agg_catt_preside.php', 'Aggiorna cattedre per preside');
        menu_title_end();

        menu_title_begin('CATTEDRE');
        menu_item('../cattedre/cat.php', 'CATTEDRE');
        menu_item('../cattedre/cat_sost.php', 'CATTEDRE SOSTEGNO');
        menu_item('../lezionigruppo/vis_gru.php', 'CATTEDRE SPECIALI');
        menu_item('../cattedre/vis_cattedre.php', 'VISUALIZZA CATTEDRE');
        menu_title_end();

        menu_title_begin('PROGRAMMAZIONE');
        menu_item('../programmazione/compsc.php', 'GEST. PROGR. (COMPETENZE)');
        menu_item('../programmazione/abcosc.php', 'GEST. PROGR. (ABIL./CONO.)');
        menu_item('../programmazione/esportaprogrammazionescolincsv.php', 'ESPORTA IN CSV');
        menu_item('../programmazione/importaprogrammazionescoldacsv.php', 'IMPORTA DA CSV');
        menu_title_end();
        menu_title_begin('VALUTAZIONE COMPORTAMENTO');
        menu_item('../valutazionecomportamento/obiettivi.php', 'GEST. OBIETTIVI COMPORTAMENTO');
        menu_item('../valutazionecomportamento/subobiettivi.php', 'GEST. SUB-OBIETTIVI COMPORTAMENTO');
        menu_item('../valutazionecomportamento/visobiettivicomportamento.php', 'VIS. OBIETTIVI DI COMPORTAMENTO');
        menu_item('../valutazionecomportamento/modiobiettivo.php', 'CORREGGI OBIETTIVO');
        menu_item('../valutazionecomportamento/modisubobiettivo.php', 'CORREGGI SUB-OBIETTIVO');
        menu_title_end();
        menu_title_begin('CIRCOLARI E AVVISI');
        menu_item('../circolari/circolari.php', 'CIRCOLARI');
        menu_item('../circolari/listadistr.php', 'VERIFICA LETTURA CIRCOLARI');
        menu_item('../circolari/viscircolari.php', 'LEGGI CIRCOLARI');
        menu_item('../contr/vis_avvisi.php', 'AVVISI');
        menu_item('../sms/seleinviosms.php', 'SMS ASSENZE');
        menu_item('../sms/seleinviosmsvari.php', 'SMS VARI');
        menu_item('../sms/seleinviosmsdoc.php', 'SMS DOCENTI');
        menu_title_end();
        if ($_SESSION['tokenservizimoodle'] != "") {
            menu_title_begin('GESTIONE MOODLE');
            menu_item('../moodle/esporta_moodle.php', 'ESPORTA DATI PER MOODLE');
            menu_item('../moodle/testcollegamentomoodle.php', 'TEST COLLEGAMENTO MOODLE');
            menu_item('../moodle/creacorsimoodle.php', 'CREA E SINCRONIZZA CORSI MOODLE');
            menu_item('../moodle/creacorsimoodleclasse.php', 'CREA CORSI MOODLE PER CLASSE');
            menu_item('../moodle/sincronizzacorsimoodleclasse.php', 'SINCRONIZZA CORSI MOODLE PER CLASSE');
            menu_item('../moodle/rigenerapasswordmoodle.php', 'RIGENERA PASSWORD MOODLE ALUNNI');
            menu_item('../moodle/rigenerapasswordmoodledoc.php', 'RIGENERA PASSWORD MOODLE DOCENTI');
            menu_item('../moodle/sincronizzautenti.php', 'AGGIUNGI NUOVI UTENTI A MOODLE');
            menu_item('../moodle/seleiscrizionecorsi.php', 'ISCRIVI STUDENTI A CORSO MOODLE');
            menu_item('../moodle/seleiscrizionecorsidoc.php', 'ISCRIVI DOCENTI A CORSO MOODLE');
            menu_item('../moodle/sincronizzacorsi.php', 'SINCRONIZZA TUTTI I CORSI MOODLE (PESANTE)');

            menu_title_end();
        }

        menu_separator("DATI E STATISTICHE");
        menu_title_begin('STATISTICHE E RIEPILOGHI');
        menu_item('../contr/statinsertot.php', 'STATISTICHE INSERIMENTO DATI');
        menu_item('../valutazioni/riepvoticlasse.php', 'RIEPILOGO MEDIE PER CLASSE');
        menu_item('../lezioni/vis_lez.php', 'CORREZIONE LEZIONI');
        menu_item('../contr/verifsovrapp.php', 'VERIFICA SOVRAPPOSIZIONI');
        menu_item('../contr/vissupplenze.php', 'VISUALIZZA SUPPLENZE');
        menu_item('../contr/vis_accessi.php', 'VISUALIZZA ACCESSI');
        menu_item('../rp/vistimbrature.php', 'VISUALIZZA TIMBRATURE');
        menu_item('../contr/visualizzalog.php', 'VISUALIZZA LOG');
        menu_item('../contr/test.php', 'TEST ');
        menu_title_end();

        menu_title_begin('ASSENZE');
        menu_item('../assenze/forzaassenzapertutti.php', 'FORZA ASSENZE PER TUTTI! (Da usare in caso di mancate timbrature)');
        menu_title_end();
    }

    if ($tipoutente == 'A') {  // Amministrativo
        menu_title_begin('ANAGRAFICHE');
        menu_item('../alunni/vis_alu_cla.php', 'ALUNNI');
        // menu_item('../alunni/carica_anagrafe_sidi.php', 'Carica alunni da file SIDI');
        //  $par=serialize(array(';','\"','1','1','2','3','4','5','6','7','8','9','10','11','12'));
        //  print $par;
        //  menu_item("../alunni/carica_alunni_da_csv.php?par=$par", "Carica alunni da file CSV generico");
        menu_item("../alunni/carica_alunni_da_csv.php?par=1!0!1!1!2!3!4!5!6!7!8!9!10!11!12", "Carica alunni da file CSV generico");
        menu_item('../alunni/attr_classe.php', 'Attribuisci classe ad alunni');

        menu_item('../alunni/vis_alu_ricerca.php', 'Ricerca alunni');
        menu_item('../segreteria/vis_imp.php', 'AMMINISTRATIVI');
        menu_item('../docenti/vis_doc.php', 'DOCENTI');
        menu_item("../docenti/carica_docenti_da_csv.php?par=1!0!1!1!2!99!99!99!99!99!99!99", "Carica docenti da file CSV generico");
        menu_item('../sms/seleinviosmsvari.php', 'SMS VARI');
        menu_title_end();

        menu_title_begin('PASSWORD');
        menu_item('../password/rigenera_password.php', 'Rigenera e stampa password tutor');
        if ($_SESSION['gestioneutentialunni'] == 'yes')
            menu_item('../password/alu_rigenera_password.php', 'Rigenera e stampa password alunni');
        menu_item('../password/conf_rig_pass_doc.php', 'Rigenera e stampa password docenti');
        menu_item('../password/gestpwd.php', 'Cambia password utente');
        menu_item('../segreteria/vis_imp.php?modo=vis', 'VISUALIZZA AMMINISTRATIVI');
        menu_item('../docenti/vis_doc.php?modo=vis', 'VISUALIZZA DOCENTI');
        menu_item('../alunni/vis_alu_solo_vis.php', 'VISUALIZZA ALUNNI');
        menu_title_end();

        menu_title_begin('TABELLE');
        if ($_SESSION['plesso_specializzazione'] == "Specializzazione") {
            menu_item('../classi/CRUDspecializzazioni.php', 'SPECIALIZZAZIONI');
        }
        if ($_SESSION['plesso_specializzazione'] == "Plesso") {
            menu_item('../classi/CRUDspecializzazioni.php', 'PLESSI');
        }
        menu_item('../classi/CRUDsezioni.php', 'SEZIONI');
        menu_item('../classi/CRUDclassi.php', 'CLASSI');
        menu_item('../materie/vis_mat.php', 'MATERIE');
        menu_item('../documenti/vis_tdoc.php', 'TIPI DOCUMENTO');
        menu_item('../materie/ordmaterie.php', 'ORDINAMENTO MATERIE');
        menu_item('../colloqui/orario.php', 'ORARIO');
        menu_item('../colloqui/festivita.php', 'FESTIVITA\'');

        menu_item('../scrutini/tabesiti.php', 'TIPI ESITI');

        menu_title_end();

        menu_title_begin('VALUTAZIONE COMPORTAMENTO');
        menu_item('../valutazionecomportamento/obiettivi.php', 'GEST. OBIETTIVI COMPORTAMENTO');
        menu_item('../valutazionecomportamento/subobiettivi.php', 'GEST. SUB-OBIETTIVI COMPORTAMENTO');
        menu_item('../valutazionecomportamento/visobiettivicomportamento.php', 'VIS. OBIETTIVI DI COMPORTAMENTO');
        menu_item('../valutazionecomportamento/modiobiettivo.php', 'CORREGGI OBIETTIVO');
        menu_item('../valutazionecomportamento/modisubobiettivo.php', 'CORREGGI SUB-OBIETTIVO');
        menu_title_end();
        menu_title_begin('ASSENZE');

        menu_item('../assenze/sitmensassalu.php', 'ASSENZE MENSILI ALUNNO');
        menu_title_end();
        menu_title_begin('SCRUTINI');
        menu_item('../scrutini/riepvoti.php', 'SCRUTINI INTERMEDI');
        menu_item('../scrutini/riepvotifinali.php', 'SCRUTINI FINALI');
        // menu_item('../obiettivi/obstampaschede.php', 'STAMPA SCHEDE VALUTAZIONE OBIETTIVI DI APPRENDIMENTO');
        if ($_SESSION['livello_scuola'] == '4') {
            menu_item('../scrutini/riepvotifinali.php?integrativo=yes', 'SCRUTINI INTEGRATIVI');
        }
        menu_item('../scrutini/schedaalu.php', 'SCRUTINIO INTERMEDIO ALUNNO');
        menu_item('../scrutini/schedafinalealu.php', 'SCRUTINIO FINALE ALUNNO');
        if ($_SESSION['livello_scuola'] == '3' || $_SESSION['livello_scuola'] == '2') {
            menu_item('../consorientativo/cotabellone.php', 'CONSIGLI ORIENTATIVI');
        }
        menu_title_end();
        if ($_SESSION['livello_scuola'] != 4) {
            menu_title_begin('CERTIFICAZIONE COMPETENZE');
            menu_item('../certcomp/cctabellone.php', 'STAMPA CERTIFICATI');
            menu_title_end();
        }

        menu_title_begin('CATTEDRE');
        menu_item('../cattedre/cat.php', 'CATTEDRE');
        menu_item('../cattedre/cat_sost.php', 'CATTEDRE SOSTEGNO');
        menu_item('../lezionigruppo/vis_gru.php', 'CATTEDRE SPECIALI');
        menu_item('../cattedre/vis_cattedre.php', 'VISUALIZZA CATTEDRE');
        menu_title_end();

        menu_title_begin('PROGRAMMAZIONE');
        menu_item('../programmazione/compsc.php', 'GEST. PROGR. (COMPETENZE)');
        menu_item('../programmazione/abcosc.php', 'GEST. PROGR. (ABIL./CONO.)');
        menu_item('../programmazione/esportaprogrammazionescolincsv.php', 'ESPORTA IN CSV');
        menu_item('../programmazione/importaprogrammazionescoldacsv.php', 'IMPORTA DA CSV');
        menu_title_end();

        menu_title_begin('PEI');
        menu_item('../pei/sele_stampa_pei.php', 'STAMPA PEI');
        menu_item('../pei/scarica_doc_pei.php', 'SCARICA DOCUMENTI PEI');
        menu_title_end();
        menu_title_begin('ALTRO');
        menu_item('../password/cambpwd.php', 'CAMBIAMENTO PROPRIA PASSWORD');
        menu_item('../circolari/circolari.php', 'CIRCOLARI');
        menu_item('../circolari/listadistr.php', 'VERIFICA LETTURA CIRCOLARI');
        menu_item('../circolari/viscircolari.php', 'LEGGI CIRCOLARI');
        menu_item("../collegamenti/coll.php", 'VISUALIZZA COLLEGAMENTI WEB');

        menu_title_end();
    }

    // print "Solo comunicazioni $_SESSION['gensolocomunicazioni']";

    if ($tipoutente == 'T' & $_SESSION['gensolocomunicazioni'] == 'no') {

        //  $sql = "SELECT * FROM tbl_tutori WHERE idutente='" . $_SESSION['idutente'] . "'";
        $sql = "SELECT * FROM tbl_alunni WHERE idalunno='" . $_SESSION['idutente'] . "'";
        $ris = eseguiQuery($con, $sql);
        if ($val = mysqli_fetch_array($ris)) {
            $idstudente = $val["idalunno"];
        }


        $sql = "select * from tbl_alunni where idalunno='$idstudente'";
        $ris = eseguiQuery($con, $sql);
        if ($val = mysqli_fetch_array($ris)) {
            $cognome = $val["cognome"];
            $nome = $val["nome"];
            $idstudente = $val["idalunno"];
            $idclasse = $val["idclasse"];
            $idesterno = $val["idesterno"];
            $pwesterna = $val["pwesterna"];
            $telcel = $val["telcel"];

            // print "<br/><button onclick=\"window.open('../assenze/sitassalut.php'>ASSENZE</a>";
            menu_title_begin("SITUAZIONE ALUNNO $cognome&nbsp;$nome");
            if ($_SESSION['votigenitori'] == "yes")
                menu_item('../valutazioni/visvaltut.php', 'VOTI');
            if ($_SESSION['notegenitori'] == "yes")
                menu_item('../note/sitnotealu.php', 'NOTE');
            if ($_SESSION['assenzegenitori'] == "yes")
                menu_item('../assenze/sitassalut.php', 'ASSENZE');
            menu_title_end();
            if ($_SESSION['argomentigenitori'] == "yes") {
                menu_title_begin("ARGOMENTI LEZIONI");
                menu_item('../lezioni/riepargomgen.php', 'VISUALIZZA ARGOMENTI per materia');
                menu_item('../lezioni/riepargomgendata.php', 'VISUALIZZA ARGOMENTI per data');
                menu_title_end();
            }
            if ($_SESSION['visualizzapagelle'] == 'yes') {
                menu_title_begin("PAGELLE");
                if ($_SESSION['numeroperiodi'] == 2) {
                    menu_item('../valutazioni/vispagper.php?periodo=Primo', 'Pagella primo quadrimestre');
                    menu_item('../valutazioni/vispagfin.php', 'PAGELLA FINALE');
                } else {
                    if ($_SESSION['numeroperiodi'] == 3) {
                        menu_item('../valutazioni/vispagper.php?periodo=Primo', 'Pagella primo trimestre');
                        menu_item('../valutazioni/vispagper.php?periodo=Secondo', 'Pagella secondo trimestre');
                        menu_item('../valutazioni/vispagfin.php', 'PAGELLA FINALE');
                    }
                }
                menu_title_end();
            }
            menu_title_begin('COLLOQUI');
            menu_item("../colloqui/visdisponibilita.php?idclasse=$idclasse", 'PRENOTAZIONE COLLOQUIO MATTUTINO');

            menu_item("../colloqui/prenotazionecolloqui.php", 'PRENOTAZIONE COLLOQUI POMERIDIANI');
            menu_item("../colloqui/riepilogocolloqui.php", 'RIEPILOGO APPUNTAMENTI COLLOQUI POMERIDIANI');

            menu_title_end();
            menu_title_begin('COMUNICAZIONI SCUOLA-FAMIGLIA');
            if ($_SESSION['utentesms'] != '')
                menu_item('../assenze/giustassonline.php', 'GIUSTIFICA ASSENZE');
            menu_item('../circolari/viscircolari.php', 'LEGGI CIRCOLARI');
            menu_item("../collegamenti/coll.php", 'VISUALIZZA COLLEGAMENTI WEB');
            if (!strpos($telcel, ",")) {
                if ($_SESSION['agg_dati_genitori'] == 'yes') {
                    menu_item("../alunni/mod_contatto.php", 'AGGIORNA DATI DI CONTATTO');
                }
            }
            menu_title_end();
            if (!$_SESSION['dischpwd']) {
                menu_title_begin('PASSWORD');
                menu_item('../password/cambpwd.php', 'CAMBIAMENTO PROPRIA PASSWORD');
                menu_title_end();
            }
        }
    }

    if ($tipoutente == 'T' & $_SESSION['gensolocomunicazioni'] == 'yes') {

        //  $sql = "SELECT * FROM tbl_tutori WHERE idutente='" . $_SESSION['idutente'] . "'";
        $sql = "SELECT * FROM tbl_alunni WHERE idalunno='" . $_SESSION['idutente'] . "'";
        $ris = eseguiQuery($con, $sql);
        if ($val = mysqli_fetch_array($ris)) {
            $idstudente = $val["idalunno"];
        }


        $sql = "select * from tbl_alunni where idalunno='$idstudente'";
        $ris = eseguiQuery($con, $sql);
        if ($val = mysqli_fetch_array($ris)) {
            $cognome = $val["cognome"];
            $nome = $val["nome"];
            $idstudente = $val["idalunno"];
            $idclasse = $val["idclasse"];
            $idesterno = $val["idesterno"];
            $pwesterna = $val["pwesterna"];
            $telcel = $val["telcel"];

            menu_title_begin('COMUNICAZIONI SCUOLA-FAMIGLIA');

            menu_item('../circolari/viscircolari.php', 'LEGGI CIRCOLARI');
            // menu_item("../colloqui/visdisponibilita.php?idclasse=$idclasse", 'PRENOTAZIONE COLLOQUIO');
            // menu_item("../collegamenti/coll.php", 'VISUALIZZA COLLEGAMENTI WEB');
            menu_title_end();
        }
    }


    if ($tipoutente == 'L') {

        //  $sql = "SELECT * FROM tbl_tutori WHERE idutente='" . $_SESSION['idutente'] . "'";
        $sql = "SELECT * FROM tbl_alunni WHERE idalunno='" . ($_SESSION['idutente'] - 2100000000) . "'";
        $ris = eseguiQuery($con, $sql);
        if ($val = mysqli_fetch_array($ris)) {
            $idstudente = $val["idalunno"];
        }


        $sql = "select * from tbl_alunni where idalunno='$idstudente'";
        $ris = eseguiQuery($con, $sql);
        if ($val = mysqli_fetch_array($ris)) {
            $cognome = $val["cognome"];
            $nome = $val["nome"];
            $idstudente = $val["idalunno"];
            $idclasse = $val["idclasse"];
            $idesterno = $val["idesterno"];
            $pwesterna = $val["pwesterna"];
            $telcel = $val["telcel"];

            // print "<br/><button onclick=\"window.open('../assenze/sitassalut.php'>ASSENZE</a>";
            menu_title_begin("SITUAZIONE ALUNNO $cognome&nbsp;$nome");
            if ($_SESSION['votigenitori'] == "yes")
                menu_item('../valutazioni/visvaltut.php', 'VOTI');
            menu_item('../note/sitnotealu.php', 'NOTE');
            menu_item('../assenze/sitassalut.php', 'ASSENZE');
            menu_title_end();

            // VERIFICO SE L'ALUNNO E' UN RAPPRESENTANTE DI CLASSE
            if ($_SESSION['livello_scuola'] == '4') {
                // $query = "select * from tbl_classi where rappresentante1=$idstudente or rappresentante2=$idstudente";
                // $riscontr = eseguiQuery($con,$query);
                // if (mysqli_num_rows($riscontr) != 0)
                // {
                menu_title_begin("ASSEMBLEE DI CLASSE");
                menu_item('../assemblee/assricgen.php', 'ASSEMBLEE DI CLASSE');
                menu_title_end();
                //}
            }
            if ($_SESSION['argomentigenitori'] == "yes") {
                menu_title_begin("ARGOMENTI LEZIONI");
                menu_item('../lezioni/riepargomgen.php', 'VISUALIZZA ARGOMENTI per materia');
                menu_item('../lezioni/riepargomgendata.php', 'VISUALIZZA ARGOMENTI per data');
                menu_title_end();
            }
            menu_title_begin("LEZIONI A DISTANZA");
            menu_item('../docenti/elencocollegamentiwebex.php', 'VISUALIZZA COLLEGAMENTI DOCENTI');
            menu_title_end();
            menu_title_begin('PASSWORD');
            menu_item('../password/cambpwd.php', 'CAMBIAMENTO PROPRIA PASSWORD');
            menu_title_end();
            menu_title_begin('COMUNICAZIONI');
            menu_item('../circolari/viscircolari.php', 'LETTURA CIRCOLARI');
            menu_title_end();
        }
    }

    menu_close();

    print "</td>";

    /*
     * GESTIONE AVVISI
     */







    if ($tipoutente == 'D' | $tipoutente == 'S' | $tipoutente == 'T' | $tipoutente == 'A' | $tipoutente == 'E' | $tipoutente == 'L') {

        $dataoggi = date('Y-m-d');

        print ("<td valign=top>");

        //VERIFICO PRESENZA ASSEMBLEE DI CLASSE DA AUTORIZZARE
        if ($_SESSION['livello_scuola'] == '4') {
            if ($tipoutente == 'S' | $tipoutente == 'P') {
                $query = "SELECT DISTINCT * FROM tbl_assemblee
				  WHERE (autorizzato=0) 
				  AND ((docenteconcedente1!=0 AND concesso1=1) AND (docenteconcedente2=0) OR (docenteconcedente2!=0 AND concesso2=1))";
                $ris = eseguiQuery($con, $query);
                if (mysqli_num_rows($ris) > 0) {
                    print ("<center><br><i><b><font color='red'><big><big>Ci sono assemblee da autorizzare! <a href='../assemblee/assstaff.php'>Esamina ora!</a></big></big></font></b></i><br/></center>");
                    print ("<br/>");
                }
            }

            //VERIFICO PRESENZA RICHIESTE DI ASSEMBLEE DI CLASSE
            if ($tipoutente == 'D' | $tipoutente == 'S') {
                $query = "SELECT * FROM tbl_assemblee 
		  WHERE ((docenteconcedente1=$idutente AND concesso1=0)
                        OR (docenteconcedente2=$idutente AND concesso2=0))
                        AND (rappresentante1<>0 and rappresentante2<>0)";
                $ris = eseguiQuery($con, $query);
                if (mysqli_num_rows($ris) > 0) {
                    print ("<center><br><i><b><font color='red'><big><big>Ci sono richieste di assemblee da visionare! <a href='../assemblee/assdoc.php'>Esamina ora!</a></big></big></font></b></i><br/></center>");
                    print ("<br/>");
                }
            }
        }
        //
        // VERIFICO PRESENZA CIRCOLARI NON LETTE
        //
        $dataoggi = date('Y-m-d');
        $query = "select * from tbl_diffusionecircolari,tbl_circolari
							  where tbl_diffusionecircolari.idcircolare=tbl_circolari.idcircolare
							  and idutente='" . $_SESSION['idutente'] . "'
							  and (isnull(datalettura) or datalettura='0000-00-00')
                                                          and (isnull(dataconfermalettura) or dataconfermalettura='0000-00-00')
							  and datainserimento<='$dataoggi'";
        // print "tttt ".inspref($query);
        $ris = eseguiQuery($con, $query);
        if (mysqli_num_rows($ris) > 0) {
            print ("<center><br><i><b><font color='red'><big><big>Ci sono circolari non lette! <a href='../circolari/viscircolari.php'>Leggi ora!</a></big></big></font></b></i><br/></center>");
            print ("<br/>");
        }

        //
        // VERIFICO PRESENZA COLLOQUI
        //

        if ($tipoutente == "D" | $tipoutente == "S") {
            $dataoggi = date('Y-m-d');
            $oraattuale = date('H:i');
            //$datadomani=aggiungi_giorni($dataoggi,1);
            //$datadopodomani=aggiungi_giorni($dataoggi,2);
            //
            //  PRENOTAZIONI IN SOSPESO
            //
            $query = "select * from tbl_prenotazioni, tbl_orericevimento
								  where tbl_prenotazioni.idoraricevimento=tbl_orericevimento.idoraricevimento
								  and iddocente=" . $_SESSION['idutente'] . "
								  and data>='$dataoggi'
								  and tbl_prenotazioni.valido
								  and conferma=1";

            // print "tttt ".inspref($query);
            $ris = eseguiQuery($con, $query);
            if (mysqli_num_rows($ris) > 0) {

                print ("<center><br><i><b><font color='red'>Ci sono prenotazioni per colloqui a cui rispondere! <a href='../colloqui/visrichieste_doc.php'>Rispondi ora!</a></font></b></i><br/></center>");
                print ("<br/>");
            }

            //
            //  COLLOQUI IMMINENTI
            //
            $query = "select *,tbl_prenotazioni.note as notaprenotazione from tbl_prenotazioni,tbl_orericevimento,tbl_alunni,tbl_orario
								  where tbl_prenotazioni.idoraricevimento=tbl_orericevimento.idoraricevimento
								  and tbl_prenotazioni.idalunno=tbl_alunni.idalunno
								  and tbl_orericevimento.idorario=tbl_orario.idorario
								  and iddocente=" . $_SESSION['idutente'] . "
								  and data>='$dataoggi'
								  and tbl_prenotazioni.valido
								  and conferma in (2,4)";

            $ris = eseguiQuery($con, $query);
            if (mysqli_num_rows($ris) > 0) {
                while ($rec = mysqli_fetch_array($ris)) {

                    if ($rec['data'] > $dataoggi | $oraattuale < substr($rec['fine'], 0, 5)) {
                        if ($rec['conferma'] == 2)
                            print ("<center><br><i><b><font color='red'>Colloquio con genitore di " . $rec['cognome'] . " " . $rec['nome'] . " il " . data_italiana($rec['data']) . ". " . $rec['notaprenotazione'] . "</font></b></i><br/></center>");

                        if ($rec['conferma'] == 4)
                            print ("<center><br><i><b><font color='red'>Colloquio online con genitore di " . $rec['cognome'] . " " . $rec['nome'] . " il " . data_italiana($rec['data']) . ". " . $rec['notaprenotazione'] . "</font></b></i><br/></center>");
                        print ("<br/>");
                    }
                }
            }
        }

        if ($tipoutente == "T") {
            $dataoggi = date('Y-m-d');
            //$datadomani=aggiungi_giorni($dataoggi,1);
            //$datadopodomani=aggiungi_giorni($dataoggi,2);
            $oraattuale = date('H:i');

            //
            //  COLLOQUI IMMINENTI
            //
            $query = "select *,tbl_prenotazioni.note as notaprenotazione from tbl_prenotazioni,tbl_orericevimento,tbl_docenti,tbl_orario
								  where tbl_prenotazioni.idoraricevimento=tbl_orericevimento.idoraricevimento
								  and tbl_orericevimento.iddocente=tbl_docenti.iddocente
								  and tbl_orericevimento.idorario=tbl_orario.idorario
								  and idalunno=" . $_SESSION['idutente'] . "
								  and data>='$dataoggi'
								  and tbl_prenotazioni.valido
								  and conferma in (2,4)";

            $ris = eseguiQuery($con, $query);
            if (mysqli_num_rows($ris) > 0) {
                while ($rec = mysqli_fetch_array($ris)) {

                    if ($rec['data'] > $dataoggi | $oraattuale < substr($rec['fine'], 0, 5)) {
                        if ($rec['conferma'] == 2)
                            print ("<center><br><i><b><font color='red'>Colloquio con Prof. " . $rec['cognome'] . " " . $rec['nome'] . " il " . data_italiana($rec['data']) . " or. ricev. " . substr($rec['inizio'], 0, 5) . " - " . substr($rec['fine'], 0, 5) . "<br>" . $rec['notaprenotazione'] . "</a></font></b></i><br/></center>");
                        if ($rec['conferma'] == 4)
                            print ("<center><br><i><b><font color='red'>Colloquio <a href='" . $rec['collegamentowebex'] . "'>online</a> con Prof. " . $rec['cognome'] . " " . $rec['nome'] . " il " . data_italiana($rec['data']) . " or. ricev. " . substr($rec['inizio'], 0, 5) . " - " . substr($rec['fine'], 0, 5) . "<br>" . $rec['notaprenotazione'] . "</a></font></b></i><br/></center>");

                        print ("<br/>");
                    }
                }
            }

            //
            //  ANNOTAZIONI RECENTI
            //

            $idclassealunno = estrai_classe_alunno($_SESSION['idutente'], $con);
            $datalimiteinferiore = aggiungi_giorni(date('Y-m-d'), -1);
            $query = "select * from tbl_annotazioni,tbl_docenti
                where tbl_annotazioni.iddocente=tbl_docenti.iddocente
                    and idclasse=$idclassealunno
                    and data>'$datalimiteinferiore'
                    and visibilitagenitori=true
                    order by data";

            $ris = eseguiQuery($con, $query);
            if (mysqli_num_rows($ris) > 0) {
                while ($rec = mysqli_fetch_array($ris)) {


                    print ("<center><br><i>" . data_italiana($rec['data']) . "</i><b><font color='green'><br> " . $rec['testo'] . " <small>(" . $rec['cognome'] . " " . $rec['nome'] . ")<big></font></b><br/></center>");
                    print ("<br/>");
                }
            }
        }

        if ($tipoutente == "L") {


            //
            //  ANNOTAZIONI RECENTI
            //

            $idclassealunno = estrai_classe_alunno($_SESSION['idutente'] - 2100000000, $con);
            $datalimiteinferiore = aggiungi_giorni(date('Y-m-d'), -1);
            $query = "select * from tbl_annotazioni,tbl_docenti
                where tbl_annotazioni.iddocente=tbl_docenti.iddocente
                    and idclasse=$idclassealunno
                    and data>'$datalimiteinferiore'
                    and visibilitaalunni=true
                    order by data";

            $ris = eseguiQuery($con, $query);
            if (mysqli_num_rows($ris) > 0) {
                while ($rec = mysqli_fetch_array($ris)) {


                    print ("<center><br><i>" . data_italiana($rec['data']) . "</i><b><font color='green'><br> " . $rec['testo'] . " <small>(" . $rec['cognome'] . " " . $rec['nome'] . ")<big></font></b><br/></center>");
                    print ("<br/>");
                }
            }
        }

        //
        // VERIFICO PRESENZA AVVISI
        //
        $query = "select * from tbl_avvisi where inizio<='$dataoggi' and fine>='$dataoggi' and LOCATE('$tipoutente',destinatari)<>0 order by inizio desc";
        $ris = eseguiQuery($con, $query);
        while ($val = mysqli_fetch_array($ris)) {
            $inizio = data_italiana($val["inizio"]);
            $oggetto = $val["oggetto"];
            $testo = inserisci_parametri($val["testo"], $con);   // TTTT Modifica per parametrizzazione messaggi
            print ("<center><i>$inizio</i><br/>");
            print ("<b>$oggetto</b><br/></center>");
            print (html_entity_decode($testo, ENT_QUOTES, 'UTF-8') . "<br/><br/><br/>");
        }

        print ("</td></tr></table>");
    } else {  // ADMIN e PRESIDE
        $dataoggi = date('Y-m-d');
        print ("<td valign=top>");
        if ($tipoutente == 'M') {
            //
            //  VERIFICO PRESENZA AGGIORNAMENTI
            //
            //   inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§1");
            $idscuola = md5($_SESSION['nomefilelog']);
            //   inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§1");
            //print "<iframe style='visibility:hidden;display:none' src='http://www.lampschool.net/test/testesist.php?ids=$idscuola&nos=$_SESSION['nome_scuola']&cos=$_SESSION['comune_scuola']'></iframe>";
            // print "<iframe src='http://www.lampschool.net/test/testesist.php?ids=$idscuola&nos=$_SESSION['nome_scuola']&cos=$_SESSION['comune_scuola']'></iframe>";
            /* $url = "http://www.lampschool.net/test/testesist.php?ids=$idscuola&nos=$_SESSION['nome_scuola']&cos=$_SESSION['comune_scuola']&ver=$_SESSION['versioneprecedente']&asc=$_SESSION['annoscol']";
              $url = str_replace(" ", "_", $url);
              $ch = curl_init();
              $timeout = 5;
              curl_setopt($ch, CURLOPT_URL, $url);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
              $data = curl_exec($ch);
              curl_close($ch);
              echo $data; */
            //  inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§1");
            /*  $ch = curl_init();

              curl_setopt($ch, CURLOPT_URL, 'http://www.lampschool.net/test/testesist.php?ids=$idscuola&nos=$_SESSION['nome_scuola']&cos=$_SESSION['comune_scuola']');
              curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
              curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);

              curl_exec($ch);
              curl_close($ch);
             */

            $risultato = controlloNuovaVersione();
            $esito = $risultato['esito'];
            $nuovaVersione = $risultato['versione'];

            if ($esito) {
                print "<center><h5><font color='red'>E' disponibile sul sito di LAMPSchool la versione $nuovaVersione</font></h5></center>";
            }

            print $_SERVER['HTTP_USER_AGENT'];
            //
            // FINE VERIFICA AGGIORNAMENTI
            //
        }
        //inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "2");
        $query = "select * from tbl_avvisi where inizio<='$dataoggi' and fine>='$dataoggi' order by inizio desc";
        $ris = eseguiQuery($con, $query);
        //inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§3");
        while ($val = mysqli_fetch_array($ris)) {
            $inizio = data_italiana($val["inizio"]);
            $oggetto = $val["oggetto"];
            $destinatari = $val["destinatari"];
            $testo = $val["testo"];
            print ("<center><i>$inizio</i><br/>");
            print ("<b>$oggetto</b><br/>");
            print ("<b>$destinatari</b><br/></center>");
            print (html_entity_decode($testo, ENT_QUOTES, 'UTF-8') . "<br/><br/><br/>");
        }


        print("</td></tr></table>");
    }
} // Fine else cambiamento password

mysqli_close($con);
stampa_piede("");

// Crea il menu'

function menu_open($enable = TRUE) {
    $enable and print "\n<form id='formMenu' method='POST'>\n<div id='accordion'>";
}

// Chiude il menu'

function menu_close($enable = TRUE) {
    $enable and print "</div>\n</form>\n";
}

// Disegna il titolo contenitore del menu'

function menu_title_begin($label, $enable = TRUE) {
    $enable and print "\n<h3>$label</h3><div>";
}

// Chiude il titolo contenitore

function menu_title_end($enable = TRUE) {
    $enable and print "\n</div>";
}

// Disegna una voce del menu'

function menu_item($url, $label, $enable = TRUE) {

    $enable and print "\n<button onclick=\"setAction('$url');\" class='button'>$label</button>";
}

function menu_item_new_page($url, $label, $enable = TRUE) {
// $enable and print "\n<button onclick=\"window.open('$url','_self');\" class='button'>$label</button>";
// permette di cambiare l'attributo action della form
// la function setAction è definita nella sezione HEAD
// in questo modo tutto il menu è compreso nella form con il metodo POST

    $enable and print "\n<button onclick=\"setAction('$url');\" class='button' formtarget=\"_blank\">$label</button>";
}

// Disegna una riga vuota nel menu'

function menu_separator($titolo) {
    // print "\n<p>&nbsp;</p>";
    print "<br>$titolo<br>";
}
