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


//print inspref($query);


$schede = new FPDF('P', 'mm', 'A4');
$schede->AddFont('palacescript', '', 'palacescript.php'); // Font del Ministero

$sql = "SELECT * FROM tbl_classi order by anno, specializzazione, sezione";
$risclassi = mysqli_query($con, inspref($sql));

$altriga=9;
while ($recclasse = mysqli_fetch_array($risclassi))

{
    $posY=10;
    $idclasse = $recclasse['idclasse'];
    $schede->AddPage();
    $schede->SetFont('Arial', 'B', 12);
    $posY += 8;
    $schede->setXY(10, $posY);
    $schede->Cell(210, 6, converti_utf8("$nome_scuola") . " " . converti_utf8("$comune_scuola"), NULL, 1, "C");
    $schede->SetFont('Arial', 'B', 12);
    $posY += 8;
    $schede->setXY(10, $posY);
    $schede->Cell(210, 6, converti_utf8("Elezione genitori rappresentanti di classe"), NULL, 1, "C");
    $posY += 8;
    $schede->SetFont('Arial', 'BI', 12);
    $schede->setXY(10, $posY);
    $specplesso = converti_utf8("A.S.:" . $annoscol . "/" . ($annoscol + 1) . " - Classe: " . decodifica_classe($idclasse, $con, 1));
    $schede->Cell(210, 6, $specplesso, NULL, 1, "C");
    $posY += 8;

    $schede->setXY(10, $posY);
    $schede->Cell(70,$altriga,"Alunno",1,NULL,"L");
    $schede->setXY(80, $posY);
    $schede->Cell(60,$altriga,"Genitore 1",1,NULL,"C");
    $schede->setXY(140, $posY);
    $schede->Cell(60,$altriga,"Genitore 2",1,NULL,"C");

    $schede->SetFont('Arial', '', 8);

    $sqlalu="select * from tbl_alunni where idclasse = $idclasse order by cognome, nome";
    $risalu = mysqli_query($con, inspref($sqlalu)) or die ("Errore nella query: " . mysqli_error($con) . $sqlalu);
    while ($recalu = mysqli_fetch_array($risalu))
    {
        $posX = 10;
        $posY += $altriga;
        $schede->setXY(10, $posY);
        $schede->Cell(70,$altriga,converti_utf8(estrai_dati_alunno($recalu['idalunno'],$con)),1,NULL,"L");
        $schede->setXY(80, $posY);
        $padre=$recalu['cognomepa']." ".$recalu['nomepa'];
        $schede->Cell(60,$altriga,converti_utf8($padre),1,NULL,"C");
        $madre=$recalu['cognomema']." ".$recalu['nomema'];
        $schede->setXY(140, $posY);
        $schede->Cell(60,$altriga,converti_utf8($madre),1,NULL,"C");


    }
}

$nomefile = "foglielezioni.pdf";
$nomefile = str_replace(" ", "_", $nomefile);
$schede->Output($nomefile, "I");


mysqli_close($con);