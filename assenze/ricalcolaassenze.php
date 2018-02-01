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
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Ricalcolo assenze lezioni";
$script = "";
stampa_head($titolo, "", $script,"MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$idclasse = stringa_html('idclasse');
$inizio = stringa_html('inizio');
$fine = stringa_html('fine');


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

if ($idclasse!="0")
    $queryclasse="select idclasse from tbl_classi where idclasse=$idclasse";
else
    $queryclasse="select idclasse from tbl_classi where 1=1";

$ris=mysqli_query($con,inspref($queryclasse));
while ($rec=mysqli_fetch_array($ris))
{
    $data=$inizio;
    while ($data <= $fine)
    {
        if (!giorno_festa($data, $con))
        {
            ricalcola_assenze_lezioni_classe($con, $rec['idclasse'], $data);
            print "Ricalcolate assenze per classe " . decodifica_classe($rec['idclasse'], $con) . " in data " . $data . "<br>";
            ob_flush();
            flush();
        }
        $data = aggiungi_giorni($data, 1);

    }
}

mysqli_close($con);
stampa_piede("");


