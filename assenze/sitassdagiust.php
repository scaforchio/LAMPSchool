<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2015 Pietro Tamburrano
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma é distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */



@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Inserimento giustificazioni assenze";
$script = "<script type='text/javascript'>
 <!--
  var stile = 'top=10, left=10, width=600, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
     function Popup(apri) {
        window.open(apri, '', stile);
     }
 //-->
</script>";
stampa_head($titolo, "", $script, "MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$idalunno = stringa_html('idalunno');
$idclasse = stringa_html('idclasse');
$data = stringa_html('data');


if (($idalunno != ""))
{

    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

    $datialunno = estrai_dati_alunno($idalunno, $con);
    print "<center>Assenze dell'alunno $datialunno</center>";
    $query = 'select * from tbl_assenze where idalunno="' . $idalunno . '" and (isnull(giustifica) or giustifica=0) order by data ';
    $ris = eseguiQuery($con, $query);
    if (mysqli_num_rows($ris) > 0)
    {
        print "<form name='giustass' method='post' action='insgiust.php'>";
        print "<table align='center' border='1'>
			   <tr class='prima'>
				   <td align='center'>Data assenza</td>
				   <td align='center'>Giustifica</td>
			   </tr>
		   ";
        while ($val = mysqli_fetch_array($ris))
        {
            print "<tr><td align='center'>" . data_italiana($val['data']) . "</td><td align='center'><input type=checkbox name='giu" . $val['idassenza'] . "'></td></tr>";
        }

        print "<input type='hidden' name='idalunno' value='$idalunno'>";
        print "<input type='hidden' name='idclasse' value='$idclasse'>";
        print "<input type='hidden' name='data' value='$data'>";
        print "</table><center><input type=submit value='Registra giustificazioni'></center></form>";
    }
}
// fine if

mysqli_close($con);
stampa_piede("");

