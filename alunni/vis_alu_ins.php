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
session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Inserimento alunno";
$script = "<SCRIPT>             
                        $(document).ready(function(){
				 $('#datacambioclasse').datepicker({ dateFormat: 'dd/mm/yy' });
			});
                        </SCRIPT>";
stampa_head($titolo, "", $script,"MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_alu_cla.php'>Elenco classi</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
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
    $sql = "SELECT idclasse FROM tbl_alunni ";
    $result = mysqli_query($con, inspref($sql));
    if (!($res = mysqli_fetch_array($result)))
    {
        print("Attenzione campo nome mancante.");
    }

    print ("\n<form action='vis_alu_ins_ok.php' method='POST'>");
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
    print ("<tr> <td><i>Numero di telefono</i></td>");
    print ("<td> <input type='text' name='tel' size='30' maxlength='15' value=''> </td> </tr>");
    print ("<tr> <td><i>Numero di cellulare<br><small>(massimo 2 separati eventualmente da virgola)</small> </i></td>");
    print ("<td> <input type='text' name='cel' size='30' maxlength='25' value=''> </td> </tr>");
    print ("<tr> <td><i>Indirizzo E-mail</i> </td>");
    print (" <td> <input type='text' name='mail' size='50' maxlength='100'value=''> </td> </tr>");
    print ("<tr><td>Certificato</td><td><select name='certificato'>");
    print ("<option value='0' selected>No</option><option value='1'>S&igrave;</option>");
    print ("</select></td></tr>");
    print ("<tr> <td><i>Note</i> </td>");
    print (" <td> <input type='text' name='note' size='30' maxlength='50'value=''> </td> </tr>");

    print ("<tr> <td><i>Autorizz. perm. entr. postic.</i> </td>");
    print (" <td> <input type='text' name='autentrata' size='30' maxlength='30'value=''> </td> </tr>");
    print ("<tr> <td><i>Autorizz. perm. usc. antic.</i> </td>");
    print (" <td> <input type='text' name='autuscita' size='30' maxlength='30'value=''> </td> </tr>");

    print ("<tr><td>Autorizz. firma propria</td><td><select name='firmapropria'>");
    print ("<option value='0' selected>No</option><option value='1'>S&igrave;</option>");
    print ("</select></td></tr>");

    print ("<tr> <td><i>Numero registro generale</i> </td>");
    print (" <td> <input type='text' name='numeroregistro' size='20' maxlength='20'value=''> </td> </tr>");
    print ("<tr> <td><i>Provenienza</i> </td>");
    print (" <td> <input type='text' name='provenienza' size='50' maxlength='50'value=''> </td> </tr>");
    print ("<tr> <td><i>Titolo ammissione</i> </td>");
    print (" <td> <input type='text' name='titoloammissione' size='50' maxlength='50'value=''> </td> </tr>");
    print ("<tr> <td><i>Sequenza iscrizione</i> </td>");
   // print (" <td> <input type='number' name='sequenzaiscrizione' size='1' min='1' max='3' step='1'> </td> </tr>");
    print (" <td> <select name='sequenzaiscrizione'><option>1<option>2<option>3</option></select></td> </tr>");

    $sqlc = "SELECT * FROM tbl_classi ORDER BY specializzazione,sezione,anno";
    $resc = mysqli_query($con, inspref($sqlc));
    if (!$resc)
    {
        print ("<br/> <br/> <br/> <h2>Impossibile visualizzare i dati </h2>");
    }
    else
    {
        print ("<tr> <td> <i>Classe</i><B><font color='#CC0000'>*</font></B> </td> <td> <select name='datc'>");
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
        print("<tr bgcolor='lightgrey'><td align='left'>Data inizio frequenza nella classe<br><small><b><span style=\"color: red; \">(Da compilare se l'alunno è arrivato ad A.S. già iniziato,<br>lasciare vuota se la classe è valida da inizio anno.</span>");
        print("</td>");
        print("<td align='left'><input type='text' name='datacambioclasse' id='datacambioclasse' maxlength='10' size='10'></td></tr>"); // TTTTT Da completare

    }


    print (" <tr><td colspan='2' align ='center'> <br/> <input type='submit' value='Inserisci'><br/></td></tr></table></center>");
    print ("</form>\n");
    print ("<form action='vis_alu.php' method='POST'>");
    print ("<p align='center'>");
    print ("<input type='hidden' name='idcla' value='$c'>");
    print ("<input type='submit' value=' << Indietro '><br/>");
    print ("<div align='center'><font color='#CC0000'>*</font> <font size='2'>Campo obbligatorio</font></div>");
    print ("</p></form>");
}
mysqli_close($con);
stampa_piede("");


