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
$schede->AliasNbPages();
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

$testo1 = $rec['testo1'];
$testo2 = $rec['testo2'];
$testo3 = $rec['testo3'];
$testo4 = $rec['testo4'];
$testo1 = str_replace("\r", "", $testo1);
$testo2 = str_replace("\r", "", $testo2);
$testo3 = str_replace("\r", "", $testo3);
$testo4 = str_replace("\r", "", $testo4);

// print ($testo1);

if ($codsegretario != "")
{
    // verifico se si tratta di un sostituto
    if (strpos($sostituzioni, $codsegretario . "<") > 0)
    {
        $possegr = strpos($sostituzioni, $codsegretario . "<");
        $codsegretario = substr($sostituzioni, $possegr + 11, 10);
    }
    // print "Segr. ".$codsegretario;
    $segretario = estrai_dati_docente($codsegretario, $con);

}
else
{
    $segretario = "";
}

$codpresidente = 1000000000;
// verifico se c'è il sostituto del presidente
if (strpos($sostituzioni, $codpresidente . "<") > 0)
{
    $pospres = strpos($sostituzioni, $codpresidente . "<");
    $codpresidente = substr($sostituzioni, $pospres + 11, 10);
    // print "Sost.pres.".$codpresidente;
}
if ($codpresidente != 1000000000)
{
    $presidente = estrai_dati_docente($codpresidente, $con);
    $presid = $presidente . " (su delega del dirigente scolastico)";
}
else
{
    $presidente = estrai_dirigente($con);
    $presid = $presidente;
}
if ($numeroperiodi == 2)
{
    $per = "quadrimestre";
}
else
{
    $per = "trimestre";
}

if ($numeroperiodi == 2)
{
    $quadrimestre = "secondo " . $per;
}
if ($numeroperiodi == 3)
{
    $quadrimestre = "terzo " . $per;
}



$query = "select distinct tbl_cattnosupp.iddocente, cognome, nome from tbl_cattnosupp,tbl_docenti
        where tbl_cattnosupp.iddocente=tbl_docenti.iddocente
        and idclasse=$idclasse and tbl_cattnosupp.iddocente<>1000000000
        order by cognome, nome";
$ris = mysqli_query($con, inspref($query));
$elencodocentititolari = array();
while ($rec = mysqli_fetch_array($ris))
{
    if (!(strpos($sostituzioni, "§" . $rec['iddocente']) > -1))
    {
        $elencodocentititolari[] = $rec['iddocente'];
    }

}
$numerodocentitit = count($elencodocentititolari);
$elencodocentipresenti = array();

foreach ($elencodocentititolari as $doctit)
{
    if (strpos($sostituzioni, $doctit . "<") > 0)
    {
        $pos = strpos($sostituzioni, $doctit . "<");
        $codsost = substr($sostituzioni, $pos + 11, 10);
        $elencodocentipresenti[] = $codsost;
    }

    else
    {
        $elencodocentipresenti[] = $doctit;
    }
}

// $contopres=true;
// if (in_array($codpresidente,$elencodocentipresenti))
//   $contopres=false;

//
// Sostituisco i parametri nel testo
//

$testo1 = str_replace("[presidente]", $presid, $testo1);
$testo1 = str_replace("[classe]", $classe, $testo1);
$testo1 = str_replace("[orainizio]", $orainizio, $testo1);
$testo1 = str_replace("[data]", $dataverbale, $testo1);
$testo1 = str_replace("[luogo]", $luogo, $testo1);
$testo1 = str_replace("[quadrimestre]", $quadrimestre, $testo1);
$testo1 = str_replace("[segretario]", $segretario, $testo1);
$testo1 = str_replace("[numero docenti]", $numerodocentitit, $testo1);
$testo1 = str_replace("[numero votanti]", $numerodocentitit + 1, $testo1);
$testo1 = str_replace("[omissis]", "", $testo1);
$testo1 = str_replace("[orafine]", $orafine, $testo1);

// print ($testo1);

$testo2 = str_replace("[presidente]", $presid, $testo2);
$testo2 = str_replace("[classe]", $classe, $testo2);
$testo2 = str_replace("[orainizio]", $orainizio, $testo2);
$testo2 = str_replace("[data]", $dataverbale, $testo2);
$testo2 = str_replace("[luogo]", $luogo, $testo2);
$testo2 = str_replace("[quadrimestre]", $quadrimestre, $testo2);
$testo2 = str_replace("[segretario]", $segretario, $testo2);
$testo2 = str_replace("[numero docenti]", $numerodocentitit, $testo2);
$testo2 = str_replace("[numero votanti]", $numerodocentitit + 1, $testo2);
$testo2 = str_replace("[omissis]", "", $testo2);
$testo2 = str_replace("[orafine]", $orafine, $testo2);

$testo3 = str_replace("[presidente]", $presid, $testo3);
$testo3 = str_replace("[classe]", $classe, $testo3);
$testo3 = str_replace("[orainizio]", $orainizio, $testo3);
$testo3 = str_replace("[data]", $dataverbale, $testo3);
$testo3 = str_replace("[luogo]", $luogo, $testo3);
$testo3 = str_replace("[quadrimestre]", $quadrimestre, $testo3);
$testo3 = str_replace("[segretario]", $segretario, $testo3);
$testo3 = str_replace("[numero docenti]", $numerodocentitit, $testo3);
$testo3 = str_replace("[numero votanti]", $numerodocentitit + 1, $testo3);
$testo3 = str_replace("[omissis]", "", $testo3);
$testo3 = str_replace("[orafine]", $orafine, $testo3);

$testo4 = str_replace("[presidente]", $presid, $testo4);
$testo4 = str_replace("[classe]", $classe, $testo4);
$testo4 = str_replace("[orainizio]", $orainizio, $testo4);
$testo4 = str_replace("[data]", $dataverbale, $testo4);
$testo4 = str_replace("[luogo]", $luogo, $testo4);
$testo4 = str_replace("[quadrimestre]", $quadrimestre, $testo4);
$testo4 = str_replace("[segretario]", $segretario, $testo4);
$testo4 = str_replace("[numero docenti]", $numerodocentitit, $testo4);
$testo4 = str_replace("[numero votanti]", $numerodocentitit + 1, $testo4);
$testo4 = str_replace("[omissis]", "", $testo4);
$testo4 = str_replace("[orafine]", $orafine, $testo4);


$schede->AddPage();
if ($_SESSION['suffisso'] != "") $suff = $_SESSION['suffisso'] . "/";
else $suff = "";
$schede->Image('../abc/' . $suff . 'testata.jpg', NULL, NULL, 190, 43);


$posY = 70;


$schede->SetFont('Times', '', 12);
$schede->setXY(10, $posY);
if ($periodo!='9')
    $verbale = converti_utf8("Verbale scrutinio finale, classe $classe");
else
    $verbale = converti_utf8("Verbale scrutinio integrativo, classe $classe");
$schede->MultiCell(190, 8, $verbale, NULL, "C");
$posY += 10;


$schede->SetXY(10, $posY);
$schede->SetFont('Times', '', 10);
// print converti_utf8($testo1);
$schede->write(4, converti_utf8($testo1));
$posY = $schede->GetY();
$posY += 5;

$elencodocenti = "";
for ($i = 0; $i < $numerodocentitit; $i++)
{
    if ($elencodocentipresenti[$i] == $elencodocentititolari[$i])
    {
        $elencodocenti .= estrai_dati_docente($elencodocentipresenti[$i], $con) . ", ";
    }
    else
    {
        $elencodocenti .= estrai_dati_docente($elencodocentipresenti[$i], $con) . " (in sostituzione di " . estrai_dati_docente($elencodocentititolari[$i], $con) . "), ";
    }
}
$elencodocenti = substr($elencodocenti, 0, strlen($elencodocenti) - 2);
$schede->SetXY(10, $posY);
$schede->SetFont('Times', '', 10);
$schede->write(4, converti_utf8($elencodocenti));
$posY = $schede->GetY();
$posY += 5;

$schede->SetXY(10, $posY);
$schede->SetFont('Times', '', 10);
$schede->write(4, converti_utf8($testo2));
$posY = $schede->GetY();
$posY += 5;

$annotazioni = "";
if ($periodo=='9')
    $conddebito = " and idalunno in (select idalunno from tbl_esiti WHERE (validita='1' OR validita='2') AND esito=0) ";
else
    $conddebito = "";

$query = "select * from tbl_alunni where idclasse=$idclasse $conddebito order by cognome, nome, datanascita";
$ris = mysqli_query($con, inspref($query));
while ($rec = mysqli_fetch_array($ris))
{
    $idalunno = $rec['idalunno'];
    $datialunno = estrai_dati_alunno($idalunno, $con) . "\n";
    $annotazionialunno = "";
    $query = "select * from tbl_giudizi where idalunno=$idalunno and periodo=$numeroperiodi and giudizio<>''";
    $risgiu = mysqli_query($con, inspref($query));
    if ($recgiu = mysqli_fetch_array($risgiu))
    {
        $annotazionialunno .= $recgiu['giudizio'] . "\n";
    }
    $query = "select * from tbl_valutazionifinali,tbl_materie
	        where tbl_valutazionifinali.idmateria=tbl_materie.idmateria
	        and idalunno=$idalunno and periodo=$numeroperiodi and note<>''
	        order by tbl_materie.progrpag, denominazione";
    $risnot = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query));
    while ($recnot = mysqli_fetch_array($risnot))
    {
        $annotazionialunno .= $recnot['denominazione'] . ": ";
        $annotazionialunno .= $recnot['note'] . "\n";
    }
    if ($annotazionialunno != "")
    {
        $annotazioni .= $datialunno . $annotazionialunno;
    }
}

if ($annotazioni != "")
{
    $annotazioni = "Si riportano annotazioni sugli alunni di seguito elencati:\n" . $annotazioni;
}

$schede->SetXY(10, $posY);
$schede->SetFont('Times', '', 10);
$schede->write(4, converti_utf8($annotazioni));
$posY = $schede->GetY();
$posY += 5;

$schede->SetXY(10, $posY);
$schede->SetFont('Times', '', 10);
$schede->write(4, converti_utf8($testo3));
$posY = $schede->GetY();
$posY += 5;

$schede->SetXY(10, $posY);
$schede->SetFont('Times', '', 10);
$schede->write(4, converti_utf8($testo4));
$posY = $schede->GetY();
$posY += 5;


$posY += 5;
$schede->SetXY(105, $posY);
$schede->SetFont('Times', '', 10);
if ($codpresidente == 1000000000)
{
    $schede->Cell(95, 8, converti_utf8("Il dirigente scolastico"), NULL, 1, "C");
}
else
{
    $schede->Cell(95, 8, converti_utf8("Il delegato del D.S."), NULL, 1, "C");
}

$posY += 5;
$schede->SetXY(105, $posY);
$schede->SetFont('Times', 'B', 10);
$schede->Cell(95, 8, converti_utf8($presidente), NULL, 1, "C");

$nomefile = "Verbale_finale_" .decodifica_classe($idclasse, $con) . ".pdf";
$nomefile = str_replace(" ", "_", $nomefile);
// $schede->FooterAll();
$schede->AddPage();
$posY = 20;
$schede->SetFont('Times', '', 12);
$schede->setXY(10, $posY);
$verbale = converti_utf8("Firma docenti presenti allo scrutinio finale, classe $classe");
$schede->MultiCell(190, 8, $verbale, NULL, "C");
$posY += 10;

$schede->SetFont('Times', '', 10);
for ($i = 0; $i < $numerodocentitit; $i++)
{
    $docentepresente = estrai_dati_docente($elencodocentipresenti[$i], $con);
    if ($elencodocentipresenti[$i] != $elencodocentititolari[$i])
    {
        $docentepresente .= " (in sost. di " . estrai_dati_docente($elencodocentititolari[$i], $con) . ")";
    }
    $schede->Cell(110, 8, converti_utf8($docentepresente), "B");
    $schede->Cell(70, 8, "", "B", 1);

}


//$schede->FooterAll();   
$schede->Output($nomefile, "I");

mysqli_close($con);


