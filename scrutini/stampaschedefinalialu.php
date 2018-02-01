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
//$periodo=2;
$alunni = array();
if ($idalunno!=$_SESSION['idutente'] && $tipoutente=='T')
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

// Se c'è il parametro della classe stampo tutti gli alunni della classe
// altrimenti solo l'alunno passato come parametro
if ($idclasse != "")
{
    if ($periodo=='9')
        $conddebito = " and idalunno in (select idalunno from tbl_esiti WHERE (validita='1' OR validita='2') AND esito=0) ";
    else
        $conddebito = "";
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

stampa_schede($alunni, $periodo, $idclasse, $datastampa, $firmadirigente, $gioass);

mysqli_close($con);


function stampa_schede($alunni, $periodo, $classe, $datastampa, $firmadirigente, $gioass)
{
    @require("../php-ini" . $_SESSION['suffisso'] . ".php");
    // require_once("../lib/tfpdf/tfpdf.php");
    require_once("../lib/fpdf/fpdf.php");
    $schede = new FPDF();
    $schede->AddFont('palacescript', '', 'palacescript.php'); // Font del Ministero
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

    $datascrutinio = data_italiana(estrai_datascrutinio($classe, $periodo, $con));

    foreach ($alunni as $alu)
    {
        $schede->AddPage();


        $schede->Image('../immagini/repubblica.png', 96, 20, 13, 15);

        $schede->SetFont('palacescript', '', 32);
        $schede->setXY(20, 40);
        $ministero = converti_utf8("Ministero dell’Istruzione, dell’ Università e della Ricerca");
        $schede->Cell(172, 8, $ministero, NULL, 1, "C");

        $schede->SetFont('Arial', 'B', 10);
        $schede->setXY(20, 60);
        $schede->Cell(172, 6, converti_utf8("$nome_scuola"), NULL, 1, "C");

        $schede->SetFont('Arial', 'BI', 9);
        $schede->setXY(20, 66);
        $schede->Cell(172, 6, converti_utf8("$comune_scuola"), NULL, 1, "C");

        $schede->SetFont('Arial', 'BI', 9);
        $schede->setXY(20, 72);
        $specplesso = converti_utf8($plesso_specializzazione . ": " . decodifica_classe_spec($classe, $con));
        $schede->Cell(172, 6, $specplesso, NULL, 1, "C");

        $schede->SetFont('Arial', 'B', 10);
        /*  if ($numeroperiodi==3)
             $per="trimestre";
          else
             $per="quadrimestre";
          $per=converti_utf8($per); */
        $annoscolastico = $annoscol . "/" . ($annoscol + 1);
        $schede->setXY(20, 82);
        $schede->Cell(172, 6, "SCHEDA DI VALUTAZIONE FINALE - A.S. $annoscolastico", NULL, 1, "C");


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

        // CLASSE
        $schede->SetFont('Arial', '', 8);
        $schede->setXY(20, 100);
        $schede->Cell(20, 6, "Classe: ", 0);

        $schede->SetFont('Arial', 'BI', 8);
        //$schede->setXY(220,58);
        $schede->Cell(20, 6, decodifica_classe_no_spec($classe, $con, 1), 1);
        // COGNOME NOME ALUNNO

        $schede->SetFont('Arial', '', 8);
        $schede->Cell(25, 6, "Alunno: ", 0);

        $schede->SetFont('Arial', 'BI', 8);
        $schede->Cell(107, 6, converti_utf8(decodifica_alunno($alu, $con)), 1, 1);

        // DATA NASCITA
        $schede->SetFont('Arial', '', 8);
        $schede->setXY(20, 110);
        $schede->Cell(20, 6, "Data nascita: ", 0);

        $schede->SetFont('Arial', 'BI', 8);
        $schede->Cell(20, 6, $datanascita, 1);

        // COMUNE DI NASCITA
        $posX = 20;
        $schede->setXY($posX, 120);
        if (trim($denominazione) != "" & $denominazione != "NON DEFINITO")
        {
            $schede->SetFont('Arial', '', 8);
            $schede->Cell(20, 6, "Com. nasc.: ", 0);

            $schede->SetFont('Arial', 'BI', 8);
            $schede->Cell(66, 6, $denominazione, 1, 1);
            $posX += 86;
        }
        // COMUNE DI NASCITA
        $schede->setXY($posX, 120);
        if (trim($codfiscale) != "")
        {

            $schede->SetFont('Arial', '', 8);
            $schede->Cell(20, 6, "Cod. fisc.: ", 0);

            $schede->SetFont('Arial', 'BI', 8);
            $schede->Cell(66, 6, $codfiscale, 1, 1);
        }
        // ATTESTATO DI ESITO FINALE


        $esito = "";
        $idesito = 0;
        $votoammissione = 0;
        $creditotot = 0;
        $credito = 0;

        $query = "select * from tbl_esiti where idalunno='$alu'";

        $risesi = mysqli_query($con, inspref($query));

        if ($recesi = mysqli_fetch_array($risesi))
        {
            //$esito = decodifica_esito($recesi['esito'], $con);
            $esito = estrai_esito($alu,$con);
            $idesito = $recesi['esito'];
            $votoammissione = $recesi['votoammissione'];
            $creditotot = $recesi['creditotot'];
            $credito = $recesi['credito'];
        }


        $schede->setXY(20, 160);
        $schede->SetFont('Arial', 'B', 10);
        $schede->Multicell(172, 6, "\nATTESTATO\nIn base agli atti d'ufficio ed alle valutazioni dei docenti, l'alunna/o risulta", "TLR", "C");

        $schede->setXY(20, $schede->getY());
        $schede->SetFont('Arial', 'B', 10);

        // $schede->Multicell(172,6,inserisci_new_line($esito),"LR","C");
        $schede->Cell(172, 6, estrai_prima_riga($esito), "LR", 1, "C");
        $schede->setXY(20, $schede->getY());
        $schede->SetFont('Arial', "B", 10);
        $schede->Cell(172, 6, str_replace("|", " ", estrai_seconda_riga($esito)), "LR", 1, "C");

        if ((($livello_scuola == '2' && decodifica_classe_no_spec($classe, $con) == 3) || ($livello_scuola == '3' & decodifica_classe_no_spec($classe, $con) == 8)) & (decodifica_passaggio($idesito, $con)==0))
        {
            $schede->setXY(20, $schede->getY());
            $schede->SetFont('Arial', 'B', 10);
            $schede->Cell(172, 6, converti_utf8("con giudizio di idoneità di " . $votoammissione . "/10"), "LR", 1, "C");
        }
        elseif (($livello_scuola == '4') && (decodifica_classe_no_spec($classe, $con) == 5) && (decodifica_passaggio($idesito, $con)==0))
        {
            $schede->setXY(20, $schede->getY());
            $schede->SetFont('Arial', 'B', 10);
            $schede->Cell(172, 6, converti_utf8("con credito scolastico di " . $credito . " (totale: " . $creditotot . ")"), "LR", 1, "C");
        }
        elseif (($livello_scuola == '4') && (decodifica_classe_no_spec($classe, $con) == 4 || decodifica_classe_no_spec($classe, $con) == 3) && (decodifica_passaggio($idesito, $con)==0))
        {
            $schede->setXY(20, $schede->getY());
            $schede->SetFont('Arial', 'B', 10);
            $schede->Cell(172, 6, converti_utf8("con credito scolastico di " . $credito . " (totale: " . $creditotot . ")"), "LR", 1, "C");
        }

        $schede->setXY(20, $schede->getY());
        $schede->Cell(172, 6, "", "LRB", 1, "C");

        // STAMPA PARTE TERMINALE

        $luogodata = converti_utf8("$comune_scuola, $datastampa");
        $schede->setXY(20, 230);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(70, 5, $luogodata, "", 0, "L");

        $schede->setXY(140, 240);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(40, 5, $firmadirigente, "B", 0, "C");

        $dicituradirigente = "Il Dirigente Scolastico";
        $schede->setXY(140, 245);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(40, 3, $dicituradirigente, "", 0, "C");

        if ($_SESSION['suffisso'] != "") $suff = $_SESSION['suffisso'] . "/";
        else $suff = "";
        $schede->setXY(130, 250);
        $schede->Image('../abc/' . $suff . 'firmadirigente.png');
        $schede->setXY(90, 215);
        $schede->Image('../abc/' . $suff . 'timbro.png');


        /*
                $schede->setXY(20, $schede->getY());

                $schede->SetFont('Arial', 'B', 10);
                $schede->Multicell(172, 6, converti_utf8("\nIl dirigente scolastico\n" . $firmadirigente . "\n\n\n\n\n\n"), "", "C");

                $schede->setXY(68, $schede->getY() - 25);
                if ($_SESSION['suffisso'] != "") $suff = $_SESSION['suffisso'] . "/";
                else $suff = "";
                if (estrai_dirigente($con) == $firmadirigente)
                {
                    $schede->Image('../abc/' . $suff . 'firmadirigente.png');
                }
                $schede->setXY(140, $schede->getY() - 25);
                $schede->Image('../abc/' . $suff . 'timbro.png');
        */

        /*
         * SECONDA PAGINA
         */
        $schede->AddPage();

        $schede->SetFont('Arial', 'B', 10);
        $schede->setXY(20, 15);
        $schede->Cell(172, 6, converti_utf8("$nome_scuola"), NULL, 1, "C");

        $schede->SetFont('Arial', 'BI', 9);
        $schede->setXY(20, 20);
        $schede->Cell(172, 6, converti_utf8("$comune_scuola"), NULL, 1, "C");

        $schede->SetFont('Arial', 'BI', 9);
        $schede->setXY(20, 25);
        $specplesso = converti_utf8($plesso_specializzazione . ": " . decodifica_classe_spec($classe, $con));
        $schede->Cell(172, 6, $specplesso, NULL, 1, "C");

        $schede->SetFont('Arial', 'B', 10);
        /*  if ($numeroperiodi==3)
             $per="trimestre";
          else
             $per="quadrimestre";
          $per=converti_utf8($per); */
        $annoscolastico = $annoscol . "/" . ($annoscol + 1);
        $schede->setXY(20, 32);
        $schede->Cell(172, 6, "SCHEDA DI VALUTAZIONE FINALE - A.S. $annoscolastico", NULL, 1, "C");


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

        // CLASSE
        $schede->SetFont('Arial', '', 8);
        $schede->setXY(20, 40);
        $schede->Cell(20, 6, "Classe: ", 0);

        $schede->SetFont('Arial', 'BI', 8);
        //$schede->setXY(220,58);
        $schede->Cell(20, 6, decodifica_classe_no_spec($classe, $con, 1), 1);
        // COGNOME NOME ALUNNO

        $schede->SetFont('Arial', '', 8);
        $schede->Cell(25, 6, "Alunno: ", 0);

        $schede->SetFont('Arial', 'BI', 8);
        $schede->Cell(107, 6, converti_utf8(decodifica_alunno($alu, $con)), 1, 1);

        // DATA NASCITA
        $schede->SetFont('Arial', '', 8);
        $schede->setXY(20, 46);
        $schede->Cell(20, 6, "Data nascita: ", 0);

        $schede->SetFont('Arial', 'BI', 8);
        $schede->Cell(20, 6, $datanascita, 1);

        // COMUNE DI NASCITA
        $posX = 20;
        $schede->setXY($posX, 52);
        if (trim($denominazione) != "" & $denominazione != "NON DEFINITO")
        {
            $schede->SetFont('Arial', '', 8);
            $schede->Cell(20, 6, "Com. nasc.: ", 0);

            $schede->SetFont('Arial', 'BI', 8);
            $schede->Cell(66, 6, $denominazione, 1, 1);
            $posX += 86;
        }
        // COMUNE DI NASCITA
        $schede->setXY($posX, 52);
        if (trim($codfiscale) != "")
        {

            $schede->SetFont('Arial', '', 8);
            $schede->Cell(20, 6, "Cod. fisc.: ", 0);

            $schede->SetFont('Arial', 'BI', 8);
            $schede->Cell(66, 6, $codfiscale, 1, 1);
        }

        $posY = 60;
        $schede->setY($posY);
        $schede->SetFont('Arial', 'BI', 12);
        $schede->Cell(190, 8, "VALUTAZIONI FINALI", NULL, 1, "C");
        $posY += 7;
        $schede->setY($posY);
        $query = "SELECT distinct tbl_materie.idmateria,sigla,denominazione,tipovalutazione FROM tbl_cattnosupp,tbl_materie
           WHERE tbl_cattnosupp.idmateria=tbl_materie.idmateria
           and tbl_cattnosupp.idclasse=$classe
           and tbl_cattnosupp.iddocente <> 1000000000
           and tbl_materie.progrpag < 99
           order by tbl_materie.progrpag,tbl_materie.sigla";
        $rismat = mysqli_query($con, inspref($query));
        while ($valmat = mysqli_fetch_array($rismat))

        {
            $denom = converti_utf8($valmat['denominazione']);

            $idmateria = $valmat['idmateria'];
            $scritto = "";
            $orale = "";
            $pratico = "";
            $unico = "";
            $assenze = "";
            $annotazioni = "";
            $query = "SELECT votounico,assenze,note FROM tbl_valutazionifinali
              WHERE idalunno=$alu
              AND periodo='$numeroperiodi'
              AND idmateria=$idmateria";

            $risvoti = mysqli_query($con, inspref($query));

            if ($recvoti = mysqli_fetch_array($risvoti))
            {
                $unico = dec_to_pag($recvoti['votounico']);
                $assenze = $recvoti['assenze'];
                $annotazioni = converti_utf8($recvoti['note']);
            }
            $schede->SetFont('Arial', 'B', 7);
            $schede->Cell(55, 6, "$denom", 0);  // TTTT
            $valutazione = "";

            if ($unico != "")
            {
                $valutazione = $unico;
            }
            $schede->SetFont('Arial', 'BI', 7);
            $schede->Cell(35, 6, $valutazione, 1, 0, 'C');

            // SE PREVISTA LA STAMPA DEI GIUDIZI STAMPO LE ANNOTAZIONI ALTRIMENTI STAMPO UNA CELLA VUOTA
            if ($livello_scuola == '1' | $livello_scuola == '2' | $livello_scuola == '3')
            {
                $schede->SetFont('Arial', '', 7);
                $y = $schede->GetY();
                $schede->Multicell(100, 3, elimina_cr($annotazioni), 0, 1);
                if ($schede->GetY() < ($y + 6))
                {
                    $schede->SetY($y + 6);
                }
            }
            else
            {
                $schede->SetFont('Arial', '', 7);
                $y = $schede->GetY();
                $schede->Cell(100, 6, "Ore assenza: " . $assenze, 0, 1);

            }


        }
        // AGGIUNGO IL VOTO DI COMPORTAMENTO
        $query = "SELECT denominazione,votounico,note FROM tbl_valutazionifinali,tbl_materie
              WHERE tbl_valutazionifinali.idmateria=tbl_materie.idmateria 
              AND idalunno=$alu
              AND periodo='$numeroperiodi'
              AND tbl_valutazionifinali.idmateria=-1
              ORDER BY denominazione";
        $risvoti = mysqli_query($con, inspref($query));

        if ($recvoti = mysqli_fetch_array($risvoti))
        {
            $denom = $recvoti['denominazione'];
            $unico = dec_to_pag($recvoti['votounico']);
            $annotazioni = converti_utf8($recvoti['note']);
            $schede->SetFont('Arial', 'B', 7);
            $schede->Cell(55, 6, "$denom", 0);
            $valutazione = "";

            if ($unico != "")
            {
                $valutazione = $unico;
            }
            $schede->SetFont('Arial', 'BI', 7);
            $schede->Cell(35, 6, $valutazione, 1, 0, 'C');
            // SE PREVISTA LA STAMPA DEI GIUDIZI STAMPO LE ANNOTAZIONI ALTRIMENTI STAMPO UNA CELLA VUOTA
            if ($giudizisuscheda == 'yes')
            {
                $schede->SetFont('Arial', '', 7);
                $y = $schede->GetY();
                $schede->Multicell(100, 3, elimina_cr($annotazioni), 0, 1);
                if ($schede->GetY() < ($y + 6))
                {
                    $schede->SetY($y + 6);
                }
            }
            else
            {
                $schede->Multicell(100, 6, "", 0, 1);
            }
        }

        // ESTRAGGO IL NUMERO DI ASSENZE


        if ($gioass=='yes')
        {
            $query = "select count(*) as numassenze from tbl_assenze where idalunno=$alu";
            $risasse = mysqli_query($con, inspref($query));

            if ($recasse = mysqli_fetch_array($risasse))
            {
                $numasse = $recasse['numassenze'];

                $schede->SetFont('Arial', 'B', 7);
                $schede->Cell(55, 6, "Assenze totali", 0);
                $schede->SetFont('Arial', 'BI', 8);
                $schede->Cell(35, 6, $numasse, 1, 1, 'C');
            }
        }
        // ESTRAGGO IL GIUDIZIO DISCORSIVO SE PREVISTO
        if ($giudizisuscheda == "yes")
        {
            $query = "SELECT giudizio from tbl_giudizi
						WHERE idalunno=$alu
						AND periodo='$numeroperiodi'";
            $risgiud = mysqli_query($con, inspref($query));
            if ($recgiud = mysqli_fetch_array($risgiud))
            {

                $giudizio = converti_utf8($recgiud['giudizio']);
                $giudizio = trim($giudizio);
                if (strlen(trim($giudizio)) != 0)
                {
                    $schede->SetFont('Arial', 'BI', 8);
                    $schede->Cell(190, 8, "GIUDIZIO", NULL, 1, "C");
                    $schede->SetFont('Arial', '', 7);
                    $schede->Multicell(190, 4, $giudizio, 1, 1);
                }

            }
            // ESITO
        }
        else
        {
            // ESITO, CREDITO SCOLASTICO
        }

        // STAMPA PARTE TERMINALE

        $luogodata = converti_utf8("$comune_scuola, $datastampa");
        $schede->setXY(20, 240);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(70, 5, $luogodata, "", 0, "L");

        $schede->setXY(140, 250);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(40, 5, $firmadirigente, "B", 0, "C");

        $dicituradirigente = "Il Dirigente Scolastico";
        $schede->setXY(140, 255);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(40, 3, $dicituradirigente, "", 0, "C");

        if ($_SESSION['suffisso'] != "") $suff = $_SESSION['suffisso'] . "/";
        else $suff = "";
        $schede->setXY(130, 260);
        $schede->Image('../abc/' . $suff . 'firmadirigente.png');
        $schede->setXY(90, 225);
        $schede->Image('../abc/' . $suff . 'timbro.png');


    }


    $nomefile = "pagelle_" . decodifica_classe($classe, $con) . "_" . $periodo . ".pdf";
    $nomefile = str_replace(" ", "_", $nomefile);

    $schede->Output($nomefile,"I");

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


