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
                      select idlezione from tbl_lezioni where idclasse='$idclasse' and datalezione = '$datalezione')
                      and not forzata ";
        eseguiQuery($conn, $query);
        //  Eliminare tutte le assenze per la classe;
    } else
    {

        $query = "delete from tbl_asslezione where idalunno='$idalunno' and data='$datalezione' and not forzata ";
        eseguiQuery($conn, $query);
    }
}

function inserisci_assenze_per_ritardi_uscite($conn, $idalunno, $data)
{

    $idclasse = estrai_classe_alunno_data($idalunno, $data, $conn);
    $query = "select * from tbl_lezioni where  idclasse='$idclasse' and datalezione='$data'";
    $rislez = eseguiQuery($conn, $query);

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
            eseguiQuery($conn, $query);
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

  $ris = eseguiQuery($conn,$query);
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
  $risoraini = eseguiQuery($conn,$query);
  if ($recoraini = mysqli_fetch_array($risoraini))
  {
  $orarioini = $recoraini['inizio'];
  }

  $query = "select max(ora) as numore from tbl_orario where giorno='$gs' and valido";
  // print inspref($query);
  $risoretot = eseguiQuery($conn,$query);
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
  eseguiQuery($conn,$query);

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

  $ris = eseguiQuery($conn,$query);
  if ($rec = mysqli_fetch_array($ris))
  {

  $iduscita = $rec['iduscita'];

  $classe = estrai_classe_alunno_data($idalunno, $data, $conn);

  if ($classe != 0)
  {
  // ESTRAGGO L'ORA DI FINE GIORNATA DELLA CLASSE
  $orafin = estrai_ora_fine_giornata($data, $classe, $conn);

  // inserisci_log("TTTT Orafine $orafin \n", 3, "../lampschooldata/demo/00$_SESSION['nomefilelog'].log");
  if ($orafin != '')
  {

  $gs = numdagiorno(giorno_settimana($data));
  $orariofin = "";
  $query = "select fine from tbl_orario where giorno='$gs' and ora='$orafin' and valido";
  // print inspref($query);
  //  inserisci_log("TTTT Query $query \n", 3, "../lampschooldata/demo/00$_SESSION['nomefilelog'].log");
  $risorafin = eseguiQuery($conn,$query);
  if ($recorafin = mysqli_fetch_array($risorafin))
  {
  $orariofin = $recorafin['fine'];
  }
  //  inserisci_log("TTTT Orariofine $orariofin \n", 3, "../lampschooldata/demo/00$_SESSION['nomefilelog'].log");
  $query = "select max(ora) as numore from tbl_orario where giorno='$gs' and valido";
  // print inspref($query);
  $risoretot = eseguiQuery($conn,$query);
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

  // inserisci_log("TTTT Differenza $differenza \n", 3, "../lampschooldata/demo/00$_SESSION['nomefilelog'].log");
  // echo $differenza;
  $orediff = $differenza->format("%H");
  $mindiff = $differenza->format("%i");
  $minutidiff = $orediff * 60 + $mindiff;
  //   inserisci_log("TTTT Differenza $minutidiff \n", 3, "../lampschooldata/demo/00$_SESSION['nomefilelog'].log");
  $oreuscita = round($minutidiff / 60);
  // print "$minutidiff -> $oreritardo <br>";

  if ($oreuscita > 0)
  {
  if ($oreuscita < $oretot)
  {
  $query = "update tbl_usciteanticipate set numeroore=$oreuscita where iduscita=$iduscita";
  eseguiQuery($conn,$query);

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
  $risass = eseguiQuery($con,$queryass);
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

  $ris = eseguiQuery($con,$query);
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

  $ris = eseguiQuery($con,$query);
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
    $queryass = "select * from tbl_assenze where idalunno='$idalunno' and data='$data'";
    $risass = eseguiQuery($con, $queryass);
    if (mysqli_num_rows($risass) > 0)
    {
        return $durata;
    }

    
    $idclassealunno= estrai_classe_alunno_data($idalunno, $data, $con);
    if (gestione_manuale_assenze($idclassealunno, $data, $con))
    {
        return 0;
    }
    // VERIFICO SE CI SONO EVENTI (Ritardi o Uscite Anticipate) ALTRIMENTI RESTITUISCO 0
    else
    {
        $esistonoeventi = false;
        $eventi = array();
        $query = "select * from tbl_ritardi where idalunno='$idalunno' and data='$data'";
        $ris = eseguiQuery($con, $query);
        while ($rec = mysqli_fetch_array($ris))
        {
            $eventi[] = substr($rec['oraentrata'], 0, 5) . "R";
            $esistonoeventi = true;
        }
        $query = "select * from tbl_usciteanticipate where idalunno='$idalunno' and data='$data'";
        $ris = eseguiQuery($con, $query);
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
        $numeroeventi = count($eventi);
        // print "Numero eventi $numeroeventi";
        // foreach ($eventi as $evento)
        //    print "Evento $evento <br>";
        $iniziogiornata = orainizio(1, numero_giorno_settimana($data), $con);
        $query = "SELECT fine FROM tbl_orario WHERE giorno=" . numero_giorno_settimana($data) . " AND ora=
            (SELECT max(ora) FROM tbl_orario WHERE giorno=" . numero_giorno_settimana($data) . " AND valido)
            AND valido";
        $ris = eseguiQuery($con, $query);
        $rec = mysqli_fetch_array($ris);
        $finegiornata = $rec['fine'];

        $orainiziogiornata = date_create($iniziogiornata);
        $orafinegiornata = date_create($finegiornata);

        $presenzaminutigiornata = array();
        $puntaminuto = $orainiziogiornata;

        if (substr($eventi[0], 5, 1) == 'R')
        {
            $pres_ass = false;
        } else
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
                } else
                {
                    if (substr($eventi[$puntaevento], 5, 1) == 'R')
                    {
                        $pres_ass = true;
                    }
                }

                if ($puntaevento < ($numeroeventi - 1))
                    $puntaevento++;
                $presenzaminutigiornata[date_format($puntaminuto, "H:i")] = $pres_ass;
            } else
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
}

/**
 *
 * @param int $idalunno
 * @param int $idmateria
 * @param int $periodo
 * @param object $conn Connessione al db
 * @return string
 */
function calcola_ore_assenza($idalunno, $datainizio, $datafine, $conn)
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

    /*   $query = "select sum(oreassenza) as oretot from tbl_asslezione
      where idalunno='$idalunno' $seledata";
      $risass = eseguiQuery($conn, $query);
      $rec= mysqli_fetch_array($risass);
      $oreassenza=$rec['oretot']; */

    $arrayoreassenza = array();
    $query = "select tbl_asslezione.data as dataass,orainizio,oreassenza from tbl_asslezione,tbl_lezioni
                  where tbl_asslezione.idlezione=tbl_lezioni.idlezione
                  and idalunno='$idalunno' $seledata";

    $risass = eseguiQuery($conn, $query);
    while ($recass = mysqli_fetch_array($risass))
    {
        $dataass = substr($recass['dataass'], 5, 2) . substr($recass['dataass'], 8, 2);
        $orainizio = $recass['orainizio'];
        $orafine = $recass['orainizio'] - 1 + $recass['oreassenza'];
        for ($i = $orainizio; $i <= $orafine; $i++)
        {
            $strass = $dataass . $i;

            $arrayoreassenza[$strass] = 1;
        }
    }
    $oreassenza = count($arrayoreassenza);

    return $oreassenza;
}

function calcola_ore_deroga($idalunno, $datainizio, $datafine, $conn)
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
    $query = "select tbl_asslezione.data as dataass,orainizio,oreassenza from tbl_asslezione,tbl_lezioni
                  where tbl_asslezione.idlezione=tbl_lezioni.idlezione
                  and idalunno='$idalunno' $seledata and not tbl_asslezione.data in (select distinct data from tbl_deroghe where idalunno=$idalunno and numeroore=0)";

    $risass = eseguiQuery($conn, $query);
    while ($recass = mysqli_fetch_array($risass))
    {
        $dataass = substr($recass['dataass'], 5, 2) . substr($recass['dataass'], 8, 2);
        $orainizio = $recass['orainizio'];
        $orafine = $recass['orainizio'] - 1 + $recass['oreassenza'];
        for ($i = $orainizio; $i <= $orafine; $i++)
        {
            $strass = $dataass . $i;

            $arrayoreassenza[$strass] = 1;
        }
    }
    $oreassenzader = count($arrayoreassenza);

    return $oreassenzader;
}

function calcola_ore_deroga_oraria($idalunno, $datainizio, $datafine, $conn)
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
    $query = "select data, numeroore from tbl_deroghe where idalunno='$idalunno' and numeroore <> 0 $seledata";
    $risder = eseguiQuery($conn, $query);
    while ($recder = mysqli_fetch_array($risder))
    {
        $numorederoga = $recder['numeroore'];
        $data = data_italiana($recder['data']);

        $numoreassenza = calcola_ore_assenza($idalunno, $data, $data, $conn);
        if ($numoreassenza >= $numorederoga)
        {
            $oreassenzaperm += $numorederoga;
        } else
        {
            $oreassenzaperm += $numoreassenza;
        }
    }

    return $oreassenzaperm;
}

function calcola_ritardi_brevi($idalunno, $con, $ritardobreve, $rangedate = '')
{
    $numritardibrevi = 0;

    $query = "select * from tbl_ritardi where idalunno='$idalunno' $rangedate";
    $ris = eseguiQuery($con, $query);
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

        if ($mintotali < $_SESSION['ritardobreve'] & $mintotali > 0)
        {
            $numritardibrevi++;
        }
    }
    return $numritardibrevi;
}

function esiste_assenza_alunno($idalunno, $data, $con)
{
    $query = "select * from tbl_assenze where idalunno='$idalunno' and data='$data'";
    $ris = eseguiQuery($con, $query);
    if (mysqli_num_rows($ris) > 0)
    {
        return true;
    }

    return false;
}

function inserisciAmmonizioneGiustRitardi($idalunno, $iddocente, $datalimiteinferiore, $con)
{

    $dataammoniz = date('Y-m-d');
    $query = "SELECT idritardo,data FROM tbl_ritardi WHERE (isnull(giustifica) or giustifica=0) AND data< '$datalimiteinferiore'
            AND dataammonizione IS NULL AND idalunno=$idalunno ORDER BY data";
    $risass = eseguiQuery($con, $query);
    $elenco = "";
    while ($recass = mysqli_fetch_array($risass))
    {
        $elenco .= substr(data_italiana($recass['data']), 0, 5) . ", ";
    }

    $query = "UPDATE tbl_ritardi SET dataammonizione='" . date('Y-m-d') . "' WHERE (isnull(giustifica) or giustifica=0) AND data< '$datalimiteinferiore'
            AND dataammonizione IS NULL AND idalunno=$idalunno";
    
    eseguiQuery($con, $query);
    $elenco = substr($elenco, 0, strlen($elenco) - 1);
    if (strlen($elenco) > 7)
        $elenco = substr($elenco, 0, strlen($elenco) - 9) . " e " . substr($elenco, strlen($elenco) - 7, 6);

    $idclasse = estrai_classe_alunno($idalunno, $con);
    $sesso = estrai_sesso_alunno($idalunno, $con);
    //$iddocente = $_SESSION['idutente'];
    if (strlen($elenco) > 7)
        $numero = "ai ritardi";
    else
        $numero = "al ritardo";
    if ($sesso == 'F')
    {
        $oa = "a";
        $testo = elimina_apici("Con riferimento $numero del $elenco l'alunna $datialunno non ha portato la giustifica nei termini consentiti.");
    } else
    {
        $oa = "o";
        $testo = elimina_apici("Con riferimento $numero del $elenco l'alunno $datialunno non ha portato la giustifica nei termini consentiti.");
    }
    $provvedimenti = str_replace("[oa]", $oa, elimina_apici(estrai_testo_modificato("ammonizmancgiust", "[alunno]", $datialunno, $con)));
    $query = "INSERT INTO tbl_notealunno(idclasse,data,iddocente,testo,provvedimenti) values('$idclasse','$dataammoniz','$iddocente','$testo','$provvedimenti')";
    eseguiQuery($con, $query);
    $numnota = mysqli_insert_id($con);
    $query = "INSERT INTO tbl_noteindalu(idnotaalunno,idalunno) values('$numnota','$idalunno')";
    eseguiQuery($con, $query);
}

function inserisciAmmonizioneGiustAssenze($idalunno, $iddocente, $datalimiteinferiore, $con)
{
    $dataammoniz = date('Y-m-d');
    $query = "SELECT idassenza,data FROM tbl_assenze WHERE (isnull(giustifica) or giustifica=0) AND data< '$datalimiteinferiore'
            AND dataammonizione IS NULL AND idalunno=$idalunno ORDER BY data";
    $risass = eseguiQuery($con, $query);

    $elenco = "";
    while ($recass = mysqli_fetch_array($risass))
    {
        $elenco .= substr(data_italiana($recass['data']), 0, 5) . ", ";
    }
    print $elenco;
    if (strlen($elenco) > 7)
        $elenco = substr($elenco, 0, strlen($elenco) - 9) . " e " . substr($elenco, strlen($elenco) - 7, 6);
    $query = "UPDATE tbl_assenze SET dataammonizione='" . date('Y-m-d') . "' WHERE (isnull(giustifica) or giustifica=0) AND data< '$datalimiteinferiore'
            AND dataammonizione IS NULL AND idalunno=$idalunno";
    eseguiQuery($con, $query);
    $elenco = substr($elenco, 0, strlen($elenco) - 1);
    $idclasse = estrai_classe_alunno($idalunno, $con);
    $sesso = estrai_sesso_alunno($idalunno, $con);
    //$iddocente = $_SESSION['idutente'];
    if (strlen($elenco) > 7)
        $numero = "alle assenze";
    else
        $numero = "all'assenza";

    if ($sesso == 'F')
    {
        $testo = elimina_apici("Con riferimento $numero del $elenco l'alunna $datialunno non ha portato la giustifica nei termini consentiti.");
        $oa = "a";
    } else
    {
        $oa = "o";
        $testo = elimina_apici("Con riferimento $numero del $elenco l'alunno $datialunno non ha portato la giustifica nei termini consentiti.");
    }
    $provvedimenti = str_replace("[oa]", $oa, elimina_apici(estrai_testo_modificato("ammonizmancgiust", "[alunno]", $datialunno, $con)));
    $query = "INSERT INTO tbl_notealunno(idclasse,data,iddocente,testo,provvedimenti) values('$idclasse','$dataammoniz','$iddocente','$testo','$provvedimenti')";
    eseguiQuery($con, $query);
    $numnota = mysqli_insert_id($con);
    $query = "INSERT INTO tbl_noteindalu(idnotaalunno,idalunno) values('$numnota','$idalunno')";
    eseguiQuery($con, $query);
}

function lezione_dad($idclasse, $data, $con)
{    
    $query = "SELECT * from tbl_dad where idclasse=$idclasse and datadad='$data'";
    $ris = eseguiQuery($con, $query);

    if (mysqli_num_rows($ris)>0)
        return true;
    else
        return false;
}

function gestione_manuale_assenze($idclasse, $data, $con)
{
    if ($_SESSION['tipogestassenzelezione']=='man')
        return true;
    if ($_SESSION['tipogestassenzelezione']=='ibr' && lezione_dad($idclasse,$data,$con))
        return true;
    return false;
}