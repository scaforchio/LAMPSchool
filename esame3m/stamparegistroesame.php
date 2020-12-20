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

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

// $idclasse = stringa_html("classe");

$datastampa = stringa_html("datastampa");

//  Richiamare funzione di stampa passando gli array come parametri


$schede = new FPDFPAG();
$schede->AliasNbPages();
// $schede->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
// $schede->SetFont('DejaVu','',14);
//
// Estraggo tutti i valori per sostituzione parametri di stampa
//
$query = "select * from tbl_esami3m,tbl_escommissioni where
          tbl_esami3m.idcommissione=tbl_escommissioni.idescommissione";
//       and idclasse=$idclasse";
//print inspref($query);
$ris = eseguiQuery($con, $query);
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
} else
{
    $segretario = "";
}
$query = "SELECT distinct cognome,nome,tbl_docenti.iddocente FROM tbl_escompcommissioni,tbl_docenti
        WHERE tbl_escompcommissioni.iddocente = tbl_docenti.iddocente
        AND idcommissione<>0
        ORDER BY cognome, nome";
$risdoc = eseguiQuery($con, $query);
//$elencodocenti = "";
$arrdocenti = array();
// print inspref($query);
while ($recdoc = mysqli_fetch_array($risdoc))
{
    // $elencodocenti .= $recdoc['nome'] . " " . $recdoc['cognome'] . ", ";
    // RICERCA MATERIE
    $iddocentecomm = $recdoc['iddocente'];
    if ($livello_scuola == 3)
        $ultimoanno = 8;
    else
        $ultimoanno = 3;
    $materie = "";
    $query = "select distinct denominazione from "
            . "tbl_cattnosupp,tbl_materie,tbl_classi "
            . "where tbl_cattnosupp.idmateria=tbl_materie.idmateria "
            . "and tbl_cattnosupp.idclasse = tbl_classi.idclasse "
            . "and anno=$ultimoanno "
            . "and tbl_cattnosupp.iddocente=$iddocentecomm "
            . "and idalunno=0";
    $rismat = eseguiQuery($con, $query);
    while ($recmat = mysqli_fetch_array($rismat))
    {
        $materie .= $recmat['denominazione'] . " ";
    }
    $arrdocenti[] = $recdoc['cognome'] . " " . $recdoc['nome'];
    $arrmaterie[] = $materie;
}
//$elencodocenti = substr($elencodocenti, 0, strlen($elencodocenti) - 2);




$annoscolastico = $_SESSION['annoscol'] . " / " . ($_SESSION['annoscol'] + 1);

stampa_prima_pagina($annoscolastico, $schede);


// VARIABILI PER CALCOLI STATISTICI
$contatori = array();

$contatori['candidatiinterniammessi'] = 0;
$contatori['candidatiesterniammessi'] = 0;
$contatori['candidatiinterniesaminati'] = 0;
$contatori['candidatiesterniesaminati'] = 0;
$contatori['candidatiinterniassgiu'] = 0;
$contatori['candidatiesterniassgiu'] = 0;
$contatori['candidatiinterniassing'] = 0;
$contatori['candidatiesterniassing'] = 0;
$contatori['candidatiinternilicenziati'] = 0;
$contatori['candidatiesternilicenziati'] = 0;
$contatori['candidatiinterninonlicesito'] = 0;
$contatori['candidatiesterninonlicesito'] = 0;
$contatori['candidatiinterninonlicassenza'] = 0;
$contatori['candidatiesterninonlicassenza'] = 0;

$contatori['candidatitotaliammessi'] = 0;

$contatori['candidatitotaliesaminati'] = 0;

$contatori['candidatitotaliassenti'] = 0;


$contatori['candidatitotalilicenziati'] = 0;

$contatori['candidatitotalinonlicenziati'] = 0;








// STATISTICHE
$query = "select *,tbl_alunni.idclasse as idclassealunno from tbl_esesiti, tbl_alunni, tbl_classi
              where tbl_esesiti.idalunno = tbl_alunni.idalunno
              and tbl_alunni.idclasseesame = tbl_classi.idclasse
              and tbl_alunni.idclasseesame <> 0";
$risesi = eseguiQuery($con, $query);
// print inspref($query);

while ($recesa = mysqli_fetch_array($risesi))
{


    if ($recesa['idclassealunno'] != 0)
    {
        $contatori['candidatiinterniammessi'] ++;
        $contatori['candidatitotaliammessi'] ++;
    } else
    {

        $contatori['candidatiesterniammessi'] ++;
        $contatori['candidatitotaliammessi'] ++;
    }

    if ($recesa['idclassealunno'] != 0 & $recesa['votofinale'] > 0)
    {
        $contatori['candidatiinterniesaminati'] ++;
        $contatori['candidatitotaliesaminati'] ++;
    }
    if ($recesa['idclassealunno'] == 0 & $recesa['votofinale'] > 0)
    {
        $contatori['candidatiesterniesaminati'] ++;
        $contatori['candidatitotaliesaminati'] ++;
    }

    if ($recesa['idclassealunno'] != 0 & $recesa['votofinale'] == 0)
    {
        $contatori['candidatiinterniassing'] ++;
        $contatori['candidatiinterninonlicassenza'] ++;
        $contatori['candidatitotaliassenti'] ++;
        $contatori['candidatitotalinonlicenziati'] ++;
    }
    if ($recesa['idclassealunno'] == 0 & $recesa['votofinale'] == 0)
    {
        $contatori['candidatiesterniassing'] ++;
        $contatori['candidatiesterninonlicassenza'] ++;
        $contatori['candidatitotaliassenti'] ++;
        $contatori['candidatitotalinonlicenziati'] ++;
    }

    if ($recesa['idclassealunno'] != 0 & $recesa['votofinale'] > 5)
    {
        $contatori['candidatiinternilicenziati'] ++;
        $contatori['candidatitotalilicenziati'] ++;
    }
    if ($recesa['idclassealunno'] == 0 & $recesa['votofinale'] > 5)
    {
        $contatori['candidatiesternilicenziati'] ++;
        $contatori['candidatitotalilicenziati'] ++;
    }

    if ($recesa['idclassealunno'] != 0 & $recesa['votofinale'] < 6 & $recesa['votofinale'] > 0)
    {
        $contatori['candidatiinterninonlicesito'] ++;
        $contatori['candidatitotalinonlicenziati'] ++;
    }

    if ($recesa['idclassealunno'] == 0 & $recesa['votofinale'] < 6 & $recesa['votofinale'] > 0)
    {
        $contatori['candidatiesterninonlicesito'] ++;
        $contatori['candidatitotalinonlicenziati'] ++;
    }
}



// STATISTICHE





stampa_commissione($arrdocenti, $arrmaterie, $contatori, $schede, $dataverbale, $presidente);


$numalu = 0;
$query = "select distinct idclasseesame,anno, sezione, specializzazione from tbl_alunni,tbl_classi
          where tbl_alunni.idclasseesame = tbl_classi.idclasse
          order by specializzazione, sezione";
$riscla = eseguiQuery($con, $query);
while ($reccla = mysqli_fetch_array($riscla))
{
    $idclasseesame = $reccla['idclasseesame'];
    $query = "select * from tbl_esesiti, tbl_alunni, tbl_classi
              where tbl_esesiti.idalunno = tbl_alunni.idalunno
              and tbl_alunni.idclasseesame = tbl_classi.idclasse
              and tbl_alunni.idclasseesame = $idclasseesame
              order by tbl_alunni.idclasse desc, cognome, nome, datanascita";
    $risesi = eseguiQuery($con, $query);

    $progrclasse = 0;
    $posYiniz = 0;
    while ($recesa = mysqli_fetch_array($risesi))
    {
        $classe = decodifica_classe_no_spec($idclasseesame, $con, 1);
        $classe .= " ".$_SESSION['plesso_specializzazione']." ";
        $classe .= decodifica_classe_spec($idclasseesame, $con);
        $numalu++;
        $progrclasse++;
        //print $numalu;
        if ($progrclasse % 2 != 0)
        {
            $schede->AddPage();
            stampa_testata_registro($classe, $annoscolastico, $schede);
            $posYiniz = 20;
        } else
        {

            $posYiniz = 145;
        }
        stampa_alunno($recesa['idalunno'], $numalu, $posYiniz, $con, $schede, $dataverbale, $presidente);
    }
}

$nomefile = "verbale_esame_" . $idclasse . ".pdf";
$schede->Output($nomefile, "I");

mysqli_close($con);

function stampa_alunno($idalunno, $numalunno, $posYiniz, $con, &$schede, $dataverbale, $presidente)
{
    $schede->rect(20, $posYiniz, 180, 125);
    $schede->rect(20, $posYiniz, 60, 125);
    $schede->SetFont('Times', '', 10);
    $schede->setXY(20, $posYiniz);
    $schede->MultiCell(60, 7, converti_utf8("Cognome, nome e generalità del candidato"), 1, "C");
    $schede->setXY(80, $posYiniz);
    $schede->MultiCell(120, 14, converti_utf8("Risultato dell'esame di stato"), 1, "C");

    $schede->setXY(20, $posYiniz + 14);
    $schede->Cell(50, 10, "N. $numalunno", 0, 0, "C");
    $query = "SELECT * FROM tbl_alunni,tbl_esesiti
              WHERE tbl_alunni.idalunno=tbl_esesiti.idalunno and tbl_alunni.idalunno=$idalunno";
    $ris = eseguiQuery($con, $query);
    if ($val = mysqli_fetch_array($ris))
    {
        $datanascita = data_italiana($val['datanascita']);
        $codfiscale = $val['codfiscale'];

        $comunenascita = converti_utf8(decodifica_comune($val['idcomnasc'], $con));
        $comuneresidenza = converti_utf8(decodifica_comune($val['idcomres'], $con));
        $provincianasc = estrai_sigla_provincia($val['idcomnasc'], $con);
        $indirizzo = $val['indirizzo'];
        $cognome = $val['cognome'];
        $nome = $val['nome'];
        $sesso = substr($codfiscale, 9, 2) > 35 ? 'f' : 'm';
        $idclasse = $val['idclasse'];
        $votofinale = $val['votofinale'];
        $giudiziocomplessivo = $val['giudiziocomplessivo'];
        $consiglioorientativo = $val['consorientcomm'];
        if ($consiglioorientativo=='')
            $consiglioorientativo = $val['consorientcons'];
    //    if ($sesso == 'm')
    //    {
            if ($votofinale >= 6)
                $esito = "ha superato l'esame";
            else
                $esito = "non ha superato l'esame";
    //    }
    //    else
    //    {
    //        if ($votofinale >= 6)
    //            $esito = "ha superato l'esame";
    //        else
    //            $esito = "non ha superato l'esame";
    //    }
        if ($idclasse != 0)
            $classe = decodifica_classe($idclasse, $con);
        else
            $classe = "";
    }
    $schede->SetFont('Times', '', 10);
    $schede->setXY(20, $posYiniz + 24);
    $schede->Cell(60, 7, converti_utf8("$cognome $nome"), 0, 0, "L");
    $schede->setXY(20, $posYiniz + 31);
    $schede->Cell(60, 7, converti_utf8("Nato a " . $comunenascita), 0, 0, "L");
    $schede->setXY(20, $posYiniz + 38);
    $schede->Cell(60, 7, converti_utf8("Prov. " . $provincianasc), 0, 0, "L");
    $schede->setXY(20, $posYiniz + 45);
    $schede->Cell(60, 7, converti_utf8("addì " . $datanascita), 0, 0, "L");
    $schede->setXY(20, $posYiniz + 52);
    $schede->Cell(60, 7, converti_utf8("Abitante in " . $comuneresidenza), 0, 0, "L");
    $schede->setXY(20, $posYiniz + 59);
    $schede->Cell(60, 7, converti_utf8($indirizzo), 0, 0, "L");
    if ($sesso == 'm')
    {
        if ($classe != "")
        {
            $schede->setXY(20, $posYiniz + 66);
            $schede->Cell(60, 7, converti_utf8("Ammesso in seguito a scrutinio"), 0, 0, "L");
            //  $schede->setXY(20, $posYiniz + 72);
            //  $schede->Cell(50, 7, converti_utf8($classe), 0, 0, "L");
        } else
        {
            $schede->setXY(20, $posYiniz + 66);
            $schede->Cell(60, 7, converti_utf8("Ammesso in seguito a domanda"), 0, 0, "L");
        }
    } else
    {
        if ($classe != "")
        {
            $schede->setXY(20, $posYiniz + 66);
            $schede->Cell(60, 7, converti_utf8("Ammessa in seguito a scrutinio"), 0, 0, "L");
            //  $schede->setXY(20, $posYiniz + 72);
            //  $schede->Cell(50, 7, converti_utf8($classe), 0, 0, "L");
        } else
        {
            $schede->setXY(20, $posYiniz + 66);
            $schede->Cell(60, 7, converti_utf8("Ammessa in seguito a domanda"), 0, 0, "L");
        }
    }
    $query = "select * from tbl_esmaterie where idclasse=$idclasse";
    $rismat = eseguiQuery($con, $query);
    $recmat = mysqli_fetch_array($rismat);
    $secondalingua = converti_utf8($recmat['m' . $recmat['num2lin'] . 'e']);

    $schede->setXY(20, $posYiniz + 80);
    $schede->Cell(60, 7, "Seconda lingua com.: $secondalingua", 0, 0, "L");

    $schede->setXY(20, $posYiniz + 87);
    $schede->Cell(60, 7, "NOTE", 0, 0, "C");



    // ESITO ESAME

    $schede->setXY(80, $posYiniz + 14);
    if ($sesso == 'm')
        $schede->Cell(120, 7, converti_utf8("Il Presidente, sulla base del giudizio della commissione dichiara che il candidato"), 0, 0, "C");
    else
        $schede->Cell(120, 7, converti_utf8("Il Presidente, sulla base del giudizio della commissione dichiara che la candidata"), 0, 0, "C");
    $schede->setXY(80, $posYiniz + 21);
  //  if ($sesso == 'm')
  //      $schede->Cell(120, 7, converti_utf8("$cognome $nome $esito"), 0, 0, "C");
  //  else
        $schede->Cell(120, 7, converti_utf8("$cognome $nome $esito"), 0, 0, "C");
    $schede->setXY(80, $posYiniz + 28);

    $schede->Cell(120, 7, converti_utf8("con la valutazione di $votofinale / 10"), 0, 0, "C");
    $schede->setXY(80, $posYiniz + 35);
    $schede->SetFont('Times', 'B', 8);
    $schede->Cell(120, 7, converti_utf8("Motivato giudizio complessivo sul grado di formazione e di sviluppo della personalità del candidato"), 0, 0, "L");
    $schede->setXY(80, $posYiniz + 40);
    $schede->SetFont('Times', '', 8);
    $schede->MultiCell(120, 7, converti_utf8($giudiziocomplessivo), 0, "L");
    $schede->setXY(80, $posYiniz + 70);
    $schede->SetFont('Times', 'B', 8);
    $schede->Cell(120, 7, converti_utf8("Consiglio orientativo sulle scelte successive"), 0, 0, "L");
    $schede->setXY(80, $posYiniz + 75);
    $schede->SetFont('Times', '', 8);
    $schede->MultiCell(120, 7, converti_utf8($consiglioorientativo), 0, "L");


    $schede->setXY(80, $posYiniz + 110);
    $schede->SetFont('Times', '', 8);
    $schede->Cell(120, 7, converti_utf8("Data $dataverbale"), 0, "L");

    if ($_SESSION['suffisso'] != "")
    {
        $suff = $_SESSION['suffisso'] . "/";
    } else
        $suff = "";
    $schede->setXY(100, $posYiniz + 85);
    $schede->Image('../abc/' . $suff . 'timbro.png');


    $schede->setXY(140, $posYiniz + 100);
    $schede->SetFont('Arial', '', 11);
    $schede->Cell(40, 5, converti_utf8($presidente), "B", 0, "C");

    $dicituradirigente = "IL PRESIDENTE";
    $schede->setXY(140, $posYiniz + 90);
    $schede->SetFont('Arial', '', 8);
    $schede->Cell(40, 3, $dicituradirigente, "", 0, "C");
}

function stampa_testata_registro($classe, $annoscolastico, &$schede)
{
    $schede->SetFont('Times', '', 12);
    $schede->setXY(20, 10);
    $schede->Cell(170, 0, "Classe: $classe" . " - Anno scolastico " . $annoscolastico, 0, 0, "C");
}

function stampa_prima_pagina($annoscolastico, &$schede)
{
    $schede->AddPage();
    $schede->SetFont('Times', 'B', 20);
    $schede->setXY(20, 100);
    $schede->Cell(170, 0, converti_utf8("REGISTRO DEGLI ESAMI DI STATO"), 0, 0, "C");
    $schede->SetFont('Times', '', 16);
    $schede->setXY(20, 110);
    $schede->Cell(170, 0, converti_utf8("CONCLUSIVI DEL PRIMO CICLO DI ISTRUZIONE"), 0, 0, "C");
    $schede->SetFont('Times', 'I', 14);
    $schede->setXY(20, 120);
    $schede->Cell(170, 0, converti_utf8("(Decreto Legge 1° Settembre 2008, n. 137 - Legge 30 ottobre 2008 n. 169)"), 0, 0, "C");
    $schede->SetFont('Times', '', 16);
    $schede->setXY(20, 150);
    $schede->Cell(170, 0, converti_utf8("Anno scolastico " . $annoscolastico), 0, 0, "C");
}

function stampa_commissione($elencodocenti, $elencomaterie, &$contatori, &$schede, $dataverbale, $presidente)
{
    $schede->AddPage();

    $posY = 8;
    $schede->setFont('Times', 'B', 12);
    $schede->setXY(15, $posY);
    $schede->Cell(180, 7, "RIASSUNTO STATISTICO", 0, 0, "C");

    $posY = 15;
    $schede->setFont('Times', 'B', 12);
    $schede->setXY(15, $posY);
    $schede->Cell(45, 20, "", 1, 0, "C");

    $posY = 15;
    $schede->setFont('Times', '', 8);
    $schede->setXY(60, $posY);
    $schede->Cell(15, 5, "1", 1, 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(75, $posY);
    $schede->Cell(15, 5, "2", 1, 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(90, $posY);
    $schede->Cell(30, 5, "3", 1, 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(120, $posY);
    $schede->Cell(15, 5, "4", 1, 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(135, $posY);
    $schede->Cell(30, 5, "5", 1, 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(165, $posY);
    $schede->Cell(35, 5, "6", 1, 0, "C");

    $posY = 20;
    $schede->setFont('Times', '', 8);
    $schede->setXY(60, $posY);
    $schede->Cell(15, 5, "Candidati", "ULR", 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(75, $posY);
    $schede->Cell(15, 5, "Candidati", "ULR", 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(90, $posY);
    $schede->Cell(30, 5, "ASSENTI", 1, 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(120, $posY);
    $schede->Cell(15, 15, "ESAME SUPERATO", "ULRB", 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(135, $posY);
    $schede->Cell(30, 5, "ESAME NON SUPERATO", 1, 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(165, $posY);
    $schede->Cell(35, 15, "OSSERVAZIONI", "ULRB", 0, "C");

    $posY = 25;
    $schede->setFont('Times', '', 8);
    $schede->setXY(60, $posY);
    $schede->Cell(15, 5, "ammessi", "LR", 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(75, $posY);
    $schede->Cell(15, 5, "esaminati", "LR", 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(90, $posY);
    $schede->Cell(15, 10, "giustificati", 1, 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(105, $posY);
    $schede->Cell(15, 10, "ingiustificati", 1, 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(135, $posY);
    $schede->Cell(15, 5, "per esito", "LR", 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(150, $posY);
    $schede->Cell(15, 5, "per assenza", "LR", 0, "C");



    $posY = 30;
    $schede->setFont('Times', '', 8);
    $schede->setXY(60, $posY);
    $schede->Cell(15, 5, "agli esami", "LRB", 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(75, $posY);
    $schede->Cell(15, 5, "", "LRB", 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(135, $posY);
    $schede->Cell(15, 5, "esame", "LRB", 0, "C");
    $schede->setFont('Times', '', 8);
    $schede->setXY(150, $posY);
    $schede->Cell(15, 5, "ingiustificata", "LRB", 0, "C");



    $posY = 35;
    $schede->setFont('Times', '', 10);
    $schede->setXY(60, $posY);
    $schede->Cell(15, 8, $contatori['candidatiinterniammessi'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(75, $posY);
    $schede->Cell(15, 8, $contatori['candidatiinterniesaminati'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(90, $posY);
    $schede->Cell(15, 8, $contatori['candidatiinterniassgiu'], 1, 1, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(105, $posY);
    $schede->Cell(15, 8, $contatori['candidatiinterniassing'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(120, $posY);
    $schede->Cell(15, 8, $contatori['candidatiinternilicenziati'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(135, $posY);
    $schede->Cell(15, 8, $contatori['candidatiinterninonlicesito'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(150, $posY);
    $schede->Cell(15, 8, $contatori['candidatiinterninonlicassenza'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(165, $posY);
    $schede->Cell(35, 24, "", 1, 0, "C");

    $posY = 43;
    $schede->setFont('Times', '', 10);
    $schede->setXY(60, $posY);
    $schede->Cell(15, 8, $contatori['candidatiesterniammessi'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(75, $posY);
    $schede->Cell(15, 8, $contatori['candidatiesterniesaminati'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(90, $posY);
    $schede->Cell(15, 8, $contatori['candidatiesterniassgiu'], 1, 1, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(105, $posY);
    $schede->Cell(15, 8, $contatori['candidatiesterniassing'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(120, $posY);
    $schede->Cell(15, 8, $contatori['candidatiesternilicenziati'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(135, $posY);
    $schede->Cell(15, 8, $contatori['candidatiesterninonlicesito'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(150, $posY);
    $schede->Cell(15, 8, $contatori['candidatiesterninonlicassenza'], 1, 0, "C");

    $posY = 51;
    $schede->setFont('Times', '', 10);
    $schede->setXY(60, $posY);
    $schede->Cell(15, 8, $contatori['candidatitotaliammessi'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(75, $posY);
    $schede->Cell(15, 8, $contatori['candidatitotaliesaminati'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(90, $posY);
    $schede->Cell(30, 8, $contatori['candidatitotaliassenti'], 1, 1, "C");

    $schede->setFont('Times', '', 10);
    $schede->setXY(120, $posY);
    $schede->Cell(15, 8, $contatori['candidatitotalilicenziati'], 1, 0, "C");
    $schede->setFont('Times', '', 10);
    $schede->setXY(135, $posY);
    $schede->Cell(30, 8, $contatori['candidatitotalinonlicenziati'], 1, 0, "C");




    $posY = 35;
    $schede->setFont('Times', 'B', 11);
    $schede->setXY(15, $posY);
    $schede->Cell(45, 8, "CANDIDATI INTERNI", 1, 0, "C");
    $posY = 43;
    $schede->setFont('Times', 'B', 11);
    $schede->setXY(15, $posY);
    $schede->Cell(45, 8, "CANDIDATI ESTERNI", 1, 0, "C");
    $posY = 51;
    $schede->setFont('Times', 'B', 11);
    $schede->setXY(15, $posY);
    $schede->Cell(45, 8, "TOTALE", 1, 0, "C");

    /*
     * ELENCO DOCENTI COMMISSIONE
     */


    $posY = 62;
    $schede->setFont('Times', 'B', 12);
    $schede->setXY(15, $posY);
    $schede->Cell(180, 7, "LA COMMISSIONE", 0, 0, "C");
    $posY = 70;
    $schede->setFont('Times', '', 10);
    $schede->setXY(15, $posY);
    $schede->Cell(5, 7, converti_utf8("N."), 1, 0, "L");
    $schede->Cell(60, 7, converti_utf8("COGNOME E NOME"), 1, 0, "L");
    $schede->Cell(80, 7, converti_utf8("MATERIE"), 1, 0, "L");
    $schede->Cell(40, 7, "FIRMA", 1, 0, "L");

    $progrdoc = 0;
    foreach ($elencodocenti as $docente)
    {
        if ($posY > 240)
        {
            $schede->AddPage();
            $posY = 20;
            $schede->setFont('Times', '', 10);
            $schede->setXY(15, $posY);
            $schede->Cell(5, 7, converti_utf8("N."), 1, 0, "L");
            $schede->Cell(60, 7, converti_utf8("COGNOME E NOME"), 1, 0, "L");
            $schede->Cell(80, 7, converti_utf8("MATERIE"), 1, 0, "L");
            $schede->Cell(40, 7, "FIRMA", 1, 0, "L");
        }
        $posY += 7;
        $schede->setXY(15, $posY);
        $schede->setFont('Times', '', 9);
        $numprogr = $progrdoc + 1;
        $schede->Cell(5, 7, "$numprogr", 1, 0, "L");


        $schede->setXY(20, $posY);
        $schede->setFont('Times', '', 10);
        $schede->Cell(60, 7, converti_utf8("$docente"), 1, 0, "L");

        $materie = $elencomaterie[$progrdoc];
        $schede->setXY(80, $posY);
        $schede->setFont('Times', '', 10);
        if ($materie == "")
            $materie = "Sostegno";
        $schede->Cell(80, 7, converti_utf8("$materie"), 1, 0, "L");


        $schede->setXY(160, $posY);
        $schede->setFont('Times', '', 10);
        $schede->Cell(40, 7, "", 1, 0, "L");
        $progrdoc++;
    }
    $schede->setXY(30, $posY + 20);
    $schede->SetFont('Times', '', 8);
    $schede->Cell(120, 7, converti_utf8("Data $dataverbale"), 0, "L");

    if ($_SESSION['suffisso'] != "")
    {
        $suff = $_SESSION['suffisso'] . "/";
    } else
        $suff = "";
    $schede->setXY(70, $posY + 10);
    $schede->Image('../abc/' . $suff . 'timbro.png');


    $schede->setXY(140, $posY + 25);
    $schede->SetFont('Arial', '', 11);
    $schede->Cell(40, 5, converti_utf8($presidente), "B", 0, "C");

    $dicituradirigente = "IL PRESIDENTE";
    $schede->setXY(140, $posY + 15);
    $schede->SetFont('Arial', '', 8);
    $schede->Cell(40, 3, $dicituradirigente, "", 0, "C");
}
