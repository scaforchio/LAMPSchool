<?php

/**
 * ELIMINA LE ASSENZE DELLE LEZIONI PER UN ALUNNO E UNA DATA SPECIFICATA
 *
 */
function elimina_assenze_lezione($conn, $idalunno, $datalezione, $idclasse = "")
{

    if ($idclasse != "")
    {
        $query = "delete from tbl_asslezione where idlezione in (
                      select idlezione from tbl_lezioni where idclasse=$idclasse and datalezione = '$datalezione')
                      and not forzata ";
        mysqli_query($conn, inspref($query)) or die("Errore in cancellazione assenze" . $query);
        //  Eliminare tutte le assenze per la classe;
    }
    else
    {
        
        $query = "delete from tbl_asslezione where idalunno=$idalunno and data='$datalezione' and not forzata ";
        mysqli_query($conn, inspref($query)) or die("Errore in cancellazione assenze" . $query);

    }


}


function inserisci_assenze_per_ritardi_uscite($conn, $idalunno, $data)
{

    $idclasse = estrai_classe_alunno_data($idalunno, $data, $conn);
    $query = "select * from tbl_lezioni where  idclasse=$idclasse and datalezione='$data'";
    $rislez = mysqli_query($conn, inspref($query));

    while ($reclez = mysqli_fetch_array($rislez))
    {
        $idlezione = $reclez['idlezione'];
        $durata = $reclez['numeroore'];
        $idmateria = $reclez['idmateria'];
        $inizio = $reclez['orainizio'];

        $numeroore = oreassenza($inizio, $durata, $idalunno, $data, $conn);
        if ($numeroore > 0)
        {
            $query = "insert into tbl_asslezione(idalunno, idlezione, idmateria, data, oreassenza) values ($idalunno,$idlezione,$idmateria,'$data',$numeroore)";
            mysqli_query($conn, inspref($query)) or die ("Errore inserimento assenza lezione" . inspref($query));
        }
    }
}


/*
 * RICALCOLA RITARDI PER INSERIMENTI DI RITARDI SENZA IL NUMERO DI ORE MA CON ORARIO CHE INCLUDE
 * LEZIONI SALTATE
 */
/*
function ricalcola_ritardi($conn, $idalunno, $data)
{

    $query = "SELECT * FROM tbl_ritardi WHERE idalunno=$idalunno and data='$data'";

    $ris = mysqli_query($conn, inspref($query)) or die ("Errore:". inspref($query, false));
    if ($rec = mysqli_fetch_array($ris))
    {

        $idritardo = $rec['idritardo'];

        $classe = estrai_classe_alunno_data($idalunno, $data, $conn);

        if ($classe != 0)
        {
            // ESTRAGGO L'ORA DI INIZIO GIORNATA DELLA CLASSE
            $oraini = estrai_ora_inizio_giornata($data, $classe, $conn);

            if ($oraini != '')
            {

                $gs = numdagiorno(giorno_settimana($data));
                $orarioini = "";
                $query = "select inizio from tbl_orario where giorno='$gs' and ora='$oraini' and valido";
                // print inspref($query);
                $risoraini = mysqli_query($conn, inspref($query));
                if ($recoraini = mysqli_fetch_array($risoraini))
                {
                    $orarioini = $recoraini['inizio'];
                }

                $query = "select max(ora) as numore from tbl_orario where giorno='$gs' and valido";
                // print inspref($query);
                $risoretot = mysqli_query($conn, inspref($query));
                if ($recoratot = mysqli_fetch_array($risoretot))
                {
                    $oretot = $recoratot['numore'];
                }
                else
                {
                    $oretot = 0;
                }

                // print "Ingresso: ".$rec['oraentrata']. " Ora entrata classe: $orarioini <br>";

                $ingressoclasse = $orarioini;
                $ingressoalunno = $rec['oraentrata'];
                if ($ingressoalunno>$ingressoclasse)
                {
                    $differenza = date_diff(date_create($ingressoclasse), date_create($ingressoalunno));

                    // echo $differenza;
                    $orediff = $differenza->format("%H");
                    $mindiff = $differenza->format("%i");
                    $minutidiff = $orediff * 60 + $mindiff;

                    $oreritardo = round($minutidiff / 60);
                    // print "$minutidiff -> $oreritardo <br>";

                    if ($oreritardo > 0)
                    {
                        if ($oreritardo < $oretot)
                        {
                            $query = "update tbl_ritardi set numeroore=$oreritardo where idritardo=$idritardo";
                            mysqli_query($conn, inspref($query));

                        }
                        //    else
                        //    {
                        //        print "<span style=\"color: red; \">Verifica ritardo di " . $oreritardo . " ore per alunno " . decodifica_alunno($idalunno, $conn) . " (" . decodifica_classe(estrai_classe_alunno($idalunno, $conn), $conn) . ") in data " . data_italiana($dataritardo) . " </span><br>";
                        //    }

                    }

                }
            }
        }
    }
}
*/

/*
 * RICALCOLA RITARDI PER INSERIMENTI DI RITARDI SENZA IL NUMERO DI ORE MA CON ORARIO CHE INCLUDE
 * LEZIONI SALTATE
 */
/*
function ricalcola_uscite($conn, $idalunno, $data)
{

    $query = "SELECT * FROM tbl_usciteanticipate WHERE data='$data' AND idalunno=$idalunno";

    $ris = mysqli_query($conn, inspref($query)) or die ("Errore:" . inspref($query, false));
    if ($rec = mysqli_fetch_array($ris))
    {

        $iduscita = $rec['iduscita'];

        $classe = estrai_classe_alunno_data($idalunno, $data, $conn);

        if ($classe != 0)
        {
            // ESTRAGGO L'ORA DI FINE GIORNATA DELLA CLASSE
            $orafin = estrai_ora_fine_giornata($data, $classe, $conn);

           // inserisci_log("TTTT Orafine $orafin \n", 3, "../lampschooldata/demo/00$nomefilelog.log");
            if ($orafin != '')
            {

                $gs = numdagiorno(giorno_settimana($data));
                $orariofin = "";
                $query = "select fine from tbl_orario where giorno='$gs' and ora='$orafin' and valido";
                // print inspref($query);
              //  inserisci_log("TTTT Query $query \n", 3, "../lampschooldata/demo/00$nomefilelog.log");
                $risorafin = mysqli_query($conn, inspref($query)) or die ("Errore:".inspref($query,false));
                if ($recorafin = mysqli_fetch_array($risorafin))
                {
                    $orariofin = $recorafin['fine'];
                }
              //  inserisci_log("TTTT Orariofine $orariofin \n", 3, "../lampschooldata/demo/00$nomefilelog.log");
                $query = "select max(ora) as numore from tbl_orario where giorno='$gs' and valido";
                // print inspref($query);
                $risoretot = mysqli_query($conn, inspref($query));
                if ($recoratot = mysqli_fetch_array($risoretot))
                {
                    $oretot = $recoratot['numore'];
                }
                else
                {
                    $oretot = 0;
                }

                // print "Ingresso: ".$rec['oraentrata']. " Ora entrata classe: $orarioini <br>";

                $uscitaclasse = $orariofin;
                $uscitaalunno = $rec['orauscita'];
                if ($uscitaclasse > $uscitaalunno)
                {
                    $differenza = date_diff(date_create($uscitaclasse), date_create($uscitaalunno));

                    // inserisci_log("TTTT Differenza $differenza \n", 3, "../lampschooldata/demo/00$nomefilelog.log");
                    // echo $differenza;
                    $orediff = $differenza->format("%H");
                    $mindiff = $differenza->format("%i");
                    $minutidiff = $orediff * 60 + $mindiff;
                 //   inserisci_log("TTTT Differenza $minutidiff \n", 3, "../lampschooldata/demo/00$nomefilelog.log");
                    $oreuscita = round($minutidiff / 60);
                    // print "$minutidiff -> $oreritardo <br>";

                    if ($oreuscita > 0)
                    {
                        if ($oreuscita < $oretot)
                        {
                            $query = "update tbl_usciteanticipate set numeroore=$oreuscita where iduscita=$iduscita";
                            mysqli_query($conn, inspref($query));

                        }
                        //   else
                        //   {
                        //       print "<span style=\"color: red; \">Verifica uscita di " . $oreuscita . " ore per alunno " . decodifica_alunno($idalunno, $conn) . " (" . decodifica_classe(estrai_classe_alunno($idalunno, $conn), $conn) . ") in data " . data_italiana($data) . " </span><br>";
                        //   }

                    }
                }

            }
        }
    }

}
*/

/**
 * RICALCOLA LE ORE DI ASSENZA NELLE LEZIONI PER ASSENZE INSERITE
 * PER UNA INTERA CLASSE
 */
function ricalcola_assenze_lezioni_classe($conn, $idclasse, $data)
{

    $elencoalunni = estrai_alunni_classe_data($idclasse, $data, $conn);

    //elimino parentesi iniziale e finale

    $elencoalunni = str_replace("(", "", $elencoalunni);
    $elencoalunni = str_replace(")", "", $elencoalunni);

    $alunni = explode(",", $elencoalunni);
    elimina_assenze_lezione($conn, 0, $data, $idclasse);
    foreach ($alunni as $alu)
    {

        inserisci_assenze_per_ritardi_uscite($conn, $alu, $data);
        //ricalcola_uscite($conn, $alu, $data);
        //ricalcola_ritardi($conn, $alu, $data);

    }

}

/*
function oreassenzaold($inizio, $durata, $idalunno, $data, $con)
{


    // SE ASSENTE RESTITUISCO TUTTE LE ORE
    $queryass = "select * from tbl_assenze where idalunno=$idalunno and data='$data'";
    $risass = mysqli_query($con, inspref($queryass)) or die ("Errore nella query: " . mysqli_error($con));
    if (mysqli_num_rows($risass) > 0)
    {
        return $durata;
    }
    else
    {
        $iniziolezione = orainizio($inizio, numero_giorno_settimana($data), $con);
        $finelezione = orafine($inizio + $durata - 1, numero_giorno_settimana($data), $con);


        // print "<br>tttt inizio:$iniziolezione  fine:$finelezione";
        $oreassenzatot = 0;
        // VERIFICO LE ORE IN BASE A RITARDI

        $query = "SELECT * FROM tbl_ritardi WHERE idalunno=$idalunno and data='$data'";

        $ris = mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query, false));
        if ($rec = mysqli_fetch_array($ris))
        {

            $ingressoalunno = $rec['oraentrata'];

            if ($ingressoalunno > $iniziolezione)
            {
                $differenza = date_diff(date_create($ingressoalunno), date_create($iniziolezione));

                // echo $differenza;
                $orediff = $differenza->format("%H");
                $mindiff = $differenza->format("%i");
                $minutidiff = $orediff * 60 + $mindiff;

                $oreritardo = round($minutidiff / 60);
                // print "$minutidiff -> $oreritardo <br>";
               // print "<br>tttt ingresso:$ingressoalunno  $minutidiff oreritardo: $oreritardo";
                if ($oreritardo >= $durata)
                {
                    return $durata;
                }
                else
                {
                    $oreassenzatot += $oreritardo;
                }
            }
        }

        // VERIFICO LE ORE IN BASE A USCITE ANTICIPATE

        $query = "SELECT * FROM tbl_usciteanticipate WHERE idalunno=$idalunno and data='$data'";

        $ris = mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query, false));
        if ($rec = mysqli_fetch_array($ris))
        {
            // print "<br>tttt qui";
            $uscitaalunno = $rec['orauscita'];
            // print "tttt <br> uscita $uscitaalunno";
            if ($uscitaalunno < $finelezione)
            {
                $differenza = date_diff(date_create($uscitaalunno), date_create($finelezione));
                // echo $differenza;
                $orediff = $differenza->format("%H");


                $mindiff = $differenza->format("%i");
                $minutidiff = $orediff * 60 + $mindiff;

                $oreanticipo = round($minutidiff / 60);
                // print "$minutidiff -> $oreritardo <br>";
               // print "<br>tttt uscita:$uscitaalunno  $minutidiff oreritardo: $oreanticipo";
                if ($oreanticipo >= $durata)
                {
                    return $durata;
                }
                else
                {
                    $oreassenzatot += $oreanticipo;
                }
            }
        }
        return $oreassenzatot;
    }
}
*/

function oreassenza($inizio, $durata, $idalunno, $data, $con)
{
    // echo "$inizio $durata";
    $queryass = "select * from tbl_assenze where idalunno=$idalunno and data='$data'";
    $risass = mysqli_query($con, inspref($queryass)) or die ("Errore nella query: " . mysqli_error($con));
    if (mysqli_num_rows($risass) > 0)
    {
        return $durata;
    }

    // VERIFICO SE CI SONO EVENTI (Ritardi o Uscite Anticipate) ALTRIMENTI RESTITUISCO 0

    $esistonoeventi = false;
    $eventi = array();
    $query = "select * from tbl_ritardi where idalunno=$idalunno and data='$data'";
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
    while ($rec = mysqli_fetch_array($ris))
    {
        $eventi[] = substr($rec['oraentrata'], 0, 5) . "R";
        $esistonoeventi = true;
    }
    $query = "select * from tbl_usciteanticipate where idalunno=$idalunno and data='$data'";
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
    while ($rec = mysqli_fetch_array($ris))
    {
        $eventi[] = substr($rec['orauscita'], 0, 5) . "U";
        $esistonoeventi = true;
    }
    if (!$esistonoeventi)
    {
        return 0;
    }


    // CALCOLO LE ORE DI ASSENZA PER LA LEZIONE
    sort($eventi);
    $numeroeventi=count($eventi);
    // print "Numero eventi $numeroeventi";
     // foreach ($eventi as $evento)
     //    print "Evento $evento <br>";
    $iniziogiornata = orainizio(1, numero_giorno_settimana($data), $con);
    $query = "SELECT fine FROM tbl_orario WHERE giorno=" . numero_giorno_settimana($data) . " AND ora=
            (SELECT max(ora) FROM tbl_orario WHERE giorno=" . numero_giorno_settimana($data) . " AND valido)
            AND valido";
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $finegiornata = $rec['fine'];


    $orainiziogiornata = date_create($iniziogiornata);
    $orafinegiornata = date_create($finegiornata);


    $presenzaminutigiornata = array();
    $puntaminuto = $orainiziogiornata;


    if (substr($eventi[0], 5, 1) == 'R')
    {
        $pres_ass = false;
    }
    else
    {
        $pres_ass = true;
    }
    $puntaevento = 0;
    while ($puntaminuto < $orafinegiornata)
    {

        if ($puntaminuto == date_create(substr($eventi[$puntaevento], 0, 5)))
        {
            if ($pres_ass)
            {
                if (substr($eventi[$puntaevento], 5, 1) == 'U')
                {
                    $pres_ass = false;
                }
            }
            else
            {
                if (substr($eventi[$puntaevento], 5, 1) == 'R')
                {
                    $pres_ass = true;
                }
            }

            if ($puntaevento<($numeroeventi-1))
                $puntaevento++;
            $presenzaminutigiornata[date_format($puntaminuto, "H:i")] = $pres_ass;
        }
        else
        {
            $presenzaminutigiornata[date_format($puntaminuto, "H:i")] = $pres_ass;
        }
       //  print date_format($puntaminuto,"H:i")." ".$pres_ass. " <br>";
        date_add($puntaminuto, date_interval_create_from_date_string("1 minutes"));

    }


    // Conto il numero di minuti di assenza per la lezione

    $iniziolezione = substr(orainizio($inizio, numero_giorno_settimana($data), $con), 0, 5);
    $finelezione = substr(orafine($inizio + $durata - 1, numero_giorno_settimana($data), $con), 0, 5);
    //print "Inizio: $iniziolezione Fine: $finelezione";
    $puntaminutolezione = $iniziolezione;
    $numminutiassenza = 0;
    $numminutilezione = 0;
    while ($puntaminutolezione < $finelezione)
    {
        if (!$presenzaminutigiornata[$puntaminutolezione])
        {
            $numminutiassenza++;
        }
        $pml = date_create($puntaminutolezione);
        date_add($pml, date_interval_create_from_date_string("1 minutes"));
        $puntaminutolezione = date_format($pml, 'H:i');
        $numminutilezione++;
    }

    //print "Minuti assenza $numminutiassenza Minuti lezione $numminutilezione <br>";
    $oreassenza = ($numminutiassenza / $numminutilezione) * $durata;
    $oreassarrot = round($oreassenza);
    return $oreassarrot;
    // print " Ore ass:$oreassarrot";
    // print " Minuti assenza $numminutiassenza <br>";


}


/**
 *
 * @param int $idalunno
 * @param int $idmateria
 * @param int $periodo
 * @param object $conn Connessione al db
 * @return string
 */
function calcola_ore_assenza($idalunno, $datainizio,$datafine,$conn)
{


    $seledata = "";

    if ($datainizio != "")
    {
        $seledata = $seledata . " and data >= '" . data_to_db($datainizio) . "' ";

    }

    if ($datafine != "")
    {
        $seledata = $seledata . " and data <= '" . data_to_db($datafine) . "' ";

    }

    $arrayoreassenza = array();
    $query = "select tbl_asslezione.data as dataass,orainizio,numeroore from tbl_asslezione,tbl_lezioni
                  where tbl_asslezione.idlezione=tbl_lezioni.idlezione
                  and idalunno='$idalunno' $seledata";
    $risass = mysqli_query($conn, inspref($query)) or die("Errore: " . inspref($query, false));
    while ($recass = mysqli_fetch_array($risass))
    {
        $dataass = substr($recass['dataass'], 5, 2) . substr($recass['dataass'], 8, 2);
        $orainizio = $recass['orainizio'];
        $orafine = $recass['orainizio'] - 1 + $recass['numeroore'];
        for ($i = $orainizio; $i <= $orafine; $i++)
        {
            $strass = $dataass . $i;

            $arrayoreassenza[$strass] = 1;
        }
    }
    $oreassenza = count($arrayoreassenza);

    return $oreassenza;

}

function calcola_ore_deroga($idalunno, $datainizio,$datafine,$conn)
{

    $seledata = "";

    if ($datainizio != "")
    {
        $seledata = $seledata . " and data >= '" . data_to_db($datainizio) . "' ";

    }

    if ($datafine != "")
    {
        $seledata = $seledata . " and data <= '" . data_to_db($datafine) . "' ";

    }

    $arrayoreassenza = array();
    $query = "select tbl_asslezione.data as dataass,orainizio,numeroore from tbl_asslezione,tbl_lezioni
                  where tbl_asslezione.idlezione=tbl_lezioni.idlezione
                  and idalunno='$idalunno' $seledata and not tbl_asslezione.data in (select distinct data from tbl_deroghe where idalunno=$idalunno and numeroore=0)";
    $risass = mysqli_query($conn, inspref($query)) or die("Errore: " . inspref($query, false));
    while ($recass = mysqli_fetch_array($risass))
    {
        $dataass = substr($recass['dataass'], 5, 2) . substr($recass['dataass'], 8, 2);
        $orainizio = $recass['orainizio'];
        $orafine = $recass['orainizio'] - 1 + $recass['numeroore'];
        for ($i = $orainizio; $i <= $orafine; $i++)
        {
            $strass = $dataass . $i;

            $arrayoreassenza[$strass] = 1;
        }
    }
    $oreassenzader = count($arrayoreassenza);

    return $oreassenzader;

}


function calcola_ore_deroga_oraria($idalunno, $datainizio,$datafine,$conn)
{

    $seledata = "";

    if ($datainizio != "")
    {
        $seledata = $seledata . " and data >= '" . data_to_db($datainizio) . "' ";

    }

    if ($datafine != "")
    {
        $seledata = $seledata . " and data <= '" . data_to_db($datafine) . "' ";

    }

    $oreassenzaperm = 0;
    $query = "select data, numeroore from tbl_deroghe where idalunno=$idalunno and numeroore <> 0 $seledata";
    $risder = mysqli_query($conn, inspref($query)) or die ("Errore: " . inspref($query, false));
    while ($recder = mysqli_fetch_array($risder))
    {
        $numorederoga = $recder['numeroore'];
        $data = data_italiana($recder['data']);

        $numoreassenza = calcola_ore_assenza($idalunno,$data,$data,$conn);
        if ($numoreassenza >= $numorederoga)
        {
            $oreassenzaperm += $numorederoga;
        }
        else
        {
            $oreassenzaperm += $numoreassenza;
        }
    }

    return $oreassenzaperm;

}

function calcola_ritardi_brevi($idalunno, $con, $ritardobreve, $rangedate='')
{
    $numritardibrevi = 0;

    $query = "select * from tbl_ritardi where idalunno=$idalunno $rangedate";
    $ris = mysqli_query($con, inspref($query));
    while ($rec = mysqli_fetch_array($ris))
    {
        $giornosettimana = numero_giorno_settimana($rec['data']);
        //$orainizio = 1;
        $orainizio = estrai_ora_inizio_giornata($rec['data'], estrai_classe_alunno_data($idalunno, $rec['data'], $con), $con);

        $iniziogiornata = orainizio($orainizio, $giornosettimana, $con);
        $oraentrata = $rec['oraentrata'];

        $oraing = substr($iniziogiornata, 0, 2);
        $mining = substr($iniziogiornata, 3, 2);
        $oraent = substr($oraentrata, 0, 2);
        $minent = substr($oraentrata, 3, 2);

        $toting = $oraing * 60 + $mining;
        $totent = $oraent * 60 + $minent;
        $mintotali = $totent - $toting;

        if ($mintotali < $ritardobreve & $mintotali > 0)
        {
            $numritardibrevi++;
        }

    }
    return $numritardibrevi;
}

