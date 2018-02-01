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

// programma per la visualizzazione dei docenti

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
$modo = stringa_html('modo');
$titolo = "Elenco docenti";
$script = "<script type='text/javascript'>
         <!--

                window.onload=function(){
                  nascondi();

               };

               function mostra() {
                      // window.alert('ciao');
                       $('.pwdreset').show();
                       $('#nv').show();
                       $('#mv').hide();
                    }
                 //   document.getElementById('pwdreset').style.display = 'block';



				function nascondi() {
                       // window.alert('ciao2');
                        $('.pwdreset').hide();
                        $('#mv').show();
                       $('#nv').hide();
                    }

				 //   document.getElementById('pwdreset').style.display = 'none';




         //-->
         </script>";

stampa_head($titolo, "", $script, "APMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<H1>connessione al server mysql fallita</H1>");
    exit;
}
$DB = true;
if (!$DB)
{
    print("<H1>connessione al database stage fallita</H1>");
    exit;
}
$sql = "SELECT * FROM tbl_docenti ,tbl_utenti
       WHERE tbl_docenti.iddocente=tbl_utenti.idutente 
       ORDER BY cognome,nome";
$result = mysqli_query($con, inspref($sql));
if (!($result))
{
    print("query fallita");
}
else
{

    print "<div align='right'><br><input type='button' id='mv' value='Attiva reset password' onclick='mostra()' />
                                                  <input type='button' id='nv' value='Disattiva reset password' onclick='nascondi()' />

				   <br><br></div>

				   ";


    print("<FORM NAME='VI2' ACTION='ins_doc.php' method='POST'>");

    if ($modo != 'vis') print("<CENTER><INPUT TYPE='SUBMIT' VALUE='Inserisci nuovo docente'><br><br>");
    print("\n<table border=1>\n");

    print("<tr class='prima'><td>Cognome Nome</td>
		<td>Data di nascita</td>
		<td>Id. Utente</td>
		<td>Telefono</td>
		<td>Email</td>
		<td>Sost.</td>
		<td>Azione</td></tr>\n");

    $w = mysqli_num_rows($result);

    if ($w > 0)
    {
        while ($Data = mysqli_fetch_array($result))
        {
            $cn = $Data['idcomnasc'];
            $sql1 = "SELECT denominazione as den1 FROM tbl_comuni WHERE idcomune=$cn";
            $result1 = mysqli_query($con, inspref($sql1));
            $Data1 = mysqli_fetch_array($result1);
            $cr = $Data['idcomres'];
            $sql2 = "SELECT denominazione as den2 FROM tbl_comuni WHERE idcomune=$cr";

            if ($result2 = mysqli_query($con, inspref($sql2)))
            {
                $Data2 = mysqli_fetch_array($result2);
            }
            if ($Data['tipo'] != "S")
            {
                print("<tr class='oddeven'><td>" . $Data['cognome'] . "  " . $Data['nome'] . "</td>\n");
            }
            else
            {
                print("<tr class='oddeven'><td><b>" . $Data['cognome'] . "  " . $Data['nome'] . "</b></td>\n");
            }

            $d = $Data['datanascita'];
            $gg = substr($d, 8, 2);
            $mm = substr($d, 5, 2);
            $aa = substr($d, 0, 4);
            print("<td align='center'>$gg/$mm/$aa</td>\n");
            print("<td align='center'>" . $Data['userid'] . "</td>");

            if ($Data['telcel'])
            {
                print("<td align='center'>" . $Data['telcel'] . "</td>");
            }
            else
            {
                print("<td align='center'>" . $Data['telefono'] . "</td>");
            }
            print("<td align='center'><a href='mailto:" . $Data['email'] . "'> " . $Data['email'] . "</a></td>\n");
            if ($Data['sostegno'])
            {
                print("<td><img src='../immagini/apply_small.png'></td>");
            }
            else
            {
                print("<td>&nbsp;</td>");
            }

            print "<td align='left'>";
            if ($modo != 'vis')
            {
                print("<a href='mod_doc.php?a=" . $Data['iddocente'] . "'><img src='../immagini/edit.png' title='Modifica'></a>");
                if (poss_canc_doc($Data['iddocente'], $con))
                {
                    print("&nbsp;<a href='can_doc.php?a=" . $Data['iddocente'] . "'><img src='../immagini/delete.png' title='Elimina'></a>");
                }
            }
            print("&nbsp;&nbsp;&nbsp;<a href='../password/rigenera_password_ins_sta_doc.php?iddoc=" . $Data['iddocente'] . "'><img src='../immagini/key.png' title='Rigenera password' class='pwdreset'></a>");

            if ($tipoutente == 'P')
            {
                print("&nbsp;&nbsp;&nbsp;<a href='../contr/cambiautenteok.php?nuovoutente=" . $Data['userid'] . "'><img src='../immagini/alias.png' title='Assumi identità'></a>");
            }
            print("</td></tr>\n");
        }
    }
    else
    {
        print("<tr><td align='center' colspan='6'>Nessun docente trovato</td></tr>");
    }
    print("</TABLE>\n<br/>\n");
    if ($modo != 'vis') print("<INPUT TYPE='SUBMIT' VALUE='Inserisci nuovo docente'>");
}
print("</CENTER></FORM>");


stampa_piede("");
mysqli_close($con);

