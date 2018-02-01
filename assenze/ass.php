<?php

session_start();
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
require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

// preparazione del link per tornare indietro nel registro di classe
$goback = goBackRiepilogoRegistro();
$classeregistro = $_SESSION['classeregistro'];

$titolo = "Inserimento e modifica assenze";
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

stampa_head($titolo, "", $script, "SDP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a>$goback[1] - $titolo", "", "$nome_scuola", "$comune_scuola");

$nome = stringa_html('cl');
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
   <form method="post" action="ass.php" name="tbl_assenze">
         <input type="hidden" name="goback" value="' . $goback[0] . '">
         <input type="hidden" name="idclasse" value="' . $nome . '">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="cl" ONCHANGE="tbl_assenze.submit()">');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
if ($classeregistro == "")
{
    $query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY specializzazione, sezione, anno";
    print "<option value=''>&nbsp;";
}
else
    $query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi where idclasse=$classeregistro ORDER BY specializzazione, sezione, anno";
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

    print ">{$nom['anno']}&nbsp;{$nom['sezione']}&nbsp;{$nom['specializzazione']}</option>";
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
    </form><hr/>
");

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
    $data = $a . "-" . $m . "-" . $g;
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

    $query = 'SELECT * FROM tbl_classi WHERE idclasse="' . $idclasse . '" ';
    $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));
    if ($val = mysqli_fetch_array($ris))
    {
        $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
    }
    $query = "SELECT idalunno AS al FROM tbl_alunni WHERE idalunno IN (" . estrai_alunni_classe_data($idclasse, $data, $con) . ")  ORDER BY cognome, nome, datanascita";

    $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));

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
          <font size=4 color="black">Assenze della classe ' . $classe . ' - <small>' . $giornosettimana . '</small></font>
          <form method="post" action="insassenza.php">
          <table border=2 align="center">';
    echo '
          <tr class=prima>
          <td><b> N. </b></td>
          <td><b> Alunno </b></td>
          
          <td><b> Assenza  </b></td>
          <td><b> Dettaglio </b></td> 
          <td><b> Giust. </b></td>
          </tr>
        ';

    // $query = 'SELECT * FROM tbl_alunni WHERE idclasse="' . $idclasse . '" ORDER BY cognome,nome,datanascita';
    $query = "SELECT * FROM tbl_alunni WHERE idalunno IN (" . estrai_alunni_classe_data($idclasse, $data, $con) . ")  ORDER BY cognome, nome, datanascita";
    $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));
    $cont = 0;
    while ($val = mysqli_fetch_array($ris))
    {
        $cont++;




        if ($val['autentrata'] != "")
        {
            $autoentr = "<br><small>Perm. ingr.: " . $val['autentrata'] . "</small>";
        }
        else
        {
            $autoentr = "";
        }
        echo '
             <tr>
                 <td><b>' . $cont . '</b></td><td><b> ' . $val["cognome"] . ' ' . $val["nome"] . ' ' . data_italiana($val["datanascita"]) . ' ' . $autoentr . '</b></td>
                <td><center>   <input type=checkbox name="ass' . $val["idalunno"] . '"';

        // VERIFICA PRESENZA FORZATA
        $querypf = 'SELECT * FROM tbl_presenzeforzate WHERE idalunno = ' . $val["idalunno"] . ' AND data = "' . $a . '-' . $m . '-' . $g . '"';

        $rispf = mysqli_query($con, inspref($querypf)) or die("Errore nella query: " . mysqli_error($con));
        if ($valpf = mysqli_fetch_array($rispf))
        {
            print " disabled";
        }

        // Codice per ricerca tbl_assenze già inserite
        $queryass = 'SELECT * FROM tbl_assenze WHERE idalunno = ' . $val["idalunno"] . ' AND data = "' . $a . '-' . $m . '-' . $g . '"';

        $risass = mysqli_query($con, inspref($queryass)) or die("Errore nella query: " . mysqli_error($con));
        if ($valass = mysqli_fetch_array($risass))
        {
            print " checked";
        }


        // Fine codice per ricerca tbl_assenze già inserite

        print "></center></td>";
        print "<td><center>";

        print "<a href=javascript:Popup('stasitassalu.php?alunno=" . $val['idalunno'] . "')>
             <img src='../immagini/tabella.png'></a>";


        print "</center></td>";
        print "<td><center>";

        $query = "select count(*) as numassingiust from tbl_assenze
                where idalunno=" . $val['idalunno'] . "
                and data< '$a-$m-$g'
                and not giustifica";
        $risassing = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));
        $valassing = mysqli_fetch_array($risassing);
        $numero_assenze_ing = $valassing['numassingiust'];
        if ($numero_assenze_ing > 0)
        {
            print "<a href='sitassdagiust.php?idalunno=" . $val['idalunno'] . "&idclasse=" . $val['idclasse'] . "&data=$data'><img src='../immagini/stilo.png'></a>";
        }

        print "</center></td>
      </tr>";
    }
    echo '</table>
          <p align="center"><input type=submit name=b value="Inserisci assenza">
          <p align="center"><input type=hidden value=' . $idclasse . ' name=cl>
	      <p align="center"><input type=hidden value=' . $g . ' name=gio>
	      <p align="center"><input type=hidden value=' . $m . ' name=mese>
          <p align="center"><input type=hidden value=' . $a . ' name=anno>
      </form>';
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

