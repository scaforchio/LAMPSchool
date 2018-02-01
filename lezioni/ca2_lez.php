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


/*programma per la cancellazione di un docente
riceve in ingresso i dati del docente*/
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

$titolo = "Cancellazione lezione";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_lez.php'>ELENCO LEZIONI</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$idlezione = stringa_html('idlezione');
$iddocente = stringa_html('iddocente');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);


if (!$con)
{
    print("<H1>connessione al server mysql fallita</H1>");
    exit;
}
$DB = true;
if (!$DB)
{
    print("<H1>connessione al database fallita</H1>");
    exit;
}
$f = "DELETE FROM tbl_firme WHERE idlezione='$idlezione' and iddocente='$iddocente'";
$res = mysqli_query($con, inspref($f)) or die ("Cancellazione firme fallita!");

$q = "SELECT * FROM tbl_firme where idlezione='$idlezione'";
$res = mysqli_query($con, inspref($q)) or die ("Errore ricerca firme!");
if (mysqli_num_rows($res) == 0)
{
    $f = "DELETE FROM tbl_asslezione WHERE idlezione='$idlezione'";
    $res = mysqli_query($con, inspref($f)) or die ("Cancellazione assenze fallita!");
    $f = "DELETE FROM tbl_valutazioniintermedie WHERE idlezione='$idlezione'";
    $res = mysqli_query($con, inspref($f)) or die ("Cancellazione valutazioni fallita!");
    $f = "DELETE FROM tbl_lezioni WHERE idlezione='$idlezione'";
    $res = mysqli_query($con, inspref($f)) or die ("Cancellazione lezione fallita!");
    $f = "DELETE FROM tbl_osssist WHERE idlezione='$idlezione'";
    $res = mysqli_query($con, inspref($f)) or die ("Cancellazione osservazioni sistematiche fallita!");
}
else
{
    $f = "DELETE FROM tbl_valutazioniintermedie WHERE idlezione='$idlezione' and iddocente='$iddocente'";
    $res = mysqli_query($con, inspref($f)) or die ("Cancellazione valutazioni fallita!");
    $f = "DELETE FROM tbl_osssist WHERE idlezione='$idlezione' and iddocente='$iddocente'";
    $res = mysqli_query($con, inspref($f)) or die ("Cancellazione osservazioni sistematiche fallita!");
}
//header("location: ../lezioni/vis_lez.php?iddocente=$iddocente");
print "
        <form method='post' id='formlez' action='../lezioni/vis_lez.php'>
             <input type='hidden' name='iddocente' value='$iddocente'>
        </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formlez').submit();
        }
        </SCRIPT>";

$idclasse = estrai_classe_lezione($idlezione, $con);
$datalezione = estrai_data_lezione($idlezione, $con);
ricalcola_assenze_lezioni_classe($con, $idclasse, $datalezione);
stampa_piede("");
mysqli_close($con);

