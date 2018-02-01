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
stampa_head($titolo, "", $script, "MASP");
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
$sql = "SELECT * FROM tbl_classi ORDER BY specializzazione, sezione, anno";
if (!($res = mysqli_query($con, inspref($sql))))
{
    print ("Query fallita");
}
else
{

    print "<center><a href='vis_alu_ins.php'>Inserisci nuovo alunno senza classe</a></center><br/><br/>";
    print "<form method='POST' action='vis_alu.php' name='alunni'>";
    print "<center>";
    print " <select name='idcla' ONCHANGE='alunni.submit()'><option value=''>&nbsp;</option>";
    while ($dati = mysqli_fetch_array($res))
    {
        //print("<tr> <td> <font size='3'> <a href='vis_alu.php?idcla=".$dati['idclasse']."'> ".$dati['anno']." ".$dati['sezione']." ".$dati['specializzazione']." </a> </font> </td> </tr>");
        print("<option value='" . $dati['idclasse'] . "'> " . $dati['anno'] . " " . $dati['sezione'] . " " . $dati['specializzazione'] . "  </option>");
    }
    print "<option value='0'>Senza classe</option>";
    print "</select>";
}
print "</form>";

mysqli_close($con);
stampa_piede("");


