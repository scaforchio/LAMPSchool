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

//
//    Parte iniziale della pagina
//

$titolo = "Modifica obiettivo di comportamento";

$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


//
//    Fine parte iniziale della pagina
//


$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

// Carico in una combobox a scelta multipla tutte le competenze della programmazione
print "<form method='post' action='updobiettivo.php' name='updcomp' >";

print "<table align='center'>
					 <tr>
						 <td valign='top'> <center><b>Obiettivi:</b><br/></center>";
// Conto competenze, abilità e conoscenze per dimensionare la select multiple
$query = "SELECT count(*) AS numobiettivi FROM tbl_compob";
// print $query;
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
$nomcomp = mysqli_fetch_array($ris);
$numobiettivi = $nomcomp['numobiettivi'];

$totalerighe = $numobiettivi;

print "<select name='idobiettivo' size='$totalerighe'>";
$query = "SELECT * FROM tbl_compob
	              ORDER BY numeroordine";
$riscomp = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

while ($nomcomp = mysqli_fetch_array($riscomp))
{
    $idobiettivo = $nomcomp['idobiettivo'];

    print "<option value='" . $nomcomp['idobiettivo'] . "'>" . $nomcomp['sintob'] . "</option>";

}
print "</select>";

print "</td></tr>";

echo "</table>";
print "<center><input type='submit' value='Modifica obiettivo di comportamento'></center></form>";



mysqli_close($con);
stampa_piede("");




