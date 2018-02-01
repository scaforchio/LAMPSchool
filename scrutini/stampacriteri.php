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
require_once("../lib/fpdf/fpdf.php");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$idclasse = stringa_html("classe");
$periodo = stringa_html("periodo");


//  Richiamare funzione di stampa passando gli array come parametri


$schede = new FPDFPAG();
// $schede->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
// $schede->SetFont('DejaVu','',14);

//
// Estraggo tutti i valori per sostituzione parametri di stampa
//
$query = "select * from tbl_scrutini where idclasse=$idclasse and periodo=$periodo";
$ris = mysqli_query($con, inspref($query));
$rec = mysqli_fetch_array($ris);

$dataverbale = data_italiana($rec['dataverbale']);
$orainizio = substr($rec['orainizioscrutinio'], 0, 5);
$orafine = substr($rec['orafinescrutinio'], 0, 5);
$luogo = $rec['luogoscrutinio'];
$sostituzioni = $rec['sostituzioni'];
$codsegretario = $rec['segretario'];
$classe = decodifica_classe_no_spec($idclasse, $con, 1);
$classe .= " $plesso_specializzazione ";
$classe .= decodifica_classe_spec($idclasse, $con);

$criteri = $rec['criteri'];

$criteri = str_replace("\r", "", $criteri);

$nomefile = "Criteri_" . $idclasse . ".pdf";

$schede->AddPage();
if ($_SESSION['suffisso'] != "") $suff = $_SESSION['suffisso'] . "/";
else $suff = "";
$schede->Image('../abc/' . $suff . 'testata.jpg', NULL, NULL, 190, 43);

$posY = 60;

$schede->SetFont('Times', '', 12);
$schede->setXY(10, $posY);
$testata = converti_utf8("CRITERI GENERALI DI VALUTAZIONE CLASSE $classe");
$schede->MultiCell(190, 8, $testata,1, "C");
$posY += 10;

$schede->SetXY(10, $posY);
$schede->SetFont('Times', '', 10);
$schede->multicell(190,5,converti_utf8($criteri),'J');


//$schede->FooterAll();   
$schede->Output($nomefile, "I");

mysqli_close($con);


