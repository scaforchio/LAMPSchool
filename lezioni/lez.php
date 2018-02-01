<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo modificarlo 
* secondo i termini della 
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


// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$id_ut_doc = $_SESSION['idutente'];

$classeregistro = $_SESSION['classeregistro'];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


// preparazione del link per tornare indietro nel registro di classe
$goback = goBackRiepilogoRegistro($con);

$titolo = "Gestione lezione";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri)
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a>$goback[1] - $titolo", "", "$nome_scuola", "$comune_scuola");



$cattedra = '';
$giorno = '';
$meseanno = '';
$anno = '';
$mese = '';
$idclasse = '';
$materia = '';
$iddocente = '';
$idlezione = '';
$orainizionew = '';
$orainizioold = '';


$idgruppo = '';
// Creo un array per verificare le ore già impegnate da lezioni
$oredisp = array();
$oredisp[] = 9;
for ($i = 1; $i <= $numeromassimoore; $i++)
    $oredisp[] = 0;

// CODICE PER GESTIONE RICHIAMO DA RIEPILOGO


$idlezione = stringa_html('idlezione');

$provenienza = stringa_html('provenienza');
$cattedra = stringa_html('cattedra');

$giorno = stringa_html('gio');

$meseanno = stringa_html('meseanno');

$orainizionew = stringa_html('orainizionew');
$orainizioold = stringa_html('orainizioold');

$anno = substr($meseanno, 5, 4);
$mese = substr($meseanno, 0, 2);

$giornosettimana = "";

//print "Id lez. $idlezione";
if ($idlezione != "")
{
    $query = "select * from tbl_lezioni where idlezione=$idlezione";
    $ris = mysqli_query($con, inspref($query));
    $lez = mysqli_fetch_array($ris);
    $materia = $lez['idmateria'];
    $idclasse = $lez['idclasse'];
    $iddocente = $lez['iddocente'];
    $orainizioold = $lez['orainizio'] . "-" . ($lez['orainizio'] - 1 + $lez['numeroore']);

    // GESTIONE LEZIONI A GRUPPI
    //  $idlezionegruppo=$lez['idlezionegruppo'];
    // 4/8/2014 $query="select idcattedra from tbl_cattnosupp where idclasse=$idclasse and idmateria=$materia and iddocente=$iddocente";
    $query = "select idcattedra from tbl_cattnosupp where idclasse=$idclasse and idmateria=$materia and iddocente=$id_ut_doc";
    //  print $query;       
    $ris = mysqli_query($con, inspref($query));
    if ($nom = mysqli_fetch_array($ris))
    {
        $cattedra = $nom['idcattedra'];
    }

    $giorno = substr($lez['datalezione'], 8, 2);
    $anno = substr($lez['datalezione'], 0, 4);
    $mese = substr($lez['datalezione'], 5, 2);
    $giornosettimana = giorno_settimana($anno . "-" . $mese . "-" . $giorno);
    $meseanno = $mese . " - " . $anno;

}

// FINE CODICE PER GESTIONE DA RIEPILOGO
else
{

    if ($cattedra != "" & $giorno != "" & $meseanno != "") // & $orainizio!="")
    {

        $mese = substr($meseanno, 0, 2);
        $anno = substr($meseanno, 5, 4);


        $query = "select idclasse, idmateria from tbl_cattnosupp where idcattedra=$cattedra";

        $ris = mysqli_query($con, inspref($query));
        if ($nom = mysqli_fetch_array($ris))
        {
            $materia = $nom['idmateria'];
            $idclasse = $nom['idclasse'];
        }


    }
}



if ($giorno == '')
{
    $giorno = date('d');
}
if ($mese == '')
{
    $mese = date('m');
}
if ($anno == '')   // rIVEDERE
{
    $anno = date('Y');
}

print '
    <form method="lez.php" name="voti">
          <input type="hidden" name="goback" value="' . $goback[0] . '">
          <input type="hidden" name="idclasse" value="' . $idclasse . '">
    <table align="center">';

if ($provenienza != "")
{
    if ($provenienza == 'argo')
    {
        print ("<tr><td colspan=2 align=center><font size=1><a href='riepargom.php?idlezione=" . $idlezione . "'>Ritorna a riepilogo</a><br/>&nbsp;</td></tr>");
    }
    else
    {
        if ($provenienza == 'tabe')
        {
            print ("<tr><td colspan=2 align=center><font size=1><a href='sitleztota.php?idlezione=" . $idlezione . "'>Ritorna a riepilogo</a><br/>&nbsp;</td></tr>");
        }
    }
}


print ('         <tr>
         <td width="50%"><b>Data (gg/mm/aaaa)</b></td>');


//
//   Inizio visualizzazione della data
//


echo('   <td width="50%">');
if ($provenienza == "" && $classeregistro=="")
{
    echo('   <select name="gio"  ONCHANGE="voti.submit()">');
}
else
{
    print ("<input type='hidden' name='gio' value='$giorno'>");
    echo('   <select name="gio" disabled>');
}
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
        echo("<option selected>$gs</option>");
    }
    else
    {
        echo("<option>$gs</option>");
    }
}
echo("</select>");


if ($provenienza == "" && $classeregistro=="")
{
    echo('   <select name="meseanno" ONCHANGE="voti.submit()">');
}
else
{
    print ("<input type='hidden' name='meseanno' value='$meseanno'>");
    echo('   <select name="meseanno" disabled>');
}
for ($m = 9; $m <= 12; $m++)
{
    if ($m < 10)
    {
        $ms = "0" . $m;
    }
    else
    {
        $ms = '' . $m;
    }
    if ($ms == $mese)
    {
        echo("<option selected>$ms - $annoscol</option>");
    }
    else
    {
        echo("<option>$ms - $annoscol</option>");
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
        echo("<option selected>$ms - $annoscolsucc</option>");
    }
    else
    {
        echo("<option>$ms - $annoscolsucc</option>");
    }
}
echo("</select>");

//
//  Fine visualizzazione della data
//


echo("        
      </td></tr>");


//
//   Leggo il nominativo del docente e lo visualizzo
//

if ($materia != "" and $idclasse != "")
{
    if ($id_ut_doc != $iddocente)   // Se la visualizzazione avviene da altro docente
    {
        if (cattedra($id_ut_doc, $materia, $idclasse, $con))
        {
            $query = "select iddocente, cognome, nome from tbl_docenti where idutente=$id_ut_doc";
        }
        else
        {
            $iddocente = $id_ut_doc;
            $query = "select iddocente, cognome, nome from tbl_docenti where idutente=$iddocente";
        }
    }
    else
    {
        $query = "select iddocente, cognome, nome from tbl_docenti where idutente=$id_ut_doc";
    }
}
else
{
    $query = "select iddocente, cognome, nome from tbl_docenti where idutente=$id_ut_doc";
}

// print $query;
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
//   Classi
//

print('
        <tr>
        <td width="50%"><b>Cattedra</b></p></td>
        <td width="50%"> ');

if ($provenienza == "")
{
    $abilitato = "";
}
else
{
    $abilitato = " disabled ";
}

//if ($classeregistro=="")
print ("<SELECT ID='cattedra' $abilitato NAME='cattedra' ONCHANGE='voti.submit()'><option value=''>&nbsp;");
//else
//    print ("<SELECT ID='cattedra' $abilitato NAME='cattedra' ONCHANGE='voti.submit()'>");

//
//  Riempimento combobox delle tbl_classi/materie
//


// TTTT 03/08/2015 Se la classe è stata già selezionata faccio in modo da visualizzare solo le cattedre della classe
if ($classeregistro == "")
{
    $query = "select idcattedra,tbl_classi.idclasse,tbl_cattnosupp.idmateria, anno, sezione, specializzazione, denominazione
        from tbl_cattnosupp, tbl_classi, tbl_materie 
        where iddocente=$iddocente 
        and tbl_cattnosupp.idclasse=tbl_classi.idclasse 
        and tbl_cattnosupp.idmateria = tbl_materie.idmateria
        order by anno, sezione, specializzazione, denominazione";
}
else
{
    $query = "select idcattedra,tbl_classi.idclasse,tbl_cattnosupp.idmateria, anno, sezione, specializzazione, denominazione
        from tbl_cattnosupp, tbl_classi, tbl_materie
        where iddocente=$iddocente
        and tbl_cattnosupp.idclasse=$classeregistro
        and tbl_cattnosupp.idclasse=tbl_classi.idclasse
        and tbl_cattnosupp.idmateria = tbl_materie.idmateria
        order by anno, sezione, specializzazione, denominazione";
}
$strvisold = "";
$ris = mysqli_query($con, inspref($query));
if (mysqli_num_rows($ris) == 1)
{
    $nom = mysqli_fetch_array($ris);
    $strvis = $nom['anno'] . "&nbsp;" . $nom['sezione'] . "&nbsp;" . $nom['specializzazione'] . "&nbsp;-&nbsp;" . $nom['denominazione'];

    print "<option value='";
    print ($nom["idcattedra"]);
    print "'";


    print " selected";

    print ">$strvis";
    $idclasse = $nom['idclasse'];
    $materia = $nom['idmateria'];
}
else
{
    while ($nom = mysqli_fetch_array($ris))
    {
        $strvis = $nom['anno'] . "&nbsp;" . $nom['sezione'] . "&nbsp;" . $nom['specializzazione'] . "&nbsp;-&nbsp;" . $nom['denominazione'];
        if ($strvis != $strvisold)
        {
            print "<option value='";
            print ($nom["idcattedra"]);
            print "'";

            if ($idclasse == $nom["idclasse"] & $materia == $nom["idmateria"])
            {
                print " selected";
            }
            print ">$strvis";
            //print ($nom["anno"]);
            //print "&nbsp;";
            //print($nom["sezione"]);
            //print "&nbsp;";
            //print($nom["specializzazione"]);
            //print "&nbsp;-&nbsp;";
            //print($nom["denominazione"]);
            $strvisold = $strvis;
        }
    }
}

echo('
      </SELECT>
      </td></tr>');

echo("<tr><td><b>Ore lezione (prima-ultima):</b></td><td>");


if ($idclasse != '' & $materia != '' & $giorno != '' & $mese != '')
{

    // VERIFICO SE SI TRATTA DI UNA CATTEDRA LEGATA A GRUPPI
    $query = "select distinct tbl_gruppi.idgruppo from tbl_gruppialunni,tbl_alunni,tbl_gruppi
           where tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
             and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
             and tbl_alunni.idclasse=$idclasse
             and tbl_gruppi.idmateria=$materia
             and tbl_gruppi.iddocente=$iddocente";
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    if ($rec = mysqli_fetch_array($ris))
    {
        $idgruppo = $rec['idgruppo'];
    }


    // Verifico se esiste già qualche lezione nella giornata

    $query = "select idlezione, orainizio, numeroore from tbl_lezioni
           where idclasse='$idclasse' and idmateria='$materia' and datalezione='$anno-$mese-$giorno'";

    $reslezpres = mysqli_query($con, inspref($query)) or die(mysqli_error);

    if (mysqli_num_rows($reslezpres) > 0)
    {
        echo("Modif. lez.:");
        if ($orainizionew != "")
        {
            print "<select name='orainizioold' disabled ONCHANGE='voti.submit()'><option value=''>&nbsp;";
        }
        else
        {
            if ($provenienza == "")
            {
                print "<select name='orainizioold'  ONCHANGE='voti.submit()'><option value=''>&nbsp;";
            }
            else
            {
                print "<select name='orainizioold' disabled ONCHANGE='voti.submit()'><option value=''>&nbsp;";
            }
        }
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

            }
            for ($i = $vallezpres['orainizio']; $i <= ($vallezpres['orainizio'] - 1 + $vallezpres['numeroore']); $i++)
                $oredisp[$i] = 1;
        }
        print "</select>";

    }
    else
    {
        $orainizioold = "";
    }

    //  if (!$_SESSION['sostegno']){
    if (!cattedra_sost($iddocente, $materia, $idclasse, $con) & $idgruppo == '')
    {
        echo(" Nuova lez.:");

        if ($orainizioold != "")
        {
            print "<select name='orainizionew' disabled ONCHANGE='voti.submit()'><option value=''>&nbsp;";
        }
        else
        {
            if ($provenienza == "")
            {
                print "<select name='orainizionew'  ONCHANGE='voti.submit()'><option value=''>&nbsp;";
            }
            else
            {
                print "<select name='orainizionew' disabled ONCHANGE='voti.submit()'><option value=''>&nbsp;";
            }
        }

        for ($i = 1; $i <= $numeromassimoore; $i++)
        {
            for ($j = $i; $j <= $numeromassimoore; $j++)
            {
                if (!occupata($oredisp, $i, $j, $numeromaxorelez))
                {
                    $strore = "$i-$j";
                    if ($strore != $orainizionew)
                    {
                        print "<option>$strore";
                    }
                    else
                    {
                        print "<option selected>$strore";
                    }
                }
            }
        }

        print "</select>";
    }
    else
    {
        if ($idgruppo != '')
        {
            echo(" Nuova lez.: <b>[Usare inserimento <a href='../lezionigruppo/lezgru.php'>lezioni di gruppo</a>]</b>");
        }
        else
        {
            echo(" Nuova lez.: <b>[Usare inserimento <a href='lezcert.php'>lezioni di sostegno</a>]</b>");
        }
    }
}

echo "</td></tr>";

echo('</table>
 
       <table align="center">
       <td>');

echo('</td>
   
       </table></form><hr>');


if ($mese == "")
{
    $m = 0;
}
else
{
    $m = $mese;
}
if ($giorno == "")
{
    $g = 0;
}
else
{
    $g = $giorno;
}

if ($anno == "")
{
    $a = 0;
}
else
{
    $a = $anno;
}

$giornosettimana = giorno_settimana($anno . "-" . $mese . "-" . $giorno);


if (!checkdate($m, $g, $a))
{
    print ("<center> <big><big>Data non corretta!</big></big> </center>");
}
else
{
    if ($giornosettimana == "Dom" | giorno_festa($anno . "-" . $mese . "-" . $giorno,$con))
    {
        print ("<center> <big><big>Il giorno selezionato &egrave; festivo!</big></big> </center>");
    }
    else
    {
        if (($cattedra != "") & ($orainizioold != "" | $orainizionew != ""))
        {

            $classe = "";

            $query = 'SELECT * FROM tbl_classi WHERE idclasse="' . $idclasse . '" ';
            $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            if ($val = mysqli_fetch_array($ris))
            {
                $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
            }


            //
            //    ESTRAZIONE DEI DATI DELLA LEZIONE
            //


            // TTTT Lezione 10
            if ($orainizionew != '')
            {
                $postratt = strpos($orainizionew, "-");
                $orainizio = substr($orainizionew, 0, $postratt);
            }
            else
            {
                $postratt = strpos($orainizioold, "-");
                $orainizio = substr($orainizioold, 0, $postratt);
            }

            if ($idlezione != "")
            {
                $query = "select * from tbl_lezioni where idlezione='$idlezione'";
            }
            else
            {
                $query = "select * from tbl_lezioni where idclasse=$idclasse and idmateria=$materia and orainizio='$orainizio' and datalezione='" . $anno . "-" . $mese . "-" . $giorno . "'";
            }


            $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            $l = mysqli_fetch_array($ris);

            if ($l != NULL)
            {
                $numeroore = $l['numeroore'];
                $argomenti = $l['argomenti'];
                $attivita = $l['attivita'];
                $ultimamodifica = $l['oraultmod'];
                $idlezione = $l['idlezione'];
                $iddocente = $l['iddocente'];
                $idlezionegruppo = $l['idlezionegruppo'];
                
            }
            else
            {
                $numeroore = 0;
                $argomenti = "";
                $attivita = "";
                $ultimamodifica = "";
                $idlezione = '';
            }


            $querypiani = "SELECT iddocumento FROM tbl_documenti WHERE idclasse='" . $idclasse . "' AND idmateria='" . $materia . "' AND idtipodocumento=1000000001";
            $rispiani = mysqli_query($con, inspref($querypiani)) or die ("Errore nella query: " . mysqli_error($con));
            if ($valpiani = mysqli_fetch_array($rispiani))
            {
                print"<p align='right'>
                        <font size=2><a href='../documenti/actionsdocum.php?action=view&Id=" . $valpiani["iddocumento"] . "' ";
                echo "target='_blank'>Visualizza piano di lavoro</a><br/></font></p>  ";
            }

                print"<p align='right'>
                        <font size=2><a href='rieptotalezioniperiodo.php?idcattedra=$cattedra&idclasse=$idclasse&idmateria=$materia&data=$anno-$mese-$giorno' ";
                echo "target='_blank'>Riepilogo lezioni</a><br/></font></p>  ";


            echo "<p align='center'>
                         <font size=6>Lezione svolta<br/></font>
    
                         <form method='post' action='inslez.php'>";
            echo "<table border=2 align='center'>";

            $sost = 0;
            $abilmod = "";

            if (cattedra_sostegno($cattedra, $con))
            {
                $sost = 1;
                $abilmod = " disabled";

            }

            echo "<tr class='prima'><td>Argomenti</td><td>Attivit&agrave; e compiti assegnati</td></tr>";
            echo "<tr>";

            $durata = 0;
            // TTTT Lezione 10
            if ($orainizionew != '')
            {
                $postratt = strpos($orainizionew, "-");
                $ini = substr($orainizionew, 0, $postratt);
                $fin = substr($orainizionew, $postratt + 1);
                $durata = 1 + ($fin - $ini);
            }
            else
            {
                $postratt = strpos($orainizioold, "-");
                $ini = substr($orainizioold, 0, $postratt);
                $fin = substr($orainizioold, $postratt + 1);
                $durata = 1 + ($fin - $ini);
            }

            print"<td><input type='hidden' name='orelezione' value='$durata'>";
            print"<input type='hidden' name='ultimamodifica' value='$ultimamodifica'>";
            if ($sost | $idlezionegruppo!=NULL)
            {
                print "<input type='hidden' name='argomenti' value='$argomenti'>";
                print "<textarea cols=50 rows=10 disabled>";
            }
            else
            {
                print "<textarea cols=50 rows=10 name='argomenti'>";
            }


            print $argomenti;
            print "</textarea></td>";
            if ($sost | $idlezionegruppo!=NULL)
            {
                print "<td><input type='hidden' name='attivita' value='$attivita'>";
                print "<textarea cols=50 rows=10 disabled>";
            }
            else
            {
                print "<td><textarea cols=50 rows=10 name='attivita'>";
            }
            print $attivita;
            print "</textarea></td>";
            print "</tr></table>";

            if ($valutazionedecimale == 'yes')
            {
                print "<center><table class='smallchar' border=2>
            <tr class='prima'>
            <td><b> N. </b></td>
            <td><b> Alunno </b></td>
          
            <td><b> Data di nascita </b></td>
            <td><b> Ore assenza </b></td>
            <td><b> Voto scritto e giudizio </b></td>
            <td><b> Voto orale e giudizio </b></td>
            <td><b> Voto pratico e giudizio </b></td>
  
            </tr>
           ";
            }
            else
            {
                print "<center><table class='smallchar' border=2>
            <tr class='prima'>
            <td><b> N. </b></td>
            <td><b> Alunno </b></td>
          
            <td><b> Data di nascita </b></td>
            <td><b> Ore assenza </b></td>
            <td><b> Valutazione prova scritta </b></td>
            <td><b> Valutazione prova orale </b></td>
            <td><b> Valutazione prova pratica </b></td>
  
            </tr>
           ";
            }

            // ARRAY PER I VOTI
            $idaluvoti = array();
            $idvoti = array();
            $valutazioni = array();
            $giudizi = array();
            $tipi = array();
            $docenti = array();
            // ARRAy PER LE ASSENZE
            $idaluasse = array();
            $assenze = array();
            $assforzate = array();


            $query = "select * from tbl_valutazioniintermedie
                       where idlezione='$idlezione'";
            $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            while ($rec = mysqli_fetch_array($ris))
            {
                $idaluvoti[] = $rec['idalunno'];
                $valutazioni[] = $rec['voto'];
                $idvoti[] = $rec['idvalint'];
                $tipi[] = $rec['tipo'];
                $giudizi[] = $rec['giudizio'];
                $docenti[] = $rec['iddocente'];
            }
            if ($idlezione != 0)
            {
                $query = "select * from tbl_asslezione
                           where idlezione='$idlezione'";
                $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
                while ($rec = mysqli_fetch_array($ris))
                {
                    $idaluasse[] = $rec['idalunno'];
                    $assenze[] = $rec['oreassenza'];
                    //$assforzate[] = $rec['forzato'];

                }
            }

            // print "tttt ".$idlezionegruppo;

            if (!$sost)
            {
                if ($idgruppo == "")
                {
                    $query = "SELECT idalunno,cognome,nome,datanascita FROM tbl_alunni
                              WHERE idalunno IN (" . estrai_alunni_classe_data($idclasse, $anno . "-" . $mese . "-" . $giorno, $con) . ")
                               ORDER BY cognome, nome, datanascita";
                }
                else
                {
                    $query = "select tbl_alunni.idalunno,cognome,nome,datanascita
                              from tbl_gruppi,tbl_gruppialunni,tbl_alunni
                               where
                                     tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
                                     and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
                                     and tbl_alunni.idclasse=$idclasse
                                     and tbl_gruppi.idgruppo=(select idgruppo from tbl_lezionigruppi where idlezionegruppo=$idlezionegruppo)
                                     order by cognome, nome, datanascita";
                }
                //=$idgruppo";
            }
            else
            {
                $query = "select idalunno,cognome,nome,datanascita from tbl_alunni
                          where idalunno in (" . estrai_alunni_classe_data($idclasse, $anno . "-" . $mese . "-" . $giorno, $con) . ")
                              and idalunno in (select idalunno from tbl_cattnosupp where iddocente='$id_ut_doc' and idmateria='$materia' and idclasse='$idclasse')
                              order by cognome, nome, datanascita";
            }

            $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));


            $numreg = 0;
            while ($val = mysqli_fetch_array($ris))
            {
                $numreg++;
                $esiste_voto = false;
                if (!alunno_certificato($val['idalunno'], $con))
                {
                    $cert = "";
                }
                else
                {
                    $cert = "<img src='../immagini/apply_small.png'>";
                }

                echo "
                  <tr>
                  <td><b>$numreg</b></td>
                  <td><b> <a href=javascript:Popup('sitlezalu.php?alunno=" . $val['idalunno'] . "&materia=$materia&periodo=Tutti&classe=$idclasse')>" . $val["cognome"] . " " . $val["nome"] . "</a>  $cert</b></td>
                  <td align='center'><b> " . data_italiana($val["datanascita"]) . " </b></td> ";

                // Codice per ricerca assenze già inserite nella giornata

                $assenzapresente = ricerca_assenza($val['idalunno'], $idaluasse, $assenze);

                if ($assenzapresente != 0)
                {
                    $oreassenza = $assenzapresente;
                }
                else
                {
                    $oreassenza = 0;
                }
                print "<td>";

                // GESTIONE AUTOMATICA DELLE ASSENZE ALLE LEZIONI

                print "<center><input class='smallchar' name='oreass" . $val["idalunno"] . "' size=1 value = '$oreassenza' disabled></center>";

                //
                // RIVEDERE LE RIGHE SUCCESSIVE PER GESTIONE MANUALE DELLE ASSENZE ALLA LEZIONE (27/7/205)
                //
                //tttt 24 luglio 2016   else
                //   {
                //       $oreassenza = oreassenza($ini, $durata, $val['idalunno'], $anno . "-" . $mese . "-" . $giorno, $con);
                //   }
                /*
                echo "<select class='smallchar' name='oreass" . $val["idalunno"] . "'>";
                for ($i = 0; $i <= $durata; $i++)  // TTTTT
                {
                    if ($i != $oreassenza)
                    {
                        print "<option>" . $i;
                    }
                    else
                    {
                        print "<option selected>" . $i;
                    }
                }
                print "</select>";
                if (ricerca_assenza_forzata($val['idalunno'],$idaluasse,$assenze,$assforzate))
                    print ("<input type=checkbox name='forz".$val["idalunno"]."' checked>");
                else
                    print ("<input type=checkbox name='forz".$val["idalunno"]."'>");
                 */

                print"</td>";


                $voto_medio = false;
                $altro_docente = false;

                //
                // Codice per ricerca voti scritti già inseriti nella giornata
                //

                $arrvoto = ricerca_voto($val['idalunno'], 'S', $idaluvoti, $tipi, $valutazioni, $giudizi, $idvoti, $docenti);
                if ($arrvoto[0] != 0)
                {
                    $esiste_voto = true;
                    $voto = $arrvoto[1];
                    $giudizio = $arrvoto[2];
                    $docente = $arrvoto[3];
                    $voto_medio = voto_combinato($arrvoto[0], $con);
                    $altro_docente = ($docente != $id_ut_doc);
                }
                else
                {
                    $esiste_voto = false;
                }

                if ($esiste_voto)
                {
                    echo '<td>';
                    if ($valutazionedecimale == 'yes')
                    {
                        if (!$voto_medio & !$altro_docente)
                        {
                            echo '<select class="smallchar" name="votos' . $val["idalunno"] . '"><option value=99>&nbsp;';
                        }
                        else
                        {
                            echo '<select class="smallchar" name="votos' . $val["idalunno"] . '" disabled><option value=99>&nbsp;';
                        }

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
                        echo '</select>&nbsp';
                    }
                    else
                    {
                        echo '<input type="hidden" name="votos' . $val["idalunno"] . '" value="' . $voto . '">';
                    }
                    echo '<input class="smallchar" type="text" size=15 maxlength=150 name="giudizios' . $val["idalunno"] . '" value="' . $giudizio . '">
                          </td>';
                }
                else
                {
                    echo '<td>';
                    if ($valutazionedecimale == 'yes')
                    {
                        echo '<select class="smallchar" name="votos' . $val["idalunno"] . '"><option value=99>&nbsp;';
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


                        echo '</select>&nbsp;';
                    }
                    else
                    {
                        echo '<input type="hidden" name="votos' . $val["idalunno"] . '" value=99>';
                    }
                    echo '<input class="smallchar" type="text" size=15 maxlength=150 name="giudizios' . $val["idalunno"] . '">
                            </td>';
                }

                //
                // Codice per ricerca voti orali già inseriti nella giornata
                //

                $arrvoto = ricerca_voto($val['idalunno'], 'O', $idaluvoti, $tipi, $valutazioni, $giudizi, $idvoti, $docenti);
                if ($arrvoto[0] != 0)
                {
                    $esiste_voto = true;
                    $voto = $arrvoto[1];
                    $giudizio = $arrvoto[2];
                    $docente = $arrvoto[3];
                    $voto_medio = voto_combinato($arrvoto[0], $con);
                    $altro_docente = ($docente != $id_ut_doc);
                }
                else
                {
                    $esiste_voto = false;
                }

                if ($esiste_voto)
                {
                    echo '<td>';
                    if ($valutazionedecimale == 'yes')
                    {
                        if (!$voto_medio & !$altro_docente)
                        {
                            echo '<select class="smallchar" name="votoo' . $val["idalunno"] . '"><option value=99>&nbsp;';
                        }
                        else
                        {
                            echo '<select class="smallchar" name="votoo' . $val["idalunno"] . '" disabled><option value=99>&nbsp;';
                        }

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

                        echo '</select>&nbsp';
                    }
                    else
                    {
                        echo '<input type="hidden" name="votoo' . $val["idalunno"] . '" value="' . $voto . '">';
                    }
                    echo '<input class="smallchar" type="text" size=15 maxlength=150 name="giudizioo' . $val["idalunno"] . '" value="' . $giudizio . '">
                           </td>';
                }
                else
                {
                    echo '<td>';
                    if ($valutazionedecimale == 'yes')
                    {
                        echo '<select class="smallchar" name="votoo' . $val["idalunno"] . '"><option value=99>&nbsp;';
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

                        echo '</select>&nbsp;';
                    }
                    else
                    {
                        echo '<input type="hidden" name="votoo' . $val["idalunno"] . '" value="' . $voto . '">';
                    }
                    echo '<input class="smallchar" type="text" size=15 maxlength=150 name="giudizioo' . $val["idalunno"] . '">
                          </td>';
                }

                //
                // Codice per ricerca voti pratici già inseriti nella giornata
                //

                $arrvoto = ricerca_voto($val['idalunno'], 'P', $idaluvoti, $tipi, $valutazioni, $giudizi, $idvoti, $docenti);
                if ($arrvoto[0] != 0)
                {
                    $esiste_voto = true;
                    $voto = $arrvoto[1];
                    $giudizio = $arrvoto[2];
                    $docente = $arrvoto[3];
                    $voto_medio = voto_combinato($arrvoto[0], $con);
                    $altro_docente = ($docente != $id_ut_doc);
                }
                else
                {
                    $esiste_voto = false;
                }

                if ($esiste_voto)
                {
                    echo '<td>';
                    if ($valutazionedecimale == 'yes')
                    {
                        if (!$voto_medio & !$altro_docente)
                        {
                            echo '<select class="smallchar" name="votop' . $val["idalunno"] . '"><option value=99>&nbsp;';
                        }
                        else
                        {
                            echo '<select class="smallchar" name="votop' . $val["idalunno"] . '" disabled><option value=99>&nbsp;';
                        }
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

                        echo '</select>&nbsp';
                    }
                    else
                    {
                        echo '<input type="hidden" name="votop' . $val["idalunno"] . '" value="' . $voto . '">';
                    }
                    echo '<input class="smallchar" type="text" size=15 maxlength=150 name="giudiziop' . $val["idalunno"] . '" value="' . $giudizio . '">
                          </td>';
                }
                else
                {
                    echo '<td>';
                    if ($valutazionedecimale == 'yes')
                    {
                        echo '<select class="smallchar" name="votop' . $val["idalunno"] . '"><option value=99>&nbsp;';
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

                        echo '</select>&nbsp;';
                    }
                    else
                    {
                        echo '<input type="hidden" name="votop' . $val["idalunno"] . '" value="' . $voto . '">';
                    }
                    echo '<input class="smallchar" type="text" size=15 maxlength=150 name="giudiziop' . $val["idalunno"] . '">
                           </td>';
                }


            }
            echo '</table></center>';


            echo '
                   <table align="center">
                   <tr><td>


                   <p align="center"><input type=hidden value=' . $idclasse . ' name=cl>
                   <p align="center"><input type=hidden value=' . $idgruppo . ' name=idgruppo>
                   <p align="center"><input type=hidden value=' . $giorno . ' name=gio>
                   <p align="center"><input type=hidden value=' . $mese . ' name=mese>
                   <p align="center"><input type=hidden value=' . $anno . ' name=anno>
                   <p align="center"><input type=hidden value=' . $materia . ' name=materia>
                   <p align="center"><input type=hidden value=' . $orainizio . ' name=orainizio>
                   <p align="center"><input type=hidden value=' . $durata . ' name=orelezione>
                   <p align="center"><input type=hidden value=' . $id_ut_doc . ' name=iddocente>
                   <p align="center"><input type=hidden value=' . $provenienza . ' name=provenienza>
                   <p align="center"><input type=hidden value=' . $idlezione . ' name=codlezione>';


            if (controlla_scadenza($maxgiorniritardolez,$giorno,$mese,$anno))  // Verifica se non è passato il tempo e che non c'è una deroga
            {
                if ($iddocente == $id_ut_doc)
                {
                    echo '<p align="center"><input type=submit name=b value="Inserisci lezione">';
                }
                else
                {
                    if ((cattedra($id_ut_doc, $materia, $idclasse, $con)) & !$sost)
                    {
                        echo '<p align="center"><input type=submit name=b value="Modifica e/o firma lezione">';
                    }
                }
            }
            else
            {
                print '<p align="center"><font color="red"><b>Tempo scaduto per modifica lezione! Rivolgersi a dirigente scolastico!</b></font></p>';
            }

            echo '</td></tr>';
            // Visualizzo firme attuali
            $queryfirme = "select cognome, nome from tbl_firme,tbl_docenti
                           where tbl_firme.iddocente=tbl_docenti.iddocente
                           and idlezione='$idlezione'";
            $resfirme = mysqli_query($con, inspref($queryfirme)) or die(mysqli_error($con));
            if (mysqli_num_rows($resfirme) > 0)

            {
                print "<tr><td align=center><b>Firme:</b>";
                while ($valfirme = mysqli_fetch_array($resfirme))
                {
                    $cogn = $valfirme['cognome'];
                    $nome = $valfirme['nome'];
                    print "<br>$cogn $nome";
                }
                print "</td></tr>";
            }


            echo '</table></form>';


        }
        else
        {

            print "";

        }
    }
}

mysqli_close($con);
stampa_piede("");

function ricerca_voto($idalunno, $tipo, $arridal, $arrtipov, $arrval, $arrgiud, $arridvoti, $arriddoc)
{

    $arr_id_val = array();
    for ($i = 0; $i < count($arridal); $i++)
    {

        if ($idalunno == $arridal[$i] & $tipo == $arrtipov[$i])
        {
            $arr_id_val[] = $arridvoti[$i];
            $arr_id_val[] = $arrval[$i];
            $arr_id_val[] = $arrgiud[$i];
            $arr_id_val[] = $arriddoc[$i];
            return $arr_id_val;
        }
    }
    $arr_id_val[] = 0;
    $arr_id_val[] = 0;
    $arr_id_val[] = "";
    $arr_id_val[] = 0;
    return $arr_id_val;

}

function ricerca_assenza($idalunno, $arridal, $arrass)
{

    for ($i = 0; $i < count($arridal); $i++)
    {

        if ($idalunno == $arridal[$i])
        {
            return $arrass[$i];
        }
    }
    return 0;
}


function ricerca_assenza_forzata($idalunno, $arridal, $arrass, $arrfor)
{

    for ($i = 0; $i < count($arridal); $i++)
    {

        if ($idalunno == $arridal[$i] & $arrfor[$i])
        {
            return true;
        }
    }
    return false;
}

function voto_combinato($idvoto, $con)
{
    $query = "select * from tbl_valutazioniabilcono where idvalint=$idvoto";
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    if (mysqli_num_rows($ris) > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}


function occupata($oredisp, $i, $j, $maxore)
{
    $occ = false;
    for ($k = $i; $k <= $j; $k++)
        if ($oredisp[$k] == 1)
        {
            $occ = true;
        }
    if (($j - $i + 1) > $maxore)
    {
        $occ = true;
    }
    return $occ;
}

/*
function oreassenza($durata, $idalunno, $data, $con)
{
    $queryass = "select * from tbl_assenze where idalunno=$idalunno and data='$data'";
    $risass = mysqli_query($con, inspref($queryass)) or die ("Errore nella query: " . mysqli_error($con));
    if (mysqli_num_rows($risass) > 0)
    {
        return $durata;
    }
    else
    {
        return 0;
    }
}
*/


function cattedra($id_ut_doc, $idmateria, $idclasse, $con)
{
    $querycatt = "select * from tbl_cattnosupp where idclasse='$idclasse' and idmateria='$idmateria' and iddocente='$id_ut_doc' and iddocente<>1000000000";
    $riscatt = mysqli_query($con, inspref($querycatt)) or die ("Errore nella query: " . mysqli_error($con));
    if (mysqli_num_rows($riscatt) > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}





