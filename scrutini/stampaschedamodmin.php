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

$periodo = stringa_html('periodo');
//$periodo=2;



/* if ($firmadirigente=="")
    $firmadirigente=converti_utf8(restituisci_dirigente($con)); */


$alunni = array();

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
if ($firmadirigente!="" && $datastampa!="")
{
  //  print "tttt $datastampa $firmadirigente $periodo $idclasse";
    aggiorna_data_firma_scrutinio($datastampa, $firmadirigente, $periodo, $idclasse, $con);
}
else
{
    $firmadirigente=estrai_firma_scrutinio($idclasse,$periodo,$con);
    $datastampa=data_italiana(estrai_data_stampa($idclasse,$periodo,$con));
}


stampa_schede($alunni, $periodo, $idclasse, $datastampa, $firmadirigente);

mysqli_close($con);


function stampa_schede($alunni, $periodo, $classe, $datastampa, $firmadirigente)
{
    @require("../php-ini" . $_SESSION['suffisso'] . ".php");
    // require_once("../lib/tfpdf/tfpdf.php");
    require_once("../lib/fpdf/fpdf.php");
    global $schede;
    $schede = new FPDF();
    $schede->AddFont('palacescript', '', 'palacescript.php'); // Font del Ministero
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

  //  $datascrutinio = data_italiana(estrai_datascrutinio($classe, $periodo, $con));
    $nomeclasse = decodifica_classe($classe, $con);
    $annoclasse = substr($nomeclasse, 0, 1);
    if ($livello_scuola != "3")
    {
        $livello = $livello_scuola;
    }
    else
    {
        if ($annoclasse > 5)
        {
            $livello = 2;
        }
        else
        {
            $livello = 1;
        }
    }
    foreach ($alunni as $alu)
    {
        $schede->AddPage();
        stampa_elementi_fissi($schede, 1, "", "", "", "", $annoscol, $datastampa, $livello, $firmadirigente, false);
        $schede->SetFont('Arial', '', 8);
        $schede->setXY(150, 20);
        switch ($livello)
        {
            case 1:
                $modello = "P-QP";
                break;
            case 2:
                $modello = "SP-QP";
                break;
            case 4:
                $modello = "SS-QP";
                break;
        }
        $schede->Cell(30, 6, converti_utf8("Modello $modello"), NULL, 1, "C");

        $schede->Image('../immagini/repubblica.png', 96, 25, 13, 15);
        $schede->SetFont('palacescript', '', 32);
        $schede->setXY(20, 40);
        $ministero = converti_utf8("Ministero dell’Istruzione, dell’ Università e della Ricerca");
        $schede->Cell(172, 8, $ministero, NULL, 1, "C");

        // ISTITUZIONE SCOLASTICA
        $schede->SetFont('Arial', 'B', 8);
        $schede->setXY(20, 60);
        $schede->MultiCell(30, 6, "Istituzione\r\nscolastica", 1, 'C');
        $schede->SetFont('Arial', '', 10);
        $schede->setXY(50, 60);
        $schede->MultiCell(142, 6, converti_utf8($tipologiascuola) . "\r\n" . converti_utf8($comune_scuola . " (" . $provincia . ")"), 1, 'C');

        //TODO: Cambiare il nome parametro da provincia a provinciascuola
        if ($istitutostatale == "yes")
        {
            $statale = "statale";
        }
        else
        {
            $statale = "\r\n";
        }
        switch ($livello)
        {
            case 1:
                $tiposcuola = "\r\nScuola primaria\r\n$statale\r\n\r\n";
                break;
            case 2:
                $tiposcuola = "Scuola secondaria\r\ndi primo grado\r\n$statale\r\n\r\n";
                break;
            case 4:
                $tiposcuola = "\r\nScuola secondaria\r\ndi secondo grado\r\n$statale\r\n \r\n";
                break;
        }
        $schede->SetFont('Arial', 'B', 8);
        $schede->setXY(20, 72);
        $schede->MultiCell(30, 5, $tiposcuola, 1, 'C');
        $schede->SetFont('Arial', '', 10);
        $schede->setXY(50, 72);
        $datiscuola = "";
        if ($livello == 4)
        {
            $datiscuola .= decodifica_classe_spec($classe, $con) . "\r\n";
        }
        //   else
        //       $datiscuola.="";
        $datiscuola .= $nomescuola;
        $datiscuola .= "\r\n" . $codicemeccanografico;
        $datiscuola .= "\r\n" . $indirizzoscuola;
        if (isset($capscuola))
           $datiscuola .= "\r\n" .$capscuola." ".$comune_scuola . " ($provincia)";
        else
            $datiscuola .= "\r\n" .$comune_scuola . " ($provincia)";
        $schede->MultiCell(142, 5, converti_utf8($datiscuola), 1, 'C');

        // TESTATA

        if ($livello == 4)
        {
            $testata = "Pagella scolastica";
        }
        else
        {
            $testata = "Documento di valutazione\r\nanno scolastico $annoscol/" . ($annoscol + 1);
        }

        $schede->setXY(20, 100);
        $schede->SetFont('Arial', 'B', 16);
        $schede->Multicell(172, 10, $testata, 0, 'C');

        // DATI ANAGRAFICI

        $schede->setXY(20, 130);
        $schede->SetFont('Arial', 'B', 10);
        $schede->Cell(172, 5, "Dati anagrafici dello studente", "TLR", 0, "C");

        $query = "SELECT * FROM tbl_alunni,tbl_comuni
                WHERE tbl_alunni.idcomnasc=tbl_comuni.idcomune
                AND idalunno=$alu";
        $ris = mysqli_query($con, inspref($query));
        if ($val = mysqli_fetch_array($ris))
        {
            $datanascita = data_italiana($val['datanascita']);
            $codfiscale = $val['codfiscale'];
            if ($val['statoestero'] == 'N')
            {
                $comunenascita = converti_utf8($val['denominazione']);
            }
            else
            {
                $comunenascita = "";
            }
            $cognome = converti_utf8($val['cognome']);
            $nome = converti_utf8($val['nome']);
            if ($val['statoestero'] == 'S')
            {
                $provinciaalunno = converti_utf8($val['denominazione']);
            }
            else
            {
                $provinciaalunno = estrai_sigla_provincia($val['idcomune'],$con);
            }
        }

        // RIGA 1
        $schede->setXY(20, 135);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(2, 5, " ", "L", 0, "C");

        $schede->setXY(22, 135);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(50, 5, $cognome, "B", 0, "L");

        $schede->setXY(77, 135);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(50, 5, $nome, "B", 0, "L");

        $schede->setXY(132, 135);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(40, 5, $codfiscale, "B", 0, "L");

        $schede->setXY(192, 135);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(2, 5, " ", "L", 0, "C");

        // RIGA 2

        $schede->setXY(20, 140);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(2, 3, " ", "L", 0, "C");

        $schede->setXY(22, 140);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(50, 3, "COGNOME", "", 0, "L");

        $schede->setXY(77, 140);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(50, 3, "NOME", "", 0, "L");

        $schede->setXY(132, 140);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(40, 3, "CODICE FISCALE", "", 0, "L");

        $schede->setXY(192, 140);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(2, 3, " ", "L", 0, "C");

        // RIGA 3 - Vuota
        $schede->setXY(20, 143);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(2, 5, " ", "L", 0, "C");

        $schede->setXY(192, 143);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(2, 5, " ", "L", 0, "C");

        // RIGA 4
        $schede->setXY(20, 148);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(2, 5, " ", "L", 0, "C");

        $schede->setXY(22, 148);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(50, 5, $datanascita, "B", 0, "L");

        $schede->setXY(77, 148);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(50, 5, $comunenascita, "B", 0, "L");

        $schede->setXY(132, 148);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(40, 5, $provinciaalunno, "B", 0, "L");

        $schede->setXY(192, 148);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(2, 5, " ", "L", 0, "C");


        // RIGA 5

        $schede->setXY(20, 153);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(2, 3, " ", "L", 0, "C");

        $schede->setXY(22, 153);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(50, 3, "DATA DI NASCITA", "", 0, "L");

        $schede->setXY(77, 153);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(50, 3, "COMUNE DI NASCITA", "", 0, "L");

        $schede->setXY(132, 153);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(40, 3, "PROV. O STATO ESTERO", "", 0, "L");

        $schede->setXY(192, 153);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(2, 3, " ", "L", 0, "C");

        // ULTIMA RIGA
        $schede->setXY(20, 156);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(172, 2, " ", "LBR", 0, "C");

        // SITUAZIONE ALUNNO
        if ($livello == 1 | $livello == 2)
        {
            $schede->setXY(20, 170);
            $schede->SetFont('Arial', 'B', 10);
            $schede->Cell(172, 8, "Iscritto/a alla classe: " . converti_utf8(decodifica_classe($classe, $con,1)), "TLBR", 0, "C");
        }
        else
        {
            //TODO: Aggiungere la gestione dei campi seguenti in inserimentoe modifica alunni

            $numeroregistro = $val['numeroregistro'];
            if ($numeroregistro == "") $numeroregistro = "****";
            $provenienza = $val['provenienza'];
            if ($provenienza == "") $provenienza = "****";
            $titoloammissione = $val['titoloammissione'];
            if ($titoloammissione == "") $titoloammissione = "****";
            if ($val['sequenzaiscrizione'] == 1)
            {
                $voltaiscrizione = "PRIMA";
            }
            if ($val['sequenzaiscrizione'] == 2)
            {
                $voltaiscrizione = "SECONDA";
            }
            if ($val['sequenzaiscrizione'] == 3)
            {
                $voltaiscrizione = "TERZA";
            }


            // LINEA SUPERIORE

            $schede->setXY(20, 170);
            $schede->SetFont('Arial', 'B', 10);
            $schede->Cell(172, 5, "Posizione scolastica dello studente    Anno scolastico $annoscol/" . ($annoscol + 1), "TLR", 0, "C");

            // RIGA 1
            $schede->setXY(20, 175);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(2, 5, " ", "L", 0, "C");

            $schede->setXY(22, 175);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(40, 5, $numeroregistro, "B", 0, "L");

            $schede->setXY(67, 175);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(60, 5, converti_utf8(decodifica_classe($classe, $con)), "B", 0, "L");

            $schede->setXY(132, 175);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(40, 5, $provenienza, "B", 0, "L");

            $schede->setXY(192, 175);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(2, 5, " ", "L", 0, "C");

            // RIGA 2

            $schede->setXY(20, 180);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(2, 3, " ", "L", 0, "C");

            $schede->setXY(22, 180);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(40, 3, "N. REG. GEN.", "", 0, "L");

            $schede->setXY(67, 180);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(60, 3, "CLASSE", "", 0, "L");

            $schede->setXY(132, 180);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(40, 3, "PROVENIENZA", "", 0, "L");

            $schede->setXY(192, 180);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(2, 3, " ", "L", 0, "C");

            // RIGA 3 - Vuota
            $schede->setXY(20, 183);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(2, 5, " ", "L", 0, "C");

            $schede->setXY(192, 183);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(2, 5, " ", "L", 0, "C");

            // RIGA 4
            $schede->setXY(20, 188);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(2, 5, " ", "L", 0, "C");

            $schede->setXY(22, 188);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(50, 5, $titoloammissione, "B", 0, "L");

            $schede->setXY(107, 188);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(50, 5, "Iscritto per la " . $voltaiscrizione . " volta (2)", "", 0, "L");

            $schede->setXY(192, 188);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(2, 5, " ", "L", 0, "C");

            // RIGA 5 (sottoriga di 4)
            $schede->setXY(20, 193);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(2, 3, " ", "L", 0, "C");

            $schede->setXY(22, 193);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(50, 3, "TITOLO DI AMMISSIONE (1)", "", 0, "L");

            $schede->setXY(192, 193);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(2, 3, " ", "L", 0, "C");

            // RIGA 6
            $schede->setXY(20, 196);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(2, 5, " ", "L", 0, "C");

            $schede->setXY(127, 196);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(40, 5, converti_utf8($dsga), "B", 0, "C");

            $schede->setXY(192, 196);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(2, 5, " ", "L", 0, "C");

            // RIGA 7 (sottoriga di 6)
            $schede->setXY(20, 201);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(2, 3, " ", "L", 0, "C");

            $schede->setXY(127, 201);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(40, 3, "Il Dir. Serv. Gen. e Amm. (3)", "", 0, "C");

            $schede->setXY(192, 201);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(2, 3, " ", "L", 0, "C");

            // LINEA INFERIORE

            $schede->setXY(20, 204);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(172, 2, " ", "LBR", 0, "C");


        }


        // ******* FINE PRIMA PAGINA  *********

        // ******* ESTRAZIONE DATI VALUTAZIONI ************

        // CREAZIONE ARRAY
        $idmat = array();
        $denmat = array();

        $votu1 = array();
        $voto1 = array();
        $vots1 = array();
        $votp1 = array();
        $not1 = array();
        $ass1 = array();

        $votu2 = array();
        $not2 = array();
        $ass2 = array();

        if ($livello == 1 | $livello == 2)
        {
            $query = "SELECT votounico,assenze,note,tbl_materie.idmateria,tbl_materie.progrpag,denominazione FROM tbl_valutazionifinali,tbl_materie
                  WHERE tbl_valutazionifinali.idmateria=tbl_materie.idmateria
                  AND periodo='$numeroperiodi'
                  AND tbl_materie.progrpag<99 AND tbl_materie.idmateria>0
                  AND idalunno=$alu
                  ORDER BY tbl_materie.progrpag,denominazione";
            $risvoti = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query, false));
            while ($recvoti = mysqli_fetch_array($risvoti))
            {
                $idmat[] = $recvoti['idmateria'];
                $denmat[] = $recvoti['denominazione'];
                $votu2[] = $recvoti['votounico'];
                $ass2[] = $recvoti['assenze'];
                $not2[] = $recvoti['note'];

                $query = "SELECT votounico,votoorale,votopratico,votoscritto,assenze,note FROM tbl_valutazionifinali
                          WHERE idalunno=$alu
                          AND periodo='1'
                          AND idmateria=" . $recvoti['idmateria'];
                $risvotipre = mysqli_query($con, inspref($query));
                if ($recvotipre = mysqli_fetch_array($risvotipre))
                {
                    $votu1[] = $recvotipre['votounico'];
                    $vots1[] = $recvotipre['votoscritto'];
                    $votp1[] = $recvotipre['votopratico'];
                    $voto1[] = $recvotipre['votoorale'];
                    $ass1[] = $recvotipre['assenze'];
                    $not1[] = $recvotipre['note'];
                }
                else
                {
                    $votu1[] = '';
                    $vots1[] = '';
                    $votp1[] = '';
                    $voto1[] = '';
                    $ass1[] = '';
                    $not1[] = '';
                }


            }

            $nummaterie = mysqli_num_rows($risvoti);
            $numpag = 1;

            for ($i = 0; $i < $nummaterie; $i++)
            {
                // ******* INIZIO PAGINA INTERMEDIA *********
                if ($i % 7 == 0)
                {
                    $schede->AddPage();
                    $numpag++;
                    stampa_elementi_fissi($schede, $numpag, $cognome, $nome, $codfiscale, $codicemeccanografico, $annoscol, $datastampa, $livello, $firmadirigente, true);
                    $posY = 25;

                    $schede->SetXY(20, $posY);
                    $alt = 6;
                    $schede->SetFont("Arial", 'B', 10);
                    $schede->Cell(172, $alt, "VALUTAZIONI PERIODICHE", "TBLR", 0, "C");
                    $posY += $alt;
                }

                $posX = 20;
                $schede->SetXY($posX, $posY);

                $lar = 62;
                $alt = 10;
                $schede->SetFont("Arial", 'B', 10);
                $schede->Cell($lar, $alt, converti_utf8($denmat[$i]), "TBLR", 0, "L");
                $posX += $lar;

                $lar = 55;
                $schede->SetXY($posX, $posY);
                $schede->SetFont("Arial", '', 10);
                $schede->Cell($lar, $alt, converti_utf8("1^ frazione temporale"), "TBLR", 0, "C");
                $posX += $lar;

                $lar = 55;
                $schede->SetXY($posX, $posY);
                $schede->SetFont("Arial", '', 10);
                $schede->Cell($lar, $alt, converti_utf8("2^ frazione temporale"), "TBLR", 0, "C");
                $posY += $alt;


                $posX = 20;
                $schede->SetXY($posX, $posY);

                $lar = 62;
                $alt = 10;
                $schede->SetFont("Arial", '', 10);
                $schede->Cell($lar, $alt, "Voto (in cifre e in lettere)", "TBLR", 0, "L");
                $posX += $lar;

                if ($votu1[$i] < 11 & $votu2[$i] > 0)
                {
                    $vot1nst = $votu1[$i] . " / 10";
                    $vot1lst = dec_to_pag($votu1[$i]) . "/dieci";
                }
                else
                {
                    $vot1nst = "";
                    $vot1lst = dec_to_pag($votu1[$i]);
                }

                if ($votu2[$i] < 11 & $votu2[$i] > 0)
                {
                    $vot2nst = $votu2[$i] . " / 10";
                    $vot2lst = dec_to_pag($votu2[$i]) . "/dieci";
                }
                else
                {
                    $vot2nst = "";
                    $vot2lst = dec_to_pag($votu2[$i]);
                }

                $lar = 20;
                $schede->SetXY($posX, $posY);
                $schede->SetFont("Arial", 'B', 10);
                $schede->Cell($lar, $alt, $vot1nst, "TBLR", 0, "C");
                $posX += $lar;


                $lar = 35;
                $schede->SetXY($posX, $posY);
                $schede->SetFont("Arial", 'B', 10);
                $schede->Cell($lar, $alt, $vot1lst, "TBLR", 0, "C");
                $posX += $lar;

                $lar = 20;
                $schede->SetXY($posX, $posY);
                $schede->SetFont("Arial", 'B', 10);
                $schede->Cell($lar, $alt, $vot2nst, "TBLR", 0, "C");
                $posX += $lar;


                $lar = 35;
                $schede->SetXY($posX, $posY);
                $schede->SetFont("Arial", 'B', 10);
                $schede->Cell($lar, $alt, $vot2lst, "TBLR", 0, "C");
                $posX += $lar;
                $posY += $alt;


            }
/*
            if (($nummaterie % 7) > 5)
            {
                $schede->AddPage();
                $numpag++;
                stampa_elementi_fissi($schede, $numpag, $cognome, $nome, $codfiscale, $codicemeccanografico, $annoscol, $datastampa, $livello, $firmadirigente, true);
                $posY = 25;
            }
*/
            // AGGIUNGO I VOTI DEL COMPORTAMENTO
            $votocomp1 = "";
            $votocomp2 = "";
            $query = "SELECT votounico,note FROM tbl_valutazionifinali
              WHERE idalunno=$alu
              AND periodo='1'
              AND idmateria=-1";
            $risvoti = mysqli_query($con, inspref($query));
            $giudiziocomp1="";
            $votocomp1="";
            if ($recvoti = mysqli_fetch_array($risvoti))
            {
                $votocomp1 = $recvoti['votounico'];
                $giudiziocomp1 = $recvoti['note'];
            }

            $query = "SELECT votounico,note FROM tbl_valutazionifinali
              WHERE idalunno=$alu
              AND periodo='$numeroperiodi'
              AND idmateria=-1";
            $risvoti = mysqli_query($con, inspref($query));

            if ($recvoti = mysqli_fetch_array($risvoti))
            {
                $votocomp2 = $recvoti['votounico'];
                $giudiziocomp2 = $recvoti['note'];
            }
            $posX = 20;
            $schede->SetXY($posX, $posY);

            $lar = 62;
            $alt = 10;
            $schede->SetFont("Arial", 'B', 10);
            $schede->Cell($lar, $alt, "COMPORTAMENTO", "TBLR", 0, "L");
            $posX += $lar;

            $lar = 55;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", 'B', 10);
            $schede->Cell($lar, $alt, converti_utf8("1^ frazione temporale"), "TBLR", 0, "C");
            $posX += $lar;

            $lar = 55;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", 'B', 10);
            $schede->Cell($lar, $alt, converti_utf8("2^ frazione temporale"), "TBLR", 0, "C");
            $posY += $alt;

            if ($livello == 2)
            {
                $posX = 20;
                $schede->SetXY($posX, $posY);

                $lar = 62;
                $alt = 10;
                $schede->SetFont("Arial", 'B', 10);
                $schede->Cell($lar, $alt, "Voto (in cifre e in lettere)", "TBLR", 0, "L");
                $posX += $lar;

                $lar = 20;
                $schede->SetXY($posX, $posY);
                $schede->SetFont("Arial", 'B', 10);
                $schede->Cell($lar, $alt, $votocomp1 . " / 10", "TBLR", 0, "C");
                $posX += $lar;


                $lar = 35;
                $schede->SetXY($posX, $posY);
                $schede->SetFont("Arial", 'B', 10);
                $schede->Cell($lar, $alt, dec_to_pag($votocomp1) . "/dieci", "TBLR", 0, "C");
                $posX += $lar;

                $lar = 20;
                $schede->SetXY($posX, $posY);
                $schede->SetFont("Arial", 'B', 10);
                $schede->Cell($lar, $alt, $votocomp2 . " / 10", "TBLR", 0, "C");
                $posX += $lar;


                $lar = 35;
                $schede->SetXY($posX, $posY);
                $schede->SetFont("Arial", 'B', 10);
                $schede->Cell($lar, $alt, dec_to_pag($votocomp2) . "/dieci", "TBLR", 0, "C");
                $posX += $lar;
                $posY += $alt;
            }

            // AGGIUNGO I GIUDIZI SUL COMPORTAMENTO
            
            if ($livello==1)
            {
                $giudiziocomp1=dec_to_pag($votocomp1)."  ".$giudiziocomp1;
                $giudiziocomp2=dec_to_pag($votocomp2)."  ".$giudiziocomp2;
            }
            $posX = 20;
            $schede->SetXY($posX, $posY);

            $lar = 62;
            $alt = 5;
            $schede->SetFont("Arial", '', 10);
            $schede->Cell($lar, $alt, "Giudizio (2)", "TBLR", 0, "L");
            $posX += $lar;

            $lar = 55;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", 'B', 10);
            $schede->MultiCell($lar, $alt, converti_utf8($giudiziocomp1), "TBLR", "C");
            $posX += $lar;

            $lar = 55;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", 'B', 10);
            $schede->MultiCell($lar, $alt, converti_utf8($giudiziocomp2), "TBLR", "C");
            $posY += $alt;


        }
        else
        {
            $schede->AddPage();

            stampa_elementi_fissi($schede, 2, $cognome, $nome, $codfiscale, $codicemeccanografico, $annoscol, $datastampa, $livello, $firmadirigente, true);
            $posY = 25;
            $posX = 20;
            $schede->SetXY(20, $posY);
            $alt = 20;
            $lar = 72;
            $schede->SetFont("Arial", 'B', 8);
            $schede->Cell($lar, $alt, "\n\nDISCIPLINE", "TBLR", 0, "C");
            $posX += $lar;
            $lar = 100;
            $alt = 10;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", 'B', 8);
            $schede->Cell($lar, $alt, "VALUTAZIONE PERIODICA PRIMA FRAZIONE TEMPORALE", "TBLR", 0, "C");
            $posY += $alt;
            $schede->SetXY($posX, $posY);
            $lar = 22;
            $schede->SetFont("Arial", '', 7);
            $schede->Cell($lar, $alt, "SCRITTO", "TBLR", 0, "C");
            $posX += $lar;
            $lar = 22;
            $schede->SetFont("Arial", '', 7);
            $schede->Cell($lar, $alt, "ORALE", "TBLR", 0, "C");
            $posX += $lar;
            $lar = 22;
            $schede->SetFont("Arial", '', 7);
            $schede->Cell($lar, $alt, "PRATICO", "TBLR", 0, "C");
            $posX += $lar;
            $lar = 22;
            $schede->SetFont("Arial", '', 7);
            $schede->Cell($lar, $alt, "ALTRO", "TBLR", 0, "C");
            $posX += $lar;
            $lar = 12;
            $schede->SetFont("Arial", '', 7);
            $schede->Cell($lar, $alt, "ORE ASS.", "TBLR", 0, "C");
            $posY += $alt;


            $posX += 20;

            $query = "SELECT votounico,votoorale,votopratico,votoscritto,assenze,note,denominazione FROM tbl_valutazionifinali,tbl_materie
                          WHERE tbl_valutazionifinali.idmateria=tbl_materie.idmateria
                          AND idalunno=$alu
                          AND periodo='1'
                          AND tbl_materie.progrpag<99 AND tbl_materie.idmateria>0
                          ORDER BY tbl_materie.progrpag, denominazione";
            $risvoti = mysqli_query($con, inspref($query));
            while ($recvoti = mysqli_fetch_array($risvoti))
            {
                $votu = $recvoti['votounico'];
                if ($votu == 99) $votu = "";
                $vots = $recvoti['votoscritto'];
                if ($vots == 99) $vots = "";
                $votp = $recvoti['votopratico'];
                if ($votp == 99) $votp = "";
                $voto = $recvoti['votoorale'];
                if ($voto == 99) $voto = "";
                $ass = $recvoti['assenze'];
                $not = $recvoti['note'];
                $den = $recvoti['denominazione'];

                $posX = 20;
                $schede->SetXY(20, $posY);
                $alt = 6;
                $lar = 72;
                $schede->SetFont("Arial", '', 8);
                $schede->Cell($lar, $alt, converti_utf8($den), "TBLR", 0, "L");
                $posX += $lar;

                $schede->SetXY($posX, $posY);
                $lar = 8;
                $schede->Cell($lar, $alt, $vots, "TBLR", 0, "C");
                $posX += $lar;
                $schede->SetXY($posX, $posY);
                $lar = 14;
                $schede->Cell($lar, $alt, dec_to_pag($vots), "TBLR", 0, "C");
                $posX += $lar;

                $schede->SetXY($posX, $posY);
                $lar = 8;
                $schede->Cell($lar, $alt, $voto, "TBLR", 0, "C");
                $posX += $lar;
                $schede->SetXY($posX, $posY);
                $lar = 14;
                $schede->Cell($lar, $alt, dec_to_pag($voto), "TBLR", 0, "C");
                $posX += $lar;

                $schede->SetXY($posX, $posY);
                $lar = 8;
                $schede->Cell($lar, $alt, $votp, "TBLR", 0, "C");
                $posX += $lar;
                $schede->SetXY($posX, $posY);
                $lar = 14;
                $schede->Cell($lar, $alt, dec_to_pag($votp), "TBLR", 0, "C");
                $posX += $lar;

                $schede->SetXY($posX, $posY);
                $lar = 8;
                $schede->Cell($lar, $alt, $votu, "TBLR", 0, "C");
                $posX += $lar;
                $schede->SetXY($posX, $posY);
                $lar = 14;
                $schede->Cell($lar, $alt, dec_to_pag($votu), "TBLR", 0, "C");
                $posX += $lar;

                $schede->SetXY($posX, $posY);
                $lar = 12;
                $schede->Cell($lar, $alt, $ass, "TBLR", 0, "C");
                $posY += $alt;

            }

            // AGGIUNGO IL VOTO DEL COMPORTAMENTO

            $query = "SELECT votounico FROM tbl_valutazionifinali
              WHERE idalunno=$alu
              AND periodo='1'
              AND idmateria=-1";
            $risvoti = mysqli_query($con, inspref($query));

            if ($recvoti = mysqli_fetch_array($risvoti))
            {
                $votocomp = $recvoti['votounico'];
            }
            else
            {
                $votocomp = "";
            }

            $posX = 20;
            $schede->SetXY(20, $posY);
            $alt = 6;
            $lar = 72;
            $schede->SetFont("Arial", '', 7);
            $schede->Cell($lar, $alt, "COMPORTAMENTO", "TBLR", 0, "L");
            $posX += $lar;

            $schede->SetXY($posX, $posY);
            $lar = 8;
            $schede->Cell($lar, $alt, "", "TBLR", 0, "C");
            $posX += $lar;
            $schede->SetXY($posX, $posY);
            $lar = 14;
            $schede->Cell($lar, $alt, "", "TBLR", 0, "C");
            $posX += $lar;

            $schede->SetXY($posX, $posY);
            $lar = 8;
            $schede->Cell($lar, $alt, "", "TBLR", 0, "C");
            $posX += $lar;
            $schede->SetXY($posX, $posY);
            $lar = 14;
            $schede->Cell($lar, $alt, "", "TBLR", 0, "C");
            $posX += $lar;

            $schede->SetXY($posX, $posY);
            $lar = 8;
            $schede->Cell($lar, $alt, "", "TBLR", 0, "C");
            $posX += $lar;
            $schede->SetXY($posX, $posY);
            $lar = 14;
            $schede->Cell($lar, $alt, "", "TBLR", 0, "C");
            $posX += $lar;

            $schede->SetXY($posX, $posY);
            $lar = 8;
            $schede->Cell($lar, $alt, $votocomp, "TBLR", 0, "C");
            $posX += $lar;
            $schede->SetXY($posX, $posY);
            $lar = 14;
            $schede->Cell($lar, $alt, dec_to_pag($votocomp), "TBLR", 0, "C");
            $posX += $lar;

            $schede->SetXY($posX, $posY);
            $lar = 12;
            $schede->Cell($lar, $alt, "", "TBLR", 0, "C");
            $posY += $alt;

            $posY += 5;
            $posX = 20;
            $alt = 7;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", 'B', 10);
            $schede->Cell(172, $alt, "ANNOTAZIONI (4)", "TBLR", 0, "C");
            $posY += $alt;
            $query = "select giudizio from tbl_giudizi where periodo='1' and idalunno=$alu";
            $ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query, false));
            $schede->SetFont("Arial", '', 9);
            if ($rec = mysqli_fetch_array($ris))
            {
                $annotaz = $rec['giudizio'];
                $schede->SetXY($posX, $posY);
                $schede->MultiCell(172, 5, $annotaz, "TBLR", "J");
            }
            else
            {
                $annotaz = "========== ========== ========== ==========";
                $schede->SetXY($posX, $posY);
                $schede->MultiCell(172, 5, $annotaz, "TBLR", "C");
            }


            // *****************   SECONDO QUADRIMESTRE TERZA PAGINA **********************
            $schede->AddPage();

            stampa_elementi_fissi($schede, 3, $cognome, $nome, $codfiscale, $codicemeccanografico, $annoscol, $datastampa, $livello, $firmadirigente, true);

            $posY = 25;
            $posX = 20;
            $schede->SetXY(20, $posY);
            $alt = 20;
            $lar = 82;
            $schede->SetFont("Arial", 'B', 8);
            $schede->Cell($lar, $alt, "\n\nDISCIPLINE", "TBLR", 0, "C");
            $posX += $lar;
            $lar = 50;
            $alt = 10;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", 'B', 8);
            $schede->Cell($lar, $alt, "SCRUTINIO FINALE", "TBLR", 0, "C");
            $posY += $alt;
            $schede->SetXY($posX, $posY);

            $lar = 35;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", '', 8);
            $schede->Cell($lar, 5, "VOTO UNICO", "TLR", 0, "C");
            $posY += 5;

            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", '', 8);
            $schede->Cell($lar, 5, "(in lettere)", "BLR", 0, "C");

            $posX += $lar;

            $posY -= 5;
            $lar = 15;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", '', 8);
            $schede->Cell($lar, 5, "Totale ore", "TLR", 0, "C");
            $posY += 5;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", '', 8);
            $schede->Cell($lar, 5, "assenza", "BLR", 0, "C");
            $posY += 5;


            $query = "SELECT votounico,assenze,note,denominazione FROM tbl_valutazionifinali,tbl_materie
                          WHERE tbl_valutazionifinali.idmateria=tbl_materie.idmateria
                          AND idalunno=$alu
                          AND periodo='$numeroperiodi'
                          AND tbl_materie.progrpag<99 AND tbl_materie.idmateria>0
                          ORDER BY tbl_materie.progrpag, denominazione";
            $risvoti = mysqli_query($con, inspref($query));
            $numvoti = 0;
            while ($recvoti = mysqli_fetch_array($risvoti))
            {
                $numvoti++;
                $votu = $recvoti['votounico'];
                if ($votu == 99) $votu = "";

                $ass = $recvoti['assenze'];
                $not = $recvoti['note'];
                $den = $recvoti['denominazione'];

                $posX = 20;
                $schede->SetXY(20, $posY);
                $alt = 6;
                $lar = 82;
                $schede->SetFont("Arial", '', 8);
                $schede->Cell($lar, $alt, converti_utf8($den), "TBLR", 0, "L");
                $posX += $lar;
                $schede->SetFont("Arial", '', 10);
                $schede->SetXY($posX, $posY);
                $lar = 35;
                $schede->Cell($lar, $alt, dec_to_pag($votu), "TBLR", 0, "C");
                $posX += $lar;
                $schede->SetXY($posX, $posY);
                $lar = 15;
                $schede->Cell($lar, $alt, $ass, "TBLR", 0, "C");


                $posY += $alt;

            }

            $righevuote = 19 - $numvoti;
            for ($i = 1; $i <= $righevuote; $i++)
            {
                $posX = 20;
                $lar = 82;
                $alt = 6;
                $schede->SetXY($posX, $posY);
                $schede->Cell($lar, $alt, "", "TBLR", 0, "L");
                $posX += $lar;
                $schede->SetXY($posX, $posY);
                $lar = 35;
                $schede->Cell($lar, $alt, "", "TBLR", 0, "C");
                $posX += $lar;
                $schede->SetXY($posX, $posY);
                $lar = 15;
                $schede->Cell($lar, $alt, "", "TBLR", 0, "C");
                $posY += $alt;


            }
            // AGGIUNGO IL VOTO DEL COMPORTAMENTO

            $query = "SELECT votounico FROM tbl_valutazionifinali
              WHERE idalunno=$alu
              AND periodo='$numeroperiodi'
              AND idmateria=-1";
            $risvoti = mysqli_query($con, inspref($query));

            if ($recvoti = mysqli_fetch_array($risvoti))
            {
                $votocomp = $recvoti['votounico'];

            }

            $posX = 20;
            $schede->SetXY(20, $posY);
            $alt = 6;
            $lar = 82;
            $schede->SetFont("Arial", '', 8);
            $schede->Cell($lar, $alt, "COMPORTAMENTO", "TBLR", 0, "L");
            $posX += $lar;
            $schede->SetFont("Arial", '', 10);
            $schede->SetXY($posX, $posY);
            $lar = 35;
            $schede->Cell($lar, $alt, dec_to_pag($votocomp), "TBLR", 0, "C");
            $posX += $lar;
            $schede->SetXY($posX, $posY);
            $lar = 15;
            $schede->Cell($lar, $alt, "", "TBLR", 0, "C");
            $posY += $alt;

            $posY += 5;
            $posX = 20;
            $alt = 7;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", 'B', 10);
            $schede->Cell(172, $alt, "ANNOTAZIONI (4)", "TBLR", 0, "C");
            $posY += $alt;
            $schede->SetFont("Arial", '', 9);
            $query = "select giudizio from tbl_giudizi where periodo='$numeroperiodi' and idalunno=$alu";
            $ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query, false));
            if ($rec = mysqli_fetch_array($ris))
            {
                $annotaz = $rec['giudizio'];
                $schede->SetXY($posX, $posY);
                $schede->MultiCell(172, 5, $annotaz, "TBLR", "J");
            }
            else
            {
                $annotaz = "========== ========== ========== ==========";
                $schede->SetXY($posX, $posY);
                $schede->MultiCell(172, 5, $annotaz, "TBLR", "C");
            }
            //
            //  PARTE RELATVA ALL'ESAME
            //
            $votoammissione = "";
            $media = "";
            $credito = "";
            $esito = "";
            $decesito = "";
            $query = "SELECT * from tbl_esiti where idalunno=$alu";
            $ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query, false));
            if ($rec = mysqli_fetch_array($ris))
            {
                $votoammissione = $rec['votoammissione'];
                $media = $rec['media'];
                $credito = $rec['credito'];
                $esito = $rec['esito'];
                $decesito = "";
                if ($annoclasse == 5)
                {
                    if (decodifica_passaggio($esito, $con) == 0)
                    {
                        $decesito = "Ammesso";
                    }
                    else
                    {
                        $decesito = "Non ammesso";
                    }
                }


            }

            $posX = 152;
            $posY = 25;
            $schede->SetXY($posX, $posY);
            $alt = 10;
            $schede->Cell(40, $alt, "ESAMI", "TBLR", 0, "C");
            $posY += $alt;
            $schede->SetXY($posX, $posY);
            $alt = 10;
            $lar = 40;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", '', 8);
            $schede->Cell($lar, 5, "VOTO UNICO (6)", "TLR", 0, "C");
            $posY += 5;

            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", '', 8);
            $schede->Cell($lar, 5, "(in lettere)", "BLR", 0, "C");
            $posY += 5;
            $alt = 30;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", 'B', 10);
            $schede->Cell(40, $alt, dec_to_pag($votoammissione), "TBLR", 0, "C");
            $schede->SetFont("Arial", '', 8);
            $posY += $alt;
            $alt = 12;
            $schede->SetXY($posX, $posY);
            $schede->Cell(40, $alt, "CREDITO SCOLASTICO", "TBLR", 0, "C");
            $posY += $alt;
            $alt = 12;
            $schede->SetFont("Arial", '', 7);
            $schede->SetXY($posX, $posY);
            $schede->Cell(40, $alt, "Media dei voti conseguiti", "TLR", 0, "C");
            $posY += 6;
            $schede->SetXY($posX, $posY);
            $schede->Cell(40, $alt, "nello scrutinio finale", "LR", 0, "C");
            $posY += 6;
            $alt = 18;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", 'B', 10);
            $schede->Cell(40, $alt, $media, "LR", 0, "C");
            $schede->SetFont("Arial", '', 8);
            $posY += $alt;
            $alt = 12;
            $schede->SetXY($posX, $posY);
            $schede->Cell(40, $alt, "Credito scolastico attribuito", "LR", 0, "C");
            $posY += 6;
            $schede->SetXY($posX, $posY);
            $schede->Cell(40, $alt, "nell'anno scolastico in corso", "LR", 0, "C");
            $posY += 6;
            $alt = 18;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", 'B', 10);
            $schede->Cell(40, $alt, $credito, "BLR", 0, "C");
            $schede->SetFont("Arial", '', 8);
            $posY += $alt;
            $alt = 18;
            $schede->SetXY($posX, $posY);
            $schede->Cell(40, $alt, $decesito, "BLR", 0, "L");
            $posY += $alt;


        }

// ******* INIZIO QUARTA PAGINA  *********
        $schede->AddPage();

        stampa_elementi_fissi($schede, 4, $cognome, $nome, $codfiscale, $codicemeccanografico, $annoscol, $datastampa, $livello, $firmadirigente);


        if ($livello == 1)
        {
            $posX = 20;
            $posY = 25;
            $schede->SetFont("Arial", 'B', 10);
            $schede->SetXY($posX, $posY);
            $schede->Cell(172, 5, "RILEVAZIONE DEI PROGRESSI NELL'APPRENDIMENTO E NELLO SVILUPPO PERSONALE", "", 0, "C");
            $posY += 5;
            $schede->SetXY($posX, $posY);
            $schede->Cell(172, 5, "E SOCIALE DELL'ALUNNO", "", 0, "C");
            $posY += 10;
            $giu1 = estrai_giudizio($alu, 1, $con);
            $giu2 = estrai_giudizio($alu, $numeroperiodi, $con);
            $schede->SetXY($posX, $posY);
            $schede->Cell(172, 5, "VALUTAZIONE INTERMEDIA", "TBLR", 0, "C");
            $posY += 5;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", '', 9);
            $schede->MultiCell(172, 5, converti_utf8($giu1), 'TBLR', 'J');
            $posY = $schede->GetY();


            $datascrutinio = estrai_datascrutinio($classe, 1, $con);
            $luogodata = converti_utf8("$comune_scuola, " . data_italiana($datascrutinio));

            $schede->setXY(20, $posY);
            $schede->SetFont('Arial', '', 9);
            $schede->Cell(70, 5, $luogodata, "", 0, "L");
            $posY += 10;
            $schede->setXY(20, $posY);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(40, 5, "", "B", 0, "C");
            $schede->setXY(20, $posY + 5);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(40, 3, "Il(i) genitore(i) o chi ne fa le veci", "", 0, "C");


            $schede->setXY(140, $posY);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(40, 5, $firmadirigente, "B", 0, "C");
            $dicituradirigente = "Il Dirigente Scolastico (1)";
            $schede->setXY(140, $posY + 5);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(40, 3, $dicituradirigente, "", 0, "C");
            $posY += 10;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", 'B', 10);
            $schede->Cell(172, 5, "VALUTAZIONE FINALE", "TBLR", 0, "C");
            $posY += 5;
            $schede->SetXY($posX, $posY);
            $schede->SetFont("Arial", '', 9);
            $schede->MultiCell(172, 5, converti_utf8($giu2), 'TBLR', 'J');
            $posY = $schede->GetY();


          //TODO: Verificare modalità registrazione e dare possibilità di cambiare la data dello scrutinio
            $datascrutinio = estrai_data_stampa($classe, $numeroperiodi, $con);
           // print "tttt $datascrutinio $numeroperiodi $classe";
           // $datascrutinio = $datastampa;
            $luogodata = converti_utf8("$comune_scuola, " . data_italiana($datascrutinio));

            $schede->setXY(20, $posY);
            $schede->SetFont('Arial', '', 9);
            $schede->Cell(70, 5, $luogodata, "", 0, "L");
            $posY += 10;
            $schede->setXY(20, $posY);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(40, 5, "", "B", 0, "C");
            $schede->setXY(20, $posY + 5);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(40, 3, "Il(i) genitore(i) o chi ne fa le veci", "", 0, "C");


            $schede->setXY(140, $posY);
            $schede->SetFont('Arial', '', 10);
            $schede->Cell(40, 5, $firmadirigente, "B", 0, "C");
            $dicituradirigente = "Il Dirigente Scolastico (1)";
            $schede->setXY(140, $posY + 5);
            $schede->SetFont('Arial', '', 7);
            $schede->Cell(40, 3, $dicituradirigente, "", 0, "C");

            $posY += 15;
            $schede->setXY(20, $posY);

            $schede->SetFont('Arial', 'B', 10);
            $schede->Cell(172, 6, "ATTESTAZIONE", "", 0, "C");
            $posY += 10;
            $schede->setXY(20, $posY);

            $schede->SetFont('Arial', 'B', 10);
            $schede->Cell(172, 6, "Visti gli atti d'ufficio e la valutazione dei docenti della classe, si attesta che", "", 0, "C");

            $posY += 10;
            $schede->setXY(20, $posY);

            $schede->SetFont('Arial', 'B', 10);
            $esito = str_replace("|", " ", estrai_esito($alu, $con));
            $schede->Cell(172, 6, converti_utf8("l'alunno/a é stato/a $esito ."), "", 0, "C");

        }
        else
        {
            if ($livello == 2)
            {
                $legge = "(Art.2, comma 10 del D.P.R. n. 122/2009)";
            }
            else
            {
                $legge = "(Art.14, comma 7 del D.P.R. n. 122/2009)";
            }

            $posX = 20;
            $posY = 25;
            $schede->SetFont("Arial", 'B', 10);
            $schede->SetXY($posX, $posY);
            $schede->Cell(172, 5, "VALIDITA' DELL'ANNO SCOLASTICO", "LRT", 0, "C");
            $posY += 5;
            $schede->SetFont("Arial", 'B', 10);
            $schede->SetXY($posX, $posY);
            $schede->Cell(172, 5, "$legge", "LR", 0, "C");
            $posY += 5;
            $schede->SetFont("Arial", '', 9);
            $schede->SetXY($posX, $posY);
            $schede->Cell(172, 10, converti_utf8("Ai fini della validità dell'anno e dell'ammissione allo scrutinio finale, l'alunno/a:"), "LR", 0, "L");
            $posY += 10;

            $val1 = "[  ]";
            $val2 = "[  ]";
            $val3 = "[  ]";
            if (validita_anno($alu, $con) == 1)
            {
                $val1 = "[X]";
            }
            if (validita_anno($alu, $con) == 2)
            {
                $val2 = "[X]";
            }
            if (validita_anno($alu, $con) == 3)
            {
                $val3 = "[X]";
            }
            $schede->SetFont("Arial", '', 9);
            $schede->SetXY($posX, $posY);
            $schede->Cell(172, 10, "$val1  ha frequentato per almeno tre quarti dell'orario annuale;", "LR", 0, "L");
            $posY += 10;
            $schede->SetFont("Arial", '', 9);
            $schede->SetXY($posX, $posY);
            $schede->Cell(172, 10, "$val2  non ha frequentato per almeno tre quarti dell'orario annuale, ma ha usufruito della deroga;", "LR", 0, "L");
            $posY += 10;
            $schede->SetFont("Arial", '', 9);
            $schede->SetXY($posX, $posY);
            $schede->Cell(172, 10, "$val3  non ha frequentato per almeno tre quarti dell'orario annuale.", "BLR", 0, "L");
            $posY += 10;

            if ($livello==4)
            {
                $posY += 15;
                $schede->setXY(20, $posY);

                $schede->SetFont('Arial', 'B', 10);
                $schede->Cell(172, 6, "RISULTATO FINALE", "", 0, "C");
                $posY += 10;
                $schede->setXY(20, $posY);

                $schede->SetFont('Arial', 'B', 10);
                $schede->Cell(172, 6, "Visti i risultati conseguiti si dichiara che", "", 0, "C");

                $posY += 10;
                $schede->setXY(20, $posY);

                $schede->SetFont('Arial', 'B', 10);
                $esito = str_replace("|", " ", estrai_esito($alu, $con));
                $schede->Cell(172, 6, converti_utf8("l'alunno/a é stato/a $esito .(7)"), "", 0, "C");
            }
            else
            {
                if ($annoclasse==3 | $annoclasse==8)
                {
                    $posY += 15;
                    $schede->setXY(20, $posY);

                    $schede->SetFont('Arial', 'B', 10);
                    $schede->Cell(172, 6, "SOLO PER LE CLASSI TERZE", "", 0, "C");
                    $posY += 10;
                    $schede->setXY(20, $posY);

                    $schede->SetFont('Arial', 'B', 10);
                    $schede->Cell(86, 6, "GIUDIZIO DI IDONEITA'", "TL", 0, "C");

                    $schede->setXY(106, $posY);

                    $schede->SetFont('Arial', 'B', 10);
                    $schede->Cell(86, 6, "", "TR", 0, "C");


                    $posY += 6;

                    $schede->setXY(20, $posY);

                    $schede->SetFont('Arial', '', 8);
                    $schede->Cell(86, 6, "Voto (in cifre e in lettere)", "BL", 0, "C");

                    $schede->setXY(106, $posY);
                    $votoammissione=estrai_voto_ammissione($alu,$con);
                    $schede->SetFont('Arial', 'B', 10);
                    $schede->Cell(86, 6, "$votoammissione/10  ".dec_to_pag($votoammissione)."/decimi", "BR", 0, "C");


                    $posY+=20;
                    $schede->setXY(20, $posY);



                    $datascrutinio = estrai_datascrutinio($classe, $numeroperiodi, $con);
                    $luogodata = converti_utf8("$comune_scuola, " . data_italiana($datascrutinio));

                    $schede->setXY(20, $posY);
                    $schede->SetFont('Arial', '', 9);
                    $schede->Cell(70, 5, $luogodata, "", 0, "L");
                    $posY += 10;
                    $schede->setXY(20, $posY);
                    $schede->SetFont('Arial', '', 10);
                    $schede->Cell(40, 5, "", "B", 0, "C");
                    $schede->setXY(20, $posY + 5);
                    $schede->SetFont('Arial', '', 7);
                    $schede->Cell(40, 3, "Il(i) genitore(i) o chi ne fa le veci", "", 0, "C");


                    $schede->setXY(140, $posY);
                    $schede->SetFont('Arial', '', 10);
                    $schede->Cell(40, 5, $firmadirigente, "B", 0, "C");
                    $dicituradirigente = "Il Dirigente Scolastico (1)";
                    $schede->setXY(140, $posY + 5);
                    $schede->SetFont('Arial', '', 7);
                    $schede->Cell(40, 3, $dicituradirigente, "", 0, "C");

                    $posY += 15;
                    $schede->setXY(20, $posY);

                    $schede->SetFont('Arial', 'B', 10);
                    $schede->Cell(172, 6, "ATTESTAZIONE", "", 0, "C");
                    $posY += 10;
                    $schede->setXY(20, $posY);

                    $schede->SetFont('Arial', 'B', 10);
                    $schede->Cell(172, 6, "Visti gli atti d'ufficio e la valutazione dei docenti della classe, si attesta che", "", 0, "C");

                    $posY += 10;
                    $schede->setXY(20, $posY);

                    $schede->SetFont('Arial', 'B', 10);
                    $esito = str_replace("|", " ", estrai_esito($alu, $con));
                    $schede->Cell(172, 6, converti_utf8("l'alunno/a é stato/a $esito ."), "", 0, "C");

                }
            }

            // TTT

        }
        // DICITURE NOTE
        if ($livello==1)
        {

            $diciture="(1) La firma è omessa ai sensi dell'art. 3, D.to Lgs. 12.02.1993, n.39.
                       \n(2) Giudizio formulato secondo le modalità deliberate  dal Collegio dei docenti, ai sensi dell'Art. 2, comma 8, del D.P.R. n. 122/2009.";
        }
        if ($livello==2)
        {

            $diciture="(1) La firma è omessa ai sensi dell'art. 3, D.to Lgs. 12.02.1993, n.39.
                       \n(2) Specifica nota illustrativa di cui all'Art. , comma 8, del D.P.R. n. 122/2009.";
        }
        if ($livello==4)
    {

        $diciture="(1) PROMOZIONE; IDONEITA'; QUALIFICA; Idoneità all'ultima classe a seguito di esito positivo all'esame preliminare  e mancato superamento esami di Stato.
                       \n(2) PRIMA; SECONDA; TERZA.
                       \n(3) La firma è omessa ai sensi dell'art. 3, D.to Lgs. 12.02.1993, n.39.
                       \n(4) Il riquadro può essere utilizzato anche: per l'annotazione delle materie Art. 4, comma 6 del D.P.R. 122/2009; per l'annotazione prevista dall'Art. 9, comma 1 del D.P.R. 122/2009; per eventuali altre annotazioni o
                       \n       indicazione di rilascio di certificazione.
                       \n(5) Per le classi terminali indicare: ammesso/a agli esami - non ammesso/a agli esami.
                       \n(6) Solo per esami di qualifica professionale.
                       \n(7) promosso/a - non promosso/a. Per le classi terminali indicare: ammesso/a - non ammesso/a.";
    }
        $schede->SetXY(20,250);
        $schede->SetFont('Arial', '', 5);
        $schede->MultiCell(172,1,converti_utf8($diciture),0,"L");
// FINE QUARTA PAGINA

        // Cella della materia
    }


    $nomefile = "pagelle_" . decodifica_classe($classe, $con) . "_" . $periodo . ".pdf";
    $nomefile = str_replace(" ", "_", $nomefile);
    $schede->Output($nomefile, "I");

    mysqli_close($con);


}

function stampa_elementi_fissi(&$schede, $numpagina, $cognome, $nome, $codfiscale, $codicemeccanografico, $annoscol, $datastampa, $livello, $firmadirigente, $genitori = false)
{
    @require("../php-ini" . $_SESSION['suffisso'] . ".php");
    $posY = 15;

    $posX = 20;
    if ($numpagina > 1)
    {
        // RIGA 1
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(1, 4, " ", "LT", 0, "C");
        $posX += 1;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(50, 4, $cognome, "BT", 0, "L");
        $posX += 50;

        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(2, 4, " ", "T", 0, "C");
        $posX += 2;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(50, 4, $nome, "BT", 0, "L");
        $posX += 50;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(2, 4, " ", "T", 0, "C");
        $posX += 2;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(30, 4, $codfiscale, "BT", 0, "L");
        $posX += 30;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(2, 4, " ", "T", 0, "C");
        $posX += 2;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(17, 4, $codicemeccanografico, "BT", 0, "L");
        $posX += 17;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(2, 4, " ", "T", 0, "C");
        $posX += 2;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(15, 4, "$annoscol/" . ($annoscol + 1), "BT", 0, "L");
        $posX += 15;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(1, 4, " ", "TR", 0, "C");


        $posY += 4;

        $posX = 20;
        // RIGA 1
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 6);
        $schede->Cell(1, 3, " ", "LB", 0, "C");
        $posX += 1;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 6);
        $schede->Cell(50, 3, "COGNOME", "B", 0, "L");
        $posX += 50;

        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 6);
        $schede->Cell(2, 3, " ", "B", 0, "C");
        $posX += 2;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 6);
        $schede->Cell(50, 3, "NOME", "B", 0, "L");
        $posX += 50;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 6);
        $schede->Cell(2, 3, " ", "B", 0, "C");
        $posX += 2;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 6);
        $schede->Cell(30, 3, "CODICE FISCALE", "B", 0, "L");
        $posX += 30;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 6);
        $schede->Cell(2, 3, " ", "B", 0, "C");
        $posX += 2;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 6);
        $schede->Cell(17, 3, "COD. IST.", "B", 0, "L");
        $posX += 17;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 6);
        $schede->Cell(2, 3, " ", "B", 0, "C");
        $posX += 2;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 6);
        $schede->Cell(15, 3, "A. SCOL.", "B", 0, "L");
        $posX += 15;
        $schede->setXY($posX, $posY);
        $schede->SetFont('Arial', '', 6);
        $schede->Cell(1, 3, " ", "BR", 0, "C");
    }
    // FIRME

    // DATA E DIRIGENTE

    $luogodata = converti_utf8("$comune_scuola, $datastampa");

    $schede->setXY(20, 230);
    $schede->SetFont('Arial', '', 10);
    $schede->Cell(70, 5, $luogodata, "", 0, "L");

    if ($genitori)
    {
        $schede->setXY(20, 240);
        $schede->SetFont('Arial', '', 10);
        $schede->Cell(40, 5, "", "B", 0, "C");
        $schede->setXY(20, 245);
        $schede->SetFont('Arial', '', 7);
        $schede->Cell(40, 3, "Il(i) genitore(i) o chi ne fa le veci", "", 0, "C");

    }

    $schede->setXY(140, 240);
    $schede->SetFont('Arial', '', 10);
    $schede->Cell(40, 5, $firmadirigente, "B", 0, "C");


    if ($livello == 1 | $livello == 2)
    {
        $dicituradirigente = "Il Dirigente Scolastico (1)";
    }
    else
    {
        $dicituradirigente = "Il Dirigente Scolastico (3)";
    }
    $schede->setXY(140, 245);
    $schede->SetFont('Arial', '', 7);
    $schede->Cell(40, 3, $dicituradirigente, "", 0, "C");


    if ($_SESSION['suffisso'] != "") $suff = $_SESSION['suffisso'] . "/";
    else $suff = "";
  //  $schede->setXY(120, 250);
  //  $schede->Image('../abc/' . $suff . 'firmadirigente.png');
    $schede->setXY(90, 215);
    $schede->Image('../abc/' . $suff . 'timbro.png');


    // INDICAZIONE PAGINA

    $schede->setXY(170, 270);
    $schede->SetFont('Arial', '', 7);
    $schede->Cell(40, 3, "$numpagina/4", "", 0, "C");

    return;
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
  

