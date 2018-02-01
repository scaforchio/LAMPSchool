<?php session_start();


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

/*Programma per la visualizzazione del menu principale.*/

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idesterno = "";
if ($tipoutente == "")
{
    header("location: login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "ESECUZIONE SQL";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
if (isset($_POST["que"]))
   $querydaeseguire=$_POST["que"];
else
    $querydaeseguire="";
// SVUOTO LA CARTELLA BUFFER DAI VECCHI FILE SQL E CSV
//  svuota_cartella("$cartellabuffer/", ".sql");
// svuota_cartella("$cartellabuffer/", ".csv");
// svuota_cartella("$cartellabuffer/", ".txt");

print "<form action='eseguisql_ok.php' method='POST'>";

print "<CENTER><table border='0'>";
// print "<tr><td ALIGN='CENTER'><b>".$dati['parametro']."</b></td></tr>";
print "<tr><td ALIGN='CENTER'><b>Query SQL<br>(Non aggiungere ai comandi il prefisso delle tabelle)<br></b></td></tr>";
print "<tr><td ALIGN='CENTER'><textarea name='que' rows='5' cols='80'>$querydaeseguire</textarea></td></tr>";
print "<tr><td ALIGN='CENTER'><input type='submit' value='Esegui SQL'></td></tr>";
print "</table></form>";


stampa_piede("");