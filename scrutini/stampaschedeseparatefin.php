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
//$periodo = 2;
$periodo = stringa_html('periodo');
$alunni = array();
if ($idclasse != "")
{
    if ($periodo == '9')
    {
        $conddebito = " and idalunno in (select idalunno from tbl_esiti WHERE (validita='1' OR validita='2') AND esito=0) ";
    }
    else
    {
        $conddebito = "";
    }

    $query = "select idalunno from tbl_alunni where idclasse=$idclasse $conddebito order by cognome,nome";
    $ris = mysqli_query($con, inspref($query));
    while ($val = mysqli_fetch_array($ris))
    {
        $alunni[] = $val['idalunno'];
    }
}
else
{

    $alunni[] = $idalunno;
    $query = "select idclasse from tbl_alunni where idalunno=$idalunno";
    $ris = mysqli_query($con, inspref($query));
    if ($val = mysqli_fetch_array($ris))
    {
        $idclasse = $val['idclasse'];
    }
}

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

mysqli_close($con);

stampa_schede($alunni, $periodo, $idclasse, $datastampa, $firmadirigente);


function stampa_schede($alunni, $periodo, $classe, $datastampa, $firmadirigente)
{
    @require("../php-ini" . $_SESSION['suffisso'] . ".php");
    require_once("../lib/fpdf/fpdf.php");

    $schede = new FPDF();
    $schede->AddFont('palacescript', '', 'palacescript.php'); // Font del Ministero
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

    $datascrutinio = data_italiana(estrai_datascrutinio($classe, $periodo, $con));

    foreach ($alunni as $alu)
    {


        // STAMPO LE SCHEDE SEPARATE


        $query = "SELECT distinct tbl_materie.idmateria,sigla,denominazione,tipovalutazione FROM tbl_cattnosupp,tbl_materie
           WHERE tbl_cattnosupp.idmateria=tbl_materie.idmateria
           and tbl_cattnosupp.idclasse=$classe
           and tbl_cattnosupp.iddocente <> 1000000000
           and tbl_materie.progrpag = 99
           order by tbl_materie.progrpag,tbl_materie.sigla";
        $rismat = mysqli_query($con, inspref($query));
        while ($valmat = mysqli_fetch_array($rismat))
        {

            $materia = $valmat['denominazione'];
            $codmateria = $valmat['idmateria'];
            $unico1 = "";
            $assenze1 = "";
            $annotazioni1 = "";
            $query = "SELECT votounico,assenze,note FROM tbl_valutazionifinali
              WHERE idalunno=$alu
              AND periodo='1'
              AND idmateria=$codmateria";
            // print inspref($query);
            $risvoti = mysqli_query($con, inspref($query));

            if ($recvoti = mysqli_fetch_array($risvoti))
            {
                $unico1 = dec_to_pag($recvoti['votounico']);
                $assenze1 = $recvoti['assenze'];
                $annotazioni1 = $recvoti['note'];

            }


            $unico = "";
            $assenze = "";
            $annotazioni = "";
            $query = "SELECT votounico,assenze,note FROM tbl_valutazionifinali
              WHERE idalunno=$alu
              AND periodo='$numeroperiodi'
              AND idmateria=$codmateria";
            //   print inspref($query)."<br>";
            $risvoti = mysqli_query($con, inspref($query));

            if ($recvoti = mysqli_fetch_array($risvoti))
            {
                $unico = dec_to_pag($recvoti['votounico']);
                $annotazioni = $recvoti['note'];

            }
            // print "unico1 $unico1 unico $unico annotazioni1 $annotazioni1 annotazioni $annotazioni1 <br>";
            if ($unico1 != "" | $unico != "" | $annotazioni1 != "" | $annotazioni != "")
            {
                $schede->AddPage();

                $schede->Image('../immagini/repubblica.png', 95, NULL, 13, 15);
                //$schede->Image('../immagini/miur.png',35,NULL,120,10);
                $schede->SetFont('palacescript', '', 32);
                $ministero = converti_utf8("Ministero dell’Istruzione, dell’ Università e della Ricerca");
                $schede->Cell(190, 8, $ministero, NULL, 1, "C");
                $schede->SetFont('Arial', 'BI', 9);
                $schede->Cell(190, 6, converti_utf8("$nome_scuola"), NULL, 1, "C");
                $schede->SetFont('Arial', 'BI', 9);
                $schede->Cell(190, 6, converti_utf8("$comune_scuola"), NULL, 1, "C");
                $schede->SetFont('Arial', 'BI', 9);

                $specplesso = converti_utf8($plesso_specializzazione . ": " . decodifica_classe_spec($classe, $con));
                $schede->Cell(190, 6, $specplesso, NULL, 1, "C");
                $schede->setXY($schede->getX(), $schede->getY() + 10);
                $schede->SetFont('Arial', 'B', 10);

                $annoscolastico = $annoscol . "/" . ($annoscol + 1);
                $schede->Cell(190, 6, "SCHEDA DI VALUTAZIONE FINALE DI " . converti_utf8($materia) . " - A.S. $annoscolastico", NULL, 1, "C");
                //$schede->Cell(190,4,"",NULL,1,"C");
                //$schede->Cell(190,4,$materia,"B",1,"C");


                // Prelievo dei dati degli alunni

                $datanascita = "";
                $codfiscale = "";
                $denominazione = "";
                $query = "SELECT datanascita, codfiscale, denominazione FROM tbl_alunni,tbl_comuni
					  WHERE tbl_alunni.idcomnasc=tbl_comuni.idcomune 
					  AND idalunno=$alu";
                $ris = mysqli_query($con, inspref($query));
                if ($val = mysqli_fetch_array($ris))
                {
                    $datanascita = data_italiana($val['datanascita']);
                    $codfiscale = $val['codfiscale'];
                    $denominazione = converti_utf8($val['denominazione']);

                }

                $schede->setXY($schede->getX(), $schede->getY() + 10);
                // CLASSE
                $schede->SetFont('Arial', '', 8);
                $schede->Cell(25, 6, "Classe: ", 0);
                $schede->SetFont('Arial', 'BI', 8);
                $schede->Cell(30, 6, decodifica_classe_no_spec($classe, $con, 1), 1);
                // COGNOME NOME ALUNNO
                $schede->SetFont('Arial', '', 8);
                $schede->Cell(25, 6, "Alunno: ", 0);
                $schede->SetFont('Arial', 'BI', 8);

                $schede->Cell(110, 6, converti_utf8(decodifica_alunno($alu, $con)), 1, 1);

                // DATA NASCITA
                $schede->SetFont('Arial', '', 8);
                $schede->Cell(25, 6, "Data nascita: ", 0);
                $schede->SetFont('Arial', 'BI', 8);
                $schede->Cell(30, 6, $datanascita, 1);
                // COMUNE DI NASCITA
                $schede->SetFont('Arial', '', 8);
                $schede->Cell(25, 6, "Com. nasc.: ", 0);
                $schede->SetFont('Arial', 'BI', 8);
                $schede->Cell(110, 6, $denominazione, 1, 1);


                // ESTRAGGO LE VALUTAZIONI PERIODICHE
                //$schede->setXY($schede->getX(),$schede->getY()+10);

                //$schede->Cell(190,8,"VALUTAZIONE FINALE PER ".converti_utf8($materia),NULL,1,"C");
                $schede->SetFont('Arial', 'BI', 10);
                $schede->setXY($schede->getX(), $schede->getY() + 10);
                $schede->Cell(190, 8, "PRIMO QUADRIMESTRE", 0, 1, "C");
                $schede->SetFont('Arial', 'B', 10);
                $schede->Cell(190, 8, $unico1, "TLR", 1, "C");
                $schede->SetFont('Arial', NULL, 8);
                $schede->Cell(190, 8, converti_utf8($annotazioni1), "BLR", 1, "C");

                $schede->setXY($schede->getX(), $schede->getY() + 10);
                $schede->SetFont('Arial', 'BI', 10);
                $schede->Cell(190, 8, "SECONDO QUADRIMESTRE", 0, 1, "C");
                $schede->SetFont('Arial', 'B', 10);
                $schede->Cell(190, 8, $unico, "TLR", 1, "C");
                $schede->SetFont('Arial', NULL, 8);
                $schede->Multicell(190, 8, converti_utf8($annotazioni), "BLR", "C");


                $schede->SetFont('Arial', '', 7);
                // LUOGO E DATA SCRUTINIO

                $luogodata = converti_utf8("$comune_scuola, $datastampa");
                $schede->SetY($schede->GetY() + 16);
                $schede->Cell(95, 8, $luogodata, 0, 1, 'L');

                // FIRMA DEL DIRIGENTE
                $schede->Cell(95, 8, "", 0, 0, 'C');
                $schede->Cell(95, 8, "Il Dirigente Scolastico", 0, 1, 'C');
                $schede->Cell(95, 8, "", 0, 0, 'C');
                $schede->Cell(95, 8, converti_utf8($firmadirigente), 0, 1, 'C');
                if ($_SESSION['suffisso'] != "")
                {
                    $suff = $_SESSION['suffisso'] . "/";
                }
                else $suff = "";
                $schede->Image('../abc/' . $suff . 'firmadirigente.png', 120, NULL);
                $schede->setXY(45, $schede->getY() - 30);
                $schede->Image('../abc/' . $suff . 'timbro.png', 60, NULL);
            }

        }

    }


    $nomefile = "pagelle_" . decodifica_classe($classe, $con) . "_" . $periodo . ".pdf";
    $nomefile = str_replace(" ", "_", $nomefile);
    $schede->Output($nomefile, "I"); // "D" Forza download  "I" visione inline


    mysqli_close($con);

}


function elimina_cr($stringa)
{

    $strpul = str_replace(array("\n", "\r"), " ", $stringa);
    return $strpul;
}


