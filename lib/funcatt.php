<?php
/**
 * Created by PhpStorm.
 * User: pietro
 * Date: 16/06/15
 * Time: 18.26
 */
/**
 * Controllo nella tabella delle firme se il docente ha firmato
 *
 * @param int $idlezione
 * @param int $iddocente
 * @param object $conn Connessione al db
 * @return boolean true se esiste
 */
function esiste_firma($idlezione, $iddocente, $conn)
{
    $query = "SELECT * FROM tbl_firme WHERE idlezione=$idlezione AND iddocente=$iddocente";
    $risfirma = mysqli_query($conn, inspref($query));

    if (mysqli_num_rows($risfirma) > 0)
    {
        return true;
    }

    return false;
}

/**
 * Controllo nella tabella delle firme se il docente ha firmato
 *
 * @param int $idlezione
 * @param int $iddocente
 * @param object $conn Connessione al db
 * @return boolean true se esiste
 */
function esiste_cattedra($idlezione, $iddocente, $conn)
{
    $query = "SELECT * FROM tbl_lezioni,tbl_cattnosupp WHERE idlezione=$idlezione
             AND tbl_lezioni.idmateria=tbl_cattnosupp.idmateria
             AND tbl_lezioni.idclasse=tbl_cattnosupp.idclasse
             AND tbl_cattnosupp.iddocente=$iddocente";
    $risfirma = mysqli_query($conn, inspref($query));

    if (mysqli_num_rows($risfirma) > 0)
    {
        return true;
    }

    return false;
}

/**
 * Controllo se la cattedra è di sostegeno
 *
 * @param int $idcattedra
 * @param object $conn Connessione al db
 * @return boolean true se cattedra di sostegno
 */
function cattedra_sostegno($idcattedra, $conn)
{
    $query = "SELECT * FROM tbl_cattnosupp WHERE idcattedra='$idcattedra'";
    //print "TTTT ".inspref($query);
    $riscatt = mysqli_query($conn, inspref($query));
    $reccatt = mysqli_fetch_array($riscatt);

    if ($reccatt['idalunno'] != 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * Controllo se la cattedra è di sostegeno
 *
 * @param int $idcattedra
 * @param object $conn Connessione al db
 * @return boolean true se cattedra di sostegno
 */
function cattedra_sost($iddocente, $idmateria, $idclasse, $conn)
{
    $query = "SELECT * FROM tbl_cattnosupp WHERE iddocente=$iddocente AND idmateria=$idmateria AND idclasse=$idclasse ";
    //print "TTTT ".inspref($query);
    $riscatt = mysqli_query($conn, inspref($query));

    $reccatt = mysqli_fetch_array($riscatt);

    if ($reccatt['idalunno'] != 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * Controllo se il docente ha cattedre normali
 *
 * @param int $iddocente
 * @param object $conn Connessione al db
 * @return boolean true se cattedra di sostegno
 */
function cattedre_normali($iddocente, $conn)
{
    $query = "SELECT * FROM tbl_cattnosupp WHERE iddocente=$iddocente AND idalunno=0";
    // print "TTTT ".inspref($query);
    $riscatt = mysqli_query($conn, inspref($query)) or die("Errore ".inspref($query,false));

    if (mysqli_num_rows($riscatt) > 0)
    {
        return true;
    }

    return false;


}

/**
 * Controllo se il docente ha cattedre di sostegno
 *
 * @param int $iddocente
 * @param object $conn Connessione al db
 * @return boolean true se cattedra di sostegno
 */
function cattedre_sostegno($iddocente, $conn)
{
    $query = "SELECT * FROM tbl_cattnosupp WHERE iddocente=$iddocente AND idalunno<>0";
    //  print "TTTT ".inspref($query);
    $riscatt = mysqli_query($conn, inspref($query));

    if (mysqli_num_rows($riscatt) > 0)
    {
        return true;
    }

    return false;


}


/**
 * Controllo se una materia è seguita da un alunno nel caso sia
 * legata ad una cattedra speciale di gruppo
 *
 * @param int $idalunno ,$idmateria
 * @param object $conn Connessione al db
 * @return boolean true se alunno segue materia
 */
function segue_alunno_materia($idalunno, $idmateria, $conn)
{
    $stampa = true;
    $idclasse = estrai_classe_alunno($idalunno, $conn);
    $query = "select * from tbl_gruppialunni where
		            idalunno=$idalunno and idgruppo IN (
                    select distinct tbl_gruppi.idgruppo from tbl_gruppialunni,tbl_alunni,tbl_gruppi
                    where tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
                    and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
                    and tbl_alunni.idclasse=$idclasse
                    and tbl_gruppi.idmateria=$idmateria)";

    $risgrualu = mysqli_query($conn, inspref($query)) or die ("Errore: " . inspref($query));
    if (mysqli_num_rows($risgrualu) == 0)
    {
        $stampa = false;
    }

    return $stampa;


}

/**
 *
 * @param int $idalunno ,$iddocente,$idmateria
 * @param object $conn Connessione al db
 * @return int
 */
function trova_cattedra($idalunno, $iddocente, $idmateria, $conn)
{
    $codcatt = 0;
    $query = "select idcattedra from tbl_cattnosupp where idalunno=$idalunno and iddocente=$iddocente and idmateria=$idmateria";

    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);

    $codcatt = $rec['idcattedra'];

    return $codcatt;
}

/**
 *
 * @param string $cattedra
 * @param object $conn Connessione al db
 * @return string
 */
function estrai_id_materia($cattedra, $conn)
{
    $query = "select * from tbl_cattnosupp where idcattedra='$cattedra'";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $idmateria = $rec['idmateria'];

    return $idmateria;
}

/**
 *
 * @param string $cattedra
 * @param object $conn Connessione al db
 * @return string
 */
function estrai_id_classe($cattedra, $conn)
{
    $query = "select * from tbl_cattnosupp where idcattedra='$cattedra'";

    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $idclasse = $rec['idclasse'];

    return $idclasse;
}
/**
 * Fornisce il codice della cattedra
 *
 * @param int $iddocente
 * @param int $idclasse
 * @param int $idmateria
 * @param object $conn Connessione al db
 * @return int idcattedra
 */
function codice_cattedra($iddocente, $idclasse, $idmateria, $conn)
{
    $query = "select * from tbl_cattnosupp
	        where idclasse=$idclasse and iddocente=$iddocente and idmateria=$idmateria";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $idcattedra = $rec['idcattedra'];

    return $idcattedra;
}
/**
 *
 * @param int $iddocente
 * @param int $idclasse
 * @param object $conn Connessione al db
 * @return boolean
 */
function is_docente_classe($iddocente, $idclasse, $conn)
{
    $query = "select * from tbl_cattnosupp
	        where iddocente=$iddocente and idclasse=$idclasse and idalunno=0";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));

    if (mysqli_num_rows($ris) > 0)
    {
        return true;
    }

    return false;
}

/**
 *
 * @param int $iddocente
 * @param int $idclasse
 * @param object $conn Connessione al db
 * @return boolean
 */
function is_docente_sostegno_classe($iddocente, $idclasse, $conn)
{
    $query = "select * from tbl_cattnosupp
	        where iddocente=$iddocente and idclasse=$idclasse";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));

    if (mysqli_num_rows($ris) > 0)
    {
        return true;
    }

    return false;
}




/**
 *
 * @param int $idmateria
 * @param object $conn Connessione al db
 * @return string
 */
function decodifica_materia($idmateria, $conn)
{
    $query = "select * from tbl_materie where idmateria=$idmateria";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datimateria = $rec['denominazione'];

    return $datimateria;
}
