<?php

require_once '../lib/req_apertura_sessione.php';

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

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$iddocente = stringa_html('iddocente');

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Disponibilità ricevimento genitori";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head($titolo, "", $script, "TDASPM");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


// INIZIO SCRIPT

$idgiornatacolloqui = stringa_html("idgiornatacolloqui");

// scelta classe
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$idalunno = $_SESSION['idutente'];
$idclassealunno = estrai_classe_alunno($idalunno, $con);

print "<form name='giornata' action='prenotazionecolloqui.php' method='post'>";
print("<table align='center'>
			  <tr>
			  <td width='50%'><b>Data</b></p></td>
			  <td width='50%'>
			  <SELECT NAME='idgiornatacolloqui' ONCHANGE='giornata.submit()'><option value=''></option>  ");

//
//  Riempimento combobox delle giornate
//
$query = "select distinct tbl_colloquiclasse.idgiornatacolloqui,data"
        . " from tbl_colloquiclasse,tbl_giornatacolloqui"
        . " where tbl_colloquiclasse.idgiornatacolloqui=tbl_giornatacolloqui.idgiornatacolloqui"
        . " and data>'" . date('Y-m-d') . "'"
        . " and idclasse=$idclassealunno "
        . " order by data";
print inspref($query);
$ris = eseguiQuery($con, $query);
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idgiornatacolloqui"]);
    print "'";
    if ($idgiornatacolloqui == $nom["idgiornatacolloqui"])
        print " selected";
    print ">";
    print ($nom["data"]);
}

print ("</select></td></tr></table></form>");

if ($idgiornatacolloqui != "")
{



    // ESTRAZIONE E PREOARAZIONE SLOT
    $query = "select orainizio, orafine, durataslot"
            . " from tbl_giornatacolloqui"
            . " where idgiornatacolloqui=$idgiornatacolloqui";
    $risgiornata = eseguiQuery($con, $query);
    $recgiornata = mysqli_fetch_array($risgiornata);
    $slots = array();
    $docenti = array();
    $prenotazioni = array();

    $orainizio = substr($recgiornata['orainizio'], 0, 5);
    $orafine = substr($recgiornata['orafine'], 0, 5);
    $durataslot = $recgiornata['durataslot'];

    print "<center><br>ASS - docente assente, OCC - docente già occupato, PREN - orario con possibilità di prenotazione, ANN - orario gà prenotato con possibilità di annullamento <br><br>";
    print "<table border='1' align='center'>";
    print "<tr class='prima'><td></td>";
    $cont = 0;
    $orainizioslot = $orainizio;
    do
    {
        $slots[] = $orainizioslot;
        print "<td>" . $orainizioslot . "</td>";
        $orainizioslot = aggiungi_minuti($orainizioslot, $durataslot);

        $cont++;
    } while ($orainizioslot < $orafine);

    // ESTRAZIONE PRENOTAZIONI GIA? INSERITE

    $query = "select iddocente,idalunno,orainizio from tbl_slotcolloqui
	        where  idgiornatacolloqui=$idgiornatacolloqui";
    $risprenotazioni = eseguiQuery($con, $query);
    while ($recprenotazioni = mysqli_fetch_array($risprenotazioni))
    {
        $strprenotazione = $recprenotazioni['iddocente'] . " " . substr($recprenotazioni['orainizio'], 0, 5);
        $prenotazioni[$strprenotazione] = $recprenotazioni['idalunno'];
    }


    $query = "select distinct cognome,nome,tbl_docenti.iddocente,collegamentowebex,sostegno from tbl_cattnosupp,tbl_docenti
	        where tbl_cattnosupp.iddocente=tbl_docenti.iddocente
	        and tbl_cattnosupp.idclasse=   " . $idclassealunno . "
	        and tbl_cattnosupp.iddocente!=1000000000
                order by cognome,nome";
    $ris = eseguiQuery($con, $query);
    while ($nom = mysqli_fetch_array($ris))
    {


        $iddocente = $nom['iddocente'];
        $docenti[] = $iddocente;
    }
    foreach ($docenti as $docente)
    {


        print "<tr><td class='prima'>" . estrai_dati_docente($docente, $con);

        if (!docente_sostegno($docente, $con))
        {
            $query = "select idmateria from tbl_cattnosupp
			         where idclasse=$idclassealunno and iddocente=" . $docente .
                    " and idalunno=0";

            $rismat = eseguiQuery($con, $query);
            print "<small>";
            while ($recmat = mysqli_fetch_array($rismat))
            {
                print "<br>" . decodifica_materia($recmat['idmateria'], $con) . "  ";
            }
// if ($nom['collegamentowebex']!='')
//     print "<br><a href='" . $nom['collegamentowebex'] . "'><b>Colloquio online</b></a>  ";
            print "</small>";
        }
        else
            {
            
            print "<small>";
            
                print "<br>Sostegno";
           
            print "</small>";
        }
        print "</td>";
        foreach ($slots as $slot)
        {

            print "<td align='center'>";
            if (assenteDocente($docente, $idgiornatacolloqui, $con))
                print "ASS";
            else
            {
                if ($prenotazioni[$docente . " " . $slot] == $idalunno)
                    print "<a href='delprenotazionecolloqui.php?idal=$idalunno&iddoc=$docente&slot=$slot&idgiornatacolloqui=$idgiornatacolloqui'>ANN</A>";
                else
                {
                    if (esistePrenotazioneDocente($idalunno, $docente, $prenotazioni, $slots))
                        print "--";
                    else
                    {
                        if (esistePrenotazioneSlot($idalunno, $slot, $prenotazioni, $docenti))
                            print "--";
                        else
                        {
                            if (isset($prenotazioni[$docente . " " . $slot]))
                            {
                                print "OCC";
                            } else
                            {
                                print "<a href='insprenotazionecolloqui.php?idal=$idalunno&iddoc=$docente&slot=$slot&idgiornatacolloqui=$idgiornatacolloqui'>PREN</A>";
                            }
                        }
                    }
                }
            }

            print "</td>";
        }
    }




    print "</tr>";
    print "</table>";
    print "<br><br>";
    print "<a href='riepilogocolloqui.php'>RIEPILOGO</a>";
    print "<br>";
}


mysqli_close($con);
stampa_piede("");

function esistePrenotazioneDocente($idalunno, $iddocente, $prenotazioni, $slots)
{
    foreach ($slots as $slot)
    {
        $indice = $iddocente . " " . $slot;

        if (isset($prenotazioni[$indice]) & ($prenotazioni[$indice] == $idalunno))
            return true;
    }
    return false;
}

function esistePrenotazioneSlot($idalunno, $slot, $prenotazioni, $docenti)
{
    foreach ($docenti as $docente)
    {
        $indice = $docente . " " . $slot;

        if (isset($prenotazioni[$indice]) & ($prenotazioni[$indice] == $idalunno))
            return true;
    }
    return false;
}

function assenteDocente($iddocente, $idgiornatacolloqui, $con)
{
    $query = "select * from tbl_assenzedocenticolloqui"
            . " where iddocente=$iddocente and idgiornatacolloqui=$idgiornatacolloqui";
    $ris = eseguiQuery($con, $query);
    if (mysqli_num_rows($ris) > 0)
        return true;
    else
        return false;
}
