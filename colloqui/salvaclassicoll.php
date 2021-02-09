<?php

session_start();

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

//Visualizzazione classi
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Selezione classe colloqui";
$script = "";
stampa_head($titolo, "", $script, "MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Selezione classe colloqui", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$idgiornata = $_GET['idgiornata'];

$query = "SELECT * FROM tbl_classi order by specializzazione,anno, sezione";
$result = eseguiQuery($con, $query);

$query2 = "SELECT * FROM tbl_colloquiclasse WHERE idgiornatacolloqui=$idgiornata";
$result2 = eseguiQuery($con, $query2);

$classispuntate = array();

$i = 0;
while($row2 = mysqli_fetch_array($result2))
{
  $classispuntate[$i] = $row2['idclasse'];
  $i++;
}

//foreach ($classispuntate as $i)
//  print "ID = $i<br>";

$data = $_GET['data'];

print "<center>";
print "<form action='./salvaclasse.php' method='get'>";
print "<b>ELENCO CLASSI</b><br><br>
       <table border=1 align='center'>";
       while($row = mysqli_fetch_array($result))
       {
         $idclasse = $row['idclasse'];
         $classe = $row['anno'].$row['sezione'].' '.$row['specializzazione'];
         print "<tr>
                <td align='left'>$classe</td>";
                if (in_array($idclasse, $classispuntate))
                  print "<td align='right'><input type='checkbox' value='$idclasse' name='idclassi[]' checked></td>";
                else
                  print "<td align='right'><input type='checkbox' value='$idclasse' name='idclassi[]'></td>";

         print "</tr>";
       }

print "</table><br>";
print "<input type='hidden' name='idgiornata' value='$idgiornata'>";
print "<input type='submit' value='Salva'>";
print "</form>";
print "</center>";

stampa_piede("");
mysqli_close($con);
