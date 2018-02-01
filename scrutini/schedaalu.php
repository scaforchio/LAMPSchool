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
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
// DEFINIZIONE ARRAY PER MEMORIZZAZZIONE IN CSV
$listamaterie = array();
$listamaterie[] = "Alunno";


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


//
//    Parte iniziale della pagina
//

$titolo = "Scrutinio alunno";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";

stampa_head($titolo, "", $script, "SDMAP");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$nome = stringa_html('cl');
$idalunno = stringa_html('idalunno');
$periodo = stringa_html('periodo');
$provenienza = stringa_html('prov');
//$anno = stringa_html('anno');


$idclasse = stringa_html('cl');
$id_ut_doc = $_SESSION["idutente"];



print ('
         <form method="post" action="schedaalu.php" name="voti">
   
         <p align="center">
         <table align="center">');


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
//  Fine visualizzazione del quadrimestre
//
//
//   Classi
//

print('
        <tr>
        <td width="50%"><b>Classe</b></p></td>
        <td width="50%">
        <SELECT ID="cl" NAME="cl" ONCHANGE="voti.submit()"><option value=""></option>  ');

//
//  Riempimento combobox delle tbl_classi
//
if ($tipoutente == "S" | $tipoutente == "P" | $tipoutente == "A")
{
    $query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY anno,sezione,specializzazione";
}
else
{
    $query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi
           WHERE idcoordinatore=" . $_SESSION['idutente'] . " ORDER BY anno,sezione,specializzazione";
    $coordinatore = true;
}


$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
    if ($idclasse == $nom["idclasse"])
    {
        print " selected";
    }
    print ">";
    print ($nom["anno"]);
    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
}


print ("</select></td></tr>");

//
//   Alunni
//

if ($nome != "")
{
    print "<tr><td><b>Alunno</b></td><td>";
    $query = "select idalunno,cognome,nome,datanascita from tbl_alunni where idclasse=$idclasse order by cognome, nome, datanascita";
    $ris = mysqli_query($con, inspref($query));
    echo("<select name='idalunno' ONCHANGE='voti.submit()'><option value=''>&nbsp");
    while ($nom = mysqli_fetch_array($ris))
    {
        print "<option value='";
        print ($nom["idalunno"]);
        print "'";
        if ($idalunno == $nom["idalunno"])
        {
            print " selected";
        }
        print ">";
        print ($nom["cognome"]);
        print "&nbsp;";
        print($nom["nome"]);
        print "&nbsp;&nbsp;&nbsp;";
        print(data_italiana($nom["datanascita"]));
    }
}
else
{
    print "<tr><td width='50%'><p align='center'><b>Alunno</b></p></td><td>";
    echo("<select name='idalunno'><option value=''>&nbsp");
}


print("</table></form>");

if ($nome != "" & $idalunno != "")
{

// APERTURA FILE CSV PER MEMORIZZAZIONE PROPOSTE
//$nf=session_id().".csv";
//$nomefile="$cartellabuffer/".$nf;
//$fp = fopen($nomefile, 'w');

    /*
      // VERIFICO SE E' POSSIBILE IMPORTARE PROPOSTE

      $query="SELECT * FROM tbl_proposte
      where idalunno=$idalunno
      and periodo=$periodo";
      //print inspref($query);
      $ris=mysqli_query($con,inspref($query));
      $numproposte=mysqli_num_rows($ris);

      $query="SELECT * FROM tbl_valutazionifinali
      where idalunno=$idalunno
      and periodo=$periodo";
      $ris=mysqli_query($con,inspref($query));
      $numvalutazioni=mysqli_num_rows($ris);
      //print inspref($query);



      if ($numproposte>0 and $numvalutazioni==0)
      {
      // IMPORTO LE PROPOSTE PER L'ALUNNO

      $queryins = "INSERT into tbl_valutazionifinali(idalunno,idmateria,votoscritto,votoorale,votopratico,votounico,assenze,note,periodo)
      SELECT idalunno,idmateria,scritto,orale,pratico,unico,assenze,note,periodo from tbl_proposte
      where idalunno=$idalunno and periodo=$periodo";
      print "<center><b>Importate le proposte di voto!</b></center>";
      $risins =  mysqli_query($con,inspref($queryins)) or die(mysqli_error());
      // INSERISCO LA VALUTAZIONE DI CONDOTTA MEDIA
      $queryins = "INSERT into tbl_valutazionifinali(idalunno,idmateria,votounico,periodo)
      VALUES ($idalunno,-1,".calcola_media_condotta($idalunno,$periodo).",$periodo)";

      $risins =  mysqli_query($con,inspref($queryins)) or die(mysqli_error());
      }

     */

    print "<form method='post' action='insschedaalu.php' name='votialu'>";

// ESTRAGGO TUTTE LE MATERIE PER LA CLASSE
    $query = "SELECT distinct tbl_materie.idmateria,tbl_materie.denominazione,sigla,tipovalutazione FROM tbl_cattnosupp,tbl_materie
              WHERE tbl_cattnosupp.idmateria=tbl_materie.idmateria
              and tbl_cattnosupp.idclasse=$idclasse
              and tbl_cattnosupp.iddocente <> 1000000000
              order by progrpag,tbl_materie.sigla";
// print inspref($query);
    $ris = mysqli_query($con, inspref($query));
    if (mysqli_num_rows($ris) > 0)
    {
        //print ("<table align='center' border='1'><tr class='prima' align='center'><td>Alunno</td>");
        $tottipoval = "";
        while ($nom = mysqli_fetch_array($ris))
        {
            //   print ("<td>");
            //   print ($nom["sigla"]);

            $aggiungi = true;
            // VERIFICO SE SI TRATTA DI UNA MATERIA DI CATTEDRA SPECIALE
            // E NEL CASO VERIFICO SE L'ALUNNO SEGUE LA MATERIA

            $query = "select distinct tbl_gruppi.idgruppo from tbl_gruppialunni,tbl_alunni,tbl_gruppi
                      where tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
                      and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
                      and tbl_alunni.idclasse=$idclasse
                      and tbl_gruppi.idmateria=" . $nom['idmateria'] . "
                      ";

            $risgru = mysqli_query($con, inspref($query));
            if ($recgru = mysqli_fetch_array($risgru))
            {
                $idgruppo = $recgru['idgruppo'];
                $query = "select * from tbl_gruppialunni where
		            idalunno=$idalunno and idgruppo IN (
		            select distinct tbl_gruppi.idgruppo from tbl_gruppialunni,tbl_alunni,tbl_gruppi 
                    where tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
                    and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
                    and tbl_alunni.idclasse=$idclasse
                    and tbl_gruppi.idmateria=" . $nom['idmateria'] . ")";

                $risgrualu = mysqli_query($con, inspref($query));
                if (mysqli_num_rows($risgrualu) == 0)
                {
                    $aggiungi = false;
                }
            }


            // FINE CONTROLLO
            if ($aggiungi)
            {
                $codmat[] = $nom["idmateria"];
                $sigmat[] = $nom["sigla"];
                $nomemat[] = $nom["denominazione"];
                $listamaterie[] = $nom["sigla"];
                $tipoval[] = $nom["tipovalutazione"];
                $tottipoval = $tottipoval . $nom["tipovalutazione"];
            }
            //   print ("</td>");
        }

        // INSERISCO LA CONDOTTA
        //print ("<td>");
        //print "COMPO";
        $codmat[] = -1;
        $sigmat[] = "COMPO";
        $nomemat[] = "Comportamento";
        $listamaterie[] = "COMPO";
        $tipoval[] = "CU";
        $tottipoval = $tottipoval . "CU";
        //print ("</td>");


        print ("<br><table align='center' border=1><tr class='prima'><td>Materia</td>");
        if (strpos($tottipoval, "S") != false)
        {
            print("<td>Scritto</td>");
        }
        if (strpos($tottipoval, "O") != false)
        {
            print("<td>Orale</td>");
        }
        if (strpos($tottipoval, "P") != false)
        {
            print("<td>Pratico</td>");
        }
        if (strpos($tottipoval, "U") != false)
        {
            print("<td>Unico</td>");
        }
        print ("<td>Ass.</td>");
        print ("<td>Annotazioni</td>");
        print("</tr>");


        for ($nummat = 0; $nummat < count($codmat); $nummat++)
        {
            print ("<tr>");

            print "<td>" . $nomemat[$nummat] . "</td>";

            $cm = $codmat[$nummat];

            if ($tipoval[$nummat][0] == 'N')
            {
                $inizio = 1;
                $fine = 11;
            }
            if ($tipoval[$nummat][0] == 'G')
            {
                $inizio = 11;
                $fine = 20;
            }
            if ($tipoval[$nummat][0] == 'T')
            {
                $inizio = 1;
                $fine = 20;
            }

            if ($tipoval[$nummat][0] == 'C' & $livello_scuola < 4)
            {
                $inizio = 21;
                $fine = 30;
            }
            if ($tipoval[$nummat][0] == 'C' & $livello_scuola == 4)
            {
                $inizio = 1;
                $fine = 11;
            }
            $query = "SELECT votoscritto,votoorale,votopratico,votounico,assenze,note FROM tbl_valutazionifinali
                      WHERE idalunno=$idalunno
                      and idmateria=$cm
                      and periodo='$periodo'";
            $rismedia = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));
            if ($valmedia = mysqli_fetch_array($rismedia))
            {
                $votounico = $valmedia['votounico'];
                $votoscritto = $valmedia['votoscritto'];
                $votoorale = $valmedia['votoorale'];
                $votopratico = $valmedia['votopratico'];
                $assenze = $valmedia['assenze'];
                // $giudizio=$valmedia['giudizio'];
                $note = $valmedia['note'];
                if (strpos($tipoval[$nummat], "S") != false)
                {
                    echo '<td>
                          <select name="scritto_' . $cm . '"><option value=99>&nbsp;';

                    for ($v = $inizio; $v <= $fine; $v = $v + 1)
                    {

                        if ($votoscritto == $v)
                        {
                            echo '<option value=' . $v . ' selected>' . dec_to_vot($v);
                        }
                        else
                        {
                            echo '<option value=' . $v . '>' . dec_to_vot($v);
                        }
                    }
                    echo '</select>
               </td>';
                }
                else
                {
                    if (strpos($tottipoval, "S") != false)
                    {
                        print("<td>&nbsp;</td>");
                    }
                }
                if (strpos($tipoval[$nummat], "O") != false)
                {
                    echo '<td>
              <select name="orale_' . $cm . '"><option value=99>&nbsp;';

                    for ($v = $inizio; $v <= $fine; $v = $v + 1)
                    {
                        if ($votoorale == $v)
                        {
                            echo '<option value=' . $v . ' selected>' . dec_to_vot($v);
                        }
                        else
                        {
                            echo '<option value=' . $v . '>' . dec_to_vot($v);
                        }
                    }
                    echo '</select>
               </td>';
                }
                else
                {
                    if (strpos($tottipoval, "O") != false)
                    {
                        print("<td>&nbsp;</td>");
                    }
                }
                if (strpos($tipoval[$nummat], "P") != false)
                {
                    echo '<td>
                            <select name="pratico_' . $cm . '"><option value=99>&nbsp;';

                    for ($v = $inizio; $v <= $fine; $v = $v + 1)
                    {
                        if ($votopratico == $v)
                        {
                            echo '<option value=' . $v . ' selected>' . dec_to_vot($v);
                        }
                        else
                        {
                            echo '<option value=' . $v . '>' . dec_to_vot($v);
                        }
                    }
                    echo '</select>
               </td>';
                }
                else
                {
                    if (strpos($tottipoval, "P") != false)
                    {
                        print("<td>&nbsp;</td>");
                    }
                }
                if (strpos($tipoval[$nummat], "U") != false)
                {
                    echo '<td>
                            <select name="unico_' . $cm . '"><option value=99>&nbsp;';

                    for ($v = $inizio; $v <= $fine; $v = $v + 1)
                    {
                        if (dec_to_vot($v) != 'NULL')
                        {
                            if ($votounico == $v)
                            {
                                echo '<option value=' . $v . ' selected>' . dec_to_vot($v);
                            }
                            else
                            {
                                echo '<option value=' . $v . '>' . dec_to_vot($v);
                            }
                        }
                    }
                    echo '</select>
               </td>';
                }
                else
                {
                    if (strpos($tottipoval, "U") != false)
                    {
                        print("<td>&nbsp;</td>");
                    }
                }
                if ($cm != -1)
                {
                    print "<td><input type='text' size='2' maxsize='3' value='$assenze' name='ass_$cm'></td>";
                }
                else
                {
                    print "<td>&nbsp;</td>";
                }
                print ("<td><textarea name='not_$cm' cols='45' rows='2' maxlength='180'>$note</textarea></td>");
            }
            else
            {


                if (strpos($tipoval[$nummat], "S") != false)
                {
                    echo '<td>
                             <select name="scritto_' . $cm . '"><option value=99>&nbsp;';

                    for ($v = $inizio; $v <= $fine; $v = $v + 1)
                    {
                        echo '<option value=' . $v . '>' . dec_to_vot($v);
                    }
                    echo '</select>
               </td>';
                }
                else
                {
                    if (strpos($tottipoval, "S") != false)
                    {
                        print("<td>&nbsp;</td>");
                    }
                }
                if (strpos($tipoval[$nummat], "O") != false)
                {
                    echo '<td>
              <select name="orale_' . $cm . '"><option value=99>&nbsp;';

                    for ($v = $inizio; $v <= $fine; $v = $v + 1)
                    {
                        echo '<option value=' . $v . '>' . dec_to_vot($v);
                    }
                    echo '</select>
               </td>';
                }
                else
                {
                    if (strpos($tottipoval, "O") != false)
                    {
                        print("<td>&nbsp;</td>");
                    }
                }
                if (strpos($tipoval[$nummat], "P") != false)
                {
                    echo '<td>
              <select name="pratico_' . $cm . '"><option value=99>&nbsp;';

                    for ($v = $inizio; $v <= $fine; $v = $v + 1)
                    {
                        echo '<option value=' . $v . '>' . dec_to_vot($v);
                    }
                    echo '</select>
               </td>';
                }
                else
                {
                    if (strpos($tottipoval, "P") != false)
                    {
                        print("<td>&nbsp;</td>");
                    }
                }
                if (strpos($tipoval[$nummat], "U") != false)
                {
                    echo '<td>
              <select name="unico_' . $cm . '"><option value=99>&nbsp;';

                    for ($v = $inizio; $v <= $fine; $v = $v + 1)
                    {
                        if (dec_to_vot($v) != 'NULL')  // Le valutazioni di comportamento potrebbero non essere tutte utilizzate
                            echo '<option value=' . $v . '>' . dec_to_vot($v);
                    }
                    echo '</select>
               </td>';
                }
                else
                {
                    if (strpos($tottipoval, "U") != false)
                    {
                        print("<td>&nbsp;</td>");
                    }
                }
                if ($cm != -1)
                {
                    print "<td><input type='text' size='2' maxsize='3' value='' name='ass_$cm'></td>";
                }
                else
                {
                    print "<td>&nbsp;</td>";
                }
                print ("<td><textarea name='not_$cm' cols='45' rows='2' maxlength='180'></textarea></td>");
            }

            print"</tr>";
        }


        print "</table><br><br>";

        $query = "SELECT giudizio FROM tbl_giudizi
           WHERE idalunno=$idalunno
           and idclasse=$idclasse 
           and periodo='$periodo'";
        $risgiud = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));
        if ($valgiud = mysqli_fetch_array($risgiud))
        {
            $giudizio = $valgiud['giudizio'];
        }
        else
        {
            $giudizio = '';
        }
    }

    print "<center><b>Giudizio generale</b><br>";
    print "<textarea name='giudizio' cols=50 rows=5>";
    print $giudizio;
    print "</textarea><br><br>";
    print "<input type='hidden' name='idalunno' value='$idalunno'>";
    print "<input type='hidden' name='cl' value='$nome'>";
    print "<input type='hidden' name='periodo' value='$periodo'>";
    print "<input type='hidden' name='prov' value='$provenienza'>";


// VERIFICO SE LO SCRUTINIO E' ANCORA APERTO

    if (!scrutinio_aperto($idclasse, $periodo, $con))
    {
        print "<center>Scrutinio chiuso!</center>";
    }
    else
    {
        print "<center><input type='submit' value='Registra scrutinio'></center>";
    }
    print "</form>";
}
else
{
    print("<center><b><br>Nessuna valutazione presente!</b></center>");
}

// fclose($fp);
mysqli_close($con);


stampa_piede("");

