<?php session_start();

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
/*programma per l'inserimento o modifica di un avviso
*/
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Inserimento avviso";
$script = "";
stampa_head($titolo, "", $script, "PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_avvisi.php'>ELENCO AVVISI</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$idavviso = stringa_html('idavviso');
$inizio = stringa_html('inizio');
$fine = stringa_html('fine');

$inizio = str_replace(" ", "", $inizio);
$fine = str_replace(" ", "", $fine);
$inizio = ItaAme2Ita($inizio);
$fine = ItaAme2Ita($fine);
$oggetto = stringa_html('oggetto');
$testo = stringa_html('testo', false);
$destinatari = is_stringa_html('destinatari') ? stringa_html('destinatari') : array();
$destin = "";
foreach ($destinatari as $d)
{

    $destin = $destin . $d;

}
if (strpos($destin,"S")===false & !(strpos($destin,"D")===false))
    $destin.='S';
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
$mes = "";

if ($oggetto == "")
{
    $err = 1;
    $mes = "Non &egrave; stato inserito l'oggetto<br/>";
}


if ($testo == "")
{
    $err = 1;
    $mes = $mes . "Non &egrave; stato inserito il testo<br/>";
}

if (!ControlloData($inizio))
{
    $err = 1;
    $mes = $mes . "Data inizio non valida<br/>";
}

if (!ControlloData($fine))
{
    $err = 1;
    $mes = $mes . "Data fine non valida<br/>";
}

if ($err == 1)
{
    print("<center><font size='3' color='red'><b>Correzioni:</b></font></center>");
    print("$mes");
    print("<FORM NAME='hid' method='POST' action='avvisi.php'>");

    print(" <input type ='hidden' size='80' name='oggetto' value= '$oggetto'>");
    print(" <input type ='hidden' size='10' name='inizio' value= '$inizio'>");
    print(" <input type ='hidden' size='10' name='fine' value= '$fine'>");
    print(" <input type ='hidden' name='testo' value='$testo'>");
    print("<center><INPUT TYPE='SUBMIT' VALUE='<< Indietro'></center>");
    print("</form><br/>");


}
else
{
    if ($idavviso == '')
    {
        $sqlt = "insert into tbl_avvisi(inizio, fine, oggetto, testo, destinatari) values ('" . data_to_db($inizio) . "','" . data_to_db($fine) . "','$oggetto','$testo','$destin')";
        $res = mysqli_query($con, inspref($sqlt));

        print("<center><h2>Avviso inserito correttamente</h2>");

    }
    else
    {
        $sqlt = "update tbl_avvisi set inizio='" . data_to_db($inizio) . "',fine='" . data_to_db($fine) . "',oggetto='$oggetto',testo='$testo',destinatari='$destin' where idavviso=$idavviso";
        $res = mysqli_query($con, inspref($sqlt));

        print("<center><h2>Avviso modificato correttamente</h2>");

    }
}
stampa_piede("");
mysqli_close($con);

