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

/*Programma per la modifica delle tbl_materie.*/

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Modifica ordinamento materia";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='ordmaterie.php'>ELENCO MATERIE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

print("<br/><br/>");
//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    die("\n<h1> Connessione al server fallita </h1>");
}

//Connessione al database
$DB = true;
if (!$DB)
{
    die("\n<h1> Connessione al database fallita </h1>");
}

//Esecuzione query
$idmateria = stringa_get_html('idmat');

$sql = "select * from tbl_materie where idmateria=$idmateria";
if (!($ris = mysqli_query($con, inspref($sql))))
{
    die("\n<h1> Query fallita </h1>");
}
else
{
    $dati = mysqli_fetch_array($ris);
    print "<form action='agg_ord_mat.php' method='POST'>";
    print "<input type='hidden' name='idmateria' value='" . $dati['idmateria'] . "'>";
    print "<CENTER><table border='0'>";
    print "<tr><td> Materia&nbsp;</td> <td> ";
    print "<b>" . $dati['denominazione'] . " </b>";
    print "</td></tr>";


    print "<tr> <td>Ordinamento materia</td><td>
           <select name ='progrpag'>";

    for ($i = 1; $i <= 25; $i++)
    {
        if ($i == $dati['progrpag'])
        {
            print "<option value='$i' selected>$i";
        }
        else
        {
            print "<option value='$i'>$i";
        }
    }
    if (99 == $dati['progrpag'])
    {
        print "<option value='99' selected>Scheda separata";
    }
    else
    {
        print "<option value='99'>Scheda separata";
    }
    if (100 == $dati['progrpag'])
    {
        print "<option value='100' selected>Non in tabellone";
    }
    else
    {
        print "<option value='100'>Non in tabellone";
    }


    echo "
           </select>";

    print "</td> </tr>";

    print "<tr> <td>Valida per media</td><td>
           <select name ='media'>";

    if ($dati['media'])
    {
        print "<option value='1' selected>Sì";
        print "<option value='0'>No";
    }
    else
    {
        print "<option value='1'>Sì";
        print "<option value='0' selected>No";
    }


    print "</td> </tr>";

    print "<tr>";
    print "<td COLSPAN='2' ALIGN='CENTER'><br/><input type='submit' value='Aggiorna'></td> ";
    print "</form>";
    print "<TR><TD COLSPAN='2'>&nbsp;</TD></TR>";

    print "</table></CENTER>";
}
mysqli_close($con);
stampa_piede("");

