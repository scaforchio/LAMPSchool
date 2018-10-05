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
    $query = "select * from tbl_docenti where iddocente='$iddocente'";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datidocente = $rec['cognome'] . " " . $rec['nome'];

    return $datidocente;
}

/**
 *
 * @param int $iddocente
 * @param object $conn Connessione al db
 * @return string
 */
function estrai_mail_docente($iddocente, $conn)
{
    $query = "select * from tbl_docenti where iddocente='$iddocente'";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $maildocente = $rec['email'];

    return $maildocente;
}


/**
 *
 * @param int $idamm
 * @param object $conn Connessione al db
 * @return string
 */
function estrai_dati_amministrativo($idamministrativo, $conn)
{
    $query = "select * from tbl_amministrativi where idamministrativo='$idamministrativo'";
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
    $query = "select * from tbl_docenti where iddocente='$iddocente'";
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
    $query = "select * from tbl_classi where idcoordinatore='$iddocente'";
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
    
    $query="select * from tbl_lezionicert where idlezionenorm='$idlezione' and iddocente='$iddocente'";
    $ris=mysqli_query($con,inspref($query)) or die ("Errore".inspref($query));
    
    
    if (mysqli_num_rows($ris)>0)
        return true;
    else
        return false;
}

function calcolaOrePermesso($iddoc,$con)
{
    $totaleore = 0;
    $query = "select * from tbl_richiesteferie where iddocente=$iddoc and concessione=1 and subject LIKE '%permesso breve%'";
    $risperm = mysqli_query($con, inspref($query)) or die("Errore: $query");
    while ($recperm = mysqli_fetch_array($risperm)) {

        $mail = $recperm['testomail'];
        $posizioneore = strpos($mail, "per un totale di ore") + 21;
        // PREPARAZIONE STRINGA SINTETICA RICHIESTA
        //$periodo = $recperm['subject'];
        //$posperiodo = strpos($testocompleto,"", $testocompleto)j
        //str_replace("");
        $oreperm = substr($mail, $posizioneore, 1);

        $totaleore += $oreperm;
    }
    return $totaleore;
}

function calcolaGiorniPermesso($iddoc,$con)
{
    $totalegiorni = 0;
    $query = "select * from tbl_richiesteferie where iddocente=$iddoc and concessione=1 and testomail LIKE '%Permesso retribuito%'";
    $risperm = mysqli_query($con, inspref($query)) or die("Errore: $query");
    while ($recperm = mysqli_fetch_array($risperm)) {

        $mail = $recperm['testomail'];
        $posizionegiorni = strpos($mail, "alla S.V. di assentarsi per n.") + 34;
        // PREPARAZIONE STRINGA SINTETICA RICHIESTA
        //$periodo = $recperm['subject'];
        //$posperiodo = strpos($testocompleto,"", $testocompleto)j
        //str_replace("");
        $giorniperm = substr($mail, $posizionegiorni, 1);
        // print "GP $giorniperm $posizionegiorni ";
        $totalegiorni += $giorniperm;
    }
    return $totalegiorni;
}

function calcolaGiorniFerie($iddoc,$con)
{
    $totalegiorni = 0;
    $query = "select * from tbl_richiesteferie where iddocente=$iddoc and concessione=1 and testomail LIKE '%Ferie%'";
    $risperm = mysqli_query($con, inspref($query)) or die("Errore: $query");
    while ($recperm = mysqli_fetch_array($risperm)) {

        $mail = $recperm['testomail'];
        $posizionegiorni = strpos($mail, "alla S.V. di assentarsi per n.") + 34;
        // PREPARAZIONE STRINGA SINTETICA RICHIESTA
        //$periodo = $recperm['subject'];
        //$posperiodo = strpos($testocompleto,"", $testocompleto)j
        //str_replace("");
        $giorniferie = substr($mail, $posizionegiorni, 1);
        //print "GF $giorniferie $posizionegiorni ";
        $totalegiorni += $giorniferie;
    }
    return $totalegiorni;
}