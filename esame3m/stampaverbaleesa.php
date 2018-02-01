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


//  Richiamare funzione di stampa passando gli array come parametri


$schede = new FPDFPAG();
$schede->AliasNbPages();
// $schede->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
// $schede->SetFont('DejaVu','',14);

//
// Estraggo tutti i valori per sostituzione parametri di stampa
//
$query = "select * from tbl_esami3m,tbl_escommissioni where
          tbl_esami3m.idcommissione=tbl_escommissioni.idescommissione
          and idclasse=$idclasse";
// print inspref($query);
$ris = mysqli_query($con, inspref($query))  or die("Errore: ".inspref($query,false));
$rec = mysqli_fetch_array($ris);


$dataverbale = data_italiana($rec['datascrutinio']);
$giorno = substr($rec['datascrutinio'], 8, 2);
$anno = substr($rec['datascrutinio'], 0, 4);
$mese = nomemese(substr($rec['datascrutinio'], 3, 2));
$orainizio = substr($rec['orainizio'], 0, 5);
$orafine = substr($rec['orafine'], 0, 5);
$luogo = $rec['luogoscrutinio'];
$presidente = $rec['nomepresidente'] . " " . $rec['cognomepresidente'];
$commissione = $rec['denominazione'];
if ($rec['idsegretario'] != "" & $rec['idsegretario'] != 0)
{
    $segretario = estrai_dati_docente($rec['idsegretario'], $con);
}
else
{
    $segretario = "";
}
$query = "SELECT cognome,nome FROM tbl_escompcommissioni,tbl_docenti
        WHERE tbl_escompcommissioni.iddocente = tbl_docenti.iddocente
        AND idcommissione=" . $rec['idcommissione'];
$risdoc = mysqli_query($con, inspref($query)) or die("Errore: ".inspref($query,false));
$elencodocenti = "";
$arrdocenti = array();
while ($recdoc = mysqli_fetch_array($risdoc))
{
    $elencodocenti .= $recdoc['nome'] . " " . $recdoc['cognome'] . ", ";
    $arrdocenti[] = $recdoc['nome'] . " " . $recdoc['cognome'];
}
$elencodocenti = substr($elencodocenti, 0, strlen($elencodocenti) - 2);

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

/*

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
*/
$testo1 = str_replace("[presidente]", $presidente, $testo1);
$testo1 = str_replace("[classe]", $classe, $testo1);
$testo1 = str_replace("[orainizio]", $orainizio, $testo1);
$testo1 = str_replace("[giorno]", $giorno, $testo1);
$testo1 = str_replace("[mese]", $mese, $testo1);
$testo1 = str_replace("[anno]", $anno, $testo1);
$testo1 = str_replace("[luogo]", $luogo, $testo1);
$testo1 = str_replace("[commissione]", $commissione, $testo1);
$testo1 = str_replace("[omissis]", "", $testo1);
$testo1 = str_replace("[orafine]", $orafine, $testo1);
$testo1 = str_replace("[elenco docenti]", $elencodocenti, $testo1);
$testo1 = str_replace("[segretario]", $segretario, $testo1);

// print ($testo1);

$testo2 = str_replace("[presidente]", $presidente, $testo2);
$testo2 = str_replace("[classe]", $classe, $testo2);
$testo2 = str_replace("[orainizio]", $orainizio, $testo2);
$testo2 = str_replace("[giorno]", $giorno, $testo2);
$testo2 = str_replace("[mese]", $mese, $testo2);
$testo2 = str_replace("[anno]", $anno, $testo2);
$testo2 = str_replace("[luogo]", $luogo, $testo2);
$testo2 = str_replace("[commissione]", $commissione, $testo2);
$testo2 = str_replace("[omissis]", "", $testo2);
$testo2 = str_replace("[orafine]", $orafine, $testo2);
$testo2 = str_replace("[elenco docenti]", $elencodocenti, $testo2);
$testo2 = str_replace("[segretario]", $segretario, $testo2);

$testo3 = str_replace("[presidente]", $presidente, $testo3);
$testo3 = str_replace("[classe]", $classe, $testo3);
$testo3 = str_replace("[orainizio]", $orainizio, $testo3);
$testo3 = str_replace("[giorno]", $giorno, $testo3);
$testo3 = str_replace("[mese]", $mese, $testo3);
$testo3 = str_replace("[anno]", $anno, $testo3);
$testo3 = str_replace("[luogo]", $luogo, $testo3);
$testo3 = str_replace("[commissione]", $commissione, $testo3);
$testo3 = str_replace("[omissis]", "", $testo3);
$testo3 = str_replace("[orafine]", $orafine, $testo3);
$testo3 = str_replace("[elenco docenti]", $elencodocenti, $testo3);
$testo3 = str_replace("[segretario]", $segretario, $testo3);

$testo4 = str_replace("[presidente]", $presidente, $testo4);
$testo4 = str_replace("[classe]", $classe, $testo4);
$testo4 = str_replace("[orainizio]", $orainizio, $testo4);
$testo4 = str_replace("[giorno]", $giorno, $testo4);
$testo4 = str_replace("[mese]", $mese, $testo4);
$testo4 = str_replace("[anno]", $anno, $testo4);
$testo4 = str_replace("[luogo]", $luogo, $testo4);
$testo4 = str_replace("[commissione]", $commissione, $testo4);
$testo4 = str_replace("[omissis]", "", $testo4);
$testo4 = str_replace("[orafine]", $orafine, $testo4);
$testo4 = str_replace("[elenco docenti]", $elencodocenti, $testo4);
$testo4 = str_replace("[segretario]", $segretario, $testo4);

$schede->AddPage();

if ($_SESSION['suffisso'] != "")
{
    $suff = $_SESSION['suffisso'] . "/";
}
else $suff = "";
$schede->Image('../abc/' . $suff . 'testata.jpg', NULL, NULL, 190, 43);


$posY = 70;


$schede->SetFont('Times', '', 12);
$schede->setXY(10, $posY);

$verbale = converti_utf8("Verbale della sottocommissione $commissione");
$schede->MultiCell(190, 8, $verbale, NULL, "C");
$posY += 10;


$schede->SetXY(10, $posY);
$schede->SetFont('Times', '', 10);
// print converti_utf8($testo1);
$schede->write(4, converti_utf8($testo1));
$posY = $schede->GetY();
$posY += 5;

/*
$schede->SetXY(10, $posY);
$schede->SetFont('Times', '', 10);
$schede->write(4, converti_utf8($elencodocenti));
$posY = $schede->GetY();
$posY += 5;
*/


$schede->SetXY(10, $posY);
$schede->SetFont('Times', '', 10);
$schede->write(4, converti_utf8($testo2));
$posY = $schede->GetY();
$posY += 2;
/*
$annotazioni = "";
if ($periodo=='9')
    $conddebito = " and idalunno in (select idalunno from tbl_esiti WHERE (validita='1' OR validita='2') AND esito=0) ";
else
    $conddebito = "";

$query = "select * from tbl_alunni where idclasseesame=$idclasse $conddebito order by cognome, nome, datanascita";
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
*/


// ESITI POSITIVI

$query = "select * from tbl_esesiti,tbl_alunni
        where tbl_esesiti.idalunno = tbl_alunni.idalunno
        and tbl_alunni.idclasseesame = $idclasse
        and tbl_esesiti.votofinale>=6
        order by idclasse DESC, cognome, nome, datanascita";
$risesi = mysqli_query($con, inspref($query));
$numdalic = mysqli_num_rows($risesi);
if ($numdalic > 0)
{
    $posY += 4;
    $schede->SetXY(10, $posY);
    $schede->SetFont('Times', '', 10);
    $schede->Cell(170, 5, converti_utf8("Candidati da licenziare n. $numdalic"));
    $posY = $schede->GetY();
    $posY += 8;


}
while ($recesi = mysqli_fetch_array($risesi))
{

    $riga = $recesi['cognome'] . " " . $recesi['nome'] . " (" . $recesi['datanascita'] . ")";
    $riga .= " - Voto finale: " . $recesi['votofinale'];
    if ($recesi['lode'])
    {
        $riga .= " con lode ";
    }
    if (!$recesi['unanimita'])
    {
        $riga .= " (M) ";
    }
    if ($recesi['consorientcomm'] == '')
    {
        $riga .= " - Si conferma il giudizio orientativo: " . $recesi['consorientcons'];
    }
    else
    {
        $riga .= " - Il consiglio orientativo proposto dal Consiglio di Classe (" . $recesi['consorientcons'] . ") viene cambiato in: " . $recesi['consorientcomm'];
    }


    $schede->SetXY(10, $posY);
    $schede->SetFont('Times', '', 10);
    $schede->MultiCell(170, 5, converti_utf8($riga));
    $posY = $schede->GetY();
    $posY++;
}


// ESITI NEGATIVI

$query = "select * from tbl_esesiti,tbl_alunni
        where tbl_esesiti.idalunno = tbl_alunni.idalunno
        and tbl_alunni.idclasseesame = $idclasse
        and tbl_esesiti.votofinale<6
        order by idclasse DESC, cognome, nome, datanascita";
$risesi = mysqli_query($con, inspref($query));
$numnondalic = mysqli_num_rows($risesi);
if ($numnondalic > 0)
{
    $posY += 4;

    $schede->SetXY(10, $posY);
    $schede->SetFont('Times', '', 10);
    $schede->Cell(170, 5, converti_utf8("Candidati da non licenziare n. $numnondalic"));
    $posY = $schede->GetY();
    $posY += 8;
}
while ($recesi = mysqli_fetch_array($risesi))
{

    $riga = $recesi['cognome'] . " " . $recesi['nome'] . " (" . $recesi['datanascita'] . ")";
    $riga .= " - Voto finale: " . $recesi['votofinale'];

    if (!$recesi['unanimita'])
    {
        $riga .= " (M) ";
    }


    $schede->SetXY(10, $posY);
    $schede->SetFont('Times', '', 10);
    $schede->MultiCell(170, 5, converti_utf8($riga));
    $posY = $schede->GetY();
    $posY++;
}

// ESITI POSITIVI PER TERZA MEDIA

$query = "select * from tbl_esesiti,tbl_alunni
        where tbl_esesiti.idalunno = tbl_alunni.idalunno
        and tbl_alunni.idclasseesame = $idclasse
        and tbl_esesiti.votofinale<=6
        and tbl_esesiti.ammissioneterza
        order by cognome, nome, datanascita";
$risesi = mysqli_query($con, inspref($query));
$numterza = mysqli_num_rows($risesi);
if ($numterza > 0)
{
    $posY += 10;
    $schede->SetXY(10, $posY);
    $schede->SetFont('Times', '', 10);
    $schede->Cell(170, 5, converti_utf8("La sottocommissione, inoltre, esprime parere favorevole per l'ammissione alla classe terza dei seguenti $numterza privatisti,"));
    $posY = $schede->GetY();
    $posY += 5;
    $schede->SetXY(10, $posY);
    $schede->Cell(170, 5, converti_utf8("che non hanno superato le prove per il conseguimento della licenza:"));
    $posY = $schede->GetY();
    $posY += 8;

}
while ($recesi = mysqli_fetch_array($risesi))
{

    $riga = $recesi['cognome'] . " " . $recesi['nome'] . " (" . $recesi['datanascita'] . ")";
    $riga .= " - Voto finale: " . $recesi['votofinale'];

    if (!$recesi['unanimita'])
    {
        $riga .= " (M) ";
    }


    $schede->SetXY(10, $posY);
    $schede->SetFont('Times', '', 10);
    $schede->MultiCell(170, 5, converti_utf8($riga));
    $posY = $schede->GetY();
    $posY++;
}


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
/*
if ($codpresidente == 1000000000)
{
    $schede->Cell(95, 8, converti_utf8("Il dirigente scolastico"), NULL, 1, "C");
}
else
{
    $schede->Cell(95, 8, converti_utf8("Il delegato del D.S."), NULL, 1, "C");
}
*/
$posfinY=$schede->getY();
if ($posfinY<120)
{
    $posY += 5;
}
else
{
    $schede->AddPage();
    $posY = 20;
}
$schede->SetXY(20, $posY);
$schede->SetFont('Times', 'B', 10);
$schede->Cell(50, 8, converti_utf8("Il segretario"), NULL, 1, "C");
$posY +=5;
$schede->SetXY(20, $posY);
$schede->Cell(50, 8, converti_utf8("($segretario)"), NULL, 1, "C");
$schede->Line(20, $posY + 20, 70, $posY + 20);


$posY-=5;
$schede->setXY(80, $posY);
$schede->Image('../abc/' . $suff . 'timbro.png');


$schede->SetXY(125, $posY);
$schede->SetFont('Times', 'B', 10);
$schede->Cell(50, 8, converti_utf8("Il presidente"), NULL, 1, "C");
$posY+=5;
$schede->SetXY(125, $posY);
$schede->Cell(50, 8, converti_utf8("($presidente)"), NULL, 1, "C");
$schede->Line(125, $posY + 20, 175, $posY + 20);
$posY = $posY + 30;

$posYiniz = $posY;
$cont = 0;
foreach ($arrdocenti as $nominativodocente)
{

    $posX = 20 + ($cont % 3 * 55);
    $posY = $posYiniz + 15 + (floor($cont / 3) * 15);

    $schede->setXY($posX, $posY);
    $schede->Line($posX, $posY, $posX + 50, $posY);
    $schede->Cell(50, 4, converti_utf8($nominativodocente), 0, 0, "C");
    $cont++;
}
// $schede->FooterAll();
/*
$schede->AddPage();
$posY = 20;
$schede->SetFont('Times', '', 12);
$schede->setXY(10, $posY);
$verbale = converti_utf8("Firma docenti presenti allo scrutinio, classe $classe");
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
*/
$nomefile = "verbale_esame_" . $idclasse . ".pdf";
$schede->Output($nomefile, "I");

mysqli_close($con);


