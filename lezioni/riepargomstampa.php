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


$titolo = "Situazione mensile tbl_lezioni";
$script = "
	<script type='text/javascript'>
         <!--
            function printPage()
            {
               if (window.print)
                  window.print();
               else 
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');            }
         //-->
         </script>
        

";
stampa_head($titolo, "", $script, "SDMAP");

$annoscolastico = $annoscol . "/" . ($annoscol + 1);

print ('<body class="stampa" onLoad="printPage()">');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));




$catt = stringa_html('cattedra');
if ($catt != "")
{
    // RECUPERO idclasse e idmateria dalla cattedra
    // Prelevo classe e materia dalla cattedra selezionata
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));



    if ($catt <> "")
    {
        $query = "select idclasse, idmateria from tbl_cattnosupp where idcattedra=$catt";
        $ris = mysqli_query($con, inspref($query));
        if ($nom = mysqli_fetch_array($ris))
        {
            $idmateria = $nom['idmateria'];
            $idclasse = $nom['idclasse'];
        }
    }
}
$id_ut_doc = $_SESSION["idutente"];






/*
  $query="select iddocente, cognome, nome from tbl_docenti where idutente=$id_ut_doc";

  $ris=mysqli_query($con,inspref($query));
  if($nom=mysqli_fetch_array($ris))
  {
  $iddocente=$nom["iddocente"];
  $cognomedoc=$nom["cognome"];
  $nomedoc=$nom["nome"];
  $nominativo =$nomedoc." ".$cognomedoc;
  }
 */
$classe = "";


$query = 'select * from tbl_classi where idclasse="' . $idclasse . '" ';
$ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));
if ($val = mysqli_fetch_array($ris))
    $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];

$query = 'select * from tbl_materie where idmateria="' . $idmateria . '" ';
$ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));
if ($val = mysqli_fetch_array($ris))
    $nomemateria = $val["denominazione"];

if ($_SESSION['suffisso'] != "")
    $suff = $_SESSION['suffisso'] . "/";
else
    $suff = "";
print ("<center><img src='../abc/" . $suff . "testata.jpg' width='600'></center>");

print ("<font size=2><center><br/>A.S. <i>$annoscolastico</i> <br/>Argomenti e attivit&agrave; <br/>Classe <i>$classe</i> - Materia: <i>$nomemateria</i>");

$strdocenti = "";
$query = "select iddocente from tbl_cattnosupp where idmateria=$idmateria and idclasse=$idclasse and iddocente<>1000000000";

$ris = mysqli_query($con, inspref($query));
if (mysqli_num_rows($ris) > 1)
{
    $strdocenti = "Docenti:<i> ";
    $piudocenti = true;
}
else
{
    $strdocenti = "Docente:<i> ";
    $piudocenti = false;
}
while ($nom = mysqli_fetch_array($ris))
{
    $strdocenti .= estrai_dati_docente($nom['iddocente'], $con) . ",";
}
$strdocenti = substr($strdocenti, 0, strlen($strdocenti) - 1);
$strdocenti .= "</i>";


print("<br/>$strdocenti</center> ");
print ("</font>");




//
//   ESTRAZIONE DATI DELLE LEZIONI
//

print ("<table border=1 width=100%>");
if ($idclasse != "")
{
    echo'
          <tr class="prima">
          
          <td width=10%>Data</td>
          <td width=90%>Argomenti e attivit&agrave;</td></tr>';
    $query = "select * from tbl_lezioni where idclasse=$idclasse and idmateria=$idmateria order by datalezione";
    $rislez = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));




    while ($reclez = mysqli_fetch_array($rislez))
    {
        print "<tr><td>" . data_italiana($reclez['datalezione']) . "</td><td>";
        if ($reclez['argomenti'] != "")
            print($reclez['argomenti']) . "&nbsp;<br/>";
        if ($reclez['attivita'] != "")
            print($reclez['attivita']) . "&nbsp;";
        print("</td></tr>");
    }
    print("</table>");


    print("<br/><br/><table border=0 width=100%><tr><td width=50%>&nbsp</td><td width=50% align='center'>Il docente<br/>(Prof. $nomedoc $cognomedoc)<br/><br/>______________________________</td></tr></table>");
}


// fine if





mysqli_close($con);


