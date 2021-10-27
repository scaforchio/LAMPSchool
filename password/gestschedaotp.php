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


/* Programma per il cambiamento password. */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$utente=stringa_html('utente');
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
//Headers di partenza
$titolo = "Scheda OTP";
$script = "<script src='../lib/js/crypto.js'></script>\n";
$script .= "<script>
   
</script>\n";
stampa_head($titolo, "", $script, "SMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("\n<h1> Connessione al server fallita </h1>");
    exit;
}

print ("
   <form method='post' action='gestschedaotp.php' name='gestotp'>
   
   <p align='center'>
   <table align='center' border='1'>

      <tr class='prima'>
      <td colspan='2' align='center'><b>Utente</b>");

$sqld = "SELECT * FROM tbl_utenti WHERE tipo='S' or tipo='D' or tipo='P' ORDER BY userid";

$resd = eseguiQuery($con, $sqld);
if (!$resd)
{
    print ("<br/> <br/> <br/> <h2>Impossibile visualizzare i dati </h2>");
} else
{
    print ("<select name='utente' ONCHANGE='gestotp.submit();'>");
    print ("<option>");
    while ($datal = mysqli_fetch_array($resd))
    {
        print("<option value='");
        print($datal['userid']);
        print("'");
        if ($utente == $datal['userid'])
        {
            print " selected";
        }
        print ">";
        print($datal['userid']);
    }
}
print("</select> </td> </tr>");
print("</table>");
print "</form>";
if ($utente != "")
{
    $query = "select schematoken from tbl_utenti where userid='$utente'";
    $ris = eseguiQuery($con, $query);
    $rec = mysqli_fetch_array($ris);

    $schematoken = $rec['schematoken'];
    print "<br>";
    print "<br>";
    print("<center>SCHEDA PER OTP");
    print "<table border='1'>";

    print "<tr class='prima'> <td></td><td><big><big>A<small><small></td><td><big><big>B<small><small></td><td><big><big>C<small><small></td><td><big><big>D<small><small></td><td><big><big>E<small><small></td><td><big><big>F<small><small></td><td><big><big>G<small><small></td><td><big><big>H<small><small></td><td><big><big>I<small><small></td><td><big><big>L<small><small></big></td></tr>";
    for ($i = 0; $i < 5; $i++)
    {
        $nr = $i + 1;
        print "<tr><td class='prima'><big><big>$nr<small><small></td>";
        for ($j = 0; $j < 10; $j++)
        {
            print "<td><big>";
            print substr($schematoken, $i * 10 + $j, 1);
            print "<small></td>";
        }
        print "</tr>";
    }
    print "</table>";
    print "<br><a href='javascript:void(0)' onclick='window.print()'>
          Stampa la tabella
       </a>";
}


mysqli_close($con);
stampa_piede("");


