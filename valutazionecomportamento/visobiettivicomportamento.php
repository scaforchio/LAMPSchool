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


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$iddocprog = 0;

$titolo = "Visualizzazione Obiettivi Comportamento";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";

stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


print "<center>OBIETTIVI DI COMPORTAMENTO<br/>";
print "</center>";

$query = "select * from tbl_compob order by numeroordine";
// print inspref($query);
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

print "<font size=2>";
while ($val = mysqli_fetch_array($ris))
{
    $numord = $val["numeroordine"];
    $sintob = $val["sintob"];
    $obiettivo = $val["obiettivo"];
    $idobiettivo = $val["idobiettivo"];
    print "<br/><br/><b>$numord. $sintob</b><br>  $obiettivo";

    $query = "select * from tbl_compsubob where idobiettivo=$idobiettivo order by numeroordine";
    $risabil = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    print "<font size=1>";
    while ($valabil = mysqli_fetch_array($risabil))
    {
        $sintsubob = $valabil["sintsubob"];
        $numordsubob = $valabil["numeroordine"];
        $subob=$valabil["subob"];



            print "<br/><i><b>$numord.$numordsubob $sintsubob</b><br> $subob</i>";

    }


    print "</font>";
}

print "</font>";

print "<br>";

mysqli_close($con);
stampa_piede("");
