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
$iddocente = $_SESSION["idutente"];
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

//
//    Parte iniziale della pagina
//

$titolo = "Inserimento e modifica voti di comportamento";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


$cattedra = stringa_html('cattedra');

$idgruppo = '';

$giorno = stringa_html('gio');

$meseanno = stringa_html('mese');


//$nominativo = "";

$idclasse = estrai_id_classe($cattedra, $con);
$idmateria = estrai_id_materia($cattedra, $con);

// print "ttt Classe $idclasse <br>Materia $idmateria <br>Giorno $giorno <br>Mese $meseanno <br>";



$alunni = array();
$subobiettivi = array();
$subobvoti = array();


$giudizi = array();
$voti = array();
//$abilcomb = array();
// Divido il mese dall'anno
$mese = substr($meseanno, 0, 2);
$anno = substr($meseanno, 5, 4);

if ($giorno == '')
{
    $giorno = date('d');
}
if ($mese == '')
{
    $mese = date('m');
}
if ($anno == '')
{
    $anno = date('Y');
}

$data = $anno . "-" . $mese . "-" . $giorno;
$giornosettimana = giorno_settimana($anno . "-" . $mese . "-" . $giorno);



print ("
   <form method='post' action='valcomp.php' name='valabil'>
   
   
   <table align='center'>");

//
//   Leggo il nominativo del docente e lo visualizzo
//


$query = "select iddocente, cognome, nome from tbl_docenti where iddocente='$iddocente'";

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


print("
   
   <tr>
      <td width='50%'><b>Cattedra</b></td>
      <td width='50%'>
      <SELECT ID='cattedra' NAME='cattedra' ONCHANGE='valabil.submit()'> <option value=''>&nbsp; ");


$query = "select idcattedra,tbl_classi.idclasse,tbl_materie.idmateria, anno, sezione, specializzazione, denominazione from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente='$iddocente' and tbl_cattnosupp.idalunno=0 and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria order by anno, sezione, specializzazione, denominazione";
$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idcattedra"]);
    print "'";
    if ($cattedra == $nom["idcattedra"])
    {
        print " selected";
        $idmateria = $nom["idmateria"];   // Memorizzo materia e classe della cattedra selezionata
        $idclasse = $nom["idclasse"];
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

print "</select>";
print ("
         

         <tr>
         <td width='50%'><b>Data (gg/mm/aaaa)</b></td>");


//
//   Inizio visualizzazione della data
//


print("   <td width='50%'>");
print("   <select name='gio'  ONCHANGE='valabil.submit()'>");
for ($g = 1; $g <= 31; $g++)
{
    if ($g < 10)
    {
        $gs = '0' . $g;
    }
    else
    {
        $gs = '' . $g;
    }
    if ($gs == $giorno)
    {
        print("<option selected>$gs");
    }
    else
    {
        print("<option>$gs");
    }
}
print("</select>");

print("   <select name='mese' ONCHANGE='valabil.submit()'>");
for ($m = 9; $m <= 12; $m++)
{
    if ($m < 10)
    {
        $ms = '0' . $m;
    }
    else
    {
        $ms = '' . $m;
    }
    if ($ms == $mese)
    {
        print("<option selected>$ms - $annoscol");
    }
    else
    {
        print("<option>$ms - $annoscol");
    }
}
$annoscolsucc = $annoscol + 1;
for ($m = 1; $m <= 8; $m++)
{
    if ($m < 10)
    {
        $ms = '0' . $m;
    }
    else
    {
        $ms = '' . $m;
    }
    if ($ms == $mese)
    {
        print("<option selected>$ms - $annoscolsucc");
    }
    else
    {
        print("<option>$ms - $annoscolsucc");
    }
}
print("</select>");


print("        
      </td></tr></table></form>");


// VERIFICO SE ESISTONO GIA' VALUTAZIONI
if ($idclasse != '' & $iddocente != '' & $idmateria != '' & $giorno != '' & $mese != '')
{

    // VERIFICO SE SI TRATTA DI UNA CATTEDRA LEGATA A UN GRUPPO
    $query = "select distinct tbl_gruppi.idgruppo from tbl_gruppialunni,tbl_alunni,tbl_gruppi
           where tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
             and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
             and tbl_alunni.idclasse=$idclasse
             and tbl_gruppi.idmateria=$idmateria
             and tbl_gruppi.iddocente=$iddocente";
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    if ($rec = mysqli_fetch_array($ris))
    {
        $idgruppo = $rec['idgruppo'];
    }
}


// $arrabil=array_merge($abilita,$abilsel);
if (!checkdate($mese, $giorno, $anno))
{
    print "<center>Il giorno selezionato non è valido.</center>";
}
elseif (($giornosettimana == "Dom"))
{
    print "<center>Il giorno selezionato è una domenica.</center>";
}
elseif ($cattedra == "")
{
    print "";
}
else
{

    // CARICO I VOTI GIA' INSERITI

    $arrcodsubob = riempi_array_codici_valsubob($con);

    print "<form name='registra' action='insvalcomp.php' method='POST'><table border=1 align='center'>";
    print "
              <tr class='prima'>
          
          <td><b> Alunno </b></td>
          <td><b> Data di nascita </b></td>";


    for ($i = 1; $i <= count($arrcodsubob[0]); $i++)
        print "<td>" . $arrcodsubob[0][$i - 1] . "</td>";

    //    <td><b>Voti </b></td>

    print"</tr>";

    // ESTRAGGO TUTTE LE VALUTAZIONI GIA' INSERITE E LE INSERISCO NEGLI ARRAY
    $queryval = "SELECT tbl_valutazioniobcomp.voto AS voto,tbl_valutazioniobcomp.idsubob AS idsubob,idalunno
                   FROM tbl_valutazioniobcomp, tbl_valutazionicomp
                     WHERE tbl_valutazioniobcomp.idvalcomp=tbl_valutazionicomp.idvalcomp
                     AND data='" . $data . "' AND idmateria=$idmateria AND iddocente=$iddocente";
    $risval = mysqli_query($con, inspref($queryval)) or die("Errore nella query: " . mysqli_error($con));

    while ($valval = mysqli_fetch_array($risval))
    {

        $voti[] = $valval["voto"];
        $subobvoti[] = $valval["idsubob"];
        $alunni[] = $valval["idalunno"];
    }


    // $query='select * from tbl_alunni where idclasse="'.$idclasse.'" order by cognome,nome';


    if ($idgruppo == "")
    {
        $query = "select idalunno,cognome,nome,datanascita from tbl_alunni
          where idclasse=$idclasse
          order by cognome, nome, datanascita";
    }
    else
    {

        $query = "select tbl_alunni.idalunno,cognome,nome,datanascita
          from tbl_gruppi,tbl_gruppialunni,tbl_alunni
          where
             tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
             and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
             and tbl_alunni.idclasse=$idclasse
             and tbl_gruppi.idgruppo= $idgruppo";
    }

    $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . inspref($query));
    while ($val = mysqli_fetch_array($ris))
    {

        $esiste_voto = false;

        print "
				 <tr>
				 
				 <td><b>" . $val['cognome'] . " " . $val['nome'] . " </b></td>
				 <td><b>" . data_italiana($val['datanascita']) . " </b></td> ";


        for ($i = 0; $i < count($arrcodsubob[1]); $i++)
        {
            $esiste_voto = false;
            $voto = CercaVoto($val['idalunno'], $arrcodsubob[1][$i], $alunni, $subobvoti, $voti);

            if ($voto != 0)
                $esiste_voto = true;
            if ($esiste_voto)
            {
                print "<td><select name='voto" . $val['idalunno'] . "_" . $arrcodsubob[1][$i] . "'><option value=99>&nbsp;";

                if ($ordinevalutazioni == 'C')
                {
                    for ($v = $votominimoattribuibile; $v <= 10; $v = $v + 0.25)
                    {
                        if ($voto == $v)
                        {
                            echo '<option value=' . $v . ' selected>' . dec_to_mod($v);
                        }
                        else
                        {
                            echo '<option value=' . $v . '>' . dec_to_mod($v);
                        }
                    }
                }
                else
                {
                    for ($v = 10; $v >= $votominimoattribuibile; $v = $v - 0.25)
                    {
                        if ($voto == $v)
                        {
                            echo '<option value=' . $v . ' selected>' . dec_to_mod($v);
                        }
                        else
                        {
                            echo '<option value=' . $v . '>' . dec_to_mod($v);
                        }
                    }
                }
                echo "</select>
									  </td>";
            }
            else
            {

                echo "<td>
									  <select name='voto" . $val['idalunno'] . "_" . $arrcodsubob[1][$i] . "'><option value=99>&nbsp;";
                if ($ordinevalutazioni == 'C')
                {
                    for ($v = $votominimoattribuibile; $v <= 10; $v = $v + 0.25)
                    {

                        echo '<option value=' . $v . '>' . dec_to_mod($v);
                    }
                }
                else
                {
                    for ($v = 10; $v >= $votominimoattribuibile; $v = $v - 0.25)
                    {

                        echo '<option value=' . $v . '>' . dec_to_mod($v);
                    }
                }
                echo "</select>
										  </td>";
            }
        }


        echo "</tr>";
    }
    echo "</table>";


    print("
	   <input type=hidden value=" . $idclasse . " name=cl>
           <input type=hidden value=" . $giorno . " name=gio>
           <input type=hidden value=" . $mese . " name=mese>
           <input type=hidden value=" . $anno . " name=anno>
           <input type=hidden value=" . $idmateria . " name=materia>
           <input type=hidden value=" . $idgruppo . " name=idgruppo>
           <input type=hidden value=" . $iddocente . " name=iddocente>
	   <input type=hidden value=" . $cattedra . " name=cattedra>
	   
	   <center><br><input type='submit' value='Inserisci voti'></center></form>");
}

/*
 * VISUALIZZAZIONE OBIETTIVI
 */
print "<center>OBIETTIVI DI COMPORTAMENTO<br/>";
print "</center>";

$query = "select * from tbl_compob order by numeroordine";
// print inspref($query);
$ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));

print "<font size=1>";
print "<table border='1'>";
while ($val = mysqli_fetch_array($ris))
{
    $numord = $val["numeroordine"];
    $sintob = $val["sintob"];
    $obiettivo = $val["obiettivo"];
    $idobiettivo = $val["idobiettivo"];
    print "<tr valign='top'><td><b>$numord. $sintob</b><br> <small> $obiettivo</small></td>";

    $query = "select * from tbl_compsubob where idobiettivo=$idobiettivo order by numeroordine";
    $risabil = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));
    print "<font size=1>";
    while ($valabil = mysqli_fetch_array($risabil))
    {
        $sintsubob = $valabil["sintsubob"];
        $numordsubob = $valabil["numeroordine"];
        $subob = $valabil["subob"];


        print "<td>$numord.$numordsubob $sintsubob</b><br><small> $subob</small></td>";
    }

    print "</tr>";
    print "</font>";
}
print "</table>";

print "</font>";

print "<br>";
/*
 * FINE VISUALIZZAZIONE OBIETTIVI
 */



mysqli_close($con);
stampa_piede("");

function CercaVoto($codalu, $codabil, $arralu, $arrabi, $arrvot)
{
    $numvot = count($arralu);

    $vototro = 0;

    for ($i = 0; $i < $numvot; $i++)
    {

        if ($codalu == $arralu[$i] && $codabil == $arrabi[$i])
        {
            $vototro = $arrvot[$i];
        }
    }
    return $vototro;
}

function riempi_array_codici_valsubob($conn)
{
    $arr = array(array(), array());

    $query = "SELECT idsubob, tbl_compsubob.subob, tbl_compsubob.numeroordine AS nosubob, tbl_compob.numeroordine AS noob
		        FROM tbl_compsubob, tbl_compob
		        WHERE tbl_compsubob.idobiettivo=tbl_compob.idobiettivo
		        ORDER BY noob, nosubob";


    $res = mysqli_query($conn, inspref($query)) or die(mysqli_error($conn));

    while ($val = mysqli_fetch_array($res))
    {

        $arr[0][] = $val['noob'] . "." . $val['nosubob'];
        $arr[1][] = $val['idsubob'];
    }
    return $arr;
}
