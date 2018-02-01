<?php
/**
 * Created by PhpStorm.
 * User: pietro
 * Date: 16/06/15
 * Time: 18.23
 */

/**
 * Funzione che estrae l'identificativo del comune dal codice catastale
 *
 * @param string $codcatastale
 * @param object $conn Connessione al db
 * @return int
 */
function estraicodcomune($codcatastale, $conn)
{
    $idcomune = 0;
    $sqlt = "select idcomune from tbl_comuni where codcatastale='$codcatastale'";
    $res = mysqli_query($conn, inspref($sqlt));

    if (mysqli_num_rows($res) > 0)
    {
        $rec = mysqli_fetch_array($res);
        $idcomune = $rec['idcomune'];
    }
    else
    {
        $idcomune = 9999; // NON DEFINITO;
    }

    return $idcomune;
}

/**
 * Funzione che estrae la denominazione del comune dal codice catastale
 *
 * @param string $codcatastale
 * @param object $conn Connessione al db
 * @return string
 */
function estraidenocomune($codcatastale, $conn)
{
    $denocomune = "";
    $sqlt = "select denominazione from tbl_comuni where codcatastale='$codcatastale'";
    $res = mysqli_query($conn, inspref($sqlt));

    if (mysqli_num_rows($res) > 0)
    {
        $rec = mysqli_fetch_array($res);
        $denocomune = $rec['denominazione'];
    }
    else
    {
        $denocomune = 'NON DEFINITO';
    }

    return $denocomune;
}

/**
 * Funzione che estrae la denominazione del comune dall'idcomune
 *
 * @param int $idcomune
 * @param object $conn Connessione al db
 * @return string
 */
function decodifica_comune($idcomune, $conn)
{
    $denocomune = "";
    $sqlt = "select denominazione from tbl_comuni where idcomune='$idcomune'";
    $res = mysqli_query($conn, inspref($sqlt));

    if (mysqli_num_rows($res) > 0)
    {
        $rec = mysqli_fetch_array($res);
        $denocomune = $rec['denominazione'];
    }
    else
    {
        $denocomune = 'NON DEFINITO';
    }

    return $denocomune;
}

/**
 * Funzione che controlla il codistat
 *
 * @param string $cod
 * @return int
 */
function controlla_codistat($cod)
{
    $car = substr($cod, 0, 1);
    if (!(is_string($car)))
    {
        return 1;
    }
    else
    {
        $car = substr($cod, 1, 3);
        if (!(is_numeric($car)))
        {
            return 1;
        }
        if (is_numeric($cod))
        {
            return 1;
        }
    }
}



function estrai_sigla_provincia($idcomune, $conn)
{
    
    $query = "select codiceistat from tbl_comuni where idcomune=$idcomune";
    $ris = mysqli_query($conn, inspref($query));
    $rec = mysqli_fetch_array($ris);
    $codiceistat = $rec['codiceistat'];
    
    $lung = strlen($codiceistat);
    $codprovincia = substr($codiceistat, 0, $lung - 3);
    
    
    if ($codprovincia!="")
    {
        $query = "select siglaprovincia from tbl_province where codprovincia=$codprovincia";
        $ris = mysqli_query($conn, inspref($query)) or die("Errore:" . mysqli_error($conn) . " " . inspref($query));
        $rec = mysqli_fetch_array($ris);
        return $rec['siglaprovincia'];
    }
    else
    {
        return "";
    }

}

