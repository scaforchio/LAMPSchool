<?php

session_start();

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

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

//  istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Report assemblee di classe";
$script = "<script>
            function printPage()
            {
               if (window.print)
                  window.print();
               else
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');
            }
         </script>";
stampa_head($titolo, "", $script, "SP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$iddocente = stringa_html('idutente');
$idclasse = stringa_html('idclasse');
$mese = substr(stringa_html('mese'), 0, 2);



print "<center><br><b>OSSERVAZIONI SUI VERBALI DELLE ASSEMBLEE DI CLASSE</b><br><br>";

//
//   Classi
//
print "<form action='reportassemblee.php' name='classi' method='POST'>";



//
//   Mese
//

print " <center><b>Mese </b><SELECT NAME='mese' ONCHANGE='classi.submit()'><option value=''>&nbsp;";



require '../lib/req_aggiungi_mesi_a_select.php';
echo("</select>");

print ("</form>");

//STAMPO TABELLA IN BASE ALLA CLASSE
if ($mese != "")
{
    $query = "SELECT * FROM tbl_assemblee, tbl_classi where "
            . "tbl_assemblee.idclasse=tbl_classi.idclasse "
            . "and alunnopresidente<>0 and alunnosegretario<>0 ";


    $mm = substr($mese, 0, 2);
    $query .= " AND month(dataassemblea)=$mm";

    $query .= " order by anno, specializzazione, sezione";

    $ris = eseguiQuery($con, $query);
    print "<br/><br/><center><table border ='1' cellpadding='5' class='smallchar'>";

    print "<tr class='prima'>
				<td>Classe</td> 
                                <td>Rappresentanti</td>
				<td>Data</td>
				<td>Insegn.</td>
                                <td>Osservazioni</td>
		   </tr>";
    if (mysqli_num_rows($ris) == 0)
    {
        print "<td colspan='8' align='center'><b><i>Nessuna assemblea da visualizzare</i></b></td>";
    } else
    {
        while ($data = mysqli_fetch_array($ris))
        {

            //CLASSE
            print "<td width=5%>" . substr(decodifica_classe($data['idclasse'], $con), 0, 5) . "</td>";
            //Rappresentanti
            print "<td width=15%>" . estrai_dati_alunno_rid($data['rappresentante1'], $con) . "<br>" . estrai_dati_alunno_rid($data['rappresentante2'], $con) . "</td>";

            print "<td width=10%>" . data_italiana($data['dataassemblea']) . "</td>";
            print "<td width=15%>" . estrai_dati_docente($data['docenteconcedente1'], $con) . "</td>";


            //COMMENTI
            print "<td width=55%>" . /*nl2br*/($data['rapportoperdirigente']) . "</td>";


            print "</tr>";
        }
    }
    print "</table>";
    print "<br>";
    print "<center><img src='../immagini/stampa.png' onClick='printPage();'</center>";
}

mysqli_close($con);
stampa_piede("");

