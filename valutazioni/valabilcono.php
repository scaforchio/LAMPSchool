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

$titolo = "Inserimento e modifica voti verifiche";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$cattedra = stringa_html('cattedra');
$tipo = stringa_html('tipo');

$idgruppo = '';

$giorno = stringa_html('gio');

$meseanno = stringa_html('mese');

$abilsel = is_stringa_html('abil') ? stringa_html('abil') : array();
$orainizioold = stringa_html('orainizioold');

$nominativo = "";

$idclasse = "";
$idmateria = "";
$idlezione = "";
$idlezionegruppo = "";

$alunni = array();
$abilita = array();
$abilvoti = array();


$giudizi = array();
$voti = array();
$abilcomb = array();


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


$giornosettimana = giorno_settimana($anno . "-" . $mese . "-" . $giorno);

/*
$a=$anno;
$m=$mese;
$g=$giorno;
*/
print ("
   <form method='post' action='valabilcono.php' name='valabil'>
   
   
   <table align='center'>");

//
//   Leggo il nominativo del docente e lo visualizzo
//


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

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
      </td></tr>");
print("      
      <tr>
          <td><b>Tipo verifica</b>
          </td>
          <td span>
          <SELECT ID ='tipo' NAME='tipo' ONCHANGE='valabil.submit()'><option value=''>&nbsp;</option>");
print("<option value='S'");
if ($tipo == "S") print" selected";
print ">Scritto";
print("<option value='O'");
if ($tipo == "O") print" selected";
print ">Orale";
print("<option value='P'");
if ($tipo == "P") print" selected";
print ">Pratico";


print("</select>
          
          </td>
          
      </tr>");

// VERIFICO SE ESISTE UNA LEZIONE E SE PIU' DI UNA FACCIO SCEGLIERE
if ($idclasse != '' & $idmateria != '' & $giorno != '' & $mese != '')
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


    // Verifico se esiste già qualche lezione nella giornata

    $query = "select idlezione, idlezionegruppo,orainizio, numeroore from tbl_lezioni
           where idclasse='$idclasse' and idmateria='$idmateria' and datalezione='$anno-$mese-$giorno'";
    // print inspref($query);
    $reslezpres = mysqli_query($con, inspref($query)) or die(mysqli_error);
    if (mysqli_num_rows($reslezpres) == 0)
    {
        echo("<tr><td colspan=2><font color='red'><center><b>Non ci sono lezioni per la giornata scelta!</b></center></font></td></tr>");

    }
    if (mysqli_num_rows($reslezpres) == 1)
    {
        $lez = mysqli_fetch_array($reslezpres);
        $idlezione = $lez['idlezione'];
        $idlezionegruppo = $lez['idlezionegruppo'];
    }
    if (mysqli_num_rows($reslezpres) > 1)
    {
        echo "<tr><td>Lezione:</td><td>";
        print "<select name='orainizioold' ONCHANGE='valabil.submit()'><option value=''>&nbsp;</option>";
        while ($vallezpres = mysqli_fetch_array($reslezpres))
        {
            $strore = $vallezpres['orainizio'] . "-" . ($vallezpres['orainizio'] - 1 + $vallezpres['numeroore']);
            if ($strore != $orainizioold)
            {
                print "<option>" . $strore;
            }
            else
            {
                print "<option selected>" . $strore;
                $idlezione = $vallezpres['idlezione'];
                $idlezionegruppo = $lez['idlezionegruppo'];
            }

        }
        print "</select>";
        echo "</td></tr>";
    }


}


// if ($idmateria!="" & $tipo!="" & $idclasse!="" &  checkdate($mese,$giorno,$anno))
if ($idlezione != "" & $tipo != "")
{
    // VERIFICO SE LA LEZIONE E' UNA LEZIONE DI GRUPPO


    $query = "select tbl_valutazioniabilcono.idabilita
	              from tbl_valutazioniabilcono,tbl_valutazioniintermedie where 
	              tbl_valutazioniabilcono.idvalint = tbl_valutazioniintermedie.idvalint 
	              and tbl_valutazioniintermedie.idlezione='$idlezione'
	              and not tbl_valutazioniabilcono.pei
	              and tipo='$tipo'";


//	$query="select tbl_valutazioniabilcono.idabilita, tbl_valutazioniintermedie.idalunno,tbl_valutazioniintermedie.voto,tbl_valutazioniintermedie.giudizio
//	              from tbl_valutazioniabilcono,tbl_valutazioniintermedie,  tbl_alunni, tbl_classi where 
//	              tbl_valutazioniabilcono.idvalint = tbl_valutazioniintermedie.idvalint and
//	              tbl_valutazioniintermedie.idalunno=tbl_alunni.idalunno
//	              and tbl_alunni.idclasse = tbl_classi.idclasse 
//	              and tbl_valutazioniintermedie.idmateria='$idmateria' 
//	              and tbl_alunni.idclasse='$idclasse'
//	              and data='$anno-$mese-$giorno'
//	              and tipo='$tipo'" ;              

    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

    while ($nom = mysqli_fetch_array($ris))
    {

        //$alunni[]=$nom['idalunno'];
        $abilita[] = $nom['idabilita'];
        //$giudizi[]=$nom['giudizio'];
        //$voti[]=$nom['voto'];
    }
    //if (count($abilita)==0)
//	{

    $abilcomb = array_unique(array_merge($abilita, $abilsel));
    // Il codice seguente serve a ricompattare l'array dopo la funzione unique
    $abco = array();
    foreach ($abilcomb as $ac)
        $abco[] = $ac;
    $abilcomb = $abco;
    // sort($abilcomb);


//	}   
    // Carico in una combobox a scelta multipla tutte le voci della programmazione già presenti

    print "<tr>
						 <td valign='top' align='center' colspan=2> <b>Abilit&aacute; e conoscenze verificate:</b><br/>";
    // Conto competenze, abilità e conoscenze per dimensionare la select multiple
    $query = "select count(*) as numcomp from tbl_competdoc
	              where idmateria='$idmateria' and idclasse='$idclasse'";
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    $nomcomp = mysqli_fetch_array($ris);
    $numcomp = $nomcomp['numcomp'];

    $query = "select count(*) as numabil from tbl_abildoc,tbl_competdoc
	              where tbl_abildoc.idcompetenza=tbl_competdoc.idcompetenza 
	              and idmateria='$idmateria' and idclasse='$idclasse'";
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    $nomabil = mysqli_fetch_array($ris);
    $numabil = $nomabil['numabil'];

    $totalerighe = $numabil + $numcomp;

    print "<select multiple size=$totalerighe name='abil[]'>";
    $query = "select * from tbl_competdoc
	              where idmateria='$idmateria' and idclasse='$idclasse'
	              order by numeroordine";
    $riscomp = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

    while ($nomcomp = mysqli_fetch_array($riscomp))
    {
        $idcompetenza = $nomcomp['idcompetenza'];

        print "<optgroup label='" . $nomcomp['numeroordine'] . ". " . $nomcomp['sintcomp'] . "'>";

        //CARICO LE CONOSCENZE

        $query = "select * from tbl_abildoc
	              where idcompetenza='$idcompetenza'
	              and abil_cono = 'C'
	              order by numeroordine";

        $risabil = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

        while ($nomabil = mysqli_fetch_array($risabil))
        {
            $idabilita = $nomabil['idabilita'];
            $sintabil = $nomabil['sintabilcono'];
            print "<option value='$idabilita' ";

            if (CercaAbilita($idabilita, $abilcomb)) print "SELECTED ";
            print ">CO." . $nomcomp['numeroordine'] . "." . $nomabil['numeroordine'] . " $sintabil</option>";
        }
        //CARICO LE COMPETENZE
        $query = "select * from tbl_abildoc
	              where idcompetenza='$idcompetenza'
	              and abil_cono = 'A'
	              order by numeroordine";

        $risabil = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

        while ($nomabil = mysqli_fetch_array($risabil))
        {
            $idabilita = $nomabil['idabilita'];
            $sintabil = $nomabil['sintabilcono'];
            print "<option value='$idabilita' ";
            if (CercaAbilita($idabilita, $abilcomb)) print "SELECTED ";
            print "> AB." . $nomcomp['numeroordine'] . "." . $nomabil['numeroordine'] . " $sintabil</option>";
        }
        print "</optgroup>";

    }
    print "</select>";
    print("</td></tr>");
    // il submit è necessario per la select multiple
    print("</table><center><input type='submit' value='Aggiorna maschera'></center></form>");

}


else
{
    print "</table>";
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
elseif (count($abilcomb) == 0)
{
    print "";
}
else
{
    $numero = count($abilcomb);
    // print $numero;


    // CARICO I VOTI GIA' INSERITI
    $arrcodabil = riempi_array_codici_abilcono($abilcomb, $cattedra, $con);


    print "<form name='registra' action='insvalabilcono.php' method='POST'><table border=1 align='center'>";
    print "
              <tr class='prima'>
          
          <td><b> Alunno </b></td>
          <td><b> Data di nascita </b></td>";


    for ($i = 1; $i <= count($arrcodabil[0]); $i++)
        print "<td>" . $arrcodabil[0][$i - 1] . "</td>";

    //    <td><b>Voti </b></td>

    print"</tr>";

    $queryval = "SELECT tbl_valutazioniabilcono.voto AS voto,tbl_valutazioniabilcono.idabilita AS idabilcono,idalunno
                   FROM tbl_valutazioniabilcono, tbl_valutazioniintermedie
                     WHERE tbl_valutazioniabilcono.idvalint=tbl_valutazioniintermedie.idvalint
                     AND idlezione='" . $idlezione . "' AND tipo = '" . $tipo . "'";
    $risval = mysqli_query($con, inspref($queryval)) or die ("Errore nella query: " . mysqli_error($con));
    while ($valval = mysqli_fetch_array($risval))
    {

        $voti[] = $valval["voto"];
        $abilvoti[] = $valval["idabilcono"];
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
        $idlezionegruppo = estrai_lezione_gruppo($idlezione, $con);
        $query = "select tbl_alunni.idalunno,cognome,nome,datanascita
          from tbl_gruppi,tbl_gruppialunni,tbl_alunni
          where
             tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
             and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
             and tbl_alunni.idclasse=$idclasse
             and tbl_gruppi.idgruppo= (select idgruppo from tbl_lezionigruppi where idlezionegruppo=$idlezionegruppo)"; //=$idgruppo";
    }
    // (select idgruppo from tbl_lezionigruppi where idlezionegruppo=$idlezionegruppo)
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . inspref($query));
    while ($val = mysqli_fetch_array($ris))
    {
        if (!alunno_certificato_pei($val['idalunno'], $idmateria, $con))
        {
            $esiste_voto = false;
            $presaltro = CercaAltroVoto($tipo, $val['idalunno'], $idlezione, $con);

            print "
				 <tr>
				 
				 <td><b>" . $val['cognome'] . " " . $val['nome'] . " </b></td>
				 <td><b>" . data_italiana($val['datanascita']) . " </b></td> ";

            $ac = alunno_certificato($val['idalunno'], $con);
            $acn = alunno_certificato_norm($val['idalunno'], $idmateria, $con);
            //(!alunno_certificato($val['idalunno'],$con)) || alunno_certificato_norm($val['idalunno'],$idmateria,$con)

            if (!$presaltro)
            {

                for ($i = 0; $i < count($arrcodabil[1]); $i++)
                {
                    $esiste_voto = false;
                    $voto = CercaVoto($val['idalunno'], $arrcodabil[1][$i], $alunni, $abilvoti, $voti);

                    if ($voto != 0) $esiste_voto = true;
                    if ($esiste_voto)
                    {
                        print "<td>
								<select name='voto" . $val['idalunno'] . "_" . $arrcodabil[1][$i] . "'><option value=99>&nbsp;";

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
                        $obmin = $arrcodabil[2][$i];
                        if ($obmin || !$ac || $acn)
                        {
                            echo "<td>
									  <select name='voto" . $val['idalunno'] . "_" . $arrcodabil[1][$i] . "'><option value=99>&nbsp;";
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
                        else
                        {
                            echo "<td></td>";
                        }
                    }

                }
            }

            echo "</tr>";
        }
    }
    echo "</table>";




    print("
	   <input type=hidden value=" . $idclasse . " name=cl>
       <input type=hidden value=" . $giorno . " name=gio>
       <input type=hidden value=" . $mese . " name=mese>
       <input type=hidden value=" . $anno . " name=anno>
       <input type=hidden value=" . $idmateria . " name=materia>
       <input type=hidden value='" . $idlezione . "' name=idlezione>
       <input type=hidden value='" . $orainizioold . "' name=orainizioold>
       <input type=hidden value=" . $tipo . " name=tipo>
       <input type=hidden value=" . $iddocente . " name=iddocente>
	   <input type=hidden value=" . $cattedra . " name=cattedra>");
	   
	   if (controlla_scadenza($maxgiorniritardolez,$giorno,$mese,$anno))  // Verifica se non è passato il tempo e che non c'è una deroga
          {
              print "<center><input type='submit' value='Inserisci voti'></center>";
          }
          else
          {
              print '<p align="center"><font color="red"><b>Tempo scaduto per modifica lezioni! Rivolgersi a dirigente scolastico!</b></font></p>';
          }
          print "</center></form>";


}


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

function CercaGiudizio($codice, $alu, $giu)
{
    $numvot = count($alu);

    $giudtro = 0;

    for ($i = 0; $i < $numvot; $i++)
    {

        if ($codice == $alu[$i])

        {
            $giudtro = $giu[$i];
        }
    }
    return $giudtro;
}

function CercaAbilita($codice, $abilarr)
{
    $numabil = count($abilarr);

    $trovato = false;

    for ($i = 0; $i < $numabil; $i++)
    {

        if ($codice == $abilarr[$i])

        {
            $trovato = true;
        }
    }
    return $trovato;
}

function riempi_array_codici_abilcono($abilcono, $idcattedra, $conn)
{
    $arr = array(array(), array(), array());
    $idmateria = estrai_id_materia($idcattedra, $conn);
    $idclasse = estrai_id_classe($idcattedra, $conn);
    $query = "select idabilita, tbl_abildoc.abil_cono, tbl_abildoc.numeroordine as noabcon, tbl_competdoc.numeroordine as nocomp, obminimi
		        from tbl_abildoc, tbl_competdoc
		        where tbl_abildoc.idcompetenza=tbl_competdoc.idcompetenza
		        and tbl_competdoc.idmateria = $idmateria and  tbl_competdoc.idclasse = $idclasse
		        order by abil_cono DESC, nocomp, noabcon";


    $res = mysqli_query($conn, inspref($query)) or die (mysqli_error($conn));

    while ($val = mysqli_fetch_array($res))
    {
        if (CercaAbilita($val['idabilita'], $abilcono))
        {
            $arr[0][] = $val['abil_cono'] . " " . $val['nocomp'] . "." . $val['noabcon'];
            $arr[1][] = $val['idabilita'];
            $arr[2][] = $val['obminimi'];
        }
    }
    return $arr;
}

function CercaAltroVoto($tipo, $alunno, $idlezione, $con)
{
    $query = "select * from tbl_valutazioniintermedie
	        where tipo='$tipo' 
	        and idlezione='$idlezione'
	        and idalunno='$alunno'
	        and voto<>99";


    $risvotiprec = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    if (mysqli_num_rows($risvotiprec) > 0)
    {
        $vot = mysqli_fetch_array($risvotiprec);
        $codvoto = $vot['idvalint'];
        $query2 = "select * from tbl_valutazioniabilcono
	        where idvalint='$codvoto'";
        $risvotiabprec = mysqli_query($con, inspref($query2)) or die ("Errore nella query: " . mysqli_error($con));
        if (mysqli_num_rows($risvotiabprec) == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}               

