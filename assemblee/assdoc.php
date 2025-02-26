<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2015 Pietro Tamburrano
  Copyright (C) 2025 Michele Sacco - Flowopia Network [Rielaborazione sezione assemblee per adeguamento nuova UI]
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

//
//    VISUALIZZAZIONE E CONCESSIONE
//	  DELLE ASSEMBLEE DI CLASSE PER I DOCENTI
//


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

//  istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Assemblee proprie classi";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=800, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head_new($titolo, "", $script, "SD");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$iddocente = stringa_html('iddocente');
$iddocente = $_SESSION['idutente'];
//query per selezionare assemblee da concedere riferite al docente collegato
$asses = "SELECT * FROM tbl_assemblee 
		  WHERE ((docenteconcedente1=$iddocente AND concesso1=0)
                        OR (docenteconcedente2=$iddocente AND concesso2=0))
                        AND (rappresentante1<>0 and rappresentante2<>0)";
$ris1 = eseguiQuery($con, $asses);

//ELENCO RICHIESTE ASSEMBLEE DA CONCEDERE
print ("
        <div>
            <h5 align='center'>Richieste da concedere</h5>
            <table class='table table-striped table-bordered' width='100%'>
                <thead><tr class='prima'>
                    <th colspan=3 align=center width=60%>RICHIESTA</th>
                    <th colspan=1 align=center width=40%>CONCESSIONE</th>
                </tr></thead>
                <tr class='prima'>
                    <td>Classe e Data</td> 
                    <td>Richiedenti</td>
                    <td>Ordine del Giorno </td>
                    <td>Concessione</td>
                </tr>
    ");
if (mysqli_num_rows($ris1) == 0){
    print "<td colspan='4' align='center'><b><i>Nessuna assemblea da concedere</i></b></td>";
}else{
    while ($dataass = mysqli_fetch_array($ris1))
    {
        $idassemblea = $dataass['idassemblea'];
        // Flag di controllo se assemblea è stata già concessa
        if ($dataass['docenteconcedente1'] == $iddocente)
        {
            $controllo = $dataass['concesso1'];
        }elseif($dataass['docenteconcedente2'] == $iddocente)
        {
            $controllo = $dataass['concesso2'];
        }

        if ($controllo == 0)
        {
            print "<tr>";
            // Stampa classe
            print "<td><i class='bi bi-people-fill'></i> <b>" . decodifica_classe($dataass['idclasse'], $con, 1) . "</b><br>";
            // Stampa data richiesta
            print "<i class='bi bi-calendar-plus'></i> " . data_italiana($dataass['datarichiesta']) . "<br>";
            // Stampa data assemblea
            print "<i class='bi bi-calendar-check'></i> " . data_italiana($dataass['dataassemblea']) . "<br>";
            // Stampa ora inizio e fine
            print "<i class='bi bi-clock'></i> " . $dataass['orainizio'] . " - " . $dataass['orafine'] . "</td>";
            // Stampa rappresentanti
            $alu = "SELECT cognome,nome FROM tbl_alunni 
					WHERE idalunno=" . $dataass['rappresentante1'] . "
					OR idalunno=" . $dataass['rappresentante2'] . "
					ORDER BY cognome";
            $risalu = eseguiQuery($con, $alu);
            print "<td>";
            while ($dataalu = mysqli_fetch_array($risalu))
            {
                print ($dataalu['cognome'] . "&nbsp;" . $dataalu['nome'] . "<br/>");
            }
            print "</td>";
            // Stampa ordine del giorno
            print "<td>" . nl2br($dataass['odg']) . "</td>";
            // Pulsanti per concessione o negazione
            print "<td align='center'>
                   <a tabindex='0' class='btn btn-outline-success' href='registra_concessione.php?idassemblea=$idassemblea&concesso=1' role='button' data-bs-toggle='popover' data-bs-trigger='hover' data-bs-content='Conferma concessione'>CONCEDI</a>  
                   <a tabindex='0' class='btn btn-outline-danger' href='registra_concessione.php?idassemblea=$idassemblea&concesso=0' role='button' data-bs-toggle='popover' data-bs-trigger='hover' data-bs-content='Nega concessione'>NEGA</a>
                   </td>";
            print "</tr>";
        }
    }
}
print "</table>";
print("<hr/>");
//ELENCO ASSEMBLEE CONCESSE
print ("
        <div>
            <h5 align='center' class='mt-2'>Assemblee concesse</h5>
            <table class='table table-striped table-bordered' id='assemblee' width='100%'>
                <thead><tr class='prima'>
                    <td data-priority='1'>Classe e data</td> 
                    <td data-priority='2'>Richiedenti</td>
                    <td data-priority='3' class='not-mobile'>Ordine del Giorno</td>
                    <td data-priority='4' class='not-mobile'>Verbale</td>
                </tr></thead>
    ");
// Query
$ris2 = eseguiQuery($con, "SELECT * FROM tbl_assemblee WHERE ((docenteconcedente1=$iddocente AND concesso1=1) OR (docenteconcedente2=$iddocente AND concesso2=1))");
if (mysqli_num_rows($ris2) == 0){
    print "<td colspan='4' align='center'><b><i>Nessuna assemblea concessa</i></b></td>";
}else{
    while ($dataass = mysqli_fetch_array($ris2))
    {
        if ($dataass['docenteconcedente1'] == $iddocente){
            $controllo = $dataass['concesso1'];
        }elseif ($dataass['docenteconcedente2'] == $iddocente){
            $controllo = $dataass['concesso2'];
        }
        if ($controllo == 1){
            print "<tr>";
            // Stampa classe
            print "<td><i class='bi bi-people-fill'></i><b> " . decodifica_classe($dataass['idclasse'], $con, 1) . "</b><br>";
            // Stampa data richiesta
            print "<i class='bi bi-calendar-plus'></i> " . data_italiana($dataass['datarichiesta']) . "<br>";
            // Stampa data assemblea
            print "<i class='bi bi-calendar-check'></i> " . data_italiana($dataass['dataassemblea']) . "<br>";
            // Stampa ora di inizio e fine
            print "<i class='bi bi-clock'></i> " . $dataass['orainizio'] . " - " . $dataass['orafine'] . "</td>";
            // Stampa nome rappresentanti
            $risalu = eseguiQuery($con, "SELECT cognome,nome FROM tbl_alunni WHERE idalunno=" . $dataass['rappresentante1'] . " OR idalunno=" . $dataass['rappresentante2'] . " ORDER BY cognome");
            print "<td>";
            while ($dataalu = mysqli_fetch_array($risalu)){
                print ($dataalu['cognome'] . "&nbsp;" . $dataalu['nome'] . "<br/>");
            }
            print "</td>";
            // Stampa ordine del giorno
            print "<td>" . nl2br($dataass['odg']) . "</td>";
            // Stampa verbale se presente
            if($dataass['verbale'] != ""){
                // Check esame verbale staff dirigenza
                if($dataass['visione_verbale'] == 1){
                    $visverb = "-- <br><b>ESAME VERBALE</b><br>" .$dataass['commenti_verbale'] ."<br><b><i>" .estrai_dati_docente($dataass['docente_visione'], $con) ."</i></b>";
                }else{
                    $visverb = "";
                }
                // Modal visualizzazione verbale
                print ("
                    <div class='modal fade' id='modalAss$idassemblea' tabindex='-1' aria-labelledby='modalAssLabel$idassemblea' aria-hidden='true'>
                        <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                            <h1 class='modal-title fs-5' id='modalAssLabel$idassemblea'>Visualizza Verbale</h1>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                " .nl2br($dataass['verbale']) ."<br> Ora Termine: " .substr($dataass['oratermine'], 0, 5) ."<br>
                                SEGRETARIO: " .estrai_dati_alunno_rid($dataass['alunnosegretario'], $con) ."<br>
                                PRESIDENTE: " .estrai_dati_alunno_rid($dataass['alunnopresidente'], $con) ."<br>
                                $visverb
                            </div>
                            <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Chiudi</button>
                            </div>
                        </div>
                        </div>
                    </div>"
                    );
                print("<td align='center'><button type='button' class='btn btn-outline-primary' data-bs-toggle='modal' data-bs-target='#modalAss$idassemblea'><i class='bi bi-eye-fill'> Visualizza</i></button></td>");
            }else{
                print "<td align='center'>
                        <span class='d-inline-block' tabindex='0' data-bs-toggle='popover' data-bs-trigger='hover focus' data-bs-content='Verbale non presente!'>
                            <button class='btn btn-secondary btn-sm' type='button' disabled><i class='bi bi-eye-fill'> Visualizza </i></button>
                        </span>
                       </td>";
            }
            print "</tr>";
        }
    }
}
print "</table>";
import_datatables();
?>

<script>
    $(document).ready(function() {
        let table = new DataTable('#assemblee', {
            responsive: true,
            scrollX: true,
            searching: false,
            paging: false,
            info: false,
        });
    });
</script>

<?php

stampa_piede_new("");
mysqli_close($con);
