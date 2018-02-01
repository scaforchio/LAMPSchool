<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
GNU Affero General Public License come pubblicata 
dalla Free Software Foundation; sia la versione 3, 
sia (a vostra scelta) ogni versione successiva.

Questo programma è distribuito nella speranza che sia utile 
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di 
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE. 
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];  // prende il codice del docente
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Situazione alunno";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
print "\n<body>";
//stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola"); 

$codalu = stringa_html('alunno');
$idmateria = stringa_html('materia');
$idclasse = stringa_html('classe');
// $coddoc = stringa_html('docente');
$periodo = stringa_html('periodo');

if ($numeroperiodi == 2)
{
    $per = 'quadrimestre';
}
else
{
    $per = 'trimestre';
}


//
// Prelevo il nome dell'alunno
//


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$query = 'SELECT * FROM tbl_alunni WHERE idalunno="' . $codalu . '"';
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
$val = mysqli_fetch_array($ris);
$nominativo = $val["cognome"] . ' ' . $val["nome"];

if (!alunno_certificato($val['idalunno'], $con))
{
    $cert = "";
}
else
{
    $cert = "<img src='../immagini/apply_small.png'>";
}

$titolo = "Situazione alunno: $nominativo";

$id_ut_doc = $_SESSION["idutente"];

$query = "select anno,sezione,specializzazione from tbl_classi where tbl_classi.idclasse=$idclasse";
$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    $classe = $nom["anno"] . " " . $nom["sezione"] . " " . $nom["specializzazione"];
}

print "<b><center>$classe</center></b>";


//
//    Leggo le tbl_materie e le visualizzo
//

print "<b><center>Materia:&nbsp;";


$query = "select * from tbl_materie where idmateria=$idmateria";
$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    $materia = $nom["denominazione"];
    print $materia . "<br/>Periodo: $periodo";
}

print " $per";


//
//  Ore lezione totali
//


if ($periodo == "Primo")
{
    $querylez = 'SELECT sum(numeroore) AS orelez FROM tbl_lezioni WHERE idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND datalezione <= "' . $fineprimo . '"';
}
if ($periodo == "Secondo" & $numeroperiodi == 2)
{
    $querylez = 'SELECT sum(numeroore) AS orelez FROM tbl_lezioni WHERE idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND datalezione >  "' . $fineprimo . '"';
}
if ($periodo == "Secondo" & $numeroperiodi == 3)
{
    $querylez = 'SELECT sum(numeroore) AS orelez FROM tbl_lezioni WHERE idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND datalezione >  "' . $fineprimo . '" AND datalezione <=  "' . $finesecondo . '"';
}
if ($periodo == "Terzo")
{
    $querylez = 'SELECT sum(numeroore) AS orelez FROM tbl_lezioni WHERE idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND datalezione >  "' . $finesecondo . '"';
}
if ($periodo == "Tutti")
{
    $querylez = 'SELECT sum(numeroore) AS orelez FROM tbl_lezioni WHERE idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" ';
}

$rislez = mysqli_query($con, inspref($querylez));
$vallez = mysqli_fetch_array($rislez);
print ('<br/>Ore totali di lezione: <b>' . $vallez['orelez'] . '</b><br/><br/></center>');


//


echo "<table border=1 width=98%>";


$query = 'SELECT * FROM tbl_alunni WHERE idalunno="' . $codalu . '"';
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
while ($val = mysqli_fetch_array($ris))
{
    // $esiste_voto=false;
    echo "<tr class='prima'>           
              <td colspan=3>";

    echo "<b><big><center>" . $val["cognome"] . " " .
        $val["nome"] . " " . data_italiana($val["datanascita"]) . " </b></big>$cert</td>
          </tr>";

    // Codice per ricerca voti e calcolo medie
    $numo = 0;
    $valo = 0;
    $nums = 0;
    $vals = 0;
    $nump = 0;
    $valp = 0;
    $numa = 0;
    $vala = 0;
    $mediao = 0;
    $medias = 0;
    $mediap = 0;
    $mediaa = 0;
    $riempito = false;

    // ORALE 

    if ($periodo == "Primo")
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND data <= "' . $fineprimo . '" AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="O" ORDER BY data';
    }
    if ($periodo == "Secondo" & $numeroperiodi == 2)
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND data >  "' . $fineprimo . '" AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="O" ORDER BY data';
    }
    if ($periodo == "Secondo" & $numeroperiodi == 3)
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND data >  "' . $fineprimo . '" AND data <=  "' . $finesecondo . '" AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="O" ORDER BY data';
    }
    if ($periodo == "Terzo")
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND data >  "' . $finesecondo . '" AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="O" ORDER BY data';
    }
    if ($periodo == "Tutti")
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="O" ORDER BY data';
    }

    if ($risval = mysqli_query($con, inspref($queryval)))

    {
        if (mysqli_num_rows($risval) > 0)
        {
            print "<tr class='prima'><td colspan=3 align=center><i>Orale</i></td></tr>";
        }
        while ($valval = mysqli_fetch_array($risval))
        {
            $riempito = true;
            print ('<tr><td width=20%>' . data_italiana($valval['data']) . '</td><td width=10%>' . dec_to_mod($valval['voto']) . '</td><td width=70%>' . $valval['giudizio'] . '</td></tr>');
            if ($valval['voto'] != 99)
            {
                $numo++;
                $valo = $valo + $valval["voto"];
            }
        }
    }

    if (!$riempito)
    {
        print"";
    }
    else
    {
        print"<tr>";
        $riempito = false;
    }


    if ($numo != 0)
    {
        print"<td><b>Media orale:</td><td><b>";
        $mediao = round($valo / $numo, 2);
        print ($mediao);
        print "</b></td>";
        print "</tr>";
    }


    // SCRITTO


    if ($periodo == "Primo")
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND data <= "' . $fineprimo . '" AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="S" ORDER BY data';
    }
    if ($periodo == "Secondo" & $numeroperiodi == 2)
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND data >  "' . $fineprimo . '" AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="S" ORDER BY data';
    }
    if ($periodo == "Secondo" & $numeroperiodi == 3)
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND data >  "' . $fineprimo . '" AND data <=  "' . $finesecondo . '" AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="S" ORDER BY data';
    }
    if ($periodo == "Terzo")
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND data >  "' . $finesecondo . '" AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="S" ORDER BY data';
    }
    if ($periodo == "Tutti")
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="S" ORDER BY data';
    }

    if ($risval = mysqli_query($con, inspref($queryval)))

    {
        if (mysqli_num_rows($risval) > 0)
        {
            print "<tr class='prima'><td colspan=3 align=center><i>Scritto</i></td></tr>";
        }
        while ($valval = mysqli_fetch_array($risval))
        {
            $riempito = true;
            print ('<tr><td width=20%>' . data_italiana($valval['data']) . '</td><td width=10%>' . dec_to_mod($valval['voto']) . '</td><td width=70%>' . $valval['giudizio'] . '</td></tr>');
            if ($valval['voto'] != 99)
            {
                $nums++;
                $vals = $vals + $valval["voto"];
            }
        }
    }

    if (!$riempito)
    {
        print"";
    }
    else
    {
        print"<tr>";
        $riempito = false;
    }


    if ($nums != 0)
    {
        print"<td><b>Media scritto:</td><td><b>";

        $medias = round($vals / $nums, 2);
        print ($medias);
        print "</b></td>";
        print "</tr>";
    }

    // PRATICO


    if ($periodo == "Primo")
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND data <= "' . $fineprimo . '" AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="P" ORDER BY data';
    }
    if ($periodo == "Secondo" & $numeroperiodi == 2)
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND data >  "' . $fineprimo . '" AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="P" ORDER BY data';
    }
    if ($periodo == "Secondo" & $numeroperiodi == 3)
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND data >  "' . $fineprimo . '" AND data <=  "' . $finesecondo . '"AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="P" ORDER BY data';
    }
    if ($periodo == "Terzo")
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND data >  "' . $finesecondo . '" AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="P" ORDER BY data';
    }
    if ($periodo == "Tutti")
    {
        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND idmateria="' . $idmateria . '" AND idclasse="' . $idclasse . '" AND tipo="P" ORDER BY data';
    }

    if ($risval = mysqli_query($con, inspref($queryval)))

    {

        if (mysqli_num_rows($risval) > 0)
        {
            print "<tr class='prima'><td colspan=3 align=center><i>Pratico</i></td></tr>";
        }
        while ($valval = mysqli_fetch_array($risval))
        {
            $riempito = true;
            print ('<tr><td width=20%>' . data_italiana($valval['data']) . '</td><td width=10%>' . dec_to_mod($valval['voto']) . '</td><td width=70%>' . $valval['giudizio'] . '</td></td>');
            if ($valval['voto'] != 99)
            {
                $nump++;
                $valp = $valp + $valval["voto"];
            }
        }
    }

    if (!$riempito)
    {
        print"";
    }
    else
    {
        print"<tr>";
        $riempito = false;
    }


    if ($nump != 0)
    {
        print"<td><b>Media pratico:</td><td><b>";

        $mediap = round($valp / $nump, 2);
        print ($mediap);
        print "</b></td>";
        print "</tr>";
    }


    // CALCOLO MEDIE


    $numvoti = 0;
    if ($numo != 0)
    {
        $numvoti++;
    }
    if ($nump != 0)
    {
        $numvoti++;
    }
    if ($nums != 0)
    {
        $numvoti++;
    }

    print "<tr><td align=center colspan=3><b><i>Media totale tra O, S e P: &nbsp;";
    if (($numvoti) != 0)
    {
        print round((($mediap + $medias + $mediao) / $numvoti), 2);
    }
    else
    {
        print "---";
    }
    print "<br/><b><i>Media totale tra tutti i voti: &nbsp;";
    if (($numvoti) != 0)
    {
        print round(($vals + $valp + $valo) / ($nump + $numo + $nums), 2);
    }
    else
    {
        print "---";
    }


//
//   Calcolo ore assenza dell'alunno
//

    if ($periodo == "Primo")
    {
        $queryass = 'SELECT sum(oreassenza) AS oreass FROM tbl_asslezione,tbl_lezioni WHERE tbl_asslezione.idlezione=tbl_lezioni.idlezione AND idalunno = ' . $val["idalunno"] . ' AND idclasse="' . $idclasse . '" AND tbl_lezioni.idmateria="' . $idmateria . '" AND data <= "' . $fineprimo . '"';
    }
    if ($periodo == "Secondo" & $numeroperiodi == 2)
    {
        $queryass = 'SELECT sum(oreassenza) AS oreass FROM tbl_asslezione,tbl_lezioni WHERE tbl_asslezione.idlezione=tbl_lezioni.idlezione AND idalunno = ' . $val["idalunno"] . ' AND idclasse="' . $idclasse . '" AND tbl_lezioni.idmateria="' . $idmateria . '" AND data >  "' . $fineprimo . '"';
    }
    if ($periodo == "Secondo" & $numeroperiodi == 3)
    {
        $queryass = 'SELECT sum(oreassenza) AS oreass FROM tbl_asslezione,tbl_lezioni WHERE tbl_asslezione.idlezione=tbl_lezioni.idlezione AND idalunno = ' . $val["idalunno"] . ' AND idclasse="' . $idclasse . '" AND tbl_lezioni.idmateria="' . $idmateria . '" AND data >  "' . $fineprimo . '" AND data <=  "' . $finesecondo . '"';
    }
    if ($periodo == "Terzo")
    {
        $queryass = 'SELECT sum(oreassenza) AS oreass FROM tbl_asslezione,tbl_lezioni WHERE tbl_asslezione.idlezione=tbl_lezioni.idlezione AND idalunno = ' . $val["idalunno"] . ' AND idclasse="' . $idclasse . '" AND tbl_lezioni.idmateria="' . $idmateria . '" AND data >  "' . $finesecondo . '"';
    }
    if ($periodo == "Tutti")
    {
        $queryass = 'SELECT sum(oreassenza) AS oreass FROM tbl_asslezione,tbl_lezioni WHERE tbl_asslezione.idlezione=tbl_lezioni.idlezione AND idalunno = ' . $val["idalunno"] . ' AND idclasse="' . $idclasse . '" AND tbl_lezioni.idmateria="' . $idmateria . '"';
    }


    $risval = mysqli_query($con, inspref($queryass));

    $valass = mysqli_fetch_array($risval);
    print ('<br/>Ore assenza: ' . $valass['oreass']);


    print "</b></td></tr>";


    // Fine codice per ricerca voti già inseriti


    // echo '</td></tr>';
}

echo '</table>';


//
//   VISUALIZZAZIONE DATI DEI PERIODI PRECEDENTI
//

if ($periodo <> "Primo" & $periodo <> "Tutti")
{
    print "<table border=1 align=center>";
    print "<tr class='prima'><td colspan=1 align=center>VALUTAZIONI PERIODI PRECEDENTI</td></tr>";

    // Vedo le tbl_proposte dei prediodi precedenti
    if ($periodo == "Secondo")
    {
        print "<tr class='prima'><td colspan=1 align=center>Valutazioni primo $per</td></tr>";
        $querypro = "SELECT * FROM tbl_proposte WHERE idalunno = " . $codalu . " AND periodo='1' AND idmateria=" . $idmateria;
        if ($rispro = mysqli_query($con, inspref($querypro)))
        {
            if ($valpro = mysqli_fetch_array($rispro))
            {
                echo "<tr><td>";
                if ($valpro['orale'] != 99)
                {
                    print ('<br/>Voto orale: ' . dec_to_vot($valpro['orale']));
                }
                if ($valpro['scritto'] != 99)
                {
                    print ('<br/>Voto scritto: ' . dec_to_vot($valpro['scritto']));
                }
                if ($valpro['pratico'] != 99)
                {
                    print ('<br/>Voto pratico: ' . dec_to_vot($valpro['pratico']));
                }
                if ($valpro['unico'] != 99)
                {
                    print ('<br/>Voto unico: ' . dec_to_vot($valpro['unico']));
                }
                if ($valpro['condotta'] != 99)
                {
                    print ('<br/>Voto condotta: ' . dec_to_vot($valpro['condotta']));
                }
                print"<br/>&nbsp;</td></tr>";
            }
            else
            {
                echo "<tr><td><center><br/>Nessuna valutazione registrata!<br/>&nbsp;</center></td></tr>";
            }
        }
        print "</center><br><br>";
    }
    if ($periodo == "Terzo")
    {
        print "<tr class='prima'><td colspan=1 align=center>Valutazioni primo $per</td></tr>";
        $querypro = "SELECT * FROM tbl_proposte WHERE idalunno = " . $codalu . " AND periodo='1' AND idmateria=" . $idmateria;
        if ($rispro = mysqli_query($con, inspref($querypro)))
        {
            if ($valpro = mysqli_fetch_array($rispro))
            {
                echo "<tr><td>";
                if ($valpro['orale'] != 99)
                {
                    print ('<br/>Voto orale: ' . dec_to_vot($valpro['orale']));
                }
                if ($valpro['scritto'] != 99)
                {
                    print ('<br/>Voto scritto: ' . dec_to_vot($valpro['scritto']));
                }
                if ($valpro['pratico'] != 99)
                {
                    print ('<br/>Voto pratico: ' . dec_to_vot($valpro['pratico']));
                }
                if ($valpro['unico'] != 99)
                {
                    print ('<br/>Voto unico: ' . dec_to_vot($valpro['unico']));
                }
                if ($valpro['condotta'] != 99)
                {
                    print ('<br/>Voto condotta: ' . dec_to_vot($valpro['condotta']));
                }
                print"<br/>&nbsp;</td></tr>";
            }
            else
            {
                echo "<tr><td><center><br/>Nessuna valutazione registrata!<br/>&nbsp;</center></td></tr>";
            }
        }
        print "</center><br>";
        print "<tr class='prima'><td colspan=1 align=center>Valutazioni secondo $per</td></tr>";
        $querypro = "SELECT * FROM tbl_proposte WHERE idalunno = " . $codalu . " AND periodo='2' AND idmateria=" . $idmateria;
        if ($rispro = mysqli_query($con, inspref($querypro)))
        {
            if ($valpro = mysqli_fetch_array($rispro))
            {
                echo "<tr><td>";
                if ($valpro['orale'] != 99)
                {
                    print ('<br/>Voto orale: ' . dec_to_vot($valpro['orale']));
                }
                if ($valpro['scritto'] != 99)
                {
                    print ('<br/>Voto scritto: ' . dec_to_vot($valpro['scritto']));
                }
                if ($valpro['pratico'] != 99)
                {
                    print ('<br/>Voto pratico: ' . dec_to_vot($valpro['pratico']));
                }
                if ($valpro['unico'] != 99)
                {
                    print ('<br/>Voto unico: ' . dec_to_vot($valpro['unico']));
                }
                if ($valpro['condotta'] != 99)
                {
                    print ('<br/>Voto condotta: ' . dec_to_vot($valpro['condotta']));
                }
                print"<br/>&nbsp;</td></tr>";
            }
            else
            {
                echo "<tr><td><center><br/>Nessuna valutazione registrata!<br/>&nbsp;</center></td></tr>";
            }
        }
        print "</center><br><br>";
    }

    print "</table>";
}
echo "<br>";
// Ricerca note

$query = "select tbl_notealunno.idnotaalunno, data, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, tbl_alunni.datanascita, testo, provvedimenti
            from tbl_noteindalu, tbl_notealunno,tbl_classi, tbl_alunni, tbl_docenti 
            where 
            tbl_noteindalu.idnotaalunno=tbl_notealunno.idnotaalunno
            and tbl_noteindalu.idalunno=tbl_alunni.idalunno
            and tbl_notealunno.idclasse=tbl_classi.idclasse and  tbl_notealunno.iddocente=tbl_docenti.iddocente 
            and tbl_noteindalu.idalunno=$codalu 
            order by tbl_notealunno.data desc";
// print inspref($query);
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query di selezione nota: " . mysqli_error($con));

$c = mysqli_num_rows($ris);


print "<table border=1 align=center>";
print "<tr class='prima'><td colspan=4 align=center>Note e provvedimenti disciplinari individuali</td></tr>";
if ($c == 0)
{
    echo "<tr><td colspan=4 align=center><br>Nessuna nota da visualizzare!<br>&nbsp;</td></tr>";
}
else
{
    print "<tr class=prima><td>Docente</td><td>Data</td><td>Nota</td><td>Provv.</td></tr>";
    while ($rec = mysqli_fetch_array($ris))
    {
        print("<tr>");

        print("<td>");
        print($rec['cogndocente'] . " " . $rec['nomedocente']);
        print("</td>");
        print("<td>");
        print(data_italiana($rec['data']));
        print("</td>");

        print("<td>");
        print("" . $rec['testo'] . "");
        print("</td>");
        print("<td>");
        print("" . $rec['provvedimenti'] . "");
        print("</td>");

        print("</tr>");

    }


}
print "</table><br><br>";


$query = "select idosssist, data, testo
            from tbl_osssist
            where tbl_osssist.iddocente=$iddocente
            and tbl_osssist.idalunno=$codalu  
            order by tbl_osssist.data";
// print $query."<br/>";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query di selezione osservazione: " . mysqli_error($con));

$c = mysqli_num_rows($ris);

print "<table border=1 align=center>";
print "<tr class='prima'><td colspan=2 align=center>Osservazioni sull'alunno</td></tr>";


if ($c == 0)
{
    echo "<tr><td colspan=2 align=center><br>Nessuna osservazione da visualizzare!<br>&nbsp;</td></tr>";
}
else
{

    print "<tr class='prima'><td>Data</td><td>Osservazione</td></tr>";
    while ($rec = mysqli_fetch_array($ris))
    {
        print("<tr>");

        print("<td>");
        print(data_italiana($rec['data']));
        print("</td>");
        print("<td>");
        print("<i>" . $rec['testo'] . "</i>");
        print("</td>");

        print("</tr>");

    }
    print "</table>";


}


$query = "select count(*) as numeroaccessi,max(dataacc) as ultimoaccesso from tbl_logacc
        where utente = 'gen$codalu'";
// print $query."<br/>";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query di selezione osservazione: " . mysqli_error($con));

$rec = mysqli_fetch_array($ris);
$numacc = $rec['numeroaccessi'];
$ultacc = $rec['ultimoaccesso'];

print "<br><table border=1 align=center>";
print "<tr class='prima'><td colspan=2 align=center>Attività genitori</td></tr>";


print "<tr class='prima'><td align='center'>Numero accessi</td><td>Ultimo accesso</td></tr>";

print("<tr>");

print("<td>");
print($numacc);
print("</td>");
print("<td>");
print($ultacc);
print("</td>");

print("</tr>");


print "</table>";


//stampa_piede("");
mysqli_close($con);
print "
</body>
</html>";


