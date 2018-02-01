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


$titolo = "Diario di classe";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$idnota = stringa_html('idnota');
$idclasse = stringa_html('idclasse');

if ($idnota != "")   // se si arriva dalla pagina della ricerca
{
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


    $query = "SELECT * FROM tbl_diariocl WHERE iddiariocl=" . $idnota . "";
    $ris = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query,false));
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
    $iddocente = is_stringa_html('iddocente') ? stringa_html('iddocente') : $_SESSION['idutente'];
    $idalunno = stringa_html('idalunno');
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
   <form method="post" action="diariocl.php" name="tbl_diariocl">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="idclasse" ONCHANGE="tbl_diariocl.submit()">
      <option value="">&nbsp;  ');


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


// Riempimento combo box tbl_classi

$iddocente = $_SESSION['idutente'];
$query = "select distinct tbl_classi.idclasse,anno,sezione,specializzazione
        from tbl_classi,tbl_cattnosupp
        where tbl_classi.idclasse=tbl_cattnosupp.idclasse
        and tbl_cattnosupp.iddocente=$iddocente
        order by specializzazione, sezione, anno";
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
    echo('   <select name="gio" ONCHANGE="tbl_diariocl.submit()">');
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

    echo('   <select name="mese" ONCHANGE="tbl_diariocl.submit()">');
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



//   Fine riempimento combo box tbl_docenti

echo('</table>
 
    <table align="center">
      <td>');
//   <p align="center"><input type="submit" value="Visualizza" name="b"></p>
echo('     </td>
   
</table><hr></form>
 
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


if (($idclasse != "") && ((checkdate($m, $g, $a)) & !($giornosettimana == "Dom")))
{

    $classe = "";
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


    //   $query='select * from tbl_classi where idclasse="'.$idclasse.'" ';
    //   $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    //   if($val=mysqli_fetch_array($ris))
    //      $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];
    //  print $iddocente;
    $query = "select * from tbl_diariocl where idclasse=$idclasse and data='$data' and iddocente=$iddocente";
    //  print $query;
    $ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query,false));

    $c = mysqli_fetch_array($ris);

    if ($c == NULL)
    {
        echo "<form method='post' action='insdiariocl.php'>
             <table border=2 align='center'><tr class=prima><td align='center'><b>Osservazioni</b></td></tr><tr><td>";
        echo "<textarea cols=60 rows=10 name ='notacl'>";
        echo "";
        echo "</textarea><br/>";
        echo "</td>";
    }
    else
    {
        echo "<form method='post' action='insdiariocl.php'>
             <table border=2 align='center'><tr class=prima><td align='center'><b>Osservazioni</b></td></tr><tr><td>";
        echo "<textarea  cols=60 rows=10 name ='notacl'>";
        echo $c['testo'];
        echo "</textarea><br/>";
        echo "</td>";
    }

    // VISUALIZZO EVENTUALI ANNOTAZIONI GIA' PRESENTI

    $query="select * from tbl_diariocl where idclasse=$idclasse and data='$data' order by oraultmod desc";
    $risprec=mysqli_query($con,inspref($query));
    if (mysqli_num_rows($risprec)>0)
    {

        print "<tr><td>";
        while($recprec=mysqli_fetch_array($risprec))
        {
            print $recprec['testo']."<br>";
            print "<p align='right'><i>".estrai_dati_docente($recprec['iddocente'],$con). " - ". data_italiana(substr($recprec['oraultmod'],0,10))." ".substr($recprec['oraultmod'],11,5)."</i></p>";
        }
        print "</td></tr>";
    }
    echo '</tr></table>';

    echo '
  
          <table align="center">
          <tr> </tr>
          </table>
          <p align="center"><input type=submit name=b value="Inserisci osservazione">
          <p align="center"><input type=hidden value=' . $idclasse . ' name=idclasse>
	  <p align="center"><input type=hidden value=' . $giorno . ' name=gio>
	  <p align="center"><input type=hidden value=' . $mese . ' name=mese>
          <p align="center"><input type=hidden value=' . $anno . ' name=anno>
          <p align="center"><input type=hidden value=' . $iddocente . ' name=iddocente>

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

            print("<Center> <big><big>La data selezionata non &egrave; valida<small><small> </center>");

    }
}
// fine if

mysqli_close($con);
stampa_piede(""); 

