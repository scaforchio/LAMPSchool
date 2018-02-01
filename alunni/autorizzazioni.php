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
// require_once '../lib/ db / query.php';

//$lQuery = LQuery::getIstanza();

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$filtro = stringa_html('filtro');


$titolo = "Situazione totale deroghe e autorizzazioni";
$script = "";
stampa_head($titolo, "", $script, "MSP");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");



print "<form action='autorizzazioni.php' method='POST'>";
print "<br><center><B>Filtro: </B><input type='text' name='filtro' value ='$filtro'><br></center><br>";
print "</form>";
// print "<center><B>AUTORIZZAZIONI, ESONERI E DEROGHE</B><br></center>";
// prelevamento dati alunno
// $rs = $lQuery->selectstar('tbl_alunni', 'idalunno=?', array($codalunno));
$query = "select * from tbl_alunni where not autorizzazioni is null and autorizzazioni <> '' and autorizzazioni like '%$filtro%' order by cognome, nome, datanascita";
$rs = mysqli_query($con, inspref($query));
if (mysqli_num_rows($rs)>0)
{
    print "<table border=1 align='center'><tr class='prima'><td>Alunno</td><td>Autorizz. o deroga</td></tr>";
    while ($rec = mysqli_fetch_array($rs))
    {
        print "<tr>";
        print "<td>" . $rec['cognome'] . " " . $rec['nome'] . " (" . data_italiana($rec['datanascita']) . ") - " . decodifica_classe(estrai_classe_alunno($rec['idalunno'], $con), $con) . "</td>";
        // print "<pre>".$rec['autorizzazioni']."</pre><br>";
        print "<td>" . nl2br($rec['autorizzazioni']) . "</td>";
        print "</tr>";

    }
    print "</table>";
}
else
    print "<BR><br><b><i><center>Niente da visualizzare!</b></i></center>";
mysqli_close($con);
stampa_piede();
