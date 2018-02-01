<?php

session_start();

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

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$iddocente = stringa_html('iddocente');
$idclasse = stringa_html('idclasse');
$numeromassimocolloqui = 2;
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Richiesta appuntamento con docente";
$script = "";
stampa_head($titolo, "", $script, "T");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='visdisponibilita.php?idclasse=$idclasse'>Orari disponibilita</a> - $titolo", "", "$nome_scuola", "$comune_scuola");



$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$numeromassimocolloqui = numero_colloqui_massimi_docente($iddocente, $con);

if ($iddocente != "")
{
    //print "<center><br><b><font color='red'>Funzionalità attiva a breve!</font></b><br></center>";
    print "<center><img src='../immagini/grey_tick.gif'>&nbsp;Colloquio prenotato &nbsp;&nbsp;&nbsp;<img src='../immagini/green_tick.gif'>&nbsp;Colloquio confermato &nbsp;&nbsp;&nbsp;<img src='../immagini/red_cross.gif'>&nbsp;Colloquio non possibile &nbsp;&nbsp;&nbsp;<img src='../immagini/delete.png'>&nbsp;Disdetta prenotazione</center>";
    $ore = array("xyz");
    $idore = array("xyz");
    $inizi = array("xyz");
    $fini = array("xyz");
    $dataoggi = date('Y-m-d');
    print "<center><br><b>Selezionare una data<br>
                         per colloquio con Prof. " . estrai_dati_docente($iddocente, $con) . "</b><br></center>";

    // AGGIUNGO EVENTUALI PRENOTAZIONI SU ORE DI RICEVIMENTO CAMBIATE
    $query = "select * from tbl_prenotazioni,tbl_orericevimento,tbl_orario 
           where 
           tbl_prenotazioni.idoraricevimento=tbl_orericevimento.idoraricevimento
           and tbl_orericevimento.idorario=tbl_orario.idorario
           and not tbl_orericevimento.valido 
           and tbl_prenotazioni.valido
           and iddocente=$iddocente
           and idalunno=" . $_SESSION['idutente'] . "
           and tbl_prenotazioni.data>'$dataoggi'";
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));


    while ($rec = mysqli_fetch_array($ris))
    {
        $ore[] = giornodanum($rec['giorno']);
        $idore[] = $rec['idoraricevimento'];
        $inizi[] = $rec['inizio'];
        $fini[] = $rec['fine'];
    }


    // AGGIUNGO LE ORE RICEVIMENTO PREVISTE
    $query = "select * from tbl_orericevimento,tbl_orario 
           where tbl_orericevimento.idorario=tbl_orario.idorario
           and tbl_orericevimento.valido=1 
           and iddocente=$iddocente";
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));

    while ($rec = mysqli_fetch_array($ris))
    {
        $ore[] = giornodanum($rec['giorno']);
        $idore[] = $rec['idoraricevimento'];
        $inizi[] = $rec['inizio'];
        $fini[] = $rec['fine'];
    }

    //$dataoggi=date('Y-m-d');
    $numgiorni = 0;
    $trovatogiorno = false;
    $dataattuale = $dataoggi;
    print "<center>";
    print "<form action='insrichiestaappuntamento.php' method='post'>";
    print "<br><input type='submit' value='Inoltra richiesta'><br><br>";
    print "<input type='hidden' name='iddocente' value='$iddocente'>";
    print "<input type='hidden' name='idclasse' value='$idclasse'>";
    print "<table border=1 align='center'><tr class='prima'><td>Data e ora</td><td>Prenotaz.</td><td>Comunicazioni del docente</td><td>Elimina prenotaz.</td></tr>";
    do
    {
        $numgiorni++;
        $dataattuale = aggiungi_giorni($dataoggi, $numgiorni);


        $giornoutile = array_search(giorno_settimana($dataattuale), $ore);
        if ($giornoutile > 0)
            $idoraricevimento = $idore[$giornoutile];
        //print "tttt ".$giornoutile;

        if (giorno_settimana($dataattuale) != 'Dom' && !giorno_festa($dataattuale, $con) && !giorno_sospensione_colloqui($dataattuale, $con) && $dataattuale <= $datafinecolloqui)
        {
            for ($i = 1; $i < count($ore); $i++)
            {
                if ($ore[$i] == giorno_settimana($dataattuale))
                {
                    print "<tr>";
                    print "<td><br>" . giorno_settimana($dataattuale) . " " . data_italiana($dataattuale) . " (" . substr($inizi[$i], 0, 5) . "-" . substr($fini[$i], 0, 5) . ")</td>";
                    $stato = esiste_prenotazione($dataattuale, $idore[$i], $con);
                    $idprenotazione = id_prenotazione($dataattuale, $idore[$i], $con);
                    //     
                    //      if ($stato[0] == 0 & ($dataattuale<=$datafinecolloqui) & numero_colloqui_docente($iddocente,$idore[$i],$dataattuale,$con)<$numeromassimocolloqui)
                    $ncd=numero_colloqui_docente($iddocente, $idore[$i], $dataattuale, $con);
                   // print "NCD $ncd NMC $numeromassimocolloqui idore ".$idore[$i];
                    if (($stato[0] == 0) & ($ncd < $numeromassimocolloqui))
                        print "<td align=center><input type='radio' name='giorno' value='$dataattuale|" . $idore[$i] . "'></td><td>&nbsp;</td><td>&nbsp;</td>";
                    else
                    {
                        if ($stato[0] == 0)
                            print "<td align=center>NON PRENOTABILE!</td><td>&nbsp;</td><td>&nbsp;</td>";
                        if ($stato[0] == 1)
                            print "<td align=center><img src='../immagini/grey_tick.gif'></td><td>" . $stato[1] . "</td>";
                        if ($stato[0] == 2)
                            print "<td align=center><image src='../immagini/green_tick.gif'></td><td>" . $stato[1] . "</td>";
                        if ($stato[0] == 3)
                            print "<td align=center><image src='../immagini/red_cross.gif'></td><td>" . $stato[1] . "</td>";
                        if ($stato[0] != 0)
                            print "<td align=center><a href='cancprenotazione.php?idprenotazione=$idprenotazione&iddocente=$iddocente&idclasse=$idclasse'><img src='../immagini/delete.png'></a></td> ";
                    }

                    print "</tr>";
                }
            }
        }
        /* 	if (giorno_settimana($dataattuale)!='Dom' && !giorno_festa($dataattuale,$con)  && $giornoutile>0 )
          {
          print "<br>".data_italiana($dataattuale);
          print "&nbsp;&nbsp;<input type='radio' name='giorno' value='$dataattuale|$idoraricevimento'>";
          }
         */
    }
    while ($dataattuale < $datafinelezioni);
    print "</table>";

    print "<br><input type='submit' value='Inoltra richiesta'>";
    print "</form></center>";
}
stampa_piede("");
mysqli_close($con);

function esiste_prenotazione($data, $idoraric, $conn)
{
    $statoprenotazione = array(0, "");
    $query = "select * from tbl_prenotazioni 
	        where data='$data' 
	        and valido
	        and idalunno=" . $_SESSION['idutente'] . "
	        and idoraricevimento=$idoraric";
    $ris = mysqli_query($conn, inspref($query));
    if (mysqli_num_rows($ris) > 0)
    {
        $rec = mysqli_fetch_array($ris);
        $statoprenotazione[0] = $rec['conferma'];
        $statoprenotazione[1] = $rec['note'];

        return $statoprenotazione;
    }
    else
        return $statoprenotazione;
}

function id_prenotazione($data, $idoraric, $conn)
{
    $idprenotazione = 0;
    $query = "select * from tbl_prenotazioni 
	        where data='$data' 
	        and valido
	        and idalunno=" . $_SESSION['idutente'] . "
	        and idoraricevimento=$idoraric";
    $ris = mysqli_query($conn, inspref($query));
    if (mysqli_num_rows($ris) > 0)
    {
        $rec = mysqli_fetch_array($ris);
        $idprenotazione = $rec['idprenotazione'];
        return $idprenotazione;
    }
    else
        return 0;
}

function giorno_sospensione_colloqui($data, $conn)
{
    $query = "select data from tbl_sospensionicolloqui where data='$data'";
    $ris = mysqli_query($conn, inspref($query));
    if (mysqli_num_rows($ris) > 0)
        return true;
    // if ($data=="2017-11-21")
    // return true;
    return false;
}
