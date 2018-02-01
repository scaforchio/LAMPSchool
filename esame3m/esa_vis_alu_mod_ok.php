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


//segnalazione di eventuali errori
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");    //parametri ingresso:codice dell'alunno
@require_once("../lib/funzioni.php");    //parametri di uscita: codice dell'alunno, dati dell'alunno modificati, flag di errore

// istruzioni per tornare alla pagina di login 

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Conferma modifica alunno";
$script = "";
stampa_head($titolo, "", $script, "E");
stampa_testata("Conferma modifica alunno", "", "$nome_scuola", "$comune_scuola");
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

$sql = "SELECT * FROM tbl_alunni WHERE idalunno='$c'";
$resw = mysqli_query($con, inspref($sql));
if ($dato = mysqli_fetch_array($resw))
{
    $idclasseold = $dato['idclasseesame'];
}
else
{

    print ("<h2> Dati non trovati </h2>");

}

$sqla = "UPDATE tbl_alunni SET cognome='$cognome', nome='$nome', datanascita='$aa-$mm-$gg',codfiscale='$codfiscale',certificato='$certificato',";

if ($idcomn != null)
{
    $sqla = $sqla . "idcomnasc=$idcomn,";
}
$sqla = $sqla . "indirizzo='$indirizzo', ";
if ($idcomr != null)
{
    $sqla = $sqla . "idcomres=$idcomr,";
}
$sqla = $sqla . " codmeccanografico='$sidi', idclasseesame=$datc WHERE idalunno=$c";
$err = 0;
$mes = "";

if (!$cognome)
{
    $err = 1;
    $mes = "Il cognome non &egrave; stato inserito<br/> ";
}
else
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
}
else
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
    mysqli_query($con, inspref($sqla)) or die("Errore:" . inspref($sqla, false));

    // print "ttt $datacambio";

    print "<div style=\"text-align: center;\">";
    print("Dati modificati correttamente<br/>");
    print(" <form action='esa_vis_alu.php' method='POST'>");
    print ("<input type ='hidden' name='idcla' value='" . $dato['idclasseesame'] . "'>");
    print ("<input type='submit' value=' << Indietro'> ");
    print ("</form>");


}
else
{
    print "<div style=\"text-align: center;\">";
    print (" <form action='esa_vis_alu_mod.php' method='POST'>");
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

    print ("<br/><input type='submit' value=' << Indietro'> ");
    print ("</form>");

}


stampa_piede("");
mysqli_close($con);



