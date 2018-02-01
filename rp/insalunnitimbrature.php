<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
GNU Affero General Public License come pubblicata 
dalla Free Software Foundation; sia la versione 3, 
sia (a vostra scelta) ogni versione successiva.

Questo programma é distribuito nella speranza che sia utile 
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di 
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE. 
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
@require_once("../lib/sms/php-send.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$titolo = "Inserimento timbrature forzate";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='selealunnitimbraturaforzata.php'>Timbrature forzate</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$dest = array();

$destinatari = array();

$idalunno = stringa_html('idalunno');
$tipotimbratura = stringa_html('tipotimbratura');
$datatimbratura = data_to_db(stringa_html('datatimbratura'));
$oratimbratura = stringa_html('oratimbratura');


// VERIFICO SE NON CI SONO ANCORA TIMBRATURE E NEL CASO INSERISCO ASSENZE PER TUTTI

$dataoggi = date('Y-m-d');

if ($datatimbratura == $dataoggi)   // PER EVIATRE CHE SI ATTIVI IN CASO DI INSERIMENTO TIMBRATURE DI GIORNI PRECEDENTI
{
    $query = "select count(*) as numtimbrature from tbl_timbrature where datatimbratura='$dataoggi' and idalunno in(select idalunno from tbl_alunni where idclasse<>0)";


    if (!$ris = mysqli_query($con, inspref($query, false)))
    {

        die("errore query " . inspref($query, false));
    }

    $val = mysqli_fetch_array($ris);
    $numtimbrature = $val['numtimbrature'];

    $esiste_assenza = esiste_assenza($dataoggi, $con);

    // INSERIMENTO ASSENZE PER TUTTI ALLA PRIMA TIMBRATURA
    if (($numtimbrature == 0) && esiste_alunno($idalunno, $con) && (!$esiste_assenza))
    {
        $query = "insert into tbl_assenze(idalunno,data)
                      select idalunno,'$dataoggi'
                      from tbl_alunni
                      where idclasse<>0
                      and idalunno NOT IN (select idalunno from tbl_presenzeforzate where data = '$dataoggi')
                      order by idalunno";

        if (!$ris = mysqli_query($con, inspref($query, false)))
        {

            die("errore query " . inspref($query, false));
        }

    }
}


// Inserisco la timbratura forzata per l'alunno se non è già presente
$query = "select * from tbl_timbrature where idalunno=$idalunno and tipotimbratura='$tipotimbratura' and datatimbratura='$datatimbratura' and oratimbratura='$oratimbratura'";
$ris=mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query, false));
if (mysqli_num_rows($ris)==0)
{

    $query = "insert into tbl_timbrature(idalunno,tipotimbratura,datatimbratura,oratimbratura,forzata) values ($idalunno,'$tipotimbratura','$datatimbratura','$oratimbratura',true)";
    mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query, false));


    if ($tipotimbratura == 'I')
    {
        $query = "delete from tbl_assenze where idalunno='$idalunno' and data='$datatimbratura'";
        mysqli_query($con, inspref($query)) or die("errore query " . inspref($query, false));
        elimina_assenze_lezione($con, $idalunno, $datatimbratura);
    }

    if ($tipotimbratura == 'U')
    {
        if ($giustificauscite == 'yes')
        {
            $valgiust = 'false';
        }
        else
        {
            $valgiust = 'true';
        }
        $query = "insert into tbl_usciteanticipate(idalunno,data,orauscita,giustifica) values ('$idalunno', '$datatimbratura', '$oratimbratura',$valgiust)";
        mysqli_query($con, inspref($query)) or die("errore query " . inspref($query, false));
        //ricalcola_uscite($con, $idalunno, $datatimbratura);
        elimina_assenze_lezione($con, $idalunno, $datatimbratura);
        inserisci_assenze_per_ritardi_uscite($con, $idalunno, $datatimbratura);
    }

    if ($tipotimbratura == 'R')
    {
        $query = "insert into tbl_ritardi(idalunno,data,oraentrata,autorizzato) values ('$idalunno', '$datatimbratura', '$oratimbratura',true)";
        mysqli_query($con, inspref($query)) or die("errore query " . inspref($query, false));
        $query = "delete from tbl_assenze where idalunno='$idalunno' and data='$datatimbratura'";
        mysqli_query($con, inspref($query)) or die("errore query " . inspref($query, false));
        //ricalcola_ritardi($con, $idalunno, $datatimbratura);
        elimina_assenze_lezione($con, $idalunno, $datatimbratura);
        inserisci_assenze_per_ritardi_uscite($con, $idalunno, $datatimbratura);
    }


    print "<br><br><center><b><font color='green'>Inserimento effettuato!</font></b>";
}
else
{
    inserisci_log($_SESSION['userid']."§" . date('m-d|H:i:s') . "§OMESSO INSERIMENTO TIMBRATURA FORZATA PER DUPLICAZIONE " . $matricola . "", $nomefilelog . "rp", $suff);
    print "<br><br><center><b><font color='red'>Inserimento già effettuato in precedenza!</font></b>";
}

stampa_piede("");
mysqli_close($con);

function esiste_alunno($matricola, $conn)
{
    $query = "select * from tbl_alunni where idalunno='$matricola' and idclasse<>0";
    // inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
    if (!$ris = mysqli_query($conn, inspref($query, false)))
    {
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

function esiste_assenza($dataodierna, $conn)
{
    $query = "select * from tbl_assenze where data='$dataodierna'";
    //  inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . inspref($query, false) . "\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");

    if (!$ris = mysqli_query($conn, inspref($query, false)))
    {
        //  inserisci_log("Errore esecuzione query\n", 3, "../lampschooldata/" . $suff . "logsqlrp.log");
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

