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

$idclasse = stringa_html('classe');
$idalunno = stringa_html('idalunno');
$datastampa = stringa_html('data');
$firmadirigente = stringa_html('firma');

$schede = new FPDF('P', 'mm', 'A4');

if ($idalunno != $_SESSION['idutente'] && $tipoutente == 'T')
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

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

if ($_SESSION['livello_scuola'] == '1')
    $tiposcheda = 1;
else if ($_SESSION['livello_scuola'] == '2')
    $tiposcheda = 2;
else if ($_SESSION['livello_scuola'] == '3')
{
    if ($anno == '5')
        $tiposcheda = 1;
    if ($anno == '8')
        $tiposcheda = 2;
} else if ($_SESSION['livello_scuola'] == '4')
    $tiposcheda = 3;


// Se non vengo dal tabellone degli scrutini imposto data e dirigente

if ($datastampa == "")
    $datastampa = data_italiana(date('Y-m-d'));
if ($firmadirigente == "")
    $firmadirigente = estrai_dirigente($con);

$contalunni = 0;
foreach ($alunni as $idalunno)
{
    stampa_alunno($schede, $idalunno, $idclasse, $firmadirigente, $datastampa, $tiposcheda, $con, $_SESSION['annoscol'], $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
    $contalunni++;
    $codicefiscale = estrai_codicefiscale($idalunno, $con);
}


if ($contalunni > 1)
    $nomefile = "schede_val_ob_" . decodifica_classe($idclasse, $con) . ".pdf";
else
    $nomefile = "schede_val_ob_" . decodifica_classe($idclasse, $con) . "_$codicefiscale" . ".pdf";
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
    $schede->Cell(190, 6, converti_utf8($_SESSION['nome_scuola']), NULL, 1, "C");
    $schede->SetFont('Times', 'BI', 9);
    $schede->Cell(190, 6, converti_utf8($_SESSION['comune_scuola']), NULL, 1, "C");

    $schede->SetY(60);
    $schede->SetFont('Times', 'B', 12);
    $schede->Cell(190, 8, converti_utf8("SCHEDA VALUTAZIONE OBIETTIVI DI APPRENDIMENTO"), NULL, 1, "C");

    $schede->SetFont('Times', 'B', 12);
    $schede->Cell(190, 8, converti_utf8("$termine"), NULL, 1, "C");

    $schede->SetY(100);

    $schede->SetFont('Times', 'B', 10);
    $schede->Cell(190, 6, converti_utf8("Il Dirigente Scolastico"), NULL, 1, "C");
    $schede->SetFont('Times', '', 9);
    //$schede->MultiCell(190, 6, converti_utf8("Visto il decreto legislativo 13 aprile 2017, n. 62 e, in particolare, l'articolo 9;"));
    //$schede->MultiCell(190, 6, converti_utf8("Visto il decreto ministeriale 3 ottobre 2017, n. 742, concernente l'adozione del modello nazionale di certificazione delle competenze per le scuole del primo ciclo di istruzione;"));
    /*
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
     */
    $schede->SetFont('Times', 'B', 10);
    $schede->SetY($schede->GetY() + 10);
    $schede->Cell(190, 6, converti_utf8("CERTIFICA"), NULL, 1, "C");

    $ris = eseguiQuery($con, "select * from tbl_alunni where idalunno=$alu");
    $rec = mysqli_fetch_array($ris);
    $codfiscale = $rec['codfiscale'];
    $sesso = substr($codfiscale, 9, 2) > 35 ? 'f' : 'm';
    $comunenascita = converti_utf8(decodifica_comune($rec['idcomnasc'], $con));
    $datanascita = converti_utf8(data_italiana($rec['datanascita']));
    $classe = decodifica_classe($idclasse, $con, 1);
    $orelezione = estrai_ore_lezione_classe($idclasse, $con);
    $alunno = decodifica_alunno($alu, $con);
    $annoscolastico = $_SESSION['annoscol'] . "/" . ($_SESSION['annoscol'] + 1);

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
    $schede->Cell(190, 6, converti_utf8("e ha raggiunto i livelli di apprendimento di seguito illustrati."), NULL, 1, "C");

    $schede->AddPage();

// PAGINA COMPETENZE
    $denmateria = "";
    // ESTRAGGO TUTTI GLI OBIETTIVI PER LE VARIE MATERIE DELLA CLASSE
    $query = "select * from tbl_obiettivi, tbl_materie where tbl_obiettivi.idmateria=tbl_materie.idmateria and idclasse=$idclasse order by progrpag";
    $ris = eseguiQuery($con, $query);
    while ($rec = mysqli_fetch_array($ris))
    {
        $posizioneraggiunta = $schede->GetY();
        if ($posizioneraggiunta > 250)
        {
            $schede->AddPage();
            $posY = 0;
        }
        if ($rec['denominazione'] != $denmateria)
        {
            $posY += 10;
            $denmateria = $rec['denominazione'];
            $schede->SetY($posY);
            $schede->SetFont('Times', 'B', 10);
            $schede->Cell(190, 6, converti_utf8($denmateria), 1, NULL, "C");
            $posY += 6;
            $schede->setY($posY);
            $schede->SetFont('Times', 'B', 9);
            $schede->Cell(150, 6, converti_utf8("Obiettivi oggetto di valutazione nel periodo didattico"), 1, NULL, "C");
            $schede->Cell(40, 6, converti_utf8("Livello raggiunto (1)"), 1, NULL, "C");
        }

        // ESTRAGGO LA VALUTAZIONE PER L'OBIETTIVO 
        $query = "select * from tbl_valutazioniobiettivi,tbl_livelliobiettivi,tbl_obiettivi, tbl_materie "
                . " where tbl_valutazioniobiettivi.idobiettivo=tbl_obiettivi.idobiettivo "
                . " and tbl_valutazioniobiettivi.idlivelloobiettivo=tbl_livelliobiettivi.idlivelloobiettivo"
                . " and tbl_obiettivi.idmateria = tbl_materie.idmateria "
                . " and idalunno=$alu and tbl_valutazioniobiettivi.idobiettivo=" . $rec['idobiettivo'] . " and periodo=2 order by progrpag, progressivo";
        $risval = eseguiQuery($con, $query);
        if ($recval = mysqli_fetch_array($risval))
        {
            if ($posizioneraggiunta > 270)
            {
                $schede->AddPage();
                $posY = 0;
            }
            $posY += 6;

            $schede->SetY($posY);
            $schede->SetFont('Times', NULL, 9);
            $schede->MultiCell(150, 6, converti_utf8($recval['obiettivo']), 1, "L");
            $altezzacelle = 6;
            $altezzacelle = $schede->GetY() - $posY;
            $schede->SetY($posY);
            $schede->SetX(160);
            $schede->SetFont('Times', NULL, 9);
            $schede->Cell(40, $altezzacelle, converti_utf8($recval['descrizione']), 1, "C");
            $posY += ($altezzacelle - 6);
        }
    }

    // if ($posY > 120)
    // {
    $schede->AddPage();
    $posY = 10;
    // }

    $query = "SELECT giudizio from tbl_giudizi
						WHERE idalunno=$alu
						AND periodo='" . $_SESSION['numeroperiodi'] . "'";
    $risgiud = eseguiQuery($con, $query);
    if ($recgiud = mysqli_fetch_array($risgiud))
    {

        $giudizio = converti_utf8($recgiud['giudizio']);
        $giudizio = trim($giudizio);
        if (strlen(trim($giudizio)) != 0)
        {
            $posY += 10;
            $schede->SetY($posY);
            $schede->SetFont('Arial', 'B', 8);
            $schede->Cell(190, 8, "GIUDIZIO GENERALE", NULL, 1, "C");

            $posY += 10;
            $schede->SetY($posY);
            $schede->SetFont('Arial', '', 7);
            $schede->Multicell(190, 4, $giudizio, 1, 1);
        }
    }
    // COMPORTAMENTO
    // AGGIUNGO IL VOTO DI COMPORTAMENTO
    // TTTT
    $query = "SELECT denominazione,votounico,note FROM tbl_valutazionifinali,tbl_materie
              WHERE tbl_valutazionifinali.idmateria=tbl_materie.idmateria 
              AND idalunno=$alu
              AND periodo='" . $_SESSION['numeroperiodi'] . "'
              AND tbl_valutazionifinali.idmateria=-1
              ORDER BY denominazione";
    $risvoti = eseguiQuery($con, $query);

    if ($recvoti = mysqli_fetch_array($risvoti))
    {
        $denom = $recvoti['denominazione'];
        $unico = dec_to_pag($recvoti['votounico']);
        $annotazioni = converti_utf8($recvoti['note']);

        $posY = $schede->GetY()+10;
        $schede->SetY($posY);
        $schede->SetFont('Arial', 'B', 8);
        $schede->Cell(190, 8, "COMPORTAMENTO", NULL, 1, "C");

        $posY += 10;
        $schede->SetY($posY);
        $schede->SetFont('Arial', '', 7);
        $schede->Multicell(190, 4, elimina_cr($unico), 1, 1);

        // $schede->Cell(55, 6, "$denom", 0);
        // $valutazione = "";
        // $posY += 10;
        // $schede->SetFont('Arial', '', 7);
        // $schede->SetY($posY);
        // $schede->Multicell(100, 3, elimina_cr($annotazioni), 0, 1);
    }


    // ESITO FINALE
    $esito = "";
    $idesito = 0;
    $votoammissione = 0;
    $creditotot = 0;
    $credito = 0;

    $query = "select * from tbl_esiti where idalunno='$alu'";

    $risesi = eseguiQuery($con, $query);

    if ($recesi = mysqli_fetch_array($risesi))
    {
        //$esito = decodifica_esito($recesi['esito'], $con);
        $esito = estrai_esito($alu, $con);
        $idesito = $recesi['esito'];
        $votoammissione = $recesi['votoammissione'];
        $creditotot = $recesi['creditotot'];
        $credito = $recesi['credito'];
    }
    $posY += 10;
    $schede->SetY($posY);
    $schede->SetFont('Arial', 'B', 10);
    $schede->Multicell(172, 6, "\nATTESTATO\nIn base agli atti d'ufficio ed alle valutazioni dei docenti, l'alunna/o risulta", "", "C");

    $schede->setXY(20, $schede->getY());
    $schede->SetFont('Arial', 'B', 10);

    // $schede->Multicell(172,6,inserisci_new_line($esito),"LR","C");
    $schede->Cell(172, 6, estrai_prima_riga($esito), "", 1, "C");
    $schede->setXY(20, $schede->getY());
    $schede->SetFont('Arial', "B", 10);
    $schede->Cell(172, 6, str_replace("|", " ", estrai_seconda_riga($esito)), "", 1, "C");

    if ((($_SESSION['livello_scuola'] == '2' && decodifica_classe_no_spec($classe, $con) == 3) || ($_SESSION['livello_scuola'] == '3' & decodifica_classe_no_spec($classe, $con) == 8)) & (decodifica_passaggio($idesito, $con) == 0))
    {
        $schede->setXY(20, $schede->getY());
        $schede->SetFont('Arial', 'B', 10);
        $schede->Cell(172, 6, converti_utf8("con giudizio di idoneità di " . $votoammissione . "/10"), "LR", 1, "C");
    } elseif (($_SESSION['livello_scuola'] == '4') && (decodifica_classe_no_spec($classe, $con) == 5) && (decodifica_passaggio($idesito, $con) == 0))
    {
        $schede->setXY(20, $schede->getY());
        $schede->SetFont('Arial', 'B', 10);
        $schede->Cell(172, 6, converti_utf8("con credito scolastico di " . $credito . " (totale: " . $creditotot . ")"), "LR", 1, "C");
    } elseif (($_SESSION['livello_scuola'] == '4') && (decodifica_classe_no_spec($classe, $con) == 4 || decodifica_classe_no_spec($classe, $con) == 3) && (decodifica_passaggio($idesito, $con) == 0))
    {
        $schede->setXY(20, $schede->getY());
        $schede->SetFont('Arial', 'B', 10);
        $schede->Cell(172, 6, converti_utf8("con credito scolastico di " . $credito . " (totale: " . $creditotot . ")"), "LR", 1, "C");
    }
    $posY += 20;
    $schede->SetY($posY);
    $schede->setXY(20, $schede->getY());
    $schede->Cell(172, 6, "", "", 1, "C");

    // PARTE FINALE

    $posY = $schede->GetY() + 5;
    $schede->SetFont('Times', '', 10);
    $schede->SetXY(10, $posY + 15);
    $schede->Cell(60, 8, converti_utf8("Data, $datastampa"));
    $schede->SetXY(140, $posY + 10);
    $schede->Cell(60, 8, converti_utf8("Il Dirigente Scolastico"), 0, 0, "C");
    $schede->SetXY(140, $posY + 30);
    $schede->Cell(60, 8, converti_utf8($firmadir), "B", 0, "C");
    if ($_SESSION['suffisso'] != "")
    {
        $suff = $_SESSION['suffisso'] . "/";
    } else
        $suff = "";
    $schede->SetXY(140, $posY + 16);
    $schede->Image('../abc/' . $suff . 'firmadirigente.png');
    $schede->SetXY(80, $posY + 10);
    $schede->Image('../abc/' . $suff . 'timbro.png');

// LEGENDA INDICATORI
    $schede->SetFont('Times', 'B', 8);
    $posx = 10;

    $posY += 50;
    $schede->SetXY($posx, $posY);
    $schede->Cell(40, 5, "(1) Livello", "B");
    $posx += 40;
    $schede->SetXY($posx, $posY);
    $schede->Cell(150, 5, "Indicatori esplicativi", "B");

    $schede->SetFont('Times', NULL, 8);

    $posx = 10;
    $posY += 5;
    $schede->SetXY($posx, $posY);
    $schede->Cell(40, 5, "Avanzato");
    $posx += 40;
    $schede->SetXY($posx, $posY);
    $schede->MultiCell(150, 5, converti_utf8("L’alunno porta a termine compiti in situazioni note e non note, mobilitando una varietà di risorse sia fornite dal docente sia reperite altrove, in modo autonomo e con continuità."));

    $posY = $schede->GetY();

    $posx = 10;
    $schede->SetXY($posx, $posY);
    $schede->Cell(40, 5, "Intermedio");
    $posx += 40;
    $schede->SetXY($posx, $posY);
    $schede->MultiCell(150, 5, converti_utf8("L’alunno porta a termine compiti in situazioni note in modo autonomo e continuo; risolve compiti in situazioni non note utilizzando le risorse fornite dal docente o reperite altrove, anche se in modo discontinuo e non del tutto autonomo."));

    $posY = $schede->GetY();
    $posx = 10;
    $schede->SetXY($posx, $posY);
    $schede->Cell(40, 5, "Base");
    $posx += 40;
    $schede->SetXY($posx, $posY);
    $schede->MultiCell(150, 5, converti_utf8("L’alunno porta a termine compiti solo in situazioni note e utilizzando le risorse fornite dal docente, sia in modo autonomo ma discontinuo, sia in modo non autonomo, ma con continuità."));

    $posY = $schede->GetY();
    $posx = 10;
    $schede->SetXY($posx, $posY);
    $schede->Cell(40, 5, "In via di prima acquisizione");
    $posx += 40;
    $schede->SetXY($posx, $posY);
    $schede->MultiCell(160, 5, converti_utf8("L’alunno porta a termine compiti solo in situazioni note e unicamente con il supporto del docente e di risorse fornite appositamente."));
}

function elimina_cr($stringa)
{
    // $strpul=converti_utf8($stringa);
    $strpul = str_replace(array("\n", "\r"), " ", $stringa);
    return $strpul;
}

function estrai_prima_riga($stringa)
{

    $posint = strpos($stringa, "|");
    if ($posint != 0)
    {
        $str1 = substr($stringa, 0, $posint);
    } else
    {
        $str1 = $stringa;
    }
    return converti_utf8($str1);
}

function estrai_seconda_riga($stringa)
{

    $posint = strpos($stringa, "|");
    if ($posint != 0)
    {
        $str2 = substr($stringa, $posint + 1);
    } else
    {
        $str2 = "";
    }
    return converti_utf8($str2);
}
