<?php

require_once '../lib/req_apertura_sessione.php';

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

/* programma per la visualizzazione di un componente scelto di una classe con parametro in 
  ingresso "idcla" e parametro in uscita "idal" */
//connessione al server

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
}

$titolo = "Elenco alunni con filtro su cognome e nome";
$script = "";
stampa_head($titolo, "", $script, "MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> -  $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$strcogn = trim(stringa_html('strcogn'));
$strnome = trim(stringa_html('strnome'));
$codice = trim(stringa_html('codice'));


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<h1>Connessione al server fallita</h1>");
}
$db = true;
if (!$db)
{
    print"<h1>Connessione nel database fallita</h1>";
}
print "<form action='vis_alu_ricerca.php' method='POST'>
        <table align='center'>
           <tr>
               <td>Cognome: <input type='text' name='strcogn' value='$strcogn'></td>
               <td>Nome: <input type='text' name='strnome' value='$strnome'></td>
               <td>Codice: <input type='text' name='codice' value='$codice'></td>
               <td colspan=2><input type='submit' value='CERCA'></td>
           </tr>
        </table>
        </form>";


if (strlen($strcogn) > 1 | strlen($strnome) > 1 | strlen($codice))
{
//imposta la tabella del titolo
    print("<table width='100%'>
		<tr>
		   <td align ='center' bgcolor='white'><strong><font size='+1'>Alunni trovati</td>
		</tr>
		</table> <br/><br/>");
    if ($codice == '')
        $sql = "SELECT * FROM tbl_alunni,tbl_utenti
            WHERE tbl_alunni.idalunno=tbl_utenti.idutente
            AND cognome LIKE '%$strcogn%' AND nome LIKE '%$strnome%'
            ORDER BY cognome,nome";
    else
        $sql = "SELECT * FROM tbl_alunni,tbl_utenti
            WHERE tbl_alunni.idalunno=tbl_utenti.idutente
            AND idalunno=$codice
            ORDER BY cognome,nome";
    $result = eseguiQuery($con, $sql);
    print"<center>";
    print("<table border=1>");
    print("<tr class='prima'>");
    print("<td align='center'><b> Cognome</b> </td>");
    print("<td align='center'><b> Nome</b> </td>");
    print("<td align='center'> <b>Data di Nascita </b></td>");
    print("<td align='center'><b>Id. Utente</b> </td>");
    print("<td align='center'><b>Classe</b> </td>");
    print("<td align='center' ><b> E-mail</b> </td>");
    print("<td align='center' ><b> Maggiorenne</b> </td>");
    print("<td align='center' ><b> Censito</b> </td>");
    print("<td align='center' ><b> Cert.</b> </td>");
    print("<td align='center' ><b> Note</b> </td>");
    print("<td align='center'><b> Azione </b></td>");
    print ("</tr>");
    if (!(mysqli_num_rows($result) > 0))
    {
        print("<tr bgcolor='#cccccc'><td colspan='9'><center><b>Nessun alunno presente</b></td></tr>");
    } else
    {
        while ($dati = mysqli_fetch_array($result))
        {
            //comunicazione tra le tabelle tbl_alunni,tbl_comuni,tbl_tutori per il passaggio dei valori
            print("<tr class='oddeven'>");
            print("<td>" . $dati['cognome'] . "</td><td>" . $dati['nome'] . "</td>");

            print("<td>" . data_italiana($dati['datanascita']) . "</td>");
            print("<td>" . $dati['userid'] . "</td>");
            print("<td>" . decodifica_classe($dati['idclasse'], $con) . "</td>");
            print("<td><a href='MAILTO:" . $dati['email'] . "'>" . $dati['email'] . "</A></td>");
            print(maggiorenneok($dati["datanascita"]));
            print(censito($dati["datanascita"], $dati["censito"]));
            print("<td>");
            if ($dati['certificato'])
            {
                print("<img src='../immagini/apply_small.png'>");
            }
            print("</td>");
            print("<td>" . $dati['note'] . "</td>");
            print("<td><a href='vis_alu_mod.php?idal=" . $dati['idalunno'] . "&strcogn=$strcogn&strnome=$strnome'><img src='../immagini/modifica.png'></a>&nbsp;&nbsp;");
            if (poss_canc_alu($dati['idalunno'], $con))
            {
                print ("<a href='alu_conf.php?idal=" . $dati['idalunno'] . "&idcla=" . $dati['idclasse'] . "'><img src='../immagini/delete.png'></a>");
            }
            print("<a href='../password/rigenera_password_ins_sta.php?idalu=" . $dati['idalunno'] . "'>Rig. password tutor</a>&nbsp;<a href='../password/alu_rigenera_password_ins_sta.php?idalu=" . $dati['idalunno'] . "'>Rig. password alunno</a>");
            print("&nbsp;&nbsp;&nbsp;<a target='_blank' href='../alunni/genassotp.php?idalu=" . $dati['idalunno'] . "'><img src='../immagini/key.png' title='Rigenera OTP tutor'></a>");
            print("&nbsp;<a target='_blank' href='../alunni/prefcens.php?idalu=" . $dati['idalunno'] . "'><img src='../immagini/ci.png' width=22 height=22 title='Preferenze Censimento'></a>");
            print("&nbsp;&nbsp;<a href='#' onclick='barcode(`" . strtoupper($_SESSION['suffisso']) . $dati['idalunno'] . "`)'><img src='../immagini/card.png' width=22 height=22 title='Codice a barre'></a>");
            if ($tipoutente == 'M')
            {
                print("<a href='../contr/cambiautenteok.php?nuovoutente=" . $dati['userid'] . "'><img src='../immagini/alias.png' title='Assumi identità tutor'></a>");
            }
            print("</td>");
            print("</tr>");
        }
    }
    print("</table><br/>");

    print("<center>");
    print "<form action='vis_alu_ins.php' method='POST'>";

    print("<input type='hidden' name='strcogn' value=$strcogn>");
    print("<input type='hidden' name='strnome' value=$strnome>");
    print("<input type ='submit' value='Inserimento'><br/>");
    print "</form>";
    print "<a href='vis_alu_cla.php'>";

    print ("<br/>Elenco classi");
    print ("</a>");
}
mysqli_close($con);
stampa_piede("");


