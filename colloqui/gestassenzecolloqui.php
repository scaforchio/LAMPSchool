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

$dataselez = $_POST['data'];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Gestione assenze docenti";
$script = "";
stampa_head($titolo, "", $script, "SPM");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

//select di data
$dataoggi = date("Y-m-d");

$query = "SELECT DISTINCT data
          FROM tbl_giornatacolloqui
          WHERE data >= $dataoggi
          ORDER BY data";

$risultato = eseguiQuery($con, $query);

print "<form method='post' action='gestassenzecolloqui.php' name='programmi'>
		    <input type='hidden' name='suffisso' value='$suffisso'>
		    <p align='center'>
		    <table align='center'>";

print "<tr>
		    <td width='50%'><b>Data colloquio</b></p></td>
			  <td width='50%'>
			     <select name='data' onchange='programmi.submit()'>";

if(mysqli_num_rows($risultato) == 0)
{
    print"<option value=''></option>
          </select></td></tr></table></form>
          <p align='center'><b><i><font color='red' size ='5'> Ops!</font></i></b></p>
          <br><center><font color='red' size ='3'><i>Non ci sono date disponibili per i colloqui</i></font></center></br</p>";
}
else
{
    while ($row = mysqli_fetch_array($risultato))
    {
      $data = $row['data'];
      $dataform = date("d/m/Y", strtotime($data));

      print "<option value='$data'";
      if($data == $dataselez)
      {
        print " selected";
      }
      elseif($dataselez =="")
      {
        if(isset($_GET["data"]))
        {
          $dataselez = $_GET["data"];
        }
        else
        {
          $dataselez = $data;
        }
      }
      print ">$dataform</option>";

    }
    print "</select></td></tr></table></form>";

    //caricamento docenti assenti
    $query = "SELECT doc.iddocente
              FROM tbl_docenti AS doc
              WHERE EXISTS(SELECT *
                           FROM tbl_assenzedocenticolloqui, tbl_giornatacolloqui
                           WHERE tbl_assenzedocenticolloqui.iddocente=doc.iddocente
                           AND tbl_assenzedocenticolloqui.idgiornatacolloqui=tbl_giornatacolloqui.idgiornatacolloqui
                           AND tbl_giornatacolloqui.data='$dataselez')";

    $risultato = eseguiQuery($con, $query);

    $docassenti = [];

    while($row = mysqli_fetch_array($risultato))
    {
      $docassenti[] = $row['iddocente'];
    }


    //tabella visualizzazione assenze
    $query = "SELECT DISTINCT cognome,nome,tbl_docenti.iddocente
              FROM tbl_cattnosupp,tbl_docenti, tbl_colloquiclasse, tbl_classi, tbl_giornatacolloqui
              WHERE tbl_cattnosupp.iddocente=tbl_docenti.iddocente
              AND tbl_cattnosupp.idclasse=tbl_classi.idclasse
              AND tbl_colloquiclasse.idclasse=tbl_classi.idclasse
              AND tbl_colloquiclasse.idgiornatacolloqui=tbl_giornatacolloqui.idgiornatacolloqui
              AND tbl_cattnosupp.iddocente!=1000000000
              AND tbl_giornatacolloqui.data='$dataselez'
              ORDER BY cognome,nome";

    $risultato = eseguiQuery($con, $query);


    print "<center><form name='form2' action='registraassenzecolloqui.php' method='POST'>
          <input type='date' name='data' value='$dataselez' hidden>
          <center><table border='1' bordercolor='black'>
          <tr class='prima'><td align='center'><b>Docenti</b></td>
            <td align='center'><b>Assenze</b></td>
          </tr>";

  $arrayiddoc = [];

  while ($row = mysqli_fetch_array($risultato))
  {
      $cognome = $row['cognome'];
      $nome = $row['nome'];
      $iddocente = $row['iddocente'];

      print "<tr>
              <td>$cognome $nome</td>
              <td align='center'><input type='checkbox' name='docenti[]' value=$iddocente";

      $arrayiddoc[] = $iddocente;

      if(in_array($iddocente, $docassenti))
          print " checked>";
      else
          print " >";

      print "</td></tr>";
  }

  print "</table></center>
        <br><input type='submit' name='registra' value='Registra'>
        </form></center>
        </tr>
        </table>";
}

stampa_piede("");

