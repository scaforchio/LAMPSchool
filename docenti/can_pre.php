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
/* programma per l'inserimento di un docente
  riceve in ingresso i valori del docente */
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login 

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$titolo = "Cancellazione preside";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<H1>connessione al server mysql fallita</H1>");
    exit;
}
$DB = true;
if (!$DB)
{
    print("<H1>connessione al database stage fallita</H1>");
    exit;
}


// VERIFICO SE ESISTE GIA' il PRESIDE

$que = "select * from tbl_docenti where iddocente=1000000000";
$res = eseguiQuery($con, $que);
if (mysqli_num_rows($res) == 0)
{
    print("<br><CENTER>Preside non inserito!</CENTER>");
} else
{

    $query = "delete from tbl_docenti where iddocente=1000000000";
    $res = eseguiQuery($con, $query);
    if (!$res)
        print("<h2>Errore nella cancellazione del preside</h2>");
    else
    {
        // CANCELLO ANCHE IL RECORD NELLA TABELLA DEGLI UTENTI

        $query = "delete from tbl_utenti where idutente=1000000000";
        eseguiQuery($con, $query);
        echo "<br><b><center>Utenza preside cancellata!</center></b>";
    }
}

stampa_piede("");
mysqli_close($con);

