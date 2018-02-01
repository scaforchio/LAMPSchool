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

//Visualizzazione classi
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Elenco classi";
$script = "";
stampa_head($titolo, "", $script, "E");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Elenco classi", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<h1> Connessione server fallita </h1>");
}
$db = true;
if (!$db)
{
    print("<h1> Connessione db fallita </h1>");
}


if ($livello_scuola == '2')
{
    $ricercaterze = " AND anno='3' ";
}
else
{
    $ricercaterze = " AND anno='8' ";
}

$query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi
              WHERE 1=1 $ricercaterze
              ORDER BY anno,sezione,specializzazione";


if (!($res = mysqli_query($con, inspref($query))))
{
    print ("Query fallita");
}
else
{


    print "<form method='POST' action='esa_vis_alu.php' name='alunni'>";
    print "<center>";
    print " <select name='idcla' ONCHANGE='alunni.submit()'><option value=''>&nbsp;</option>";
    while ($dati = mysqli_fetch_array($res))
    {
        //print("<tr> <td> <font size='3'> <a href='esa_vis_alu.php?idcla=".$dati['idclasse']."'> ".$dati['anno']." ".$dati['sezione']." ".$dati['specializzazione']." </a> </font> </td> </tr>");
        print("<option value='" . $dati['idclasse'] . "'> " . $dati['anno'] . " " . $dati['sezione'] . " " . $dati['specializzazione'] . "  </option>");
    }
    print "<option value='0'>Senza classe</option>";
    print "</select>";
}
print "</form>";

mysqli_close($con);
stampa_piede("");


