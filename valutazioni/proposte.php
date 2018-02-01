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
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

//
//    Parte iniziale della pagina
//

$titolo = "Inserimento e modifica proposte di voto";
$script = "";

stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

//
//    Fine parte iniziale della pagina
//


$cattedra = stringa_html('cattedra');

$periodo = stringa_html('periodo');
$but = stringa_html('visass');
$giorno = stringa_html('gio');
$meseanno = stringa_html('mese');

$idgruppo = '';

// Divido il mese dall'anno
$mese = substr($meseanno, 0, 2);
$anno = substr($meseanno, 5, 4);

$giornosettimana = giorno_settimana($anno . "-" . $mese . "-" . $giorno);

$tipo = stringa_html('tipo');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

// Prelevo classe e materia dalla cattedra selezionata
if ($cattedra <> "")
{
    $query = "select idclasse, idmateria from tbl_cattnosupp where idcattedra=$cattedra";
    // print inspref($query);
    $ris = mysqli_query($con, inspref($query));
    if ($nom = mysqli_fetch_array($ris))
    {
        $materia = $nom['idmateria'];
        $idclasse = $nom['idclasse'];
    }
}

$id_ut_doc = $_SESSION["idutente"];


print ('
         <form method="post" action="proposte.php" name="voti">
   
         <p align="center">
         <table align="center">

         ');


//
//   Inizio visualizzazione del combo box del periodo
//
if ($numeroperiodi == 2)
{
    print('<tr><td width="50%"><b>Quadrimestre</b></td>');
}
else
{
    print('<tr><td width="50%"><b>Trimestre</b></td>');
}

echo('   <td width="50%">');
echo('   <select name="periodo" ONCHANGE="voti.submit()">');

if ($periodo == '1')
{
    echo("<option selected value='1'>Primo</option>");
}
else
{
    echo("<option value='1'>Primo</option>");
}
if ($periodo == '2')
{
    echo("<option selected value='2'>Secondo</option>");
}
else
{
    echo("<option value='2'>Secondo</option>");
}

if ($numeroperiodi == 3)
{
    if ($periodo == '3')
    {
        echo("<option selected value='3'>Terzo</option>");
    }
    else
    {
        echo("<option value='3'>Terzo</option>");
    }
}


echo("</select>");
echo("</td></tr>");
//
//  Fine visualizzazione del periodo
//
//
//   Leggo il nominativo del docente e lo visualizzo
//

$query = "select iddocente, cognome, nome from tbl_docenti where idutente=$id_ut_doc";
$ris = mysqli_query($con, inspref($query));
if ($nom = mysqli_fetch_array($ris))
{
    $iddocente = $nom["iddocente"];
    $cognomedoc = $nom["cognome"];
    $nomedoc = $nom["nome"];
    $nominativo = $nomedoc . " " . $cognomedoc;
}

print("    
             <tr>
              <td><b>Docente</b></td>

          <td>
          <INPUT TYPE='text' VALUE='$nominativo' disabled>
          <input type='hidden' value='$iddocente' name='iddocente'>
          </td></tr>");

//
//   Cattedre
//

print('
        <tr>
        <td width="50%"><b>Cattedra</b></td>
        <td width="50%">
        <SELECT ID="cattedra" NAME="cattedra" ONCHANGE="voti.submit()"><option value="">');

//
//  Riempimento combobox delle tbl_classi/materie
//

$tipoval = "";
$query = "select idcattedra,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione, tipovalutazione from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria order by anno, sezione, specializzazione, denominazione";


$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idcattedra"]);
    print "'";
    if ($cattedra == $nom["idcattedra"])
    {
        print " selected";
        $tipoval = $nom["tipovalutazione"];
    }
    print ">";
    print ($nom["anno"]);
    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
    print "&nbsp;-&nbsp;";
    print($nom["denominazione"]);
}

echo('
      </SELECT>
      </td></tr>');
echo('</table>
 
       ');
//     <p align="center"><input type="submit" value="Visualizza voti" name="b"></p>
echo('</form><hr>');


$numerorighe = 0;
if ($cattedra != "")
{
    $query = "select * from tbl_cattnosupp where iddocente=$iddocente and idclasse=$idclasse and idmateria=$materia";
    $ris = mysqli_query($con, inspref($query));
    $numerorighe = mysqli_num_rows($ris);
}


if (($cattedra != "") & ($numerorighe > 0))
{

    // VERIFICO SE SI TRATTA DI UNA CATTEDRA LEGATA A UN GRUPPO
    $idcl = estrai_id_classe($cattedra, $con);
    $idmat = estrai_id_materia($cattedra, $con);

    $query = "select distinct tbl_gruppi.idgruppo from tbl_gruppialunni,tbl_alunni,tbl_gruppi
           where tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
             and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
             and tbl_alunni.idclasse=$idcl
             and tbl_gruppi.idmateria=$idmat
             and tbl_gruppi.iddocente=$iddocente";
    //print inspref($query);
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    // CREO UNA LISTA DEI GRUPPI
    // TTTT 04/02/2017 MODIFICA EFFETTUATA PER RISOLVERE PROBLEMA IN CASO DI PIU GRUPPI CON STESSA MATERIA IN STESSA CLASSE
    while ($rec = mysqli_fetch_array($ris))
    {
        $idgruppo .= $rec['idgruppo'] . ",";
        //print "idgruppo $idgruppo<br>";
    }
    if (strlen($idgruppo) > 0)
    {
        $idgruppo = substr($idgruppo, 0, strlen($idgruppo) - 1);
        // print "$idgruppo";
    }
    // print "$idgruppo";
    /* if ($rec = mysqli_fetch_array($ris))
      {
      $idgruppo = $rec['idgruppo'];
      } */
    //
    // $idclasse=$nome;
    $classe = "";
//   $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
//    

    $query = 'SELECT * FROM tbl_classi WHERE idclasse="' . $idclasse . '" ';
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    if ($val = mysqli_fetch_array($ris))
    {
        $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
    }
    //
    //if (!$_SESSION['sostegno'])


    if (!cattedra_sostegno($cattedra, $con))
    {
        if ($idgruppo == '')
        {
            $query = "select * from tbl_alunni where idclasse='$idclasse' order by cognome,nome, datanascita";
        }
        else
        {
            // MODIFICA EFFETTUATA PER RISOLVERE PROBLEMA IN CASO DI PIU GRUPPI CON STESSA MATERIA IN STESSA CLASSE
            /* $query = "select distinct tbl_alunni.idalunno,cognome,nome,datanascita
              from tbl_gruppi,tbl_gruppialunni,tbl_alunni
              where tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
              and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
              and tbl_alunni.idclasse=$idclasse
              and tbl_gruppi.idgruppo  in (select idgruppo from tbl_gruppi where idmateria=$idmat and iddocente=$iddocente)"; */
            $query = "select distinct tbl_alunni.idalunno,cognome,nome,datanascita
                  from tbl_gruppi,tbl_gruppialunni,tbl_alunni
                  where tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
                       and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
                       and tbl_alunni.idclasse=$idclasse
                       and tbl_gruppi.idgruppo  in ($idgruppo)
                       order by cognome, nome,datanascita";
        }
    } // =$idgruppo";
    else
    {
        $query = "select * from tbl_alunni
          where idclasse=$idclasse
          and idalunno in (select idalunno from tbl_cattnosupp where iddocente='$id_ut_doc' and idmateria='$materia' and idclasse='$idclasse')
          order by cognome, nome, datanascita";
    }

    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));


    $c = mysqli_fetch_array($ris);
    if ($c == NULL)
    {
        echo '
                    <p align="center">
            <font size=4 color="black">Nessun alunno presente nella classe ' . $classe . '</font>
               ';
        exit;
    }

    echo '<p align="center">
        <font size=4 color="black">Proposte di voto per la classe <i>' . $classe . '</i></font>';


//
//  Ore lezione totali
//


    if ($periodo == "1")
    {
        $querylez = 'SELECT sum(numeroore) AS orelez FROM tbl_lezioni WHERE idmateria="' . $materia . '" AND idclasse="' . $idclasse . '" AND datalezione <= "' . $fineprimo . '"';
    }
    if ($periodo == "2" & $numeroperiodi == 2)
    {
        $querylez = 'SELECT sum(numeroore) AS orelez FROM tbl_lezioni WHERE idmateria="' . $materia . '" AND idclasse="' . $idclasse . '" AND datalezione >  "' . $fineprimo . '"';
    }
    if ($periodo == "2" & $numeroperiodi == 3)
    {
        $querylez = 'SELECT sum(numeroore) AS orelez FROM tbl_lezioni WHERE idmateria="' . $materia . '" AND idclasse="' . $idclasse . '" AND datalezione >  "' . $fineprimo . '" AND datalezione <=  "' . $finesecondo . '"';
    }
    if ($periodo == "3")
    {
        $querylez = 'SELECT sum(numeroore) AS orelez FROM tbl_lezioni WHERE idmateria="' . $materia . '" AND idclasse="' . $idclasse . '" AND datalezione >  "' . $finesecondo . '"';
    }
    if ($periodo == "Tutti")
    {
        $querylez = 'SELECT sum(numeroore) AS orelez FROM tbl_lezioni WHERE idmateria="' . $materia . '" AND idclasse="' . $idclasse . '" ';
    }

    $rislez = mysqli_query($con, inspref($querylez));
    $vallez = mysqli_fetch_array($rislez);
    print ('<center>Ore totale lezione: <i>' . $vallez['orelez'] . '</i><br/>');


    echo '   <form method="post" action="insproposte.php">
        <table border=2 align="center">';


    echo "
       <tr class='prima'>
          
          <td><b> Alunno </b></td>";
    if ($periodo < $numeroperiodi)
    {
        echo "  <td><b> Scritto </b></td><td><b> Orale</b></td><td><b>Pratico </b></td><td><b>Unico </b></td><td><b>Condotta </b></td><td align='center'><b>Annotazioni</b></td>";
    }
    else
    {
        echo "  <td><b>Unico </b></td><td><b>Condotta </b></td><td align='center'><b>Annotazioni</b></td>";
    }

    echo "<td align='center'><b> Voti del periodo  </b></td>";
    if ($periodo == $numeroperiodi)
    {
        echo "<td align='center'>Valutazioni periodi precedenti</td>";
    }
    echo "</tr>
      ";

    // if (!$_SESSION['sostegno'])
    if (!cattedra_sostegno($cattedra, $con))
    {
        if ($idgruppo == '')
        {
            $query = "select * from tbl_alunni where idclasse='$idclasse' order by cognome,nome, datanascita";
        }
        else
        {
            $query = "select distinct tbl_alunni.idalunno,cognome,nome,datanascita,certificato
                  from tbl_gruppi,tbl_gruppialunni,tbl_alunni
                  where
                       tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
                       and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
                       and tbl_alunni.idclasse=$idclasse
                       and tbl_gruppi.idgruppo  in (select idgruppo from tbl_gruppi where idmateria=$idmat and iddocente=$iddocente)"
                    . "order by cognome, nome, datanascita";
        }
    } //=$idgruppo";
    else
    {
        $query = "select * from tbl_alunni
          where idclasse=$idclasse
          and idalunno in (select idalunno from tbl_cattnosupp where iddocente='$id_ut_doc' and idmateria='$materia' and idclasse='$idclasse')
          order by cognome, nome, datanascita";
    }
    // $query='select * from tbl_alunni where idclasse="'.$idclasse.'" order by cognome,nome';

    $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));
    while ($val = mysqli_fetch_array($ris))
    {
        $esiste_voto = false;
        if (!$val['certificato'])
        {
            $cert = "";
        }
        else
        {
            $cert = "<img src='../immagini/apply_small.png'>";
        }
        echo "
          <tr>
          
          <td><b> " . $val["cognome"] . " " . $val["nome"] . " (" . data_italiana($val["datanascita"]) . ") $cert</b></td> ";
        // Codice per ricerca voti già inseriti
        $queryval = 'SELECT * FROM tbl_proposte WHERE idalunno = ' . $val["idalunno"] . ' AND periodo= "' . $periodo . '" AND idmateria="' . $materia . '" ';

        //  print $queryval;
        $scritto = "";
        $orale = "";
        $pratico = "";
        $unico = "";
        $condotta = "";
        $note = "";
        $risval = mysqli_query($con, inspref($queryval)) or die("Errore nella query: " . mysqli_error($con));
        if ($valval = mysqli_fetch_array($risval))
        {

            $scritto = $valval["scritto"];
            $orale = $valval["orale"];
            $pratico = $valval["pratico"];
            $unico = $valval["unico"];
            $condotta = $valval["condotta"];
            $note = $valval["note"];
        }

        // Fine codice per ricerca voti già inseriti


        if ($tipoval[0] == 'N')
        {
            $inizio = 1;
            $fine = 11;
        }
        if ($tipoval[0] == 'G')
        {
            $inizio = 11;
            $fine = 20;
        }
        if ($tipoval[0] == 'T')
        {
            $inizio = 1;
            $fine = 20;
        }


        if ($periodo == "1")
        {
            $queryper = ' and data <= "' . $fineprimo . '"';
        }
        if ($periodo == "2" & $numeroperiodi == 2)
        {
            $queryper = ' and data >  "' . $fineprimo . '"';
        }
        if ($periodo == "2" & $numeroperiodi == 3)
        {
            $queryper = ' and data >  "' . $fineprimo . '" and data <=  "' . $finesecondo . '"';
        }
        if ($periodo == "3")
        {
            $queryper = ' and data >  "' . $finesecondo . '"';
        }
        if ($periodo == "Tutti")
        {
            $queryper = ' ';
        }


        if ($periodo < $numeroperiodi)
        {
            // SCRITTO

            echo "<td>";
            if (strpos($tipoval, "S") != false)
            {
                echo "   <select name='scritto" . $val['idalunno'] . "'><option value=99>&nbsp;";

                for ($v = $inizio; $v <= $fine; $v = $v + 1)
                {

                    if ($scritto == $v)
                    {
                        echo "<option value=" . $v . " selected>" . dec_to_mod($v);
                    }
                    else
                    {
                        echo "<option value=" . $v . ">" . dec_to_mod($v);
                    }
                }
                echo "</select>";
            }
            else
            {
                echo "<input type='hidden' name='scritto" . $val['idalunno'] . "' value=99>";
            }
            echo "</td>";

            // ORALE

            echo "<td>";
            if (strpos($tipoval, "O") != false)
            {
                echo "   <select name='orale" . $val['idalunno'] . "'><option value=99>&nbsp;";

                for ($v = $inizio; $v <= $fine; $v = $v + 1)
                {

                    if ($orale == $v)
                    {
                        echo "<option value=" . $v . " selected>" . dec_to_mod($v);
                    }
                    else
                    {
                        echo "<option value=" . $v . ">" . dec_to_mod($v);
                    }
                }
                echo "</select>";
            }
            else
            {
                echo "<input type='hidden' name='orale" . $val['idalunno'] . "' value=99>";
            }
            echo "</td>";

            // PRATICO

            echo "<td>";
            if (strpos($tipoval, "P") != false)
            {
                echo "   <select name='pratico" . $val['idalunno'] . "'><option value=99>&nbsp;";

                for ($v = $inizio; $v <= $fine; $v = $v + 1)
                {

                    if ($pratico == $v)
                    {
                        echo "<option value=" . $v . " selected>" . dec_to_mod($v);
                    }
                    else
                    {
                        echo "<option value=" . $v . ">" . dec_to_mod($v);
                    }
                }
                echo "</select>";
            }
            else
            {
                echo "<input type='hidden' name='pratico" . $val['idalunno'] . "' value=99>";
            }
            echo "</td>";
        }
        // UNICO

        echo "<td>";
        if (strpos($tipoval, "U") != false | $periodo == $numeroperiodi)
        {
            echo "   <select name='unico" . $val['idalunno'] . "'><option value=99>&nbsp;";

            for ($v = $inizio; $v <= $fine; $v = $v + 1)
            {

                if ($unico == $v)
                {
                    echo "<option value=" . $v . " selected>" . dec_to_mod($v);
                }
                else
                {
                    echo "<option value=" . $v . ">" . dec_to_mod($v);
                }
            }
            echo "</select>";
        }
        else
        {
            echo "<input type='hidden' name='unico" . $val['idalunno'] . "' value=99>";
        }
        echo "</td>";
        /*
         * CONDOTTA
         */
        if ($livello_scuola < 4)
        {
            $inizio = 21;
            $fine = 30;
        }
        if ($livello_scuola == 4)
        {
            $inizio = 1;
            $fine = 11;
        }

        echo '<td>
               <select name="condotta' . $val["idalunno"] . '"><option value=99>&nbsp;';

        // for ($v = 1; $v <= 30; $v = $v + 1)
        for ($v = $inizio; $v <= $fine; $v = $v + 1)
        {

            if (dec_to_mod($v) != 'NULL')
                if ($condotta == $v)
                {
                    echo '<option value=' . $v . ' selected>' . dec_to_mod($v);
                }
                else
                {
                    echo '<option value=' . $v . '>' . dec_to_mod($v);
                }
        }
        echo '</select>
              </td>';
        // ANNOTAZIONI TTTT
        // if ($giudizisuscheda=="yes")
        if (!isset($note))
        {
            $note = '';
        }

        print ("<td><textarea name='note" . $val['idalunno'] . "' cols='45' rows='2' maxlength='180'>$note</textarea></td>");

        echo '<td>';

        // Codice per ricerca voti dell'alunno gi� inseriti
//
// IMPOSTO LA QUERY IN BASE AL PERIODO
//
        /*
          if ($periodo == "1")
          {
          $queryper = ' and data <= "' . $fineprimo . '"';
          }
          if ($periodo == "2" & $numeroperiodi == 2)
          {
          $queryper = ' and data >  "' . $fineprimo . '"';
          }
          if ($periodo == "2" & $numeroperiodi == 3)
          {
          $queryper = ' and data >  "' . $fineprimo . '" and data <=  "' . $finesecondo . '"';
          }
          if ($periodo == "3")
          {
          $queryper = ' and data >  "' . $finesecondo . '"';
          }
          if ($periodo == "Tutti")
          {
          $queryper = ' ';
          }
         */
        $totvoto = 0;
        $totvotoor = 0;
        $totvotosc = 0;
        $totvotopr = 0;
        $totvotoal = 0;
        $numvoti = 0;
        $numvotior = 0;
        $numvotisc = 0;
        $numvotipr = 0;
        $numvotial = 0;
        $numtipivoti = 0;
        $mediapr = 0;
        $mediasc = 0;
        $mediaor = 0;
        $mediaal = 0;
        echo '<font face="courier" size=1 color="black">';
        print "<center><u><b>VALUTAZIONI</b></u></center>";
// VALUTAZIONI SCRITTE

        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND idclasse="' . $idclasse . '" AND idmateria="' . $materia . '" ' . $queryper . ' AND tipo="S" ORDER BY DATA ';
        $risval = mysqli_query($con, inspref($queryval)) or die("Errore nella query: " . mysqli_error($con));

        while ($valval = mysqli_fetch_array($risval))
        {

            if ($valval["voto"] != 99)
            {
                if ($valval["voto"] >= 6)
                {
                    echo '<font face="courier" size=1 color=green>';
                }
                else
                {
                    echo '<font face="courier" size=1 color=red>';
                }
                echo $valval["tipo"];
                echo '&nbsp;&nbsp;';
                echo data_italiana($valval["data"]);
                echo '&nbsp;&nbsp;';
                echo $valval["voto"];
                echo '&nbsp;&nbsp;';
                echo $valval["giudizio"];
                echo '</font><br/>';
                $totvoto = $totvoto + $valval["voto"];
                $totvotosc = $totvotosc + $valval["voto"];
                $numvoti++;
                $numvotisc++;
            }
            else
            {
                echo '<font face="courier" size=1 color=black>';
                echo $valval["tipo"];
                echo '&nbsp;&nbsp;';
                echo data_italiana($valval["data"]);
                echo '&nbsp;&nbsp;';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '&nbsp;&nbsp;';
                echo $valval["giudizio"];
                echo '</font><br/>';
            }
        }

        if ($numvotisc > 0)
        {
            $numtipivoti++;
            $mediasc = round($totvotosc / $numvotisc, 2);
            print "<font face='courier' size=1 color=blue><b>MEDIA SCRITTO: $mediasc</b><br/>";
        }

// VALUTAZIONI ORALI    

        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND idclasse="' . $idclasse . '" AND idmateria="' . $materia . '" ' . $queryper . ' AND tipo="O" ORDER BY DATA ';
        $risval = mysqli_query($con, inspref($queryval)) or die("Errore nella query: " . mysqli_error($con));
        while ($valval = mysqli_fetch_array($risval))
        {
            if ($valval["voto"] != 99)
            {
                if ($valval["voto"] >= 6)
                {
                    echo '<font face="courier" size=1 color=green>';
                }
                else
                {
                    echo '<font face="courier" size=1 color=red>';
                }
                echo $valval["tipo"];
                echo '&nbsp;&nbsp;';
                echo data_italiana($valval["data"]);
                echo '&nbsp;&nbsp;';
                echo $valval["voto"];
                echo '&nbsp;&nbsp;';
                echo $valval["giudizio"];
                echo '</font><br/>';
                $totvoto = $totvoto + $valval["voto"];
                $totvotoor = $totvotoor + $valval["voto"];
                $numvoti++;
                $numvotior++;
            }
            else
            {
                echo '<font face="courier" size=1 color=black>';
                echo $valval["tipo"];
                echo '&nbsp;&nbsp;';
                echo data_italiana($valval["data"]);
                echo '&nbsp;&nbsp;';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '&nbsp;&nbsp;';
                echo $valval["giudizio"];
                echo '</font><br/>';
            }
        }
        if ($numvotior > 0)
        {
            $numtipivoti++;
            $mediaor = round($totvotoor / $numvotior, 2);
            print "<font face='courier' size=1 color=blue><b>MEDIA ORALE: $mediaor</b><br/>";
        }

// VALUTAZIONI PRATICHE


        $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND idclasse="' . $idclasse . '" AND idmateria="' . $materia . '" ' . $queryper . ' AND tipo="P" ORDER BY DATA ';
        $risval = mysqli_query($con, inspref($queryval)) or die("Errore nella query: " . mysqli_error($con));
        while ($valval = mysqli_fetch_array($risval))
        {
            if ($valval["voto"] != 99)
            {
                if ($valval["voto"] >= 6)
                {
                    echo '<font face="courier" size=1 color=green>';
                }
                else
                {
                    echo '<font face="courier" size=1 color=red>';
                }
                echo $valval["tipo"];
                echo '&nbsp;&nbsp;';
                echo data_italiana($valval["data"]);
                echo '&nbsp;&nbsp;';
                echo $valval["voto"];
                echo '&nbsp;&nbsp;';
                echo $valval["giudizio"];
                echo '</font><br/>';
                $totvoto = $totvoto + $valval["voto"];
                $totvotopr = $totvotopr + $valval["voto"];
                $numvoti++;
                $numvotipr++;
            }
            else
            {
                echo '<font face="courier" size=1 color=black>';
                echo $valval["tipo"];
                echo '&nbsp;-&nbsp;';
                echo data_italiana($valval["data"]);
                echo '&nbsp;&nbsp;';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '&nbsp;&nbsp;';
                echo $valval["giudizio"];
                echo '</font><br/>';
            }
        }
        if ($numvotipr > 0)
        {
            $numtipivoti++;
            $mediapr = round($totvotopr / $numvotipr, 2);
            print "<font face='courier' size=1 color=blue><b>MEDIA PRATICO: $mediapr</b><br/>";
        }

        /*
          // ALTRE VALUTAZIONI LEGATE ALLE COMPETENZE

          $queryval = 'SELECT * FROM tbl_valutazioniintermedie WHERE idalunno = ' . $val["idalunno"] . ' AND idmateria="' . $materia . '" ' . $queryper . ' AND tipo="M" ORDER BY DATA ';
          $risval = mysqli_query($con, inspref($queryval)) or die ("Errore nella query: " . mysqli_error($con));

          while ($valval = mysqli_fetch_array($risval))
          {

          if ($valval["voto"] != 99)
          {
          if ($valval["voto"] >= 6)
          {
          echo '<font face="courier" size=1 color=green>';
          }
          else
          {
          echo '<font face="courier" size=1 color=red>';
          }
          echo $valval["tipo"];
          echo '&nbsp;&nbsp;';
          echo data_italiana($valval["data"]);
          echo '&nbsp;&nbsp;';
          echo $valval["voto"];
          echo '&nbsp;&nbsp;';
          echo $valval["giudizio"];
          echo '</font><br/>';
          $totvoto = $totvoto + $valval["voto"];
          $totvotoal = $totvotoal + $valval["voto"];
          $numvoti++;
          $numvotial++;

          }
          else
          {
          echo '<font face="courier" size=1>';
          echo $valval["tipo"];
          echo '&nbsp;&nbsp;';
          echo data_italiana($valval["data"]);
          echo '&nbsp;&nbsp;';
          echo '&nbsp;&nbsp;&nbsp;&nbsp;';
          echo '&nbsp;&nbsp;';
          echo $valval["giudizio"];
          echo '</font><br/>';
          }
          }

          if ($numvotial > 0)
          {
          $numtipivoti++;
          $mediaal = round($totvotoal / $numvotial, 2);
          print "<font face='courier' size=1 color=blue><b>MEDIA ALTRI VOTI: $mediaal</b><br/>";
          }

         */
// Calcolo medie

        if ($numvoti > 0)
        {
            $mediaosp = round(($mediapr + $mediasc + $mediaor + $mediaal) / $numtipivoti, 2);
            $mediatot = round($totvoto / $numvoti, 2);
            print("<font face='courier' size=1 color=black><b>MEDIA SOP: $mediaosp - MEDIA TOT: $mediatot <br> ");
        }


// CALCOLO ORE ASSENZA DEL PERIODO
        echo '<font face="courier" size=1 color="black">';

        $queryass = "select sum(oreassenza) as oreass from tbl_asslezione,tbl_lezioni where tbl_asslezione.idlezione=tbl_lezioni.idlezione and idalunno = " . $val["idalunno"] . " and tbl_asslezione.idmateria=$materia and idclasse=$idclasse ";
        if ($periodo == "1")
        {
            $queryass = $queryass . " and data <= '$fineprimo'";
        }
        if ($periodo == "2" & $numeroperiodi == 2)
        {
            $queryass = $queryass . " and data >  '$fineprimo'";
        }
        if ($periodo == "2" & $numeroperiodi == 3)
        {
            $queryass = $queryass . " and data >  '$fineprimo' and data <=  '$finesecondo'";
        }
        if ($periodo == "3")
        {
            $queryass = $queryass . " and data >  '$finesecondo'";
        }
        if ($periodo == "Tutti")
        {
            $queryass = $queryass;
        }

        //print inspref($queryass);
        $risval = mysqli_query($con, inspref($queryass));
        $valass = mysqli_fetch_array($risval);
        echo "<font face='courier' size=1 color='black'>";
        print "<center><u><b>ASSENZE</b></u></center>";
        print ("PERIODO: " . $valass['oreass'] . "<br>");


        // INSERISCO UN HIDDEN PER LE ASSENZE PER POTERLE REGISTRARE
        print "<input type='hidden' name='ass" . $val["idalunno"] . "' value='" . $valass['oreass'] . "'>";

// CALCOLO ORE ASSENZA TOTALI
        $queryass = "select sum(oreassenza) as oreass from tbl_asslezione,tbl_lezioni where tbl_asslezione.idlezione=tbl_lezioni.idlezione and idalunno = " . $val["idalunno"] . " and tbl_asslezione.idmateria=$materia and idclasse=$idclasse ";


        //print inspref($queryass);
        $risval = mysqli_query($con, inspref($queryass));
        $valass = mysqli_fetch_array($risval);
        print ("TOTALI:  " . $valass['oreass'] . "<br>");



        // VOTI RELATIVI AL COMPORTAMENTO
        echo '<font face="courier" size=1 color="black">';
        print "<center><u><b>COMPORTAMENTO</b></u></center>";
        // Calcolo la media dei voti di comportamento per la materia

        $totvotocomp = 0;
        $numvoticomp = 0;

        $query = "select voto,data from tbl_valutazionicomp where idalunno=" . $val['idalunno'] . "
                  and idmateria=$idmat $queryper
                  order by data";

        $riscomp = mysqli_query($con, inspref($query)) or die("Errore " . inspref($query, false));
        while ($reccomp = mysqli_fetch_array($riscomp))
        {
            $totvotocomp += $reccomp['voto'];
            $numvoticomp++;
            print $reccomp['voto'] . " (" . data_italiana($reccomp['data']) . ")<br>";
        }
        if ($numvoticomp > 0)
        {
            $votomediocomp = round($totvotocomp / $numvoticomp, 2);
        }
        else
        {
            $votomediocomp = "==";
        }
        print "Voto medio comportamento: $votomediocomp<br>";

// Fine codice per ricerca voti gi� inseriti


        echo '</td>';

        // RICERCA VALUTAZIONI PRECEDENTI

        if ($periodo == $numeroperiodi)
        {
            print "<td>";
            $queryvi = "select * from tbl_valutazionifinali where idalunno=" . $val["idalunno"] . " and idmateria=$materia and periodo<$numeroperiodi";

            $risvi = mysqli_query($con, inspref($queryvi)) or die(mysqli_error($con));
            while ($recvi = mysqli_fetch_array($risvi))
            {
                print "<center>" . $recvi["periodo"] . "° PER.</center><br>";
                if ($recvi["votoscritto"] != 99)
                {
                    print "SCR.: " . dec_to_vot($recvi["votoscritto"]) . "<br>";
                }
                if ($recvi["votoorale"] != 99)
                {
                    print "ORA.: " . dec_to_vot($recvi["votoorale"]) . "<br>";
                }
                if ($recvi["votopratico"] != 99)
                {
                    print "PRA.: " . dec_to_vot($recvi["votopratico"]) . "<br>";
                }
                if ($recvi["votounico"] != 99)
                {
                    print "UNI.: " . dec_to_vot($recvi["votounico"]) . "<br>";
                }
                if ($recvi["note"] != "")
                {
                    print "Ann.: " . $recvi["note"] . "<br>";
                }
            }

            $queryvi = "select * from tbl_valutazionifinali where idalunno=" . $val["idalunno"] . " and idmateria=-1 and periodo<$numeroperiodi";

            $risvi = mysqli_query($con, inspref($queryvi)) or die(mysqli_error($con));
            while ($recvi = mysqli_fetch_array($risvi))
            {

                if ($recvi["votounico"] != 99)
                {
                    print "COMP.: " . dec_to_vot($recvi["votounico"]) . "<br>";
                }
                if ($recvi["note"] != "")
                {
                    print "Ann.: " . $recvi["note"] . "<br>";
                }
            }
            print "</td>";
        }

        echo '</tr>';
    }
    echo '</table>';


    echo '
       <table align="center">
       <tr>
       
       </tr>
       </table>
       <center><small><small>La media SOP &egrave; data dalla media tra le valutazioni medie di scritto, orale e pratico. La media TOT &egrave; data dal totale di tutti i voti diviso il numero dei voti.</small></small></center>';

    // VERIFICO SE LO SCRUTINIO E' ANCORA APERTO IN  TAL CASO CONSENTO LA REGISTRAZIONE
    // ALTRIMENTI LA REGISTRAZIONE E' INIBITA

    $queryscr = "SELECT * FROM tbl_scrutini WHERE idclasse=$idclasse AND periodo='$periodo'";
    $risscr = mysqli_query($con, inspref($queryscr)) or die("Errore nella query: " . mysqli_error($con));
    // print inspref($queryscr);
    if ($valscr = mysqli_fetch_array($risscr))
    {
        if ($valscr['stato'] == "C")
        {
            print "<center>Scrutinio chiuso!</center>";
        }
        else
        //if (!$_SESSION['sostegno'])
        {
            if (!cattedra_sostegno($cattedra, $con))
            {
                print "<center><input type=submit name=b value='Registra proposte di voto'></center>";
            }
        }
    }
    else
    {
        if (!cattedra_sostegno($cattedra, $con))
            print "<center><input type=submit name=b value='Registra proposte di voto'></center>";
    }


    echo "
       <input type=hidden value=" . $idclasse . " name=idclasse>
       <input type=hidden value=" . $idgruppo . " name=idgruppo>
       <input type=hidden value=" . $materia . " name=materia>
       <input type=hidden value=" . $periodo . " name=periodo>
       <input type=hidden value=" . $iddocente . " name=iddocente>
       <input type=hidden value=" . $cattedra . " name=cattedra>
       </form>";
}
else
{
    
}

mysqli_close($con);
stampa_piede("");

