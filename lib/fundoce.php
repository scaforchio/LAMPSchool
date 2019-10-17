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
    $ris = eseguiQuery($conn, $query);
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
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);
    $maildocente = $rec['email'];

    return $maildocente;
}



function estrai_cell_docente($iddocente, $conn)
{
    $query = "select * from tbl_docenti where iddocente='$iddocente'";
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);
    $cellulare = $rec['telcel'];

    return $cellulare;
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
    $ris = eseguiQuery($conn, $query);
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
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);
    $datidocente = $rec['sostegno'];

    return $datidocente;
}



function docente_gestore_moodle($iddocente, $conn)
{
    $query = "select * from tbl_docenti where iddocente='$iddocente'";
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);
    $datidocente = $rec['gestoremoodle'];

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
    $ris = eseguiQuery($conn, $query);
    if (mysqli_num_rows($ris) > 0)
    {
        return true;
    } else
    {
        return false;
    }
}

function verifica_classe_coordinata($iddocente, $idclasse, $conn)
{
    $query = "select * from tbl_classi where idcoordinatore='$iddocente' and idclasse='$idclasse'";
    $ris = eseguiQuery($conn, $query);
    if (mysqli_num_rows($ris) > 0)
    {
        return true;
    } else
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
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);
    $datidirigente = $rec['nome'] . " " . $rec['cognome'];

    return $datidirigente;
}

function lezione_sostegno($idlezione, $iddocente, $con)
{

    $query = "select * from tbl_lezionicert where idlezionenorm='$idlezione' and iddocente='$iddocente'";
    $ris = eseguiQuery($con,$query);


    if (mysqli_num_rows($ris) > 0)
        return true;
    else
        return false;
}

function calcolaOrePermesso($iddoc, $con)
{
    $totaleore = 0;
    $query = "select * from tbl_richiesteferie where iddocente=$iddoc and concessione=1 and not annullata and subject LIKE '%permesso breve%'";
    $risperm = eseguiQuery($con, $query);
    while ($recperm = mysqli_fetch_array($risperm))
    {

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

function calcolaGiorniPermesso($iddoc, $con)
{
    $totalegiorni = 0;
    $query = "select * from tbl_richiesteferie where iddocente=$iddoc and concessione=1 and not annullata and testomail LIKE '%Permesso retribuito%'";
    $risperm = eseguiQuery($con, $query);
    while ($recperm = mysqli_fetch_array($risperm))
    {

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

function calcolaGiorniFerie($iddoc, $con)
{
    $totalegiorni = 0;
    $query = "select * from tbl_richiesteferie where iddocente=$iddoc and concessione=1 and not annullata and testomail LIKE '%Ferie%'";
    $risperm = eseguiQuery($con, $query);
    while ($recperm = mysqli_fetch_array($risperm))
    {

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

function contaOreRecuperate($iddoc, $con)
{

    $query = "select sum(numeroore) as recuperate from tbl_recuperipermessi where iddocente=$iddoc";
    $risperm = eseguiQuery($con, $query);
    $recperm = mysqli_fetch_array($risperm);
    return $recperm['recuperate'];
}

function contaOrePermesso($iddoc, $con)
{
    $totaleore = 0;
    $query = "select * from tbl_richiesteferie where iddocente=$iddoc and concessione=1 and not annullata and subject LIKE '%permesso breve%'";
    $risperm = eseguiQuery($con, $query);
    while ($recperm = mysqli_fetch_array($risperm))
    {
        $oreperm = $recperm['orepermessobreve'];
        $totaleore += $oreperm;
    }
    return $totaleore;
}

function contaGiorniPermesso($iddoc, $con)
{
    $totalegiorni = 0;
    $query = "select * from tbl_richiesteferie where iddocente=$iddoc and concessione=1 and not annullata and testomail LIKE '%Permesso retribuito%'";
    $risperm = eseguiQuery($con, $query);
    while ($recperm = mysqli_fetch_array($risperm))
    {


        $giorniperm = $recperm['numerogiorni'];
        // print "GP $giorniperm $posizionegiorni ";
        $totalegiorni += $giorniperm;
    }
    return $totalegiorni;
}

function contaGiorniFerie($iddoc, $con)
{
    $totalegiorni = 0;
    $query = "select * from tbl_richiesteferie where iddocente=$iddoc and concessione=1 and not annullata and testomail LIKE '%Ferie%'";
    $risperm = eseguiQuery($con, $query);
    while ($recperm = mysqli_fetch_array($risperm))
    {


        $giorniferie = $recperm['numerogiorni'];

        $totalegiorni += $giorniferie;
    }
    return $totalegiorni;
}
