<?php

require_once '../lib/req_apertura_sessione.php';

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

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$titolo = "Ricerca note individuali";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$idutente = $_SESSION['idutente'];
$idclasse = stringa_html('idclasse');
// $but = stringa_html('visass');
$giorno = stringa_html('gio');
$iddocente = stringa_html('iddocente');
$idalunno = stringa_html('idalunno');
$meseanno = stringa_html('mese');  // In effetti contiene sia il mese che l'anno
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
           <form method="post" action="ricnoteind.php" name="tbl_notealunno">

           <p align="center">
           <table align="center">
           <tr>
              <td width="50%"><p align="center"><b>Classe</b></p></td>
              <td width="50%">
              <SELECT ID="cl" NAME="idclasse" ONCHANGE="tbl_notealunno.submit()">
              <option value="">&nbsp;  ');

if ($tipoutente == "S" | $tipoutente == "P")
{
    print "<option value='all'";
    if ($idclasse == 'all')
        print " selected";
    print ">Tutte";
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


// Riempimento combo box tbl_classi
if ($tipoutente == "S" | $tipoutente == "P")
{
    $query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY specializzazione, sezione, anno";
} else
{
    $query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi
              WHERE idclasse in (select distinct idclasse from tbl_cattnosupp where iddocente=$idutente)
              ORDER BY specializzazione, sezione, anno";
}
$ris = eseguiQuery($con, $query);
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
echo('   <select name="gio" ONCHANGE="tbl_notealunno.submit()"><option value="">&nbsp');
require '../lib/req_aggiungi_giorni_a_select.php';

echo("</select>");

echo('   <select name="mese" ONCHANGE="tbl_notealunno.submit()"><option value="">&nbsp');
require '../lib/req_aggiungi_mesi_a_select.php';

echo("</select></td></tr>");


//
//  Fine visualizzazione della data
//
// Riempimento combo box tbl_docenti
print "<tr><td width='50%'><p align='center'><b>Docente</b></p></td><td>";
$query = "SELECT iddocente,cognome,nome FROM tbl_docenti ORDER BY cognome, nome";
$ris = eseguiQuery($con, $query);
echo("<select name='iddocente' ONCHANGE='tbl_notealunno.submit()'><option value=''>&nbsp");
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["iddocente"]);
    print "'";
    if (c == $nom["iddocente"])
    {
        print " selected";
    }
    print ">";
    print ($nom["cognome"]);
    print "&nbsp;";
    print($nom["nome"]);
}

// Riempimento combo box tbl_alunni
if ($idclasse != "" & $idclasse != "all")
{
    print "<tr><td width='50%'><p align='center'><b>Alunno</b></p></td><td>";
    $query = "select idalunno,cognome,nome,datanascita from tbl_alunni where idclasse=$idclasse order by cognome, nome, datanascita";
    $ris = eseguiQuery($con, $query);
    echo("<select name='idalunno' ONCHANGE='tbl_notealunno.submit()'><option value=''>&nbsp");
    while ($nom = mysqli_fetch_array($ris))
    {
        print "<option value='";
        print ($nom["idalunno"]);
        print "'";
        if ($idalunno == $nom["idalunno"])
        {
            print " selected";
        }
        print ">";
        print ($nom["cognome"]);
        print "&nbsp;";
        print($nom["nome"]);
        print "&nbsp;&nbsp;&nbsp;";
        print(data_italiana($nom["datanascita"]));
    }
} else
{
    print "<tr><td width='50%'><p align='center'><b>Alunno</b></p></td><td>";
    echo("<select name='idalunno'><option value=''>&nbsp");
}

echo('
      </SELECT>
      </td></tr>');


//   Fine riempimento combo box tbl_alunni

echo('</table>
 
    <table align="center">
      <td>');
//     <p align="center"><input type="submit" value="Visualizza" name="b"></p>
echo('</form></td>
   
</table><hr>
 
    ');


$stringaricerca = " true ";
if ($idclasse != "")
{
    if ($idclasse != 'all')
    {
        $stringaricerca = $stringaricerca . " and tbl_notealunno.idclasse=$idclasse ";
    }
} else
{
    // $stringaricerca = $stringaricerca . " and 1=2 ";
    if ($_SESSION['tipoutente'] == 'D')
        $stringaricerca = $stringaricerca . " and tbl_notealunno.iddocente=$idutente ";
}


if ($iddocente != "")
{
    $stringaricerca = $stringaricerca . " and tbl_notealunno.iddocente=$iddocente ";
}
if ($idalunno != "")
{
    $stringaricerca = $stringaricerca . " and tbl_noteindalu.idalunno=$idalunno ";
}
if ($mese != "")
{
    $stringaricerca = $stringaricerca . " and month(tbl_notealunno.data)=$mese ";
    if ($giorno != "")
    {
        $stringaricerca = $stringaricerca . " and day(tbl_notealunno.data)=$giorno ";
    }
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


//   $query='select * from tbl_classi where idclasse="'.$idclasse.'" ';
//   $ris=eseguiQuery($con,$query);
//   if($val=mysqli_fetch_array($ris))
//      $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];
//  print $iddocente;
$query = "select tbl_notealunno.idnotaalunno, data, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno,tbl_alunni.idalunno as idalunno, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, tbl_docenti.iddocente as iddocente, specializzazione, sezione,anno, tbl_alunni.datanascita, testo, provvedimenti
            from tbl_noteindalu,tbl_notealunno,tbl_classi, tbl_alunni, tbl_docenti 
            where 
            tbl_noteindalu.idnotaalunno=tbl_notealunno.idnotaalunno
            and tbl_noteindalu.idalunno=tbl_alunni.idalunno
            and tbl_notealunno.idclasse=tbl_classi.idclasse and  tbl_notealunno.iddocente=tbl_docenti.iddocente 
            and $stringaricerca 
            order by tbl_notealunno.data desc, tbl_classi.specializzazione, tbl_classi.sezione, tbl_classi.anno, tbl_notealunno.data, tbl_docenti.cognome, tbl_docenti.nome, tbl_alunni.cognome, tbl_alunni.nome, tbl_alunni.datanascita";
// print $query."<br/>";
$ris = eseguiQuery($con, $query);

$c = mysqli_num_rows($ris);


if ($c == 0)
{
    echo "<center><b>Nessuna nota da visualizzare!</b></center>";
} else
{
    print "<table border=1  align='center'>";
    print "<tr class='prima'><td>Classe</td><td>Docente</td><td>Data</td><td>Alunno</td><td>Nota</td><td>Provv.</td><td>Modif.</td><td>Canc.</td></tr>";
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
        print($rec['cognalunno'] . " " . $rec['nomealunno'] . " <br/> " . data_italiana($rec['dataalunno']));
        print("</td>");
        print("<td>");
        print("<i>" . $rec['testo'] . "</i>");
        print("</td>");
        print("<td>");
        print("<i>" . $rec['provvedimenti'] . "</i>");
        print("</td>");
        print("<td>");
        if ($tipoutente == "P" | $tipoutente == "S" | ($idutente == $rec['iddocente'] && $rec['data'] == date('Y-m-d')))
        {
            print("<center><a href='noteindmul.php?idnota=" . $rec['idnotaalunno'] . "' title='Modifica'><img src='../immagini/modifica.png' alt='Modifica'></a>");
        }
        print("</td>");
        print("<td>");
        if ($tipoutente == "P" | $tipoutente == "S" | ($idutente == $rec['iddocente'] && $rec['data'] == date('Y-m-d')))
        {
            print("<center><a href='confcancnotealu.php?idnota=" . $rec['idnotaalunno'] . "&idalunno=" . $rec['idalunno'] . "' title='Cancella'><img src='../immagini/cancella.png' alt='Cancella'></a>");
        }
        print("</td>");

        print("</tr>");
    }
    print "</table>";
}

mysqli_close($con);
stampa_piede("");

