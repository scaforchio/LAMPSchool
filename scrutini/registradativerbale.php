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

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Registrazione dati verbale";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$idscrutinio = stringa_html('idscrutinio');
$integrativo = stringa_html('integrativo');
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$dataverbale = stringa_html('dataverbale');
$orainizioscrutinio = stringa_html('orainizioscrutinio');
$orafinescrutinio = stringa_html('orafinescrutinio');
$luogoscrutinio = stringa_html('luogoscrutinio');
$testo1 = stringa_html('testo1');
$testo2 = stringa_html('testo2');
$testo3 = stringa_html('testo3');
$testo4 = stringa_html('testo4');
$criteri = stringa_html('criteri');
$segretario = stringa_html('segretario');
$sostituzioni = "";

$query = "SELECT idclasse,periodo FROM tbl_scrutini WHERE idscrutinio=$idscrutinio";
$ris = mysqli_query($con, inspref($query));
$rec = mysqli_fetch_array($ris);
$idclasse = $rec['idclasse'];
$periodo = $rec['periodo'];

$querydoc = "SELECT DISTINCT cognome,nome,iddocente FROM tbl_docenti
	        WHERE iddocente=1000000000
	        ";
$risdoc = mysqli_query($con, inspref($querydoc)) or die ("Errore nella query: " . inspref($querydoc, false));
while ($recdoc = mysqli_fetch_array($risdoc))
{
    $postsost = "docsost" . $recdoc['iddocente'];
    if ($_POST[$postsost] != "")
    {
        $sostituzioni .= "|" . $recdoc['iddocente'] . "<" . $_POST[$postsost];
    }
}

$querydoc = "SELECT DISTINCT cognome,nome,tbl_docenti.iddocente FROM tbl_cattnosupp,tbl_docenti
	        WHERE tbl_cattnosupp.iddocente=tbl_docenti.iddocente
	        AND tbl_cattnosupp.idclasse=" . $idclasse . "
	        AND tbl_cattnosupp.iddocente<>1000000000
	        ORDER BY cognome,nome";
$risdoc = mysqli_query($con, inspref($querydoc)) or die ("Errore nella query: " . inspref($querydoc, false));
while ($recdoc = mysqli_fetch_array($risdoc))
{
    $postsost = "docsost" . $recdoc['iddocente'];
    if ($_POST[$postsost] != "")
    {
        $sostituzioni .= "|" . $recdoc['iddocente'] . "<" . $_POST[$postsost];
    }

    $postsuppl = "suppl" . $recdoc['iddocente'];
    if (isset($_POST[$postsuppl]))
    {
        $sostituzioni .= "§" . $recdoc['iddocente'];
    }
}


$query = "UPDATE tbl_scrutini SET dataverbale='" . data_to_db($dataverbale) . "',orainizioscrutinio='$orainizioscrutinio',
         orafinescrutinio='$orafinescrutinio',luogoscrutinio='$luogoscrutinio',sostituzioni='$sostituzioni',
         testo1='$testo1', testo2='$testo2',testo3='$testo3',testo4='$testo4',criteri='$criteri',
         segretario='$segretario' 
         WHERE idscrutinio=$idscrutinio";

$ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));

if ($periodo < $numeroperiodi)
{
    print "
        <form method='post' id='formchiscr' action='../scrutini/riepvoti.php'>
        <input type='hidden' name='cl' value='$idclasse'>
        <input type='hidden' name='periodo' value='$periodo'>
        </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formchiscr').submit();
        }
        </SCRIPT>";
}
else
{
    print "
        <form method='post' id='formchiscr' action='../scrutini/riepvotifinali.php'>
        <input type='hidden' name='cl' value='$idclasse'>
        <input type='hidden' name='periodo' value='$periodo'>
        <input type='hidden' name='integrativo' value='$integrativo'>
        </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formchiscr').submit();
        }
        </SCRIPT>";
}

stampa_piede("");


