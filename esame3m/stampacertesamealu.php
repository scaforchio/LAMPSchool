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
    $query = "select tbl_alunni.idalunno from tbl_alunni, tbl_esesiti"
            . " where tbl_alunni.idalunno=tbl_esesiti.idalunno"
            . " and idclasseesame=$idclasse "
            . " and votofinale>=6"
            . " order by cognome,nome";
    $ris = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));
    ;
    while ($val = mysqli_fetch_array($ris))
    {
        $alunni[] = $val['idalunno'];
    }
} else
{

    $alunni[] = $idalunno;
    $query = "select idclasseesame from tbl_alunni where idalunno=$idalunno";
    $ris = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));
    ;
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
} else
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
    $schede = new FPDF();
    //$schede->AliasNbPages();
    $schede->AddFont('palacescript', '', 'palacescript.php'); // Font del Ministero
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

    $datascrutinio = data_italiana(estrai_datascrutinio($idclasse, $periodo, $con));


    $query = "select * from tbl_esami3m where idclasse=$idclasse";
    $risesa = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));
    ;
    $recesa = mysqli_fetch_array($risesa);


    $query = "select * from tbl_esmaterie where idclasse=$idclasse";
    $rismat = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));
    ;
    $recmat = mysqli_fetch_array($rismat);


    // $primalingua = converti_utf8($recmat['m3e']);
    $secondalingua = converti_utf8($recmat['m' . $recmat['num2lin'] . 'e']);


    foreach ($alunni as $alu)
    {
        $query = "select * from tbl_esesiti where idalunno=$alu";
        $ris = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));
        ;
        $rec = mysqli_fetch_array($ris);


        $schede->AddPage();

        // $indirizzo_scuola = "Via jkjkjkjkjjl";

        $schede->Image('../immagini/repubblica.png', 96, 20, 13, 15);

        $schede->SetFont('palacescript', '', 28);
        $schede->setXY(20, 40);
        $ministero = converti_utf8("Ministero dell’Istruzione, dell’ Università e della Ricerca");
        $schede->Cell(172, 8, $ministero, NULL, 1, "C");

        $schede->SetFont('Arial', 'B', 12);
        $schede->setXY(20, 50);
        $schede->Cell(172, 6, converti_utf8("$nome_scuola"), NULL, 1, "C");

        $schede->SetFont('Arial', 'BI', 10);
        $schede->setXY(20, 58);
        $schede->Cell(172, 6, converti_utf8("$comune_scuola"), NULL, 1, "C");

        $schede->SetFont('Arial', '', 8);
        $schede->setXY(20, 66);
        $schede->Cell(172, 6, converti_utf8("$indirizzo_scuola"), NULL, 1, "C");


        $schede->SetFont('Arial', 'B', 14);
        /*  if ($numeroperiodi==3)
          $per="trimestre";
          else
          $per="quadrimestre";
          $per=converti_utf8($per); */
        $annoscolastico = $annoscol . "/" . ($annoscol + 1);



        // Prelievo dei dati degli alunni

        $datanascita = "";
        $codfiscale = "";
        $denominazione = "";
        $idcomnasc = "";
        $query = "SELECT datanascita, codfiscale, denominazione,idcomnasc FROM tbl_alunni,tbl_comuni
              WHERE tbl_alunni.idcomnasc=tbl_comuni.idcomune 
              AND idalunno=$alu";
        $ris = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));
        ;
        if ($val = mysqli_fetch_array($ris))
        {
            $datanascita = data_italiana($val['datanascita']);
            $codfiscale = $val['codfiscale'];
            $denominazione = converti_utf8($val['denominazione']);
            $idcomnasc = $val['idcomnasc'];
            if (substr($codfiscale, 9, 2) > '35')
            {
                $sesso = 'f';
            } else
            {
                $sesso = 'm';
            }
        }


        // COGNOME NOME ALUNNO

        $schede->setXY(20, 82);
        if ($sesso == 'f')
            $schede->MultiCell(172, 6, "ALUNNA", 0, "C");
        else
            $schede->MultiCell(172, 6, "ALUNNO", 0, "C");

        $schede->setXY(20, 100);
        $schede->SetFont('Arial', '', 14);
        $schede->MultiCell(172, 6, converti_utf8(decodifica_alunno($alu, $con)), 0, "C");



        if ($sesso == 'f')
        {
            $cand = "nata il ";
        } else
        {
            $cand = "nato il ";
        }

        if ($denominazione != "NON DEFINITO")
            $cand .= $datanascita . " a " . $denominazione . " (" . estrai_sigla_provincia($idcomnasc, $con) . ")";
        else
            $cand .= $datanascita . " a " . $denominazione;
        $schede->setXY(20, 110);
        $schede->SetFont('Arial', '', 14);
        $schede->MultiCell(172, 6, converti_utf8($cand), 0, "C");

        $schede->setXY(20, 150);
        $schede->SetFont('Arial', 'I', 14);
        if ($sesso == 'm')
            $schede->MultiCell(170, 6, converti_utf8("Visti gli esiti delle prove d'esame e il percorso di studi effettuato,\nl'alunno ha superato l'Esame di Stato conclusivo del primo ciclo\ncon la seguente valutazione:"), 0, 'C');
        else
            $schede->MultiCell(170, 6, converti_utf8("Visti gli esiti delle prove d'esame e il percorso di studi effettuato,\nl'alunna ha superato l'Esame di Stato conclusivo del primo ciclo\ncon la seguente valutazione:"), 0, 'C');
        

          $schede->setXY($posX, $posY + 60);
          if ($rec['lode'])
          {
          $lode = " con lode ";
          }
          else
          {
          $lode = " ";
          }


          $schede->setXY(20, 190);
          $schede->SetFont('Arial', 'B', 14);
          $schede->MultiCell(170, 6, converti_utf8(dec_to_pag($rec['votofinale']). " / decimi" . $lode),0,"C");
          

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
          }


          // STAMPA PARTE TERMINALE
          $datastampa = data_italiana($recesa['datascrutinio']);
          $luogodata = converti_utf8("$comune_scuola, $datastampa");
          $posY=210;
          $schede->setXY(20, $posY);
          $schede->SetFont('Arial', '', 10);
          $schede->Cell(70, 5, $luogodata, "", 0, "L");

          $schede->setXY(20, $posY+40);
          $schede->SetFont('Arial', '', 10);
          $schede->Cell(40, 5, converti_utf8($nomepresidente . " " . $cognomepresidente), "B", 0, "C");
          $dicituradirigente = "IL PRESIDENTE";
          $schede->setXY(20, $posY+45);
          $schede->SetFont('Arial', '', 7);
          $schede->Cell(40, 3, $dicituradirigente, "", 0, "C");

          $schede->setXY(140, $posY+40);
          $schede->SetFont('Arial', '', 10);
          $schede->Cell(40, 5, converti_utf8(estrai_dirigente($con)), "B", 0, "C");
          $dicituradirigente = "IL DIRIGENTE SCOLASTICO";
          $schede->setXY(140, $posY+45);
          $schede->SetFont('Arial', '', 7);
          $schede->Cell(40, 3, $dicituradirigente, "", 0, "C");
          
          
          if ($_SESSION['suffisso'] != "")
          {
          $suff = $_SESSION['suffisso'] . "/";
          }
          else $suff = "";
          $schede->setXY(140, $posY+50);
          $schede->Image('../abc/' . $suff . 'firmadirigente.png');
          
          $schede->setXY(90, $posY+15);
          $schede->Image('../abc/' . $suff . 'timbro.png');

         
    }

    if (count($alunni) > 1)
        $nomefile = "cert_esame_" . decodifica_classe($idclasse, $con) . ".pdf";
    else
        $nomefile = "cert_esame_" . decodifica_alunno($alunni[0], $con) . ".pdf";

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
    } else
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
    } else
    {
        $str2 = "";
    }
    return converti_utf8($str2);
}
