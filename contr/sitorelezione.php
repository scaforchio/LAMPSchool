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


$titolo = "Situazione ore lezione";
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
stampa_head($titolo, "", $script, "MSPD");


stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


// $nome = stringa_html('cl');
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
   <form method='post' action='sitorelezione.php' name='orelezione'>
   
   <p align='center'>
   <table align='center'>
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
          <font size=4 color='black'>Ore di lezione svolte nel periodo $datainizio - $datafine</font>
          
          <table border=2 align='center'>";

print "<tr class='prima'>
          
          <td><font size=1><b> Cognome </b></td>
          <td><font size=1><b> Nome  </b></td>
          ";
print ("<td><font size=1><center>Ore totali</td></tr>");


$query = "SELECT * FROM tbl_docenti order by cognome,nome";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
while ($val = mysqli_fetch_array($ris))
{

    $iddocente = $val["iddocente"];
    $sostegno= $val["sostegno"];
    $seledata = "";
    if ($datainizio != "")
    {
        $seledata = $seledata . " and datalezione >= '" . data_to_db($datainizio) . "' ";
    }

    if ($datafine != "")
    {
        $seledata = $seledata . " and datalezione <= '" . data_to_db($datafine) . "' ";
    }

/*    $querygru="select sum(tbl_lezionigruppi.numeroore) as totoregruppi from tbl_firme,tbl_lezioni,tbl_lezionigruppi
            where tbl_firme.idlezione=tbl_lezioni.idlezione
            and tbl_lezioni.idlezionegruppo=tbl_lezionigruppi.idlezionegruppo
            and tbl_firme.iddocente=$iddocente
            $seledata"; */
/*
    $querygru="select sum(numeroore) as totorenorm from tbl_firme,tbl_lezioni
               where tbl_firme.idlezione=tbl_lezioni.idlezione
               and tbl_firme.iddocente=$iddocente
               and isnull(idlezionegruppo)
                   $seledata";

    $risgru=mysqli_query($con,inspref($querygru)) or die("Errore: ".inspref($querygru,false));
    $recgru=mysqli_fetch_array($risgru);
    $orenorm=$recgru['totorenorm'];


    if ($orenorm=="")
        $orenorm=0;


    $querygru="select sum(numeroore) as totoregruppi from tbl_lezionigruppi
            where idlezionegruppo in
                 (select idlezionegruppo from tbl_firme,tbl_lezioni
                   where tbl_firme.idlezione=tbl_lezioni.idlezione
                   and tbl_firme.iddocente=$iddocente
                   $seledata)";

    $risgru=mysqli_query($con,inspref($querygru)) or die("Errore: ".inspref($querygru,false));
    $recgru=mysqli_fetch_array($risgru);
    $oregruppo=$recgru['totoregruppi'];
    if ($oregruppo=="")
        $oregruppo=0;
*/
    $arrlezioni=array();
    $querylez="select * from tbl_firme,tbl_lezioni
               where tbl_firme.idlezione=tbl_lezioni.idlezione
               and tbl_firme.iddocente=$iddocente
               $seledata";

    $rislez=mysqli_query($con,inspref($querylez)) or die("Errore: ".inspref($querylez,false));
    while($reclez=mysqli_fetch_array($rislez))
    {
        $datalez=$reclez['datalezione'];
        $orainizio=$reclez['orainizio'];
        $numeroore=$reclez['numeroore'];
        for ($i=$orainizio;$i<($orainizio+$numeroore);$i++)
        {
            $indicearray=$datalez.$i;
            $arrlezioni[$indicearray]=1;

        }
    }



    $orenorm=count($arrlezioni);


    $arrlezionicert=array();
    $querylezcert="select * from tbl_lezionicert
               where tbl_lezionicert.iddocente=$iddocente
               and idlezionenorm=0
               $seledata";
    if ($sostegno)
    {
        $rislezcert = mysqli_query($con, inspref($querylezcert)) or die("Errore: " . inspref($querylezcert, false));
        while ($reclezcert = mysqli_fetch_array($rislezcert))
        {
            $datalez = $reclezcert['datalezione'];
            $orainizio = $reclezcert['orainizio'];
            $numeroore = $reclezcert['numeroore'];
            for ($i = $orainizio; $i < ($orainizio + $numeroore); $i++)
            {
                $indicearray = $datalez . $i;
                $arrlezionicert[$indicearray] = 1;

            }
        }

        $orecert = count($arrlezionicert);
    }
    else
        $orecert=0;
    // $orenorm$orenorm;

    $orenorm+=$orecert;

    echo '
             <tr>
                <td><font size=1><b> ' . $val["cognome"] . ' </b></td>
                <td><font size=1><b> ' . $val["nome"] . '    </b></td>

                ';
        print "<td><center>$orenorm</td></tr>";




}

echo '</table>';

// print"<br/><center><a href=javascript:Popup('staasse.php?classe=$idclasse&datainizio=$datainizio&datafine=$datafine')><img src='../immagini/stampa.png'></a></center><br/><br/>";
// fine if

mysqli_close($con);
stampa_piede(""); 

