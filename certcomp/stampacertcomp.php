<?php

session_start();

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
////session_start();


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$classe = stringa_html('classe');
$idalunno = stringa_html('idalunno');
$datastampa = stringa_html('data');
$firmadirigente = stringa_html('firma');

$schede = new FPDF('P', 'mm', 'A4');


if ($idalunno != $_SESSION['idutente'] && $tipoutente == 'T')
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

if ($classe != "")
    $elencoalunni = estrai_alunni_classe_data($classe, $datafinelezioni, $con);

$alunni = array();
if ($classe != "")
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

    $classe = estrai_classe_alunno($idalunno, $con);
}


$anno = decodifica_anno_classe($classe, $con);

if ($livello_scuola == '1')
    $tiposcheda = 1;
else if ($livello_scuola == '2')
    $tiposcheda = 2;
else if ($livello_scuola == '3')
{
    if ($anno == '5')
        $tiposcheda = 1;
    if ($anno == '8')
        $tiposcheda = 2;
}
else if ($livello_scuola == '4')
    $tiposcheda = 3;


// Se non vengo dal tabellone degli scrutini imposto data e dirigente

if ($datastampa == "")
    $datastampa = data_italiana(date('Y-m-d'));
if ($firmadirigente == "")
    $firmadirigente = estrai_dirigente($con);

$contalunni = 0;
foreach ($alunni as $idalunno)
{
    stampa_alunno($schede, $idalunno, $classe, $firmadirigente, $datastampa, $tiposcheda, $con, $annoscol, $nome_scuola, $comune_scuola);
    $contalunni++;
    $codicefiscale = estrai_codicefiscale($idalunno, $con);
}


if ($contalunni > 1)
    $nomefile = "schede_competenze_" . decodifica_classe($classe, $con) . ".pdf";
else
    $nomefile = "schede_competenze_" . decodifica_classe($classe, $con) . "_$codicefiscale" . ".pdf";
$nomefile = str_replace(" ", "_", $nomefile);

$schede->Output($nomefile, "I");


mysqli_close($con);

function stampa_alunno(&$schede, $alu, $idclasse, $firmadir, $datastampa, $tiposcheda, $con, $annoscol, $nome_scuola, $comune_scuola)
{

// $tiposcheda 1 = primaria, 2 = secondaria primo grado, 3 = secondaria secondo grado
// PAGINA DATI ANAGRAFICI

    $schede->AddPage();

    $schede->Image('../immagini/repubblica.png', 100, NULL, 13, 15);

//$schede->Image('../immagini/miur.png',35,NULL,120,10);

    $schede->SetFont('Times', 'B', 10);
    $schede->Cell(190, 6, converti_utf8("$nome_scuola"), NULL, 1, "C");
    $schede->SetFont('Times', 'BI', 9);
    $schede->Cell(190, 6, converti_utf8("$comune_scuola"), NULL, 1, "C");

    $schede->setY(60);
    $schede->SetFont('Times', 'B', 12);
    $schede->Cell(190, 8, converti_utf8("CERTIFICAZIONE DELLE COMPETENZE"), NULL, 1, "C");
    if ($tiposcheda == 1)
        $termine = "AL TERMINE DELLA SCUOLA PRIMARIA";
    IF ($tiposcheda == 2)
        $termine = "AL TERMINE DEL PRIMO CICLO DI ISTRUZIONE";
    if ($tiposcheda == 3)
        $termine = "AL TERMINE DELLA SCUOLA SECONDARIA";
    $schede->SetFont('Times', 'B', 12);
    $schede->Cell(190, 8, converti_utf8("$termine"), NULL, 1, "C");

    $schede->setY(100);

    $schede->SetFont('Times', 'B', 10);
    $schede->Cell(190, 6, converti_utf8("Il Dirigente Scolastico"), NULL, 1, "C");
    $schede->SetFont('Times', '', 9);
    $schede->MultiCell(190, 6, converti_utf8("Visto il decreto legislativo 13 aprile 2017, n. 62 e, in particolare, l'articolo 9;"));
    $schede->MultiCell(190, 6, converti_utf8("Visto il decreto ministeriale 3 ottobre 2017, n. 742, concernente l'adozione del modello nazionale di certificazione delle competenze per le scuole del primo ciclo di istruzione;"));

    if ($tiposcheda == 1)
    {
        $schede->MultiCell(190, 6, converti_utf8("Visto il decreto legislativo 13 aprile 2017, n. 62 e, in particolare, l'articolo 9;"));
        $schede->MultiCell(190, 6, converti_utf8("Visto il decreto ministeriale 3 ottobre 2017, n. 742, concernente l'adozione del modello nazionale di certificazione delle competenze per le scuole del primo ciclo di istruzione;"));
        $schede->MultiCell(190, 6, converti_utf8("Visti gli atti d'ufficio relativi alle valutazioni espresse in sede di scrutinio finale dagli insegnati di classe al termine del quinto anno di corso della scuola primaria;"));
        $schede->MultiCell(190, 6, converti_utf8("tenuto conto del percorso scolastico quinquennale:"));
    }
    if ($tiposcheda == 2)
    {
        $schede->MultiCell(190, 6, converti_utf8("Visto il decreto legislativo 13 aprile 2017, n. 62 e, in particolare, l'articolo 9;"));
        $schede->MultiCell(190, 6, converti_utf8("Visto il decreto ministeriale 3 ottobre 2017, n. 742, concernente l'adozione del modello nazionale di certificazione delle competenze per le scuole del primo ciclo di istruzione;"));
        $schede->MultiCell(190, 6, converti_utf8("Visti gli atti d'ufficio relativi alle valutazioni espresse in sede di scrutinio finale dal Consiglio di classe del terzo anno di corso della scuola secondaria di primo grado;"));
        $schede->MultiCell(190, 6, converti_utf8("tenuto conto del percorso scolastico ed in riferimento al Profilo dello studente al termine del primo ciclo di istruzione:"));
    }
    if ($tiposcheda == 3)
    {
        
    }

    $schede->SetFont('Times', 'B', 10);
    $schede->setY($schede->GetY() + 10);
    $schede->Cell(190, 6, converti_utf8("CERTIFICA"), NULL, 1, "C");

    $ris = eseguiQuery($con,"select * from tbl_alunni where idalunno=$alu");
    $rec = mysqli_fetch_array($ris);
    $codfiscale = $rec['codfiscale'];
    $sesso = substr($codfiscale, 9, 2) > 35 ? 'f' : 'm';
    $comunenascita = converti_utf8(decodifica_comune($rec['idcomnasc'], $con));
    $datanascita = converti_utf8(data_italiana($rec['datanascita']));
    $classe = decodifica_classe($idclasse, $con, 1);
    $orelezione = estrai_ore_lezione_classe($idclasse, $con);
    $alunno = decodifica_alunno($alu, $con);
    $annoscolastico = $annoscol . "/" . ($annoscol + 1);


    if ($sesso == 'f')
    {
        $schede->Cell(190, 6, converti_utf8("che l'alunna $alunno"), NULL, 1, "C");
        $schede->Cell(190, 6, converti_utf8("nata a $comunenascita il $datanascita"), NULL, 1, "C");
    } else
    {
        $schede->Cell(190, 6, converti_utf8("che l'alunno $alunno"), NULL, 1, "C");
        $schede->Cell(190, 6, converti_utf8("nato a $comunenascita il $datanascita"), NULL, 1, "C");
    }
    $schede->Cell(190, 6, converti_utf8("ha frequentato nell'anno scolastico $annoscolastico la classe $classe ,"), NULL, 1, "C");
    $schede->Cell(190, 6, converti_utf8("con orario settimanale di $orelezione ore"), NULL, 1, "C");
    $schede->Cell(190, 6, converti_utf8("e ha raggiunto i livelli di competenza di seguito illustrati."), NULL, 1, "C");

    $schede->AddPage();

// PAGINA COMPETENZE
    $schede->SetFont('Times', 'B', 8);
    $posx = 10;
    $posy = 10;
    $schede->SetXY($posx, $posy);
    $schede->Cell(5, 12, "", 1);
    $posx += 5;
    $schede->SetXY($posx, $posy);
    $schede->Cell(45, 12, "Competenze chiave europee", "1", 0, "C", 0);
    $posx += 45;
    $schede->SetXY($posx, $posy);
    $schede->Cell(120, 6, "Competenze del profilo dello studente", "TLR", 0, "C");
    $schede->SetXY($posx, $posy + 6);
    $schede->Cell(120, 6, "al termine del primo ciclo di istruzione", "BLR", 0, "C");
    $posx += 120;
    $schede->SetXY($posx, $posy);
    $schede->Cell(20, 12, "Livello (1)", 1, 0, "C");
    $posy += 12;

    $livscuola = $tiposcheda;

// Cerco tutte le competenze previste per la classe
    $query = "select * from tbl_certcompcompetenze where livscuola='$livscuola' and valido order by numprogressivo,idccc";
    $ris = eseguiQuery($con, $query);

    while ($rec = mysqli_fetch_array($ris))
    {
        $schede->SetFont('Times', '', 8);
        $numeroprogressivo = $rec['numprogressivo'];
        $compcheuropea = $rec['compcheuropea'];
        $compprofilo = $rec['compprofilo'];
        $idccc = $rec['idccc'];

        if ($compcheuropea != '')
        {
            $schede->SetXY(60, $posy);
            $schede->MultiCell(120, 6, converti_utf8($compprofilo), 0, "J");
            $altezzaprof = $schede->GetY() - $posy;
            $schede->SetXY(15, $posy);
            $schede->MultiCell(45, 6, converti_utf8($compcheuropea), 0, "L");
            $altezzaeur = $schede->GetY() - $posy;
            $altezza = ($altezzaeur > $altezzaprof) ? $altezzaeur : $altezzaprof;

            $schede->SetXY(10, $posy);
            $schede->MultiCell(5, $altezza, converti_utf8($numeroprogressivo), 1);

            $schede->Rect(15, $posy, 45, $altezza);
            $schede->Rect(60, $posy, 120, $altezza);





// Cerco eventuali competenze registrate per l'alunno


            $query = "select * from tbl_certcompvalutazioni, tbl_certcomplivelli "
                    . "where tbl_certcompvalutazioni.idccl=tbl_certcomplivelli.idccl "
                    . "and idccc=$idccc "
                    . "and idalunno=$alu";
            $risliv = eseguiQuery($con, $query);

            if ($recliv = mysqli_fetch_array($risliv))
            {
                $livello = $recliv['livello'];
                $livello = substr($livello, 0, 1);
                $schede->SetXY(180, $posy);
                $schede->SetFont('Times', '', 14);
                $schede->MultiCell(20, $altezza, $livello, 1, "C");
            } else
            {
                $schede->SetXY(180, $posy);
                $schede->SetFont('Times', '', 14);
                $schede->MultiCell(20, $altezza, "", 1, "C");
            }
            $posy += $altezza;
        } else
        {
            $schede->SetXY(15, $posy);


            $query = "select * from tbl_certcompvalutazioni "
                    . "where idccc=$idccc "
                    . "and idalunno=$alu";
            $risliv = eseguiQuery($con, $query);

            if ($recliv = mysqli_fetch_array($risliv))
                $giud = $recliv['giud'];

            $schede->MultiCell(185, 6, converti_utf8($compprofilo . "\n" . elimina_cr($giud)), 0, "J");
            $altezzaprof = $schede->GetY() - $posy;

            $altezza = $altezzaprof;

            $schede->SetXY(10, $posy);
            $schede->MultiCell(5, $altezza, converti_utf8($numeroprogressivo), 1);

            $schede->Rect(15, $posy, 185, $altezza);

            $posy += $altezza;
        }
    }

    $schede->SetFont('Times', 'I', 7);
    $schede->Cell(60, 4, converti_utf8("* Sense of initiative and entrepreneurship nella Raccomandazione europea e del Consiglio del 18 dicembre 2006"), 0, 0);

    $schede->SetFont('Times', '', 10);
    $schede->SetXY(10, $posy + 5);
    $schede->Cell(60, 8, converti_utf8("Data, $datastampa"));
    $schede->SetXY(140, $posy + 10);
    $schede->Cell(60, 8, converti_utf8("Il Dirigente Scolastico"), 0, 0, "C");
    $schede->SetXY(140, $posy + 30);
    $schede->Cell(60, 8, converti_utf8($firmadir), "B", 0, "C");
    if ($_SESSION['suffisso'] != "")
    {
        $suff = $_SESSION['suffisso'] . "/";
    } else
        $suff = "";
    $schede->setXY(140, $posy + 16);
    $schede->Image('../abc/' . $suff . 'firmadirigente.png');
    $schede->SetXY(80, $posy + 10);
    $schede->Image('../abc/' . $suff . 'timbro.png');

// LEGENDA INDICATORI
    $schede->SetFont('Times', 'B', 6);
    $posx = 10;
    $posy += 50;
    $schede->SetXY($posx, $posy);
    $schede->Cell(30, 5, "(1) Livello", "B");
    $posx += 30;
    $schede->SetXY($posx, $posy);
    $schede->Cell(160, 5, "Indicatori esplicativi", "B");

    $posy += 5;

    $query = "select * from tbl_certcomplivelli where livscuola = '$livscuola' and valido order by livello";
    $ris = eseguiQuery($con, $query);

    while ($rec = mysqli_fetch_array($ris))
    {
        $schede->SetFont('Times', '', 7);
        $livello = $rec['livello'];
        $indicatoreesplicativo = $rec['indicatoreesplicativo'];

        $schede->SetXY(40, $posy);
        $schede->MultiCell(160, 3, converti_utf8($indicatoreesplicativo), 0, "J");
        $altezza = $schede->GetY() - $posy;
        $schede->SetXY(10, $posy);
        $schede->SetFont('Times', 'I', 7);
        $schede->Cell(30, 3, converti_utf8($livello), 0, "L");

        $posy += $altezza;
    }
}

function elimina_cr($stringa)
{
    // $strpul=converti_utf8($stringa);
    $strpul = str_replace(array("\n", "\r"), " ", $stringa);
    return $strpul;
}
