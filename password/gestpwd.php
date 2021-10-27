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

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
//Headers di partenza
$titolo = "Gestione password";
$script = "<script src='../lib/js/crypto.js'></script>\n";
$script .= "<script>
    function codifica()
    {
        document.getElementById('npassmd5').value = hex_md5(document.getElementById('npas').value);
        document.getElementById('npas').value = '';
        document.getElementById('rnpassmd5').value = hex_md5(document.getElementById('rnpas').value);
        document.getElementById('rnpas').value = '';
        return true;
    }
</script>\n";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("\n<h1> Connessione al server fallita </h1>");
    exit;
}

print("<center>");
print "<table border='0'>";
print "<form name='form1' action='confcambpassgen.php' method='POST'>";
// print "<input type='hidden' name='npass' id='npassmd5'>";
print "<tr> <td> Utente </td> <td> <input type='text' name='utente' id='utente'> </td> </tr>";
print "<tr> <td> Nuova password </td> <td> <input type='text' name='npas' id='npas'> </td> </tr>";
print "<tr> <td> Ripeti nuova pasword </td> <td> <input type='text' name='rnpas' id='rnpas'> </td> </tr>";
print "</table>";


print "<center> <input type='submit' name='OK' value='OK'></center>";
print "</form></center>";

mysqli_close($con);
stampa_piede("");


