<?php
/**
 * Created by PhpStorm.
 * User: pietro
 * Date: 16/06/15
 * Time: 18.29
 */


/**
 * Stampa il registro di classe per giornata
 *
 * @param string $data
 * @param int $idclasse
 * @param int $iddocente
 * @param int $numoremax
 * @param object $conn Connessione al db
 */
function stampa_reg_classe($data, $idclasse, $iddocente, $numoremax, $conn, $stampacollegamenti = true, $gestcentrassenze = 'no',$giustificauscite= 'no')
{
    $gio = substr($data, 8, 2);
    $mese = substr($data, 5, 2) . " - " . substr($data, 0, 4);

    $gotoPage = $_SERVER['PHP_SELF'];
    print "<br><center><b>Classe: " . decodifica_classe($idclasse, $conn) . " - Data: " . data_italiana($data) . " - " . giorno_settimana($data) . "</b></center>";


    // MODIFICA ANTE TOKEN
    // print "Doc $iddocente Cla $idclasse";
    $cattedra = codice_cattedra($iddocente, $idclasse, 0, $conn);


    if ($stampacollegamenti)
    {
        if ($data <= date('Y-m-d'))
        {
            if (!is_docente_classe($iddocente, $idclasse, $conn)) // | $_SESSION['sostegno'])
            {
                if (!is_docente_sostegno_classe($iddocente, $idclasse, $conn))
                {
                    print "<center><br><a href='../lezioni/lezsupp.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&meseanno=$mese&cattedra=$cattedra'>Supplenze</a></center>";
                }
                else
                {
                    print "<center><br><a href='../lezioni/lezcert.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&meseanno=$mese'>Lezioni</a>&nbsp;&nbsp;&nbsp;<a href='../lezioni/lezsupp.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&meseanno=$mese&cattedra=$cattedra'>Supplenze</a></center>";
                }
            }
            else
            {
                print "<center><br><a href='../lezioni/lez.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&meseanno=$mese'>Lezioni</a>&nbsp;&nbsp;&nbsp;<a href='../lezioni/lezsupp.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&meseanno=$mese&cattedra=$cattedra'>Supplenze</a></center>";
            }
        }
        else
        {

            print "<center><br><a href='../lezioni/lez.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&meseanno=$mese'>Lezioni</a></center>";

        }
    }
    // FINE MODIFICA ANTE TOKEN
    print "<table align='center' border=1 width=100% class=smallchar>
	<tr class='prima'>
	<td width=3%>Ora</td><td width=27%>Materia</td><td width=20%>Docenti</td><td width=50%>Argomenti svolti</td></tr>";

    $numeroorecomp = calcola_numero_ore($data, $idclasse, $conn);

    for ($no = 1; $no <= $numoremax; $no++)
    {
        $colore = "#000000";

        print "<tr>";


        // Ora e materia

        $query = "select idlezione,denominazione,tbl_materie.idmateria,idlezionegruppo from tbl_lezioni,tbl_materie where
              tbl_lezioni.idmateria=tbl_materie.idmateria and
              datalezione='$data' and idclasse=$idclasse
              and $no>=orainizio and $no<=(orainizio+numeroore-1)";
        // print inspref($query);
        $ris = mysqli_query($conn, inspref($query)) or die(mysqli_error($conn) . inspref($query));
        // CONTROLLO EVENTUALI ERRORI DI SOVRAPPOSIZIONE
        // $riscontr=$ris;
        $numrighe = mysqli_num_rows($ris);
        if ($numrighe > 1)
        {
            // Devono essere tutte lezioni speciali altrimenti c'è un errore
            $colore = "#00ff00";

            while ($rec = mysqli_fetch_array($ris))
            {
                if ($rec['idlezionegruppo'] == null)
                {
                    $colore = "#ff0000";
                }
            }

        }
        // FINE CONTROLLI
        mysqli_data_seek($ris, 0); // RIPORTO IL CURSORE AL PRIMO RECORD
        print "<td align=center>";
        print "$no";
        print "</td>";
        print "<td>";
        while ($rec = mysqli_fetch_array($ris))
        {
            $idlezione = $rec['idlezione'];

            print "<font color='$colore'>";
            //if (esiste_firma($idlezione, $iddocente, $conn))
            if (esiste_cattedra($idlezione, $iddocente, $conn))
            {
                print "<a href='../lezioni/lez.php?goback=$gotoPage&idlezione=$idlezione&provenienza=registro'>";
                print $rec['denominazione'];
                print "</a>";
            }
            else
            {
                print $rec['denominazione'];
            }

            print "</font><br>";
        }
        print "</td>";

        // Firme

        $query = "select cognome,nome from tbl_firme,tbl_lezioni,tbl_docenti
              where tbl_firme.idlezione=tbl_lezioni.idlezione
                and tbl_firme.iddocente=tbl_docenti.iddocente
                and datalezione='$data' and idclasse=$idclasse
                and $no>=orainizio and $no<=(orainizio+numeroore-1)";
        $ris = mysqli_query($conn, inspref($query)) or die(mysqli_error($conn) . inspref($query));
        print "<td>";
        while ($rec = mysqli_fetch_array($ris))
        {
            print "<font color='$colore'>" . $rec['cognome'] . " " . $rec['nome'] . "</font><br>";
        }
        print "</td>";

        // Argomenti


        $query = "select argomenti,attivita,numeroore,orainizio
              from tbl_lezioni
              where datalezione='$data' and idclasse=$idclasse
              and $no>=orainizio and $no<=(orainizio+numeroore-1)";
        $ris = mysqli_query($conn, inspref($query)) or die(mysqli_error($conn) . inspref($query));
        print "<td>";
        while ($rec = mysqli_fetch_array($ris))
        {
            if ($no == $rec['orainizio'])
            {
                print "<font color='$colore'>" . $rec['argomenti'] . "&nbsp;" . $rec['attivita'] . "</font><br>";
            }
        }
        print "&nbsp;</td>";
    }
    print "</tr></table>";
    if ($stampacollegamenti)
    {
        if ($data <= date('Y-m-d'))
        {
            if ($_SESSION['tipoutente'] == 'S' | $_SESSION['tipoutente'] == 'P')
            {
                $collegamenti = "<a href='../assenze/ass.php?goback=$gotoPage&cl=$idclasse&gio=$gio&meseanno=$mese&idclasse=$idclasse'>Assenze</a>&nbsp;&nbsp;&nbsp;";
            }
            else
            {
                if ($gestcentrassenze == 'no')
                {
                    $collegamenti = "<a href='../assenze/ass.php?goback=$gotoPage&cl=$idclasse&gio=$gio&meseanno=$mese&idclasse=$idclasse'>Assenze</a>&nbsp;&nbsp;&nbsp;";
                }
                else
                {
                    $collegamenti = "";
                }
            }
            // VERIFICO SE CI SONO ASSENZE DA GIUSTIFICARE
            $query = "select count(*) as numassingiust from tbl_assenze
                where idalunno in (" . estrai_alunni_classe_data($idclasse, $data, $conn) . ")
                and data< '$data'
                and not giustifica";
            $risgiu = mysqli_query($conn, inspref($query));
            $recgiu = mysqli_fetch_array($risgiu);
            $numingiust = $recgiu['numassingiust'];

            $query = "select count(*) as numritingiust from tbl_ritardi
                where idalunno in (" . estrai_alunni_classe_data($idclasse, $data, $conn) . ")
                and data<= '$data'
                and not giustifica";
            $risgiu = mysqli_query($conn, inspref($query));
            $recgiu = mysqli_fetch_array($risgiu);
            $numingiust += $recgiu['numritingiust'];
            if ($giustificauscite=='yes')
            {
                $query = "select count(*) as numuscingiust from tbl_usciteanticipate
                where idalunno in (" . estrai_alunni_classe_data($idclasse, $data, $conn) . ")
                and data<= '$data'
                and not giustifica";
                $risgiu = mysqli_query($conn, inspref($query));
                $recgiu = mysqli_fetch_array($risgiu);
                $numingiust += $recgiu['numuscingiust'];
            }
            if ($numingiust > 0)
            {
                $collegamenti .= "<a href='../assenze/giust.php?goback=$gotoPage&cl=$idclasse&gio=$gio&meseanno=$mese'>Giustifiche</a>&nbsp;&nbsp;&nbsp;";
            }

            if ($_SESSION['gestcentrautorizz'] == 'no' || $_SESSION['tipoutente'] == 'S' || $_SESSION['tipoutente'] == 'P')
            {
                $collegamenti .= "<a href='../assenze/rit.php?goback=$gotoPage&cl=$idclasse&gio=$gio&meseanno=$mese'>Ritardi</a>&nbsp;&nbsp;&nbsp;";
                $collegamenti .= "<a href='../assenze/usc.php?goback=$gotoPage&cl=$idclasse&gio=$gio&meseanno=$mese'>Uscite anticipate</a>&nbsp;&nbsp;&nbsp;";
            }
            $collegamenti .= "<a href='../note/notecl.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&meseanno=$mese'>Note di classe</a>&nbsp;&nbsp;&nbsp;";
            $collegamenti .= "<a href='../note/noteindmul.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&mese=$mese'>Note individuali</a>&nbsp;&nbsp;&nbsp;";
            $collegamenti .= "<a href='../regclasse/annotaz.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&mese=$mese'>Annotazioni</a>";
            print "<div style=\"text-align: center;\"><br>$collegamenti</div>";
        }
        else
        {
            $collegamenti = "<a href='../regclasse/annotaz.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&mese=$mese'>Annotazioni</a>";

            print "<div style=\"text-align: center;\"><br>$collegamenti</div>";
        }

    }
    print "<table border=1 width=100% class=smallchar>

			 <tr class=prima>

       <td width=30%>Alunni assenti</td><td width=20%>Alunni giustificati</td><td width=25%>Note disciplinari</td><td width=25%>Annotazioni</td></tr>";

    print "<tr><td valign='top'>";

    // ASSENTI
 /*   $query = "select cognome,nome
                 from tbl_assenze,tbl_alunni
                 where
                 tbl_assenze.idalunno=tbl_alunni.idalunno
                 and data='$data' and idclasse=$idclasse"; */
    $query = "select cognome,nome
                 from tbl_assenze,tbl_alunni
                 where
                 tbl_assenze.idalunno=tbl_alunni.idalunno
                 and data='$data' and tbl_alunni.idalunno in (".estrai_alunni_classe_data($idclasse,$data,$conn).")";
    $res = mysqli_query($conn, inspref($query)) or die (mysqli_error($conn) . inspref($query));
    $numalunni = mysqli_num_rows($res);
    $conta = 0;
    while ($rec = mysqli_fetch_array($res))
    {
        $conta++;
        print $rec['cognome'] . " " . $rec['nome'];
        if ($conta < $numalunni)
        {
            print ", ";
        }
    }

    if ($_SESSION['giustifica_ritardi'] == 'yes')
    {
        $query = "select cognome,nome, oraentrata, autorizzato
                 from tbl_ritardi,tbl_alunni
                 where
                 tbl_ritardi.idalunno=tbl_alunni.idalunno
                 and data='$data' and tbl_alunni.idalunno in (".estrai_alunni_classe_data($idclasse,$data,$conn).") and autorizzato";
        $res = mysqli_query($conn, inspref($query)) or die (mysqli_error($conn) . inspref($query));
        $numalunni = mysqli_num_rows($res);
        if ($numalunni > 0)
        {
            print "<center><i><b>RITARDI AUTORIZZATI:</b></i><br></center>";
            $conta = 0;
            while ($rec = mysqli_fetch_array($res))
            {
                $conta++;
                print $rec['cognome'] . " " . $rec['nome'];
                print "(" . substr($rec['oraentrata'], 0, 5) . ")";
                if ($conta < $numalunni)
                {
                    print ", ";
                }
            }
        }
        else
        {

        }

        $query = "select cognome,nome, oraentrata, autorizzato
                 from tbl_ritardi,tbl_alunni
                 where
                 tbl_ritardi.idalunno=tbl_alunni.idalunno
                 and data='$data' and tbl_alunni.idalunno in (".estrai_alunni_classe_data($idclasse,$data,$conn).") and not autorizzato";
        $res = mysqli_query($conn, inspref($query)) or die (mysqli_error($conn) . inspref($query));
        $numalunni = mysqli_num_rows($res);
        if ($numalunni > 0)
        {
            print "<center><i><b>RITARDI NON AUTORIZZATI:</b></i><br></center>";
            $conta = 0;
            while ($rec = mysqli_fetch_array($res))
            {
                $conta++;
                print $rec['cognome'] . " " . $rec['nome'];
                print "(" . substr($rec['oraentrata'], 0, 5) . ")";
                if ($conta < $numalunni)
                {
                    print ", ";
                }
            }
        }
    }
    else
    {
        $query = "select cognome,nome, oraentrata, autorizzato
                 from tbl_ritardi,tbl_alunni
                 where
                 tbl_ritardi.idalunno=tbl_alunni.idalunno
                 and data='$data' and tbl_alunni.idalunno in (".estrai_alunni_classe_data($idclasse,$data,$conn).")";
        $res = mysqli_query($conn, inspref($query)) or die (mysqli_error($conn) . inspref($query));
        $numalunni = mysqli_num_rows($res);
        if ($numalunni > 0)
        {
            print "<center><i><b>RITARDI:</b></i><br></center>";
            $conta = 0;
            while ($rec = mysqli_fetch_array($res))
            {
                $conta++;
                print $rec['cognome'] . " " . $rec['nome'];
                print "(" . substr($rec['oraentrata'], 0, 5) . ")";
                if ($conta < $numalunni)
                {
                    print ", ";
                }
            }
        }
        else
        {

        }
    }
    $query = "select cognome,nome, orauscita
                 from tbl_usciteanticipate,tbl_alunni
                 where
                 tbl_usciteanticipate.idalunno=tbl_alunni.idalunno
                 and data='$data' and tbl_alunni.idalunno in (".estrai_alunni_classe_data($idclasse,$data,$conn).")";
    $res = mysqli_query($conn, inspref($query)) or die (mysqli_error($conn) . inspref($query));
    $numalunni = mysqli_num_rows($res);
    if ($numalunni > 0)
    {
        print "<center><i><b>USCITE ANTICIPATE:</b></i><br></center>";
        $conta = 0;
        while ($rec = mysqli_fetch_array($res))
        {
            $conta++;
            print $rec['cognome'] . " " . $rec['nome'];

            print "(" . substr($rec['orauscita'], 0, 5) . ")";

            if ($conta < $numalunni)
            {
                print ", ";
            }
        }
    }
    print "</td>";
    print "<td valign='top'>";


    // GIUSTIFICATI
    $query = "select distinct cognome,nome
                 from tbl_assenze,tbl_alunni
                 where
                 tbl_assenze.idalunno=tbl_alunni.idalunno
                 and datagiustifica='$data' and tbl_alunni.idalunno in (".estrai_alunni_classe_data($idclasse,$data,$conn).")";
    $res = mysqli_query($conn, inspref($query)) or die (mysqli_error($conn) . inspref($query));
    $numalunni = mysqli_num_rows($res);
    $conta = 0;
    while ($rec = mysqli_fetch_array($res))
    {
        $conta++;
        print $rec['cognome'] . " " . $rec['nome'];
        if ($conta < $numalunni)
        {
            print ", ";
        }
    }

    // RITARDI
    $query = "select distinct cognome,nome
                 from tbl_ritardi,tbl_alunni
                 where
                 tbl_ritardi.idalunno=tbl_alunni.idalunno
                 and datagiustifica='$data' and tbl_alunni.idalunno in (".estrai_alunni_classe_data($idclasse,$data,$conn).")";
    $res = mysqli_query($conn, inspref($query)) or die (mysqli_error($conn) . inspref($query));
    $numalunni = mysqli_num_rows($res);
    if ($numalunni > 0)
    {
        print "<center><i><b>RITARDI:</b></i><br></center>";
        $conta = 0;
        while ($rec = mysqli_fetch_array($res))
        {
            $conta++;
            print $rec['cognome'] . " " . $rec['nome'];
            if ($conta < $numalunni)
            {
                print ", ";
            }
        }
    }


    // USCITE ANT.
    $query = "select distinct cognome,nome
                 from tbl_usciteanticipate,tbl_alunni
                 where
                 tbl_usciteanticipate.idalunno=tbl_alunni.idalunno
                 and datagiustifica='$data' and tbl_alunni.idalunno in (".estrai_alunni_classe_data($idclasse,$data,$conn).")";
    $res = mysqli_query($conn, inspref($query)) or die (mysqli_error($conn) . inspref($query));
    $numalunni = mysqli_num_rows($res);
    if ($numalunni > 0)
    {
        print "<center><i><b>USCITE ANTICIPATE:</b></i><br></center>";
        $conta = 0;
        while ($rec = mysqli_fetch_array($res))
        {
            $conta++;
            print $rec['cognome'] . " " . $rec['nome'];
            if ($conta < $numalunni)
            {
                print ", ";
            }
        }

    }

    print "</td>";

    // NOTE DI CLASSE

    print "<td valign='top'>";

    $query = "select testo,provvedimenti,cognome,nome
                 from tbl_noteclasse,tbl_docenti
                 where tbl_noteclasse.iddocente=tbl_docenti.iddocente
                 and data='$data' and idclasse=$idclasse";
    $res = mysqli_query($conn, inspref($query)) or die (mysqli_error($conn) . inspref($query));
    while ($rec = mysqli_fetch_array($res))
    {
        print "" . $rec['testo'] . "(<i>" . $rec['cognome'] . " " . $rec['nome'] . "</i>)<br><b>" . $rec['provvedimenti'] . "</b><br><br>";
    }

    // NOTE INDIVIDUALI

    $query = "select idnotaalunno, testo, provvedimenti, cognome, nome
                 from tbl_notealunno,tbl_docenti
                 where tbl_notealunno.iddocente=tbl_docenti.iddocente
                 and data='$data' and idclasse=$idclasse";
    $res = mysqli_query($conn, inspref($query)) or die (mysqli_error($conn) . inspref($query));
    while ($rec = mysqli_fetch_array($res))
    {
        $queryal = "SELECT idalunno
                          FROM tbl_noteindalu
                          WHERE idnotaalunno=" . $rec['idnotaalunno'];
        $resalu = mysqli_query($conn, inspref($queryal)) or die (mysqli_error($conn));
        $numalunni = mysqli_num_rows($resalu);
        $conta = 0;
        print "[Alunni: ";
        while ($recalu = mysqli_fetch_array($resalu))
        {
            $conta++;
            print estrai_dati_alunno_rid($recalu['idalunno'], $conn);
            if ($conta < $numalunni)
            {
                print ",";
            }
        }
        print "]<br/>";
        print "" . $rec['testo'] . "(<i>" . $rec['cognome'] . " " . $rec['nome'] . "</i>)<br><b>" . $rec['provvedimenti'] . "</b><br><br>";
    }
    print "</td>";
    /*
     * ANNOTAZIONI
     */

    print "<td valign='top'>";

    $query = "select testo,cognome,nome
                 from tbl_annotazioni,tbl_docenti
                 where tbl_annotazioni.iddocente=tbl_docenti.iddocente
                 and data='$data' and idclasse=$idclasse";
    $res = mysqli_query($conn, inspref($query)) or die (mysqli_error($conn) . inspref($query));
    while ($rec = mysqli_fetch_array($res))
    {
        print "" . $rec['testo'] . "<br>(<i>" . $rec['cognome'] . " " . $rec['nome'] . "</i>)<br>";
    }

    // Cerco presenti 'forzati'


    $elencoalunni = estrai_alunni_classe_data($idclasse, $data, $conn);

    $query = "select concat(concat(cognome,' '),nome) as nominativo, motivo from tbl_presenzeforzate,tbl_alunni
            where tbl_presenzeforzate.idalunno = tbl_alunni.idalunno
            and data='$data'
            and tbl_alunni.idalunno in ($elencoalunni)
            order by motivo,cognome, nome";
    $risprf = mysqli_query($conn, inspref($query)) or die ("Errore: " . inspref($query, false));
    // print "tttt ".inspref($query);
    $elencopresenti = "";
    if (mysqli_num_rows($risprf) > 0)
    {
        $recprf = mysqli_fetch_array($risprf);
        $motivo = $recprf['motivo'];
        $elencopresenti .= $recprf['nominativo'] . ", ";
        while ($recprf = mysqli_fetch_array($risprf))
        {
            if ($motivo != $recprf['motivo'])
            {
                $elencopresenti = substr($elencopresenti, 0, strlen($elencopresenti) - 2);
                print "Gli alunni $elencopresenti non sono in classe per $motivo.<br>";
                $elencopresenti = "";
            }

            $motivo = $recprf['motivo'];
            $elencopresenti .= $recprf['nominativo'] . ", ";

        }
        $elencopresenti = substr($elencopresenti, 0, strlen($elencopresenti) - 2);
        print "Gli alunni $elencopresenti non sono in classe per $motivo.";
        $elencopresenti = "";
    }
    print "</td>";


    print "</tr></table><br>";
    if ($stampacollegamenti)
    {
        print "<center><b>Allegati al registro: </b><a href=javascript:Popup('visautorizzazioni.php?idclasse=$idclasse')>Autorizzazioni ed esoneri</a>&nbsp;&nbsp;&nbsp;
                     <a href=javascript:Popup('elencoalunni.php?idclasse=$idclasse')>Elenco alunni</a>&nbsp;&nbsp;&nbsp;
                     <a href='../documenti/visdocumenticlasse.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&mese=$mese')>Documenti di classe</a></center>";
    }

}

function esiste_lezione($data,$con)
{
    $query="select datalezione from tbl_lezioni where datalezione='$data'";
    $ris=mysqli_query($con,inspref($query)) or die ("Errore".inspref($query));
    if (mysqli_num_rows($ris)>0)
        return true;
    else
        return false;
}



