<?php

/**
 * Created by PhpStorm.
 * User: pietro
 * Date: 17/06/15
 * Time: 8.31
 */

/**
 * Restituisce la denominazione completa della classe
 *
 * @param int $idclasse
 * @param object $conn Connessione al db
 * @return string
 */
function decodifica_classe($idclasse, $conn1, $normalizzazione = 0)
{
    $query = "select * from tbl_classi where idclasse='$idclasse'";
    $ris = eseguiQuery($conn1, $query);
    $rec = mysqli_fetch_array($ris);


    if ($normalizzazione == 1)
    {
        $anno = (($rec['anno'] < 6) ? $rec['anno'] : ($rec['anno'] - 5));
    } else
    {
        $anno = $rec['anno'];
    }

    $daticlasse = $anno . " " . $rec['sezione'] . " " . $rec['specializzazione'];
    return $daticlasse;
}

function estraiAprifila2($idclasse, $conn)
{
    $query = "select aprifila2 from tbl_classi where idclasse='$idclasse'";
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);

    return $rec['aprifila2'];
}

function estraiChiudifila1($idclasse, $conn)
{
    $query = "select chiudifila1 from tbl_classi where idclasse='$idclasse'";
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);

    return $rec['chiudifila1'];
}

function estraiChiudifila2($idclasse, $conn)
{
    $query = "select chiudifila2 from tbl_classi where idclasse='$idclasse'";
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);

    return $rec['chiudifila2'];
}

function estraiAprifila1($idclasse, $conn)
{
    $query = "select aprifila1 from tbl_classi where idclasse='$idclasse'";
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);

    return $rec['aprifila1'];
}

/**
 * Restituisce l'anno della classe
 *
 * @param int $idclasse
 * @param object $conn Connessione al db
 * @return string
 */
function decodifica_anno_classe($idclasse, $conn1, $normalizzazione = 0)
{
    $query = "select * from tbl_classi where idclasse='$idclasse'";
    $ris = eseguiQuery($conn1, $query);
    $rec = mysqli_fetch_array($ris);
    if ($normalizzazione == 1)
    {
        $anno = (($rec['anno'] < 6) ? $rec['anno'] : ($rec['anno'] - 5));
    } else
    {
        $anno = $rec['anno'];
    }

    $daticlasse = $anno;

    return $daticlasse;
}

/**
 * Decodifica della classe senza specializzazione con normalizzazione
 * medie (-5)
 *
 * @param int $idclasse
 * @param object $conn Connessione al db
 * @return string
 */
function decodifica_classe_no_spec($idclasse, $conn, $normalizzazione = 0)
{
    // Se normalizzazione = 1 toglie 5 alle classi >5

    $query = "select * from tbl_classi where idclasse='$idclasse'";
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);
    if ($normalizzazione == 1)
    {
        $anno = (($rec['anno'] < 6) ? $rec['anno'] : ($rec['anno'] - 5));
    } else
    {
        $anno = $rec['anno'];
    }


    $daticlasse = $anno . " " . $rec['sezione'];
    return $daticlasse;
}

/**
 *
 * Restituisce la specializzazione di una classe
 *
 * @param string $idclasse
 * @param object $conn Connessione al db
 * @return string
 */
function decodifica_classe_spec($idclasse, $conn)
{
    $query = "select specializzazione from tbl_classi where idclasse='$idclasse'";
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);
    $daticlasse = $rec['specializzazione'];

    return $daticlasse;
}

function decodifica_classe_sezione($idclasse, $conn)
{
    $query = "select sezione from tbl_classi where idclasse='$idclasse'";
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);
    $daticlasse = $rec['sezione'];

    return $daticlasse;
}

function estrai_ore_lezione_classe($idclasse, $conn)
{
    $query = "select oresett from tbl_classi where idclasse='$idclasse'";
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);
    $daticlasse = $rec['oresett'];

    return $daticlasse;
}

function estrai_classe_lezione($idlezione, $conn)
{
    $query = "select * from tbl_lezioni where idlezione='$idlezione'";
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);

    $idclasse = $rec['idclasse'];
    return $idclasse;
}

function estrai_classi_coordinate($iddocente, $conn)
{
    $elenco = "";
    // print "ttt $data <br>";
    // SE LA DATA E' QUELLA ODIERNA POSSO SELEZIONARE DALLA COMPOSIZIONE DELLA CLASSE


    $query = "select idclasse from tbl_classi where idcoordinatore='$iddocente'";
    // print "TTTT ".inspref($query)."<br>";
    $ris = eseguiQuery($conn, $query);
    while ($rec = mysqli_fetch_array($ris))
    {
        $elenco .= $rec['idclasse'] . ",";
    }

    // ELIMINO L'ULTIMA VIRGOLA
    $elenco = substr($elenco, 0, strlen($elenco) - 1);
    return $elenco;
}
