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

/* Programma per la modifica delle specializzazione. */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
// istruzioni per tornare alla pagina di login 

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Modifica tipo documento";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_tdoc.php'>Elenco</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

print("<br/><br/>");
//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("\n<h1> Connessione al server fallita </h1>");
    exit;
};

//Connessione al database
$DB = true;
if (!$DB)
{
    print("\n<h1> Connessione al database fallita </h1>");
    exit;
};

//Esecuzione query
$idsp = stringa_html('idspe');
$sql = "select * from tbl_tipidocumenti where idtipodocumento=$idsp";
if (!($ris = eseguiQuery($con, $sql)))
{
    print("\n<h1> Query fallita </h1>");
    exit;
} else
{
    $dati = mysqli_fetch_array($ris);
    print "<form action='agg_tdoc.php' method='POST'>";
    print "<input type='hidden' name='idtipodocumento' value='" . $dati['idtipodocumento'] . "'>";
    print "<CENTER><table border='0'>";
    print "<tr><td ALIGN='CENTER'> Tipo documento&nbsp;</td> <td ALIGN='CENTER'> ";
    print "<input type='text' name='denomin' value='" . $dati['descrizione'] . "'>";
    print "</td></tr>";

    print "<tr>";
    print "<td COLSPAN='2' ALIGN='CENTER'><br/><input type='submit' value='Aggiorna'></td> ";
    print "</form>";
    print "<TR><TD COLSPAN='2'>&nbsp;</TD></TR>";
    print "</table></CENTER>";
}

mysqli_close($con);
stampa_piede("");

