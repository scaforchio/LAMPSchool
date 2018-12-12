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

/* Programma per la visualizzazione dell'elenco delle tbl_classi. */

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


$daticrud = $_SESSION['daticrud'];
$titolo = "Inserimento nuovo record in tabella " . $daticrud['aliastabella'];
$script = "";
stampa_head($titolo, "", $script, "MAPSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='CRUD.php'>ELENCO</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$daticrud = $_SESSION['daticrud'];
ordina_array_su_campo_sottoarray($daticrud['campi'], 7);
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore connessione!");

// COSTRUZIONE QUERY DI INSERIMENTO

$elencocampi = "";
$elencovalori = "";
$valori = stringa_html('campo');

foreach ($daticrud['campi'] as $c)
    $elencocampi .= $c[0] . ", ";

foreach ($valori as $v)
    $elencovalori .= "'$v', ";

//print $elencocampi;
$elencocampi = substr($elencocampi, 0, strlen($elencocampi) - 2);
$elencovalori = substr($elencovalori, 0, strlen($elencovalori) - 2);
//print "<br>$elencovalori";

$queryins = "insert into " . $daticrud['tabella'] . "($elencocampi) values ($elencovalori)";
print $queryins;
eseguiQuery($con, $queryins);
inserisci_log($_SESSION['userid'] . "§" . date('m-d|H:i:s') . "§" . $_SESSION['indirizzoip'] . "§" . $queryins . "");

// TTTT Aggiungere al log

header("location: ../crudtabelle/CRUD.php?suffisso=" . $_SESSION['suffisso']);

stampa_piede("");
mysqli_close($con);


