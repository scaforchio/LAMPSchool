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
$sostegno = $_SESSION["sostegno"];


$tipo = stringa_html("tipo");


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$idclasse = stringa_html('idclasse');
$idalunno = stringa_html('idalunno');

$titolo = "Carica documenti classe";

$script = "";
$script .= "<script>
               
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
	                 $('#datadocumento').datepicker({ dateFormat: 'dd/mm/yy' });
	             });
</script>";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$visualizzabili = array("image/jpeg", "application/pdf", "image/pjpeg", "image/gif", "image/png");


//
//  SELEZIONE ALUNNO
//


print ("
   <form method='post' action='documenticlasse.php' name='documenti'>
   
   <p align='center'>
   <table align='center'>
   <tr>
      <td width='50%'><input type='hidden' name='tipo' value='$tipo'><p align='center'><b>Classe</b></p></td>
      <td width='50%'>
      <SELECT NAME='idclasse' ONCHANGE='documenti.submit()'>
      <option value=''>&nbsp;  ");


// Riempimento combo box tbl_classi

$iddocente = $_SESSION['idutente'];

if ($tipoutente!='S' && $tipoutente != 'P')
    $query = "select distinct tbl_classi.idclasse,anno,sezione,specializzazione
        from tbl_classi,tbl_cattnosupp
        where tbl_classi.idclasse=tbl_cattnosupp.idclasse
        and tbl_cattnosupp.iddocente=$iddocente
        order by specializzazione, sezione, anno";
else
    $query = "select idclasse,anno,sezione,specializzazione
        from tbl_classi
        order by specializzazione, sezione, anno";
$ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));
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

echo('
      </SELECT>
      </td></tr>');


print "</table></form>";

if ($idclasse != "")
{

    print ("
		
		
		<p align='center'>
		<table align='center' border='1'>
		<tr class='prima'>
			<td><b>Documento</b></td>
			<td><b>Tipo</b></td>
			<td><b>Data</b></td>
			<td><b>Docente</b></td>
			<td><b>File caricato</b></td>
			<td><b>Azione</b></td>");

    $query = "SELECT iddocumento,iddocente,tbl_documenti.idtipodocumento,tbl_documenti.pei,datadocumento,tbl_documenti.descrizione AS descrdoc,tbl_tipidocumenti.descrizione AS descrtipo,docsize,docnome,doctype
			  FROM tbl_documenti,tbl_tipidocumenti
			  WHERE tbl_documenti.idtipodocumento=tbl_tipidocumenti.idtipodocumento
			  AND tbl_documenti.idtipodocumento<1000000000
			  AND idclasse=$idclasse AND idalunno=0
			  ORDER BY datadocumento";

    $ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query) . mysqli_error($ris));
    while ($nom = mysqli_fetch_array($ris))
    {


        print "<tr><td>" . $nom['descrdoc'] .
            "</td><td>" . $nom['descrtipo'];

        print "</td><td>" . data_italiana($nom['datadocumento']) .
            "</td><td>" . estrai_dati_docente($nom['iddocente'], $con) .
            "</td><td>" . $nom["docnome"] .
            "<font size=1> (" . $nom["docsize"] . ") bytes</font></td>" .
            "<td><a href='actionsdocum.php?action=download&Id=" . $nom["iddocumento"] . "' target='_blank'><img src='../immagini/download.jpg' alt='scarica'></a> ";

        if (in_array($nom["doctype"], $visualizzabili))
        {
            echo "<a href='actionsdocum.php?action=view&Id=" . $nom["iddocumento"] . "' ";
            echo "target='_blank'><img src='../immagini/view.jpg' alt='visualizza'></a>  ";
        }

        if ($iddocente == $nom['iddocente'])
        {
            echo " <a href='mod_doc_classe.php?iddocumento=" . $nom["iddocumento"] . "&idclasse=$idclasse&idalunno=$idalunno&tipo=$tipo'><img src='../immagini/edit.png' alt='modifica descrizione'></a>
			         <a href='cancdocumentoclasse.php?iddocumento=" . $nom["iddocumento"] . "&idclasse=$idclasse&idalunno=$idalunno&tipo=$tipo'><img src='../immagini/delete.png' alt='cancella'></a>";
        }
    }
    print "</td></tr>";


    print "</table>";

    //
    //  AGGIUNTA DOCUMENTO
    //

    $querytd = "select * from tbl_tipidocumenti where idtipodocumento<1000000000";
    $ristd=mysqli_query($con,inspref($querytd));
    if (mysqli_num_rows($ristd)>0)
    {

        print "<br><br>";
        print "<fieldset><legend>AGGIUNGI DOCUMENTO</legend>";
        print ("
		
		<form action='insdocumentoclasse.php' method='POST' enctype='multipart/form-data'>
		<input type='hidden' name='idclasse' value='$idclasse'><input type='hidden' name='tipo' value='$tipo'>
		<p align='center'>
		<table align='center' border='1'>
		<tr class='prima'>
			<td><b>Documento</b></td>
			<td><b>Tipo</b></td>");

        print ("<td><b>Data</b></td>

			<td><b>File da caricare</b></td>
			");

        print "<tr>";
        print "<td><input type='text' maxlength='255' size='30' name='descrizione'></td>";
        print "<td><select name='idtipodocumento'>";
        $query = "SELECT idtipodocumento,descrizione FROM tbl_tipidocumenti";
        $ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));
        while ($rec = mysqli_fetch_array($ris))
        {

            print "<option value='" . $rec['idtipodocumento'] . "'>" . $rec['descrizione'] . "</option>";
        }
        print "</select></td>";


        print "<td>";
        $dataoggi = date('d/m/Y');
        print "<input type ='text' id='datadocumento' name='datadocumento' size='10' maxlength='10' value='$dataoggi'>";
        print "</td>";


        print ("<td><center><input type=file name='filedocumento' value='Carica file'>  </td></tr>");


        print "</table>";

        print "<center><br><input type='submit' value='Invia file selezionato'></center>";
        print "</form>";
        print "</fieldset>";
    }
    else
    {
        print "<br><br><center><b>Non sono stati definiti tipi di documento!</b></center>";

    }

}

mysqli_close($con);
stampa_piede(""); 

