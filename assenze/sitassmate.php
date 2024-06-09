<?php

require_once '../lib/req_apertura_sessione.php';

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
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

// istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Situazione assenze per materia";
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


stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$classe = stringa_html('cl');


//$giornosettimana=giorno_settimana($anno."-".$mese."-".$giorno);

$datainizio = stringa_html("datainizio");
$datafine = stringa_html("datafine");

$dataoggi = date("d/m/Y");
if ($datainizio == "")
{
    $datainizio = data_italiana($_SESSION['datainiziolezioni']);
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
   <form method='post' action='sitassmate.php' name='assenze'>
   
   <p align='center'>
   <table align='center'>
   <tr>
      <td width='50%'><center><b>Classe</b></center></td>
      <td width='50%'>
      <SELECT ID='cl' NAME='cl' ONCHANGE='assenze.submit()'> <option value=''>&nbsp ");


$query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY specializzazione, sezione, anno";
if ($tipoutente == 'D')
{
    $query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi
           WHERE idcoordinatore=" . $_SESSION['idutente'] . " ORDER BY anno,sezione,specializzazione";
}
$ris = eseguiQuery($con, $query);
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
    if ($classe == $nom["idclasse"])
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
	<input type='text' name='datainizio' id='datainizio' class='datepicker' size='8' maxlength='10' value='$datainizio'  ONCHANGE='assenze.submit()'>
	</td></tr>
	<tr>
	<td width='50%'><center><b>Data fine </b></center></td>
	<td width='50%'>
	<input type='text' name='datafine' id='datafine'  class='datepicker' size='8' maxlength='10' value='$datafine' ONCHANGE='assenze.submit()'>
	</td></tr>");


print ("</table>");

if ($classe != "")
{
    $assenze=array();
    $orelez=array();
    
    $seledatalezione = "";
    $seledata = "";
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
    
    $query="select * from tbl_lezioni where idclasse=$classe $seledatalezione";
    $ris= eseguiQuery($con, $query);
    while ($rec=mysqli_fetch_array($ris))
    {
        $idmateria=$rec['idmateria'];
        $numeroore=$rec['numeroore'];
        if (isset($orelez["$idmateria"]))
            $orelez["$idmateria"]+=$numeroore;
        else
            $orelez["$idmateria"]=$numeroore;
    }

    $elencoalunni= estrai_alunni_classe_data($classe, date('Y-m-d'), $con);
    $query="select * from tbl_asslezione where idalunno in ($elencoalunni) $seledata";
    
    $ris= eseguiQuery($con, $query);
    while ($rec=mysqli_fetch_array($ris))
    {
        $idmateria=$rec['idmateria'];
        $idalunno=$rec['idalunno'];
        $numeroore=$rec['oreassenza'];
        
        if (isset($assenze["$idmateria $idalunno"]))
            $assenze["$idmateria $idalunno"]+=$numeroore;
        else
            $assenze["$idmateria $idalunno"]=$numeroore;
    }
    
    $query = "SELECT distinct tbl_materie.idmateria,tbl_materie.denominazione,sigla,tipovalutazione, progrpag FROM tbl_cattnosupp,tbl_materie
              WHERE tbl_cattnosupp.idmateria=tbl_materie.idmateria
              and tbl_cattnosupp.idclasse=$classe
              and tbl_cattnosupp.iddocente <> 1000000000
              and tbl_materie.progrpag<100
              order by progrpag,tbl_materie.sigla";
    $rismat= eseguiQuery($con, $query);
    print "<table align='center' border='1'>";
    print "<tr class='prima'><td></td>";
    foreach($rismat as $recmat)
        print "<td align='center'>".$recmat['sigla']."</td>";
    print "</tr>";
    $query="select * from tbl_alunni where idalunno in ($elencoalunni) order by cognome, nome, datanascita";
    $risalu=eseguiQuery($con, $query);
    foreach($risalu as $recalu)
    {
        print "<tr><td>".$recalu['cognome']." ".$recalu['nome']."</td>";
        foreach($rismat as $recmat)
        {
            $ricercaass=$recmat['idmateria']." ".$recalu['idalunno'];
            if (isset($assenze[$ricercaass]))
                print "<td align='center'>".$assenze[$ricercaass]."(".round(($assenze[$ricercaass]/$orelez[$recmat['idmateria']]*100))."%)</td>";
            else
                print "<td> -- </td>";
        }
        print "</tr>";
                 
    }
    
    print "</table>";
    print "<center><img src='../immagini/stampa.png' onClick='printPage();'</center>";
}

mysqli_close($con);
stampa_piede("");

