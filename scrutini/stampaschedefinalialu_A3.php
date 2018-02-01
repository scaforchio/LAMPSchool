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
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

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
$periodo = stringa_html('periodo');
//$periodo=2;
$alunni = array();
if ($idclasse != "")
{
    if ($periodo == '9')
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

mysqli_close($con);
stampa_schede($alunni, $periodo, $idclasse, $firmadirigente, $datastampa);

function stampa_schede($alunni, $periodo, $classe, $firmadirigente, $datastampa)
{
    @require("../php-ini" . $_SESSION['suffisso'] . ".php");
    require_once("../lib/fpdf/fpdf.php");


    $schede = new FPDF('L', 'mm', 'A3');
    $schede->AddFont('palacescript', '', 'palacescript.php'); // Font del Ministero
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

    $datascrutinio = data_italiana(estrai_datascrutinio($classe, $periodo, $con));

    foreach ($alunni as $alu)
    {
        $schede->AddPage();

        // PRIMA PAGINA
        // $schede->setX(300);
        // $schede->setY(20);
        $schede->Image('../immagini/repubblica.png', 296, 20, 13, 15);
        //$schede->Image('../immagini/miur.png',35,NULL,120,10);
        $schede->SetFont('palacescript', '', 32);
        $schede->setXY(220, 40);
        $ministero = converti_utf8("Ministero dell’Istruzione, dell’ Università e della Ricerca");
        $schede->Cell(172, 8, $ministero, NULL, 1, "C");
        //$schede->SetFont('Times','B',10);


        $schede->SetFont('Arial', 'B', 10);
        $schede->setXY(220, 60);
        $schede->Cell(172, 6, converti_utf8("$nome_scuola"), NULL, 1, "C");

        $schede->SetFont('Arial', 'BI', 9);
        $schede->setXY(220, 66);
        $schede->Cell(172, 6, converti_utf8("$comune_scuola"), NULL, 1, "C");

        $schede->SetFont('Arial', 'BI', 9);
        $schede->setXY(220, 72);
        $specplesso = converti_utf8($plesso_specializzazione . ": " . decodifica_classe_spec($classe, $con));
        $schede->Cell(172, 6, $specplesso, NULL, 1, "C");

        $schede->SetFont('Arial', 'B', 10);

        $annoscolastico = $annoscol . "/" . ($annoscol + 1);
        $schede->setXY(220, 82);
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
        $schede->setXY(220, 100);
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
        $schede->setXY(220, 110);
        $schede->Cell(20, 6, "Data nascita: ", 0);

        $schede->SetFont('Arial', 'BI', 8);
        $schede->Cell(20, 6, $datanascita, 1);
        // COMUNE DI NASCITA
        $posX = 220;
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

        $query = "select * from tbl_esiti where idalunno='$alu'";

        $risesi = mysqli_query($con, inspref($query));

        if ($recesi = mysqli_fetch_array($risesi))
        {
            // $esito = decodifica_esito($recesi['esito'], $con);
            $esito = converti_utf8(estrai_esito($alu, $con));
            $idesito = $recesi['esito'];
            $votoammissione = $recesi['votoammissione'];
        }


        $schede->setXY(220, 160);
        $schede->SetFont('Arial', 'B', 10);
        $valanno = validita_anno($alu, $con);
        $schede->Multicell(172, 6, converti_utf8("\nATTESTATO"), "TLR", "C");
        /*  if ($livello_scuola=='2' | $livello_scuola=='4' | ($livello_scuola=='3' & decodifica_classe_no_spec($classe, $con) >5))
          {
          $schede->setXY(220, $schede->getY());
          $schede->SetFont('Arial', '', 8);
          if ($valanno == '1')
          {
          $schede->Multicell(172, 6, converti_utf8("Visti gli atti d’ufficio e accertato che l’alunno/a, ai fini della validità dell’anno scolastico (comma 1, art. 11D.L. 12/02/2004 n. 59).\nHA FREQUENTATO LE LEZIONI E LE ATTIVITA’ DIDATTICHE per almeno i tre quarti dell’orario personale previsto"), "LR", "C");
          }
          if ($valanno == '2')
          {
          $schede->Multicell(172, 6, converti_utf8("Visti gli atti d’ufficio e accertato che l’alunno/a, ai fini della validità dell’anno scolastico (comma 1, art. 11D.L. 12/02/2004 n. 59).\nNON HA FREQUENTATO LE LEZIONI E LE ATTIVITA’ DIDATTICHE per almeno i tre quarti dell’orario personale previsto\nma ha usufruito della deroga"), "LR", "C");
          }
          if ($valanno == '3')
          {
          $schede->Multicell(172, 6, converti_utf8("Visti gli atti d’ufficio e accertato che l’alunno/a, ai fini della validità dell’anno scolastico (comma 1, art. 11D.L. 12/02/2004 n. 59).\nNON HA FREQUENTATO LE LEZIONI E LE ATTIVITA’ DIDATTICHE per almeno i tre quarti dell’orario personale previsto"), "LR", "C");
          }
          }
         */

        if ($valanno == '1' | $valanno == '2')
        {
            $schede->setXY(220, $schede->getY());
            $schede->SetFont('Arial', 'B', 10);
            $der = "";
            if ($valanno == 2)
                $der = " tenuto conto delle deroghe,";

            $schede->Multicell(172, 6, converti_utf8("\nAccertata, ai fini della validità dell’anno scolastico (comma 1, art. 11D.L. 12/02/2004 n. 59),\nla frequenza delle lezioni e delle attività didattiche\nper almeno i tre quarti dell’orario personale previsto,$der\nl'alunno/a, in base agli atti d'ufficio e alle valutazioni dei docenti,  risulta"), "LR", "C");
            // $schede->Multicell(172,6,inserisci_new_line($esito),"LR","C");
            $schede->setXY(220, $schede->getY());
            $schede->Cell(172, 6, estrai_prima_riga($esito), "LR", 1, "C");
            $schede->setXY(220, $schede->getY());
            $schede->SetFont('Arial', 'B', 10);
            $schede->Cell(172, 6, str_replace("|", " ", estrai_seconda_riga($esito)), "LR", 1, "C");

            if ((($livello_scuola == '2' & decodifica_classe_no_spec($classe, $con) == 3) | ($livello_scuola == '3' & decodifica_classe_no_spec($classe, $con) == 8)) & (decodifica_passaggio($idesito, $con) == 0))
            {
                $schede->setXY(220, $schede->getY());
                $schede->SetFont('Arial', 'B', 10);
                $schede->Cell(172, 6, converti_utf8("con giudizio di idoneità di " . $votoammissione . "/10"), "LR", 1, "C");
            }
        }
        else
        {
            $schede->setXY(220, $schede->getY());
            $schede->SetFont('Arial', 'B', 10);
            $schede->Multicell(172, 6, converti_utf8("\nAccertata, ai fini della validità dell’anno scolastico (comma 1, art. 11D.L. 12/02/2004 n. 59),\nla mancata frequenza delle lezioni e delle attività didattiche\nper almeno i tre quarti dell’orario personale previsto,\nnon si procede allo scrutinio dell'alunno"), "LR", "C");
            // $schede->Multicell(172,6,inserisci_new_line($esito),"LR","C");
            $schede->setXY(220, $schede->getY());
            $schede->Cell(172, 6, "", "LR", 1, "C");
            $schede->setXY(220, $schede->getY());
            $schede->SetFont('Arial', 'B', 10);
            $schede->Cell(172, 6, "", "LR", 1, "C");
        }

        $schede->setXY(220, $schede->getY());
        $schede->SetFont('Arial', 'B', 10);
        $schede->Multicell(172, 6, converti_utf8("\nIl dirigente scolastico\n" . $firmadirigente . "\n\n\n\n\n\n"), "BLR", "C");

        $schede->setXY(278, $schede->getY() - 25);
        if ($_SESSION['suffisso'] != "")
        {
            $suff = $_SESSION['suffisso'] . "/";
        }
        else
            $suff = "";
        $schede->Image('../abc/' . $suff . 'firmadirigente.png');
        $schede->setXY(350, $schede->getY() - 25);
        $schede->Image('../abc/' . $suff . 'timbro.png');


        /*
         * PAGINA 4
         */

        // GIUDIZIO GLOBALE PRIMO QUADRIMESTRE
        $schede->SetFont('Arial', 'BI', 12);
        $schede->setXY(23, 24);
        $schede->Cell(172, 8, "Giudizio globale del primo quadrimestre", 1, 1, "C");
        $giudizio = "";
        $query = "SELECT giudizio from tbl_giudizi
						WHERE idalunno=$alu
						AND periodo='1'";
        $risgiud = mysqli_query($con, inspref($query));
        if ($recgiud = mysqli_fetch_array($risgiud))
        {

            $giudizio = converti_utf8($recgiud['giudizio']);
            $giudizio = trim($giudizio);
        }
        $schede->SetFont('Arial', '', 7);
        $schede->setXY(23, 32);
        $schede->Multicell(172, 4, $giudizio . "\n", 1);

        // NUMERO ASSENZE PRIMO QUADRIMESTRE

        $perioquery = "and true";
        $perioquery = " and data <= '" . $fineprimo . "'";

        $numasse = 0;
        $query = "select count(*) as numassenze from tbl_assenze where idalunno=$alu $perioquery";
        $risasse = mysqli_query($con, inspref($query));

        if ($recasse = mysqli_fetch_array($risasse))
        {
            $numasse = $recasse['numassenze'];
        }


        $schede->setXY(23, $schede->getY());
        $schede->SetFont('Arial', 'BI', 8);
        $schede->Cell(172, 6, "Numero assenze primo quadrimestre: " . $numasse, 1, 1);


        // GIUDIZIO GLOBALE FINALE
        $schede->SetFont('Arial', 'BI', 12);
        $schede->setXY(23, $schede->getY() + 12);
        $schede->Cell(172, 8, "Giudizio globale finale", 1, 1, "C");
        $giudizio = "";
        $query = "SELECT giudizio from tbl_giudizi
						WHERE idalunno=$alu
						AND periodo='$numeroperiodi'";
        $risgiud = mysqli_query($con, inspref($query));
        if ($recgiud = mysqli_fetch_array($risgiud))
        {

            $giudizio = converti_utf8($recgiud['giudizio']);
            $giudizio = trim($giudizio);
        }
        $schede->SetFont('Arial', '', 12);
        $schede->setXY(23, $schede->getY());
        $schede->Multicell(172, 4, $giudizio . "\n", 1);

        // NUMERO ASSENZE TOTALI

        $numasse = 0;
        $query = "select count(*) as numassenze from tbl_assenze where idalunno=$alu";
        $risasse = mysqli_query($con, inspref($query));

        if ($recasse = mysqli_fetch_array($risasse))
        {
            $numasse = $recasse['numassenze'];
        }


        $schede->setXY(23, $schede->getY());
        $schede->SetFont('Arial', 'BI', 8);
        $schede->Cell(172, 6, "Numero assenze totali: " . $numasse, 1, 1);

        // FIRME, LUOGO E DATA SCRUTINIO
        // FIRME

        $posY = $schede->getY() + 10;
        $schede->SetXY(23, $posY);
        $schede->Cell(60, 8, "Firme dei genitori o di chi ne fa le veci", 0, 0, 'C');
        $schede->SetXY(90, $posY);
        $schede->Cell(110, 8, "Coordinatore o docenti equipe pedagogica", 0, 0, 'C');
        $posY += 8;
        $schede->SetXY(23, $posY);
        $schede->Cell(60, 8, "____________________________", 0, 0, 'C');
        $schede->SetXY(90, $posY);
        $schede->Cell(55, 8, "____________________________", 0, 0, 'C');
        $schede->SetXY(145, $posY);
        $schede->Cell(55, 8, "____________________________", 0, 1, 'C');

        $posY += 8;
        $schede->SetXY(23, $posY);
        $schede->Cell(60, 8, "____________________________", 0, 0, 'C');
        $schede->SetXY(90, $posY);
        $schede->Cell(55, 8, "____________________________", 0, 0, 'C');
        $schede->SetXY(145, $posY);
        $schede->Cell(55, 8, "____________________________", 0, 1, 'C');

        $posY += 8;
        $schede->SetXY(90, $posY);
        $schede->Cell(55, 8, "____________________________", 0, 0, 'C');
        $schede->SetXY(145, $posY);
        $schede->Cell(55, 8, "____________________________", 0, 1, 'C');
        $posY += 8;
        $schede->SetXY(90, $posY);
        $schede->Cell(55, 8, "____________________________", 0, 0, 'C');
        $schede->SetXY(145, $posY);
        $schede->Cell(55, 8, "____________________________", 0, 1, 'C');


        // LUOGO E DATA
        // $datastampa = date("d/m/Y");
        //print $datastampa;
        $luogodata = converti_utf8("$comune_scuola, $datastampa");
        $schede->SetXY(23, $schede->getY() + 10);
        $schede->Cell(95, 8, $luogodata, 0, 1, 'L');


        /*
         * PAGINE INTERNE
         */
        $schede->AddPage();


        // INTESTAZIONI

        $schede->SetFont('Arial', 'BI', 8);
        $schede->setXY(23, 15);
        $schede->Cell(172, 8, "VALUTAZIONI PERIODICHE", 1, NULL, "C");
        $schede->setXY(23, 23);
        $schede->Cell(100, 6, "Disciplina", 1, NULL, "C");
        $schede->setXY(123, 23);
        $schede->Cell(36, 6, converti_utf8("1° quadrimestre"), 1, NULL, "C");
        $schede->setXY(159, 23);
        $schede->Cell(36, 6, converti_utf8("2° quadrimestre"), 1, NULL, "C");


        $schede->SetFont('Arial', 'BI', 8);
        $schede->setXY(220, 15);
        $schede->Cell(172, 8, "VALUTAZIONI PERIODICHE", 1, NULL, "C");
        $schede->setXY(220, 23);
        $schede->Cell(100, 6, "Disciplina", 1, NULL, "C");
        $schede->setXY(320, 23);
        $schede->Cell(36, 6, converti_utf8("1° quadrimestre"), 1, NULL, "C");
        $schede->setXY(356, 23);
        $schede->Cell(36, 6, converti_utf8("2° quadrimestre"), 1, NULL, "C");

        $posInizX = 23;
        $posInizY = 30;
        $nummat = 0;

        /* $query = "SELECT distinct tbl_materie.idmateria,sigla,denominazione,tipovalutazione FROM tbl_cattnosupp,tbl_materie
          WHERE tbl_cattnosupp.idmateria=tbl_materie.idmateria
          and tbl_cattnosupp.idclasse=$classe
          and tbl_cattnosupp.iddocente <> 1000000000
          and tbl_materie.progrpag < 99
          order by tbl_materie.progrpag,tbl_materie.sigla"; */
        $query = "SELECT votounico,assenze,note,denominazione,tbl_valutazionifinali.idmateria FROM tbl_valutazionifinali,tbl_materie
                          WHERE tbl_valutazionifinali.idmateria=tbl_materie.idmateria
                          AND idalunno=$alu
                          AND periodo='$numeroperiodi'
                          AND tbl_materie.progrpag<99 AND tbl_materie.idmateria>0
                          ORDER BY tbl_materie.progrpag, denominazione";
        $rismat = mysqli_query($con, inspref($query));
        while ($valmat = mysqli_fetch_array($rismat))
        {
            $nummat++;
            $posY = $posInizY + 33 * ($nummat - 1);
            $posX = $posInizX;
            if ($nummat > 7)
            {
                //   $posY = $posY - 23 * 10; // -$posInizY;
                $posY = $posInizY + 33 * ($nummat - 8);
                $posX = 220;
            }


            $denom = converti_utf8($valmat['denominazione']);

            $idmateria = $valmat['idmateria'];

            $unico1 = "";
            $assenze1 = "";
            $annotazioni1 = "";
            $unico2 = "";
            $assenze2 = "";
            $annotazioni2 = "";

            $query = "SELECT votounico,assenze,note FROM tbl_valutazionifinali
              WHERE idalunno=$alu
              AND periodo='1'
              AND idmateria=$idmateria";
            $risvoti = mysqli_query($con, inspref($query));
            if ($recvoti = mysqli_fetch_array($risvoti))
            {
                $unico1 = dec_to_pag($recvoti['votounico']);
                $assenze1 = $recvoti['assenze'];
                $annotazioni1 = converti_utf8($recvoti['note']);
            }

            $query = "SELECT votounico,assenze,note FROM tbl_valutazionifinali
              WHERE idalunno=$alu
              AND periodo='$numeroperiodi'
              AND idmateria=$idmateria";
            $risvoti = mysqli_query($con, inspref($query));
            if ($recvoti = mysqli_fetch_array($risvoti))
            {
                $unico2 = dec_to_pag($recvoti['votounico']);
                $assenze2 = $recvoti['assenze'];
                $annotazioni2 = converti_utf8($recvoti['note']);
            }

            stampa_materia($schede, $posX, $posY, $denom, $unico1, $unico2, $annotazioni1, $annotazioni2);
        }
        for ($i = $nummat + 1; $i < 14; $i++)
        {
            if ($i > 7)
            {
                $nm = $i - 7;
            }
            else
            {
                $nm = $i;
            }
            $posY = $posInizY + 33 * ($nm - 1);


            $posX = $posInizX;
            if ($i > 7)
            {
                //   $posY = $posY - 23 * 10; // -$posInizY;

                $posX = 220;
            }

            $denom = "";


            $unico1 = "";
            $assenze1 = "";
            $annotazioni1 = "";
            $unico2 = "";
            $assenze2 = "";
            $annotazioni2 = "";


            stampa_materia($schede, $posX, $posY, $denom, $unico1, $unico2, $annotazioni1, $annotazioni2);
        }

        // AGGIUNGO IL VOTO DI COMPORTAMENTO


        $posY = $posInizY + 33 * 6; // -$posInizY;
        $posX = 220;
        $unico1 = "";
        $annotazioni1 = "";
        $unico2 = "";
        $annotazioni2 = "";
        $denom = "";

        $query = "SELECT denominazione,votounico,note FROM tbl_valutazionifinali,tbl_materie
              WHERE tbl_valutazionifinali.idmateria=tbl_materie.idmateria 
              AND idalunno=$alu
              AND periodo='1'
              AND tbl_valutazionifinali.idmateria=-1";
        $risvoti = mysqli_query($con, inspref($query));

        if ($recvoti = mysqli_fetch_array($risvoti))
        {
            $denom = $recvoti['denominazione'];
            $unico1 = dec_to_pag($recvoti['votounico']);
            $annotazioni1 = converti_utf8($recvoti['note']);
        }
        $query = "SELECT denominazione,votounico,note FROM tbl_valutazionifinali,tbl_materie
              WHERE tbl_valutazionifinali.idmateria=tbl_materie.idmateria 
              AND idalunno=$alu
              AND periodo='$numeroperiodi'
              AND tbl_valutazionifinali.idmateria=-1";
        $risvoti = mysqli_query($con, inspref($query));

        if ($recvoti = mysqli_fetch_array($risvoti))
        {
            $denom = $recvoti['denominazione'];
            $unico2 = dec_to_pag($recvoti['votounico']);
            $annotazioni2 = converti_utf8($recvoti['note']);
        }

        stampa_materia($schede, $posX, $posY, $denom, $unico1, $unico2, $annotazioni1, $annotazioni2);
    }


    if (count($alunni) > 1)
    {
        $nomefile = "pagelle_" . decodifica_classe($classe, $con) . "_F.pdf";
        $nomefile = str_replace(" ", "_", $nomefile);
    }
    else
    {
        $nomefile = "pagella_" . decodifica_alunno($alunni[0], $con) ."_".$codfiscale. "_F.pdf";
        $nomefile = str_replace(" ", "_", $nomefile);
    }
    $schede->Output($nomefile, "I");


    mysqli_close($con);
}

function stampa_materia($schede, $posX, $posY, $denom, $unico1, $unico2, $annotazioni1, $annotazioni2)
{

    $schede->setXY($posX, $posY);
    $schede->SetFont('Arial', 'B', 12);
    $schede->Cell(100, 12, $denom, 1, 0, 'C');
    $schede->setXY($posX + 100, $posY);
    $schede->SetFont('Arial', 'B', 12);
    $schede->Cell(36, 12, $unico1, 1, 0, 'C');
    $schede->setXY($posX + 136, $posY);
    $schede->SetFont('Arial', 'B', 12);
    $schede->Cell(36, 12, $unico2, 1, 0, 'C');

    $schede->setXY($posX, $posY + 12);
    $schede->SetFont('Arial', NULL, 7);
    $schede->Cell(172, 10, converti_utf8("ANNOTAZIONI 1° Q.: ") . $annotazioni1, 1, 0, 'L');
    $schede->setXY($posX, $posY + 22);
    $schede->SetFont('Arial', NULL, 7);
    $schede->Cell(172, 10, converti_utf8("ANNOTAZIONI 2° Q.: ") . $annotazioni2, 1, 0, 'L');
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
    return $str1;
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
    return $str2;
}
