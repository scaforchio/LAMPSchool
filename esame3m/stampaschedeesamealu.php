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
$gioass = stringa_html('gioass');
$firmadirigente = stringa_html('firma');
$periodo = stringa_html('periodo');
$alunni = array();
if ($idalunno != $_SESSION['idutente'] && $tipoutente == 'T')
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

// Se c'è il parametro della classe stampo tutti gli alunni della classe
// altrimenti solo l'alunno passato come parametro
if ($idclasse != "")
{

    $query = "select idalunno from tbl_alunni where idclasseesame=$idclasse order by idclasse DESC, cognome,nome";
    $ris = mysqli_query($con, inspref($query));
    while ($val = mysqli_fetch_array($ris))
    {
        $alunni[] = $val['idalunno'];
    }
}
else
{

    $alunni[] = $idalunno;
    $query = "select idclasseesame from tbl_alunni where idalunno=$idalunno";
    $ris = mysqli_query($con, inspref($query));
    if ($val = mysqli_fetch_array($ris))
    {
        $idclasse = $val['idclasseesame'];

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

stampa_schede($alunni, $periodo, $idclasse, $datastampa, $firmadirigente, $gioass);

mysqli_close($con);


function stampa_schede($alunni, $periodo, $idclasse, $datastampa, $firmadirigente, $gioass)
{
    @require("../php-ini" . $_SESSION['suffisso'] . ".php");
    // require_once("../lib/tfpdf/tfpdf.php");
    require_once("../lib/fpdf/fpdf.php");
    $schede = new FPDFPAG();
    $schede->AliasNbPages();
    $schede->AddFont('palacescript', '', 'palacescript.php'); // Font del Ministero
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

    $datascrutinio = data_italiana(estrai_datascrutinio($idclasse, $periodo, $con));


    $query = "select * from tbl_esami3m where idclasse=$idclasse";
    $risesa = mysqli_query($con, inspref($query));
    $recesa = mysqli_fetch_array($risesa);


    $query = "select * from tbl_esmaterie where idclasse=$idclasse";
    $rismat = mysqli_query($con, inspref($query));
    $recmat = mysqli_fetch_array($rismat);


    $primalingua = converti_utf8($recmat['m3e']);
    $secondalingua = converti_utf8($recmat['m' . $recmat['num2lin'] . 'e']);


    foreach ($alunni as $alu)
    {
        $query = "select * from tbl_esesiti where idalunno=$alu";
        $ris = mysqli_query($con, inspref($query));
        $rec = mysqli_fetch_array($ris);


        $schede->AddPage();

        // $indirizzo_scuola = "Via jkjkjkjkjjl";

        $schede->Image('../immagini/repubblica.png', 96, 20, 13, 15);

        $schede->SetFont('palacescript', '', 28);
        $schede->setXY(20, 40);
        $ministero = converti_utf8("Ministero dell’Istruzione, dell’ Università e della Ricerca");
        $schede->Cell(172, 8, $ministero, NULL, 1, "C");

        $schede->SetFont('Arial', 'B', 12);
        $schede->setXY(20, 60);
        $schede->Cell(172, 6, converti_utf8("$nome_scuola"), NULL, 1, "C");

        $schede->SetFont('Arial', 'BI', 10);
        $schede->setXY(20, 66);
        $schede->Cell(172, 6, converti_utf8("$comune_scuola"), NULL, 1, "C");

        $schede->SetFont('Arial', '', 8);
        $schede->setXY(20, 72);
        $schede->Cell(172, 6, converti_utf8("$indirizzo_scuola"), NULL, 1, "C");


        $schede->SetFont('Arial', 'B', 14);
        /*  if ($numeroperiodi==3)
             $per="trimestre";
          else
             $per="quadrimestre";
          $per=converti_utf8($per); */
        $annoscolastico = $annoscol . "/" . ($annoscol + 1);
        $schede->setXY(20, 82);
        $schede->MultiCell(172, 6, "ESAME DI STATO\nCONCLUSIVO DEL PRIMO CICLO DI ISTRUZIONE\n\nSCHEDA PERSONALE DEL CANDIDATO\nCon verbale dei giudizi sulle prove scritte e orali e risultato finale", 0, "C");


        // Prelievo dei dati degli alunni

        $datanascita = "";
        $codfiscale = "";
        $denominazione = "";
        $idcomnasc = "";
        $query = "SELECT datanascita, codfiscale, denominazione,idcomnasc FROM tbl_alunni,tbl_comuni
              WHERE tbl_alunni.idcomnasc=tbl_comuni.idcomune 
              AND idalunno=$alu";
        $ris = mysqli_query($con, inspref($query));
        if ($val = mysqli_fetch_array($ris))
        {
            $datanascita = data_italiana($val['datanascita']);
            $codfiscale = $val['codfiscale'];
            $denominazione = converti_utf8($val['denominazione']);
            $idcomnasc = $val['idcomnasc'];
            if (substr($codfiscale, 9, 2) > '35')
            {
                $sesso = 'f';
            }
            else
            {
                $sesso = 'm';
            }

        }


        // COGNOME NOME ALUNNO

        if ($sesso == 'f')
        {
            $cand = "Candidata";
        }
        else
        {
            $cand = "Candidato";
        }

        $schede->setXY(20, 130);
        $schede->SetFont('Arial', '', 12);
        $schede->Cell(25, 6, "$cand: ", 0);

        $schede->SetFont('Arial', 'B', 12);
        $schede->Cell(35, 6, converti_utf8(decodifica_alunno($alu, $con)), 0, 0, 'L');


        $schede->setXY(20, 140);
        $schede->SetFont('Arial', '', 12);
        $schede->Cell(25, 6, "Cod. fisc.: ", 0);
        $schede->SetFont('Arial', 'B', 12);
        $schede->Cell(35, 6, $codfiscale, 0, 0, 'L');

        if ($sesso == 'f')
        {
            $cand = "Nata il ";
        }
        else
        {
            $cand = "Nato il ";
        }

        $schede->setXY(20, 150);
        $schede->SetFont('Arial', '', 12);
        $schede->Cell(25, 6, "$cand: ", 0);

        $schede->SetFont('Arial', 'B', 12);
        $schede->Cell(35, 6, $datanascita, 0, 0, 'L');


        // COMUNE DI NASCITA
        $posX = 20;
        $schede->setXY(80, 150);
        if ($idcomnasc != '')
        {
            $provincia = estrai_sigla_provincia($idcomnasc, $con);
        }
        if (trim($denominazione) != "" & $denominazione != "NON DEFINITO")
        {
            $schede->SetFont('Arial', '', 12);
            $schede->Cell(10, 6, " a ", 0);

            $schede->SetFont('Arial', 'B', 12);
            $schede->Cell(66, 6, $denominazione . "  ( $provincia )", 0, 0, 'L');
            $posX += 86;
        }


        $schede->setXY(60, 175);
        $schede->SetFont('Arial', 'B', 12);
        $schede->Cell(100, 6, "Prima lingua straniera:", 0, 0, 'C');
        $schede->setXY(60, 185);
        $schede->SetFont('Arial', 'B', 12);
        $schede->Cell(100, 6, $primalingua, 0, 0, 'C');
        $schede->setXY(60, 200);
        $schede->SetFont('Arial', 'B', 12);
        $schede->Cell(100, 6, "Seconda lingua straniera:", 0, 0, 'C');
        $schede->setXY(60, 210);
        $schede->SetFont('Arial', 'B', 12);
        $schede->Cell(100, 6, $secondalingua, 0, 0, 'C');

        $schede->AddPage();

        // SECONDA PAGINA

        $posX = 20;
        $posY = 10;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 12);
        if ($sesso == 'm')
        {
            $schede->Cell(100, 6, converti_utf8("Il candidato è stato ammesso agli esami di licenza con il seguente giudizio di idoneità:"), 0, 0, 'L');
        }
        else
        {
            $schede->Cell(100, 6, converti_utf8("La candidata è stata ammessa agli esami di licenza con il seguente giudizio di idoneità:"), 0, 0, 'L');
        }

        $posY += 10;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', 'B', 14);
        $schede->Cell(170, 6, $rec['votoammissione'] . "    (" . dec_to_pag($rec['votoammissione']) . ")", 0, 0, 'C');

        $posY += 10;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', 'B', 12);
        $schede->Cell(170, 6, converti_utf8("Consiglio Orientativo"), 0, 0, 'C');

        $posY += 10;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(100, 6, converti_utf8("Il consiglio di classe ha inoltre formulato il seguente CONSIGLIO ORIENTATIVO sulle scelte successive:"), 0, 0, 'L');

        $posY += 10;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', 'B', 11);
        $schede->Cell(170, 6, converti_utf8($rec['consorientcons']), 0, 0, 'C');

        $posY += 10;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', 'B', 12);
        $schede->Cell(170, 6, converti_utf8("PROVE D'ESAME"), 0, 0, 'C');

        $posY += 7;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 12);
        $schede->Cell(170, 6, converti_utf8("PROVE SCRITTE"), 0, 0, 'L');

        $posY += 7;

        $schede->setXY($posX, $posY);

        $schede->SetFont('Arial', 'B', 12);
        $testata="Prova scritta di " . $recmat['m1e'];
        $testata.= " - Prova scelta: ".$rec['provasceltam1'];
        $testata.= " - valutazione complessiva  " . $rec['votom1'];
        $schede->Cell(170,7, converti_utf8($testata),"TLR",0,"C");
        $schede->setXY($posX, $posY+7);
        $schede->SetFont('Arial', '', 10);
        $schede->MultiCell(170, 5, converti_utf8($rec['criteri1']),"BLR");

        $pY=$schede->GetY();
        $schede->SetXY($posX,$pY);
        $schede->SetFont('Arial', 'B', 12);
        $testata="Prova scritta di " . $recmat['m2e'];
        $testata.= " - Prova scelta: ".$rec['provasceltam2'];
        $testata.= " - valutazione complessiva  " . $rec['votom2'];
        $schede->Cell(170,7, converti_utf8($testata),"TLR",0,"C");
        $schede->setXY($posX, $pY+7);
        $schede->SetFont('Arial', '', 10);
        $schede->MultiCell(170, 5, converti_utf8($rec['criteri2']),"BLR");

        $pY=$schede->GetY();
        $schede->SetXY($posX,$pY);
        $schede->SetFont('Arial', 'B', 12);
        $testata="Prova scritta di " . $recmat['m3e'];
        $testata.= " - Prova scelta: ".$rec['provasceltam3'];
        $testata.= " - valutazione complessiva  " . $rec['votom3'];
        $schede->Cell(170,7, converti_utf8($testata),"TLR",0,"C");
        $schede->setXY($posX, $pY+7);
        $schede->SetFont('Arial', '', 10);
        $schede->MultiCell(170, 5, converti_utf8($rec['criteri3']),"BLR");

        $pY=$schede->GetY();
        $schede->SetXY($posX,$pY);
        $schede->SetFont('Arial', 'B', 12);
        $testata="Prova scritta di " . $recmat['m' . $recmat['num2lin'] . 'e'];
        $testata.= " - Prova scelta: ".$rec['provasceltam'. $recmat['num2lin']];
        $testata.= " - valutazione complessiva  " . $rec['votom'. $recmat['num2lin']];
        $schede->Cell(170,7, converti_utf8($testata),"TLR",0,"C");
        $pY=$schede->GetY();
        $schede->setXY($posX, $pY+7);
        $schede->SetFont('Arial', '', 10);
        $schede->MultiCell(170, 5, converti_utf8($rec['criteri'. $recmat['num2lin']]),"BLR");

        $pY=$schede->GetY();
        $schede->SetXY($posX,$pY);
        $schede->SetFont('Arial', 'B', 12);
        $testata="Prova scritta nazionale";
        $testata.= " - valutazione complessiva  " . $rec['votom'. $recmat['numpni']];
        $schede->Cell(170,7, converti_utf8($testata),"TLR",0,"C");
        $schede->setXY($posX, $pY+7);
        $testo="Matematica: ".  $rec['votopnimat'];
        $testo.="\n\nItaliano: ".$rec['votopniita']."\n\n";
        $schede->SetFont('Arial', '', 10);
        $schede->MultiCell(170, 5, converti_utf8($testo),"BLR");


        $schede->AddPage();
        // TERZA PAGINA
        $posX = 20;
        $posY = 10;

        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 12);
        $schede->Cell(170, 6, converti_utf8("PROVA ORALE - COLLOQUIO MULTIDISCIPLINARE"), 0, 0, 'L');

        $posY = 20;
        $schede->SetXY($posX,$posY);
        $schede->SetFont('Arial', 'B', 12);
        $testata="Traccia del colloquio";
        $schede->Cell(170,7, converti_utf8($testata),"TLR",0,"C");
        $schede->setXY($posX, $posY+7);
        $schede->SetFont('Arial', '', 10);
        $schede->MultiCell(170, 5, converti_utf8($rec['tracciacolloquio']),"BLR");


        $pY=$schede->GetY();
        $schede->SetXY($posX,$pY);
        $schede->SetFont('Arial', 'B', 12);
        $testata="Giudizio del colloquio orale";
        $schede->Cell(170,7, converti_utf8($testata),"TLR",0,"C");
        $schede->setXY($posX, $pY+7);
        $schede->SetFont('Arial', '', 10);
        $schede->MultiCell(170, 5, converti_utf8($rec['giudiziocolloquio']),"LR");
        $pY=$schede->GetY();
        $schede->SetXY($posX,$pY);
        $schede->SetFont('Arial', 'B', 12);
        $testata="Giudizio del colloquio orale";
        $schede->Cell(170,7, converti_utf8(converti_utf8("Valutazione complessiva colloquio   " . $rec['votoorale'] . "  (" . dec_to_pag($rec['votoorale']) . ")")),"BLR",0,"C");
/*
        $posY += 90;
        $schede->Rect($posX, $posY, 170, 70);
        $schede->SetFont('Arial', '', 12);
        $schede->setXY($posX, $posY);
        $schede->Cell(170, 6, converti_utf8("Giudizio del colloquio orale:"), 0, 0, 'L');
        $schede->SetFont('Arial', '', 10);
        $schede->setXY($posX, $posY + 10);
        $schede->MultiCell(170, 6, converti_utf8($rec['giudiziocolloquio']));
        $schede->SetFont('Arial', '', 12);
        $schede->setXY($posX, $posY + 60);
        $schede->Cell(170, 6, converti_utf8("Valutazione complessiva colloquio   " . $rec['votoorale'] . "  ( " . dec_to_pag($rec['votoorale']) . " )"));
        $schede->setXY($posX, $posY + 80);

*/
        $pY=$schede->GetY();
        $schede->SetXY($posX,$pY+10);
        $schede->Cell(170, 6, "Data, " . data_italiana($rec['datacolloquio']));


        // QUARTA PAGINA
        $schede->AddPage();

        $posX = 20;
        $posY = 10;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', 'B', 14);
        $schede->Cell(170, 6, converti_utf8("RISULTANZE DELL'ESAME"), 0, 0, 'C');

        $posY += 10;

        $schede->SetFont('Arial', 'B', 12);
        $schede->setXY($posX, $posY);
        $schede->Cell(170, 7, converti_utf8("Giudizio complessivo"), "TLR", 'C');
        $schede->SetFont('Arial', '', 10);
        $schede->setXY($posX, $posY + 7);
        $schede->MultiCell(170, 5, converti_utf8($rec['giudiziocomplessivo']),"BLR");
        $pY=$schede->GetY();
        $schede->SetXY($posX,$pY);
        $schede->SetFont('Arial', 'B', 12);
        $schede->setXY($posX, $posY + 60);
        if ($rec['lode'])
        {
            $lode = " con lode ";
        }
        else
        {
            $lode = " ";
        }


        $schede->setXY($posX, $pY);
        $schede->Cell(170, 6, converti_utf8("Valutazione complessiva dell'Esame di Stato  " . $rec['votofinale'] . "  (" . dec_to_pag($rec['votofinale']) . ") $lode."),"LR");
        $schede->setXY($posX, $pY + 6);
        $schede->SetFont('Arial', '', 12);
        $schede->Cell(170, 6, converti_utf8("Si consiglia la frequenza di (Consiglio orientativo):"),"TLR", 0, 'L');
        $schede->SetXY($posX, $pY + 12);
        $schede->SetFont('Arial', '', 10);
        $schede->MultiCell(170, 6, converti_utf8($rec['consorientcomm']),"BLR");

        // Sottocommissione
        $idcommissione = $recesa['idcommissione'];
        $nomepresidente = "";
        $cognomepresidente = "";
        $denominazionecomm = "";
        $query = "select * from tbl_escompcommissioni,tbl_docenti,tbl_escommissioni
                where tbl_escompcommissioni.idcommissione=tbl_escommissioni.idescommissione
                and tbl_escompcommissioni.iddocente=tbl_docenti.iddocente
                and tbl_escompcommissioni.idcommissione=$idcommissione";
        $riscom = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));
        $cont = 0;
        $posYiniz=$schede->GetY();
        while ($reccom = mysqli_fetch_array($riscom))
        {

            $nomepresidente = $reccom['nomepresidente'];
            $cognomepresidente = $reccom['cognomepresidente'];
            $denominazionecomm = $reccom['denominazione'];
            $nomedocente = $reccom['nome'];
            $cognomedocente = $reccom['cognome'];

            $posX = 20 + ($cont % 3 * 55);
            $posY = $posYiniz+18 + (floor($cont / 3) * 18);

            $schede->setXY($posX, $posY);
            $schede->Line($posX, $posY, $posX + 50, $posY);
            $schede->Cell(50, 4, converti_utf8($nomedocente . " " . $cognomedocente), 0, 0, "C");
            $cont++;

        }

        $posY=$schede->getY()+10;
        // Risultato esame

        if ($rec['votofinale'] >= 6)
        {
            $schede->setXY(20, $posY);
            if ($sesso == 'f')
            {
                $cand = "la candidata";
                $dich = "venga dichiarata licenziata";
            }
            else
            {
                $cand = "il candidato";
                $dich = "venga dichiarato licenziato";
            }

            $esito = "La Commissione plenaria, visto il curriculum scolastico e le risultanze dell’esame, delibera che $cand " . decodifica_alunno($alu, $con) . " $dich con la valutazione in decimi di ";
            $esito .= $rec['votofinale'] . "  (" . dec_to_pag($rec['votofinale']) . ")";
            if ($rec['lode'])
            {
                $esito .= " con LODE ";
            }

        }
        else
        {
            $schede->setXY(20, $posY);
            if ($sesso == 'f')
            {
                $cand = "la candidata";
                $dich = "venga dichiarata non licenziata";
            }
            else
            {
                $cand = "il candidato";
                $dich = "venga dichiarato non licenziato";
            }

            $esito = "La Commissione plenaria, visto il curriculum scolastico e le risultanze dell’esame, delibera che $cand " . decodifica_alunno($alu, $con) . " $dich ";

        }
        $esito .= ".";
        $schede->SetFont('Arial', '', 12);
        $schede->MultiCell(170, 7, converti_utf8($esito));

        // STAMPA PARTE TERMINALE
        $datastampa = data_italiana($recesa['datascrutinio']);
        $luogodata = converti_utf8("$comune_scuola, $datastampa");
        $schede->setXY(20, $posY+30);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(70, 5, $luogodata, "", 0, "L");

        $schede->setXY(140, $posY+40);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(40, 5, converti_utf8($nomepresidente . " " . $cognomepresidente), "B", 0, "C");

        $dicituradirigente = "IL PRESIDENTE";
        $schede->setXY(140, $posY+45);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(40, 3, $dicituradirigente, "", 0, "C");

        if ($_SESSION['suffisso'] != "")
        {
            $suff = $_SESSION['suffisso'] . "/";
        }
        else $suff = "";

        $schede->setXY(90, $posY+45);
        $schede->Image('../abc/' . $suff . 'timbro.png');


    }

    if (count($alunni)>1)
        $nomefile = "schede_esame_" . decodifica_classe($idclasse, $con) . ".pdf";
    else
        $nomefile = "scheda_esame_" . decodifica_alunno($alunni[0],$con) . ".pdf";

    $nomefile = str_replace(" ", "_", $nomefile);
    $schede->Output($nomefile, "I");

    mysqli_close($con);
}


function elimina_cr($stringa)
{

    $strpul = str_replace(array("\n", "\r"), " ", $stringa);
    return $strpul;
}


function inserisci_new_line($stringa)
{

    $strpul = str_replace("|", "\n", $stringa);
    return $strpul;
}

function estrai_prima_riga($stringa)
{

    $posint = strpos($stringa, "|");
    if ($posint != 0)
    {
        $str1 = substr($stringa, 0, $posint);
    }
    else
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
    }
    else
    {
        $str2 = "";
    }
    return converti_utf8($str2);
}


