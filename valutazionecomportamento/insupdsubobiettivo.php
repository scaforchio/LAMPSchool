
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
$iddocente = $_SESSION["idutente"];
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Aggiornamento sub-obiettivo";
$script = "";
stampa_head($titolo, "", $script, "MA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);



$idabil = stringa_html("idabil");
$sintesi = stringa_html("sintesi");
$descrizione = stringa_html("descrizione");





$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$query = "update tbl_compsubob set sintsubob='" . $sintesi . "', subob='" . $descrizione . "' where idsubob=$idabil";
// print inspref($query);
if ($ris = eseguiQuery($con, $query))
// header("location: modivoceprog.php?cattedra=$cattedra");
    print "
        <form method='post' id='formvoce' action='modisubobiettivo.php'>

        </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formvoce').submit();
        }
        </SCRIPT>";
else
    die("Errore nella query! Contattare il sistemista.");





mysqli_close($con);
stampa_piede("");



