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

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$idclasse = stringa_html('idclasse');

$datascrutinio = stringa_html('datascrutinio');
$testo1 = stringa_html('testo1');
$testo2 = stringa_html('testo2');
$testo3 = stringa_html('testo3');
$testo4 = stringa_html('testo4');
$orainizio = stringa_html('orainizio');
$orafine = stringa_html('orafine');


$luogoscrutinio = stringa_html('luogoscrutinio');
$idcommissione = stringa_html('idcommissione');

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


//
//    Parte iniziale della pagina
//

$titolo = "Inserimento dati dell'esame";
$script = "";

// PRELIEVO DATI DA INSERIRE



stampa_head($titolo, "", $script, "E");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$query="update tbl_esami3m set
        datascrutinio='".data_to_db($datascrutinio)."',
        luogoscrutinio='$luogoscrutinio',
        testo1='$testo1',
        testo2='$testo2',
        testo3='$testo3',
        testo4='$testo4',
        orainizio='$orainizio',
        orafine='$orafine',
        idcommissione='$idcommissione'
        where idclasse=$idclasse";
mysqli_query($con,inspref($query));





print "
        <form method='post' id='formscr' action='../esame3m/rieptabesame.php'>
        <input type='hidden' name='cl' value='$idclasse'>
        </form>
        <SCRIPT language='JavaScript'>
           document.getElementById('formscr').submit();
        </SCRIPT>";
// fclose($fp);
mysqli_close($con);

stampa_piede("");

