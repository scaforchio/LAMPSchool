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
//MODIFICA PER RICARICARE ANCHE IL VOTO DI COMPORTAMENTO - RIGA 63
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

$titolo = "Ricaricamento proposte valutazioni certificazioni";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("$titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);



$idclasse = stringa_html('idclasse');
$livscuola = stringa_html('livscuola');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));



$query = "SELECT idalunno FROM tbl_alunni WHERE idclasse=$idclasse";
$ris = eseguiQuery($con, $query);

while ($recalu = mysqli_fetch_array($ris))
{
    $query = "DELETE FROM tbl_certcompvalutazioni WHERE idalunno=" . $recalu['idalunno'];
    $risdel = eseguiQuery($con, $query);

    importa_proposte($con, $recalu['idalunno'], $livscuola);
}


print "<form method='post' id='formricprop' action='cctabellone.php'>";

print "<input type='hidden' name='idclasse' value='$idclasse'>
       <input type='hidden' name='ricarica' value='yes'>
        
        </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formricprop').submit();
        }
        </SCRIPT>";


stampa_piede("");


