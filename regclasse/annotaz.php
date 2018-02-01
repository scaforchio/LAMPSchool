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
$titolo = "Inserimento e modifica annotazioni";
$script = "";

stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a>$goback[1] - $titolo", "", "$nome_scuola", "$comune_scuola");

$idannotazione = stringa_html('idannotazione');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

if ($idannotazione != "")   // se si arriva dalla pagina della ricerca
{
    $query = "SELECT * FROM tbl_annotazioni WHERE idannotazione=" . $idannotazione . " ";
    $ris = mysqli_query($con, inspref($query));
    $nom = mysqli_fetch_array($ris);
    $idclasse = $nom['idclasse'];
    // $but = stringa_html('visass');
    $giorno = substr($nom['data'], 8, 2);
    $mese = substr($nom['data'], 5, 2);
    $anno = substr($nom['data'], 0, 4);
    // $but = stringa_html('visass');
    $iddocente = $nom['iddocente'];
}
else
{
    $idclasse = stringa_html('idclasse');
    $giorno = stringa_html('gio');
    $iddocente = stringa_html('iddocente');
    if ($iddocente == "")
    {
        $iddocente = $_SESSION['idutente'];
    }
    $meseanno = stringa_html('mese');  // In effetti contiene sia il mese che l'anno
    // Divido il mese dall'anno
    $mese = substr($meseanno, 0, 2);
    $anno = substr($meseanno, 5, 4);
}

$data = $anno . "-" . $mese . "-" . $giorno;
$giornosettimana = giorno_settimana($data);

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
   <form method="post" action="annotaz.php" name="tbl_noteclasse">
         <input type="hidden" name="goback" value="' . $goback[0] . '">
         <input type="hidden" name="idclasse" value="' . $idclasse . '">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="idclasse" ONCHANGE="tbl_noteclasse.submit()"><option value="">&nbsp;');


// Riempimento combo box tbl_classi
if ($classeregistro == "")
{
    $query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY specializzazione, sezione, anno";
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
      <td width="50%"><p align="center"><b>Data (gg/mm/aaaa)</b></p></td>');


echo('   <td width="50%">');
if ($classeregistro == "")
{
    echo('   <select name="gio" ONCHANGE="tbl_noteclasse.submit()">');
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
        echo("<option selected>$gs");
    }
    else
    {
        echo("<option>$gs");
    }
}
echo("</select>");

if ($classeregistro == "")
{
    echo('   <select name="mese" ONCHANGE="tbl_noteclasse.submit()">');
}
else
{
    print ("<input type='hidden' name='mese' value='$mese'>");
    echo('   <select name="mese" disabled>');
}
for ($m = 9; $m <= 12; $m++)
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
        echo("<option selected>$ms - $annoscol");
    }
    else
    {
        echo("<option>$ms - $annoscol");
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
        echo("<option selected>$ms - $annoscolsucc");
    }
    else
    {
        echo("<option>$ms - $annoscolsucc");
    }
}
echo("</select></td></tr>");


//
//  Fine visualizzazione della data
//

// Riempimento combo box tbl_docenti
print "<tr><td width='50%'><p align='center'><b>Docente</b></p></td><td>";
$query = "SELECT iddocente,cognome,nome FROM tbl_docenti ORDER BY cognome, nome";
$ris = mysqli_query($con, inspref($query));
if ($tipoutente == 'P' | $tipoutente == 'S')
{
    echo("<select name='iddocente' ONCHANGE='tbl_noteclasse.submit()'>");
}
else
{
    echo("<select name='iddocente' ONCHANGE='tbl_noteclasse.submit()' disabled>");
}
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["iddocente"]);
    print "'";
    if ($iddocente == $nom["iddocente"])
    {
        print " selected";
    }
    print ">";
    print ($nom["cognome"]);
    print "&nbsp;";
    print($nom["nome"]);

}

echo('
      </SELECT>
      </td></tr>');


//   Fine riempimento combo box tbl_docenti

echo('</table>
 
    <table align="center">
      <td>');
//   <p align="center"><input type="submit" value="Visualizza nota" name="b"></p>
echo('</form></td>
   
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


// print($nome." -   ". $m.$g.$a.$giornosettimana);

if (($idclasse != "") && ((checkdate($m, $g, $a)) & !($giornosettimana == "Dom")))
{
    $c=NULL;
    if ($idannotazione != "")
    {
        //$idclasse = $nome;
        //$classe = "";
        $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


        //   $query='select * from tbl_classi where idclasse="'.$idclasse.'" ';
        //   $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
        //   if($val=mysqli_fetch_array($ris))
        //      $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];
        //  print $iddocente;
        // $query = "select * from tbl_annotazioni where idclasse='$idclasse' and data='$data' and iddocente='$iddocente'";
        $query = "select * from tbl_annotazioni where idannotazione='$idannotazione'";
        //  print $query;
        $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query di selezione nota: " . mysqli_error($con));

        $c = mysqli_fetch_array($ris);
    }
    if ($c == NULL)
    {
        echo "<form method='post' action='insannotaz.php'>
          <table border=1 align='center'><tr class=prima><td align='center'><b>Annotazione</b></td></tr><tr><td>";
        echo "<textarea cols=60 rows=10 name ='testo'>";
        echo "";
        echo "</textarea><br/>";
        echo "</td>";


    }
    else
    {
        echo "<form method='post' action='insannotaz.php'>
          <table border=2 align='center'><tr class=prima><td align='center'><b>Annotazione</b></td></tr><tr><td>";
        echo "<textarea  cols=60 rows=10 name ='testo'>";
        echo $c['testo'];
        echo "</textarea><br/>";
        echo "</td>";

    }
    echo '</tr></table>';

    echo '
  
          <table align="center">
          <tr> </tr>
          </table>
          <p align="center"><input type=submit name=b value="Inserisci annotazione">
          <p align="center"><input type=hidden value=' . $idclasse . ' name=idclasse>
	      <p align="center"><input type=hidden value=' . $giorno . ' name=gio>
	      <p align="center"><input type=hidden value=' . $mese . ' name=mese>
          <p align="center"><input type=hidden value=' . $anno . ' name=anno>
          <p align="center"><input type=hidden value=' . $iddocente . ' name=iddocente>
          <p align="center"><input type=hidden value=' . $idannotazione . ' name=idannotazione>
          </form>
         ';


}
else
{
    if ($giornosettimana == "Dom")
    {
        print("<Center> <big><big>Il giorno selezionato &egrave; una domenica<small><small> </center>");
    }
    else
    {
        if ($idclasse == "")
        {
            print("");
        }
        else
        {
            print("<Center> <big><big>La data selezionata non &egrave; valida<small><small> </center>");
        }
    }
}
// fine if

mysqli_close($con);
stampa_piede(""); 

