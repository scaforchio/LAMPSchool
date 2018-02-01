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


$schede = new FPDF('P', 'mm', 'A3');
$schede->AddFont('palacescript', '', 'palacescript.php'); // Font del Ministero
$schede->AddPage();

// $datascrutinio=data_italiana(datascrutinio($idclasse, $periodo, $con));

$posX = 0;
$posY = 10;
$altriga = 7;
$larghcol = 35;
// STAMPA INTESTAZIONE

/* $schede->Image('../immagini/repubblica.png', 200, 20, 13, 15);
//$schede->Image('../immagini/miur.png',35,NULL,120,10);
$posY += 35;
$schede->SetFont('palacescript', '', 32);
$schede->setXY(10, $posY);
$ministero = converti_utf8("Ministero dell’Istruzione, dell’ Università e della Ricerca");
$schede->Cell(240, 8, $ministero, NULL, 1, "C");
$posY += 10; */
$schede->SetFont('Arial', 'B', 12);
$schede->setXY(10, $posY);
$schede->Cell(270, 6, converti_utf8("$nome_scuola") . " " . converti_utf8("$comune_scuola"), NULL, 1, "C");
$posY += 8;
$schede->SetFont('Arial', 'BI', 12);
$schede->setXY(10, $posY);
$specplesso = converti_utf8("ESITI FINALI ESAME DI STATO A.S.:" . $annoscol . "/" . ($annoscol + 1) . " - " . decodifica_classe_spec($idclasse, $con) . " - Classe: " . decodifica_classe_no_spec($idclasse, $con, 1));
$schede->Cell(270, 6, $specplesso, NULL, 1, "C");
$posY += 8;


// INIZIO TABELLA
$query = "SELECT * FROM tbl_esesiti,tbl_alunni,tbl_esami3m,tbl_esmaterie
	          WHERE tbl_esesiti.idalunno=tbl_alunni.idalunno
	          AND tbl_alunni.idclasseesame=tbl_esami3m.idclasse
	          AND tbl_alunni.idclasseesame=tbl_esmaterie.idclasse
	          AND tbl_alunni.idclasseesame=$idclasse
	          order by tbl_alunni.idclasse DESC, cognome, nome, datanascita";

$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con) . $query);

$recesi = mysqli_fetch_array($ris);
$idcommissione = $recesi['idcommissione'];
$datastampa = data_italiana($recesi['datascrutinio']);
/*
$codmat = array();

$nummaterie = mysqli_num_rows($ris);
$posIniziale = calcolaPosizioneIniziale($nummaterie, $votitab, $voamtab, $mediatab, $credtab, $larghcol);
*/

$posIniziale = 50;

$posX = 50;


$posY += $altriga;
$schede->setXY($posIniziale, $posY);
$schede->SetFont('Arial', 'B', 12);


$schede->SetFillColor(200, 200, 200);

$schede->Cell(10, $altriga, converti_utf8("N°"), 1, NULL, "C", true);
$posX += 10;

$schede->Cell(150, $altriga, converti_utf8("Alunno"), 1, NULL, "C", true);
$posX += 150;
$schede->Cell(10, $altriga, converti_utf8("Voto"), 1, NULL, "C", true);
$posX += 10;
$schede->Cell($larghcol, $altriga, converti_utf8("Esito"), 1, NULL, "C", true);
$posX += $larghcol;

// TTTT


// CICLO SU TUTTI GLI ALUNNI

$numeroalunno = 0;
/*

$query = "select * from tbl_alunni
                where idclasseesame= $idclasse order by idclasse DESC, cognome,nome,datanascita";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con) . $query);
*/
do
{

    // FINE ESTRAZIONE ESITO

    $codfiscale = $recesi['codfiscale'];

    $sesso='';
    if (substr($codfiscale, 9, 2) > '35')
    {
        $sesso = 'f';
    }
    else
    {
        $sesso = 'm';
    }

    $numeroalunno++;
    $posY += $altriga;
    $posX = $posIniziale;
    $schede->setXY($posX, $posY);
    $schede->SetFont('Arial', '', 12);
    $schede->Cell(10, $altriga, converti_utf8($numeroalunno), 1, NULL, "L");
    $posX += 10;
    $schede->Cell(150, $altriga, converti_utf8($recesi['cognome'] . " " . $recesi['nome'] . " (" . data_italiana($recesi['datanascita']) . ")"), 1, NULL, "L");
    $posX += 90;

    if ($recesi['lode'])
    {
        $lode = " L";
    }
    else
    {
        $lode = "";
    }
    if ($recesi['votofinale'] >= 6)

    {
        $schede->Cell(10, $altriga, converti_utf8($recesi['votofinale'] . $lode), 1, NULL, "C");
        if ($sesso=='f')
            $schede->Cell($larghcol, $altriga, converti_utf8("Licenziata"), 1, NULL, "C");
        else
            $schede->Cell($larghcol, $altriga, converti_utf8("Licenziato"), 1, NULL, "C");
    }
    else
    {
        $schede->Cell(10, $altriga, "==", 1, NULL, "C");
        if ($sesso=='f')
            $schede->Cell($larghcol, $altriga, converti_utf8("Non licenziata"), 1, NULL, "C");
        else
            $schede->Cell($larghcol, $altriga, converti_utf8("Non licenziato"), 1, NULL, "C");
    }
    $posX += $larghcol;


} while ($recesi = mysqli_fetch_array($ris));


// Sottocommissione
//$idcommissione = $recesi['idcommissione'];
$nomepresidente = "";
$cognomepresidente = "";
$denominazionecomm = "";
$query = "select * from tbl_escompcommissioni,tbl_docenti,tbl_escommissioni
                where tbl_escompcommissioni.idcommissione=tbl_escommissioni.idescommissione
                and tbl_escompcommissioni.iddocente=tbl_docenti.iddocente
                and tbl_escompcommissioni.idcommissione=$idcommissione";
$riscom = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));
$cont = 0;
$posYiniz = $schede->GetY() + 10;
while ($reccom = mysqli_fetch_array($riscom))
{

    $nomepresidente = $reccom['nomepresidente'];
    $cognomepresidente = $reccom['cognomepresidente'];
    $denominazionecomm = $reccom['denominazione'];
    $nomedocente = $reccom['nome'];
    $cognomedocente = $reccom['cognome'];

    $posX = 30 + ($cont % 4 * 60);
    $posY = $posYiniz + 18 + (floor($cont / 4) * 18);

    $schede->setXY($posX, $posY);
    $schede->Line($posX, $posY, $posX + 50, $posY);
    $schede->Cell(50, 4, converti_utf8($nomedocente . " " . $cognomedocente), 0, 0, "C");
    $cont++;

}


// STAMPA PARTE TERMINALE

$luogodata = converti_utf8("$comune_scuola, $datastampa");
$schede->setXY(30, $posY + 30);
$schede->SetFont('Arial', '', 11);
$schede->Cell(70, 5, $luogodata, "", 0, "L");

$schede->setXY(200, $posY + 40);
$schede->SetFont('Arial', '', 11);
$schede->Cell(40, 5, converti_utf8($nomepresidente . " " . $cognomepresidente), "B", 0, "C");

$dicituradirigente = "IL PRESIDENTE";
$schede->setXY(200, $posY + 45);
$schede->SetFont('Arial', '', 8);
$schede->Cell(40, 3, $dicituradirigente, "", 0, "C");

if ($_SESSION['suffisso'] != "")
{
    $suff = $_SESSION['suffisso'] . "/";
}
else $suff = "";

$schede->setXY(120, $posY + 45);
$schede->Image('../abc/' . $suff . 'timbro.png');


$nomefile = "tab_esame_" . decodifica_classe($idclasse, $con) . ".pdf";
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

