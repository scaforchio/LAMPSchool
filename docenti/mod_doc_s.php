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


/* programma per la modifica dei tbl_docenti
  riceve in ingresso i dati del docente */


// istruzioni per tornare alla pagina di login 

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
@require_once("../lib/admqtt.php");

$titolo = "Modifica docente";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_doc.php'>ELENCO DOCENTI</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<H1>connessione al server mysql fallita</H1>");
    exit;
}
$DB = true;
if (!$DB)
{
    print("<H1>connessione al database stage fallita</H1>");
    exit;
}

$err = 0;
$b = 0;
$flag = 0;
$mes = "";
$iddocente = stringa_html('codice');

$idok = intval($iddocente) - 1000000000;
$username = "doc$idok";

$cognome = stringa_html('cognome');
$nome = stringa_html('nome');
$aa = stringa_html('datadinasca')!=''&stringa_html('datadinasca')!='0000'?stringa_html('datadinasca'):'0001';
$gg = stringa_html('datadinascg')!=''&stringa_html('datadinasca')!='00'?stringa_html('datadinascg'):'01';
$mm = stringa_html('datadinascm')!=''&stringa_html('datadinasca')!='00'?stringa_html('datadinascm'):'01';

$comnasc = stringa_html('idcomn')!=''?stringa_html('idcomn'):'0';
$indirizzo = stringa_html('indirizzo');
$comresi = stringa_html('idcomr')!=''?stringa_html('idcomr'):'0';
$email = stringa_html('email');
$sostegno = stringa_html('sostegno');
$accessowifi = stringa_html('accessowifi');
$gestoremoodle= stringa_html('gestoremoodle');
$telefono = stringa_html('telefono');
$cellulare = stringa_html('telcel');
$s = "UPDATE tbl_docenti SET cognome='$cognome',nome='$nome',datanascita='$aa-$mm-$gg',idcomnasc='$comnasc',indirizzo='$indirizzo',idcomres='$comresi',telefono='$telefono',telcel='$cellulare',email='$email',sostegno='$sostegno',gestoremoodle='$gestoremoodle' WHERE iddocente=$iddocente";
$ss = "UPDATE tbl_utenti SET wifi=$accessowifi WHERE idutente=$iddocente";
if (!$cognome)
{
    $err = 1;
    $mes = "Il cognome non &egrave; stato inserito <br/>";
} else
{
    if (controlla_stringa($cognome) == 1)
    {
        $err = 1;
        $mes = "Il cognome non pu&ograve; contenere valori numerici <br/>";
    }
}

if (!$nome)
{
    $err = 1;
    $mes = $mes . " Il nome non &egrave; stato inserito <br/>";
} else
{
    if (controlla_stringa($nome) == 1)
    {
        $err = 1;
        $mes = "Il nome non pu&ograve; contenere valori numerici <br/>";
    }
}

if ($err == 1)
{
    print("<center><font size='3' color='red'><b>Correzioni:</b></font></center>");
    print("$mes");
    print("<FORM NAME='hid' action='mod_doc.php' method='POST'>");

    print(" <input type ='hidden' size='20' name='codi' value= '$codice'>");
    print(" <input type ='hidden' size='20' name='cog' value= '$cognome'>");
    print(" <input type ='hidden' size='20' name='no' value= '$nome'>");
    print(" <input type ='hidden' size='2' maxlength='2' name='datag' value=$gg><input type ='hidden' size='2' maxlength='2'name='datam' value=$mm><input type ='hidden' size='4' maxlength='4'name='dataa' value=$aa>");
    print(" <input type ='hidden' size='20' name='idcomn' value= '$idcomn'>");
    print(" <input type ='hidden' size='20' name='ind' value= '$indirizzo'> ");
    print(" <input type ='hidden' size='20' name='idcomr' value= '$idcomr'>");
    print("  <input type ='hidden' size='20' name='tel' value= '$telefono'>");
    print(" <input type ='hidden' size='20' name='telc' value= '$telcel'>");
    print(" <input type ='hidden' size='20' name='em' value= '$email'>");
    print(" <input type ='hidden' size='20' name='flag' value= '1'>");

    print("<INPUT TYPE='SUBMIT' VALUE='<< Indietro'>");
    print("</form><br/>");
} else
{
    print "\n<FONT SIZE='+2'><CENTER>";

    if (eseguiQuery($con, $s) && eseguiQuery($con, $ss)){
        // ad
        if($_SESSION['adautosync_disabled'] == "no" && $_SESSION['ad_module_enabled'] == "yes" && $_SESSION['adgroup_docenti'] != "" && $_SESSION['adgroup_docenti'] != null) {
            $queue = array();
            queueCreateUpdateOperation($queue, $username, $nome, $cognome, $accessowifi == '0' ? false : true, $_SESSION['adgroup_docenti']);
            sendQueueToBroker($queue, $_SESSION['broker_host'], $_SESSION['broker_port'], $_SESSION['broker_user'], $_SESSION['broker_pass'], $_SESSION['broker_topic']);
        }
        print "Modifica eseguita";
    }else{
        print "ERRORE NELLA MODIFICA DEI DATI DEL DOCENTE!";
    }
    print "</CENTER></FONT>";
}

mysqli_close($con);
stampa_piede("");

