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


/*programma per l'inserimento di un avviso
riceve in ingresso iddocente*/
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

$titolo = "Gestione collegamenti web";
$script = '';

if ($editorhtml != '') // Include l'editore html TinyMCE
{
    $script = '<script type="text/javascript" src="../lib/js/tinymce/tinymce.min.js"></script>';
    $script .= '<script>

</script>';
}
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_collegamenti.php'>ELENCO COLLEGAMENTI WEB</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$inizio = stringa_html('inizio');
$fine = stringa_html('fine');
$oggetto = stringa_html('oggetto');
$testo = stringa_html('testo');
$destinatari = is_stringa_html('destinatari') ? stringa_html('destinatari') : array();
$destin = "";
foreach ($destinatari as $d)
    $destin = $destin . $d;

$idavviso = stringa_html('idavviso');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<H1>connessione al server mysql fallita</H1>");
    exit;
}
$DB = true;
if (!$DB)
{
    print("<H1>connessione al database fallita</H1>");
    exit;
}
if ($idavviso == '')
{
    print("<CENTER>");
    print("<FORM NAME='mod' action='ins_collegamenti.php' method='post'>");
    print("<table>");


    print("<tr><td> Descrizione </td>");
    print("<td><input type ='text' maxlength='80' size='80' name='oggetto' value='$oggetto'></td></tr>");
    print("<tr><td> Destinatari</td>");
    print(" <td><select multiple size='4' name='destinatari[]'>");

    if (strpos($destin, "D") === false)
    {
        echo("<option value='D'>Docenti</option>");
    }
    else
    {
        echo("<option value='D' selected>Docenti</option>");
    }

    if (strpos($destin, "S") === false)
    {
        echo("<option value='S'>Staff di presidenza</option>");
    }
    else
    {
        echo("<option value='S' selected>Staff di presidenza</option>");
    }

    if (strpos($destin, "T") === false)
    {
        echo("<option value='T'>Genitori e tutor</option>");
    }
    else
    {
        echo("<option value='T' selected>Genitori e tutor</option>");
    }


    if (strpos($destin, "A") === false)
    {
        echo("<option value='A'>Amministrativi</option>");
    }
    else
    {
        echo("<option value='A' selected>Amministrativi</option>");
    }

    print("</select></td></tr>");

    print("<tr><td> Link</td>");
    print(" <td><input type='text' name='testo'  maxlength='80' size='80' value='$testo'></td></tr>");
    print("</table><br/>");

    print("<INPUT TYPE='SUBMIT' VALUE='Inserisci'>");

    print("</FORM></CENTER>");
}
else
{
    $query = "select * from tbl_collegamenti where idcollegamento=$idavviso";
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    if ($val = mysqli_fetch_array($ris))
    {

        $oggetto = $val['descrizione'];
        $testo = $val['link'];
        $destin = $val['destinatari'];

        print("<CENTER>");
        print("<FORM NAME='mod' action='ins_collegamenti.php' method='post'>");
        print("<table>");


        print("<tr><td> Descrizione </td>");
        print("<td><input type ='text' maxlength='80' size='80' name='oggetto' value='$oggetto'></td></tr>");
        print("<tr><td> Destinatari</td>");
        print(" <td><select multiple size='4' name='destinatari[]'>");

        if (strpos($destin, "D") === false)
        {
            echo("<option value='D'>Docenti</option>");
        }
        else
        {
            echo("<option value='D' selected>Docenti</option>");
        }

        if (strpos($destin, "S") === false)
        {
            echo("<option value='S'>Staff di presidenza</option>");
        }
        else
        {
            echo("<option value='S' selected>Staff di presidenza</option>");
        }

        if (strpos($destin, "T") === false)
        {
            echo("<option value='T'>Genitori e tutor</option>");
        }
        else
        {
            echo("<option value='T' selected>Genitori e tutor</option>");
        }

        if (strpos($destin, "A") === false)
        {
            echo("<option value='A'>Amministrativi</option>");
        }
        else
        {
            echo("<option value='A' selected>Amministrativi</option>");
        }
        print("</select></td></tr>");

        print("<tr><td> Link</td>");
        print(" <td><input type='text' size=80 maxsize='255' name='testo' value='$testo'></td></tr>");
        print("</table><br/>");
        print("<input type='hidden' name='idavviso' value='$idavviso'>");
        print("<INPUT TYPE='SUBMIT' VALUE='Inserisci'>");
        print("</FORM></CENTER>");
    }
}
mysqli_close($con);
stampa_piede("");


