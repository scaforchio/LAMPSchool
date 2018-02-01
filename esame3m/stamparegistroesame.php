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
//print inspref($query);
$ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
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
$risdoc = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
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


$annoscolastico = $annoscol . " / " . ($annoscol + 1);


$query = "select * from tbl_esesiti,tbl_alunni
        where tbl_esesiti.idalunno = tbl_alunni.idalunno
        and tbl_alunni.idclasseesame = $idclasse
        order by idclasse DESC, cognome, nome, datanascita";
$risesi = mysqli_query($con, inspref($query));

$numalu = 0;
$posYiniz = 0;
while ($recesa = mysqli_fetch_array($risesi))
{
    $numalu++;
    //print $numalu;
    if ($numalu % 2 != 0)
    {
        $schede->AddPage();
        stampa_testata_registro($classe, $annoscolastico, $schede);
        $posYiniz = 20;
    }
    else
    {

        $posYiniz = 145;
    }
    stampa_alunno($recesa['idalunno'], $numalu, $posYiniz, $con, $schede);
}


$nomefile = "verbale_esame_" . $idclasse . ".pdf";
$schede->Output($nomefile, "I");

mysqli_close($con);

function stampa_alunno($idalunno, $numalunno, $posYiniz, $con, &$schede)
{
    $schede->rect(20, $posYiniz, 170, 125);

    $schede->setXY(20, $posYiniz);
    $schede->MultiCell(50, 7, converti_utf8("Cognome, nome e generalità del candidato"), 1, "C");
    $schede->setXY(70, $posYiniz);
    $schede->MultiCell(120, 14, converti_utf8("Risultato dell'esame di stato"), 1, "C");

    $schede->setXY(20, $posYiniz + 14);
    $schede->Cell(50, 10, "N. $numalunno", 0, 0, "C");
    $query = "SELECT idclasse,datanascita, codfiscale, denominazione,cognome, nome,idcomnasc,idcomres, indirizzo FROM tbl_alunni
              WHERE idalunno=$idalunno";
    $ris = mysqli_query($con, inspref($query));
    if ($val = mysqli_fetch_array($ris))
    {
        $datanascita = data_italiana($val['datanascita']);
        $codfiscale = $val['codfiscale'];
        $comunenascita = converti_utf8(decodifica_comune($val['idcomnas']));
        $comuneresidenza = converti_utf8(decodifica_comune($val['idcomres']));
        $provincianasc = estrai_sigla_provincia($val['idcomnas'], $con);
        $indirizzo = $val['indirizzo'];
        $cognome = $val['cognome'];
        $nome = $val['nome'];
        $idclasse=$val['idclasse'];
        
        if ($idclasse!=0)
           $classe= decodifica_classe ($idclasse, $con);
        else
            $classe="";
    }
    $schede->SetFont('Times', '', 10);
    $schede->setXY(20, $posYiniz + 24);
    $schede->Cell(50, 7, converti_utf8("$cognome $nome"), 0, 0, "L");
    $schede->setXY(20, $posYiniz + 31);
    $schede->Cell(50, 7, converti_utf8("Nato a " . $comunenascita), 0, 0, "L");
    $schede->setXY(20, $posYiniz + 38);
    $schede->Cell(50, 7, converti_utf8("Prov. " . $provincianasc), 0, 0, "L");
    $schede->setXY(20, $posYiniz + 45);
    $schede->Cell(50, 7, converti_utf8("addì " . $datanascita), 0, 0, "L");
    $schede->setXY(20, $posYiniz + 52);
    $schede->Cell(50, 7, converti_utf8("Abitante in " . $comunenascita), 0, 0, "L");
    $schede->setXY(20, $posYiniz + 59);
    $schede->Cell(50, 7, converti_utf8("Via " . $indirizzo), 0, 0, "L");  
    if ($classe!="")
    {
       $schede->setXY(20, $posYiniz + 66);
       $schede->Cell(50, 7, converti_utf8("Proveniente dalla classe " . $classe), 0, 0, "L");   
    }
    else
    {
        $schede->setXY(20, $posYiniz + 66);
        $schede->Cell(50, 7, converti_utf8("Ammesso in seguito a " . $classe), 0, 0, "L");   
    }
    $query = "select * from tbl_esmaterie where idclasse=$idclasse";
    $rismat = mysqli_query($con, inspref($query));
    $recmat = mysqli_fetch_array($rismat);
    $secondalingua = converti_utf8($recmat['m' . $recmat['num2lin'] . 'e']);
    
    $schede->setXY(20, $posYiniz + 73);
    $schede->Cell(50, 7, "Seconda lingua com.: $secondalingua", 0, 0, "L");    
    
    $schede->setXY(20, $posYiniz + 80);
    $schede->Cell(50, 7, "NOTE", 0, 0, "C");    
    
    
    
    // ESITO ESAME
    
    $schede->setXY(70, $posYiniz + 14);
    $schede->Cell(120, 7, "Il Presidente, sulla base del giudizio della commissione dichiara che il candidato", 0, 0, "C");    
    $schede->setXY(70, $posYiniz + 21);
 //   $schede->Cell(120, 7, "$cognome $nome é stato ", sulla base del giudizio della commissione dichiara che il candidato", 0, 0, "C");    
    
}

function stampa_testata_registro($classe, $annoscolastico, &$schede)
{
    $schede->SetFont('Times', '', 12);
    $schede->setXY(20, 10);
    $schede->Cell(170, 0, "Classe: $classe" . " - Anno scolastico " . $annoscolastico, 0, 0, "C");
}
