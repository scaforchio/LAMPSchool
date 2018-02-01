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

/*programma per la visualizzazione di un componente scelto di una classe con parametro in 
  ingresso "idcla" e parametro in uscita "idal" */
//connessione al server
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

$titolo = "Elenco alunni di una classe";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }



         //-->
         </script>
         <script type='text/javascript'>
         <!--

                window.onload=function(){
                  nascondi();
                  nascondialu();

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


                    function mostraalu() {
                      // window.alert('ciao');
                       $('.pwdresetalu').show();
                       $('#nva').show();
                       $('#mva').hide();
                    }
                 //   document.getElementById('pwdreset').style.display = 'block';



				function nascondialu() {
                       // window.alert('ciao2');
                        $('.pwdresetalu').hide();
                        $('#mva').show();
                       $('#nva').hide();
                    }
				 //   document.getElementById('pwdreset').style.display = 'none';


        //-->
         </script>";
stampa_head($titolo, "", $script, "MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_alu_cla.php'>Elenco classi</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$n = stringa_html('idcla');
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
$sq = "SELECT * FROM tbl_classi
       WHERE idclasse='$n' ";
$res = mysqli_query($con, inspref($sq));
$dati1 = mysqli_fetch_array($res);

//imposta la tabella del titolo
print("<table width='100%'>
		<tr>
		   <td align ='center' bgcolor='white'><strong><font size='+1'>Alunni della " . $dati1['anno'] . " " . $dati1['sezione'] . " " . $dati1['specializzazione'] . "</font></strong></td>
		</tr>
		</table> <br/><br/>");
$sql = "SELECT * FROM tbl_alunni,tbl_utenti
       WHERE tbl_alunni.idalunno=tbl_utenti.idutente 
       AND idclasse='$n'ORDER BY cognome,nome";
$result = mysqli_query($con, inspref($sql));

print "<div align='right'><br><input type='button' id='mv' value='Attiva reset password tutor' onclick='mostra()' />
                          <br><input type='button' id='mva' value='Attiva reset password alunni' onclick='mostraalu()' />
                           <input type='button' id='nv' value='Disattiva reset password tutor' onclick='nascondi()' />
                           <input type='button' id='nva' value='Disattiva reset password alunni' onclick='nascondialu()' />

				   <br><br></div>

				   ";

print"<center>";
print("<table border=1>");
print("<tr class='prima'>");
print("<td align='center'><b> N.</b> </td>");
print("<td align='center'><b> Cognome</b> </td>");
print("<td align='center'><b> Nome</b> </td>");
print("<td align='center'> <b>Data di Nascita </b></td>");
print("<td align='center'><b>Id. Utente</b> </td>");
print("<td align='center'><b>Telefono</b> </td>");
print("<td align='center' ><b> E-mail</b> </td>");
print("<td align='center' ><b> Cert.</b> </td>");
print("<td align='center' ><b> Note</b> </td>");
print("<td align='center'><b> Azione </b></td>");
print ("</tr>");
if (!(mysqli_num_rows($result) > 0))
{
    print("<tr bgcolor='#cccccc'><td colspan='7'><center><b>Nessun alunno presente</b></td></tr>");
}
else
{
    $contatore=0;
    while ($dati = mysqli_fetch_array($result))
    {
        $contatore++;
        //comunicazione tra le tabelle tbl_alunni,tbl_comuni,tbl_tutori per il passaggio dei valori
        print("<tr class='oddeven'>");
        print("<td>$contatore</td>");
        print("<td>" . $dati['cognome'] . "</td><td>" . $dati['nome'] . "</td>");

        print("<td>" . data_italiana($dati['datanascita']) . "</td>");
        print("<td>" . $dati['userid'] . "</td>");
        if ($dati['telefono'])
        {
            print("<td>" . $dati['telefono'] . "</td>");
        }
        else
        {
            print("<td>" . $dati['telcel'] . "</td>");
        }
        print("<td><a href='MAILTO:" . $dati['email'] . "'>" . $dati['email'] . "</A></td>");
        if ($dati['certificato'])
        {
            print("<td><img src='../immagini/apply_small.png'></td>");
        }
        else
        {
            print("<td>&nbsp;</td>");
        }
        print("<td>" . $dati['note'] . "</td>");

        print("<td><a href='vis_alu_mod.php?idal=" . $dati['idalunno'] . "'><img src='../immagini/modifica.png' title='Modifica'></a>");
        print "&nbsp;&nbsp;&nbsp;";
        if (poss_canc_alu($dati['idalunno'], $con))
        {
            print ("<a href='alu_conf.php?idal=" . $dati['idalunno'] . "?idcla=" . $dati['idclasse'] . "'><img src='../immagini/delete.png' title='Elimina'></a>");
        }
        else
        {
            print ("&nbsp;");
        }
        print("&nbsp;&nbsp;&nbsp;<a href='../password/rigenera_password_ins_sta.php?idalu=" . $dati['idalunno'] . "'><img src='../immagini/key.png' title='Rigenera password tutor'  class='pwdreset'></a>");
         print("&nbsp;&nbsp;&nbsp;<a href='../password/alu_rigenera_password_ins_sta.php?idalu=" . $dati['idalunno'] . "'><img src='../immagini/key.png' title='Rigenera password alunni'  class='pwdresetalu'></a>");
        if ($tipoutente == 'P')
        {
            print("&nbsp;&nbsp;&nbsp;<a href='../contr/cambiautenteok.php?nuovoutente=" . $dati['userid'] . "'><img src='../immagini/alias.png' title='Assumi identità tutor'></a>");
        }

        print "</td>";
        print "</tr>";
    }

}
print("</table><br/>");
print("<center>");
print "<form action='vis_alu_ins.php' method='POST'>";
print("<input type='hidden' name='idcla' value=$n>");
print("<input type ='submit' value='Inserimento'><br/>");
print "</form>";
print "<a href='vis_alu_cla.php'>";

print ("<br/>Elenco classi");
print ("</a>");

print"<br/><center><a href=javascript:Popup('vis_alu_stampa.php?idcla=$n')><img src='../immagini/stampa.png'></a><br/><br/>";

mysqli_close($con);
stampa_piede("");
    

