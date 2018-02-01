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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$classeregistro = $_SESSION['classeregistro'];
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

// preparazione del link per tornare indietro nel registro di classe
$goback = goBackRiepilogoRegistro();

$titolo = "Inserimento e modifica uscite anticipate";
$script = "<script src='../lib/js/popupjquery.js'></script>
           <script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri)
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
// tttt Da disabilitare quando ci sarà la compatibilità di firefox con time e date
$script .= "<script>
$(document).ready(function(){

				 $('input[name^=\"orauscita\"]').datetimepicker({
						formatTime: 'H:i',
						format: 'H:i',
						step: 5,
						datepicker:false
					});
			 });
</script>";
// tttt

stampa_head($titolo, "", $script,"MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a>$goback[1] - $titolo", "", "$nome_scuola", "$comune_scuola");


$nome = stringa_html('cl');
$but = stringa_html('visusc');
$giorno = stringa_html('gio');
$meseanno = stringa_html('meseanno');  // In effetti contiene sia il mese che l'anno
// Divido il mese dall'anno
$mese = substr($meseanno, 0, 2);
$anno = substr($meseanno, 5, 4);

$giornosettimana = giorno_settimana($anno . "-" . $mese . "-" . $giorno);

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


print ('
   <form method="post" action="usc.php" name="tbl_assenze">
         <input type="hidden" name="goback" value="' . $goback[0] . '">
         <input type="hidden" name="idclasse" value="' . $nome . '">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="cl" ONCHANGE="tbl_assenze.submit()">');


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

if ($classeregistro == "")
{
    $query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY specializzazione, sezione, anno";
    print "<option value=''>&nbsp;";
}
else
{
    $query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi where idclasse=$classeregistro ORDER BY specializzazione, sezione, anno";
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
echo('
      </SELECT>
      </td></tr>

      <tr>
      <td width="50%"><p align="center"><b>Data (gg/mm/aaaa)</b></p></td>');

//
//   Inizio visualizzazione della data
//


echo('   <td width="50%">');
if ($classeregistro == "")
{
    echo('   <select name="gio"  ONCHANGE="tbl_assenze.submit()">');
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


if ($classeregistro == "")
{
    echo('   <select name="meseanno" ONCHANGE="tbl_assenze.submit()">');
}
else
{
    print ("<input type='hidden' name='meseanno' value='$mese'>");
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
      </td></tr>
    </table>
 
    <table align='center'>
      <td>");
//   <p align="center"><input type="submit" value="Visualizza uscite anticipate" name="b"></p>
echo(' </td>
   
</table></form><hr>
 
    ');

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

if (($nome != "") && ((checkdate($m, $g, $a)) & !($giornosettimana == "Dom")))
{
    $idclasse = $nome;
    $classe = "";
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


    $query = 'SELECT * FROM tbl_classi WHERE idclasse="' . $idclasse . '" ';
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    if ($val = mysqli_fetch_array($ris))
    {
        $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
    }

    $query = 'SELECT * FROM tbl_alunni WHERE idclasse="' . $idclasse . '" ORDER BY cognome,nome';
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

    $c = mysqli_fetch_array($ris);

    if ($c == NULL)
    {
        echo '
          <p align="center">
		    <font size=4 color="black">Nessun alunno presente nella classe ' . $nome . '</font>
          </p>';
        mysqli_close($con);
        stampa_piede("");
        exit;
    }

    echo '<p align="center">
        <font size=4 color="black">Alunni della classe ' . $classe . '</font>
        <form method="post" action="insuscita.php">
        <table border=2 align="center">';
    echo '
   <tr class=prima>
          <td><b> N. </b></td>
          <td><b>Alunno</b></td>
          
          

          <td><b> Ora usc.(HH:MM)</b></td>
          <td><b> Dettaglio </b></td> 
   </tr>
  ';


    $query = "SELECT * FROM tbl_alunni WHERE idalunno IN (" . estrai_alunni_classe_data($idclasse, $anno . '-' . $mese . '-' . $giorno, $con) . ") ORDER BY cognome, nome, datanascita";

    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    $cont=0;
    while ($val = mysqli_fetch_array($ris))
    {
        $cont++;
        if ($val['autuscita'] != "")
        {
            $autousci = "<br><small>Perm. usc.: " . $val['autuscita'] . "</small>";
        }
        else
        {
            $autousci = "";
        }
        echo '
        <tr>
          
           <td><b>'.$cont.'</b></td><td><b> ' . $val["cognome"] . ' ' . $val["nome"] . ' ' . data_italiana($val["datanascita"]) . ' ' . $autousci . ' </b></td>';


// Codice per ricerca uscite già inserite
        $queryusc = 'SELECT * FROM tbl_usciteanticipate WHERE idalunno = ' . $val["idalunno"] . ' AND data = "' . $anno . '-' . $mese . '-' . $giorno . '"';

        $risusc = mysqli_query($con, inspref($queryusc)) or die ("Errore nella query: " . mysqli_error($con));

        $valusc = mysqli_fetch_array($risusc);

// Fine codice per ricerca uscite già inserite


       /* print "<td><center>";
        echo "<select class='smallchar' name='numeroore" . $val["idalunno"] . "' disabled>";
        for ($i = 0; $i <= ($numeromassimoore - 1); $i++)
        {
            if ($i != $valusc["numeroore"])
            {
                print "<option>" . $i;
            }
            else
            {
                print "<option selected>" . $i;
            }
        }
        print "</select>";
        print "</td>";
       */

        if ($valusc['orauscita'] != "00:00:00")
        {
            $valore = substr($valusc['orauscita'], 0, 5);
        }
        else
        {
            $valore = "";
        }

        print "<td><input type='text' name='orauscita" . $val["idalunno"] . "' maxlength='5' size=5 value='$valore'></td>";
        // tttt da cambiare quando firefox supporterà time print "<td><input type='time' name='orauscita" . $val["idalunno"] . "' maxlength='5' size=5 value='$valore'></td>";

        print "<td><center>";
        print "<a href=javascript:Popup('stasitassalu.php?alunno=" . $val['idalunno'] . "')><img src='../immagini/tabella.png'></a>";
        print "</center></td>";
        print"</tr>";
    }
    echo '</table>
    <p align="center"><input type=submit name=b value="Inserisci uscita">
    <p align="center"><input type=hidden value=' . $idclasse . ' name=cl>
    <p align="center"><input type=hidden value=' . $giorno . ' name=gio>
    <p align="center"><input type=hidden value=' . $mese . ' name=mese>
    <p align="center"><input type=hidden value=' . $anno . ' name=anno>
</form>
';
}
else
{
    if ($giornosettimana == "Dom")
    {
        print("<center><big><big>Il giorno selezionato &egrave; una domenica</big></big></center>");
    }
    else
    {
        if ($nome == "")
        {
            print("");
        }
        else
        {
            print("<center><big><big>La data selezionata non &egrave; valida</big></big></center>");
        }
    }
}
// fine if

mysqli_close($con);
stampa_piede("");

