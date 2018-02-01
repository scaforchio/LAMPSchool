<?php
/**
 * Created by PhpStorm.
 * User: pietro
 * Date: 16/06/15
 * Time: 18.34
 */

/**
 *
 * @param string $idalunno
 * @param object $conn Connessione al db
 * @return string
 */
function estrai_dati_alunno($idalunno, $conn)
{
    $query = "select * from tbl_alunni where idalunno='$idalunno'";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore: " . inspref($query));
    $rec = mysqli_fetch_array($ris);
    if ($rec['datanascita'] != '0000-00-00')
    {
        $datialunno = $rec['cognome'] . " " . $rec['nome'] . " (" . data_italiana($rec['datanascita']) . ")";
    }
    else
    {
        $datialunno = $rec['cognome'] . " " . $rec['nome'];
    }

    return $datialunno;
}

/**
 *
 * @param int $idalunno
 * @param object $conn Connessione al db
 * @return string
 */
function estrai_dati_alunno_rid($idalunno, $conn)
{
    $query = "select * from tbl_alunni where idalunno=$idalunno";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datialunno = $rec['cognome'] . " " . $rec['nome'];
    return $datialunno;
}


/**
 *
 * @param int $idalunno
 * @param object $conn Connessione al db
 * @return boolean
 */
function alunno_certificato($idalunno, $conn)
{
    $query = "select certificato from tbl_alunni where idalunno=$idalunno";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datialunno = $rec['certificato'];
    return $datialunno;
}

/**
 *
 * @param int $idalunno , int $idmateria
 * @param object $conn Connessione al db
 * @return boolean
 */
function alunno_certificato_pei($idalunno, $idmateria, $conn)
{
    $datialunno = 0;
    $query = "select certificato from tbl_alunni where idalunno=$idalunno";

    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    if ($rec['certificato'])
    {
        $query = "select tipoprogr from tbl_tipoprog where idalunno=$idalunno and idmateria=$idmateria";

        $ris2 = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
        $rec2 = mysqli_fetch_array($ris2);

        if ($rec2['tipoprogr'] == 'P')
        {
            $datialunno = 1;
        }
    }

    return $datialunno;
}

/**
 *
 * @param int $idalunno , int $idmateria
 * @param object $conn Connessione al db
 * @return boolean
 */
function alunno_certificato_ob_min($idalunno, $idmateria, $conn)
{
    $datialunno = 0;
    $query = "select certificato from tbl_alunni where idalunno=$idalunno";

    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    if ($rec['certificato'])
    {
        $query = "select tipoprogr from tbl_tipoprog where idalunno=$idalunno and idmateria=$idmateria";

        $ris2 = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
        $rec2 = mysqli_fetch_array($ris2);

        if ($rec2['tipoprogr'] == 'O')
        {
            $datialunno = 1;
        }
    }

    return $datialunno;
}

/**
 *
 * @param int $idclasse , data $data
 * @param object $conn Connessione al db
 * @return stringa da usare in clausola in con elenco di tutti gli alunni della classe in una determinata data
 */
function estrai_alunni_classe_data($idclasse, $data, $conn)
{
    $elenco = "";
    // print "ttt $data <br>";
    // SE LA DATA E' QUELLA ODIERNA POSSO SELEZIONARE DALLA COMPOSIZIONE DELLA CLASSE

    if ($data == date("Y-m-d"))
    {
        $query = "select idalunno from tbl_alunni where idclasse=$idclasse";
        $ris = mysqli_query($conn, inspref($query));
        while ($rec = mysqli_fetch_array($ris))
        {
            $elenco .= $rec['idalunno'] . ",";
        }

    }
    else  // DEVO VEDERE GLI ALUNNI PRESENTI NELLA DATA NELLA CLASSE
    {
        // AGGIUNGO TUTTI GLI ALUNNI CHE NON HANNO CAMBIAMENTI DI CLASSE SUCCESSIVI
        // ALLA DATA SPECIFICATA
        $query = "select idalunno from tbl_alunni alu where idclasse=$idclasse
                and not exists (select * from tbl_cambiamenticlasse where idalunno=alu.idalunno
                                   and datafine>='$data')";
        //   print inspref($query);
        $ris = mysqli_query($conn, inspref($query)) or die("Errore:" . inspref($query, false));
        while ($rec = mysqli_fetch_array($ris))
        {
            $elenco .= $rec['idalunno'] . ",";
        }

        // AGGIUNGO TUTTI GLI ALUNNI CHE HANNO AVUTO LA CLASSE IN QUELLA DATA
        // LA QUERY CERCA GLI idalunno degli alunni

        $query = "select idalunno,datafine from tbl_cambiamenticlasse camb where idclasse=$idclasse
                and datafine>'$data' and not exists (select * from tbl_cambiamenticlasse
                                  where idalunno=camb.idalunno
                                  and datafine>'$data' and datafine<camb.datafine) ";
        // print inspref($query,false);
        $ris = mysqli_query($conn, inspref($query)) or die("Errore:" . inspref($query, false));
        while ($rec = mysqli_fetch_array($ris))
        {
            $elenco .= $rec['idalunno'] . ",";
        }


    }
    // ELIMINO L'ULTIMA VIRGOLA
    $elenco = substr($elenco, 0, strlen($elenco) - 1);
    return $elenco;
}


/**
 *
 * @param int $idalunno , data $data
 * @param object $conn Connessione al db
 * @return int classe di appartenenza di un alunno in una data
 */
function estrai_classe_alunno_data($idalunno, $data, $conn)
{
    $idclasse = "";

    // SE LA DATA E' QUELLA ODIERNA POSSO SELEZIONARE DIRETTAMENTE LA CLASSE
    if ($data == date("Y-m-d"))
    {
        return estrai_classe_alunno($idalunno, $conn);

    }
    else  // DEVO VEDERE LA CLASSE DI APPARTENENZA NELLA DATA SPECIFICATA
    {

        // VERIFICO SE L'ALUNNO NON HA TRASFERIMENTI SUCCESIVI ALLA DATA
        $query = "select * from tbl_cambiamenticlasse where idalunno=$idalunno
                       and datafine>'$data' order by datafine";
        $ris = mysqli_query($conn, inspref($query)) or die("Errore:" . inspref($query, false));
        if (mysqli_num_rows($ris) == 0)
        {
            return estrai_classe_alunno($idalunno, $conn);
        }
        else // LA CLASSE DEL PRIMO RECORD TROVATO SARA' LA CLASSE DI APPARTENENZA DELL'ALUNNO
        {

            $rec = mysqli_fetch_array($ris);
            return $rec['idclasse'];

        }
    }

}


/**
 *
 * @param int $idalunno , int $idmateria
 * @param object $conn Connessione al db
 * @return boolean
 */
function alunno_certificato_norm($idalunno, $idmateria, $conn)
{
    $datialunno = 0;
    $query = "select certificato from tbl_alunni where idalunno=$idalunno";

    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    if ($rec['certificato'])
    {
        $query = "select tipoprogr from tbl_tipoprog where idalunno=$idalunno and idmateria=$idmateria";

        $ris2 = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));

        $rec2 = mysqli_fetch_array($ris2);

        if ($rec2['tipoprogr'] == 'N' | $rec2['tipoprogr'] == '')
        {
            $datialunno = 1;
        }

    }

    return $datialunno;
}

/**
 *
 * @param int $cattedra
 * @param object $conn Connessione al db
 * @return boolean
 */
function estrai_alunno_da_cattedra_pei($idcattedra, $conn)
{
    $codalu = 0;
    $query = "select idalunno from tbl_cattnosupp where idcattedra=$idcattedra";

    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);

    $codalu = $rec['idalunno'];

    return $codalu;
}



/**
 *
 * @param int $idalunno
 * @param object $conn Connessione al db
 * @return int
 */
function estrai_classe_alunno($idalunno, $conn)
{
    $query = "select idclasse from tbl_alunni where idalunno='$idalunno'";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query : " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $classealunno = $rec['idclasse'];

    return $classealunno;
}



/**
 *
 * @param int $idalunno , int $idmateria
 * @param object $conn Connessione al db
 * @return char
 */
function estrai_tipo_prog($idalunno, $idmateria, $conn)
{
    $query = "select tipoprogr from tbl_tipoprog where idalunno=$idalunno and idmateria=$idmateria";

    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    if ($rec = mysqli_fetch_array($ris))
    {
        $dato = $rec['tipoprogr'];
    }
    else
    {
        $dato = "";
    }
    return $dato;
}

/**
 *
 * @param int $idalunno
 * @param object $conn Connessione al db
 * @return string
 */
function decodifica_alunno($idalunno, $conn)
{
    $query = "select cognome,nome from tbl_alunni where idalunno=$idalunno";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datialunno = $rec['cognome'] . " " . $rec['nome'];

    return $datialunno;
}


/**
 *
 * @param int $idalunno
 * @param object $conn Connessione al db
 * @return string
 */

function estrai_alunno_data($idalunno, $conn)
{
    $query = "select cognome, nome, datanascita from tbl_alunni where idalunno=$idalunno";
    $ris = mysqli_query($conn, inspref($query)) or die ("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $datialunno = $rec['cognome'] . " " . $rec['nome'] . " (" . data_italiana($rec['datanascita']) . ")";

    return $datialunno;
}
