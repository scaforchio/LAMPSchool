<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2015 Pietro Tamburrano
  Copyright (C) 2023 Pietro Tamburrano, Vittorio Lo Mele
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

$index = 0;
$trig = false;

$urlorigine = $_SERVER['HTTP_REFERER'];
if (isset($_SERVER['HTTPS'])) {
    $urlattuale = 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['SERVER_NAME'];
} else {
    $urlattuale = 'http://' . $_SERVER['SERVER_NAME'];
}

if ($urlattuale == substr($urlorigine, 0, strlen($urlattuale))) {
    $origineok = true;
}

// il controllo dell'origine deve essere saltato in caso di accesso
// tramite OIDC a profilo singolo, siccome il redirect verso LAMPSchool
// da parte dell'IDP OIDC, passando direttamente senza bisogno di un clic di scelta
// sul profilo da usare, non contiene l'header Referer, quindi il controllo fallisce
if ($_SESSION["oidc-step3"] == true && !isset($_SESSION["oidc_multiprofile"])) {
    $origineok = true;
}

// salta il controllo dell'origine se in devmode
if ($_SESSION["devmode"] == true) {
    $origineok = true;
}

//VERIFICO CHE LA SESSIONE NON SIA SCADUTA
if (isset($_SESSION['prefisso'])) {

    require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
    require_once '../lib/funzioni.php';
} else {
    stampa_sessionescaduta();
    die;
}

// VERIFICO CHE NON SIA RICHIESTO IL TOKEN
if (!$_SESSION['tokenok']) {
    header("location: login.php?messaggio=Errore token&suffisso=" . $_SESSION['suffisso']);
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

$json = leggeFileJSON('../lampschool.json');
$_SESSION['versione'] = $json['versione'];

// AZZERO LA SESIONE DEL REGISTRO
$_SESSION['classeregistro'] = "";
$ultimoaccesso = "";
if (!$con) {
    die("<h1> Connessione al server fallita </h1>");
}
$JSdisab = is_stringa_html('js_enabled') ? stringa_html('js_enabled') : '0';

if ($JSdisab == 1) {
    die("<center><b>Attenzione! Abilitare Java Script per utilizzare LAMPSchool!</b></center>");
}

$cambiamentopassword = false;
if ($_SESSION['tipoutente'] != 'E') {
    if (!$_SESSION['accessouniversale']) {
        $sql = "SELECT unix_timestamp(ultimamodifica) AS ultmod FROM " . $_SESSION['prefisso'] . "tbl_utenti WHERE userid='" . $_SESSION['userid'] . "'";
        $data = mysqli_fetch_array(eseguiQuery($con, $sql, false));
        $dataultimamodifica = $data['ultmod'];
        $dataodierna = time();
        $giornidiff = differenza_giorni($dataultimamodifica, $dataodierna);
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
    header("location: login.php?messaggio=Utente sconosciuto&suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Menu generale";
$script = "
<style>
.lscontainer {
    margin-left: 0px;
    margin-right: 0px;
    display: flex;
}
#lsheader{
    margin-bottom: 0px !important;
}
</style>
<link rel='stylesheet' type='text/css' href='../lib/js/sidebar/sidebarjs.min.css' />
";

stampa_head_new($titolo, "", $script, "SDMAPTEL");
if ($_SESSION['ultimoaccesso'] != "") {
    $ult = " <b>(Ultimo accesso: " . $_SESSION['ultimoaccesso'] . ")</b>";
} else {
    $ult = "";
}

if ($_SESSION['sitoinmanutenzione'] == "yes" & $_SESSION['tipoutente'] != 'M') {
    stampa_testata_new("MENU PRINCIPALE $ult", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
    alert("Registro in manutenzione!", "Aspettare qualche ora e ritentare l'accesso...", "danger", "gear");
    stampa_piede_new();
    die;
}


if ($cambiamentopassword) {
    stampa_testata_new("MENU PRINCIPALE $ult", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
    alert("Password scaduta!", "<a href='../password/cambpwd.php'>Cambia password</a>", "danger", "key");
    stampa_piede_new();
    die;
}
?>

<body>
    <div class="flex-shrink-0 sidebar-fix" sidebarjs>
        <ul class="list-unstyled p-3 w-400 scrolly">
            <span class="funzionemenu">
            MENU PRINCIPALE <br> <?php echo $ult; ?>
        <hr>
    </span>
<?php

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
    menu_title_end();
}

if ($tipoutente == 'D') {

    menu_title_begin('REGISTRO DI CLASSE', $icon="journal-bookmark-fill");
    menu_item('../regclasse/riepgiorno.php', 'Visualizza Giornata', $icon="calendar-fill");
    menu_item('../regclasse/riepsett.php', 'Visualizza Settimana', $icon="calendar-week");
    menu_item('../regclasse/annotaz.php', 'Annotazioni su Registro', $icon="chat-quote-fill");
    menu_item('../regclasse/ricannotaz.php', 'Ricerca Annotazioni', $icon="search");
    menu_item('../regclasse/CRUDannotazioni.php?mod=1&can=1&ins=1', 'Gestisci Annotazioni', $icon="chat-left-quote-fill");
    menu_item('../classi/CRUDevacuazione.php', 'Nomine ALUNNI per Evacuazione', $icon="building-fill-down");
    menu_item('../evacuazione/evacuazione.php', 'Gestione Modulo Evacuazione', $icon="building-fill-exclamation");
    if ($_SESSION['livello_scuola'] == '4') {
        menu_item("../assemblee/assdoc.php", 'Assemblee di Classe', $icon="person-raised-hand");
    }
    menu_title_end();
    menu_title_begin('ASSENZE', $icon="journal-x");
    menu_item('../assenze/sitassmens.php', 'Situazione Mensile', $icon="calendar-month");
    menu_item('../assenze/visgiustifiche.php', 'Elimina Giustifiche', $icon="trash-fill");
    menu_title_end();
    menu_title_begin('LEZIONI', $icon="view-list");
    menu_item('../lezioni/sitleztota.php', 'Tabellone di Riepilogo', $icon="clipboard");

    if ($norm)
        menu_item('../lezioni/riepargom.php', 'Riepilogo Argomenti Svolti', $icon="clock-history");
    menu_item('../lezioni/lezsupp.php', 'Inserisci Supplenza', $icon="file-plus");
    if ($norm)
        menu_item('../lezionigruppo/lezgru.php', 'Lezioni a Gruppi di Alunni', $icon="people-fill");

    menu_item('../contr/verifsovrappdoc.php', 'Controlla sovrapposizione lezioni', $icon="card-checklist");
    menu_item("../lezioni/vis_lez.php?iddocente=$idutente", "Correzione proprie lezioni", $icon="pencil-square");
    menu_item("../lezionigruppo/vis_lez_gru.php?iddocente=$idutente", "Correzioni proprie lezioni a gruppi", $icon="person-fill-check");
    if ($norm)
        menu_item('../lezioni/riepargomcert.php?modo=norm', 'Riepilogo Argomenti Sostegno', $icon="arrow-clockwise");
    menu_title_end();
    menu_title_begin('NOTE DISCIPLINARI', $icon="hand-thumbs-down-fill");
    menu_item('../note/notecl.php', 'Inserisci Note di Classe', $icon="people");
    menu_item('../note/ricnotecl.php', 'Ricerca Note di Classe', $icon="search-heart-fill");
    menu_item('../note/noteindmul.php', 'Inserisci Note Individuali', $icon="person-fill-exclamation");
    menu_item('../note/ricnoteind.php', 'Ricerca Note Individuali', $icon="search-heart");

    menu_title_end();
    menu_title_begin('OSSERV. E DIARIO CLASSE', $icon="journal-medical");

    menu_item('../valutazioni/osssist.php', 'Inserisci Osservazioni Sistematiche', $icon="patch-exclamation-fill");
    menu_item('../valutazioni/ricosssist.php', 'Ricerca Osservaz. Sistematiche', $icon="search");
    menu_item('../valutazioni/stampaosssist.php', 'Stampa Osservaz. Sistematiche', $icon="printer-fill");
    menu_item('../valutazioni/diariocl.php', 'Diario di Classe', $icon="passport");
    menu_item('../valutazioni/ricdiariocl.php', 'Ricerca su Diario di Classe', $icon="search");
    menu_item('../valutazioni/stampadiariocl.php', 'Stampa Diario di Classe', $icon="printer-fill");
    if ($sost) {
        menu_item('../valutazioni/osssistcert.php', 'Osservaz. Sistematiche Alunni Certificati', $icon="patch-exclamation-fill");
        menu_item('../valutazioni/ricosssistcert.php', 'Ricerca Osservaz. Sistematiche Al. Cert.', $icon="search");
        menu_item('../valutazioni/stampaosssistcert.php', 'Stampa Osservaz. Sistematiche Al. Cert.', $icon="printer-fill");
    }
    menu_title_end();
    menu_title_begin('VOTI', $icon="list-ol");
    menu_item('../valutazioni/prospettovoti.php', 'Prospetto Voti Classe', $icon="card-list");
    menu_item('../valutazioni/proposte.php', 'Medie e Proposte Voto', $icon="question-circle");
    menu_item('../valutazionecomportamento/valcomp.php', 'Voto Comportamento', $icon="emoji-angry");
    menu_item('../valutazionecomportamento/sitvalcompalu.php', 'Situazione Voti Comportamento', $icon="clock-history");
    menu_item('../obiettivi/obproposteint.php', 'Proposte Valutazioni Intermedie per Alunni', $icon="check");
    menu_item('../obiettivi/obproposte.php', 'Proposte Valutazioni Finali per Alunni', $icon="check-all");

    menu_title_end();
    if ($_SESSION['livello_scuola'] != 4) {
        menu_title_begin('CERTIFICAZIONE COMPETENZE');
        menu_item('../obiettivi/CRUDobiettivi.php', 'GESTIONE OBIETTIVI MATERIE/CLASSE');
        menu_item('../obiettivi/scrutobiettiviintermedio.php', 'SCRUTINIO INTERMEDIO OBIETTIVI');
        menu_item('../obiettivi/scrutobiettivifinale.php', 'SCRUTINIO FINALE OBIETTIVI');
        menu_title_end();
    }

    if (estrai_docente_coordinatore($idutente, $con)) {
        menu_title_begin('FUNZIONI COORDINATORE', $icon="person-lines-fill");

        menu_item('../valutazioni/riepvoticlasse.php', 'Situazione Voti Medi per Classe', $icon="clipboard-check-fill");
        menu_item('../valutazioni/visvalpre.php', 'Situazione per Alunno', $icon="person-bounding-box");
        menu_item('../note/stampanote.php', 'Stampa note di Classe', $icon="printer-fill");
        menu_item('../assenze/sitassmens.php', 'Situazione Mensile Assenze', $icon="calendar-month");
        menu_item('../assenze/sitasstota.php', 'Situazione Totale Assenze', $icon="calendar-fill");
        menu_item('../assenze/sitassprob.php', 'Situazioni PROBLEMATICHE Assenze', $icon="exclamation-diamond");
        menu_item('../assenze/sitassmate.php', 'Situazioni Assenze per MATERIA', $icon="bookmark-x-fill");
        menu_item('../assenze/deroghe.php', 'Deroghe Assenze', $icon="question-octagon-fill");
        menu_item('../alunni/CRUD_autorizzazioni.php?soloclasse=yes', 'Gestione autorizzazioni uscita anticipata con classe', $icon="box-arrow-left");
        menu_item('../assenze/visderoghe.php', 'Situazione Deroghe Assenze', $icon="journal-text");
        menu_item('../scrutini/riepvoti.php', 'Tabelllone Scrutini Intermedi', $icon="file-spreadsheet");
        menu_item('../scrutini/riepvotifinali.php', 'Tabellone Scrutini Finali', $icon="table");
        if ($_SESSION['livello_scuola'] == '4') {
            menu_item('../scrutini/riepvotifinali.php?integrativo=yes', 'Scrutini Integrativi', $icon="node-plus");
        }
        if ($_SESSION['livello_scuola'] == '3' || $_SESSION['livello_scuola'] == '2') {
            menu_item('../consorientativo/cotabellone.php?tipoaccesso=coordinatore', 'Consigli Orientativi');
        }
        menu_item('../scrutini/riepproposte.php', 'Riepilogo Proposte di Voto', $icon="card-heading");
        menu_item('../documenti/stampafirmaprogrammi.php?docente=' . $idutente, 'Stampe per Presa Visione Programmi', $icon="printer");
        menu_item('../documenti/stampafirmacdc.php?docente=' . $idutente, 'Stampe Firme Presenze Cons. di Classe', $icon="printer");

        menu_item('../documenti/documenticlasse.php', 'Elenco Documenti Classe', $icon="file-earmark");
        menu_title_end();
    }

    if ($norm & $_SESSION['valutazionepercompetenze'] == 'yes') {
        menu_title_begin('VALUTAZIONE COMPETENZE', $icon="motherboard-fill");
        menu_item('../valutazioni/valabilcono.php', 'Verifiche', $icon="8-square-fill");
        menu_item('../valutazioni/valaluabilcono.php?modo=norm', 'Inserisci Valutazioni Alunni', $icon="person-add");
        menu_item('../valutazioni/sitvalalu.php', 'Visualizza Situazione Alunno', $icon="person-fill");
        menu_item('../valutazioni/sitvalobi.php', 'Visualizza Situazione per Obiett.', $icon="table");

        menu_title_end();
    }

    if ($norm & $_SESSION['valutazionepercompetenze'] == 'yes') {
        menu_title_begin('PROGRAMMAZIONE', $icon="menu-app");
        menu_item('../programmazione/compdo.php', 'Gestione Competenze', $icon="book-half");
        menu_item('../programmazione/abcodo.php', 'Gestione Abilità/Conoscenze', $icon="emoji-smile-fill");
        menu_item('../programmazione/confimportaprogr.php', 'Importa Programmaz. Scolast.', $icon="cloud-download");
        menu_item('../programmazione/copiaprogdoc.php', 'Copia Programmazione', $icon="copy");
        menu_item('../programmazione/visprogrdo.php', 'Visualizza Programma', $icon="eye-fill");
        menu_item('../programmazione/modivoceprog.php', 'Correggi Abilità/Conoscenze', $icon="pencil-square");
        menu_item('../programmazione/modicompetenza.php', 'Correggi Comptenze', $icon="pencil-square");
        menu_item('../programmazione/esportaprogrammazioneincsv.php', 'Esporta programmazione in CSV', $icon="cloud-download");
        menu_item('../programmazione/importaprogrammazionedacsv.php', 'Importa programamzione da CSV', $icon="filetype-csv");
        menu_item('../progrcert/seletipoprogr.php', 'Tipo Programmaz. ALUNNI CERTIFICATI', $icon="person-fill-check");
        menu_item('../progrcert/visprogralu.php', 'Visualizza Programmi ALUNNI CERTIFICATI', $icon="eye-fill");
        menu_title_end();
    }


    menu_title_begin('DOCUMENTI', $icon="file-earmark-post");

    menu_item('../documenti/documprog.php?tipodoc=pia', 'Piani di Lavoro', $icon="file-binary-fill");
    menu_item('../documenti/documprog.php?tipodoc=pro', 'Programmi Svolti', $icon="file-medical-fill");
    menu_item('../documenti/documprog.php?tipodoc=rel', 'Relazioni Finali', $icon="files-alt");
    if ($norm)
        menu_item('../documenti/documenti.php', 'Documenti Alunno', $icon="file-earmark-person");

    menu_title_end();

    if ($sost) {
        menu_separator("SOSTEGNO");

        menu_title_begin('LEZIONI SOSTEGNO', $icon="person-fill-check");

        menu_item('../lezioni/lezcert.php', 'Inserimento e Modifica Lezioni Sostegno', $icon="calendar-fill");
        menu_item('../lezioni/riepargomcert.php?modo=sost', 'Riepilogo Argomenti Sostegno', $icon="calendar-range-fill");
        menu_item("../lezioni/vis_lez_cert.php?iddocente=$idutente", "Correzioni Proprie Lezioni", $icon="pencil-square");
        menu_title_end();
        menu_title_begin('VALUTAZIONE COMPETENZE SOST.', $icon="person-fill-check");
        menu_item('../valutazioni/valaluabilcono.php?modo=sost', 'Valutazioni Alunni Certificati', $icon="8-square-fill");
        if (!$norm)
            menu_item('../valutazioni/sitvalalu.php', 'Visualizza Situazione Alunno', $icon="person-badge");

        menu_title_end();
        menu_title_begin('PEI', $icon="person-fill-check");
        menu_item('../progrcert/seletipoprogr.php', 'Tipo Programmazione Alunni Cert.', $icon="person-arms-up");
        menu_item('../progrcert/visprogralu.php', 'Visualizza Programmi Alunni Cert.', $icon="eye");
        menu_item('../progrcert/compalu.php', 'Competenze PEI', $icon="journal-check");
        menu_item('../progrcert/abcoalu.php', 'Abilità/Conoscenze PEI', $icon="emoji-smile-fill");
        menu_item('../progrcert/modivoceprogalu.php', 'Correggi Abil./Con. Alunno Cert.', $icon="pencil-square");
        menu_item('../progrcert/modicompetenzaalu.php', 'Correggi Competenza Alunno Cert.', $icon="pencil");
        menu_item('../documenti/documenti.php?tipo=pei', 'Allegati PEI', $icon="link-45deg");
        menu_item('../pei/sele_stampa_pei.php?modo=sost', 'Stampa PEI', $icon="printer");
        menu_item('../pei/scarica_doc_pei.php?modo=sost', 'Scarica Documenti PEI', $icon="cloud-download-fill");
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
    menu_title_begin('GESTIONE COLLOQUI', $icon="people-fill");

    menu_item('../colloqui/visrichieste_doc.php', 'Prenotazioni Colloqui Mattutini', $icon="sun-fill");
    menu_item('../colloqui/visappuntamentidoc.php', 'Prenotazioni Colloqui Pomeridiani', $icon="moon-fill");

    menu_title_end();
    menu_title_begin('ALTRE FUNZIONI', $icon="box-fill");
    menu_item('../password/cambpwd.php', 'Cambia Password', $icon="key-fill");
    menu_item('../circolari/viscircolari.php', 'Leggi Circolari', $icon="newspaper");

    menu_item("../docenti/mod_contatto.php", 'Aggiorna Dati di Contatto', $icon="person-lines-fill");
    menu_item("../collegamenti/coll.php", 'Collegamenti WEB', $icon="link");
    menu_item('../ferie/richferie.php', 'Richiesta Astensione dal Lavoro', $icon="person-fill-slash");
    menu_item('../ferie/esameproprierichferie.php', 'Esamina Richieste di Ferie', $icon="eye-fill");
    menu_item('../docenti/visorario.php', 'Visualizza Orario', $icon="clock");
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
    menu_title_end();

    menu_title_begin('LEZIONI');
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
        menu_item("../assemblee/assdoc.php", 'ASSEMBLEE PROPRIE ORE');
        menu_item("../assemblee/ass.php", 'AUTORIZZAZIONE ASSEMBLEE');
        menu_item("../assemblee/contver.php", 'VERIFICA VERBALI');
        menu_item("../assemblee/visionaverbali.php", 'SITUAZIONE ASSEMBLEE');
        menu_item("../assemblee/reportassemblee.php", 'RAPPORTO PER DIRIGENTE');
        menu_item("../classi/CRUDrappresentanti.php", 'GESTIONE RAPPRESENTANTI');
        menu_title_end();
    }

    menu_title_begin('OSSERV. E DIARIO CLASSE');

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
        menu_item('../documenti/stampafirmacdc.php?docente=' . $idutente, 'STAMPE FIRMA PRESENZA CDC');

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
    menu_item('../valutazioni/prospettovoti.php', 'PROSPETTO VOTI');
    menu_item('../valutazioni/proposte.php', 'MEDIE E PROPOSTE DI VOTO');
    menu_item('../valutazioni/visvalpre.php', 'VISUALIZZA SITUAZIONE ALUNNO');
    menu_item('../valutazionecomportamento/valcomp.php', 'VOTO COMPORTAMENTO');
    menu_item('../valutazionecomportamento/sitvalcompalu.php', 'SITUAZIONE VOTI COMPORTAMENTO');
    menu_item('../obiettivi/obproposteint.php', 'PROPOSTE VALUTAZIONI INTERMEDIE PER ALUNNI');
    menu_item('../obiettivi/obproposte.php', 'PROPOSTE VALUTAZIONI FINALI PER ALUNNI');
    menu_title_end();

    if ($_SESSION['livello_scuola'] != 4) {
        menu_title_begin('CERTIFICAZIONE COMPETENZE');
        menu_item('../obiettivi/CRUDobiettivi.php', 'GESTIONE OBIETTIVI MATERIE/CLASSE');
        menu_item('../certcomp/ccproposte.php', 'PROPOSTE PER ALUNNI');
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
    menu_item('../sondaggi/sondaggi.php', 'GESTIONE SONDAGGI ALUNNI');

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
    menu_item('../contr/compleanni.php', 'COMPLEANNI DI OGGI');
    menu_item('../valutazioni/riepvoticlasse.php', 'RIEPILOGO MEDIE PER CLASSE');
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
    menu_item('../alunni/attr_classe.php', 'Attribuisci classe ad alunni');
    menu_item('../alunni/vis_alu_ricerca.php', 'Ricerca alunni');
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
    menu_title_end();
    if ($_SESSION['livello_scuola'] == '4') {
        menu_title_begin('ASSEMBLEE DI CLASSE');
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

    menu_title_begin('OSSERV. E DIARIO CLASSE');
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
        menu_item('../certcomp/cctabellone.php', 'TABELLONE CLASSE');
        menu_title_end();
    }
    menu_title_begin('PROGRAMMI');
    menu_item('../programmazione/visprogrdo.php', 'VISUALIZZA PROGRAMMI DOCENTI');
    menu_item('../programmazione/visprogrsc.php', 'VISUALIZZA PROGRAMMI SCOLAST.');
    menu_title_end();
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
    menu_item('../contr/compleanni.php', 'COMPLEANNI DI OGGI');
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
    menu_title_begin('IMPORTAZIONE');
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
    menu_item('../contr/admanualsync.php', 'Forza sincronizzazione AD');
    if ($_SESSION['oidc_enabled'] == 'yes')
        menu_item('../contr/oidcbindings.php', 'Bindings utenti Locali<->OIDC');
    menu_title_end();

    menu_separator("STRUMENTI DI SEGRETERIA");
    menu_title_begin('ANAGRAFICHE');
    menu_item('../alunni/vis_alu_cla.php', 'ALUNNI');
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
    menu_item('../assenze/gruppiritardi.php', 'GRUPPI RITARDI');
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
    menu_item('../contr/compleanni.php', 'COMPLEANNI DI OGGI');
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
    menu_item('../assenze/gruppiritardi.php', 'GRUPPI RITARDI');
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
    menu_item('../valutazioni/visvalpre.php', 'ASSENZE ALUNNO');
    menu_title_end();
    menu_title_begin('SCRUTINI');
    menu_item('../scrutini/riepvoti.php', 'SCRUTINI INTERMEDI');
    menu_item('../scrutini/riepvotifinali.php', 'SCRUTINI FINALI');
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

if ($tipoutente == 'T' & $_SESSION['gensolocomunicazioni'] == 'no') {
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
        menu_title_begin("SITUAZIONE ALUNNO", $icon="person-video2");
        if ($_SESSION['votigenitori'] == "yes")
            menu_item('../valutazioni/visvaltut.php', 'Voti', $icon="card-list");
        if ($_SESSION['notegenitori'] == "yes")
            menu_item('../note/sitnotealu.php', 'Note Disciplinari', $icon="hand-thumbs-down-fill");
        if ($_SESSION['assenzegenitori'] == "yes")
            menu_item('../assenze/sitassalut.php', 'Assenze', $icon="journal-x");
        menu_title_end();
        if ($_SESSION['argomentigenitori'] == "yes") {
            menu_title_begin("ARGOMENTI LEZIONI", $icon="calendar2-day");
            menu_item('../lezioni/riepargomgen.php', 'Visualizza Argomenti per MATERIA', $icon="book-half");
            menu_item('../lezioni/riepargomgendata.php', 'Visualizza Argomenti per DATA', $icon="calendar-date-fill");
            menu_title_end();
        }
        if ($_SESSION['visualizzapagelle'] == 'yes') {
            menu_title_begin("PAGELLE", $icon="file-binary-fill");
            if ($_SESSION['numeroperiodi'] == 2) {
                menu_item('../valutazioni/vispagper.php?periodo=Primo', 'Pagella primo quadrimestre', $icon="file-ruled");
                menu_item('../valutazioni/vispagfin.php', 'Pagella Finale', $icon="file-ruled-fill");
            } else {
                if ($_SESSION['numeroperiodi'] == 3) {
                    menu_item('../valutazioni/vispagper.php?periodo=Primo', 'Pagella primo trimestre');
                    menu_item('../valutazioni/vispagper.php?periodo=Secondo', 'Pagella secondo trimestre');
                    menu_item('../valutazioni/vispagfin.php', 'PAGELLA FINALE');
                }
            }
            menu_title_end();
        }
        menu_title_begin('PRENOT. COLLOQUI', $icon="people-fill");
        menu_item("../colloqui/visdisponibilita.php?idclasse=$idclasse", 'Colloqui Mattutini', $icon="brightness-high");

        menu_item("../colloqui/prenotazionecolloqui.php", 'Colloqui Pomeridiani', $icon="moon-fill");
        menu_item("../colloqui/riepilogocolloqui.php", 'Riepilogo Colloqui Pomeridiani', $icon="moon-stars-fill");

        menu_title_end();
        menu_title_begin('COMUNICAZIONI', $icon="newspaper");
        if ($_SESSION['abilgiustonline'] == 'yes')
            menu_item('../assenze/giustassonline.php', 'Giustifiche Online', $icon="check-square-fill");
        menu_item('../circolari/viscircolari.php', 'Circolari', $icon="newspaper");
        menu_item("../collegamenti/coll.php", 'Collegamenti WEB', $icon="link-45deg");
        if (!strpos($telcel, ",")) {
            if ($_SESSION['agg_dati_genitori'] == 'yes') {
                menu_item("../alunni/mod_contatto.php", 'Aggiorna Dati Contatto', $icon="person-lines-fill");
            }
        }
        menu_title_end();
        if (!$_SESSION['dischpwd']) {
            menu_title_begin('PASSWORD', $icon="key-fill");
            menu_item('../password/cambpwd.php', 'Cambia Password', $icon="key-fill");
            menu_title_end();
        }
    }
}


if ($tipoutente == 'T' & $_SESSION['gensolocomunicazioni'] == 'yes') {
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
        menu_title_end();
    }
}


if ($tipoutente == 'L') {

    $sql = "SELECT * FROM tbl_alunni WHERE idalunno='" . ($_SESSION['idutente'] - 2100000000) . "'";
    $ris = eseguiQuery($con, $sql);
    if ($val = mysqli_fetch_array($ris)) {
        $idstudente = $val["idalunno"];
    }

    $annoalunno = ottieniRigaClasseDaIdAlunno($idstudente, $con)['anno'];
    $listaAnniAmmessiString = explode(",", $_SESSION['anniannuario']);
    $vis_annuario = ($_SESSION['annuariopubblico'] == "yes" && 
        in_array($annoalunno, $listaAnniAmmessiString));

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

        menu_title_begin("SITUAZIONE ALUNNO", $icon="person-video2");
        if ($_SESSION['votigenitori'] == "yes")
            menu_item('../valutazioni/visvaltut.php', 'Voti', $icon="card-list");
        menu_item('../note/sitnotealu.php', 'Note Disciplinari', $icon="hand-thumbs-down-fill");
        menu_item('../assenze/sitassalut.php', 'Assenze', $icon="journal-x");
        menu_title_end();

        // VERIFICO SE L'ALUNNO E' UN RAPPRESENTANTE DI CLASSE (VERIFICO IL LIVELLO DELLA SCUOLA)
        if ($_SESSION['livello_scuola'] == '4') {
            menu_title_begin("ASSEMBLEE DI CLASSE", $icon="person-raised-hand");
            menu_item('../assemblee/assricgen.php', 'Gestione Assemblee Classe', $icon="journal-album");
            menu_title_end();
        }
        if ($_SESSION['argomentigenitori'] == "yes") {
            menu_title_begin("ARGOMENTI LEZIONI", $icon="calendar2-day");
            menu_item('../lezioni/riepargomgen.php', 'Visualizza per MATERIA', $icon="book-half");
            menu_item('../lezioni/riepargomgendata.php', 'Visualizza per DATA', $icon="calendar-date-fill");
            menu_title_end();
        }
        menu_title_begin("LEZIONI A DISTANZA", $icon="webcam");
        menu_item('../docenti/elencocollegamentiwebex.php', 'Link Docenti', $icon="camera-video-fill");
        menu_title_end();
        menu_title_begin('PASSWORD', $icon="key-fill");
        menu_item('../password/cambpwd.php', 'Modifica Password', $icon="key-fill");
        menu_title_end();
        menu_title_begin('COMUNICAZIONI', $icon="newspaper");
        menu_item('../circolari/viscircolari.php', 'Circolari', $icon="newspaper");
        if($vis_annuario)
            menu_item('../annuario/vis_annuario.php', 'Foto Annuario', $icon="people-fill");
        menu_title_end();
    }
}

?>
</ul>
</div>
<main class="widthcalc" sidebarjs-content> 
    <div class="gescontainer"><?php

stampa_testata_ges_new("MENU PRINCIPALE $ult", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
print("<div class='grow p-3' >");

// FINE GESTIONE MENU INIZIO GESTIONE AVVISI

if ($tipoutente == 'D' | $tipoutente == 'S' | $tipoutente == 'T' | $tipoutente == 'A' | $tipoutente == 'E' | $tipoutente == 'L') {
    $dataoggi = date('Y-m-d');

    //VERIFICO PRESENZA ASSEMBLEE DI CLASSE DA AUTORIZZARE
    if ($_SESSION['livello_scuola'] == '4') {
        if ($tipoutente == 'S' | $tipoutente == 'P') {
            $query = "SELECT DISTINCT * FROM tbl_assemblee
				  WHERE (autorizzato=0) 
				  AND ((docenteconcedente1!=0 AND concesso1=1) AND (docenteconcedente2=0) OR (docenteconcedente2!=0 AND concesso2=1))";
            $ris = eseguiQuery($con, $query);
            if (mysqli_num_rows($ris) > 0) {
                alert("Ci sono assemblee da autorizzare! <a class='alert-link' href='../assemblee/assstaff.php'>Esamina ora!</a>", "", "warning", "check2-square");
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
                alert("Ci sono richieste di assemblee da visionare! <a class='alert-link' href='../assemblee/assdoc.php'>Esamina ora!</a>", "", "warning", "eye");
            }
        }
    }

    $qqq = false;

    // VERIFICA COMPLEANNO UTENTE
    if ($tipoutente == 'D' | $tipoutente == 'S') { // docente
        $idc = $_SESSION['idutente'];
        $comp_query = "SELECT nome FROM `tbl_docenti` WHERE iddocente = '$idc' and MONTH(datanascita) = MONTH(CURRENT_DATE()) AND DAY(datanascita) = DAY(CURRENT_DATE())";
        $qqq = true;
    }

    if($tipoutente == 'L'){ // alunno
        $ida = $_SESSION['idstudente'];
        $comp_query = "SELECT nome FROM `tbl_alunni` WHERE idalunno = '$ida' and MONTH(datanascita) = MONTH(CURRENT_DATE()) AND DAY(datanascita) = DAY(CURRENT_DATE())";
        $qqq = true;
    }

    if($qqq){
        $comp_result = eseguiQuery($con, $comp_query);
        $comp_mostra = mysqli_num_rows($comp_result) == 1;
        $comp_nome = ucfirst(strtolower(mysqli_fetch_assoc($comp_result)['nome']));
    }
    
    if($comp_mostra && $qqq){
        alert("Buon compleanno, $comp_nome!", "", "dark", "cake2-fill");
    }

    // VERIFICO PRESENZA CIRCOLARI NON LETTE
    $dataoggi = date('Y-m-d');
    $query = "select * from tbl_diffusionecircolari,tbl_circolari
							  where tbl_diffusionecircolari.idcircolare=tbl_circolari.idcircolare
							  and idutente='" . $_SESSION['idutente'] . "'
							  and (isnull(datalettura) or datalettura='0000-00-00')
                                                          and (isnull(dataconfermalettura) or dataconfermalettura='0000-00-00')
							  and datainserimento<='$dataoggi'";
    $ris = eseguiQuery($con, $query);
    if (mysqli_num_rows($ris) > 0) {
        alert("<b>Ci sono circolari non lette!</b> <a class='alert-link' href='../circolari/viscircolari.php'>Leggi ora</a>", "", "warning", "eye");
    }

    // VERIFICO PRESENZA COLLOQUI
    if ($tipoutente == "D" | $tipoutente == "S") {
        $dataoggi = date('Y-m-d');
        $oraattuale = date('H:i');
        //  PRENOTAZIONI IN SOSPESO
        $query = "select * from tbl_prenotazioni, tbl_orericevimento
								  where tbl_prenotazioni.idoraricevimento=tbl_orericevimento.idoraricevimento
								  and iddocente=" . $_SESSION['idutente'] . "
								  and data>='$dataoggi'
								  and tbl_prenotazioni.valido
								  and conferma=1";
        $ris = eseguiQuery($con, $query);
        if (mysqli_num_rows($ris) > 0) {
            alert("Ci sono prenotazioni per colloqui a cui rispondere! <a href='../colloqui/visrichieste_doc.php'>Rispondi ora!</a>", "", "warning", "chat-dots");
        }

        //  COLLOQUI IMMINENTI
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
                        alert("Colloquio con genitore di " . $rec['cognome'] . " " . $rec['nome'] . " il " . data_italiana($rec['data']) . ". " . $rec['notaprenotazione'], "", "info", "person-raised-hand");
                    if ($rec['conferma'] == 4)
                        alert("Colloquio online con genitore di " . $rec['cognome'] . " " . $rec['nome'] . " il " . data_italiana($rec['data']) . ". " . $rec['notaprenotazione'], "", "info", "person-video3");
                }
            }
        }


        // PRENOTAZIONI POMERIDIANE
        $query = "select * from tbl_slotcolloqui,tbl_giornatacolloqui
				where tbl_slotcolloqui.idgiornatacolloqui=tbl_giornatacolloqui.idgiornatacolloqui
                                and iddocente=" . $_SESSION['idutente'] . "
				and data>='$dataoggi'";

        $ris = eseguiQuery($con, $query);
        if (mysqli_num_rows($ris) > 0) {
            alert("Ci sono prenotazioni per colloqui pomeridiani", "", "info");
        }
    }

    if ($tipoutente == "T") {
        $dataoggi = date('Y-m-d');
        $oraattuale = date('H:i');

        //  COLLOQUI IMMINENTI
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
                        alert("Colloquio con Prof. " . $rec['cognome'] . " " . $rec['nome'] . " il " . data_italiana($rec['data']) . " or. ricev. " . substr($rec['inizio'], 0, 5) . " - " . substr($rec['fine'], 0, 5), $rec['notaprenotazione'], "info", "person-raised-hand");
                    if ($rec['conferma'] == 4)
                        alert("Colloquio <a class='alert-link' href='" . $rec['collegamentowebex'] . "'>online</a> con Prof. " . $rec['cognome'] . " " . $rec['nome'] . " il " . data_italiana($rec['data']) . " or. ricev. " . substr($rec['inizio'], 0, 5) . " - " . substr($rec['fine'], 0, 5), $rec['notaprenotazione'], "info", "person-video3");
                }
            }
        }

        //  ANNOTAZIONI RECENTI
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
                if(substr(strtolower($rec['testo']), 0, 8) == "verifica" || substr(strtolower($rec['testo']), 0, 7) == "compito" || substr(strtolower($rec['testo']), 0, 4) == "quiz"){
                    $tipoalert = "alert-info";
                }else{
                    $tipoalert = "alert-primary";
                }
                annotazione($rec['data'], $rec['testo'], $rec['nome'], $rec['cognome'], $tipoalert);
            }
        }
    }

    if ($tipoutente == "L") {
        //  ANNOTAZIONI RECENTI
        $ida = $_SESSION['idutente'] - 2100000000;
        $idclassealunno = estrai_classe_alunno($ida, $con);
        $datalimiteinferiore = aggiungi_giorni(date('Y-m-d'), -1);
        $query = "select * from tbl_annotazioni,tbl_docenti
                where tbl_annotazioni.iddocente=tbl_docenti.iddocente
                    and idclasse=$idclassealunno
                    and data>'$datalimiteinferiore'
                    and visibilitaalunni=true
                    order by data";

        // controlla presenza di sondaggi senza risposta
        $res = eseguiQuery($con, 
        "SELECT tbl_rispostesondaggi.idsondaggio, 
        tbl_sondaggi.oggetto 
        FROM tbl_rispostesondaggi, 
        tbl_sondaggi 
        WHERE tbl_rispostesondaggi.idutente = $ida 
        AND tbl_rispostesondaggi.idopzione = -1 
        AND tbl_sondaggi.idsondaggio = tbl_rispostesondaggi.idsondaggio 
        AND tbl_sondaggi.attivo = 1"
        );

        while ($rec = mysqli_fetch_array($res)) {
            $idsondaggio = $rec['idsondaggio'];
            $btn = "<a href='../sondaggi/rispondi.php?id=$idsondaggio' class='alert-link'><b>RISPONDI</b></a>";
            $oggetto = $rec['oggetto'];
            alert("Hai un sondaggio da completare! $btn", "Oggetto: $oggetto", "warning", "bar-chart-fill");
        }

        $ris = eseguiQuery($con, $query);
        if (mysqli_num_rows($ris) > 0) {
            while ($rec = mysqli_fetch_array($ris)) {
                if(substr(strtolower($rec['testo']), 0, 8) == "verifica" || substr(strtolower($rec['testo']), 0, 7) == "compito" || substr(strtolower($rec['testo']), 0, 4) == "quiz"){
                    $tipoalert = "alert-info";
                }else{
                    $tipoalert = "alert-primary";
                }
                annotazione($rec['data'], $rec['testo'], $rec['nome'], $rec['cognome'], $tipoalert);
            }
        }

    }

    // VERIFICO PRESENZA AVVISI
    $query = "select * from tbl_avvisi where inizio<='$dataoggi' and fine>='$dataoggi' and LOCATE('$tipoutente',destinatari)<>0 order by inizio desc";
    $ris = eseguiQuery($con, $query);
    while ($val = mysqli_fetch_array($ris)) {
        $testo = inserisci_parametri($val["testo"], $con);   // TTTT Modifica per parametrizzazione messaggi
        $testok = html_entity_decode($testo, ENT_QUOTES, 'UTF-8');
        avviso($val["inizio"], $val["oggetto"], $testok);
    }

} else {
    $dataoggi = date('Y-m-d');
    print("<td valign=top>");
    if ($tipoutente == 'M') {
        $idscuola = md5($_SESSION['nomefilelog']);
        $risultato = controlloNuovaVersione();
        $esito = $risultato['esito'];
        $nuovaVersione = $risultato['versione'];

        if ($esito) {
            alert("Aggiornamento di LAMPSchool alla versione $nuovaVersione disponibile sulla pagina 'Release' della repository GitHub!", "", "danger", "git");
        }

        alert($_SERVER['HTTP_USER_AGENT']);
    }
    $query = "select * from tbl_avvisi where inizio<='$dataoggi' and fine>='$dataoggi' order by inizio desc";
    $ris = eseguiQuery($con, $query);
    while ($val = mysqli_fetch_array($ris)) {
        $testok = html_entity_decode($val["testo"], ENT_QUOTES, 'UTF-8');
        avviso($val["inizio"], $val["oggetto"], $testok, $val["destinatari"]);
    }
}

?>

<script type='text/javascript' src='../lib/js/sidebar/sidebarjs.min.js'></script>

<script>
    var sidebarjs = new SidebarJS.SidebarElement({
        responsive: true
    });

    function post(url) {
        var form = document.createElement("form");
        form.method = "POST";
        form.action = url;
        document.body.appendChild(form);
        form.submit();
    }

    function postnp(url) {
        var form = document.createElement("form");
        form.method = "POST";
        form.action = url;
        form.target = "_blank";
        document.body.appendChild(form);
        form.submit();
    }
</script>

<?php

mysqli_close($con);
print("</div>");
stampa_piede_ges_new("");
print("</div></main></body>");

function menu_title_begin($label, $icon = "", $enable = TRUE)
{
    if (!$enable) return;
    global $trig;
    $trig = true;
    global $index;
    $index = $index + 1;
?>
    <li class="mb-1">
        <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#l<?php echo $index; ?>-collapse" aria-expanded="false">
        <?php if($icon != "") { ?>
            <i class="bi bi-<?php echo $icon; ?>" style="margin-right: 8px;"></i> 
        <?php } ?>    
        <b><?php echo $label; ?></b>
        </button>
        <div class="collapse" id="l<?php echo $index; ?>-collapse">
            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
    <?php
}

function menu_title_end($enable = TRUE)
{
    global $trig;
    $trig = true;
    $enable and print "</ul> </div> </li>";
}

function menu_item($url, $label, $icon = "dot", $enable = TRUE)
{
    global $trig;
    $trig = true;
    if (!$enable) return; ?>
    <li>
        <a href='#' class='link-body-emphasis mil d-inline-flex text-decoration-none rounded' onclick="post('<?php echo $url; ?>')">
        <?php if($icon != "") { ?>
            <i class="bi bi-<?php echo $icon; ?>" style="margin-right: 8px;"></i> 
        <?php } ?>
        <?php echo $label; ?></a>
    </li>
<?php
}

// apre in nuova pagina
function menu_item_new_page($url, $label, $icon = "dot", $enable = TRUE)
{
    global $trig;
    $trig = true;
    if (!$enable) return;
?>
    <li><a href='#' class='mil link-body-emphasis d-inline-flex text-decoration-none rounded' onclick="postnp('<?php echo $url; ?>')">
    <?php if($icon != "") { ?>
        <i class="bi bi-<?php echo $icon; ?>" style="margin-right: 8px;"></i> 
    <?php } ?>
    <?php echo $label; ?></a></li>
<?php
}

function menu_separator($titolo)
{
    global $trig; //per il momento disabilitato
    if ($trig) {
        //print("<li class='border-top my-3'></li>");
    }
}

function annotazione($data, $testo, $nome, $cognome, $tipoalert){
    ?>
        <div class='alert <?php echo $tipoalert ?>' role='alert'> 
            <span>
                <i class='bi bi-calendar2-week' style='margin-right: 8px;'></i> 
                <?php echo data_italiana($data); ?>
            </span> <br>
            <span>
                <i class='bi bi-person' style='margin-right: 8px;'></i> 
                <?php echo $cognome . " " . $nome; ?>
            </span> <br>
            <span>
                <i class='bi bi-chat-quote' style='margin-right: 8px;'></i> 
                <?php echo $testo; ?>
            </span>
        </div>
    <?php
}

function avviso($data, $oggetto, $testo, $destinatari = ""){
    ?>
        <div class='alert alert-primary' role='alert'> 
            <span>
                <i class='bi bi-calendar2-week' style='margin-right: 8px;'></i> 
                <?php echo data_italiana($data); ?>
            </span> <br>
            <?php if($destinatari != "") { ?>
                <span>
                    <i class='bi bi-people' style='margin-right: 8px;'></i> 
                    <?php echo $destinatari; ?>
                </span> <br>
            <?php } ?>
            <span>
                <i class='bi bi-chat-quote' style='margin-right: 8px;'></i> 
                <?php echo $oggetto; ?>
            </span>
            <hr>
            <span>
                <?php echo $testo; ?>
            </span>
        </div>
    <?php
}
