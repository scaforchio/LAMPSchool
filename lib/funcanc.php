<?php
/**
 * Created by PhpStorm.
 * User: pietro
 * Date: 16/06/15
 * Time: 18.40
 */




/**
 *
 * @param int $idmateria
 * @param object $conn Connessione al db
 * @return boolean
 */
function poss_canc_mat($idmateria, $conn)
{
    $numrec = 0;
    $query = "select * from tbl_cattnosupp where idmateria=$idmateria";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_valutazioniintermedie where idmateria=$idmateria";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_valutazionifinali where idmateria=$idmateria";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_proposte where idmateria=$idmateria";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_competdoc where idmateria=$idmateria";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_competscol where idmateria=$idmateria";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_asslezione where idmateria=$idmateria";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);

    if ($numrec > 0)
    {
        return false;
    }

    return true;
}

/**
 *
 * @param string $iddocente
 * @param object $conn Connessione al db
 * @return boolean
 */
function poss_canc_doc($iddocente, $conn)
{
    $numrec = 0;
    $query = "select * from tbl_cattnosupp where iddocente=$iddocente";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_firme where iddocente=$iddocente";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_notealunno where iddocente=$iddocente";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_noteclasse where iddocente=$iddocente";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_ritardi where iddocentegiust=$iddocente";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_valutazioniintermedie where iddocente=$iddocente";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_assenze where iddocentegiust=$iddocente";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);

    if ($numrec > 0)
    {
        return false;
    }

    return true;
}

/**
 *
 * @param string $idalunno
 * @param object $conn Connessione al db
 * @return boolean
 */
function poss_canc_alu($idalunno, $conn)
{
    $numrec = 0;
    $query = "select * from tbl_assenze where idalunno=$idalunno";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_asslezione where idalunno=$idalunno";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_noteindalu where idalunno=$idalunno";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_proposte where idalunno=$idalunno";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_ritardi where idalunno=$idalunno";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select *  from tbl_usciteanticipate where idalunno=$idalunno";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_valutazionifinali where idalunno=$idalunno";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_valutazioniintermedie where idalunno=$idalunno";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_esiti where idalunno=$idalunno";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    if ($numrec > 0)
    {
        return false;
    }

    return true;
}

/**
 *
 * @param string $idclasse
 * @param object $conn Connessione al db
 * @return boolean
 */
function poss_canc_cla($idclasse, $conn)
{
    $numrec = 0;
    $query = "select * from tbl_cattnosupp where idclasse=$idclasse";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_alunni where idclasse=$idclasse";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_notealunno where idclasse=$idclasse";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_noteclasse where idclasse=$idclasse";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    $query = "select * from tbl_competdoc where idclasse=$idclasse";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);

    if ($numrec > 0)
    {
        return false;
    }

    return true;
}

/**
 *
 * @param string $idclasse
 * @param object $conn Connessione al db
 * @return boolean
 */
function poss_canc_gru($idgruppo, $conn)
{
    $numrec = 0;
    $query = "select * from tbl_lezionigruppi where idgruppo=$idgruppo";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);
    if ($numrec > 0)
    {
        return false;
    }

    return true;
}


/**
 *
 * @param string $idtipodocumento
 * @param object $conn Connessione al db
 * @return boolean
 */
function poss_canc_tdoc($idtipodocumento, $conn)
{
    $numrec = 0;
    $query = "select * from tbl_documenti where idtipodocumento=$idtipodocumento";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $numrec = $numrec + mysqli_num_rows($ris);

    if ($numrec > 0)
    {
        return false;
    }

    return true;
}
