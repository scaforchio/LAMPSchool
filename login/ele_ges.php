<?php

session_start();


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
////session_start();

$urlorigine = $_SERVER['HTTP_REFERER'];
if (isset($_SERVER['HTTPS']))
{
    $urlattuale = 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['SERVER_NAME'];
}
else
{
    $urlattuale = 'http://' . $_SERVER['SERVER_NAME'];
}

if ($urlattuale == substr($urlorigine, 0, strlen($urlattuale)))
{
    $origineok = true;
}

try
{
    require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
    require_once '../lib/funzioni.php';
} catch (Exception $e)
{
    print "<br><br><b><big><center>Sessione scaduta!</center></big></b>";
    print "<br><b><big><center>Rieffettuare il <a href='../pianif.php'>login</a>.</center></big></b>";
}

// istruzioni per tornare alla pagina di login se non c'è una sessione valida

$_SESSION['log'] = $logcompleto;
$_SESSION['sola_lettura'] = $sola_lettura;
$_SESSION['gestcentrautorizz'] = $gestcentrautorizz;
$_SESSION['nomefilelog'] = $nomefilelog;
$json = leggeFileJSON('../lampschool.json');
$_SESSION['versione'] = $json['versione'];
$_SESSION['indirizzomailfrom'] = $indirizzomailfrom;

$_SESSION['g02']=$g02;
$_SESSION['g03']=$g03;
$_SESSION['g04']=$g04;
$_SESSION['g05']=$g05;
$_SESSION['g06']=$g06;
$_SESSION['g07']=$g07;
$_SESSION['g08']=$g08;
$_SESSION['g09']=$g09;
$_SESSION['g10']=$g10;
$_SESSION['giud02']=$giud02;
$_SESSION['giud03']=$giud03;
$_SESSION['giud04']=$giud04;
$_SESSION['giud05']=$giud05;
$_SESSION['giud06']=$giud06;
$_SESSION['giud07']=$giud07;
$_SESSION['giud08']=$giud08;
$_SESSION['giud09']=$giud09;
$_SESSION['giud10']=$giud10;
$_SESSION['gc01']=$gc01;
$_SESSION['gc02']=$gc02;
$_SESSION['gc03']=$gc03;
$_SESSION['gc04']=$gc04;
$_SESSION['gc05']=$gc05;
$_SESSION['gc06']=$gc06;
$_SESSION['gc07']=$gc07;
$_SESSION['gc08']=$gc08;
$_SESSION['gc09']=$gc09;
$_SESSION['gc10']=$gc10;
$_SESSION['giudcomp01']=$giudcomp01;
$_SESSION['giudcomp02']=$giudcomp02;
$_SESSION['giudcomp03']=$giudcomp03;
$_SESSION['giudcomp04']=$giudcomp04;
$_SESSION['giudcomp05']=$giudcomp05;
$_SESSION['giudcomp06']=$giudcomp06;
$_SESSION['giudcomp07']=$giudcomp07;
$_SESSION['giudcomp08']=$giudcomp08;
$_SESSION['giudcomp09']=$giudcomp09;
$_SESSION['giudcomp10']=$giudcomp10;

$_SESSION['classeregistro'] = "";  // Si riazzera quando si torna al menu così si capisce che non si è più in fase di registro

/*
 * VARIABILI CHE SONO USATE NELLE FUNZIONI
 */

$_SESSION['giustifica_ritardi'] = $giustifica_ritardi;

$indirizzoip = IndirizzoIpReale();
$_SESSION['indirizzoip'] = $indirizzoip;


$seme = md5(date('Y-m-d'));


$ultimoaccesso = "";

//  $_SESSION['versione']=$versione;
//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

if (!$con)
{
    die("<h1> Connessione al server fallita </h1>");
}

$username = stringa_html('utente');
$password = stringa_html('password');

// Controlla la presenza di almeno uno parametro nel POST
// altrimenti la chiamata viene da un link
$accessouniversale = false;
if (isset($_SESSION['accessouniversale']))
{
    $accessouniversale = $_SESSION['accessouniversale'];
}
if (count($_POST))
{
    $JSdisab = is_stringa_html('js_enabled') ? stringa_html('js_enabled') : '0';

    if ($JSdisab == 1)
    {
        die("<center><b>Attenzione! Abilitare Java Script per utilizzare LAMPSchool!</b></center>");
    }


    // LEGGO IL FILE DELLA CHIAVE UNIVERSALE PER adminlamp


    @$fp = fopen("../unikey.txt", "r");
    if ($fp)
    {
        $unikey = fread($fp, 32);
        //print $unikey;
        //print md5($password);
        if (md5($unikey . $seme) == $password)
        {
            $accessouniversale = true;
            $_SESSION['accessouniversale'] = true;
        }
    }


    $username = stringa_html('utente');
    $password = stringa_html('password');
    if ($password != md5(md5($chiaveuniversale) . $seme) & (!$accessouniversale))
    {
        $sql = "SELECT *,unix_timestamp(ultimamodifica) AS ultmod FROM tbl_utenti WHERE userid='" . $username . "' AND  md5(concat(password,'$seme'))='" . elimina_apici($password) . "'";
    }
    else
    {
        $sql = "SELECT *,unix_timestamp(ultimamodifica) AS ultmod FROM tbl_utenti WHERE userid='" . $username . "'";
    }


    $result = mysqli_query($con, inspref($sql)) or die("Errore nella query: " . mysqli_error($con) . inspref($sql));

    if (mysqli_num_rows($result) <= 0)
    {
        //  print $passwordesame;
        // print "<br>".md5($password);
        //  print "<br>".$utente;
        //   die();
        if (($username == 'esamidistato' && $password == md5($passwordesame . $seme) | $accessouniversale))
        {
            // die("Sono qui!");
            $_SESSION['tipoutente'] = 'E';
            $_SESSION['userid'] = 'ESAMI';
            $_SESSION['idutente'] = 'esamedistato';

            $_SESSION['cognome'] = "Esame ";
            $_SESSION['nome'] = "di stato";
            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§Accesso ESAMI");
        }
        else
        {
            if ($_SESSION['suffisso'] != "")
            {
                $suff = $_SESSION['suffisso'] . "/";
            }
            else
                $suff = "";
            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§Accesso errato: $username - $password");

            header("location: login.php?messaggio=Utente sconosciuto&suffisso=" . $_SESSION['suffisso']);
            die;
        }
    }
    else
    {
        $data = mysqli_fetch_array($result);
        $_SESSION['userid'] = $data['userid'];
        $_SESSION['tipoutente'] = $data['tipo'];
        $_SESSION['sostegno'] = docente_sostegno($data['idutente'], $con);
        $_SESSION['idutente'] = $data['idutente'];
        $_SESSION['dischpwd'] = $data['dischpwd'];
        $passdb = $data['password'];  // TTTT per controllo iniziale alunni
        // print "Data: $dataultimamodifica - Ora: $dataodierna";
        // print "Diff: $giornidiff";
        if ($_SESSION['tipoutente'] == 'T')
        {
            //  $sql = "SELECT * FROM tbl_tutori WHERE idutente='" . $_SESSION['idutente'] . "'";
            $sql = "SELECT * FROM tbl_alunni WHERE idalunno='" . $_SESSION['idutente'] . "'";
            $ris = mysqli_query($con, inspref($sql)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));

            if ($val = mysqli_fetch_array($ris))
            {
                $_SESSION['idstudente'] = $val["idalunno"];
                $_SESSION['cognome'] = $val["cognome"];
                $_SESSION['nome'] = $val["nome"];
            }
        }

        if ($_SESSION['tipoutente'] == 'L')
        {
            //print "PASSDB: $passdb";
            //  $sql = "SELECT * FROM tbl_tutori WHERE idutente='" . $_SESSION['idutente'] . "'";
            $sql = "SELECT * FROM tbl_alunni WHERE idalunno='" . ($_SESSION['idutente'] - 2100000000) . "'";

            $ris = mysqli_query($con, inspref($sql)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));

            if ($val = mysqli_fetch_array($ris))
            {
                $_SESSION['idstudente'] = $val["idalunno"];
                $_SESSION['cognome'] = $val["cognome"];
                $_SESSION['nome'] = $val["nome"];
                $_SESSION['codfiscale'] = $val['codfiscale'];
            }
            // TTTT Per controllo iniziale alunni
            $strpass = $_SESSION['codfiscale'];
            $passcontr = md5(md5($strpass));
            //print "STRPASS: $strpass";
            //print "PASSCONTR: $passcontr";
            if ($passdb == $passcontr)
                header("location: ../password/cambpwd.php?suffisso=" . $_SESSION['suffisso']);
        }

        if ($_SESSION['tipoutente'] == 'D' | $_SESSION['tipoutente'] == 'S' | $_SESSION['tipoutente'] == 'P')
        {
            $sql = "SELECT * FROM tbl_docenti WHERE idutente='" . $_SESSION['idutente'] . "'";
            $ris = mysqli_query($con, inspref($sql)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));

            if ($val = mysqli_fetch_array($ris))
            {
                $_SESSION['cognome'] = $val["cognome"];
                $_SESSION['nome'] = $val["nome"];
            }
            // VERIFICO SE C'E' UNA DEROGA PER IL LIMITE DI INSERIMENTO
            $sql = "SELECT * FROM tbl_derogheinserimento WHERE iddocente='" . $_SESSION['idutente'] . "' AND DATA='" . date('Y-m-d') . "'";
            $ris = mysqli_query($con, inspref($sql)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));

            if (mysqli_num_rows($ris) > 0)
            {
                $_SESSION['derogalimite'] = true;
            }
            else
            {
                $_SESSION['derogalimite'] = false;
            }
        }

        if ($_SESSION['tipoutente'] == 'A')
        {
            $sql = "SELECT * FROM tbl_amministrativi WHERE idutente='" . $_SESSION['idutente'] . "'";
            $ris = mysqli_query($con, inspref($sql)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));

            if ($val = mysqli_fetch_array($ris))
            {
                $_SESSION['cognome'] = $val["cognome"];
                $_SESSION['nome'] = $val["nome"];
            }
        }


        if ($_SESSION['tipoutente'] == 'M')
        {
            // $idscuola = md5($nomefilelog);
            // print "<iframe style='visibility:hidden;display:none' src='http://www.lampschool.net/test/testesist.php?ids=$idscuola&nos=$nome_scuola&cos=$comune_scuola&ver=$versioneprecedente&asc=$annoscol'></iframe>";
        }
        //
        //  AZIONI PRIMO ACCESSO DELLA GIORNATA
        //
        if ($modocron == "acc")
        {
            $query = "SELECT dataacc FROM tbl_logacc
                   WHERE idlog = (SELECT max(idlog) FROM tbl_logacc)";
            $ris = mysqli_query($con, inspref($query)) or die("Errore " . inspref($query));
            $rec = mysqli_fetch_array($ris);
            $dataultimoaccesso = $rec['dataacc'];
            $dataultimo = substr($dataultimoaccesso, 0, 10);
            //print $dataultimo;
            $dataoggi = date("Y/m/d");
            //print $dataoggi;
            if ($dataoggi > $dataultimo)
            {
                daily_cron($_SESSION['suffisso'], $con, '1101', $nomefilelog);
            }
        }
        //
        //  FINE AZIONI PRIMO ACCESSO DELLA GIORNATA
        //


        // Inserimento nel log dell'accesso
        if ($_SESSION['suffisso'] != "")
        {
            $suff = $_SESSION['suffisso'] . "/";
        }
        else
            $suff = "";
        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Accesso: $username - $password");

        // Ricerca ultimo accesso
        $query = "select dataacc from " . $_SESSION["prefisso"] . "tbl_logacc where idlog=(select max(idlog) from " . $_SESSION["prefisso"] . "tbl_logacc where utente='$username' and comando='Accesso')";
        $ris = mysqli_query($con, $query) or die("Errore " . $query);
        if (mysqli_num_rows($ris) == 0)
        {
            $ultimoaccesso = "";
        }
        else
        {
            $rec = mysqli_fetch_array($ris);
            $ultimoaccesso = $rec['dataacc'];
            $dataultaccute = substr($ultimoaccesso, 0, 10);
            $oraultaccute = substr($ultimoaccesso, 13, 5);
            $giornoultaccute = giorno_settimana($dataultaccute);
            $ultimoaccesso = $giornoultaccute . " " . data_italiana($dataultaccute) . " h. " . $oraultaccute;
        }
        // Inserimento dell'accesso in tabella
        // $indirizzoip = IndirizzoIpReale();
        // $_SESSION['indirizzoip'] = $indirizzoip;
        if ($password != md5(md5($chiaveuniversale) . $seme) & (!$accessouniversale))
        {
            $sql = "INSERT INTO " . $_SESSION["prefisso"] . "tbl_logacc( utente , dataacc, comando,indirizzo) values('$username','" . date('Y/m/d - H:i') . "','Accesso','$indirizzoip')";
        }
        else
        {
            $sql = "INSERT INTO " . $_SESSION["prefisso"] . "tbl_logacc( utente , dataacc, comando,indirizzo) values('$username','" . date('Y/m/d - H:i') . "','Chiave universale','$indirizzoip')";
        }
        // print $sql;
        mysqli_query($con, $sql) or die("Errore in inserimento log!");
    }
}

$cambiamentopassword = false;
if ($_SESSION['tipoutente'] != 'E')
{
    if ($password != md5(md5($chiaveuniversale) . $seme) && !$accessouniversale)
    {
        $sql = "SELECT unix_timestamp(ultimamodifica) AS ultmod FROM " . $_SESSION['prefisso'] . "tbl_utenti WHERE userid='" . $_SESSION['userid'] . "'";
        $data = mysqli_fetch_array(mysqli_query($con, $sql));
        $dataultimamodifica = $data['ultmod'];
        $dataodierna = time();
        $giornidiff = differenza_giorni($dataultimamodifica, $dataodierna);
        // print "Differenza: $giornidiff";

        $cambiamentopassword = false;
        if (($giornidiff > $maxgiornipass) & ($_SESSION['tipoutente'] == 'D' | $_SESSION['tipoutente'] == 'P' | $_SESSION['tipoutente'] == 'S' | $_SESSION['tipoutente'] == 'A'))
        {
            $cambiamentopassword = true;
        }
    }
}

if ($_SESSION['tipoutente'] == "S" | $_SESSION['tipoutente'] == "D")
{
    $sost = cattedre_sostegno($_SESSION['idutente'], $con);
    $norm = cattedre_normali($_SESSION['idutente'], $con);
}


// Azzero i parametri che servono in modalità registro
$_SESSION['regcl'] = "";
$_SESSION['regma'] = "";
$_SESSION['reggi'] = "";

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idutente = $_SESSION["idutente"];
$idesterno = "";

if ($tipoutente == "" || !$origineok)
{
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
if ($ultimoaccesso != "")
{
    $ult = " <b>(Ultimo accesso: $ultimoaccesso)</b>";
}
else
{
    $ult = "";
}
stampa_testata("MENU PRINCIPALE $ult", "", "$nome_scuola", "$comune_scuola");

if ($cambiamentopassword)
{
    print "<br><br><center><b><big>Password scaduta, modificarla!</big></b></center>";
    print "<br><center><a href='../password/cambpwd.php'>Cambia password</a></center>";
}
else
{
    print "<table border=1 width=100%>
					<tr class='prima'>
						<td width='33%'><center>MENU</center></td>
						<td width='67%'><center>AVVISI</center></td>
				  </tr>
				  <tr>";
    print "<td align='center' valign='top'>";
    menu_open();


    if ($tipoutente == 'E')
    {

        menu_title_begin('ESAMI DI STATO');
        menu_item('../esame3m/esmaterieclasse.php', 'MATERIE ESAME');
        menu_item('../esame3m/commissione.php', 'FORMAZIONE COMMISSIONI');
        menu_separator("");
        menu_item('../esame3m/rieptabesame.php', 'TABELLONE');
        menu_item('../esame3m/sitesami.php', 'SITUAZIONE ESAMI');
        menu_separator("");
        menu_item('../esame3m/esa_vis_alu_cla.php', 'ANAGRAFICHE ALUNNI');

        menu_separator("");
        menu_item('../esame3m/cambpassesame.php', 'CAMBIAMENTO PASSWORD');
        // menu_item('../esame3m/schedaalunno.php', 'SCHEDA ALUNNO');

        menu_title_end();
    }
//menu_item('../evacuazione/ricannotaz.php', 'EVACUAZIONE');

    if ($tipoutente == 'D')
    {

        menu_title_begin('REGISTRO DI CLASSE');
        menu_item('../regclasse/riepgiorno.php', 'VISUALIZZA GIORNATA');
        menu_item('../regclasse/riepsett.php', 'VISUALIZZA SETTIMANA');
        menu_item('../regclasse/annotaz.php', 'ANNOTAZIONI SU REGISTRO');
        menu_item('../regclasse/ricannotaz.php', 'RICERCA ANNOTAZIONI');
        menu_item('../evacuazione/evacuazione.php', 'MODULO EVACUAZIONE');
        // menu_item("../assemblee/assdoc.php?iddocente=$idutente", 'ASSEMBLEE DI CLASSE');
        if ($livello_scuola == '4')
            {
        menu_item("../assemblee/assdoc.php", 'ASSEMBLEE DI CLASSE');
            }
        //menu_item('../evacuazione/ricannotaz.php', 'EVACUAZIONE');
        menu_title_end();
        menu_title_begin('ASSENZE');
        // menu_item('../assenze/ass.php', 'ASSENZE');
        if ($gestcentrautorizz == 'no')
        {
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
        if ($sost)
        {
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

        menu_title_end();


        // MODIFICA PER PROF. FINI
        /*  if ($idutente == 1000000030)
          {
          menu_title_begin('FUNZIONI COMMISSARIO');

          menu_item('../valutazioni/riepvoticlasse.php', 'SITUAZIONE VOTI MEDI PER CLASSE');
          menu_title_end();
          }
         */

        if (estrai_docente_coordinatore($idutente, $con))
        {
            menu_title_begin('FUNZIONI COORDINATORE');

            menu_item('../valutazioni/riepvoticlasse.php', 'SITUAZIONE VOTI MEDI PER CLASSE');
            menu_item('../note/stampanote.php', 'STAMPA NOTE PER CLASSE');
            menu_item('../assenze/sitassmens.php', 'SITUAZIONE MENSILE ASSENZE');
            menu_item('../assenze/sitasstota.php', 'SITUAZIONE TOTALE ASSENZE');
            menu_item('../assenze/deroghe.php', 'DEROGHE ASSENZE');
            menu_item('../assenze/visderoghe.php', 'SITUAZIONE DEROGHE ASSENZE');
            menu_item('../scrutini/riepvoti.php', 'TABELLONE SCRUTINI INTERMEDI');
            menu_item('../scrutini/riepvotifinali.php', 'TABELLONE SCRUTINI FINALI');
            if ($livello_scuola == '4')
            {
                menu_item('../scrutini/riepvotifinali.php?integrativo=yes', 'SCRUTINI INTEGRATIVI');
            }
            menu_item('../scrutini/riepproposte.php', 'RIEPILOGO PROPOSTE DI VOTO');
            menu_item('../documenti/stampafirmaprogrammi.php?docente=' . $idutente, 'STAMPE PER PRESA VISIONE PROGRAMMI');
            menu_item('../documenti/documenticlasse.php', 'DOCUMENTI CLASSE');


            menu_title_end();
        }

        if ($norm & $valutazionepercompetenze == 'yes')
        {
            menu_title_begin('VALUTAZIONE COMPETENZE');
            menu_item('../valutazioni/valabilcono.php', 'VERIFICHE');
            menu_item('../valutazioni/valaluabilcono.php?modo=norm', 'VALUTAZIONI ALUNNO');
            menu_item('../valutazioni/sitvalalu.php', 'VISUALIZZA SITUAZIONE ALUNNO');
            menu_item('../valutazioni/sitvalobi.php', 'VISUALIZZA SITUAZ. PER OBIETT.');

            menu_title_end();
        }

        if ($norm & $valutazionepercompetenze == 'yes')
        {
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


        if ($sost)
        {
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
        menu_title_begin('ALTRO');
        menu_item('../password/cambpwd.php', 'CAMBIAMENTO PROPRIA PASSWORD');
        menu_item('../circolari/viscircolari.php', 'LEGGI CIRCOLARI');
        menu_item('../colloqui/visrichieste_doc.php', 'PRENOTAZIONI COLLOQUI');
        menu_item("../docenti/mod_contatto.php", 'AGGIORNA DATI DI CONTATTO');
        menu_item("../collegamenti/coll.php", 'VISUALIZZA COLLEGAMENTI WEB');
        menu_item('../docenti/richferie.php', 'RICHIESTA ASTENSIONE DAL LAVORO');
        menu_item('../docenti/esamerichferie.php', 'ESAMINA RICHIESTE FERIE');
        menu_title_end();
    }

    if ($tipoutente == 'S')
    {
        menu_title_begin('REGISTRO DI CLASSE');
        menu_item('../regclasse/riepgiorno.php', 'VISUALIZZA GIORNATA');
        menu_item('../regclasse/riepsett.php', 'VISUALIZZA SETTIMANA');
        menu_item('../regclasse/annotaz.php', 'ANNOTAZIONI SU REGISTRO');
        menu_item('../regclasse/ricannotaz.php', 'RICERCA ANNOTAZIONI');
        menu_item('../regclasse/stamparegiclasse.php', 'STAMPA REGISTRI DI CLASSE');
        menu_item('../evacuazione/evacuazione.php', 'MODULO EVACUAZIONE');

        menu_title_end();
        menu_title_begin('ASSENZE');
        menu_item('../assenze/ass.php', 'ASSENZE');
        menu_item('../assenze/rit.php', 'RITARDI');
        menu_item('../assenze/usc.php', 'USCITE ANTICIPATE');
        menu_item('../assenze/selealunniautuscita.php', 'AUTORIZZAZIONE USCITE');
        menu_item('../rp/autorizzaritardo.php', 'AUTORIZZA ENTRATA IN RITARDO');
        menu_item('../rp/vis_ritcla.php', 'ENTRATE POSTICIPATE CLASSI');

        menu_item('../assenze/sitgiustifiche.php', 'VISUALIZZA MANCANZA GIUSTIFICHE');
        menu_separator("");
        menu_item('../assenze/sitassmens.php', 'SITUAZIONE MENSILE');
        menu_item('../assenze/sitasstota.php', 'SITUAZIONE TOTALE');
        menu_item('../assenze/sitassprob.php', 'SITUAZIONI PROBLEMATICHE');
        menu_item('../assenze/sitassperclassi.php', 'PERCENTUALI PER CLASSE');
        menu_separator("");
        menu_item('../rp/vistimbrature.php', 'VISUALIZZA TIMBRATURE');
        menu_item('../rp/selealunnipresenza.php', 'FORZA PRESENZA ALUNNI');
        menu_item('../rp/vispresenzeforzate.php', 'VISUALIZZA PRESENZE FORZATE');
        menu_item('../rp/selealunnitimbraturaforzata.php', 'FORZA TIMBRATURE');
        menu_item('../rp/elencotimbratureforzate.php', 'REPORT FORZATURE');


        menu_separator("");
        menu_item('../assenze/visgiustifiche.php', 'ELIMINA GIUSTIFICHE');
        menu_item('../assenze/deroghe.php', 'DEROGHE ASSENZE');
        menu_item('../assenze/visderoghe.php', 'SITUAZIONE DEROGHE ASSENZE');
        menu_separator("");
        menu_item('../assenze/ricalcoloassenzesele.php', 'RICALCOLO ASSENZE');
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
        if ($livello_scuola == '4')
        {
            menu_title_begin('ASSEMBLEE DI CLASSE');
            // menu_item("../assemblee/assdoc.php?iddocente=$idutente", 'CONCESSIONE');
            // menu_item("../assemblee/assstaff.php?iddocente=$idutente", 'AUTORIZZAZIONE');
            // menu_item("../assemblee/contver.php?iddocente=$idutente", 'CONTROLLO VERBALI');
            menu_item("../assemblee/assdoc.php", 'ASSEMBLEE PROPRIE ORE');
            menu_item("../assemblee/assstaff.php", 'AUTORIZZAZIONE ASSEMBLEE');
            menu_item("../assemblee/contver.php", 'VERIFICA VERBALI');
            menu_item("../assemblee/visionaverbali.php", 'SITUAZIONE ASSEMBLEE');
            menu_title_end();
        }
        menu_title_begin('OSSERVAZIONI E DIARIO DI CLASSE');

        menu_item('../valutazioni/osssist.php', 'OSSERVAZIONI SISTEMATICHE');
        menu_item('../valutazioni/ricosssist.php', 'RICERCA OSSERV. SIST.');
        menu_item('../valutazioni/stampaosssist.php', 'STAMPA OSSERV. SIST.');
        menu_item('../valutazioni/diariocl.php', 'DIARIO DI CLASSE');
        menu_item('../valutazioni/ricdiariocl.php', 'RICERCA SU DIARIO DI CLASSE');
        menu_item('../valutazioni/stampadiariocl.php', 'STAMPA DIARIO DI CLASSE');
        if ($sost)
        {
            menu_item('../valutazioni/osssistcert.php', 'OSSERVAZIONI SISTEMATICHE AL. CERT.');
            menu_item('../valutazioni/ricosssistcert.php', 'RICERCA OSSERV. SIST. AL. CERT.');
            menu_item('../valutazioni/stampaosssistcert.php', 'STAMPA OSSERV. SIST. AL. CERT.');
        }
        menu_title_end();
        if (estrai_docente_coordinatore($idutente, $con))
        {
            menu_title_begin('FUNZIONI COORDINATORE');

            menu_item('../valutazioni/riepvoticlasse.php', 'SITUAZIONE VOTI MEDI PER CLASSE');
            menu_item('../note/stampanote.php', 'STAMPA NOTE PER CLASSE');
            menu_item('../scrutini/riepvoti.php', 'TABELLONE SCRUTINI INTERMEDI');
            menu_item('../scrutini/riepvotifinali.php', 'TABELLONE SCRUTINI FINALI');
            if ($livello_scuola == '4')
            {
                menu_item('../scrutini/riepvotifinali.php?integrativo=yes', 'SCRUTINI INTEGRATIVI');
            }
            menu_item('../scrutini/riepproposte.php', 'RIEPILOGO PROPOSTE DI VOTO');
            menu_item('../documenti/stampafirmaprogrammi.php?docente=' . $idutente, 'STAMPE PER PRESA VISIONE PROGRAMMI');
            menu_item('../documenti/documenticlasse.php', 'DOCUMENTI CLASSE');
            menu_title_end();
        }
        menu_title_begin('SCRUTINI');
        menu_item('../scrutini/riepvoti.php', 'TABELLONE SCRUTINI INTERMEDI');
        menu_item('../scrutini/riepvotifinali.php', 'TABELLONE SCRUTINI FINALI');
        if ($livello_scuola == '4')
        {
            menu_item('../scrutini/riepvotifinali.php?integrativo=yes', 'SCRUTINI INTEGRATIVI');
        }
        menu_item('../scrutini/sitscrutini.php', 'SITUAZIONE SCRUTINI');
        menu_item('../scrutini/schedaalu.php', 'SCHEDA INTERMEDIA ALUNNO');

        menu_item('../scrutini/schedafinalealu.php', 'PAGELLA FINALE ALUNNO');
        menu_item('../scrutini/riepproposte.php', 'RIEPILOGO PROPOSTE DI VOTO');
        menu_title_end();


        menu_title_begin('VOTI');
        //menu_item('../valutazioni/val.php', 'INSERIMENTO');
        menu_item('../valutazioni/prospettovoti.php', 'PROSPETTO VOTI');
        menu_item('../valutazioni/proposte.php', 'MEDIE E PROPOSTE DI VOTO');
        menu_item('../valutazioni/visvalpre.php', 'VISUALIZZA SITUAZIONE ALUNNO');
        menu_item('../valutazionecomportamento/valcomp.php', 'VOTO COMPORTAMENTO');
        menu_item('../valutazionecomportamento/sitvalcompalu.php', 'SITUAZIONE VOTI COMPORTAMENTO');
        menu_title_end();

        if ($norm & $valutazionepercompetenze == 'yes')
        {
            menu_title_begin('VALUTAZIONE COMPETENZE');
            menu_item('../valutazioni/valabilcono.php', 'VERIFICHE');
            menu_item('../valutazioni/valaluabilcono.php?modo=norm', 'VALUTAZIONI ALUNNO');
            menu_item('../valutazioni/sitvalalu.php', 'VISUALIZZA SITUAZIONE ALUNNO');
            menu_item('../valutazioni/sitvalobi.php', 'VISUALIZZA SITUAZ. PER OBIETT.');

            menu_title_end();
        }

        if ($norm & $valutazionepercompetenze == 'yes')
        {
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

        if ($tokenservizimoodle != "")
        {
            menu_title_begin('INTERFACCIA CON MOODLE');
            menu_item('../moodle/esporta_moodle.php', 'ESPORTA DATI PER MOODLE');
            menu_item('../moodle/creacorsimoodle.php', 'CREA E SINCRONIZZA CORSI MOODLE');
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

        if ($sost)
        {
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
        menu_item('../alunni/autorizzazioni.php', 'Visualizza deroghe ed autorizzazioni');
        menu_item('../segreteria/vis_imp.php', 'IMPIEGATI DI SEGRETERIA');
        menu_item('../docenti/vis_doc.php', 'DOCENTI');
        menu_item('../colloqui/disponibilita.php', 'DISPONIBIL. DOCENTI');
        menu_item('../colloqui/visdisponibilita.php', 'VISUALIZZA DISP. DOCENTI');
        menu_item('../contr/stampaelezioni.php', 'STAMPA PER ELEZIONI');
        menu_title_end();
        menu_separator("&nbsp;");

        menu_title_begin('CATTEDRE');
        menu_item('../cattedre/cat.php', 'CATTEDRE');
        menu_item('../cattedre/cat_sost.php', 'CATTEDRE SOSTEGNO');
        menu_item('../lezionigruppo/vis_gru.php', 'CATTEDRE SPECIALI');
        menu_item('../cattedre/vis_cattedre.php', 'VISUALIZZA CATTEDRE');
        menu_title_end();


        menu_title_begin('ALTRO');
        menu_item('../password/cambpwd.php', 'CAMBIAMENTO PROPRIA PASSWORD');
        menu_item('../circolari/viscircolari.php', 'LEGGI CIRCOLARI');
        menu_item('../colloqui/visrichieste_doc.php', 'PRENOTAZIONI COLLOQUI');
        menu_item("../docenti/mod_contatto.php", 'AGGIORNA DATI DI CONTATTO');
        menu_item("../collegamenti/coll.php", 'VISUALIZZA COLLEGAMENTI WEB');
        menu_item('../docenti/richferie.php', 'RICHIESTA ASTENSIONE DAL LAVORO');
        menu_item('../docenti/esamerichferie.php', 'ESAMINA RICHIESTE FERIE');
        menu_item('../docenti/visrichferie.php', 'VISIONA ASTENSIONI APPROVATE DAL D.S.');
        menu_title_end();
    }


    if ($tipoutente == 'P')   // Presidenza
    {

        menu_title_begin('REGISTRO DI CLASSE');
        menu_item('../regclasse/riepgiorno.php', 'VISUALIZZA GIORNO');
        menu_item('../regclasse/riepsett.php', 'VISUALIZZA SETTIMANA');
        menu_item('../regclasse/annotaz.php', 'ANNOTAZIONI SU REGISTRO');
        menu_item('../regclasse/ricannotaz.php', 'RICERCA ANNOTAZIONI');
        menu_item('../regclasse/stamparegiclasse.php', 'STAMPA REGISTRI DI CLASSE');
        if ($maxgiorniritardolez < 300)
        {
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
        menu_separator("");
        menu_item('../rp/vistimbrature.php', 'VISUALIZZA TIMBRATURE');
        menu_item('../rp/selealunnipresenza.php', 'FORZA PRESENZA ALUNNI');
        menu_item('../rp/vispresenzeforzate.php', 'VISUALIZZA PRESENZE FORZATE');
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
        if ($livello_scuola == '4')
        {
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
        if ($livello_scuola == '4')
        {
            menu_item('../scrutini/riepvotifinali.php?integrativo=yes', 'SCRUTINI INTEGRATIVI');
        }
        menu_item('../scrutini/schedafinalealu.php', 'PAGELLA FINALE ALUNNO');
        menu_item('../scrutini/riepproposte.php', 'RIEPILOGO PROPOSTE DI VOTO');
        menu_title_end();


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
        menu_item('../alunni/autorizzazioni.php', 'Visualizza deroghe ed autorizzazioni');
        menu_item('../segreteria/vis_imp.php', 'IMPIEGATI DI SEGRETERIA');
        menu_item('../docenti/vis_doc.php', 'DOCENTI');
        menu_item('../docenti/attrruolos.php', 'Attribuisci ruolo Staff di presidenza a docente');
        menu_item('../docenti/revruolos.php', 'Revoca ruolo Staff a docente');
        menu_item('../colloqui/disponibilita.php', 'DISPONIBIL. DOCENTI');
        menu_item('../colloqui/visdisponibilita.php', 'VISUALIZZA DISP. DOCENTI');
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


        menu_title_begin('UTENTI');
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
        menu_item('../docenti/esamerichferie.php', 'ESAMINA RICHIESTE FERIE');
        menu_title_end();
    }

    if ($tipoutente == 'M')  // Amministratore
    {

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
        menu_item('../collegamenti/collegamentiweb.php', 'PREPARAZIONE COLLEGAMENTI WEB');

        menu_title_end();
        menu_title_begin('IMPORTAZIONE DA ANNO PRECEDENTE');
        menu_item('../importa/sele_tabe_import.php', 'IMPORTA DATI');
        menu_item('../importa/sele_classe_succ.php', 'RIASSEGNA CLASSI');
        menu_title_end();
        menu_title_begin('PASSWORD');
        menu_item('../password/rigenera_password.php', 'Rigenera e stampa password tutor');
        menu_item('../password/alu_rigenera_password.php', 'Rigenera e stampa password alunni');
        menu_item('../password/conf_rig_pass_doc.php', 'Rigenera e stampa password docenti');
        menu_item('../password/gestpwd.php', 'Cambia password utente');
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
        if ($plesso_specializzazione == "Specializzazione")
        {
            menu_item('../specializzazione/vis_spe.php', 'SPECIALIZZAZIONI');
        }
        if ($plesso_specializzazione == "Plesso")
        {
            menu_item('../specializzazione/vis_spe.php', 'PLESSI');
        }
        menu_item('../sezioni/vis_sez.php', 'SEZIONI');
        menu_item('../classi/vis_cla.php', 'CLASSI');
        menu_item('../materie/vis_mat.php', 'MATERIE');
        menu_item('../documenti/vis_tdoc.php', 'TIPI DOCUMENTO');
        menu_item('../materie/ordmaterie.php', 'ORDINAMENTO MATERIE');
        menu_item('../scrutini/tabesiti.php', 'TIPI ESITI');
        menu_item('../colloqui/orario.php', 'ORARIO');
        menu_item('../colloqui/festivita.php', 'FESTIVITA\'');
        menu_item('../colloqui/sospensioni.php', 'SOSPENSIONI COLLOQUI');
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
        if ($tokenservizimoodle != "")
        {
            menu_title_begin('INTERFACCIA CON MOODLE');
            menu_item('../moodle/esporta_moodle.php', 'ESPORTA DATI PER MOODLE');
            menu_item('../moodle/creacorsimoodle.php', 'CREA E SINCRONIZZA CORSI MOODLE');
            menu_item('../moodle/rigenerapasswordmoodle.php', 'RIGENERA PASSWORD MOODLE ALUNNI');
            menu_item('../moodle/rigenerapasswordmoodledoc.php', 'RIGENERA PASSWORD MOODLE DOCENTI');
            menu_item('../moodle/sincronizzautenti.php', 'AGGIUNGI NUOVI UTENTI A MOODLE');
            menu_item('../moodle/seleiscrizionecorsi.php', 'ISCRIVI STUDENTI A CORSO MOODLE');
            menu_item('../moodle/seleiscrizionecorsidoc.php', 'ISCRIVI DOCENTI A CORSO MOODLE');
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
    }

    if ($tipoutente == 'A')  // Amministrativo
    {
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
        menu_title_end();


        menu_title_begin('PASSWORD');
        menu_item('../password/rigenera_password.php', 'Rigenera e stampa password alunni');
        menu_item('../password/conf_rig_pass_doc.php', 'Rigenera e stampa password docenti');
        menu_item('../password/gestpwd.php', 'Cambia password utente');
        menu_item('../segreteria/vis_imp.php?modo=vis', 'VISUALIZZA AMMINISTRATIVI');
        menu_item('../docenti/vis_doc.php?modo=vis', 'VISUALIZZA DOCENTI');
        menu_item('../alunni/vis_alu_solo_vis.php', 'VISUALIZZA ALUNNI');
        menu_title_end();

        menu_title_begin('TABELLE');
        if ($plesso_specializzazione == "Specializzazione")
        {
            menu_item('../specializzazione/vis_spe.php', 'SPECIALIZZAZIONI');
        }
        if ($plesso_specializzazione == "Plesso")
        {
            menu_item('../specializzazione/vis_spe.php', 'PLESSI');
        }
        menu_item('../sezioni/vis_sez.php', 'SEZIONI');
        menu_item('../classi/vis_cla.php', 'CLASSI');
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

        menu_title_begin('SCRUTINI');
        menu_item('../scrutini/riepvoti.php', 'SCRUTINI INTERMEDI');
        menu_item('../scrutini/riepvotifinali.php', 'SCRUTINI FINALI');
        if ($livello_scuola == '4')
        {
            menu_item('../scrutini/riepvotifinali.php?integrativo=yes', 'SCRUTINI INTEGRATIVI');
        }
        menu_item('../scrutini/schedaalu.php', 'SCHEDA INTERMEDIA ALUNNO');
        menu_item('../scrutini/schedafinalealu.php', 'PAGELLA FINALE ALUNNO');
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


    if ($tipoutente == 'T')
    {

        //  $sql = "SELECT * FROM tbl_tutori WHERE idutente='" . $_SESSION['idutente'] . "'";
        $sql = "SELECT * FROM tbl_alunni WHERE idalunno='" . $_SESSION['idutente'] . "'";
        $ris = mysqli_query($con, inspref($sql)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));
        if ($val = mysqli_fetch_array($ris))
        {
            $idstudente = $val["idalunno"];
        }


        $sql = "select * from tbl_alunni where idalunno='$idstudente'";
        $ris = mysqli_query($con, inspref($sql)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));
        if ($val = mysqli_fetch_array($ris))
        {
            $cognome = $val["cognome"];
            $nome = $val["nome"];
            $idstudente = $val["idalunno"];
            $idclasse = $val["idclasse"];
            $idesterno = $val["idesterno"];
            $pwesterna = $val["pwesterna"];
            $telcel = $val["telcel"];

            // print "<br/><button onclick=\"window.open('../assenze/sitassalut.php'>ASSENZE</a>";
            menu_title_begin("SITUAZIONE ALUNNO $cognome&nbsp;$nome");
            if ($votigenitori == "yes")
                menu_item('../valutazioni/visvaltut.php', 'VOTI');
            menu_item('../note/sitnotealu.php', 'NOTE');
            menu_item('../assenze/sitassalut.php', 'ASSENZE');
            menu_title_end();
            if ($argomentigenitori == "yes")
            {
                menu_title_begin("ARGOMENTI LEZIONI");
                menu_item('../lezioni/riepargomgen.php', 'VISUALIZZA ARGOMENTI');
                menu_title_end();
            }
            if ($visualizzapagelle == 'yes')
            {
                menu_title_begin("PAGELLE");
                if ($numeroperiodi == 2)
                {
                    menu_item('../valutazioni/vispagper.php?periodo=Primo', 'Pagella primo quadrimestre');
                    menu_item('../valutazioni/vispagfin.php', 'PAGELLA FINALE');
                }
                else
                {
                    if ($numeroperiodi == 3)
                    {
                        menu_item('../valutazioni/vispagper.php?periodo=Primo', 'Pagella primo trimestre');
                        menu_item('../valutazioni/vispagper.php?periodo=Secondo', 'Pagella secondo trimestre');
                        menu_item('../valutazioni/vispagfin.php', 'PAGELLA FINALE');
                    }
                }
                menu_title_end();
            }
            menu_title_begin('COMUNICAZIONI SCUOLA-FAMIGLIA');

            menu_item('../circolari/viscircolari.php', 'LEGGI CIRCOLARI');
            menu_item("../colloqui/visdisponibilita.php?idclasse=$idclasse", 'PRENOTAZIONE COLLOQUIO');
            menu_item("../collegamenti/coll.php", 'VISUALIZZA COLLEGAMENTI WEB');

            if (!strpos($telcel, ","))
            {
                if ($agg_dati_genitori == 'yes')
                {
                    menu_item("../alunni/mod_contatto.php", 'AGGIORNA DATI DI CONTATTO');
                }
                menu_title_end();
                if (!$_SESSION['dischpwd'])
                {
                    menu_title_begin('PASSWORD');
                    menu_item('../password/cambpwd.php', 'CAMBIAMENTO PROPRIA PASSWORD');
                    menu_title_end();
                }
            }
        }
    }


    if ($tipoutente == 'L')
    {

        //  $sql = "SELECT * FROM tbl_tutori WHERE idutente='" . $_SESSION['idutente'] . "'";
        $sql = "SELECT * FROM tbl_alunni WHERE idalunno='" . ($_SESSION['idutente'] - 2100000000) . "'";
        $ris = mysqli_query($con, inspref($sql)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));
        if ($val = mysqli_fetch_array($ris))
        {
            $idstudente = $val["idalunno"];
        }


        $sql = "select * from tbl_alunni where idalunno='$idstudente'";
        $ris = mysqli_query($con, inspref($sql)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));
        if ($val = mysqli_fetch_array($ris))
        {
            $cognome = $val["cognome"];
            $nome = $val["nome"];
            $idstudente = $val["idalunno"];
            $idclasse = $val["idclasse"];
            $idesterno = $val["idesterno"];
            $pwesterna = $val["pwesterna"];
            $telcel = $val["telcel"];

            // print "<br/><button onclick=\"window.open('../assenze/sitassalut.php'>ASSENZE</a>";
            menu_title_begin("SITUAZIONE ALUNNO $cognome&nbsp;$nome");
            if ($votigenitori == "yes")
                menu_item('../valutazioni/visvaltut.php', 'VOTI');
            menu_item('../note/sitnotealu.php', 'NOTE');
            menu_item('../assenze/sitassalut.php', 'ASSENZE');
            menu_title_end();

            // VERIFICO SE L'ALUNNO E' UN RAPPRESENTANTE DI CLASSE
            if ($livello_scuola == '4')
            {
           // $query = "select * from tbl_classi where rappresentante1=$idstudente or rappresentante2=$idstudente";
           // $riscontr = mysqli_query($con, inspref($query)) or die("Errore" . inspref($query));
           // if (mysqli_num_rows($riscontr) != 0)
           // {
                menu_title_begin("ASSEMBLEE DI CLASSE");
                menu_item('../assemblee/assricgen.php', 'ASSEMBLEE DI CLASSE');
                menu_title_end();
            //}
            }
            if ($argomentigenitori == "yes")
            {
                menu_title_begin("ARGOMENTI LEZIONI");
                menu_item('../lezioni/riepargomgen.php', 'VISUALIZZA ARGOMENTI');
                menu_title_end();
            }
            menu_title_begin('PASSWORD');
            menu_item('../password/cambpwd.php', 'CAMBIAMENTO PROPRIA PASSWORD');
            menu_title_end();
        }
    }

    menu_close();

    print "</td>";

    /*
     * GESTIONE AVVISI
     */







    if ($tipoutente == 'D' | $tipoutente == 'S' | $tipoutente == 'T' | $tipoutente == 'A' | $tipoutente == 'E' | $tipoutente == 'L')
    {

        $dataoggi = date('Y-m-d');


        print ("<td valign=top>");

        /*            if ($tipoutente == 'S')
          {
          $risultato = controlloNuovaVersione();
          $esito = $risultato['esito'];
          $nuovaVersione = $risultato['versione'];

          if ($esito) {
          print "<center><h5><font color='red'>E' disponibile sul sito di LAMPSchool la versione $nuovaVersione</font></h5></center>";
          }
          }
         */


        //VERIFICO PRESENZA ASSEMBLEE DI CLASSE DA AUTORIZZARE
        if ($livello_scuola == '4')
        {
            if ($tipoutente == 'S' | $tipoutente == 'P')
            {
                $query = "SELECT DISTINCT * FROM tbl_assemblee 
				  WHERE (autorizzato=0) 
				  AND ((docenteconcedente1!=0 AND concesso1=1) AND (docenteconcedente2=0) OR (docenteconcedente2!=0 AND concesso2=1))";
                $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));
                if (mysqli_num_rows($ris) > 0)
                {
                    print ("<center><br><i><b><font color='red'><big><big>Ci sono assemblee da autorizzare! <a href='../assemblee/assstaff.php'>Esamina ora!</a></big></big></font></b></i><br/></center>");
                    print ("<br/>");
                }
            }

            //VERIFICO PRESENZA RICHIESTE DI ASSEMBLEE DI CLASSE
            if ($tipoutente == 'D' | $tipoutente == 'S')
            {
                $query = "SELECT * FROM tbl_assemblee 
		  WHERE ((docenteconcedente1=$idutente AND concesso1=0)
                        OR (docenteconcedente2=$idutente AND concesso2=0))
                        AND (rappresentante1<>0 and rappresentante2<>0)";
                $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));
                if (mysqli_num_rows($ris) > 0)
                {
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
							  and datalettura='0000-00-00'
							  and datainserimento<='$dataoggi'";
        // print "tttt ".inspref($query);
        $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));
        if (mysqli_num_rows($ris) > 0)
        {
            print ("<center><br><i><b><font color='red'><big><big>Ci sono circolari non lette! <a href='../circolari/viscircolari.php'>Leggi ora!</a></big></big></font></b></i><br/></center>");
            print ("<br/>");
        }








        //
        // VERIFICO PRESENZA COLLOQUI
        //
        if ($tipoutente == "D" | $tipoutente == "S")
        {
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
            $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));
            if (mysqli_num_rows($ris) > 0)
            {

                print ("<center><br><i><b><font color='red'>Ci sono prenotazioni per colloqui a cui rispondere! <a href='../colloqui/visrichieste_doc.php'>Rispondi ora!</a></font></b></i><br/></center>");
                print ("<br/>");
            }

            //
            //  COLLOQUI IMMINENTI
            //
            $query = "select * from tbl_prenotazioni,tbl_orericevimento,tbl_alunni,tbl_orario
								  where tbl_prenotazioni.idoraricevimento=tbl_orericevimento.idoraricevimento
								  and tbl_prenotazioni.idalunno=tbl_alunni.idalunno
								  and tbl_orericevimento.idorario=tbl_orario.idorario
								  and iddocente=" . $_SESSION['idutente'] . "
								  and data>='$dataoggi'
								  and tbl_prenotazioni.valido
								  and conferma=2";

            $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));
            if (mysqli_num_rows($ris) > 0)
            {
                while ($rec = mysqli_fetch_array($ris))
                {

                    if ($rec['data'] > $dataoggi | $oraattuale < substr($rec['fine'], 0, 5))
                    {
                        print ("<center><br><i><b><font color='red'>Colloquio con genitore di " . $rec['cognome'] . " " . $rec['nome'] . " il " . data_italiana($rec['data']) . " alle " . substr($rec['inizio'], 0, 5) . "</a></font></b></i><br/></center>");
                        print ("<br/>");
                    }
                }
            }
        }

        if ($tipoutente == "T")
        {
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
								  and conferma=2";

            $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));
            if (mysqli_num_rows($ris) > 0)
            {
                while ($rec = mysqli_fetch_array($ris))
                {

                    if ($rec['data'] > $dataoggi | $oraattuale < substr($rec['fine'], 0, 5))
                    {
                        print ("<center><br><i><b><font color='red'>Colloquio con Prof. " . $rec['cognome'] . " " . $rec['nome'] . " il " . data_italiana($rec['data']) . " or. ricev. " . substr($rec['inizio'], 0, 5) . " - " . substr($rec['fine'], 0, 5) . "<br>" . $rec['notaprenotazione'] . "</a></font></b></i><br/></center>");
                        print ("<br/>");
                    }
                }
            }
        }


        //
        // VERIFICO PRESENZA AVVISI
        //
        $query = "select * from tbl_avvisi where inizio<='$dataoggi' and fine>='$dataoggi' and LOCATE('$tipoutente',destinatari)<>0 order by inizio desc";
        $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));
        while ($val = mysqli_fetch_array($ris))
        {
            $inizio = data_italiana($val["inizio"]);
            $oggetto = $val["oggetto"];
            $testo = inserisci_parametri($val["testo"], $con);   // TTTT Modifica per parametrizzazione messaggi
            print ("<center><i>$inizio</i><br/>");
            print ("<b>$oggetto</b><br/></center>");
            print (html_entity_decode($testo, ENT_QUOTES, 'UTF-8') . "<br/><br/><br/>");
        }

        print ("</td></tr></table>");
    }
    else  // ADMIN e PRESIDE
    {
        $dataoggi = date('Y-m-d');
        print ("<td valign=top>");
        if ($tipoutente == 'M')
        {
            //
            //  VERIFICO PRESENZA AGGIORNAMENTI
            //
            //
            $idscuola = md5($nomefilelog);
            //print "<iframe style='visibility:hidden;display:none' src='http://www.lampschool.net/test/testesist.php?ids=$idscuola&nos=$nome_scuola&cos=$comune_scuola'></iframe>";
            // print "<iframe src='http://www.lampschool.net/test/testesist.php?ids=$idscuola&nos=$nome_scuola&cos=$comune_scuola'></iframe>";
            $url = "http://www.lampschool.net/test/testesist.php?ids=$idscuola&nos=$nome_scuola&cos=$comune_scuola&ver=$versioneprecedente&asc=$annoscol";
            $url = str_replace(" ", "_", $url);
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $data = curl_exec($ch);
            curl_close($ch);
            echo $data;

            /*  $ch = curl_init();

              curl_setopt($ch, CURLOPT_URL, 'http://www.lampschool.net/test/testesist.php?ids=$idscuola&nos=$nome_scuola&cos=$comune_scuola');
              curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
              curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);

              curl_exec($ch);
              curl_close($ch);
             */

            $risultato = controlloNuovaVersione();
            $esito = $risultato['esito'];
            $nuovaVersione = $risultato['versione'];

            if ($esito)
            {
                print "<center><h5><font color='red'>E' disponibile sul sito di LAMPSchool la versione $nuovaVersione</font></h5></center>";
            }

            //
            // FINE VERIFICA AGGIORNAMENTI
        //
        }
        $query = "select * from tbl_avvisi where inizio<='$dataoggi' and fine>='$dataoggi' order by inizio desc";
        $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con) . inspref($query));
        while ($val = mysqli_fetch_array($ris))
        {
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

function menu_open($enable = TRUE)
{
    $enable and print "\n<form id='formMenu' method='POST'>\n<div id='accordion'>";
}

// Chiude il menu'

function menu_close($enable = TRUE)
{
    $enable and print "</div>\n</form>\n";
}

// Disegna il titolo contenitore del menu'

function menu_title_begin($label, $enable = TRUE)
{
    $enable and print "\n<h3>$label</h3><div>";
}

// Chiude il titolo contenitore

function menu_title_end($enable = TRUE)
{
    $enable and print "\n</div>";
}

// Disegna una voce del menu'

function menu_item($url, $label, $enable = TRUE)
{
// $enable and print "\n<button onclick=\"window.open('$url','_self');\" class='button'>$label</button>";
// permette di cambiare l'attributo action della form
// la function setAction è definita nella sezione HEAD
// in questo modo tutto il menu è compreso nella form con il metodo POST
    $enable and print "\n<button onclick=\"setAction('$url');\" class='button'>$label</button>";
}

// Disegna una riga vuota nel menu'

function menu_separator($titolo)
{
    // print "\n<p>&nbsp;</p>";
    print "<br>$titolo<br>";
}

/*

function inserisciAmmonizioniMancataGiustifica($datamessaggio, $con)
{

    $qp = "SELECT DISTINCT nomeparametro FROM tbl_paramcomunicazpers";
    $risp = mysqli_query($con, inspref($qp));
    while ($recp = mysqli_fetch_array($risp))
    {

        $nomeparametro = $recp['nomeparametro'];
        // print "tttt $nomeparametro <br>";
        $query = "SELECT valore FROM tbl_paramcomunicazpers WHERE nomeparametro='$nomeparametro' and idutente=" . $_SESSION['idutente'];
        $rispc = mysqli_query($con, inspref($query));
        $valper = "";
        if ($recpf = mysqli_fetch_array($rispc))
        {
            $valper = $recpf['valore'];
        }

        $messaggio = str_replace("[$nomeparametro]", $valper, $messaggio);

        return $messaggio;
    }
    return $messaggio;

}
*/
