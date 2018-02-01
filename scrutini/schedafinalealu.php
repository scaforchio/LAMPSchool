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
$nome = stringa_html('cl');
$idalunno = stringa_html('idalunno');
$scrutintegrativo = stringa_html('integrativo');
$periodo = $numeroperiodi;
$provenienza = stringa_html('prov');
//$anno = stringa_html('anno');


$idclasse = stringa_html('cl');
$id_ut_doc = $_SESSION["idutente"];

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

$titolo = "Scrutinio finale alunno";
$script = "<script>
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
               function CalcolaMedia()
               {
                  var tot = 0;
                  var cont = 0;
                  var med = 0;
                  var flagNC = false;
                  var elements = document.votialu.getElementsByTagName('SELECT'); 
                  for (var i = 0; i < elements.length; i++) 
                  {
                     if (elements[i].id.substr(0,5)=='media')
                     {
                        if ((parseInt(elements[i].value) >= 0) & (parseInt(elements[i].value) <= 10))
                        {
                           tot += parseInt(elements[i].value);
                           cont++;
							   }
							   else
							      if (parseInt(elements[i].value) == 11)
							          flagNC = true;
							   
						   }
                  }
                  var media = document.getElementById('media');
                  if (cont>0)
                     med=tot/cont;
                  else
                     med=0;   
                  med = med.toFixed(2);
                  if (!flagNC)   
                     {
                        media.value = med;
                        mediah.value = med;
							}
                  else
                     {
                         media.value = 'N.C.';
                         mediah.value = 0;
					 }
                      
               }        
         </script>";

stampa_head($titolo, "", $script, "SDMAP");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


//if ($giorno=='')
//   $giorno=date('d');
//if ($mese=='')
//   $mese=date('m');
//if ($anno=='')
//   $anno=date('Y');


print ('
         <form method="post" action="schedafinalealu.php" name="voti">
   
         <p align="center">
         <table align="center">');


/*
  //
  //   Inizio visualizzazione del combo box del periodo
  //
  if ($numeroperiodi==2)
  print('<tr><td width="50%"><b>Quadrimestre</b></td>');
  else
  print('<tr><td width="50%"><b>Trimestre</b></td>');

  echo('   <td width="50%">');
  echo('   <select name="periodo" ONCHANGE="voti.submit()">');

  if ($periodo=='1')
  echo("<option selected value='1'>Primo</option>");
  else
  echo("<option value='1'>Primo</option>");
  if ($periodo=='2')
  echo("<option selected value='2'>Secondo</option>");
  else
  echo("<option value='2'>Secondo</option>");

  if ($numeroperiodi==3)
  if ($periodo=='3')
  echo("<option selected value='3'>Terzo</option>");
  else
  echo("<option value='3'>Terzo</option>");



  echo("</select>");
  echo("</td></tr>");


  //
  //  Fine visualizzazione del quadrimestre
  //

 */


//
//   Classi
//

print('
        <tr>
        <td width="50%"><b>Classe</b></p></td>
        <td width="50%">');

if ($provenienza == 'tab')
    print "<SELECT ID='cl' NAME='cl' ONCHANGE='voti.submit()' disabled><option value=''></option>  ";
else
    print "<SELECT ID='cl' NAME='cl' ONCHANGE='voti.submit()'><option value=''></option>  ";

//
//  Riempimento combobox delle tbl_classi
//
$query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY anno,sezione,specializzazione";
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
    if ($provenienza == 'tab')
        echo("<select name='idalunno' ONCHANGE='voti.submit()' disabled><option value=''>&nbsp");
    else
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



    print "<form method='post' action='insschedafinalealu.php' name='votialu'>";

    // ESTRAGGO TUTTE LE MATERIE PER LA CLASSE
    $query = "SELECT distinct tbl_materie.idmateria,tbl_materie.denominazione,sigla,tipovalutazione FROM tbl_cattnosupp,tbl_materie
              WHERE tbl_cattnosupp.idmateria=tbl_materie.idmateria
              and tbl_cattnosupp.idclasse=$idclasse
              and tbl_cattnosupp.iddocente <> 1000000000
              order by progrpag,tbl_materie.sigla";
    $ris = mysqli_query($con, inspref($query));
    if (mysqli_num_rows($ris) > 0)
    {
        //print ("<table align='center' border='1'><tr class='prima' align='center'><td>Alunno</td>");
        $tottipoval = "";
        while ($nom = mysqli_fetch_array($ris))
        {
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


            //   print ("<td>");
            //   print ($nom["sigla"]);
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

        $codmat[] = -1;
        $sigmat[] = "COMPO";
        $nomemat[] = "Comportamento";
        $listamaterie[] = "COMPO";
        $tipoval[] = "CU";
        $tottipoval = $tottipoval . "CU";
        //print ("</td>");


        print ("<br><table align='center' border=1><tr class='prima'><td>Materia</td>");

        print("<td>Unico</td>");
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

            $query = "SELECT votounico,assenze,note FROM tbl_valutazionifinali
                        WHERE idalunno=$idalunno
                        and idmateria=$cm 
                        and periodo='$periodo'";
            $rismedia = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));
            if ($valmedia = mysqli_fetch_array($rismedia))
            {
                $votounico = $valmedia['votounico'];

                $assenze = $valmedia['assenze'];
                // $giudizio=$valmedia['giudizio'];
                $note = $valmedia['note'];

                if (calcola_media($cm, $con))
                {
                    echo '<td>
                    <select id= "media_' . $cm . '" name="unico_' . $cm . '" ONCHANGE="CalcolaMedia()"><option value=99>&nbsp;';
                }
                else
                {
                    echo '<td>
                    <select id= "unico_' . $cm . '" name="unico_' . $cm . '" ONCHANGE="CalcolaMedia()"><option value=99>&nbsp;';
                }
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

                print "<td><input type='text' size='2' maxsize='3' value='$assenze' name='ass_$cm'></td>";
                print ("<td><textarea name='not_$cm' cols='45' rows='2' maxlength='100'>$note</textarea></td>");
            }
            else
            {


                if (calcola_media($cm, $con))
                {
                    echo '<td>
                    <select id= "media_' . $cm . '" name="unico_' . $cm . '" ONCHANGE="CalcolaMedia()"><option value=99>&nbsp;';
                }
                else
                {
                    echo '<td>
                    <select id= "unico_' . $cm . '" name="unico_' . $cm . '" ONCHANGE="CalcolaMedia()"><option value=99>&nbsp;';
                }

                for ($v = $inizio; $v <= $fine; $v = $v + 1)
                {
                    if (dec_to_vot($v) != 'NULL')
                    {
                        echo '<option value=' . $v . '>' . dec_to_vot($v);
                    }
                }
                echo '</select>
               </td>';


                print "<td><input type='text' size='2' maxsize='3' value='' name='ass_$cm'></td>";
                print ("<td><textarea name='not_$cm' cols='45' rows='2' maxlength='180'></textarea></td>");
            }

            print"</tr>";
        }


        print "</table><br><br>";

// GIUDIZIO

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
    print "<textarea name='giudizio' cols=50 rows=5 maxlength=900>";
    print $giudizio;
    print "</textarea><br><br>";

// ESITO FINALE

    $esito = 0;
    $integrativo = 0;
    $media = 0;
    $credito = 0;
    $validita = 0;

    $query = "SELECT * FROM tbl_esiti
           WHERE idalunno=$idalunno
           ";
    $risesito = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));
    if ($valesito = mysqli_fetch_array($risesito))
    {
        $esito = $valesito['esito'];
        $integrativo = $valesito['integrativo'];
        $media = $valesito['media'];
        $credito = $valesito['credito'];
        $creditotot = $valesito['creditotot'];
        $votoammissione = $valesito['votoammissione'];
        $validita = $valesito['validita'];
    }


    print "<center><b>ESITO FINALE</b><br>";

    if ($livello_scuola == "4" && decodifica_classe_no_spec($idclasse, $con) != 5)
        $giudsosp = "<br><small><small>Lasciare vuoto per<br>'giudizio sospeso'</small></small>";
    else
        $giudsosp = "";

    print "<table border=1><tr class='prima'><td>Validità anno scolastico</td><td>Esito finale$giudsosp</td><td>Voto medio</td>";
    if ($livello_scuola == '4' && decodifica_classe_no_spec($idclasse, $con) != 5)
    {
        print "<td>Esito scr. integr.</td>";
    }
    if (($livello_scuola == '4' && decodifica_classe_no_spec($idclasse, $con) == 3) || ($livello_scuola == '2' && decodifica_classe_no_spec($idclasse, $con) == 3) || ($livello_scuola == '3' && decodifica_classe_no_spec($idclasse, $con) == 8))
    {
        print "<td>Voto di idoneità</td>";
    }
    if (($livello_scuola == '4' && decodifica_classe_no_spec($idclasse, $con) > 2))
    {
        print "<td>Credito scolastico</td>";
    }
    if (($livello_scuola == '4' && decodifica_classe_no_spec($idclasse, $con) > 2))
    {
        print "<td>Credito totale</td>";
    }


    print "</tr>";

    print "<tr>";
    print "<td><select name='validita'>";
    if ($validita == 1)
    {
        print "<option value='1' selected>Valido";
    }
    else
    {
        print "<option value='1'>Valido";
    }
    if ($validita == 2)
    {
        print "<option value='2' selected>Deroga";
    }
    else
    {
        print "<option value='2'>Deroga";
    }
    if ($validita == 3)
    {
        print "<option value='3' selected>Non valido";
    }
    else
    {
        print "<option value='3'>Non valido";
    }
    print "</select></td>";


//
//  Riempimento combobox degli esiti
//
    print "<td>";
    if ($scrutintegrativo != 'yes')
    {
        print "<SELECT ID='esito' NAME='esito'><option value='0'></option>  ";
        if ($livello_scuola == '4')
        {
            $query = "SELECT * FROM tbl_tipiesiti ORDER BY idtipoesito";
        }
        else
        {
            $query = "SELECT * FROM tbl_tipiesiti WHERE passaggio<>2 ORDER BY idtipoesito ";
        }

// print inspref($query);   
        $ris = mysqli_query($con, inspref($query));

        while ($nom = mysqli_fetch_array($ris))
        {
            print "<option value='";
            print ($nom["idtipoesito"]);
            print "'";
            if ($esito == $nom["idtipoesito"])
            {
                print " selected";
            }
            print ">";
            print (str_replace("|", " ", $nom["descrizione"]));
        }
    }
    print "</td>";

    print "<td align='center'>";
    print "<input type='text' name='media' id='media' size='5' value='$media' disabled >";
    print "<input type='hidden' name='mediah' id='mediah' value='$media' size='5'>";
    print "</td>";


    if ($livello_scuola == '4' && decodifica_classe_no_spec($idclasse, $con) != 5)
    {
        //
        //  Riempimento combobox degli esiti
        //
        print "<td>";
        if ($scrutintegrativo == 'yes')
        {
            print "<SELECT ID='esitoint' NAME='esitoint'><option value='0'></option>  ";
            $query = "SELECT * FROM tbl_tipiesiti  WHERE passaggio<>2 ORDER BY idtipoesito";
            $ris = mysqli_query($con, inspref($query));
            while ($nom = mysqli_fetch_array($ris))
            {
                print "<option value='";
                print ($nom["idtipoesito"]);
                print "'";
                if ($integrativo == $nom["idtipoesito"])
                {
                    print " selected";
                }
                print ">";
                print (str_replace("|", " ", $nom["descrizione"]));
            }
        }
        print "</td>";
    }


    if (($livello_scuola == '4' && decodifica_classe_no_spec($idclasse, $con) == 3) || ($livello_scuola == '2' && decodifica_classe_no_spec($idclasse, $con) == 3) || ($livello_scuola == '3' && decodifica_classe_no_spec($idclasse, $con) == 8))
    {
        print "<td align='center'>";


        print "<select name='votoammissione'><option value='0'></option>";
        for ($i = 1; $i <= 10; $i++)
        {
            if ($i == $votoammissione)
            {
                print "<option value = '$i' selected>$i</option>";
            }
            else
            {
                print "<option value = '$i'>$i</option>";
            }
        }

        print "</td>";
    }

    if (($livello_scuola == '4' & decodifica_classe_no_spec($idclasse, $con) > 2))
    {
        print "<td align='center'>";


        print "<select name='credito'><option value='0'></option>";
        for ($i = 1; $i <= 10; $i++)
        {
            if ($i == $credito)
            {
                print "<option value = '$i' selected>$i</option>";
            }
            else
            {
                print "<option value = '$i'>$i</option>";
            }
        }

        print "</td>";
    }

    if (($livello_scuola == '4' & decodifica_classe_no_spec($idclasse, $con) > 2))
    {
        print "<td align='center'>";


        print "<select name='creditotot'><option value='0'></option>";
        for ($i = 1; $i <= 25; $i++)
        {
            if ($i == $creditotot)
            {
                print "<option value = '$i' selected>$i</option>";
            }
            else
            {
                print "<option value = '$i'>$i</option>";
            }
        }

        print "</td>";
    }


    print "</tr></table>";
    print "<script>CalcolaMedia();</script>";
    print "<input type='hidden' name='idalunno' value='$idalunno'>";
    print "<input type='hidden' name='integrativo' value='$scrutintegrativo'>";
    print "<input type='hidden' name='cl' value='$nome'>";
    print "<input type='hidden' name='periodo' value='$periodo'>";
    print "<input type='hidden' name='prov' value='$provenienza'>";


// VERIFICO SE LO SCRUTINIO E' ANCORA APERTO

    if ($scrutintegrativo)
        $per = 9;
    else
        $per = $periodo;

    if (!scrutinio_aperto($idclasse, $per, $con))
    {
        print "<center>Scrutinio chiuso!</center>";
    }
    else
    {
        print "<br><center><input type='submit' value='Registra scrutinio'></center>";
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

