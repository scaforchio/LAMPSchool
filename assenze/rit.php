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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';

// istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$classeregistro = $_SESSION['classeregistro'];
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

// preparazione del link per tornare indietro nel registro di classe
$goback = goBackRiepilogoRegistro();

$titolo = "Inserimento e modifica ritardi";
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
$script .= "<script>

$(document).ready(function(){
        			 $('input[name^=\"oraentrata\"]').datetimepicker({
						formatTime: 'H:i',
						format: 'H:i',
						step: 5,
                                    datepicker:false
					});
			 });


</script>";
stampa_head($titolo, "", $script, "MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a>$goback[1] - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$nome = stringa_html('cl');
// $but = stringa_html('visrit');
$giorno = stringa_html('gio');
$meseanno = stringa_html('meseanno'); // In effetti contiene sia il mese che l'anno
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
         <form method="post" action="rit.php" name="tbl_assenze">
         <input type="hidden" name="goback" value="' . $goback[0] . '">
         <input type="hidden" name="idclasse" value="' . $nome . '">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="cl" ONCHANGE="tbl_assenze.submit()">');



if ($classeregistro == "")
{
    $query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY specializzazione, sezione, anno";
    print "<option value=''>&nbsp;";
} else
    $query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi where idclasse=$classeregistro ORDER BY specializzazione, sezione, anno";

$ris = eseguiQuery($con, $query);
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
//
//   Inizio visualizzazione della data
//


echo('   <td width="50%">');
if ($classeregistro == "")
{
    echo('   <select name="gio"  ONCHANGE="tbl_assenze.submit()">');
} else
{
    print ("<input type='hidden' name='gio' value='$giorno'>");
    echo('   <select name="gio" disabled>');
}
require '../lib/req_aggiungi_giorni_a_select.php';

echo("</select>");


if ($classeregistro == "")
{
    echo('   <select name="meseanno" ONCHANGE="tbl_assenze.submit()">');
} else
{
    print ("<input type='hidden' name='meseanno' value='$mese'>");
    echo('   <select name="meseanno" disabled>');
}
require '../lib/req_aggiungi_mesi_a_select.php';

echo("</select>");

//
//  Fine visualizzazione della data
//


echo("
      </td></tr>");


echo('

    </table>
 
    <table align="center">
      <td>');
//     <p align="center"><input type="submit" value="Visualizza ritardi" name="b"></p>
echo('  </td>
</table></form><hr>
    ');

if ($mese == "")
{
    $m = 0;
} else
{
    $m = $mese;
}
if ($giorno == "")
{
    $g = 0;
} else
{
    $g = $giorno;
}

if ($anno == "")
{
    $a = 0;
} else
{
    $a = $anno;
}


if (($nome != "") && ((checkdate($m, $g, $a)) & !($giornosettimana == "Dom")))
{
    $idclasse = $nome;
    $classe = "";
    $data = $a . "-" . $m . "-" . $g;
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


    $query = 'SELECT * FROM tbl_classi WHERE idclasse="' . $idclasse . '" ';
    $ris = eseguiQuery($con, $query);
    if ($val = mysqli_fetch_array($ris))
    {
        $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
    }

    $query = 'SELECT * FROM tbl_alunni WHERE idclasse="' . $idclasse . '" ORDER BY cognome,nome,datanascita';
    $ris = eseguiQuery($con, $query);

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
        <form method="post" action="insritardo.php">
        <table border=2 align="center">';
    echo '
   <tr class=prima>
          <td><b> N. </b></td>
          <td><b> Alunno </b></td>
                   

          <td><b> Orario(HH:MM)</b></td>
          <td><b> Dettaglio </b></td> 
          <td><b> Giust. </b></td>
          <td><b> Rit. Q. </b></td>
   </tr>
  ';

    // controlla se fare la query per il primo o secondo quadrimestre
    $date_now = date("Y-m-d");
    $fpdt = new DateTime($_SESSION['fineprimo']);
    $fpdt->modify("+1 day");
    $iniziosecondo = date_format($fpdt,"Y-m-d");
    $fineprimo = $_SESSION['fineprimo'];
    if ($date_now > $fineprimo) {
        $queryextension = "and data > '$fineprimo'";
    }else{
        $queryextension = "and data < '$iniziosecondo'";
    }


    $query = "SELECT * FROM tbl_alunni WHERE idalunno in (" . estrai_alunni_classe_data($idclasse, $anno . '-' . $mese . '-' . $giorno, $con) . ")  order by cognome, nome, datanascita";

    $ris = eseguiQuery($con, $query);
    $cont = 0;
    while ($val = mysqli_fetch_array($ris))
    {
        $idalu = $val["idalunno"];
        // conta ritardi per quadrimestre selezionato
        $conteggioritardi = eseguiQuery($con, "select count(*) as numrit from tbl_ritardi where idalunno = $idalu $queryextension")->fetch_assoc()['numrit'];
    
        $cont++;
        if ($val['autentrata'] != "")
        {
            $autoentr = "<br><small>Perm. ingr.: " . $val['autentrata'] . "</small>";
        } else
        {
            $autoentr = "";
        }
        echo '
        <tr>
          
          <td><b>' . $cont . '</b></td><td><b> ' . $val["cognome"] . ' ' . $val["nome"] . ' ' . data_italiana($val["datanascita"]) . ' ' . $autoentr . ' </b></td>';

        //      <td><center>   <input type=checkbox name="rit'.$val["idalunno"].'
// Codice per ricerca tbl_ritardi già inseriti
        $queryrit = 'SELECT * FROM tbl_ritardi WHERE idalunno = ' . $val["idalunno"] . ' AND data = "' . $anno . '-' . $mese . '-' . $giorno . '"';

        $risrit = eseguiQuery($con, $queryrit);
        $valrit = mysqli_fetch_array($risrit);
        

        if ($valrit['oraentrata'] != "00:00:00")
            $valore = substr($valrit['oraentrata'], 0, 5);
        else
            $valore = "";


        // tttt Da sostituire quando firefox supporterà time    print "<td><input type='time' name='oraentrata" . $val["idalunno"] . "' maxlength='5' size=5 value='$valore'></td>";
        print "<td><input type='text' name='oraentrata" . $val["idalunno"] . "' maxlength='5' size=5 value='$valore'></td>";
        print "<td><center>";
        print "<a href=javascript:Popup('stasitassalu.php?alunno=" . $val['idalunno'] . "')><img src='../immagini/tabella.png'></a>";
        print "</center></td>";

        print "<td><center>";
        //  if ($_SESSION['giustifica_ritardi'] == 'yes')
        //  {
        $query = "select count(*) as numritingiust from tbl_ritardi
             where idalunno=" . $val['idalunno'] . "
             and data<= '$a-$m-$g'
             and (isnull(giustifica) or giustifica=0)";
        $risriting = eseguiQuery($con, $query);
        $valriting = mysqli_fetch_array($risriting);
        $numero_ritardi_ing = $valriting['numritingiust'];
        if ($numero_ritardi_ing > 0)
        {
            print "<a href='sitritdagiust.php?idalunno=" . $val['idalunno'] . "&idclasse=" . $val['idclasse'] . "&data=$data')><img src='../immagini/stilo.png'></a>";
        }
        //  }
        print "</center></td>";

        // conteggio ritardi
        if ($conteggioritardi >= $_SESSION['entrate_max']){
            //troppi
            print("<td><center> <img src='../immagini/ritwarn.png' style='margin-right: 2px;' width='20' height='20'> <b style='padding-top: 2px;'>$conteggioritardi</b></center></td>");
        }else {
            //normale
            print("<td><center>$conteggioritardi</center></td>");
        }

        print"</tr>";
    }
    echo '</table>
    <p align="center"><input type=submit name=b value="Inserisci ritardo">
    <p align="center"><input type=hidden value=' . $idclasse . ' name=cl>
    <p align="center"><input type=hidden value=' . $giorno . ' name=gio>
    <p align="center"><input type=hidden value=' . $mese . ' name=mese>
    <p align="center"><input type=hidden value=' . $anno . ' name=anno>
</form>
';
} else
{
    if ($giornosettimana == "Dom")
    {
        print("<center><big><big>Il giorno selezionato &egrave; una domenica</big></big></center>");
    } else
    {
        if ($nome == "")
        {
            print("");
        } else
        {
            print("<center><big><big>La data selezionata non &egrave; valida</big></big></center>");
        }
    }
}
// fine if

mysqli_close($con);
stampa_piede("");

