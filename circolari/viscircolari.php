<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2015 Pietro Tamburrano
  Copyright (C) 2024 Vittorio Lo Mele
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
$idutente = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$titolo = "Visualizza circolari";
$script = "
<style>
.lscontainer {
    margin-left: 10px;
    margin-right: 10px;
}
</style>";
stampa_head_new($titolo, "", $script, "MSAPDTL");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$visualizzabili = array("image/jpeg", "application/pdf", "image/pjpeg", "image/gif", "image/png");


print ("
    <div style='margin-left:5px; margin-right:5px; margin-bottom: 10px;'>
		<table class='table table-striped table-bordered' id='tabelladati' width='100%' >
        <thead>
		<tr class='prima'>
			<td data-priority='1'><b>Circolare</b></td>
			<td data-priority='3' class='not-mobile'><b>Data</b></td>
			
			<td data-priority='2' class=''><b>Lett.</b></td>
			<td data-priority='4' class='not-mobile'><b>Azione</b></td>
			<td data-priority='5' class='not-mobile'><b>Firma</b></td></tr></thead><tbody>");

$dataoggi = date('Y-m-d');
$query = "select tbl_diffusionecircolari.idcircolare,tbl_circolari.iddocumento,ricevuta,tbl_circolari.descrizione,datainserimento, datalettura,dataconfermalettura,docsize,docnome,doctype
			  from tbl_diffusionecircolari,tbl_circolari,tbl_documenti
			  where tbl_diffusionecircolari.idcircolare=tbl_circolari.idcircolare
			  and tbl_circolari.iddocumento=tbl_documenti.iddocumento
			  and tbl_diffusionecircolari.idutente=$idutente
			  and tbl_circolari.datainserimento<='$dataoggi'
			  order by datainserimento desc";

$ris = eseguiQuery($con, $query);

while ($nom = mysqli_fetch_array($ris))
{


    print "<tr><td>" . $nom['descrizione'] .
            "</td><td data-sort='" . $nom['iddocumento']  . "' >" . data_italiana($nom['datainserimento']) .
            "</td><td>";
    // print "TTTT".$nom['datalettura'];
    if ($nom['datalettura'] != "0000-00-00" & $nom['datalettura']!=NULL)
    {
        print ("<i class='bi bi-check-all'></i>");
    }
    print "</td><td>
    <a class='btn btn-outline-secondary btn-sm' href='actions.php?action=download&Id=" . $nom["iddocumento"] . "&Circ=" . $nom["idcircolare"] . "&Ute=" . $idutente . "' target='_blank'>
    <i class='bi bi-save'></i></a> ";

    if (in_array($nom["doctype"], $visualizzabili))
    {
        echo "<a class='btn btn-outline-secondary btn-sm' href='actions.php?action=view&Id=" . $nom["iddocumento"] . "&Circ=" . $nom["idcircolare"] . "&Ute=" . $idutente . "' target='_blank'>";
        echo "<i class='bi bi-search'></i></a>  ";
    }
    print "</td>";
    print "<td>";
    // print "TTTT".$nom['dataconfermalettura'];
    if (($nom['dataconfermalettura'] == '0000-00-00' | $nom['dataconfermalettura'] == '') & $nom['ricevuta'] == 1) // & $nom['datalettura']!="0000-00-00")
    {
        print "<a class='btn btn-outline-secondary btn-sm' href='firmacirc.php?idcircolare=" . $nom['idcircolare'] . "&idutente=$idutente'>
        <i class='bi bi-pencil'></i></a>";
    }
    if (($nom['dataconfermalettura'] != '0000-00-00' & $nom['dataconfermalettura'] != '') & $nom['ricevuta'] == 1)
    {
        print data_italiana($nom['dataconfermalettura']);
    }
    print "</td>";
    print "</tr>";
}

print "</tbody></table></div>";

import_datatables();
?>

<script>
    $(document).ready(function() {
        let table = new DataTable('#tabelladati', {
            responsive: true,
            pageLength: 10,
            scrollX: true,
            order: [[1, 'desc']],
            'language': {
                'search': 'Filtra risultati:',
                'zeroRecords': 'Nessun dato da visualizzare',
                'info': 'Mostrate righe da _START_ a _END_ di _TOTAL_',
                'lengthMenu': 'Visualizzate _MENU_ righe',
                'paginate': {
                    'first': 'Prima',
                    'previous': 'Prec.',
                    'next': 'Succ.',
                    'last': 'Ultima'
                }
            }
        });
    });
</script>

<?php

mysqli_close($con);
stampa_piede_new("");

