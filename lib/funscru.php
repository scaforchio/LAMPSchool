<?php
/**
 * Created by PhpStorm.
 * User: pietro
 * Date: 16/06/15
 * Time: 18.31
 */

/**
 *
 * @param int $idclasse
 * @param int $periodo
 * @param int $numvalutazioni
 * @param object $conn Connessione al db
 * @return boolean
 */
function scrutinio_completo($idclasse, $periodo, $numvalutazioni, $conn)
{
    $query = "SELECT count(*) as numvotifin FROM tbl_valutazionifinali,tbl_alunni
	          WHERE tbl_valutazionifinali.idalunno=tbl_alunni.idalunno
	          AND idclasse=$idclasse
	          AND periodo='$periodo'
	          AND (    (votoscritto>0 and votoscritto<21)
	                or (votoorale>0 and votoorale<21)
	                or (votopratico>0 and votopratico<21)
	                or (votounico>0 and votounico<21))";
    $risalu = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $recalu = mysqli_fetch_array($risalu);
    $numvoti = $recalu['numvotifin'];

    return ($numvoti == $numvalutazioni);
}

/**
 *
 * @param int $idclasse , $periodo
 * @param object $conn Connessione al db
 * @return boolean
 */
function scrutinio_aperto($idclasse, $periodo, $conn)
{
    $queryscr = "SELECT * FROM tbl_scrutini WHERE idclasse=$idclasse AND periodo='$periodo'";
    $risscr = mysqli_query($conn, inspref($queryscr)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));

    if ($valscr = mysqli_fetch_array($risscr))
    {

        if ($valscr['stato'] == "C")
        {
            return false;
        }
    }

    return true;
}



function estrai_firma_scrutinio($idclasse, $periodo, $conn)
{
    $query = "select firmadirigente from tbl_scrutini where periodo='$periodo' and idclasse='$idclasse'";
    $ris = mysqli_query($conn, inspref($query)) or die("Errore: " . inspref($query, false));
    $rec = mysqli_fetch_array($ris);
    $firma = $rec['firmadirigente'];
    if ($firma != "")
    {
        return $firma;
    }
    else
    {
        return estrai_dirigente($conn);
    }
}

/**
 *
 * @param int $periodo
 * @return boolean
 */
function periodo_finale($periodo)
{
    global $numeroperiodi;

    if ($periodo == $numeroperiodi)
    {
        return true;
    }

    return false;
}



function decod_passaggio($val)
{
    if ($val == 0)
    {
        return "Sì";
    }
    if ($val == 1)
    {
        return "No";
    }
    if ($val == 2)
    {
        return "Giudizio sospeso";
    }
}

/**
 * Restituisce la data dello scrutinio
 *
 * @param int $idclasse , string $periodo
 * @param object $conn Connessione al db
 * @return string
 */
function estrai_datascrutinio($idclasse, $periodo, $conn)
{
    $query = "SELECT datascrutinio FROM tbl_scrutini WHERE periodo='$periodo' and idclasse=$idclasse";
    $ris = mysqli_query($conn, inspref($query)) or die(mysqli_error($conn) . inspref($query));
    $val = mysqli_fetch_array($ris);

    return $val['datascrutinio'];
}

/**
 * Restituisce la descrizione dell'esito
 *
 * @param int $idclasse
 * @param object $conn Connessione al db
 * @return string
 */
function decodifica_esito($idesito, $conn1)
{

    $query = "select * from tbl_tipiesiti where idtipoesito=$idesito";
    $ris = mysqli_query($conn1, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn1) . inspref($query));
    if ($rec = mysqli_fetch_array($ris))
    {
        $datiesito = $rec['descrizione'];
    }
    else
        $datiesito = "";
    return $datiesito;
}


/**
 * Restituisce l'effetto dell'esito (0=passaggio, 1 = non passaggio, 2=giudizio sospeso)
 *
 * @param int $idclasse
 * @param object $conn Connessione al db
 * @return string
 */
function passaggio($idesito, $conn)
{
    $datopassaggio=2;
    $query="select * from tbl_tipiesiti where idtipoesito=$idesito";

    $ris=mysqli_query($conn,inspref($query)) or die ("Errore: ".inspref($query,false));
    if ($rec=mysqli_fetch_array($ris))
    {
        $datopassaggio = $rec['passaggio'];
    }
    return $datopassaggio;
}


/**
 * Restituisce il tipo esito (passaggio a classe successiva SI/NO)
 *
 * @param int $idclasse
 * @param object $conn Connessione al db
 * @return string
 */
function decodifica_passaggio($idesito, $conn1)
{

    $query = "select * from tbl_tipiesiti where idtipoesito=$idesito";
    $ris = mysqli_query($conn1, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn1) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datipassaggio = $rec['passaggio'];

    return $datipassaggio;
}


/**
 * Restituisce la validita (passaggio a classe successiva SI/NO)
 *
 * @param int $idclasse
 * @param object $conn Connessione al db
 * @return string
 */
function validita_anno($idalunno, $conn1)
{
    $query = "select * from tbl_esiti where idalunno=$idalunno";
    $ris = mysqli_query($conn1, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn1) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datipassaggio = $rec['validita'];

    return $datipassaggio;
}

/**
 *  Verifica se il voto dela materia deve essere usato per il calcolo della media
 *
 * @param int $idmateria
 * @param object $conn Connessione al db
 * @return boolean
 */
function calcola_media($idmateria, $conn)
{
    $query = "select * from tbl_materie where idmateria=$idmateria";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datimateria = $rec['media'];

    return $datimateria;
}
