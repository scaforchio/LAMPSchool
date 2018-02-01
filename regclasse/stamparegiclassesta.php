<?php session_start();

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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
require_once '../lib/funregi.php';

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idclasse = stringa_html('idclasse');
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Stampa registro di classe";
$script = "
	<script type='text/javascript'>
         <!--
            function printPage()
            {
               if (window.print)
                  window.print();
               else
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');            }
         //-->
         </script>


";
//<style>
// @media print
//          {
//              h1 {page-break-after:always}
//           }
//</style>

stampa_head($titolo,"",$script,"SDMAP");

print ('<body class="stampa" onLoad="printPage()">');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$suff = $_SESSION['suffisso'] . "/";
if ($suff == "/")
{
    $suff = "";
}


print "<div><center><img src='../abc/$suff" . "testata.jpg" . "'>";

print "<br><br><big><big><B>REGISTRO DI CLASSE<br>A.S. $annoscol-" . ($annoscol + 1) . "<br></B></big></big></center>";


$data = $datainiziolezioni;


while ($data <= $datafinelezioni)
{

    if (esiste_lezione($data,$con))
        stampa_reg_classe($data, $idclasse, 2000000000, $numeromassimoore, $con, false,$gestcentrassenze);
    do
    {
        $data = aggiungi_giorni($data, 1);
    } while (giorno_settimana($data) == "Dom" | giorno_festa($data, $con));

}


mysqli_close($con);
 stampa_piede("");

