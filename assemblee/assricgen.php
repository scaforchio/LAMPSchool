<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2015 Pietro Tamburrano
  Copyright (C) 2023 Michele Sacco - Flowopia Network [Rielaborazione sezione assemblee per adeguamento nuova UI]
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
//    VISUALIZZAZIONE DELLE ASSEMBLEE DI CLASSE PER GLI ALUNNI
//	  E
//	  RICHIESTA DI ASSEMBLEE DI CLASSE PER GLI ALUNNI 
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


$titolo = "Assemblee di classe";
stampa_head_new($titolo, "", $script, "L");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$idalunno = $_SESSION['idstudente'];
$idclasse = estrai_classe_alunno($idalunno, $con);

$query = "select * from tbl_classi where rappresentante1=$idalunno or rappresentante2=$idalunno";
$riscontr = eseguiQuery($con, $query);
if (mysqli_num_rows($riscontr) != 0)
{
    $alurapp = true;
} else
{
    $alurapp = false;
}

$queryass = "SELECT * FROM tbl_assemblee 
			 WHERE idclasse = $idclasse
			 ORDER BY idassemblea DESC";
$risass = eseguiQuery($con, $queryass);
if (mysqli_num_rows($risass) == 0)
{
    alert("Non hai richiesto/effettuato ancora nessuna assemblea");
} else {
    // Titolo della pagina
    $classe = "SELECT anno,sezione,specializzazione FROM tbl_classi WHERE idclasse=$idclasse";
    $risclasse = eseguiQuery($con, $classe);
    $val = mysqli_fetch_array($risclasse);
    print "<center><b>Riepilogo assemblee " . $val['anno'] . $val['sezione'] . "&nbsp;" . $val['specializzazione'] . "</b></center><br/>";

    // Inizio Tabella assemblee
    print ("
        <div>
            <table class='table table-striped table-bordered' width='100%'>
                <thead><tr class='prima'>
                    <th colspan=3 align=center width=60%>RICHIESTA</th>
                    <th colspan=1 align=center width=40%>SVOLGIMENTO & ESITO</th>
                </tr></thead>
                <tr class='prima'>
                    <td>Data</td> 
                    <td>Info</td>
                    <td>Esito</td>
                    <td>Verbale</td>
                </tr>
    ");

    while($dataass = mysqli_fetch_array($risass)){
        $idassemblea = $dataass['idassemblea'];
        print "<tr>";
        // sezione RICHIESTA
        // Data
            // Richiesta
            print ("<td align='center'>Richiesta il <i>" . data_italiana($dataass['datarichiesta']) . "</i><br>");
            // Svolgimento
            print ("Svolta il <i>" . data_italiana($dataass['dataassemblea']) . " (" . $dataass['orainizio'] . " - " . $dataass['orafine'] . ")</i></td>");

        // Info (O.d.G. - Docente Ora - Rapp. Richiedenti)
            print("<td align='center'>");
            // OdG
                print("<a tabindex='0' class='btn btn-outline-info' role='button' data-bs-toggle='popover' data-bs-trigger='focus' data-bs-title='Ordine del Giorno' data-bs-content='" .nl2br($dataass['odg']) ."' data-bs-html='true'><i class='bi bi-card-checklist'></i></a>");
            // Rapp. di Classe
                // Array nomi rappresentanti
                $rapp = [];
                // Query sql
                $alu = "SELECT cognome,nome FROM tbl_alunni 
		        WHERE idalunno=" . $dataass['rappresentante1'] . "
		        OR idalunno=" . $dataass['rappresentante2'] . "
		        ORDER BY cognome";
                $risalu = eseguiQuery($con, $alu);
                while($dataalu = mysqli_fetch_array($risalu)){
                    $ncrapp = $dataalu['cognome'] ." " .$dataalu['nome'];
                    array_push($rapp, $ncrapp);
                };
                // Check se assemblea confermata | TRUE = Info Rapp. Classe - FALSE = No Info Rapp. Classe
                if($dataass['rappresentante2'] == 0 & $_SESSION['idstudente'] != $dataass['rappresentante1']){
                    if ($alurapp)
                    {
                        print (" <a href='registra_conferma.php?idassemblea=" . $dataass['idassemblea'] . "' class='btn btn-outline-success' role='button'><i class='bi bi-check-lg'></i></a> ");
                    }else{
                        print(" <button tabindex='0' class='btn btn-outline-primary' disabled><i class='bi bi-people-fill'></i></button>");
                    }
                }
                else{
                    print(" <a tabindex='0' class='btn btn-outline-primary' role='button' data-bs-toggle='popover' data-bs-trigger='focus' data-bs-title='Rappresentanti di Classe' data-bs-content='" .$rapp[0] ."<br>" .$rapp[1] ."' data-bs-html='true'><i class='bi bi-people-fill'></i></a>");
                }
            // Docenti Interessati
                // Output nomi docenti concedenti
                    // Check se docente ha concesso l'assemblea
                    if($dataass['concesso1'] == 1){$fontc1 = '<i class="bi bi-check-lg"></i>';} elseif($dataass['concesso1'] == 2){$fontc1 = '<i class="bi bi-x-lg"></i>';};
                    if($dataass['concesso2'] == 1){$fontc2 = '<i class="bi bi-check-lg"></i>';} elseif($dataass['concesso2'] == 2){$fontc2 = '<i class="bi bi-x-lg"></i>';};
                    // Dati Docenti
                    // Primo
                    $docout1 = "<span>" .estrai_dati_docente($dataass['docenteconcedente1'], $con) ." $fontc1</span>";
                    // Secondo (se esiste)
                    if($dataass['docenteconcedente2'] != 0){
                        $docout2 = "<br><span>" .estrai_dati_docente($dataass['docenteconcedente2'], $con) ." $fontc2</span>";
                    }
                    print(" <a tabindex='0' class='btn btn-outline-secondary' role='button' data-bs-toggle='popover' data-bs-trigger='focus' data-bs-title='Docenti Concedenti' data-bs-content='" .$docout1 .$docout2 ."' data-bs-html='true'><i class='bi bi-person-workspace'></i></a>");
            print("</td>");
        // Esito Richiesta
        print("<td align=center>");
            if($dataass['autorizzato'] == 2){
                print("<span style='color: red;'><i class='bi bi-x-lg'></i> ". estrai_dati_docente($dataass['docenteautorizzante'], $con) ."</span>");
                if($dataass['note'] != NULL){
                    print("<br/> <a tabindex='0' class='btn btn-outline-danger' role='button' data-bs-toggle='popover' data-bs-trigger='focus' data-bs-title='Annotazioni della Dirigenza' data-bs-content='" .nl2br($dataass['note']) ."' data-bs-html='true'><i class='bi bi-info-circle'></i></a>");
                }
            }elseif($dataass['autorizzato'] == 1){
                print("<span style='color: green;'><i class='bi bi-check-lg'></i> ". estrai_dati_docente($dataass['docenteautorizzante'], $con) ."</span>");
                if($dataass['note'] != NULL){
                    print("<br/> <a tabindex='0' class='btn btn-outline-success' role='button' data-bs-toggle='popover' data-bs-trigger='focus' data-bs-title='Annotazioni della Dirigenza' data-bs-content='" .nl2br($dataass['note']) ."' data-bs-html='true'><i class='bi bi-info-circle'></i></a>");
                }
            }
        print("</td>");
        // Verbale 
        print("<td align=center>");
            // Check se esiste visione verbale e commento rapporto
            if($dataass['visione_verbale'] == 1){
                $visverb = "-- <br><b>ESAME VERBALE</b><br>" .$dataass['commenti_verbale'] ."<br><b><i>" .estrai_dati_docente($dataass['docente_visione'], $con) ."</i></b>";
            }else{
                $visverb = "";
            }
            // Modal visualizzazione verbale
            $modalverb = "
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
                </div>";
            // Check se alunno è rappresentante
            if($alurapp){
                // Check se verbale non è stato inviato
                if($dataass['verbale'] == NULL & $dataass['autorizzato'] == 1 & date('Y-m-d') >= $dataass['dataassemblea']){
                    // Status per blocco richieste assemblee di classe
                    $noverb = true;
                    // Pulsante invio verbale
                    print("<a tabindex='0' class='btn btn-outline-danger' href='insver.php?idassemblea=$idassemblea' role='button' data-bs-toggle='popover' data-bs-trigger='hover' data-bs-content='Inserisci Verbale!'><i class='bi bi-file-post-fill'></i> Inserisci</a>");
                    // Stampa ritardo di invio verbale
                    $ritardogg = date_diff(date_create($dataass['dataassemblea']), date_create(date('Y-m-d')), true)->days;
                    if($ritardogg < 10){
                        print("<span class='ms-2 badge bg-warning'>Ritardo! ($ritardogg giorni)</span>");
                    }elseif($ritardogg >= 10){
                        print("<span class='ms-2 badge bg-danger'>Ritardo! ($ritardogg giorni)</span>");
                    }
                }else{
                    // Check se siamo in data dell'assemblea oppure no
                    if(date('Y-m-d') >= $dataass['dataassemblea']){
                        // Check se verbale non è firmato dal presidente
                        if($dataass['alunnopresidente'] == 0 & $idalunno != $dataass['alunnosegretario'] & $dataass['autorizzato'] == 1){
                            // Status per blocco richieste assemblee di classe
                            $noverb = true;
                            print("<a tabindex='0' class='btn btn-outline-success' href='registra_firmapresidente.php?idassemblea=$idassemblea' role='button' data-bs-toggle='popover' data-bs-trigger='hover' data-bs-content='Conferma e Registra Firma Presidente!'><i class='bi bi-send-check-fill'></i> Firma e Invia</a>");
                            // Stampa verbale
                            print($modalverb);
                            print("<button type='button' class='btn btn-outline-primary' data-bs-toggle='modal' data-bs-target='#modalAss$idassemblea'><i class='bi bi-eye-fill'> Visualizza</i></button>");
                            // Fine stampa verbale
                            print(" <a tabindex='0' class='btn btn-outline-warning' href='insver.php?idassemblea=$idassemblea' role='button' data-bs-toggle='popover' data-bs-trigger='hover' data-bs-content='Correggi Verbale'><i class='bi bi-pencil-square'></i> Correggi</a>");
                            // Stampa ritardo di invio verbale
                            $ritardogg = date_diff(date_create($dataass['dataassemblea']), date_create(date('Y-m-d')), true)->days;
                            if($ritardogg < 10){
                                print("<span class='ms-2 badge bg-warning'>Ritardo! ($ritardogg giorni)</span>");
                            }elseif($ritardogg >= 10){
                                print("<span class='ms-2 badge bg-danger'>Ritardo! ($ritardogg giorni)</span>");
                            }
                        // Se alunno è segretario e verbale non firmato dal presidente
                        }elseif($dataass['autorizzato'] == 1 & $dataass['alunnopresidente'] == 0){
                            // Status per blocco richieste assemblee di classe
                            $noverb = true;
                            // Stampa pulsanti verbale
                            print($modalverb);
                            print("<button type='button' class='btn btn-outline-primary' data-bs-toggle='modal' data-bs-target='#modalAss$idassemblea'><i class='bi bi-eye-fill'> Visualizza</i></button>");
                            print(" <a tabindex='0' class='btn btn-outline-warning' href='insver.php?idassemblea=$idassemblea' role='button' data-bs-toggle='popover' data-bs-trigger='hover' data-bs-content='Correggi Verbale'><i class='bi bi-pencil-square'></i> Correggi</a>");
                            // Stampa ritardo di invio verbale
                            $ritardogg = date_diff(date_create($dataass['dataassemblea']), date_create(date('Y-m-d')), true)->days;
                            if($ritardogg < 10){
                                print("<span class='ms-2 badge bg-warning'>Ritardo! ($ritardogg giorni)</span>");
                            }elseif($ritardogg >= 10){
                                print("<span class='ms-2 badge bg-danger'>Ritardo! ($ritardogg giorni)</span>");
                            }
                            // Se sono presenti entrambi le firme (solo visualizzazione)
                        }elseif($dataass['autorizzato'] == 1){
                            print($modalverb);
                            print("<button type='button' class='btn btn-outline-primary' data-bs-toggle='modal' data-bs-target='#modalAss$idassemblea'><i class='bi bi-eye-fill'> Visualizza</i></button>");
                        }
                    }
                }
            }else{
                // Check se verbale è inserito, firmato sia da segretario sia da presidente
                if($dataass['verbale'] != NULL & $dataass['autorizzato'] == 1 & $dataass['alunnopresidente'] != 0 & $dataass['alunnosegretario'] != 0){
                    print($modalverb);
                    print("<button type='button' class='btn btn-outline-primary' data-bs-toggle='modal' data-bs-target='#modalAss$idassemblea'><i class='bi bi-eye-fill'> Visualizza</i></button>");
                }
            }
    }

    // Fine tabella assemblee
    print("
            </table>
        </div>
    ");

    
}
// Output pulsante Richiesta Assemblea
if($alurapp){
    if($noverb == true){
        ?>
            <p align='center'> <span class="d-inline-block" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Impossible richiedere nuove assemblee di classe finché non hai inserito/inviato il verbale della precedente!">
                <button class="btn btn-outline-secondary btn-sm" type="button" disabled>Richiedi nuova assemblea</button>
            </span></p>
        <?php
        }else{
        ?>
            <form action='ricgen.php' method='POST'>
                <p align='center'><input type=hidden value='<?= $idclasse; ?>' name='idclasse'></p>
                <p align='center'><input type=submit class='btn btn-outline-secondary btn-sm' value='Richiedi nuova assemblea'></p>
            </form>
        <?php
        /* print "<form action='ricgen.php' method='POST'>";
        print " <p align='center'><input type=hidden value='" . $idclasse . "' name='idclasse'></p>";
        print "	<p align='center'><input type=submit class='btn btn-outline-secondary btn-sm' value='Richiedi nuova assemblea'>";
        print "</form>"; */
    }

}
?>

<?php
mysqli_close($con);
stampa_piede_new("");


