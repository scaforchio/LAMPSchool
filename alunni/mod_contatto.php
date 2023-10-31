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

//Programma per la modifica dell'elenco delle tbl_aule

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");

@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login 



$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Modifica dati di contatto tutor";
$script = "";
stampa_head_new($titolo, "", $script, "T");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("\n<h1> Connessione al server fallita </h1>");
    exit;
};


//Esecuzione query
$sql = "select * from tbl_alunni where idalunno=" . $_SESSION['idutente'];
$ris = eseguiQuery($con, $sql);
$dati = mysqli_fetch_array($ris);

$email = $dati['email'];
$email2 = $dati['email2'];
$telcel = $dati['telcel'];
print "<form action='agg_contatto.php' method='POST'>";

print "<CENTER style='max-width: 40%; margin: auto;'>";

print "<label for='email1' class='form-label'><i>Email 1</i></label> <input type='text' class='form-control' id='email' name='email' value='$email'>";
print "<label for='email2' class='form-label' style='margin-top: 10px;'><i>Email 2</i></label> <input type='text' class='form-control' id='email2' name='email2' value='$email2'>";
print "<label for='email1' class='form-label' style='margin-top: 10px;'><i>Cellulare</i></label> <input type='text' class='form-control' id='telcel' name='telcel' value='$telcel'>";

print "<p><br><b>Con la registrazione dei dati inseriti si autorizza la scuola ad inviare email ed SMS riguardanti le attività scolastiche dell'alunno.</b></ps>";
print "<CENTER><input type='submit' class='btn btn-outline-secondary mb-3' value='REGISTRA'>";
print "</CENTER></form>";

stampa_piede_new("");
mysqli_close($con);
