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

$idclasse = stringa_html('classe');
$idalunno = stringa_html('idalunno');
$datastampa = stringa_html('data');
$firmadirigente = stringa_html('firma');
$votitab = stringa_html('votitab');
$votinp = stringa_html('votinp');
//print "Votinp".$votinp;
$credtab = stringa_html('credtab');
$voamtab = stringa_html('voamtab');
$mediatab = stringa_html('mediatab');
// print $votitab;
$periodo = stringa_html('periodo');


/*
 * Salvo o leggo i dati di modifica delle stampe
 */
if ($firmadirigente != "" && $datastampa != "")
{
    aggiorna_data_firma_scrutinio($datastampa, $firmadirigente, $periodo, $idclasse, $con);
}
else
{
    $firmadirigente = estrai_firma_scrutinio($idclasse, $periodo, $con);
    $datastampa = estrai_data_stampa($idclasse, $periodo, $con);
}


// Estraggo tutti i voti e li metto in arrays

$codmaterie = array();
$codalunni = array();
$voti = array();

if ($periodo == '9')
{
    $conddebito = " and tbl_valutazionifinali.idalunno in (select idalunno from tbl_esiti WHERE (validita='1' OR validita='2') AND esito=0) ";
}
else
{
    $conddebito = "";
}

$query = "SELECT tbl_valutazionifinali.*,tbl_materie.tipovalutazione FROM tbl_valutazionifinali,tbl_alunni,tbl_materie
	          WHERE tbl_valutazionifinali.idalunno=tbl_alunni.idalunno
	          AND tbl_valutazionifinali.idmateria=tbl_materie.idmateria
	          $conddebito
	          AND idclasse=$idclasse
	          AND tbl_materie.progrpag <> 100
	          AND periodo='$numeroperiodi'";

//print inspref($query);
$risvalu = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con) . $query);
while ($recval = mysqli_fetch_array($risvalu))
{
    $codalunni[] = $recval['idalunno'];
    $codmaterie[] = $recval['idmateria'];
    $voti[] = $recval['votounico'];
}


$schede = new FPDF('L', 'mm', 'A3');
$schede->AddFont('palacescript', '', 'palacescript.php'); // Font del Ministero
$schede->AddPage();

// $datascrutinio=data_italiana(datascrutinio($idclasse, $periodo, $con));

$posX = 0;
$posY = 0;
$altriga = 5;
$larghcol = 10;
// STAMPA INTESTAZIONE

$schede->Image('../immagini/repubblica.png', 200, 20, 13, 15);
//$schede->Image('../immagini/miur.png',35,NULL,120,10);
$posY += 35;
$schede->SetFont('palacescript', '', 32);
$schede->setXY(10, $posY);
$ministero = converti_utf8("Ministero dell’Istruzione, dell’ Università e della Ricerca");
$schede->Cell(400, 8, $ministero, NULL, 1, "C");
$posY += 10;
$schede->SetFont('Arial', 'B', 10);
$schede->setXY(10, $posY);
$schede->Cell(400, 6, converti_utf8("$nome_scuola") . " " . converti_utf8("$comune_scuola"), NULL, 1, "C");
$posY += 8;
$schede->SetFont('Arial', 'BI', 9);
$schede->setXY(10, $posY);
$specplesso = converti_utf8("A.S.:".$annoscol."/".($annoscol+1)." - ".$plesso_specializzazione . ": " . decodifica_classe_spec($idclasse, $con) . " - Classe: " . decodifica_classe_no_spec($idclasse, $con, 1));
$schede->Cell(400, 6, $specplesso, NULL, 1, "C");
$posY += 8;


// INIZIO TABELLA
$query = "SELECT distinct tbl_materie.idmateria,sigla,tipovalutazione FROM tbl_cattnosupp,tbl_materie
	        WHERE tbl_cattnosupp.idmateria=tbl_materie.idmateria
	              and tbl_cattnosupp.idclasse=$idclasse
	              and tbl_cattnosupp.iddocente <> 1000000000
	              and tbl_materie.progrpag<>100
	              order by tbl_materie.progrpag,tbl_materie.sigla";

$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con) . $query);

$codmat = array();

$nummaterie = mysqli_num_rows($ris);
$posIniziale = calcolaPosizioneIniziale($nummaterie, $votitab, $voamtab, $mediatab, $credtab, $larghcol);
$posX = $posIniziale;
if ($nummaterie > 0)
{

    $posY += $altriga;
    $schede->setXY($posIniziale, $posY);
    $schede->SetFont('Arial', 'B', 8);

    $schede->Cell(90, $altriga, converti_utf8("Alunno"), 1, NULL, "C");
    $posX += 90;


    if ($votitab == "yes")
    {
        while ($nom = mysqli_fetch_array($ris))
        {

            $codmat[] = $nom["idmateria"];
            $schede->setXY($posX, $posY);
            $schede->SetFont('Arial', 'B', 7);
            $schede->Cell($larghcol, $altriga, converti_utf8($nom["sigla"]), 1, NULL, "C");
            $posX += $larghcol;


        }
        // INSERISCO LA CONDOTTA
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', 'B', 7);
        $schede->Cell($larghcol, $altriga, converti_utf8("COMP"), 1, NULL, "C");
        $posX += $larghcol;
    }

    if ($voamtab == 'yes')
    {
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', 'B', 7);
        $schede->Cell($larghcol, $altriga, converti_utf8("V.AM."), 1, NULL, "C");
        $posX += $larghcol;
    }
    if ($mediatab == 'yes')
    {
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', 'B', 7);
        $schede->Cell($larghcol, $altriga, converti_utf8("MED."), 1, NULL, "C");
        $posX += $larghcol;
    }
    if ($credtab == 'yes')
    {
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', 'B', 7);
        $schede->Cell($larghcol, $altriga, converti_utf8("CR."), 1, NULL, "C");
        $posX += $larghcol;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', 'B', 7);
        $schede->Cell($larghcol, $altriga, converti_utf8("CR.T."), 1, NULL, "C");
        $posX += $larghcol;
    }

    $schede->setXY($posX, $posY);
    $schede->SetFont('Arial', 'B', 8);
    $schede->Cell(90, $altriga, converti_utf8("Esito finale"), 1, NULL, "C");

    // CICLO SU TUTTI GLI ALUNNI

    $numeroalunno = 0;
    if ($periodo == '9')
    {
        $conddebito = " and idalunno in (select idalunno from tbl_esiti WHERE (validita='1' OR validita='2') AND esito=0) ";
    }
    else
    {
        $conddebito = "";
    }

    $query = "select * from tbl_alunni
                where idclasse= $idclasse $conddebito order by cognome,nome,datanascita";
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con) . $query);

    while ($val = mysqli_fetch_array($ris))
    {
        $listavoti = array();
        // $esiste_voto=false;
        $idalunno = $val["idalunno"];

        // ESTRAGGO ESITO

        $esito = " ";  // LASCIARE COSI' PER DISTINGUERE DA GIUDIZIO SOSPESO
        $creditoanno = "";
        $creditototale = "";
        $votoammissione = "";
        $media = "";
        $stampavoti=true;
        $query = "select * from tbl_esiti where idalunno='$idalunno'";
        $risesi = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con) . $query);

        if ($recesi = mysqli_fetch_array($risesi))
        {
            //$esito = decodifica_esito($recesi['esito'], $con);
            $codesi = $recesi['esito'];
            $codint = $recesi['integrativo'];
            $esito = estrai_esito($idalunno, $con);
            $passesi = passaggio($codesi,$con);
            $passint = passaggio($codint,$con);


            /*
             * 0 - passaggio a classe successiva
             * 1 - non passaggio a classe successiva
             * 2 - giudizio sospeso
             */
            //print "codesi $codesi codint $codint esito $esito passesi $passesi passint $passint <br> ";

            // TTTT RIVEDERE
            if ($votinp=='no')
            {   // IL GIUDIZIO E' SOSPESO OPPURE ERA SOSPESO ED E' RISULTATO NEGATIVO
                if ($codesi == 0 && ($codint != 0 && $passint == 1))
                {
                    $stampavoti = false;
                }
                // IL GIUDIZIO E' NEGATIVO AL PRIMO SCRUTINIO
                if ($codesi != 0 && $passesi == 1)
                {
                    $stampavoti = false;
                }
                // IL GIUDIZIO E' ANCORA SOSPESO
                if ($codesi == 0 && $codint == 0)
                {
                    $stampavoti = false;
                }
            }
            $creditoanno = $recesi['credito'];
            $creditototale = $recesi['creditotot'];
            $votoammissione = $recesi['votoammissione'];
            $media = $recesi['media'];
        }
        // FINE ESTRAZIONE ESITO


        $numeroalunno++;
        $posY += $altriga;
        $posX = $posIniziale;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 8);
        $schede->Cell(90, $altriga, converti_utf8($val['cognome'] . " " . $val['nome'] . " (" . data_italiana($val['datanascita']) . ")"), 1, NULL, "L");
        $posX += 90;

        if ($votitab == "yes")
        {

            $contavoti = 0;
            $sommavoti = 0;
            for ($nummat = 0; $nummat < count($codmat); $nummat++)
            {
                $cm = $codmat[$nummat];

                $votounico = ricerca_voto($idalunno, $cm, $codalunni, $codmaterie, $voti);
                $schede->setXY($posX, $posY);
                $schede->SetFont('Arial', '', 8);
                if ($stampavoti)
                   $schede->Cell($larghcol, $altriga, converti_utf8(dec_to_vot($votounico)), 1, NULL, "C");
                else
                    $schede->Cell($larghcol, $altriga, "", 1, NULL, "C");
                $posX += $larghcol;
            }
            // INSERISCO IL VOTO DI CONDOTTA
            $votounico = ricerca_voto($idalunno, -1, $codalunni, $codmaterie, $voti);
            $schede->setXY($posX, $posY);
            $schede->SetFont('Arial', '', 8);
            if ($stampavoti)
                $schede->Cell($larghcol, $altriga, converti_utf8(dec_to_vot($votounico)), 1, NULL, "C");
            else
                $schede->Cell($larghcol, $altriga, "", 1, NULL, "C");
            $posX += $larghcol;
        }



        if ($voamtab == 'yes')
        {
            $schede->setXY($posX, $posY);
            $schede->SetFont('Arial', 'B', 7);
            if ($stampavoti)
               $schede->Cell($larghcol, $altriga, $votoammissione, 1, NULL, "C");
            else
                $schede->Cell($larghcol, $altriga, "", 1, NULL, "C");
            $posX += $larghcol;
        }
        if ($mediatab == 'yes')
        {
            $schede->setXY($posX, $posY);
            $schede->SetFont('Arial', 'B', 7);
            if ($stampavoti)
                $schede->Cell($larghcol, $altriga, $media, 1, NULL, "C");
            else
                $schede->Cell($larghcol, $altriga, "", 1, NULL, "C");
            $posX += $larghcol;
        }
        if ($credtab == 'yes')
        {
            $schede->setXY($posX, $posY);
            $schede->SetFont('Arial', 'B', 7);
            if ($stampavoti)
                $schede->Cell($larghcol, $altriga, $creditoanno, 1, NULL, "C");
            else
                $schede->Cell($larghcol, $altriga, "", 1, NULL, "C");
            $posX += $larghcol;
            $schede->setXY($posX, $posY);
            $schede->SetFont('Arial', 'B', 7);
            if ($stampavoti)
                $schede->Cell($larghcol, $altriga, $creditototale, 1, NULL, "C");
            else
                $schede->Cell($larghcol, $altriga, "", 1, NULL, "C");
            $posX += $larghcol;
        }


        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 8);
        if ($esito=="" & $livello_scuola==4)
            $esito="GIUDIZIO SOSPESO";
        $schede->Cell(90, $altriga, converti_utf8(str_replace("|", " ", $esito)), 1, NULL, "L");

    }

}

//   FIRMA E TIMBRO
$posY += 20;
$luogodata = converti_utf8("$comune_scuola, $datastampa");
$schede->SetXY(23, $posY);
$schede->SetFont('Arial', 'B', 10);
$schede->Cell(95, 8, $luogodata, 0, 1, 'L');
$schede->setXY(220, $posY);
$schede->SetFont('Arial', 'B', 10);
$schede->Multicell(172, 6, converti_utf8("Il dirigente scolastico\n" . $firmadirigente . "\n\n\n\n\n\n"), 0, "C");

$schede->setXY(278, $schede->getY() - 25);
if ($_SESSION['suffisso'] != "") $suff = $_SESSION['suffisso'] . "/";
else $suff = "";
$schede->Image('../abc/' . $suff . 'firmadirigente.png');
$schede->setXY(200, $schede->getY() - 25);
$schede->Image('../abc/' . $suff . 'timbro.png');


$nomefile = "tabellone_" . decodifica_classe($idclasse, $con) . "_F.pdf";
$nomefile = str_replace(" ", "_", $nomefile);
$schede->Output($nomefile, "I");


mysqli_close($con);


function elimina_cr($stringa)
{
    // $strpul=converti_utf8($stringa);
    $strpul = str_replace(array("\n", "\r"), " ", $stringa);
    return $strpul;
}

function inserisci_new_line($stringa)
{
    //$strpul=converti_utf8($stringa);
    $strpul = str_replace("|", "\n", $stringa);
    return $strpul;
}

function estrai_prima_riga($stringa)
{
    //$strpul=converti_utf8($stringa);
    $posint = strpos($stringa, "|");
    if ($posint != 0)
    {
        $str1 = substr($stringa, 0, $posint);
    }
    else
    {
        $str1 = $stringa;
    }
    return $str1;
}

function estrai_seconda_riga($stringa)
{
    //$strpul=converti_utf8($stringa);
    $posint = strpos($stringa, "|");
    if ($posint != 0)
    {
        $str2 = substr($stringa, $posint + 1);
    }
    else
    {
        $str2 = "";
    }
    return $str2;
}


function ricerca_voto($idalunno, $idmateria, $alu, $materie, $valutaz)
{
    for ($i = 0; $i < count($valutaz); $i++)
    {

        if ($idalunno == $alu[$i] & ($idmateria) == $materie[$i])
        {
            return $valutaz[$i];
        }
    }
    return 0;
}

function calcolaPosizioneIniziale($nummaterie, $votitab, $voamtab, $mediatab, $credtab, $larghcol)
{
    $spazio = 180;
    if ($votitab == "yes")
    {
        $spazio += ($nummaterie * $larghcol);
    }
    if ($voamtab == "yes")
    {
        $spazio += $larghcol;
    }
    if ($mediatab == "yes")
    {
        $spazio += $larghcol;
    }
    if ($credtab == "yes")
    {
        $spazio += $larghcol;
    }
    return (410 - $spazio) / 2;

}

