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
//require_once '../lib/ db / query.php';

//$lQuery = LQuery::getIstanza();

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Visualizzazione accessi";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$ordinamento = stringa_html('ordinamento');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


print "<form name='viscatt' action='vis_accessi.php' method='post'>";
print "<center>Ordinamento:";
print "<select name='ordinamento'  ONCHANGE='viscatt.submit()'>";
if ($ordinamento == "cro" | $ordinamento == "") $selecro = ' selected';
else $selecro = '';
if ($ordinamento == "ute") $seleute = ' selected';
else $seleute = '';

print "<option value='cro'$selecro>Cronologico</option>
       <option value='ute'$seleute>Utente</option>
       ";
print "</select></center>";
print "</form>";


print "<center><br><b>Accessi</b></center><br>";

if ($ordinamento == 'cro' | $ordinamento == '')

{
    $query = "SELECT * FROM tbl_logacc
          WHERE comando='Accesso'
          ORDER BY dataacc DESC";
}
else
{
    $query = "SELECT * FROM tbl_logacc
          WHERE comando='Accesso'
          ORDER BY utente,dataacc DESC";
}

$ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));

if (mysqli_num_rows($ris) > 0)
{
    print "<table border=1 align=center>";
    print "<tr class='prima'><td>Utente</td><td>Data</td></tr>";

    while ($lez = mysqli_fetch_array($ris))
    {
        $ute = $lez['utente'];
        $dat = $lez['dataacc'];
        // print "<tr class='oddeven'><td>$cog $nom</td><td>$ann $sez $spe</td><td>$mat</td><td>$alu</td></tr>";   
        // VERIFICO SE LA CATTEDRA E' LEGATA AD UN GRUPPO 
        // PER LEZIONI 'SPECIALI'
        print "<tr class='oddeven'><td>$ute</td><td>$dat</td></tr>";
    }
    print "</table>";
}
else
{
    print '<p>Non ci sono accessi!</p>';
}
mysqli_close($con);
stampa_piede("");

