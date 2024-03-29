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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
//require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();
// istruzioni per tornare alla pagina di login se non c'è una sessione valida


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Visualizza permessi e ferie";
$script = "";
stampa_head($titolo, "", $script, "PS");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));



print "<br><center><b>PERMESSI E FERIE</b></center><br><br>";


print "<table border='1' align='center'>";
print "<tr class='prima'><td>Docente</td><td>Ore perm.</td><td>Giorni ferie</td><td>Giorni perm.</td></tr>";
// TTTT


$query = "select * from tbl_docenti order by cognome, nome";

$ris = eseguiQuery($con, $query);
while ($rec = mysqli_fetch_array($ris))
{

    $iddoc = $rec['iddocente'];
    $nominativo = estrai_dati_docente($iddoc, $con);

    /* $totaleore=calcolaOrePermesso($iddoc,$con);
      $giorniferie = calcolaGiorniFerie($iddoc,$con);
      $giorniperm = calcolaGiorniPermesso($iddoc,$con); */

    $totaleore = contaOrePermesso($iddoc, $con);
    $totaleorerec = contaOreRecuperate($iddoc, $con);
    $giorniferie = contaGiorniFerie($iddoc, $con);
    $giorniperm = contaGiorniPermesso($iddoc, $con);


    /*  $totaleore = 0;
      $query = "select * from tbl_richiesteferie where iddocente=$iddoc and concessione=1 and subject LIKE '%permesso breve%'";
      $risperm = eseguiQuery($con,$query);
      while ($recperm = mysqli_fetch_array($risperm)) {

      $mail = $recperm['testomail'];
      $posizioneore = strpos($mail, "per un totale di ore") + 21;
      // PREPARAZIONE STRINGA SINTETICA RICHIESTA
      $periodo = $recperm['subject'];
      //$posperiodo = strpos($testocompleto,"", $testocompleto)j
      //str_replace("");
      $oreperm = substr($mail, $posizioneore, 1) . "<br>";

      $totaleore += $oreperm;
      } */
    if ($totaleorerec != '')
        $recuperate = "(Rec. $totaleorerec)";
    else
        $recuperate = "";
    if ($totaleore > 0 | $giorniferie > 0 | $giorniperm > 0)
        print "<tr><td>$nominativo</td><td>$totaleore $recuperate</td><td>$giorniferie</td><td>$giorniperm</td></tr>";
}
print "</table>";
print "<br>";


mysqli_close($con);
stampa_piede("");

