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

@require_once("../php-ini".$_SESSION['suffisso'].".php");
@require_once("../lib/funzioni.php");
require_once("../lib/fpdf/fpdf.php");

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();


$tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente=="")
{
    header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']);
    die;
}

$idclasse = stringa_html('classe');

$datastampa = stringa_html('data');
$firmadirigente = stringa_html('firma');
$votitab = 'yes';
$credtab = false;
$voamtab = false;
$mediatab = false;
// print $votitab;
$periodo = stringa_html('periodo');


// Estraggo la data dello scrutinio
// $query="select datascrutinio"
// Estraggo tutti i voti e li metto in arrays

$codmaterie=array();
$codalunni=array();
$voti=array();
// variabili aggiunte da Simone per estrarre anche i voti Scritto, Orale e Pratico
$votoS=array();
$votoO=array();
$votoP=array();

$elencoalunni=estrai_alunni_classe_data($idclasse,$fineprimo,$con);


$query = "SELECT tbl_valutazionifinali.*,tbl_materie.tipovalutazione FROM tbl_valutazionifinali,tbl_alunni,tbl_materie
	          WHERE tbl_valutazionifinali.idalunno=tbl_alunni.idalunno
	          AND tbl_valutazionifinali.idmateria=tbl_materie.idmateria
	          AND tbl_valutazionifinali.idalunno in ($elencoalunni)
	          AND periodo='$periodo'";

//print inspref($query);
$risvalu=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con).$query);
while ($recval=mysqli_fetch_array($risvalu))
{
    $codalunni[]=$recval['idalunno'];
    $codmaterie[]=$recval['idmateria'];
// inizio parte Simone
    $votoS[]=$recval['votoscritto'];
    $votoO[]=$recval['votoorale'];
    $votoP[]=$recval['votopratico'];
// fine parte Simone
    $voti[]=$recval['votounico'];
}






$schede=new FPDF('L','mm','A3');
$schede->AddFont('palacescript', '', 'palacescript.php'); // Font del Ministero
$schede->AddPage();

// $datascrutinio=data_italiana(datascrutinio($idclasse, $periodo, $con));

$posX=0;
$posY=0;
$altriga=5;
$larghcol=13;
// STAMPA INTESTAZIONE

$schede->Image('../immagini/repubblica.png',200,20,13,15);
//$schede->Image('../immagini/miur.png',35,NULL,120,10);
$posY+=35;
$schede->SetFont('palacescript', '', 32);
$schede->setXY(10,$posY);
$ministero = converti_utf8("Ministero dell’Istruzione, dell’ Università e della Ricerca");
$schede->Cell(400,8,$ministero,NULL,1,"C");
$posY+=10;
$schede->SetFont('Arial','B',10);
$schede->setXY(10,$posY);
$schede->Cell(400,6,converti_utf8("$nome_scuola")." ".converti_utf8("$comune_scuola"),NULL,1,"C");
$posY+=8;
$schede->SetFont('Arial','BI',9);
$schede->setXY(10,$posY);
$specplesso=converti_utf8($plesso_specializzazione.": ".decodifica_classe_spec($idclasse, $con). " - Classe: ".decodifica_classe_no_spec($idclasse,$con,1));
$schede->Cell(400,6,$specplesso,NULL,1,"C");
$posY+=8;
$schede->SetFont('Arial','BI',9);
$schede->setXY(10,$posY);
if ($numeroperiodi=='2') $per='quadrimestre'; else $per='trimestre';
$descrperiodo=converti_utf8("Esiti scrutinio ".$periodo."° $per");
$schede->Cell(400,6,$descrperiodo,NULL,1,"C");
$posY+=8;



// INIZIO TABELLA
$query="SELECT distinct tbl_materie.idmateria,sigla,tipovalutazione FROM tbl_cattnosupp,tbl_materie
	        WHERE tbl_cattnosupp.idmateria=tbl_materie.idmateria
	              and tbl_cattnosupp.idclasse=$idclasse
	              and tbl_materie.progrpag<100
	              and tbl_cattnosupp.iddocente <> 1000000000
	              order by tbl_materie.progrpag,tbl_materie.sigla";

$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con).$query);

$codmat=array();

$nummaterie=mysqli_num_rows($ris);
$posIniziale = calcolaPosizioneIniziale($nummaterie, $votitab,$voamtab,$mediatab,$credtab,$larghcol);
$posX = $posIniziale;
if ($nummaterie>0)
{

    $posY+=$altriga;
    $schede->setXY($posIniziale,$posY);
    $schede->SetFont('Arial','B',8);

    $schede->Cell(90,$altriga,converti_utf8("Alunno"),1,NULL,"C");
    $posX+=90;


    if ($votitab=="yes")
    {
        while($nom=mysqli_fetch_array($ris))
        {



            $codmat[]=$nom["idmateria"];
            $schede->setXY($posX,$posY);
            $schede->SetFont('Arial','B',7);

            $schede->Cell($larghcol,$altriga,converti_utf8($nom["sigla"]),1,NULL,"C");

            $posX+=$larghcol;



        }
        // INSERISCO LA CONDOTTA
        $schede->setXY($posX,$posY);
        $schede->SetFont('Arial','B',7);
        $schede->Cell($larghcol,$altriga,converti_utf8("COMP"),1,NULL,"C");
        $posX+=$larghcol;
    }


    //
    //  Stampo le tipologie di voti (S O P)
    //

    $posY+=$altriga;
    $posX=$posIniziale;
    $schede->setXY($posIniziale,$posY);
    $schede->SetFont('Arial','B',8);

    $schede->Cell(90,$altriga,converti_utf8(""),1,NULL,"C");
    $posX+=90;

    mysqli_data_seek($ris,0);

    if ($votitab=="yes")
    {
        while($nom=mysqli_fetch_array($ris))
        {

            $strtipoval="";
            if (strpos($nom['tipovalutazione'],"U")>0)
                $strtipoval.="U";
            if (strpos($nom['tipovalutazione'],"S")>0)
                $strtipoval.="S ";
            if (strpos($nom['tipovalutazione'],"O")>0)
                $strtipoval.="O ";
            if (strpos($nom['tipovalutazione'],"P")>0)
                $strtipoval.="P ";


            $schede->setXY($posX,$posY);
            $schede->SetFont('Arial','B',7);

            $schede->Cell($larghcol,$altriga,converti_utf8($strtipoval),1,NULL,"C");

            $posX+=$larghcol;

        }
        // INSERISCO LA CONDOTTA
        $schede->setXY($posX,$posY);
        $schede->SetFont('Arial','B',7);
        $schede->Cell($larghcol,$altriga,converti_utf8("U"),1,NULL,"C");
        $posX+=$larghcol;
    }

    // $schede->setXY($posX,$posY);
    //	 $schede->SetFont('Arial','B',8);
    //	 $schede->Cell(90,$altriga,converti_utf8("Esito finale"),1,NULL,"C");

    // CICLO SU TUTTI GLI ALUNNI


    $numeroalunno=0;

    $query="select * from tbl_alunni where idalunno in ($elencoalunni) order by cognome,nome,datanascita";
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con).$query);

    while($val=mysqli_fetch_array($ris))
    {
        $listavoti=array();
        // $esiste_voto=false;
        $idalunno=$val["idalunno"];
        $numeroalunno++;
        $posY+=$altriga;
        $posX=$posIniziale;
        $schede->setXY($posX,$posY);
        $schede->SetFont('Arial','',8);
        $schede->Cell(90,$altriga,converti_utf8($val['cognome']." ".$val['nome']." (".data_italiana($val['datanascita']).")"),1,NULL,"L");
        $posX+=90;

        if ($votitab=="yes")
        {
            $contavoti=0;
            $sommavoti=0;
            for ($nummat=0;$nummat<count($codmat);$nummat++)
            {
                $cm=$codmat[$nummat];
//                $votounico=ricerca_voto($idalunno,$cm,$codalunni,$codmaterie,$voti); RIGA ORIGINALE
//		Sotto ho modificato passando un altro parametro alla funzione ricerca_voto. Ho aggiunto solo il voto Orale
                $votounico=ricerca_voto($idalunno,$cm,$codalunni,$codmaterie,$voti,$votoO,$votoS,$votoP);
                $schede->setXY($posX,$posY);
                $schede->SetFont('Arial','',8);
                $schede->Cell($larghcol,$altriga,converti_utf8($votounico),1,NULL,"C");
                $posX+=$larghcol;
            }
            // INSERISCO IL VOTO DI CONDOTTA
//                $votounico=ricerca_voto($idalunno,-1,$codalunni,$codmaterie,$voti); RIGA ORIGINALE
//		Sotto ho modificato passando un altro parametro alla funzione ricerca_voto. Ho aggiunto solo il voto Orale
            $votounico=ricerca_voto($idalunno,-1,$codalunni,$codmaterie,$voti,$votoO,$votoS,$votoP);
            $schede->setXY($posX,$posY);
            $schede->SetFont('Arial','',8);
            $schede->Cell($larghcol,$altriga,converti_utf8($votounico),1,NULL,"C");
            $posX+=$larghcol;
        }



    }

}

//   FIRMA E TIMBRO
$posY+=20;
$luogodata=converti_utf8("$comune_scuola, $datastampa");
$schede->SetXY(23,$posY);
$schede->SetFont('Arial','B',10);
$schede->Cell(95,8,$luogodata,0,1,'L');
$schede->setXY(220,$posY);
$schede->SetFont('Arial','B',10);
$schede->Multicell(172,6,converti_utf8("Il dirigente scolastico\n".$firmadirigente."\n\n\n\n\n\n"),0,"C");

$schede->setXY(278,$schede->getY()-25);
if ($_SESSION['suffisso']!="") $suff=$_SESSION['suffisso']."/"; else $suff="";
if (estrai_dirigente($con)==$firmadirigente)
    $schede->Image('../abc/'.$suff.'firmadirigente.png');

$schede->setXY(200,$schede->getY()-25);
$schede->Image('../abc/'.$suff.'timbro.png');



$nomefile="tabellone_".decodifica_classe($idclasse, $con)."_F.pdf";
$nomefile=str_replace(" ","_",$nomefile);
$schede->Output($nomefile,"I");

mysqli_close($con);

function elimina_cr($stringa)
{
    // $strpul=converti_utf8($stringa);
    $strpul=str_replace(array("\n","\r"), " ", $stringa);
    return $strpul;
}
function inserisci_new_line($stringa)
{
    //$strpul=converti_utf8($stringa);
    $strpul=str_replace("|", "\n", $stringa);
    return $strpul;
}

function estrai_prima_riga($stringa)
{
    //$strpul=converti_utf8($stringa);
    $posint=strpos($stringa,"|");
    if ($posint!=0)
        $str1=substr($stringa,0,$posint);
    else
        $str1=$stringa;
    return $str1;
}

function estrai_seconda_riga($stringa)
{
    //$strpul=converti_utf8($stringa);
    $posint=strpos($stringa,"|");
    if ($posint!=0)
        $str2=substr($stringa,$posint+1);
    else
        $str2="";
    return $str2;
}


function ricerca_voto($idalunno,$idmateria,$alu,$materie,$valutaz,$valutazO,$valutazS,$valutazP)
{
    for ($i=0;$i<count($valutaz);$i++)
    {
//		Ho modificato la funzione. In questo modo controlla se è ASSENTE il voto unico. In questo caso ritorna il voto Orale.
//		Chiaramente è solo per fare un test, deve pubblicare anche Scritto e/o Pratico
        $strvoto="";
        if ($idalunno==$alu[$i] & ($idmateria) == $materie[$i])
            if ($valutaz[$i]!=99 && $valutaz[$i] != NULL && $valutaz[$i] != "")
                return dec_to_vot($valutaz[$i]);
            else
            {
                if ($valutazS[$i] != 99 && $valutazS[$i] != NULL && $valutazS[$i] != "")
                {
                    $strvoto .= dec_to_vot( $valutazS[$i])." ";
                }
                if ($valutazO[$i] != 99 && $valutazO[$i] != NULL && $valutazO[$i] != "")
                {
                    $strvoto .= dec_to_vot($valutazO[$i])." ";
                }
                if ($valutazP[$i] != 99 && $valutazP[$i] != NULL && $valutazP[$i] != "")
                {
                    $strvoto .= dec_to_vot($valutazP[$i]);
                }
                return $strvoto;
            }
    }
    return "";
}

function calcolaPosizioneIniziale($nummaterie, $votitab,$voamtab,$mediatab,$credtab,$larghcol)
{
    $spazio=90;
    if ($votitab=="yes")
        $spazio+=($nummaterie*$larghcol);
    if ($voamtab=="yes")
        $spazio+=$larghcol;
    if ($mediatab=="yes")
        $spazio+=$larghcol;
    if ($credtab=="yes")
        $spazio+=$larghcol;
    return (410-$spazio)/2;

}
