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
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$titolo = "Ricerca osservazioni sistematiche";
$script = "<script>



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
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$idclasse = stringa_html('idclasse');
// $but = stringa_html('visass');
$datainizio = stringa_html('datainizio');
$datafine = stringa_html('datafine');
$testo = stringa_html('testo');
if ($tipoutente != 'P')
{
    $iddocente = $_SESSION['idutente'];
}


/*
if ($giorno=='')
   $giorno=date('d');
if ($mese=='')
   $mese=date('m');
if ($anno=='')
   $anno=date('Y');
*/

print ('
   <form method="post" action="ricdiariocl.php" name="tbl_diariocl">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="idclasse">
      <option value="">&nbsp;  ');


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


// Riempimento combo box tbl_classi
if ($tipoutente == "P")
{
    $query = "SELECT tbl_classi.idclasse,anno,sezione,specializzazione
           FROM tbl_classi
           ORDER BY specializzazione, sezione, anno";
}
else
{
    $query = "select distinct tbl_classi.idclasse,anno,sezione,specializzazione
           from tbl_classi,tbl_cattnosupp
           where tbl_classi.idclasse=tbl_cattnosupp.idclasse
           and tbl_cattnosupp.iddocente='$iddocente'
           order by specializzazione, sezione, anno";
}
//print inspref($query);        
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

echo('
      </SELECT>
      </td></tr>');


//
//   Inizio visualizzazione della data
//
echo('      <tr>
      <td width="50%"><p align="center"><b>Data inizio</b></p></td>');


echo('   <td width="50%">');
print("<input type='text' name='datainizio' value='" . $datainizio . "' id='datainizio'>");

print("</td></tr>");

//
//   Inizio visualizzazione della data
//
echo('      <tr>
      <td width="50%"><p align="center"><b>Data fine</b></p></td>');


echo('   <td width="50%">');
print("<input type='text' name='datafine' value='" .$datafine . "' id='datafine'>");

print("</td></tr>");


//
//  Fine visualizzazione della data
//

//
//   Inizio visualizzazione della data
//
echo('      <tr>
      <td width="50%"><p align="center"><b>Testo contenuto</b></p></td>');


echo('   <td width="50%">');
print("<input type='text' name='testo' value='$testo' id='testo'>");

print("</td></tr>");


//   Fine riempimento combo box tbl_alunni

echo('</table><center><input type="submit" value="Cerca"></center></form>');

/*  
  if ($mese=="")
     $m=0;
  else
     $m=$mese; 
  if ($giorno=="") 
     $g=0;
  else
     $g=$giorno; 

  if ($anno=="") 
     $a=0;
  else
     $a=$anno; 
*/

// print($nome." -   ". $m.$g.$a.$giornosettimana);


$stringaricerca = " true ";
if ($idclasse != "")
{
    $stringaricerca = $stringaricerca . " and tbl_diariocl.idclasse=$idclasse ";
}

if ($datainizio != "")
{
    $stringaricerca = $stringaricerca . " and data>='".data_to_db($datainizio)."' ";

}
if ($datafine != "")
{
    $stringaricerca = $stringaricerca . " and data<='".data_to_db($datafine)."' ";

}

if ($testo != "")
{
    $stringaricerca = $stringaricerca . " and testo like '%$testo%' ";

}


//   $query='select * from tbl_classi where idclasse="'.$idclasse.'" ';
//   $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
//   if($val=mysqli_fetch_array($ris))
//      $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];
//  print $iddocente;

if ($idclasse != "")
{
    $query = "select iddiariocl, data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente,tbl_docenti.iddocente,  testo
            from tbl_diariocl,  tbl_docenti
            where tbl_diariocl.iddocente=tbl_docenti.iddocente
            and $stringaricerca 
            order by data desc,tbl_diariocl.oraultmod desc";

    // print inspref($query);
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query di selezione osservazione: " . mysqli_error($con));

    $c = mysqli_num_rows($ris);


    if ($c == 0)
    {
        echo "<center><b>Nessuna annotazione da visualizzare!</b></center>";
    }
    else
    {
        print "<table border=1  align='center'>";
        print "<tr class='prima'><td>Docente</td><td>Data</td><td>Osservazione</td><td>Modif.</td></tr>";
        while ($rec = mysqli_fetch_array($ris))
        {
            print("<tr>");

            print("<td>");
            print($rec['cogndocente'] . " " . $rec['nomedocente']);
            print("</td>");
            print("<td>");
            print(data_italiana($rec['data']));
            print("</td>");

            print("<td>");
            print("<i>" . $rec['testo'] . "</i>");
            print("</td>");
            print("<td>");
            if ($tipoutente != "P")
            {
                if ($rec['iddocente']==$_SESSION['idutente'])
                    print("<center><a href='diariocl.php?idnota=" . $rec['iddiariocl'] . "' title='Modifica'><img src='../immagini/modifica.png' alt='Modifica'></a>");
            }
            print("</td>");
            print("</tr>");

        }
        print "</table>";

    }
}

mysqli_close($con);
stampa_piede(""); 

