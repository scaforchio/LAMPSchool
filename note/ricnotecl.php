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
$titolo = "Ricerca note di classe";

$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$idclasse = stringa_html('idclasse');
$giorno = stringa_html('gio');
$iddocente = stringa_html('iddocente');
$meseanno = stringa_html('mese');  // In effetti contiene sia il mese che l'anno
$idutente = $_SESSION['idutente'];
// Divido il mese dall'anno
$mese = substr($meseanno, 0, 2);
$anno = substr($meseanno, 5, 4);
$data = $anno . "-" . $mese . "-" . $giorno;
$giornosettimana = giorno_settimana($data);

/*
if ($giorno=='')
   $giorno=date('d');
if ($mese=='')
   $mese=date('m');
if ($anno=='')
   $anno=date('Y');
*/


print ('
   <form method="post" action="ricnotecl.php" name="tbl_noteclasse">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="idclasse" ONCHANGE="tbl_noteclasse.submit()">
      <option value="">&nbsp;  ');

if ($tipoutente == "S" | $tipoutente == "P")
{
    print "<option value='all'";
    if ($idclasse == 'all')
    {
        print " selected";
    }
    print ">Tutte";
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


// Riempimento combo box tbl_classi
if ($tipoutente == "S" | $tipoutente == "P")
{
    $query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY specializzazione, sezione, anno";
}
else
{
    $query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi
              WHERE idclasse in (select distinct idclasse from tbl_cattnosupp where iddocente=$idutente)
              ORDER BY specializzazione, sezione, anno";
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
echo('   <select name="gio" ONCHANGE="tbl_noteclasse.submit()"><option value="">&nbsp');
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

echo('   <select name="mese" ONCHANGE="tbl_noteclasse.submit()"><option value="">&nbsp');
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
echo("<select name='iddocente' ONCHANGE='tbl_noteclasse.submit()'><option value=''>&nbsp");
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


echo('</table>
 
    <table align="center">
      <td>');
//   <p align="center"><input type="submit" value="Visualizza" name="b"></p>
echo('     </form></td>
   
</table><hr>
 
    ');

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
    if ($idclasse != 'all')
    {
        $stringaricerca = $stringaricerca . " and tbl_noteclasse.idclasse=$idclasse ";
    }

}
else
{
   // $stringaricerca = $stringaricerca . " and 1=2 ";

    $stringaricerca = $stringaricerca . " and tbl_noteclasse.iddocente=$idutente ";
}
if ($iddocente != "")
{
    $stringaricerca = $stringaricerca . " and tbl_noteclasse.iddocente=$iddocente ";
}

if ($mese != "")
{
    $stringaricerca = $stringaricerca . " and month(tbl_noteclasse.data)=$mese ";
    if ($giorno != "")
    {
        $stringaricerca = $stringaricerca . " and day(tbl_noteclasse.data)=$giorno ";
    }
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$query = "select idnotaclasse, data, tbl_docenti.iddocente, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, specializzazione, sezione,anno,  testo, provvedimenti
            from tbl_noteclasse,tbl_classi, tbl_docenti 
            where tbl_noteclasse.idclasse=tbl_classi.idclasse and  tbl_noteclasse.iddocente=tbl_docenti.iddocente  
            and $stringaricerca 
            order by tbl_noteclasse.data desc, tbl_classi.specializzazione, tbl_classi.sezione, tbl_classi.anno, tbl_docenti.cognome, tbl_docenti.nome";
// print $query."<br/>";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query di selezione nota: " . mysqli_error($con));

$c = mysqli_num_rows($ris);


if ($c == 0)
{
    echo "<center><b>Nessuna nota da visualizzare!</b></center>";
}
else
{
    print "<table border=1 align='center'>";
    print "<tr class='prima'><td>Classe</td><td>Docente</td><td>Data</td><td>Nota</td><td>Provv.</td><td>Modif.</td><td>Elimina</td></tr>";
    while ($rec = mysqli_fetch_array($ris))
    {
        print("<tr>");
        print("<td>");
        print($rec['specializzazione'] . " " . $rec['sezione'] . " " . $rec['anno']);
        print("</td>");
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
        print("<i>" . $rec['provvedimenti'] . "</i>");
        print("</td>");
        print("<td>");
        if ($tipoutente == "P" | $tipoutente == "S"  | ($idutente == $rec['iddocente'] && $rec['data']==date('Y-m-d')))
        {
            print("<center><a href='notecl.php?idnota=" . $rec['idnotaclasse'] . "' title='Modifica'><img src='../immagini/modifica.png' alt='Modifica'></a>");
        }
        print("</td>");
        print("<td>");
        if ($tipoutente == "P" | $tipoutente == "S" | ($idutente == $rec['iddocente'] && $rec['data']==date('Y-m-d')))
        {
            print("<center><a href='confcancnotecla.php?idnota=" . $rec['idnotaclasse'] . "' title='Modifica'><img src='../immagini/cancella.png' alt='Cancella'></a>");
        }
        print("</td>");
        print("</tr>");

    }
    print "</table>";

}


mysqli_close($con);
stampa_piede(""); 

