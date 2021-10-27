<?php

require_once '../lib/req_apertura_sessione.php';

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



$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

// istruzionii per tornare alla pagina di login se non c'� una sessione valida



$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$idclasse = stringa_html('idclasse');
$idalunno = stringa_html('idalunno');
$datastampa = data_italiana(date('Y-m-d'));
$firmadirigente = estrai_dirigente($con);

$schede = new FPDF('P', 'mm', 'A4');

/*
  if ($idalunno != $_SESSION['idutente'] && $tipoutente == 'T')
  {
  header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
  die;
  }
 */
if ($idclasse != "")
    $elencoalunni = estrai_alunni_classe_data($idclasse, $_SESSION['datafinelezioni'], $con);

$alunni = array();
if ($idclasse != "")
{

    $query = "select idalunno from tbl_alunni where idalunno in ($elencoalunni) order by cognome,nome";
    $ris = eseguiQuery($con, $query);
    while ($val = mysqli_fetch_array($ris))
    {
        $alunni[] = $val['idalunno'];
    }
} else
{

    $alunni[] = $idalunno;

    $idclasse = estrai_classe_alunno($idalunno, $con);
}


$anno = decodifica_anno_classe($idclasse, $con);




// Se non vengo dal tabellone degli scrutini imposto data e dirigente


$contalunni = 0;
foreach ($alunni as $idalunno)
{
    stampa_alunno($schede, $idalunno, $idclasse, $firmadirigente, $datastampa, $con, $_SESSION['annoscol'], $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
    $contalunni++;
    $codicefiscale = estrai_codicefiscale($idalunno, $con);
}


if ($contalunni > 1)
    $nomefile = "consorient_" . decodifica_classe($idclasse, $con) . ".pdf";
else
    $nomefile = "consorient_" . decodifica_classe($idclasse, $con) . "_$codicefiscale" . ".pdf";
$nomefile = str_replace(" ", "_", $nomefile);

$schede->Output($nomefile, "I");


mysqli_close($con);

function stampa_alunno(&$schede, $alu, $idclasse, $firmadir, $datastampa, $con, $annoscol, $nome_scuola, $comune_scuola)
{

// $tiposcheda 1 = primaria, 2 = secondaria primo grado, 3 = secondaria secondo grado
// PAGINA DATI ANAGRAFICI

    $schede->AddPage();
    if ($_SESSION['suffisso'] != "")
        $suff = $_SESSION['suffisso'] . "/";
    else
        $suff = "";
    $schede->Image('../abc/' . $suff . 'testata.jpg', 17, NULL, 176, 45);

//$schede->Image('../immagini/miur.png',35,NULL,120,10);


    $schede->SetY(60);
    $schede->SetFont('Times', 'B', 14);
    $schede->Cell(190, 8, converti_utf8("CONSIGLIO ORIENTATIVO"), NULL, 1, "C");
    $annoscolastico = $_SESSION['annoscol'] . "/" . ($_SESSION['annoscol'] + 1);
    $schede->SetY(68);
    $schede->SetFont('Times', 'B', 12);
    $schede->Cell(190, 8, converti_utf8("ANNO SCOLASTICO $annoscolastico"), NULL, 1, "C");
    $schede->SetY(80);
    $schede->SetX(25);
    $schede->SetFont('Times', '', 12);
    $schede->MultiCell(160, 8, converti_utf8("Dopo aver svolto uno specifico lavoro di orientamento e considerata la situazione scolastica dell'alunno"), NULL, "J");
    $schede->SetY(100);
    $schede->SetX(25);
    $schede->SetFont('Times', 'B', 12);
    $schede->MultiCell(160, 8, converti_utf8(decodifica_alunno($alu, $con)), NULL, "C");
    $schede->SetY(120);
    $schede->SetX(25);
    $schede->SetFont('Times', '', 12);
    $schede->MultiCell(160, 8, converti_utf8("in base a quanto è stato rilevato negli anni precedenti e a quanto è emerso fino a questo momento in relazione a:"), NULL, "J");

    $schede->SetY(140);
    $schede->SetX(40);
    $schede->Cell(190, 8, converti_utf8("- il grado di raggiungimento degli obiettivi educativi"), NULL, 1, "L");
    $schede->SetY(148);
    $schede->SetX(40);
    $schede->Cell(190, 8, converti_utf8("- il metodo di lavoro"), NULL, 1, "L");
    $schede->SetY(156);
    $schede->SetX(40);
    $schede->Cell(190, 8, converti_utf8("- le attitudini rilevate"), NULL, 1, "L");
    $schede->SetY(164);
    $schede->SetX(40);
    $schede->Cell(190, 8, converti_utf8("- gli interessi e le aspirazioni personali"), NULL, 1, "L");

    $classe = decodifica_classe($idclasse, $con, 1);
    $schede->SetY(175);
    $schede->SetX(25);
    $schede->SetFont('Times', '', 12);
    $schede->MultiCell(160, 8, converti_utf8("il Consiglio della Classe $classe consiglia all'alunno, per il prossimo anno scolastico, l'iscrizione al seguente istituto:"), NULL, "J");


    $query = "select consiglioorientativo from tbl_consorientativi where idalunno=$alu";
    $ris = eseguiQuery($con, $query);
    $rec = mysqli_fetch_array($ris);
    $consorientativo = $rec['consiglioorientativo'];


    $schede->SetY(200);
    $schede->SetX(25);
    $schede->SetFont('Times', 'B', 14);
    $schede->MultiCell(160, 8, converti_utf8("$consorientativo"), NULL, "C");


    $posy = 220;
    $schede->SetFont('Times', '', 10);
    $schede->SetXY(10, $posy + 5);
    $schede->Cell(60, 8, converti_utf8("Data, $datastampa"));
    $schede->SetXY(140, $posy + 10);
    $schede->Cell(60, 8, converti_utf8("Il Dirigente Scolastico"), 0, 0, "C");
    $schede->SetXY(140, $posy + 17);
    $schede->Cell(60, 8, "(" . converti_utf8($firmadir) . ")", "0", 0, "C");
    if ($_SESSION['suffisso'] != "")
    {
        $suff = $_SESSION['suffisso'] . "/";
    } else
        $suff = "";
    $schede->setXY(140, $posy + 24);
    $schede->Image('../abc/' . $suff . 'firmadirigente.png');
    $schede->SetXY(80, $posy + 10);
    $schede->Image('../abc/' . $suff . 'timbro.png');
}

function elimina_cr($stringa)
{
    // $strpul=converti_utf8($stringa);
    $strpul = str_replace(array("\n", "\r"), " ", $stringa);
    return $strpul;
}
