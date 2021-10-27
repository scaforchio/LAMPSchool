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



/* programma per la cancellazione di un docente
  riceve in ingresso iddocente */
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");


$id = stringa_html('id');
// istruzioni per tornare alla pagina di login 

$daticrud = $_SESSION['daticrud'];
$titolo = "Conferma Cancellazione " . $daticrud['aliastabella'];
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='CRUD.php'>ELENCO</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

print "<center><b>SEI SICURO DI VOLER CANCELLARE?";
print("<FORM NAME='CAN' ACTION='CRUDcancellazione.php' method='POST'>");
print("<br><table><INPUT TYPE='hidden' name='id' value='" . $id . "'>");
print("<tr><td align='left'><INPUT TYPE='SUBMIT' VALUE='    SI    '></td><td colspan='2'></td>");
print("\n</FORM>");

print("<FORM NAME='CAN' ACTION='CRUD.php' method='POST'>");
print("<td align='right'><INPUT TYPE='SUBMIT' VALUE='    NO    '></td></tr></table>");
print("\n</FORM>");
print "</center>";

stampa_piede("");
mysqli_close($con);

