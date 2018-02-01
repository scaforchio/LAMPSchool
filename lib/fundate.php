<?php

/**
 * Funzione che trasforma la data da formato anglosassone a formato italiano
 *
 * @param string $datainglese
 * @return string
 */
function data_italiana($datainglese)
{
    $datait = substr($datainglese, 8, 2) . '/' . substr($datainglese, 5, 2) . '/' . substr($datainglese, 0, 4);
    return $datait;
}

/**
 * Funzione che trasforma la data da formato AAAAMMGG a formato AAAA-MM-GG
 *
 * @param string $annomesegiorno
 * @return string
 */
function data_aaaammgg($annomesegiorno)
{
    return substr($annomesegiorno, 0, 4) . "-" . substr($annomesegiorno, 4, 2) . "-" . substr($annomesegiorno, 6);
}

/**
 * Funzione che trasforma la data da formato GG-MM-AAAA a formato AAAA-MM-GG
 *
 * @param string $giornomeseanno
 * @return string
 */
function data_to_db($giornomeseanno)
{
    return substr($giornomeseanno, 6, 4) . "-" . substr($giornomeseanno, 3, 2) . "-" . substr($giornomeseanno, 0, 2);
}

/**
 * Funzione che trasforma la data da formato AAAAMMGG a formato GG/MM/AAAA
 *
 * @param string $annomesegiorno
 * @return string
 */
function data_aaaammgg_italiana($annomesegiorno)
{
    return substr($annomesegiorno, 6, 2) . "/" . substr($annomesegiorno, 4, 2) . "/" . substr($annomesegiorno, 0, 4);
}

/**
 *
 * @param string $data
 * @return boolean
 */
function ControlloData($data)
{
    if (!preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/", $data))
    {
        return false;
    }
    else
    {
        $arrayData = explode("/", $data);
        $Giorno = $arrayData[0];
        $Mese = $arrayData[1];
        $Anno = $arrayData[2];
        if (!checkdate($Mese, $Giorno, $Anno))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}

/**
 *
 * @param string $data
 * @return string
 */
function ItaAme2Ita($data)
{
    $d = $data;
    if (ControlloData($d))
    {
        return $d;
    }
    else
    {
        $d = data_italiana($d);
        return $d;
    }
}

//
//  Restituisce il giorno della settimana dal numero
//
function giornodanum($i)
{
    if ($i == 1)
        return "Lun";
    if ($i == 2)
        return "Mar";
    if ($i == 3)
        return "Mer";
    if ($i == 4)
        return "Gio";
    if ($i == 5)
        return "Ven";
    if ($i == 6)
        return "Sab";
}

//
//  Restituisce il nome del mese
//
function nomemese($i)
{
    if ($i == 1)
        return "gennaio";
    if ($i == 2)
        return "febbraio";
    if ($i == 3)
        return "marzo";
    if ($i == 4)
        return "aprile";
    if ($i == 5)
        return "maggio";
    if ($i == 6)
        return "giugno";
    if ($i == 7)
        return "luglio";
    if ($i == 8)
        return "agosto";
    if ($i == 9)
        return "settembre";
    if ($i == 10)
        return "ottobre";
    if ($i == 11)
        return "novembre";
    if ($i == 12)
        return "dicembre";
}

//
//  Restituisce il numero dal giorno della settimana
//
function numdagiorno($i)
{
    if ($i == "Lun")
        return 1;
    if ($i == "Mar")
        return 2;
    if ($i == "Mer")
        return 3;
    if ($i == "Gio")
        return 4;
    if ($i == "Ven")
        return 5;
    if ($i == "Sab")
        return 6;
}

/**
 * Funzione che restituisce il giorno della settimana di una data
 *
 * @param string $datainglese
 * @return string
 */
function giorno_settimana($datainglese)
{
    $dw = date("D", strtotime($datainglese));
    if ($dw == "Mon")
        return "Lun";
    if ($dw == "Tue")
        return "Mar";
    if ($dw == "Wed")
        return "Mer";
    if ($dw == "Thu")
        return "Gio";
    if ($dw == "Fri")
        return "Ven";
    if ($dw == "Sat")
        return "Sab";
    if ($dw == "Sun")
        return "Dom";
}

/**
 * Funzione che restituisce il giorno della settimana di una data
 *
 * @param string $datainglese
 * @return string
 */
function numero_giorno_settimana($datainglese)
{
    $dw = date("D", strtotime($datainglese));
    if ($dw == "Mon")
        return 1;
    if ($dw == "Tue")
        return 2;
    if ($dw == "Wed")
        return 3;
    if ($dw == "Thu")
        return 4;
    if ($dw == "Fri")
        return 5;
    if ($dw == "Sat")
        return 6;
    if ($dw == "Sun")
        return 7;
}

function differenza_giorni($data_iniziale, $data_finale)
{

    $data1 = $data_iniziale;
    $data2 = $data_finale;

    $differenza = (($data2 - $data1) / 3600) / 24;
    return $differenza;
}

/**
 * Funzione che restituisce se il giorno è festivo
 *
 * @param string $data
 * @return boolean
 */
function giorno_festa($datafest, $conn)
{
    $query = "select * from tbl_festivita where data='$datafest'";
    $ris = mysqli_query($conn, inspref($query)) or die("Errore: " . inspref($query) . mysqli_error($conn));
    if (mysqli_num_rows($ris) > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * Funzione che restituisce il giorno è festivo
 *
 * @param string $data
 * @return boolean
 */
function estrai_festa($datafest, $conn)
{
    $query = "select * from tbl_festivita where data='$datafest'";
    $ris = mysqli_query($conn, inspref($query)) or die("Errore: " . inspref($query) . mysqli_error($conn));
    if ($rec = mysqli_fetch_array($ris))
    {
        return $rec['note'];
    }
}

/**
 * Funzione che restituisce il giorno di una data
 *
 * @param string $datainglese
 * @return string
 */
function estraigiorno($datainglese)
{
    return substr($datainglese, 8, 2);
}

/**
 * Funzione che restituisce il mese di una data
 *
 * @param string $datainglese
 * @return string
 */
function estraimese($datainglese)
{
    return substr($datainglese, 5, 2);
}

/**
 * Funzione che restituisce l'anno di una data
 *
 * @param string $datainglese
 * @return string
 */
function estraianno($datainglese)
{
    return substr($datainglese, 0, 4);
}

/**
 * Funzione che aggiunge un certo numero di giorni ad una data
 *
 * @param string $datainglese
 * @return string
 */
function aggiungi_giorni($datainglese, $giorni)
{
    $date = $datainglese;
    $stringasomma = "+$giorni day";
    $newdate = strtotime($stringasomma, strtotime($date)); // facciamo l'operazione
    $newdate = date('Y-m-d', $newdate); //trasformiamo la data nel formato accettato dal db YYYY-MM-DD
    return $newdate;
}

//
//  Restituisce l'orario di inizio di un'ora di lezione
//
function orainizio($h, $g, $conn)
{
    $query = "select inizio from tbl_orario where giorno='$g' and ora='$h' and valido";
    $ris = mysqli_query($conn, inspref($query)) or die("Errore:" . inspref($query));
    $rec = mysqli_fetch_array($ris);
    return $rec['inizio'];
}

//
//  Restituisce l'orario di fine di un'ora di lezione
//
function orafine($h, $g, $conn)
{
    $query = "select fine from tbl_orario where giorno=$g and ora=$h and valido";
    $ris = mysqli_query($conn, inspref($query));
    $rec = mysqli_fetch_array($ris);
    return $rec['fine'];
}

/**
 * RESTITUISCE I GIORNI EFFETTIVI DI LEZIONE TRA DUE DATE (INCLUSE)
 *
 * @param string $datainizio
 * @param string $datafine
 * @param object $conn Connessione al db
 * @return int $giornilezione
 */
function giorni_lezione_tra_date($datainizio, $datafine, $conn)
{
    $query = "select distinct datalezione from tbl_lezioni
            where datalezione>='$datainizio' and datalezione<='$datafine'";
    $ris = mysqli_query($conn, inspref($query)) or die("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $giornilezione = mysqli_num_rows($ris);

    return $giornilezione;
}

/**
 * RESTITUISCE I GIORNI EFFETTIVI DI LEZIONE TRA DUE DATE (INCLUSE)
 *
 * @param string $datainizio
 * @param string $datafine
 * @param object $conn Connessione al db
 * @return int $giornilezione
 */
function giorno_lezione_passata($datainizio, $giorni, $conn)
{
    $giornidasottrarre = 0;
    while (giorni_lezione_tra_date(aggiungi_giorni($datainizio, (0 - $giornidasottrarre)), $datainizio, $conn) < $giorni)
    {
        $giornidasottrarre++;
    }
    return aggiungi_giorni($datainizio, (0 - $giornidasottrarre));
}

/**
 * Somma le ore delle lezioni per una classe
 *
 * @param string $data
 * @param int $idclasse
 * @param object $conn Connessione al db
 * @return int
 */
function calcola_numero_ore($data, $idclasse, $conn)
{
    $numore = 0;
    $query = "select sum(numeroore) as totore from tbl_lezioni where datalezione='$data' and idclasse=$idclasse order by orainizio";
    $ris = mysqli_query($conn, inspref($query)) or die(mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);

    return $rec['totore'];
}

/**
 * Funzione che sostituisce gli apici con la virgoletta
 *
 * @param string $stringa
 * @param boolean $doppiapici se vale true sostituisce anche il carattere doppiapici
 * @return string
 */
function elimina_apici($stringa, $doppiapici = true)
{
    $strpulita = $stringa;
    $strpulita = str_replace("'", "’", $strpulita);
    $strpulita = str_replace("\\", "/", $strpulita);
    //print $strpulita."<br>";
    if ($doppiapici)
    {
        $strpulita = str_replace("\"", "’", $strpulita);
    }

    $strpulita = str_replace("<", "&lt;", $strpulita);
    $strpulita = str_replace(">", "&gt;", $strpulita);
    //print $strpulita."<br>";
    return $strpulita;
}

/**
 * Funzione che elimina la visualizzazione del 99 come voto
 *
 * @param string $stringa
 * @return string
 */
function elimina99($stringa)
{
    if ($stringa == 99)
    {
        return "";
    }

    return $stringa;
}

/**
 *
 * @return string
 */
function generacarattere()
{
    $val = rand(0, 30);
    if ($val == 0)
        return "0";
    if ($val == 1)
        return "1";
    if ($val == 2)
        return "2";
    if ($val == 3)
        return "3";
    if ($val == 4)
        return "4";
    if ($val == 5)
        return "5";
    if ($val == 6)
        return "6";
    if ($val == 7)
        return "7";
    if ($val == 8)
        return "8";
    if ($val == 9)
        return "9";
    if ($val == 10)
        return "a";
    if ($val == 11)
        return "b";
    if ($val == 12)
        return "c";
    if ($val == 13)
        return "d";
    if ($val == 14)
        return "e";
    if ($val == 15)
        return "f";
    if ($val == 16)
        return "g";
    if ($val == 17)
        return "h";
    if ($val == 18)
        return "i";
    if ($val == 19)
        return "h";   //non è un errore
    if ($val == 20)
        return "m";
    if ($val == 21)
        return "n";
    if ($val == 22)
        return "t";   //non è un errore
    if ($val == 23)
        return "p";
    if ($val == 24)
        return "q";
    if ($val == 25)
        return "r";
    if ($val == 26)
        return "s";
    if ($val == 27)
        return "t";
    if ($val == 28)
        return "u";
    if ($val == 29)
        return "v";
    if ($val == 30)
        return "z";
}

/**
 * TRASFORMA UN VALORE IN NUMERO CON VIRGOLA
 *
 * @param string $valore
 * @return string
 */
function num_to_ita($valore)
{
    return str_replace(".", ",", $valore);
}

/**
 *
 * @return string
 */
function creapassword()
{
    $password = "";

    for ($nc = 0; $nc < 8; $nc++)
    {
        $password = $password . generacarattere();
    }
    return $password;
}

/*
 * Restituisce l'ora iniziale tra le lezioni del giorno
 */

function estrai_ora_inizio_giornata($data, $idclasse, $conn)
{
    if ($data != "0000-00-00")
    {
        $query = "select min(orainizio) as oraini from tbl_lezioni where datalezione='$data' and idclasse=$idclasse";
        $risora = mysqli_query($conn, inspref($query)) or die("Errore:" . inspref($query));
        // print "tttt $query ".mysqli_num_rows($risora);
        if ($recora = mysqli_fetch_array($risora))
        {
            return $recora['oraini'];
        }
        else
        {
            return 0;
        }
    }
    else
        return 0;
    //return $oraini;
}

/*
 * Restituisce l'ora finale tra le lezioni della giornata
 */

function estrai_ora_fine_giornata($data, $idclasse, $conn)
{
    $query = "select max(orainizio+numeroore-1) as orafin from tbl_lezioni where datalezione='$data' and idclasse=$idclasse";
    $risora = mysqli_query($conn, inspref($query));
    if ($recora = mysqli_fetch_array($risora))
    {
        $orafin = $recora['orafin'];
    }
    return $orafin;
}

function aggiorna_data_firma_scrutinio($datastampa, $firmadirigente, $periodo, $classe, $conn)
{
    $query = "update tbl_scrutini set datastampa='" . data_to_db($datastampa) . "',firmadirigente='$firmadirigente' where periodo='$periodo' and idclasse='$classe'";
    // print $query;
    mysqli_query($conn, inspref($query)) or die("Errore: " . inspref($query, false));
}

function estrai_data_stampa($idclasse, $periodo, $conn)
{
    $query = "select datastampa from tbl_scrutini where periodo='$periodo' and idclasse='$idclasse'";
    $ris = mysqli_query($conn, inspref($query)) or die("Errore: " . inspref($query, false));
    $rec = mysqli_fetch_array($ris);
    $data = $rec['datastampa'];
    if ($data != "0000-00-00")
    {
        return $data;
    }
    else
    {
        return date('Y-m-d');
    }
}

function datediff($tipo = 'G', $par, $fin)
{
    $partenza = data_italiana($par);
    $fine = data_italiana($fin);
    switch ($tipo)
    {
        case "A" : $tipo = 365;
            break;
        case "M" : $tipo = (365 / 12);
            break;
        case "S" : $tipo = (365 / 52);
            break;
        case "G" : $tipo = 1;
            break;
    }
    $arr_partenza = explode("/", $partenza);
    $partenza_gg = $arr_partenza[0];
    $partenza_mm = $arr_partenza[1];
    $partenza_aa = $arr_partenza[2];
    $arr_fine = explode("/", $fine);
    $fine_gg = $arr_fine[0];
    $fine_mm = $arr_fine[1];
    $fine_aa = $arr_fine[2];
    $date_diff = mktime(12, 0, 0, $fine_mm, $fine_gg, $fine_aa) - mktime(12, 0, 0, $partenza_mm, $partenza_gg, $partenza_aa);
    $date_diff = floor(($date_diff / 60 / 60 / 24) / $tipo);
    return $date_diff;
}

function controlla_scadenza($numgiornirit, $giornolez, $meselez, $annolez)
{
    $dataoggi = date("Y-m-d");
    $datalezione = "$annolez-$meselez-$giornolez";
    $giorniadoggi = datediff("G", $datalezione, $dataoggi);

    // print "ttt $dataoggi $datalezione $giorniadoggi";
    if ($giorniadoggi > $numgiornirit & !$_SESSION['alias'] & !$_SESSION['derogalimite'])
        return false;
    else
        return true;
}

function estrai_data_lezione($idlezione, $conn)
{
    $query = "select datalezione from tbl_lezioni where idlezione=$idlezione";
    $ris = mysqli_query($conn, inspref($query)) or die("Errore nella query: " . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datalezione = $rec['datalezione'];
    return $datalezione;
}

function millitime()
{
    $microtime = microtime();
    $comps = explode(' ', $microtime);
    return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
}

function aggiungi_minuti($orainiziale, $minuti)
{
    $oraini = substr($orainiziale, 0, 2);
    $minini = substr($orainiziale, 3, 2);
    $minutitotaliiniziali = $oraini * 60 + $minini;
    $minutitotalifinali = $minutitotaliiniziali + $minuti;
    $orafin = floor($minutitotalifinali / 60);
    $strorafin = $orafin < 10 ? "0" . $orafin : $orafin;
    $minfin = $minutitotalifinali - $orafin * 60;
    $strminfin = $minfin < 10 ? "0" . $minfin : $minfin;
    return $strorafin . ":" . $strminfin;
}

function numero_colloqui_docente($iddocente, $idoraric, $datapren, $conn)
{

    $query = "select count(*) as numpren from tbl_prenotazioni
	      where data='$datapren' 
	      and tbl_prenotazioni.valido=1
	      and tbl_prenotazioni.idoraricevimento=$idoraric";
   // print inspref($query);
    $ris = mysqli_query($conn, inspref($query)) or die("Errore " . inspref($query, false));
    $rec = mysqli_fetch_array($ris);
    $numpren = $rec['numpren'];
   // print " num pren $numpren";
    return $numpren;
}

function numero_colloqui_massimi_docente($iddocente, $conn)
{

    $query = "select nummaxcolloqui from tbl_docenti
	        where iddocente=$iddocente";
    $ris = mysqli_query($conn, inspref($query)) or die("errore $query");
    $rec = mysqli_fetch_array($ris);
    $numpren = $rec['nummaxcolloqui'];
    return $numpren;
}
