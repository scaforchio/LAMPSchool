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

//Programma per la modifica dell'elenco delle tbl_classi

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

$titolo = "Modifica testo";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='testiedit.php'>ELENCO TESTI</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("\n<h1> Connessione al server fallita </h1>");
    exit;
};


//Esecuzione query
$sql = "SELECT * FROM tbl_testi WHERE idtesto=" . stringa_html('idpar');
if (!($ris = mysqli_query($con, inspref($sql))))
{
    print("\n<h1> Query fallita </h1>");
    exit;
}
else
{
    $dati = mysqli_fetch_array($ris);

    $valamm = array();
    $valamm = explode("|", $dati["possibilivalori"]);
    $numval = count($valamm);


    print "<form action='agg_testo.php' method='POST'>";
    print "<input type='hidden' name='idparametro' value='" . $dati['idtesto'] . "'>";
    print "<CENTER><table border='0'>";
    // print "<tr><td ALIGN='CENTER'><b>".$dati['parametro']."</b></td></tr>";
    print "<tr><td ALIGN='CENTER'><b>" . $dati['spiegazione'] . "</b></td></tr>";
    if ($numval == 1)
    {
        print "<tr><td ALIGN='CENTER'><br> <textarea name='valore' rows='5' cols='100'>" . $dati['valore'] . "</textarea></td></tr>";
    }
    else
    {
        print "<tr><td align='center'><select name='valore'>";
        for ($i = 0; $i < $numval; $i++)
        {
            print "<option value='" . $valamm[$i] . "' ";
            if ($valamm[$i] == $dati['valore'])
            {
                print "selected";
            }
            print ">" . $valamm[$i] . "</option>";
        }
        print "</select></td></tr>";
    }
    print "<tr><td ALIGN='CENTER'><br> <input type='hidden' name='idpar' value='" . $dati['idtesto'] . "'></td></tr>";
    print "<tr><td ALIGN='CENTER'> <input type='submit' value='REGISTRA'></td></tr>";
    print "</table></CENTER></form>";
}
stampa_piede("");
mysqli_close($con);


