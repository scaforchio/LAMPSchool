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
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Situazione assenze per classe";
$script = "<script>
            function printPage()
            {
               if (window.print)
                  window.print();
               else
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');
            }
         </script>
         <script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         
         ";
$script .= "
jQuery(function($){
	$.datepicker.regional['it'] = {
		clearText: 'Svuota', clearStatus: 'Annulla',
		closeText: 'Chiudi', closeStatus: 'Chiudere senza modificare',
		prevText: '&#x3c;Prec', prevStatus: 'Mese precedente',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: 'Mostra l\'anno precedente',
		nextText: 'Succ&#x3e;', nextStatus: 'Mese successivo',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: 'Mostra l\'anno successivo',
		currentText: 'Oggi', currentStatus: 'Mese corrente',
		monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
		'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'],
		monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu',
		'Lug','Ago','Set','Ott','Nov','Dic'],
		monthStatus: 'Seleziona un altro mese', yearStatus: 'Seleziona un altro anno',
		weekHeader: 'Sm', weekStatus: 'Settimana dell\'anno',
		dayNames: ['Domenica','Luned&#236','Marted&#236','Mercoled&#236','Gioved&#236','Venerd&#236','Sabato'],
		dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'],
		dayNamesMin: ['Do','Lu','Ma','Me','Gio','Ve','Sa'],
		dayStatus: 'Usa DD come primo giorno della settimana', dateStatus: '\'Seleziona\' D, M d',
		dateFormat: 'dd/mm/yy', firstDay: 1,
		initStatus: 'Scegliere una data', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['it']);
});
$(document).ready(function(){
	                 $('#datainizio').datepicker({ dateFormat: 'dd/mm/yy' });
	                 
	             });
$(document).ready(function(){
	                 $('#datafine').datepicker({ dateFormat: 'dd/mm/yy' });
	                 
	             });
	             </script>";
stampa_head($titolo, "", $script, "MSPD");


stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$nome = stringa_html('cl');
$but = stringa_html('visass');


$meseanno = stringa_html('mese');  // In effetti contiene sia il mese che l'anno
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


// Divido il mese dall'anno
$mese = substr($meseanno, 0, 2);
$anno = substr($meseanno, 5, 4);

//$giornosettimana=giorno_settimana($anno."-".$mese."-".$giorno);

$datainizio = stringa_html("datainizio");
$datafine = stringa_html("datafine");

$dataoggi = date("d/m/Y");
if ($datainizio == "")
{
    $datainizio = data_italiana($datainiziolezioni);
}
if ($datafine == "")
{
    $datafine = $dataoggi;
}

if ($mese == '')
{
    $mese = date('m');
}
if ($anno == '')
{
    $anno = date('Y');
}


print ("
   <form method='post' action='sitasstota.php' name='tbl_assenze'>
   
   <p align='center'>
   <table align='center'>
   <tr>
      <td width='50%'><center><b>Classe</b></center></td>
      <td width='50%'>
      <SELECT ID='cl' NAME='cl' ONCHANGE='tbl_assenze.submit()'> <option>&nbsp ");


$query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY specializzazione, sezione, anno";
if ($tipoutente == 'D')
{
    $query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi
           WHERE idcoordinatore=" . $_SESSION['idutente'] . " ORDER BY anno,sezione,specializzazione";
}
$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
    if ($nome == $nom["idclasse"])
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

print("</SELECT></td></tr>");
print("
	<tr>
	<td width='50%'><center><b>Data inizio </b></center></td>
	<td width='50%'>
	<input type='text' name='datainizio' id='datainizio' class='datepicker' size='8' maxlength='10' value='$datainizio'  ONCHANGE='tbl_assenze.submit()'>
	</td></tr>
	<tr>
	<td width='50%'><center><b>Data fine </b></center></td>
	<td width='50%'>
	<input type='text' name='datafine' id='datafine'  class='datepicker' size='8' maxlength='10' value='$datafine' ONCHANGE='tbl_assenze.submit()'>
	</td></tr>");


print ("</table>");

if ($nome != "")
{
    echo('
 
    <table align="center">
      <td>');

    echo('     </form></td>
   
</table><hr>
 
    ');

    if ($mese == "")
    {
        $m = 0;
    }
    else
    {
        $m = $mese;
    }

    if ($anno == "")
    {
        $a = 0;
    }
    else
    {
        $a = $anno;
    }


    // print($nome." -   ". $g.$m.$a.$giornosettimana);

    $idclasse = $nome;
    $classe = "";
    $oresettimanali = 0;
    $numoretot = 0;
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

    $seledata = "";
    $seledatalezione="";
    if ($datainizio != "")
    {
        $seledata = $seledata . " and data >= '" . data_to_db($datainizio) . "' ";
        $seledatalezione = $seledatalezione . " and datalezione >= '" . data_to_db($datainizio) . "' ";
    }

    if ($datafine != "")
    {
        $seledata = $seledata . " and data <= '" . data_to_db($datafine) . "' ";
        $seledatalezione = $seledatalezione . " and datalezione <= '" . data_to_db($datafine) . "' ";
    }
    //
    // CONTEGGIO ORE DI LEZIONE EFFETTIVAMENTE SVOLTE NELLA CLASSE
    //

    $arrlezioni = array();
    $querylez = "select * from tbl_lezioni
               where idclasse=$idclasse
               $seledatalezione";

    $rislez = mysqli_query($con, inspref($querylez)) or die("Errore: " . inspref($querylez, false));
    while ($reclez = mysqli_fetch_array($rislez))
    {
        $datalez = $reclez['datalezione'];
        $orainizio = $reclez['orainizio'];
        $numeroore = $reclez['numeroore'];
        for ($i = $orainizio; $i < ($orainizio + $numeroore); $i++)
        {
            $indicearray = $datalez . $i;
            $arrlezioni[$indicearray] = 1;

        }
    }

    $oresvolte = count($arrlezioni);


    $query = 'SELECT * FROM tbl_classi WHERE idclasse="' . $idclasse . '" ';
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    if ($val = mysqli_fetch_array($ris))
    {
        $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
        $oresettimanali = $val["oresett"];
        $numoretot = round(33.333 * $oresettimanali);  // 33 = numero settimane di lezione convenzionale
    }
    $query = 'SELECT * FROM tbl_alunni WHERE idclasse="' . $idclasse . '" ORDER BY cognome,nome,datanascita';
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

    $c = mysqli_fetch_array($ris);
    if ($c == NULL)
    {
        echo '
                    <p align="center">
		    <font size=4 color="black">Nessun alunno presente nella classe </font>
                   ';
        exit;
    }
    echo "<p align='center'>
          <font size=4 color='black'>Assenze della classe $classe <br>
                                     nel periodo $datainizio - $datafine
          <br>Ore svolte nel periodo: $oresvolte</font>
          <table border=2 align='center'>";

    echo '
          <tr class="prima">
          
          <td><font size=1><b> Cognome </b></td>
          <td><font size=1><b> Nome  </b></td>
          <td><font size=1><b> Data di nascita </b></td>';
    print ("<td><font size=1><center>Ass</td><td><font size=1><center>Rit (Rit. Brevi)</td><td><font size=1><center>Usc</td><td align=center><font size=1>Perc. ass.<br/>su monte ore<br/>($numoretot)</td><td align=center><font size=1>Perc. ass.<br/>su monte ore<br/>(con deroghe)</td></tr>");


    $query = 'SELECT * FROM tbl_alunni WHERE idclasse="' . $idclasse . '" ORDER BY cognome,nome,datanascita';
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    while ($val = mysqli_fetch_array($ris))
    {
        $idalunno = $val["idalunno"];
        echo '
             <tr>
                <td><font size=1><b> ' . $val["cognome"] . ' </b></td>
                <td><font size=1><b> ' . $val["nome"] . '    </b></td>
                <td><font size=1><b> ' . data_italiana($val["datanascita"]) . ' </b></td>
                ';

        $queryass = "SELECT count(*) AS numass FROM tbl_assenze WHERE idalunno = '" . $val['idalunno'] . "' " . $seledata;
        $queryrit = "SELECT count(*) AS numrit FROM tbl_ritardi WHERE idalunno = '" . $val['idalunno'] . "' " . $seledata;
        $queryusc = "SELECT count(*) AS numusc FROM tbl_usciteanticipate WHERE idalunno = '" . $val["idalunno"] . "' " . $seledata;

        $risass = mysqli_query($con, inspref($queryass)) or die ("Errore nella query: " . mysqli_error($con));
        $risrit = mysqli_query($con, inspref($queryrit)) or die ("Errore nella query: " . mysqli_error($con));
        $numritardibrevi = calcola_ritardi_brevi($val['idalunno'], $con, $ritardobreve,$seledata);
        $risusc = mysqli_query($con, inspref($queryusc)) or die ("Errore nella query: " . mysqli_error($con));
        while ($ass = mysqli_fetch_array($risass))
        {

            $numass = $ass['numass'];
        }
        while ($rit = mysqli_fetch_array($risrit))
        {
            $numrit = $rit['numrit'];
        }

        while ($usc = mysqli_fetch_array($risusc))
        {

            $numusc = $usc['numusc'];
        }

        $numoretot = round(33.333 * $oresettimanali);
        $numoregio = $oresettimanali / $giornilezsett; //calcolo ore medie giornaliere
        $oreassenza = calcola_ore_assenza($idalunno,$datainizio,$datafine,$con);

        $oreassenzader = calcola_ore_deroga($idalunno,$datainizio,$datafine,$con);


        $oreassenzaperm=calcola_ore_deroga_oraria($idalunno,$datainizio,$datafine,$con);
        $oreassenzader -= $oreassenzaperm;


        $percass = round($oreassenza / $numoretot * 100, 2);
        $percassder = round($oreassenzader / $numoretot * 100, 2);

        print "<td><center>$numass</td><td><center>$numrit ($numritardibrevi) </td><td><center>$numusc</td><td align=center>$percass (Ore: $oreassenza) </td><td align=center>$percassder (Ore: $oreassenzader) </td></tr>";


    }

    echo '</table>';

    print "<center><img src='../immagini/stampa.png' onClick='printPage();'</center>";
    // print"<br/><center><a href=javascript:Popup('staasse.php?classe=$idclasse&datainizio=$datainizio&datafine=$datafine')><img src='../immagini/stampa.png'></a></center><br/><br/>";
}
// fine if

mysqli_close($con);
stampa_piede("");

