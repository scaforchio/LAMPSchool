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

//
//    VISUALIZZAZIONE DELLE ASSEMBLEE DI CLASSE PER I GENITORI
//	  E
//	  RICHIESTA DI ASSEMBLEE DI CLASSE PER GLI ALUNNI 
//


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

//  istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$titolo = "Inserisci verbale";
$script = "";
stampa_head($titolo, "", $script, "L");
//$idclasse = stringa_html('idclasse');
$idalunno = $_SESSION['idstudente'];
$idclasse = estrai_classe_alunno($idalunno, $con);
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='assricgen.php?idclasse=$idclasse'>Visualizza assemblee</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$idassemblea = stringa_html('idassemblea');

$segretario = stringa_html('segretario');
$verbale = stringa_html('verbale');
$oratermine= stringa_html('oratermine');

$query = "UPDATE tbl_assemblee SET verbale='" . $verbale . "', alunnosegretario=$segretario, oratermine='$oratermine' WHERE idassemblea=$idassemblea";
$ris2 = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));

header("Location: assricgen.php?idclasse=$idclasse");

stampa_piede("");
mysqli_close($con);
