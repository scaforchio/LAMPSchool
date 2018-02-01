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
    $query = "select * from tbl_classi where idclasse=$idclasse";
    $ris = mysqli_query($conn1, inspref($query)) or die ("Errore nella query: " . inspref($query));
    $rec = mysqli_fetch_array($ris);


    if ($normalizzazione == 1)
    {
        $anno = (($rec['anno'] < 6) ? $rec['anno'] : ($rec['anno'] - 5));
    }
    else
    {
        $anno = $rec['anno'];
    }

    $daticlasse = $anno . " " . $rec['sezione'] . " " . $rec['specializzazione'];
    return $daticlasse;
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
    $query = "select * from tbl_classi where idclasse=$idclasse";
    $ris = mysqli_query($conn1, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn1) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    if ($normalizzazione == 1)
    {
        $anno = (($rec['anno'] < 6) ? $rec['anno'] : ($rec['anno'] - 5));
    }
    else
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

    $query = "select * from tbl_classi where idclasse=$idclasse";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    if ($normalizzazione == 1)
    {
        $anno = (($rec['anno'] < 6) ? $rec['anno'] : ($rec['anno'] - 5));
    }
    else
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
    $query = "select specializzazione from tbl_classi where idclasse=$idclasse";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $daticlasse = $rec['specializzazione'];

    return $daticlasse;
}

function estrai_classe_lezione($idlezione, $conn)
{
    $query = "select * from tbl_lezioni where idlezione=$idlezione";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . inspref($query));
    $rec = mysqli_fetch_array($ris);




    $idclasse = $rec['idclasse'];
    return $idclasse;
}
