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

/*Programma per la gestione dell'input di un alunno con parametro in ingresso "idcla"
Imposta i colori dei link*/
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Inserimento alunno";
$script = "";
stampa_head($titolo, "", $script,"E");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='esa_vis_alu_cla.php'>Elenco classi</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
$c = stringa_html('idcla');


$idcomn="";
$idcomr="";


//connessione al server
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<h1> Connessione al server fallita </h1>");
}

//connessione al database
$DB = true;
if (!$DB)
{
    print("<h1> Connessione al database fallita </h1>");
}
else
{
    $sql = "SELECT idclasseesame FROM tbl_alunni ";
    $result = mysqli_query($con, inspref($sql));
    if (!($res = mysqli_fetch_array($result)))
    {
        print("Attenzione campo nome mancante.");
    }

    print ("\n<form action='esa_vis_alu_ins_ok.php' method='POST'>");
    print ("<center>");
    print ("<table> ");
    print ("<tr> <td><i>  Cognome</i><B><font color='#CC0000'>*</font></B> </td> ");
    print ("<td> <input type='text'  name='cognome' size='30' maxlength='30' value=''> </td> </tr>");
    print ("<tr> <td><i>Nome</i><B><font color='#CC0000'>*</font></B> </td> ");
    print ("<td> <input type='text' name='nome' size='30' maxlength='30' value=''> </td> </tr>");
    print ("<tr> <td><i>Codice fiscale <font color='#cc0000'> <b> * </b> </font></i> </td> <td> <input type='text' value='' name='codfiscale' size='16' maxlength='16'> </td> </tr>");

    print ("<tr> <td> <i> Data di nascita</i><B><font color='#CC0000'>*</font></B></td> ");

    print ("<td> <input type='text'  name='gg' size='2'  maxlength='2' value=''> / <input type='text'  name='mm' size='2'  maxlength='2' value=''> / <input type='text' name='aa' size='4'  maxlength='4' value=''>(gg/mm/aaaa) </td> </tr>");
    print   ("<tr> <td> <i>Comune o stato estero di nascita<font color='#cc0000'></font></i> </td> <td> <select name='idcomn'>");
    $sqla = "SELECT * FROM tbl_comuni WHERE statoestero='N' ORDER BY denominazione ";
    $resa = mysqli_query($con, inspref($sqla)) or die ("Errore:" . inspref($sqla));
    if (!$resa)
    {
        print ("<br/> <br/> <br/> <h2>a Impossibile visualizzare i dati </h2>");
    }
    else
    {
        print ("<option value='9999'>");
        print ("<optgroup label='COMUNI ITALIANI'>");

        while ($datal = mysqli_fetch_array($resa))
        {

            if ($idcomn == ($datal['idcomune']))
            {
                print("<option value='" . $datal['idcomune'] . "' selected> " . $datal['denominazione'] . "");
            }
            else
            {
                print("<option value='" . $datal['idcomune'] . "'> " . $datal['denominazione'] . "");
            }

        }
        print ("</optgroup>");
    }

    $sqlb = "SELECT * FROM tbl_comuni WHERE statoestero='S' ORDER BY denominazione";
    $resb = mysqli_query($con, inspref($sqlb));
    if (!$resb)
    {
        print ("<br/> <br/> <br/> <h2>a Impossibile visualizzare i dati </h2>");
    }
    else
    {
        print ("<optgroup label='STATI ESTERI'>");
        while ($datal = mysqli_fetch_array($resb))
        {

            if ($idcomn == ($datal['idcomune']))
            {
                print("<option value='" . $datal['idcomune'] . "' selected> " . $datal['denominazione'] . "");
            }
            else
            {
                print("<option value='" . $datal['idcomune'] . "'> " . $datal['denominazione'] . "");
            }

        }
        print ("</optgroup>");
    }

    print("</select> </td> </tr>");
    print  ("<tr> <td> <i> Indirizzo</i></td>");
    print("<td> <input type='text' name='indirizzo' size='30' maxlength='30' value=''> </td> </tr>");
    mysqli_data_seek($resa, 0); // Ritorna all'inizio del resultset
    $resb = $resa; // Evita di rifare la query sui comuni : mysqli_query($con,inspref($sqlb));
    if (!$resb)
    {
        print ("<br/> <br/> <br/> <h2><b> Impossibile visualizzare i dati </b></h2>");
    }
    else
    {
        print  ("<tr> <td> <i>Comune di residenza<font color='#cc0000'></font></i> </td> <td> <select name='idcomr'>");
        print("<option value='9999'>");
        while ($datbl_ = mysqli_fetch_array($resb))
        {

            if ($idcomr == ($datbl_['idcomune']))
            {
                print("<option value='" . $datbl_['idcomune'] . "' selected> " . $datbl_['denominazione'] . "");
            }
            else
            {
                print("<option value='" . $datbl_['idcomune'] . "'> " . $datbl_['denominazione'] . "");
            }

        }
        print("</select> </td> </tr>");
    }
    print ("<tr> <td><i>Codice SIDI</i></td>");
    print ("<td> <input type='text' name='sidi' size='20' maxlength='20' value=''> </td> </tr>");


    if ($livello_scuola == '2')
    {
        $ricercaterze = " AND anno='3' ";
    }
    else
    {
        $ricercaterze = " AND anno='8' ";
    }

    $sqlc = "SELECT DISTINCT idclasse,anno,sezione,specializzazione FROM tbl_classi
              WHERE 1=1 $ricercaterze
              ORDER BY anno,sezione,specializzazione";




    $resc = mysqli_query($con, inspref($sqlc));
    if (!$resc)
    {
        print ("<br/> <br/> <br/> <h2>Impossibile visualizzare i dati </h2>");
    }
    else
    {
        print ("<tr> <td> <i>Classe d'esame</i><B><font color='#CC0000'>*</font></B> </td> <td> <select name='datc'>");
        print("<option value='0'>");
        while ($datc = mysqli_fetch_array($resc))
        {
            if ($c == ($datc['idclasse']))
            {
                print("<option value='" . $datc['idclasse'] . "' selected>" . $datc['anno'] . " " . $datc['sezione'] . " " . $datc['specializzazione'] . "");
            }
            else
            {
                print("<option value='" . $datc['idclasse'] . "'> " . $datc['anno'] . " " . $datc['sezione'] . " " . $datc['specializzazione'] . "");
            }
        }
        print("</select> </td> </tr>");
    }


    print (" <tr><td colspan='2' align ='center'> <br/> <input type='submit' value='Inserisci'><br/></td></tr></table></center>");
    print ("</form>\n");
    print ("<form action='esa_vis_alu.php' method='POST'>");
    print ("<p align='center'>");
    print ("<input type='hidden' name='idcla' value='$datc'>");
    print ("<input type='submit' value=' << Indietro '><br/>");
    print ("<div align='center'><font color='#CC0000'>*</font> <font size='2'>Campo obbligatorio</font></div>");
    print ("</p></form>");
}
mysqli_close($con);
stampa_piede("");


