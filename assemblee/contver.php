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


$titolo = "Verifica verbali assemblee di classe";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=800, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head($titolo, "", $script, "SP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$iddocente = $_SESSION['idutente'];
$idclasse = stringa_html('idclasse');

//
//   Classi
//

//STAMPO TABELLA IN BASE ALLA CLASSE

$query = "SELECT * FROM tbl_assemblee WHERE consegna_verbale=1 and visione_verbale=0";
$ris = mysqli_query($con, inspref($query)) or die("Errore durante la connessione: " . mysqli_error($con) . "<br/>" . $query);
print "<br/><br/><center><table border ='1' cellpadding='5'>";

print "<tr class='prima'>
                                <td>Assemblea</td> 
				<td>Docenti concedenti</td>
				<td>Docente autorizzante</td>
				<td>Verbale</td>
				<td>Risposta a verbale</td>
		   </tr>";
if (mysqli_num_rows($ris) == 0)
{
    print "<td colspan='5' align='center'><b><i>Nessun verbale da visionare</i></b></td>";
}
else
{
    while ($data = mysqli_fetch_array($ris))
    {
        print "<form action='registra_visione.php' method='POST'>";
        
        // CLASSE
        print "<td align='center'>" . decodifica_classe($data['idclasse'],$con);
        //DATA RICHIESTA
        print "<br>" . data_italiana($data['dataassemblea']) . "</td>";

        //DOCENTI CONCEDENTI
        $doc = "SELECT cognome,nome FROM tbl_docenti WHERE iddocente=" . $data['docenteconcedente1'];
        if ($data['docenteconcedente2'] != 0)
        {
            $doc .= " OR iddocente=" . $data['docenteconcedente2'] . " ORDER BY cognome";
        }
        print "<td>";
        $risdoc = mysqli_query($con, inspref($doc));
        while ($datadoc = mysqli_fetch_array($risdoc))
        {
            print ($datadoc['cognome'] . "&nbsp;" . $datadoc['nome'] . "<br/>");
        }
        print "</td>";

        //DOCENTE AUTORIZZANTE (se esiste)
        $doc = "SELECT cognome,nome FROM tbl_docenti WHERE iddocente=" . $data['docenteautorizzante'];
        $risdoc = mysqli_query($con, inspref($doc));
        $datadoc = mysqli_fetch_array($risdoc);
        print "<td>" . $datadoc['cognome'] . "&nbsp;" . $datadoc['nome'] . "</td>";

        //VERBALE
        if ($data['consegna_verbale'] == 0)
        {
            print "<td align='center'><img src='../immagini/red_cross.gif'></td>";
        }
        else
        {
            print "<td>" . nl2br($data['verbale']) . "</td>";
        }

        //COMMENTI
        print "<td><p align='center'><textarea cols=20 rows=5 name='commenti' WRAP='PHYSICAL'></textarea></p></td>";

        //BOTTONE INVIO
        print "<td><input type='submit' value='Registra visione'>";
        
        print "</td>";
        print "<input type='hidden' name='idassemblea' value='" . $data['idassemblea'] . "'>";
        print "<input type='hidden' name='iddocente' value='" . $iddocente . "'>";
        print "</tr></form>";
    }
}


print "</table>";

mysqli_close($con);
stampa_piede("");
