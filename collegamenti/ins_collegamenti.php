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

$titolo = "Inserimento collegamenti web";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_collegamenti.php'>ELENCO COLLEGAMENTI</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$idavviso = stringa_html('idcollegamento');


$oggetto = stringa_html('oggetto');
$testo = stringa_html('testo', false);
$destinatari = is_stringa_html('destinatari') ? stringa_html('destinatari') : array();
$destin = "";
foreach ($destinatari as $d)
    $destin = $destin . $d;

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<H1>connessione al server mysql fallita</H1>");
    exit;
}


$err = 0;
$mes = "";

if ($oggetto == "")
{
    $err = 1;
    $mes = "Non &egrave; stato inserita la descrizione<br/>";
}


if ($testo == "")
{
    $err = 1;
    $mes = $mes . "Non &egrave; stato inserito il link<br/>";
}


if ($err == 1)
{
    print("<center><font size='3' color='red'><b>Correzioni:</b></font></center>");
    print("$mes");
    print("<FORM NAME='hid' method='POST' action='collegamentiweb.php'>");

    print(" <input type ='hidden' size='80' name='descrizione' value= '$oggetto'>");
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
        $sqlt = "insert into tbl_collegamenti(descrizione, link, destinatari) values ('$oggetto','$testo','$destin')";
        $res = mysqli_query($con, inspref($sqlt)) or die("Errore:".inspref($sqlt));

        print("<center><h2>Collegamento inserito correttamente</h2>");

    }
    else
    {
        $sqlt = "update tbl_collegamenti set descrizione='$oggetto',link='$testo',destinatari='$destin' where idcollegamento=$idavviso";
        $res = mysqli_query($con, inspref($sqlt)) or die("Errore:".inspref($sqlt));

        print("<center><h2>Collegamento modificato correttamente</h2>");

    }
}
stampa_piede("");
mysqli_close($con);

