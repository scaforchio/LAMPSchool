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

$suff = $_SESSION['suffisso'] . "/";
if ($suff == "/")
    $suff = "";
// istruzioni per tornare alla pagina di login se non c'� una sessione valida



$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idutente = $_SESSION["idutente"];

$nuovoutente = stringa_html('nuovoutente');
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Annullamento cambiamento utente";
$script = "";
stampa_head($titolo, "", $script, "PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));



if ($_SESSION['alias'])
{


    $_SESSION['tipoutente'] = $_SESSION['tipoorig'];
    $_SESSION['idutente'] = $_SESSION['idorig'];
    $_SESSION['userid'] = $_SESSION['useridorig'];

    if ($_SESSION['tipoutente'] == 'P')
    {
        $sql = "SELECT * FROM tbl_docenti WHERE idutente='" . $_SESSION['idutente'] . "'";
        $ris = eseguiQuery($con, $sql);

        if ($val = mysqli_fetch_array($ris))
        {
            $_SESSION['cognome'] = $val["cognome"];
            $_SESSION['nome'] = $val["nome"];
        }
    }
    $_SESSION['alias'] = false;
    inserisci_log($_SESSION['userid'] . "§" . date('m-d|H:i:s') . "§" . $_SESSION['indirizzoip'] . "§Fine aliasing");

    print "<br><b><center>Aliasing annullato! Tornare a pagina principale.";
}


stampa_piede("");

