<?php

require_once '../lib/req_apertura_sessione.php';

@require_once("../lib/funzioni.php");
$suffisso = stringa_html('suffisso');
@require_once("../php-ini" . $suffisso . ".php");
if ($suffisso != "")
    $suff = $suffisso . "/";
else
    $suff = "";
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("errore query " . inspref($query, false));
require_once("../lib/req_assegna_parametri_a_sessione.php");
// TTTT Far acquisire i parametri in sessione

inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Invio dati", $_SESSION['nomefilelog'] . "rp", $suff);

//require_once '../lib/req_apertura_sessione.php';
//$_SESSION['nomefilelog']=$_SESSION['nomefilelog'];
//$_SESSION['suffisso']=$suffisso;$indirizzoip = IndirizzoIpReale();
//if (!$_SESSION['abilitata']=='yes')
//{
//   inserisci_log("LAMPSchool§" . date('m-d|H:i:s') ."§$indirizzoip §Richiesta trasmissione negata!", $_SESSION['nomefilelog']."rp");
//    die("Errore di mancata abilitazione!");
//
//}
$m1 = stringa_html("m1");
$m1md5 = $m1;
for ($cont = strlen($m1md5); $cont < 331; $cont++)
    $m1md5 .= "$";
//print ("ricevuti");


$dataoggi = date("Y-m-d");

$datamd5 = date("dmy");

$stringagenerazione = md5($_SESSION['nomefilelog']) . $m1md5 . $datamd5;
// inserisci_log("LAMPSchool§" . date('m-d|H:i:s') ."§$indirizzoip §Stringa di generazione:$stringagenerazione", $_SESSION['nomefilelog']."rp",$suff);

$chiavegenerata = md5($stringagenerazione);

$chiavericevuta = stringa_html('key');

inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$indirizzoip §Dati calcolo MD5: $stringagenerazione", $_SESSION['nomefilelog'] . "rp", $suff);


inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$indirizzoip §Chiave generata: $chiavegenerata Chiave ricevuta: $chiavericevuta ", $_SESSION['nomefilelog'] . "rp", $suff);


//if ($suffisso=='itt')
//{
if ($chiavegenerata != $chiavericevuta)
{
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$indirizzoip §MD5 Errato!", $_SESSION['nomefilelog'] . "rp", $suff);
    die("Errore MD5!");
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$m1", $_SESSION['nomefilelog'] . "rp", $suff);
} else
{
    if (trim($m1) == '')
    {
        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$indirizzoip §STRINGA M1 VUOTA!", $_SESSION['nomefilelog'] . "rp", $suff);
        die("Errore!");
    }
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$indirizzoip §INIZIO RICEZIONE", $_SESSION['nomefilelog'] . "rp", $suff);
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$m1", $_SESSION['nomefilelog'] . "rp", $suff);

//
// INSERIMENTO ASSENZE NELLA GIORNATA CHE SARANNO ELIMINATE CON LE TIMBRATURE
// 
//
//   Se non ci sono timbrature nella giornata (cioè primo invio nella giornata) 
//   inserisco le assenze per tutti
//
// VERIFICO SE CI SONO GIA' TIMBRATURE VALIDE
// NELLA GIORNATA PERCHE' SE E' LA PRIMA
// TIMBRATURA VALIDA OCCORRE INSERIRE LE ASSENZE
// PER TUTTI NELLA GIORNATA 



    $query = "select count(*) as numtimbrature from tbl_timbrature where datatimbratura='$dataoggi' and idalunno in(select idalunno from tbl_alunni where idclasse<>0)";
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "", $_SESSION['nomefilelog'] . "rp", $suff);

    if (!$ris = eseguiQuery($con, $query))
    {
        inserisci_log("Errore esecuzione query", $_SESSION['nomefilelog'] . "rp", $suff);
        die("errore query " . inspref($query, false));
    }

    $val = mysqli_fetch_array($ris);
    $numtimbrature = $val['numtimbrature'];

    $esiste_assenza = esiste_assenza($dataoggi, $con, $_SESSION['nomefilelog'], $suff);
    //$esiste_assenza=true;
    $arrtimb = array();

    $m1 = substr($m1, 0, strlen($m1) - 1); // ELIMINO il ; finale per non avere un elemento vuoto alla fine dell'array
    $arrtimb = explode(';', $m1);          // Metto nell'array delle timbratute tutte le timbrature dell'invio
    $gio = date('d');
    $mes = date('m');
    $anno = date('Y');

    $nuovetimbrature = 0;
    foreach ($arrtimb as $m2)
    {
        // Le timbrature sono del tipo:
        // [matricola][I/R/U]hhmm    [matricola] ha dimensioni variabili da 1 a 5
        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . "Stringa m2 $m2 ", $_SESSION['nomefilelog'] . "rp", $suff);

        $nuovetimbrature++;
        $postipo = 0;
        $postipo = strpos($m2, "I");
        if ($postipo == 0)
        {
            $postipo = strpos($m2, "R");
        }
        if ($postipo == 0)
        {
            $postipo = strpos($m2, "U");
        }
        if ($postipo == 0)
        {
            $postipo = strpos($m2, "E");
        }
        if ($postipo != 0)
        {
            $matricola = substr($m2, 0, $postipo);

            $tipo = substr($m2, $postipo, 1);

            $ora = substr($m2, $postipo + 1, 2);
            $min = substr($m2, $postipo + 3, 2);

            $esiste_alunno = esiste_alunno($matricola, $con, $_SESSION['nomefilelog'], $suff);

            if (!$esiste_alunno)
            {
                invia_mail($_SESSION['emailgestbadge'], "$suffisso Verificare dati timbrature", "Le timbrature contengono una matricola non presente in anagrafica: $matricola");
                inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . "INVIATA MAIL WARNING! ", $_SESSION['nomefilelog'] . "rp", $suff);
            }
            $timbratureinserite = $numtimbrature + $nuovetimbrature;
            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . "Numtimbrature $timbratureinserite ", $_SESSION['nomefilelog'] . "rp", $suff);
            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . "Data $dataoggi ", $_SESSION['nomefilelog'] . "rp", $suff);
            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . "Esiste alunno $esiste_alunno ", $_SESSION['nomefilelog'] . "rp", $suff);
            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . "Esiste assenza $esiste_assenza ", $_SESSION['nomefilelog'] . "rp", $suff);
            // Se è la prima timbratura valida della giornata e corrisponde ad un alunno esistente
            // e non ci sono ancora assenze inserite nella giornata inserisco le assenze per tutti che verranno cancellate man mano arrivano
            // le timbrature.
            if (($numtimbrature == 0) && ($dataoggi == "$anno-$mes-$gio") && ($esiste_alunno) && (!$esiste_assenza) && (!giorno_festa($dataoggi, $con)))
            {

                $query = "insert into tbl_assenze(idalunno,data,giustifica)
                      select idalunno,'$dataoggi','0'
                      from tbl_alunni
                      where idclasse<>0
                      and idalunno NOT IN (select idalunno from tbl_presenzeforzate where data = '" . date('Y-m-d') . "')
                      order by idalunno";

                inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "", $_SESSION['nomefilelog'] . "rp", $suff);
                if (!$ris = eseguiQuery($con, $query))
                {
                    inserisci_log("Errore esecuzione query", $_SESSION['nomefilelog'] . "rp", $suff);
                    die("errore query " . inspref($query, false));
                }

                $numtimbrature = 1;  //Imposto ad 1 le timbrature per non far reinserire le assenze
            }

            // TRASFORMO LA TIMBRATURA DI RITARDO IN TIMBRATURA DI USCITA
            // SE CI SONO TIMBRATURE Di INGRESSO FORZATE DELL'ALUNNO
            // NELLA STESSA GIORNATA
            if ($tipo == 'R')
            {
                $query = "select count(*) as numforzate from tbl_timbrature
            where idalunno=$matricola
            and datatimbratura='$anno-$mes-$gio'
            and forzata
            and (tipotimbratura='I' or tipotimbratura='R')
            and oratimbratura<'$ora:$min'";
                $ris = eseguiQuery($con, $query);
                $rec = mysqli_fetch_array($ris);
                if ($rec['numforzate'] > 0)
                {
                    $tipo = 'U';
                    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§VARIATO TIPO TIMBRATURA PER ALUNNO " . $matricola . "", $_SESSION['nomefilelog'] . "rp", $suff);
                }
            }
            // INSERISCO TIMBRATURA SE NON E' GIA' PRESENTE LA STESSA TIMBRATURA

            $query = "select * from tbl_timbrature where idalunno=$matricola and tipotimbratura='$tipo' and datatimbratura='$anno-$mes-$gio' and oratimbratura='$ora:$min'";
            $ris = eseguiQuery($con, $query);
            if (mysqli_num_rows($ris) == 0)
            {

                $query = "insert into tbl_timbrature(idalunno,tipotimbratura,datatimbratura,oratimbratura) values ('$matricola','$tipo','$anno-$mes-$gio','$ora:$min')";
                inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "", $_SESSION['nomefilelog'] . "rp", $suff);
                if (!$ris = eseguiQuery($con, $query))
                {
                    inserisci_log("Errore esecuzione query" . inspref($query, false), $_SESSION['nomefilelog'] . "rp", $suff);
                    //die("errore query " . inspref($query, false));
                }


                //
                // AGGIORNO LE TABELLE IN BASE ALLE TIMBRATURE
                //
            //

                if ($esiste_alunno)
                {
                    if ($tipo == 'I' | $tipo == 'E')
                    { // Errore nella registrazione sul badge della timbratura
                        $query = "delete from tbl_assenze where idalunno='$matricola' and data='$anno-$mes-$gio'";
                        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "", $_SESSION['nomefilelog'] . "rp", $suff);
                        if (!$ris = eseguiQuery($con, $query))
                        {
                            inserisci_log("Errore esecuzione query" . inspref($query, false), $_SESSION['nomefilelog'] . "rp", $suff);
                            //die("errore query " . inspref($query, false));
                        }
                        elimina_assenze_lezione($con, $matricola, "$anno-$mes-$gio");
                        if ($tipo == 'E')
                        {
                            invia_mail($_SESSION['emailgestbadge'], "$suffisso Timbratura errata E ", "In una timbratura c'è il tipo E: $m2");
                            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . "INVIATA MAIL WARNING! ", $_SESSION['nomefilelog'] . "rp", $suff);
                        }
                    }
                    if ($tipo == 'U')
                    {
                        $datausc = "$anno-$mes-$gio";
                        $orausc = "$ora:$min";
                        if ($_SESSION['giustificauscite'] == 'yes')
                        {
                            $valgiust = 'false';
                        } else
                        {
                            $valgiust = 'true';
                        }
                        $query = "insert into tbl_usciteanticipate(idalunno,data,orauscita,giustifica) values ('$matricola', '$datausc', '$orausc',$valgiust)";

                        if (!$ris = eseguiQuery($con, $query))
                        {
                            inserisci_log("Errore esecuzione query" . inspref($query, false), $_SESSION['nomefilelog'] . "rp", $suff);
                            // die("errore query " . inspref($query, false));
                        } else
                        {
                            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . " iduscita=" . mysqli_insert_id($con) . "", $_SESSION['nomefilelog'] . "rp", $suff);
                        }
                        //ricalcola_uscite($con, $matricola, $datausc);
                        elimina_assenze_lezione($con, $matricola, $datausc);
                        inserisci_assenze_per_ritardi_uscite($con, $matricola, $datausc);
                    }
                    if ($tipo == 'R' & $_SESSION['timbratureritardiabilitati'] == 'yes')
                    {
                        $dataent = "$anno-$mes-$gio";
                        $oraent = "$ora:$min";
                        $query = "insert into tbl_ritardi(idalunno,data,oraentrata,giustifica) values ('$matricola', '$dataent', '$oraent',0)";

                        if (!$ris = eseguiQuery($con, $query))
                        {
                            inserisci_log("Errore esecuzione query" . inspref($query, false), $_SESSION['nomefilelog'] . "rp", $suff);
                            //die("errore query " . inspref($query, false));
                        } else
                        {
                            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . " idritardo=" . mysqli_insert_id($con) . "", $_SESSION['nomefilelog'] . "rp", $suff);
                        }
                        $query = "delete from tbl_assenze where idalunno='$matricola' and data='$anno-$mes-$gio'";
                        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "", $_SESSION['nomefilelog'] . "rp", $suff);
                        if (!$ris = eseguiQuery($con, $query))
                        {
                            inserisci_log("Errore esecuzione query" . inspref($query, false), $_SESSION['nomefilelog'] . "rp", $suff);
                            //die("errore query " . inspref($query, false));
                        }
                        //ricalcola_ritardi($con, $matricola, $dataent);
                        elimina_assenze_lezione($con, $matricola, $dataent);
                        inserisci_assenze_per_ritardi_uscite($con, $matricola, $dataent);
                    }
                }
            } else
            {
                inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§OMESSO INSERIMENTO TIMBRATURA PER DUPLICAZIONE " . $matricola . "", $_SESSION['nomefilelog'] . "rp", $suff);
            }
        } else
        {
            invia_mail($_SESSION['emailgestbadge'], "$suffisso Timbratura malformata", "In una timbratura manca il tipo: $m2");
            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . "INVIATA MAIL WARNING! ", $_SESSION['nomefilelog'] . "rp", $suff);
        }
    }

    print ("ricevuti");
    mysqli_close($con);
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$indirizzoip §FINE RICEZIONE ", $_SESSION['nomefilelog'] . "rp", $suff);
}

function esiste_alunno($matricola, $conn, $nomefilelog, $suff)
{
    $query = "select * from tbl_alunni where idalunno='$matricola' and idclasse<>0";
    //inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "", $_SESSION['nomefilelog']."rp",$suff);
    if (!$ris = eseguiQuery($conn, $query))
    {
        inserisci_log("Errore esecuzione query" . inspref($query, false), $_SESSION['nomefilelog'] . "rp", $suff);
        //die("errore query " . inspref($query, false));
    }
    if (mysqli_num_rows($ris) != 0)
    {
        return true;
    } else
    {
        return false;
    }
}

function esiste_assenza($dataodierna, $conn, $nomefilelog, $suff)
{
    $query = "select * from tbl_assenze where data='$dataodierna'";
    //inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "", $_SESSION['nomefilelog']."rp",$suff);

    if (!$ris = eseguiQuery($conn, $query))
    {
        inserisci_log("Errore esecuzione query" . inspref($query, false), $_SESSION['nomefilelog'] . "rp", $suff);
        // die("errore query " . inspref($query, false));
    }
    if (mysqli_num_rows($ris) != 0)
    {
        return true;
    } else
    {

        return false;
    }
}
