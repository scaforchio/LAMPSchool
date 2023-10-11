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


//segnalazione di eventuali errori
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");    //parametri ingresso:codice dell'alunno
@require_once("../lib/funzioni.php");    //parametri di uscita: codice dell'alunno, dati dell'alunno modificati, flag di errore
// istruzioni per tornare alla pagina di login 

require_once '../lib/admqtt.php';

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$strcogn = stringa_html('strcogn');
$strnome = stringa_html('strnome');
$titolo = "Conferma modifica alunno";
$script = "";
stampa_head($titolo, "", $script, "MASP");
stampa_testata("Conferma modifica alunno", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<h1> Connessione al server fallita </h1>");
}
$DB = true;
if (!$DB)
{
    print("<h1> Connessione al database fallita </h1>");
}
$c = stringa_html('idal');
$cs = $c + 2100000000;
$cognome = stringa_html('cognome');
$nome = stringa_html('nome');
$codfiscale = stringa_html('codfiscale');
$certificato = stringa_html('certificato');
$aa = stringa_html('aa');
$mm = stringa_html('mm');
$gg = stringa_html('gg');
$datc = stringa_html('datc');
$indirizzo = stringa_html('indirizzo');
$idal = stringa_html('idal');
$idcomn = stringa_html('idcomn');
$idcomr = stringa_html('idcomr');
$sidi = stringa_html('sidi');
$tel = stringa_html('tel');
$cel = stringa_html('cel');
$mail = stringa_html('mail');
$mail2 = stringa_html('mail2');
$note = stringa_html('note');
$autentrata = stringa_html('autentrata');
$autuscita = stringa_html('autuscita');
$bloccopassword = stringa_html('bloccopassword');
$firmapropria = stringa_html('firmapropria');
$accessowifi = stringa_html('accessowifi');
$censito = stringa_html('censito');
$nocens = stringa_html('nocens');
$idgrupporitardo = stringa_html('idgrupporitardo');
$numeroregistro = stringa_html('numeroregistro');
$provenienza = stringa_html('provenienza');
$titoloammissione = stringa_html('titoloammissione');
$sequenzaiscrizione = stringa_html('sequenzaiscrizione');
$autorizzazioni = stringa_html('autorizzazioni');
$idtut = stringa_html('idtut');
$datacambio = stringa_html('datacambioclasse');
$datacambio = $datacambio != "" ? data_to_db($datacambio) : "";

if($censito == '') $censito = 0;

$sql = "SELECT * FROM tbl_alunni WHERE idalunno='$c'";
$resw = eseguiQuery($con, $sql);
if ($dato = mysqli_fetch_array($resw))
{
    $idclasseold = $dato['idclasse'];
} else
{
    print ("<h2> Dati non trovati </h2>");
}

if($nocens == "1"){
    $sqla = "UPDATE tbl_alunni SET cognome='$cognome', nome='$nome', datanascita='$aa-$mm-$gg',codfiscale='$codfiscale',certificato='$certificato',firmapropria='$firmapropria',autorizzazioni='$autorizzazioni',idgrupporitardo='$idgrupporitardo',";
} else {
    $sqla = "UPDATE tbl_alunni SET cognome='$cognome', nome='$nome', datanascita='$aa-$mm-$gg',codfiscale='$codfiscale',certificato='$certificato',firmapropria='$firmapropria',censito='$censito',autorizzazioni='$autorizzazioni',idgrupporitardo='$idgrupporitardo',";
}
$sqlpass = "UPDATE tbl_utenti SET dischpwd=$bloccopassword, wifi=$accessowifi WHERE idutente=$cs";
if ($idcomn != null)
{
    $sqla = $sqla . "idcomnasc=$idcomn,";
}
$sqla = $sqla . "indirizzo='$indirizzo', ";
if ($idcomr != null)
{
    $sqla = $sqla . "idcomres=$idcomr,";
}
$sqla = $sqla . " codmeccanografico='$sidi',telefono='$tel', telcel='$cel', email='$mail',email2='$mail2',autentrata='$autentrata',autuscita='$autuscita', idclasse=$datc, idtutore=$idal,note='$note',numeroregistro='$numeroregistro',provenienza='$provenienza',titoloammissione='$titoloammissione',sequenzaiscrizione=$sequenzaiscrizione WHERE idalunno=$c";
$err = 0;
$mes = "";

if (!$cognome)
{
    $err = 1;
    $mes = "Il cognome non &egrave; stato inserito<br/> ";
} else
{
    $erro = controlla_stringa($cognome);
    if ($erro == 1)
    {
        $err = 1;
        $mes = $mes . "Il cognome pu&ograve; contenere solo caratteri<br/> ";
    }
}
if (!$nome)
{
    $err = 1;
    $mes = $mes . "Il nome non &egrave; stato inserito<br/> ";
} else
{
    $erro = controlla_stringa($nome);
    if ($erro == 1)
    {
        $err = 1;
        $mes = $mes . "Il nome pu&ograve; contenere solo caratteri<br/> ";
    }
}
if (!$aa)
{
    $err = 1;
    $mes = $mes . " L'anno di nascita non &egrave; stato inserito<br/> ";
}
if (!$mm)
{
    $err = 1;
    $mes = $mes . "Il mese di nascita non &egrave; stato inserito<br/> ";
}
if (!$gg)
{
    $err = 1;
    $mes = $mes . " Il giorno di nascita non &egrave; stato inserito<br/> ";
}
if (!$codfiscale)
{
    $err = 1;
    $mes = $mes . "Il codice fiscale non &egrave; presente<br/> ";
}

if (($aa) && (is_numeric($aa) == false))
{
    $err = 1;
    $mes = $mes . "L' anno di nascita pu&ograve; contenere solo valori numerici <br/>";
}
if (($mm) && (is_numeric($mm) == false))
{
    $err = 1;
    $mes = $mes . " Il mese di nascita pu&ograve; contenere solo valori numerici <br/>";
}
if (($gg) && (is_numeric($gg) == false))
{
    $err = 1;
    $mes = $mes . "Il giorno di nascita pu&ograve; contenere solo valori numerici <br/>";
}
switch ($mm)
{
    case 1:
    case 3:
    case 5:
    case 7:
    case 8:
        {
            if ($gg > 31)
            {
                $err = 1;
                $mes = $mes . "Il giorno di nascita non &egrave; corretto <br/>";
            }
            break;
        }
    case 10:
        {
            if ($gg > 31)
            {
                $err = 1;
                $mes = $mes . "Il giorno di nascita non &egrave; corretto <br/>";
            }
            break;
        }
    case 12:
        {
            if ($gg > 31)
            {
                $err = 1;
                $mes = $mes . "Il giorno di nascita non &egrave; corretto <br/>";
            }
            break;
        }
    case 4:
    case 6:
    case 9:
    case 11:
        {
            if ($gg > 30)
            {
                $err = 1;
                $mes = $mes . "Il giorno di nascita non &egrave; corretto <br/>";
            }
            break;
        }
    case 2:
        {
            if ($gg > 29)
            {
                $err = 1;
                $mes = $mes . "Il giorno di nascita non &egrave; corretto <br/>";
            }
            break;
        }
    default:
        $mes = $mes . "Il mese di nascita non &egrave; corretta<br/>";
}
if ($mm > 12)
{
    $err = 1;
    if ($gg > 31)
    {
        $mes = $mes . "Il giorno di nascita non &egrave; corretto <br/>";
    }
}
if ($err == 0)
{
    eseguiQuery($con, $sqla);
    eseguiQuery($con, $sqlpass);

    $utentealunno = "al" . $_SESSION['suffisso'] . $c;

    if($_SESSION['adautosync_disabled'] == "no" && $_SESSION['ad_module_enabled'] == "yes" && $_SESSION['adgroup_alunni'] != "" && $_SESSION['adgroup_alunni'] != null && $_SESSION['gestioneutentialunni'] == 'yes') {
        $queue = array();
        queueCreateUpdateOperation($queue, $utentealunno, $nome, $cognome, $accessowifi == '0' ? false : true, $_SESSION['adgroup_alunni']);
        sendQueueToBroker($queue, $_SESSION['broker_host'], $_SESSION['broker_port'], $_SESSION['broker_user'], $_SESSION['broker_pass'], $_SESSION['broker_topic']);
    }

    // print "ttt $datacambio";
    if ($idclasseold != $datc)
    {
        if ($datacambio != "")
        {
            $datafine = aggiungi_giorni($datacambio, -1);
            // print "ttt $datafine";
            $querycambioclasse = "insert into tbl_cambiamenticlasse(idalunno,idclasse,datafine) values ($c,$idclasseold,'$datafine')";
            eseguiQuery($con, $querycambioclasse);
        } else
        {
            $querycambioclasse = "delete from tbl_cambiamenticlasse where idalunno='$c'";
            eseguiQuery($con, $querycambioclasse);
        }
    }
    if ($strcogn != "" | $strnome != "")
    {
        print "<div style=\"text-align: center;\">";
        print("Dati modificati correttamente<br/>");
        print(" <form action='vis_alu_ricerca.php' method='POST'>");
        print ("<input type ='hidden' name='strcogn' value='$strcogn'>");
        print ("<input type ='hidden' name='strnome' value='$strnome'>");
        print ("<input type='submit' value=' << Indietro'> ");
        print ("</form>");
    } else
    {
        print "<div style=\"text-align: center;\">";
        print("Dati modificati correttamente<br/>");
        print(" <form action='vis_alu.php' method='POST'>");
        print ("<input type ='hidden' name='idcla' value='" . $dato['idclasse'] . "'>");
        print ("<input type='submit' value=' << Indietro'> ");
        print ("</form>");
    }
} else
{
    print "<div style=\"text-align: center;\">";
    print (" <form action='vis_alu_mod.php' method='POST'>");
    print ("<input type='hidden' name='err' value='$err'>");
    print ("<input type='hidden' name='idal' value='$idal'>");
    print ("<input type='hidden' name='idcla' value='$datc'> ");
    print ("<input type='hidden' name='cognome' value='$cognome'>");
    print ("<input type='hidden' name='nome' value='$nome'> ");
    print ("<input type='hidden' name='codfiscale' value='$codfiscale'> ");
    print ("<input type='hidden' name='certificato' value='$certificato'> ");

    print ("<input type='hidden' name='gg' value='$gg'><input type='hidden' name='mm' value='$mm'><input type='hidden' name='aa' value='$aa'> ");
    print ("<input type='hidden' name='idcomn' value='$idcomn'> ");
    print ("<input type='hidden' name='indirizzo' value='$indirizzo'> ");
    print ("<input type='hidden' name='idcomr' value='$idcomr'> ");
    print ("<input type='hidden' name='sidi' value='$sidi'>");
    print ("<input type='hidden' name='tel' value='$tel'>");
    print ("<input type='hidden' name='cel' value='$cel'> ");
    print ("<input type='hidden' name='mail' value='$mail'> ");
    print ("<input type='hidden' name='mail2' value='$mail2'> ");
    print ("<input type='hidden' name='note' value='$note'> ");
    print ("<input type='hidden' name='autentrata' value='$autentrata'> ");
    print ("<input type='hidden' name='autuscita' value='$autuscita'> ");
    print ("<input type='hidden' name='bloccopassword' value='$bloccopassword'> ");
    print ("<input type='hidden' name='firmapropria' value='$firmapropria'> ");
    print ("<input type='hidden' name='censito' value='$censito'> ");
    print ("<input type='hidden' name='idtut'  value='$idtut'> ");
    print ("<input type ='hidden' name='strcogn' value='$strcogn'>");
    print ("<input type ='hidden' name='strnome' value='$strnome'>");
    print ("<input type='hidden' name='numeroregistro'  value='$numeroregistro'> ");
    print ("<input type='hidden' name='provenienza'  value='$provenienza'> ");
    print ("<input type='hidden' name='autorizzazioni'  value='$autorizzazioni'> ");
    print ("<input type='hidden' name='titoloammissione'  value='$titoloammissione'> ");
    print ("<input type='hidden' name='sequenzaiscrizione'  value='$sequenzaiscrizione'> ");
    print ("<h3> Correzioni: </h3>");
    print $mes;
    print ("<br/><input type='submit' value=' << Indietro'> ");
    print ("</form>");
}


stampa_piede("");
mysqli_close($con);



