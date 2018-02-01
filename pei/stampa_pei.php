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


$idalunno = stringa_html('idalunno');

require_once("../lib/fpdf/fpdf.php");

$pei = new FPDFPAG();

/*
 * STAMPA FRONTESPIZIO
 */

$pei->AddPage();
$pei->AddFont('palacescript', '', 'palacescript.php'); // Font del Ministero
$pei->Image('../immagini/repubblica.png', 96, 20, 13, 15);

$pei->SetFont('palacescript', '', 32);
$pei->setXY(20, 40);
$ministero = converti_utf8("Ministero dell’Istruzione, dell’ Università e della Ricerca");
$pei->Cell(172, 8, $ministero, NULL, 1, "C");

$pei->SetFont('Arial', 'B', 10);
$pei->setXY(20, 60);
$pei->Cell(172, 6, converti_utf8("$nome_scuola"), NULL, 1, "C");

$pei->SetFont('Arial', 'BI', 9);
$pei->setXY(20, 66);
$pei->Cell(172, 6, converti_utf8("$comune_scuola"), NULL, 1, "C");

$annoscolastico = $annoscol . "/" . ($annoscol + 1);
$pei->setXY(20, 82);
$pei->Cell(172, 6, "P.E.I. - A.S. $annoscolastico", NULL, 1, "C");


// Prelievo dei dati degli alunni

$datanascita = "";
$codfiscale = "";
$denominazione = "";
$idclasse = "";
$query = "SELECT datanascita, codfiscale, denominazione, idclasse FROM tbl_alunni,tbl_comuni
			  WHERE tbl_alunni.idcomnasc=tbl_comuni.idcomune 
			  AND idalunno=$idalunno";
$ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
// print inspref($query);
if ($val = mysqli_fetch_array($ris))
{
    $datanascita = data_italiana($val['datanascita']);
    $codfiscale = $val['codfiscale'];
    $denominazione = converti_utf8($val['denominazione']);
    $idclasse = $val['idclasse'];
}
// print "tttt ".$idclasse;   
// CLASSE
$pei->SetFont('Arial', '', 8);
$pei->setXY(20, 100);
$pei->Cell(20, 6, "Classe: ", 0);

$pei->SetFont('Arial', 'BI', 8);
//$pei->setXY(100,58);
$pei->Cell(20, 6, decodifica_classe($idclasse, $con), 1);

// COGNOME NOME ALUNNO     

$pei->SetFont('Arial', '', 8);
$pei->Cell(25, 6, "Alunno: ", 0);

$pei->SetFont('Arial', 'BI', 8);
$pei->Cell(107, 6, converti_utf8(decodifica_alunno($idalunno, $con)), 1, 1);

// DATA NASCITA
$pei->SetFont('Arial', '', 8);
$pei->setXY(20, 110);
$pei->Cell(20, 6, "Data nascita: ", 0);

$pei->SetFont('Arial', 'BI', 8);
$pei->Cell(20, 6, $datanascita, 1);

// COMUNE DI NASCITA
$posX = 20;
$pei->setXY($posX, 120);
if (trim($denominazione) != "" & $denominazione != "NON DEFINITO")
{
    $pei->SetFont('Arial', '', 8);
    $pei->Cell(20, 6, "Com. nasc.: ", 0);

    $pei->SetFont('Arial', 'BI', 8);
    $pei->Cell(66, 6, $denominazione, 1, 1);
    $posX += 86;
}
// COMUNE DI NASCITA
$pei->setXY($posX, 120);
if (trim($codfiscale) != "")
{
    $pei->SetFont('Arial', '', 8);
    $pei->Cell(20, 6, "Cod. fisc.: ", 0);
    $pei->SetFont('Arial', 'BI', 8);
    $pei->Cell(66, 6, $codfiscale, 1, 1);
}

$pei->AddPage();
$indicearg = array();
$indicepag = array();
/*
 * TIPO PROGRAMMAZIONE   
 */

$matpers = array();
$matall = array();
$pei->AddPage();
$indicearg[] = "Tipo programmazione";
$indicepag[] = $pei->PageNo();
$pei->setXY(20, 10);
$pei->SetFont('Arial', 'B', 14);
$pei->Cell(172, 8, "TIPO PROGRAMMAZIONE", NULL, 1, "C");
$posY = 30;
$query = "select * from tbl_tipoprog,tbl_materie
        where tbl_tipoprog.idmateria=tbl_materie.idmateria
        and idalunno=$idalunno
        order by denominazione";
$ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
while ($rec = mysqli_fetch_array($ris))
{
    $pei->setXY(50, $posY);
    $pei->SetFont('Arial', 'B', 11);
    $pei->Cell(70, 8, converti_utf8($rec['denominazione']), 1, 0);
    $pei->SetFont('Arial', 'I', 11);
    $pei->Cell(30, 8, converti_utf8(decodifica_tipo_prog($rec['tipoprogr'])), 1, 1);
    $matall[] = $rec['idmateria'];
    if ($rec['tipoprogr'] == 'P')
    {
        $matpers[] = $rec['idmateria'];
    }
    $posY += 8;
}


if (count($matpers) > 0)
{
    /*
     * PROGRAMMAZIONI INDIVIDUALIZZATE
     */

    $pei->AddPage();
    $indicearg[] = "Programmazioni individualizzate";
    $indicepag[] = $pei->PageNo();
    $pei->setXY(20, 50);
    $pei->SetFont('Arial', 'B', 14);
    $pei->Cell(172, 8, "PROGRAMMAZIONI INDIVIDUALIZZATE", NULL, 1, "C");
//$posY=30;

    for ($i = 0; $i < count($matpers); $i++)
    {
        //$posY+=10;

        $query = "select * from tbl_competalu where idmateria=" . $matpers[$i] . " and idalunno=$idalunno order by numeroordine";
        $ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));

        if (mysqli_num_rows($ris) > 0)
        {
            $pei->AddPage();
            $pei->setX(20);
            $pei->SetFont('Arial', 'B', 11);
            $pei->Cell(172, 8, "MATERIA: " . converti_utf8(decodifica_materia($matpers[$i], $con)), NULL, 1, "C");

            while ($val = mysqli_fetch_array($ris))
            {

                $numord = $val["numeroordine"];
                $sintcomp = $val["sintcomp"];
                $competenza = $val["competenza"];
                $idcompetenza = $val["idcompetenza"];

                $pei->setX(20);
                $pei->SetFont('Arial', 'BI', 10);
                $pei->Cell(172, 7, converti_utf8("$numord $sintcomp"), NULL, 1, "J");
                //$posY+=8;
                $pei->setX(20);
                $pei->SetFont('Arial', 'B', 9);
                $pei->MultiCell(172, 6, converti_utf8($competenza), 0, "J");


                $query = "select * from tbl_abilalu where idcompetenza=$idcompetenza and abil_cono='C' order by numeroordine";
                $risabil = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
                if (mysqli_num_rows($risabil) > 0)
                {
                    $pei->setX(20);
                    $pei->SetFont('Arial', 'U', 10);
                    $pei->Cell(172, 7, converti_utf8("CONOSCENZE"), NULL, 1, "J");
                }
                while ($valabil = mysqli_fetch_array($risabil))
                {
                    $sintabil = $valabil["sintabilcono"];
                    $numordabil = $valabil["numeroordine"];
                    $abilita = $valabil["abilcono"];
                    $obminimi = $valabil["obminimi"];

                    $pei->setX(20);
                    $pei->SetFont('Arial', 'I', 10);
                    $pei->Cell(172, 7, converti_utf8("$numordabil $sintabil"), NULL, 1, "J");
                    //$posY+=8;
                    $pei->setX(20);
                    $pei->SetFont('Arial', '', 9);
                    $pei->MultiCell(172, 6, converti_utf8($abilita), 0, "J");

                    // if ($numordabil==1) print "<br/><b><big><center>CONOSCENZE</center></big></b>";
                    //if (!$obminimi)
                    //	print "<br/><b>C $numord.$numordabil $sintabil</b><br> $abilita";
                    //else
                    //	print "<br/><i><b>C $numord.$numordabil $sintabil</b><br> $abilita</i>";
                }

                $query = "select * from tbl_abilalu where idcompetenza=$idcompetenza and abil_cono='A' order by numeroordine";
                $risabil = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
                if (mysqli_num_rows($risabil) > 0)
                {
                    $pei->setX(20);
                    $pei->SetFont('Arial', 'U', 10);
                    $pei->Cell(172, 7, converti_utf8("ABILITA'"), NULL, 1, "J");
                }
                while ($valabil = mysqli_fetch_array($risabil))
                {
                    $sintabil = $valabil["sintabilcono"];
                    $numordabil = $valabil["numeroordine"];
                    $abilita = $valabil["abilcono"];
                    $obminimi = $valabil["obminimi"];
                    $pei->setX(20);
                    $pei->SetFont('Arial', 'I', 10);
                    $pei->Cell(172, 7, converti_utf8("$numordabil $sintabil"), NULL, 1, "J");
                    //$posY+=8;
                    $pei->setX(20);
                    $pei->SetFont('Arial', '', 9);
                    $pei->MultiCell(172, 6, converti_utf8($abilita), 0, "J");

                    // if ($numordabil==1) print "<br/><b><big><center>ABILITA'</center></big></b>";
                    //	if (!$obminimi)
                    //		print "<br/><b>A $numord.$numordabil $sintabil</b><br> $abilita";
                    //	else
                    //		print "<br/><i><b>A $numord.$numordabil $sintabil</b><br> $abilita</i>";
                }
                // print "</font>";
            }
            // print "<br/><br/>(Le voci in <i>corsivo</i> fanno parte degli obiettivi minimi)";

            // print "</font>";

        }

    }
}


/*
 * ARGOMENTI, ATTIVITA' E COMPITI ASSEGNATI   
 */

$pei->AddPage();
$indicearg[] = "Argomenti, attività e compiti assegnati";
$indicepag[] = $pei->PageNo();
$pei->setXY(20, 50);
$pei->SetFont('Arial', 'B', 14);
$pei->Cell(172, 8, converti_utf8("ARGOMENTI, ATTIVITA' E COMPITI ASSEGNATI"), NULL, 1, "C");

for ($i = 0; $i < count($matall); $i++)
{
    $query = "select * from tbl_lezionicert
	         where idalunno=$idalunno
	         and idmateria=" . $matall[$i] . "
	         order by datalezione";

    $ris = mysqli_query($con, inspref($query));

    if (mysqli_num_rows($ris) > 0)
    {
        $pei->AddPage();
        $pei->setX(20);
        $pei->SetFont('Arial', 'B', 11);
        $pei->Cell(172, 8, "MATERIA: " . converti_utf8(decodifica_materia($matall[$i], $con)), NULL, 1, "C");
    }

    while ($rec = mysqli_fetch_array($ris))
    {
        $pei->setX(20);
        $pei->SetFont('Arial', 'I', 9);
        $pei->Cell(172, 8, converti_utf8(data_italiana($rec['datalezione'])), NULL, 1, "J");
        $pei->setX(20);
        $pei->SetFont('Arial', '', 9);
        $pei->MultiCell(172, 6, converti_utf8($rec['argomenti']), 0, "J");

        $pei->setX(20);
        $pei->SetFont('Arial', '', 9);
        $pei->MultiCell(172, 6, converti_utf8($rec['attivita']), 0, "J");


    }
}
/*
 * VALUTAZIONI   
 */

$pei->AddPage();
$indicearg[] = "Valutazioni";
$indicepag[] = $pei->PageNo();
$pei->setXY(20, 50);
$pei->SetFont('Arial', 'B', 14);
$pei->Cell(172, 8, converti_utf8("VALUTAZIONI RICEVUTE"), NULL, 1, "C");

for ($i = 0; $i < count($matall); $i++)
{

    if (estrai_tipo_prog($idalunno, $matall[$i], $con) == 'P')
    {
        $query = "select numeroordine,sintabilcono,data,tbl_valutazioniabilcono.voto
	           from tbl_valutazioniabilcono,tbl_valutazioniintermedie,tbl_abilalu 
	         where 
	         tbl_valutazioniabilcono.idvalint=tbl_valutazioniintermedie.idvalint
	         and tbl_valutazioniabilcono.idabilita=tbl_abilalu.idabilita
	         and idalunno=$idalunno
	         and idmateria=" . $matall[$i] . "
	         order by numeroordine, data";
    }
    else
    {
        $query = "select numeroordine,sintabilcono,data,tbl_valutazioniabilcono.voto
             from tbl_valutazioniabilcono,tbl_valutazioniintermedie,tbl_abildoc 
	         where 
	         tbl_valutazioniabilcono.idvalint=tbl_valutazioniintermedie.idvalint
	         and tbl_valutazioniabilcono.idabilita=tbl_abildoc.idabilita
	         and idalunno=$idalunno
	         and idmateria=" . $matall[$i] . "
	         order by numeroordine, data";
    }
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    if (mysqli_num_rows($ris) > 0)
    {
        $pei->AddPage();
        $pei->setX(20);
        $pei->SetFont('Arial', 'B', 11);
        $pei->Cell(172, 8, "MATERIA: " . converti_utf8(decodifica_materia($matall[$i], $con)), NULL, 1, "C");


        while ($rec = mysqli_fetch_array($ris))
        {
            $pei->setX(20);
            $pei->SetFont('Arial', '', 9);
            $pei->Cell(100, 8, converti_utf8($rec['sintabilcono']), 1, 0, "J");
            $pei->SetFont('Arial', 'I', 9);
            $pei->Cell(25, 8, converti_utf8(data_italiana($rec['data'])), 1, 0, "J");
            //$pei->setX(20);

            //$pei->setX(20);
            $pei->SetFont('Arial', '', 9);
            $pei->Cell(10, 8, converti_utf8(dec_to_csv($rec['voto'])), 1, 1, "J");

        }
    }


}


/*
 * OSSERVAZIONI SISTEMATICHE   
 */

$pei->AddPage();
$indicearg[] = "Osservazioni sistematiche";
$indicepag[] = $pei->PageNo();
$pei->setXY(20, 50);
$pei->SetFont('Arial', 'B', 14);
$pei->Cell(172, 8, converti_utf8("OSSERVAZIONI SISTEMATICHE"), NULL, 1, "C");

for ($i = 0; $i < count($matall); $i++)
{
    $query = "select * from tbl_osssist
	         where idalunno=$idalunno
	         and idmateria=" . $matall[$i] . "
	         order by data";

    $ris = mysqli_query($con, inspref($query));

    if (mysqli_num_rows($ris) > 0)
    {
        $pei->AddPage();
        $pei->setX(20);
        $pei->SetFont('Arial', 'B', 11);
        $pei->Cell(172, 8, "MATERIA: " . converti_utf8(decodifica_materia($matall[$i], $con)), NULL, 1, "C");
    }

    while ($rec = mysqli_fetch_array($ris))
    {
        $pei->setX(20);
        $pei->SetFont('Arial', 'I', 9);
        $pei->Cell(172, 8, converti_utf8(data_italiana($rec['data'])), NULL, 1, "J");
        $pei->setX(20);
        $pei->SetFont('Arial', '', 9);
        $pei->MultiCell(172, 6, converti_utf8($rec['testo']), 0, "J");

    }
}
/*
 * NOTE DISCIPLINARI   
 */

$pei->AddPage();
$indicearg[] = "Note disciplinari";
$indicepag[] = $pei->PageNo();
$pei->setXY(20, 10);
$pei->SetFont('Arial', 'B', 14);
$pei->Cell(172, 8, converti_utf8("NOTE DISCIPLINARI"), NULL, 1, "C");

// VERIFICO PRESENZA NOTE INDIVIDUALI


$pei->setX(20);
$pei->SetFont('Arial', 'B', 12);
$pei->Cell(172, 8, converti_utf8("INDIVIDUALI"), NULL, 1, "C");

$query = "select tbl_notealunno.idnotaalunno, data, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno,tbl_alunni.idalunno as idalunno, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, tbl_docenti.iddocente as iddocente, specializzazione, sezione,anno, tbl_alunni.datanascita, testo, provvedimenti
            from tbl_noteindalu,tbl_notealunno,tbl_classi, tbl_alunni, tbl_docenti 
            where 
            tbl_noteindalu.idnotaalunno=tbl_notealunno.idnotaalunno
            and tbl_noteindalu.idalunno=tbl_alunni.idalunno
            and tbl_notealunno.idclasse=tbl_classi.idclasse and  tbl_notealunno.iddocente=tbl_docenti.iddocente 
            and tbl_noteindalu.idalunno=$idalunno 
            order by tbl_classi.specializzazione, tbl_classi.sezione, tbl_classi.anno, tbl_notealunno.data, tbl_docenti.cognome, tbl_docenti.nome, tbl_alunni.cognome, tbl_alunni.nome, tbl_alunni.datanascita";

$ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));
if (mysqli_num_rows($ris) > 0)
{
    while ($rec = mysqli_fetch_array($ris))
    {
        $pei->setX(20);
        $pei->SetFont('Arial', 'BI', 9);
        $pei->Cell(50, 6, converti_utf8(data_italiana($rec['data'])), 0, 0, "J");
        $pei->setX(50);
        $pei->SetFont('Arial', 'BI', 9);
        $pei->Cell(50, 6, converti_utf8($rec['cogndocente'] . " " . $rec['nomedocente']), 0, 1, "J");
        $pei->SetFont('Arial', '', 9);
        $pei->MultiCell(172, 6, converti_utf8($rec['testo']), 0, "J");
        $pei->SetFont('Arial', 'I', 9);
        $pei->MultiCell(172, 6, converti_utf8($rec['provvedimenti']), 0, "J");

    }
}
else
{
    $pei->setX(20);
    $pei->SetFont('Arial', 'B', 12);
    $pei->Cell(172, 8, converti_utf8("***NESSUNA NOTA INDIVIDUALE***"), NULL, 1, "C");
}

// VERIFICO PRESENZA NOTE DI CLASSE
$pei->setX(20);
$pei->SetFont('Arial', 'B', 12);
$pei->Cell(172, 8, converti_utf8("DI CLASSE"), NULL, 1, "C");


$query = "select idnotaclasse, data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, specializzazione, sezione,anno,  testo, provvedimenti
            from tbl_noteclasse,tbl_classi, tbl_docenti 
            where tbl_noteclasse.idclasse=tbl_classi.idclasse and  tbl_noteclasse.iddocente=tbl_docenti.iddocente  
            and tbl_noteclasse.idclasse=$idclasse 
            order by tbl_classi.specializzazione, tbl_classi.sezione, tbl_classi.anno, tbl_docenti.cognome, tbl_docenti.nome, tbl_noteclasse.data";

$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query di selezione nota: " . mysqli_error($con));
if (mysqli_num_rows($ris) > 0)
{
    while ($rec = mysqli_fetch_array($ris))
    {
        $pei->setX(20);
        $pei->SetFont('Arial', 'BI', 9);
        $pei->Cell(50, 6, converti_utf8(data_italiana($rec['data'])), 0, 0, "J");
        $pei->setX(50);
        $pei->SetFont('Arial', 'BI', 9);
        $pei->Cell(50, 6, converti_utf8($rec['cogndocente'] . " " . $rec['nomedocente']), 0, 1, "J");
        $pei->SetFont('Arial', '', 9);
        $pei->MultiCell(172, 6, converti_utf8($rec['testo']), 0, "J");
        $pei->SetFont('Arial', 'I', 9);
        $pei->MultiCell(172, 6, converti_utf8($rec['provvedimenti']), 0, "J");

    }
}
else
{
    $pei->setX(20);
    $pei->SetFont('Arial', 'B', 12);
    $pei->Cell(172, 8, converti_utf8("***NESSUNA NOTA DI CLASSE***"), NULL, 1, "C");
}

/*
 * ELENCO ALLEGATI   
 */

$pei->AddPage();
$indicearg[] = "Elenco allegati";
$indicepag[] = $pei->PageNo();
$pei->setXY(20, 50);
$pei->SetFont('Arial', 'B', 14);
$pei->Cell(172, 8, converti_utf8("ELENCO ALLEGATI"), NULL, 1, "C");

for ($i = 0; $i < count($matall); $i++)
{
    $query = "select descrizione,datadocumento,pei,docnome from tbl_documenti
	         where
	         idalunno=$idalunno
	         and idmateria=" . $matall[$i] . "
	         and pei
	         order by datadocumento";

    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));

    if (mysqli_num_rows($ris) > 0)
    {
        $pei->AddPage();
        $pei->setX(20);
        $pei->SetFont('Arial', 'B', 11);
        $pei->Cell(172, 8, "MATERIA: " . converti_utf8(decodifica_materia($matall[$i], $con)), NULL, 1, "C");


        while ($rec = mysqli_fetch_array($ris))
        {
            $pei->setX(20);
            $pei->SetFont('Arial', 'I', 9);
            $pei->Cell(30, 6, converti_utf8(data_italiana($rec['datadocumento'])), 1, 0, "J");
            $pei->setX(50);
            $pei->SetFont('Arial', '', 9);
            $pei->Cell(100, 6, converti_utf8($rec['descrizione']), 1, 0, "J");
            $pei->setX(150);
            $pei->SetFont('Arial', '', 9);
            $pei->Cell(50, 6, converti_utf8($rec['docnome']), 1, 1, "J");

        }
    }

}
// VERIFICO ALLEGATI GENERICI  
$query = "select descrizione,datadocumento,pei,docnome from tbl_documenti
	         where
	         idalunno=$idalunno
	         and idmateria=0
	         and pei
	         order by datadocumento";

$ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));

if (mysqli_num_rows($ris) > 0)
{
    $pei->AddPage();
    $pei->setX(20);
    $pei->SetFont('Arial', 'B', 11);
    $pei->Cell(172, 8, "DOCUMENTI VARI", NULL, 1, "C");


    while ($rec = mysqli_fetch_array($ris))
    {
        $pei->setX(20);
        $pei->SetFont('Arial', 'I', 9);
        $pei->Cell(30, 6, converti_utf8(data_italiana($rec['datadocumento'])), 1, 0, "J");
        $pei->setX(50);
        $pei->SetFont('Arial', '', 9);
        $pei->Cell(100, 6, converti_utf8($rec['descrizione']), 1, 0, "J");
        $pei->setX(150);
        $pei->SetFont('Arial', '', 9);
        $pei->Cell(50, 6, converti_utf8($rec['docnome']), 1, 1, "J");

    }
}
/*
 * INDICE   
 */
$pei->AddPage();
$pei->setXY(20, 10);
$pei->SetFont('Arial', 'B', 14);
$pei->Cell(172, 8, converti_utf8("INDICE"), NULL, 1, "C");

$pei->setY(40);
for ($i = 0; $i < count($indicearg); $i++)
{
    $pei->setX(40);
    $pei->SetFont('Arial', '', 11);
    $pei->Cell(80, 8, converti_utf8($indicearg[$i]), 0, 0);
    $pei->SetFont('Arial', '', 11);
    $pei->Cell(30, 8, converti_utf8($indicepag[$i]), 0, 1, "R");
}


$nomefile = "PEI_" . decodifica_alunno($idalunno, $con) . ".pdf";
$nomefile = str_replace(" ", "_", $nomefile);
$pei->Output($nomefile, "I");

mysqli_close($con);


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

function decodifica_tipo_prog($stringa)
{
    if ($stringa == 'N')
    {
        return "Normale";
    }
    if ($stringa == 'O')
    {
        return "Obiettivi minimi";
    }
    if ($stringa == 'P')
    {
        return "Personalizzata";
    }
    if ($stringa == '')
    {
        return "Normale";
    }
}
  

