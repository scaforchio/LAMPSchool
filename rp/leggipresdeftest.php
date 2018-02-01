<?php
// session_start();
@require_once("../lib/funzioni.php");
$suffisso = stringa_html('suffisso');
@require_once("../php-ini" . $suffisso . ".php");



$m1 = stringa_html("m1");
//print ("ricevuti");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("errore query " . inspref($query, false));
$dataoggi = date("Y-m-d");
$indirizzoip=IndirizzoIpReale();

if ($suffisso != "")
{
    $suff = $suffisso . "/";
}
else
{
    $suff = "";
}
inserisci_log("LAMPSchool§" . date('m-d|H:i:s') ."§$indirizzoip §INIZIO RICEZIONE \n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");

inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§$m1\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");


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
inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");

if (!$ris = mysqli_query($con, inspref($query, false)))
{
    inserisci_log("Errore esecuzione query\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
    die("errore query " . inspref($query, false));
}

$val = mysqli_fetch_array($ris);
$numtimbrature = $val['numtimbrature'];

$esiste_assenza = esiste_assenza($dataoggi, $con, $suff);


$arrtimb = array();

$m1 = substr($m1, 0, strlen($m1) - 1); // ELIMINO il ; finale per non avere un elemento vuoto alla fine dell'array
$arrtimb = explode(';', $m1);          // Metto nell'array delle timbratute tutte le timbrature dell'invio

$gio = date('d');
$mes = date('m');
$anno = date('Y');

foreach ($arrtimb as $m2)
{
    // Le timbrature sono del tipo:
    // [matricola][I/R/U]hhmmGGMM    [matricola] ha dimensioni variabili da 1 a 5

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

    $matricola = substr($m2, 0, $postipo);

    $tipo = substr($m2, $postipo, 1);

    $ora = substr($m2, $postipo + 1, 2);
    $min = substr($m2, $postipo + 3, 2);



    $esiste_alunno = esiste_alunno($matricola, $con, $suff);

    // Se è la prima timbratura valida della giornata e corrisponde ad un alunno esistente
    // e non ci sono ancora assenze inserite nella giornata inserisco le assenze per tutti che verranno cancellate man mano arrivano
    // le timbrature.


    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§". "Numtimbrature $numtimbrature \n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . "Data $dataoggi \n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . "Esiste alunno $esiste_alunno \n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . "Esiste assenza $esiste_assenza \n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");

    if (($numtimbrature == 0) && ($dataoggi == "$anno-$mes-$gio") && ($esiste_alunno) && (!$esiste_assenza))
    {

            $query = "insert into tbl_assenze(idalunno,data)
                      select idalunno,'$dataoggi'
                      from tbl_alunni
                      where idclasse<>0
                      and idalunno NOT IN (select idalunno from tbl_presenzeforzate where data = '" . date('Y-m-d') . "')
                      order by idalunno";

        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
        if (!$ris = mysqli_query($con, inspref($query, false)))
        {
            inserisci_log("Errore esecuzione query\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
            die("errore query " . inspref($query, false));
        }

        $numtimbrature = 1;  //Imposto ad 1 le timbrature per non far reinserire le assenze
    }


    $query = "insert into tbl_timbrature(idalunno,tipotimbratura,datatimbratura,oratimbratura) values ('$matricola','$tipo','$anno-$mes-$gio','$ora:$min')";
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
    if (!$ris = mysqli_query($con, inspref($query, false)))
    {
        inserisci_log("Errore esecuzione query\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
        die("errore query " . inspref($query, false));
    }


    //
    // AGGIORNO LE TABELLE IN BASE ALLE TIMBRATURE
    //
    //

    if ($esiste_alunno)
    {
        if ($tipo == 'I')
        {
            $query = "delete from tbl_assenze where idalunno='$matricola' and data='$anno-$mes-$gio'";
            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
            if (!$ris = mysqli_query($con, inspref($query, false)))
            {
                inserisci_log("Errore esecuzione query\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
                die("errore query " . inspref($query, false));
            }

        }
        if ($tipo == 'U')
        {
            $datausc = "$anno-$mes-$gio";
            $orausc = "$ora:$min";
            $query = "insert into tbl_usciteanticipate(idalunno,data,orauscita) values ('$matricola', '$datausc', '$orausc')";
            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
            if (!$ris = mysqli_query($con, inspref($query, false)))
            {
                inserisci_log("Errore esecuzione query\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
                die("errore query " . inspref($query, false));
            }
            //ricalcola_uscite($con, $matricola, $datausc);
            elimina_assenze_lezione($con, $matricola, $datausc);
            inserisci_assenze_per_ritardi_uscite($con, $matricola, $datausc);

        }
        if ($tipo == 'R')
        {
            $dataent = "$anno-$mes-$gio";
            $oraent = "$ora:$min";
            $query = "insert into tbl_ritardi(idalunno,data,oraentrata) values ('$matricola', '$dataent', '$oraent')";
            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
            if (!$ris = mysqli_query($con, inspref($query, false)))
            {
                inserisci_log("Errore esecuzione query\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
                die("errore query " . inspref($query, false));
            }
            $query = "delete from tbl_assenze where idalunno='$matricola' and data='$anno-$mes-$gio'";
            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
            if (!$ris = mysqli_query($con, inspref($query, false)))
            {
                inserisci_log("Errore esecuzione query\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
                die("errore query " . inspref($query, false));
            }
            //ricalcola_ritardi($con, $matricola, $dataent);
            elimina_assenze_lezione($con, $matricola, $dataent);
            inserisci_assenze_per_ritardi_uscite($con, $matricola, $dataent);


        }
    }

}

print ("ricevuti");
mysqli_close($con);


inserisci_log("LAMPSchool§" . date('m-d|H:i:s') ."§$indirizzoip §FINE RICEZIONE \n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");


function esiste_alunno($matricola, $conn, $suff)
{
    $query = "select * from tbl_alunni where idalunno='$matricola' and idclasse<>0";
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
    if (!$ris = mysqli_query($conn, inspref($query, false)))
    {
        inserisci_log("Errore esecuzione query\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
        die("errore query " . inspref($query, false));
    }
    if (mysqli_num_rows($ris) != 0)
    {

        return true;
    }
    else
    {

        return false;
    }
}

function esiste_assenza($dataodierna, $conn, $suff)
{
    $query = "select * from tbl_assenze where data='$dataodierna'";
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");

    if (!$ris = mysqli_query($conn, inspref($query, false)))
    {
        inserisci_log("Errore esecuzione query\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
        die("errore query " . inspref($query, false));
    }
    if (mysqli_num_rows($ris) != 0)
    {

        return true;
    }
    else
    {

        return false;
    }
}



