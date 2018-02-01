<?php
/**
 * Created by PhpStorm.
 * User: pietro
 * Date: 17/06/15
 * Time: 8.29
 */


/**
 *
 * @param int $iddocente
 * @param object $conn Connessione al db
 * @return string
 */
function estrai_dati_docente($iddocente, $conn)
{
    $query = "select * from tbl_docenti where iddocente=$iddocente";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datidocente = $rec['cognome'] . " " . $rec['nome'];

    return $datidocente;
}

/**
 *
 * @param int $idamm
 * @param object $conn Connessione al db
 * @return string
 */
function estrai_dati_amministrativo($idamministrativo, $conn)
{
    $query = "select * from tbl_amministrativi where idamministrativo=$idamministrativo";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datiamm = $rec['cognome'] . " " . $rec['nome'];

    return $datiamm;
}


/**
 *
 * @param int $iddocente
 * @param object $conn Connessione al db
 * @return boolean
 */
function docente_sostegno($iddocente, $conn)
{
    $query = "select * from tbl_docenti where iddocente=$iddocente";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datidocente = $rec['sostegno'];

    return $datidocente;
}

/**
 *
 * @param int $iddocente
 * @param object $conn Connessione al db
 * @return boolean
 */

function estrai_docente_coordinatore($iddocente, $conn)
{
    $query = "select * from tbl_classi where idcoordinatore=$iddocente";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
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
 *
 * @param int $idmateria
 * @param object $conn Connessione al db
 * @return string
 */
function estrai_dirigente($conn)
{
    $query = "SELECT nome,cognome FROM tbl_docenti WHERE iddocente=1000000000";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datidirigente = $rec['nome'] . " " . $rec['cognome'];

    return $datidirigente;
}

function lezione_sostegno($idlezione,$iddocente,$con)
{
    
    $query="select * from tbl_lezionicert where idlezionenorm=$idlezione and iddocente=$iddocente";
    $ris=mysqli_query($con,inspref($query)) or die ("Errore".inspref($query));
    
    
    if (mysqli_num_rows($ris)>0)
        return true;
    else
        return false;
}
