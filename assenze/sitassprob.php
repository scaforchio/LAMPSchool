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


$titolo = "Situazione problematiche assenze";
$script = "<script type='text/javascript'>
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
stampa_head($titolo, "", $script,"MSPD");


stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


// $nome = stringa_html('cl');
$but = stringa_html('visass');

$perclim = stringa_html('perclim');


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

if ($perclim == '')
{
    $pl = 20;
}
else
{
    $pl = $perclim;
}

print ("
   <form method='post' action='sitassprob.php' name='tbl_assenze'>
   
   <p align='center'>
   <table align='center'>
   <tr>
      <td width='50%'><p align='center'><b>Percentuale limite</b></p></td>
      <td width='50%'>
      <input type='number' name='perclim' min='0' max='100' value='$pl'></td></tr>");
print("
	<tr>
	<td width='50%'><p align='center'><b>Data inizio </b></p></td>
	<td width='50%'>
	<input type='text' name='datainizio' id='datainizio' class='datepicker' size='8' maxlength='10' value='$datainizio'  ONCHANGE='tbl_assenze.submit()'>
	</td></tr>
	<tr>
	<td width='50%'><p align='center'><b>Data fine </b></p></td>
	<td width='50%'>
	<input type='text' name='datafine' id='datafine'  class='datepicker' size='8' maxlength='10' value='$datafine' ONCHANGE='tbl_assenze.submit()'>
	</td></tr>
	");


print ("</table>");
print ("<center><input type='submit' value='Visualizza'></center>");
print ("</form>");


//
//   Inizio visualizzazione della data
//


/*
  echo('   <select name="anno">');
    for($a=$annoscol;$a<=($annoscol+1);$a++)
    {
      if ($a==$anno)
         echo("<option selected>$a");
      else
         echo("<option>$a");
    } 
    echo("</select>");  
*/


//
//  Fine visualizzazione della data
//

//if ($nome!="")
if ($perclim != '')
{
    echo('

    <table align="center">
      <td>');

    echo('    </td>

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

//    $idclasse=$nome;
    $classe = "";
    $oresettimanali = 0;
    $numoretot = 0;
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

    /*
     $query='select * from tbl_classi where idclasse="'.$idclasse.'" ';
     $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
     if($val=mysqli_fetch_array($ris))
     {
        $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];
        $oresettimanali=$val["oresett"];
        $numoretot=33*$oresettimanali;  // 33 = numero settimane di lezione convenzionale
      }
    */

    echo "<p align='center'>
          <font size=4 color='black'>Alunni con situazioni di assenza problematiche <br>
                                     nel periodo $datainizio - $datafine</font>
          
          <table border=2 align='center'>";

    echo '
          <tr class="prima">
          
          <td><font size=1><b> Cognome </b></td>
          <td><font size=1><b> Nome  </b></td>
          <td><font size=1><b> Data di nascita </b></td><td><b>Classe</b></td>';
    print ("<td><font size=1><center>Ass</td><td><font size=1><center>Rit (Ent. Post.)</td><td><font size=1><center>Usc</td><td align=center><font size=1>Perc. ass.<br/>su monte ore<br/>della classe</td></tr>");


    $query = "SELECT * FROM tbl_alunni,tbl_classi WHERE
            tbl_alunni.idclasse=tbl_classi.idclasse AND tbl_alunni.idclasse<>'' ORDER BY specializzazione,anno,sezione,cognome,nome,datanascita";
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    while ($val = mysqli_fetch_array($ris))
    {
        $query = 'SELECT * FROM tbl_classi WHERE idclasse="' . $val['idclasse'] . '" ';
        $riscla = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        if ($valcla = mysqli_fetch_array($riscla))
        {
            $classe = $valcla["anno"] . " " . $valcla["sezione"] . " " . $valcla["specializzazione"];
            $oresettimanali = $valcla["oresett"];
            $numoretot = round(33.333 * $oresettimanali);  // 33 = numero settimane di lezione convenzionale
        }
        $idalunno = $val["idalunno"];


        $seledata = "";
        if ($datainizio != "")
        {
            $seledata = $seledata . " and data >= '" . data_to_db($datainizio) . "' ";
        }

        if ($datafine != "")
        {
            $seledata = $seledata . " and data <= '" . data_to_db($datafine) . "' ";
        }


        $queryass = "SELECT count(*) AS numass FROM tbl_assenze WHERE idalunno = '" . $val['idalunno'] . "' " . $seledata;
        $queryrit = "SELECT count(*) AS numrit FROM tbl_ritardi WHERE idalunno = '" . $val['idalunno'] . "' " . $seledata;
        $queryentpost = "SELECT count(*) AS numentpost FROM tbl_ritardi WHERE numeroore<>0 AND idalunno = '" . $val['idalunno'] . "' " . $seledata;
        $queryusc = "SELECT count(*) AS numusc FROM tbl_usciteanticipate WHERE idalunno = '" . $val["idalunno"] . "' " . $seledata;

         $risass = mysqli_query($con, inspref($queryass)) or die ("Errore nella query: " . mysqli_error($con));
         $risrit = mysqli_query($con, inspref($queryrit)) or die ("Errore nella query: " . mysqli_error($con));
         $risentpost = mysqli_query($con, inspref($queryentpost)) or die ("Errore nella query: " . mysqli_error($con));

         $risusc = mysqli_query($con, inspref($queryusc)) or die ("Errore nella query: " . mysqli_error($con));
         while ($ass = mysqli_fetch_array($risass))
         {

             $numass = $ass['numass'];
         }
         while ($rit = mysqli_fetch_array($risrit))
         {
             $numrit = $rit['numrit'];
         }
         while ($rit = mysqli_fetch_array($risentpost))
         {
             $numentpost = $rit['numentpost'];
         }
         while ($usc = mysqli_fetch_array($risusc))
         {

             $numusc = $usc['numusc'];
         }

        // TTTT  Da completare con il calcolo della percentuale Verificare perchè le date sotto danno sette giorni
        $numoretot = round(33.333 * $oresettimanali);
        $numoregio = $oresettimanali / $giornilezsett;


        /*   CALCOLO IN BASE A ORE LEZIONE  */


        $oreassenza = calcola_ore_assenza($idalunno,$datainizio,$datafine,$con);
   /*     $query = "select sum(oreassenza) as numerooreassenza from tbl_asslezione where idalunno='$idalunno' $seledata";
        $risass = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
        $recass = mysqli_fetch_array($risass);
        $oreassenza = $recass['numerooreassenza'];
*/
      //  $oreassenzader = $oreassenza;
        // deroghe per intera giornata
    /*    $query = "select sum(oreassenza) as numeroorederoga from tbl_asslezione where idalunno='$idalunno' $seledata
        and data in (select distinct data from tbl_deroghe where idalunno=$idalunno and numeroore=0)";
        $risass = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
        $recass = mysqli_fetch_array($risass);
        $orederogaass = $recass['numeroorederoga'];
        $oreassenzader -= $orederogaass;  */
        $oreassenzader = calcola_ore_deroga($idalunno,$datainizio,$datafine,$con);

        // deroghe per permessi orari
        $oreassenzaperm=calcola_ore_deroga_oraria($idalunno,$datainizio,$datafine,$con);
    /*    $query = "select data, numeroore from tbl_deroghe where idalunno=$idalunno and numeroore <> 0";
        $risder = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query, false));
        while ($recder = mysqli_fetch_array($risder))
        {
            $numorederoga = $recder['numeroore'];
            $data = $recder['data'];
            $query = "select sum(oreassenza) as numoreassenza from tbl_asslezione where idalunno=$idalunno and data='$data'";
            $risass = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query, false));
            $recass = mysqli_fetch_array($risass);
            $numoreassenza = $recass['numoreassenza'];
            if ($numoreassenza >= $numorederoga)
            {
                $oreassenzaperm += $numorederoga;
            }
            else
            {
                $oreassenzaperm += $numoreassenza;
            }
        }

*/
        $oreassenzader -= $oreassenzaperm;


        //giorni_lezione_tra_date("$annoscol-09-01",date("Y-m-d"));
        //$numsettimane=$numgiorni/$giornilezsett;
        //$numgiornitot=oretotale=

        $percass = round($oreassenza / $numoretot * 100, 2);
        $percassder = round($oreassenzader / $numoretot * 100, 2);

        //giorni_lezione_tra_date("$annoscol-09-01",date("Y-m-d"));
        //$numsettimane=$numgiorni/$giornilezsett;
        //$numgiornitot=oretotale=


        if ($percassder >= $perclim)
        {
            echo '
             <tr>
                <td><font size=1><b> ' . $val["cognome"] . ' </b></td>
                <td><font size=1><b> ' . $val["nome"] . '    </b></td>
                <td><font size=1><b> ' . data_italiana($val["datanascita"]) . ' </b></td>
                <td><font size=1><b> ' . $val["anno"] . ' ' . $val["sezione"] . ' ' . $val["specializzazione"] . '</b></td>
                ';
            print "<td><center>$numass</td><td><center>$numrit ($numentpost) </td><td><center>$numusc</td><td align=center>$percassder (Ore: $oreassenzader) </td></tr>";


        }


    }

    echo '</table>';

    // print"<br/><center><a href=javascript:Popup('staasse.php?classe=$idclasse&datainizio=$datainizio&datafine=$datafine')><img src='../immagini/stampa.png'></a></center><br/><br/>";
}
// fine if

mysqli_close($con);
stampa_piede(""); 

