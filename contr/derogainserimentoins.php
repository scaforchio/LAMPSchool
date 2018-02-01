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


/*
     INSERIMENTO DELLE CATTEDRE
*/


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

$suff=$_SESSION['suffisso']."/";
if ($suff=="/") $suff="";
// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$iddocente = stringa_html('docente');

//
//    Parte iniziale della pagina
//

$titolo = "Deroga a limite inserimento dati";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

//
//    Fine parte iniziale della pagina
//

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$sqld = "insert into tbl_derogheinserimento(iddocente,data)
         values ($iddocente,'".date('Y-m-d')."')";
mysqli_query($con, inspref($sqld)) or die("Errore:". inspref($sqld,false));

inserisci_log($_SESSION['userid'] . "§" . date('m-d|H:i:s') . "§" . $_SESSION['indirizzoip'] . "§DEROGA PER DOCENTE ".estrai_dati_docente($iddocente,$con)."");

print("<br><center><b>Deroga abilitata! E' necessario che il docente rieffettui il login perchè abbia effetto.</b></center>");
print "</form>";

mysqli_close($con);
stampa_piede("");

