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
//    VISUALIZZAZIONE DELLE ASSEMBLEE DI CLASSE PER I GENITORI
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
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$titolo = "Generazione Nuovo Verbale";
$script = "";
stampa_head_new($titolo, "", $script, "L");
// MEMORIZZAZIONE ID ALUNNO E ID CLASSE
$idalunno = $_SESSION['idstudente'];
$idclasse = estrai_classe_alunno($idalunno, $con);
// TESTATA
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='assricgen.php?idclasse=$idclasse'>Visualizza assemblee</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

// Prelievo id assemblea
$idassemblea = stringa_html('idassemblea');
$ver = '';
$orat = '';
$data = '';
$query = "SELECT * FROM tbl_assemblee WHERE idassemblea=$idassemblea";
$ris = eseguiQuery($con, $query);

$d = mysqli_fetch_array($ris);
$ver = $d['verbale'];
$orat = $d['oratermine'];
$data = data_italiana($d['dataassemblea']);
$segret = $d['alunnosegretario'];
$odg = $d['odg'];
if ($orat == "00:00:00")
    $orat = "";

if($idalunno == $d['rappresentante1'] || $idalunno == $d['rappresentante2']){
    if ($ver == NULL){
        // FORM GENERAZIONE VERBALE - insver2.php
        print("
            <form class='mb-3' style='max-width: 900px; margin: auto; position: relative;' action='insver2.php' name='verbale' method='POST'>
            <input type='hidden' name='idassemblea' value='$idassemblea'>
        ");
        // Stampa data assemblea
        print("
            <label class='form-label' for='dataass'><i class='bi bi-calendar-date'> Data Assemblea</i></label>
            <input type='data' class='form-control' id='dataass' value='$data' disabled>
            <input type='hidden' name='dataass' value='$data'>
        ");
        // Stampa OdG
        print("
            <label class='form-label mt-1' for='odg'> <i class='bi bi-list-ul'> Ordine del Giorno</i></label>
            <textarea class='form-control' rows=5 id='odg' disabled>$odg</textarea>
            <input type='hidden' name='odg' value='$odg'>
        ");
        // Inserimento orario di inizio
        print("
            <label class='form-label mt-1' for='orainizio'> <i class='bi bi-hourglass-top'> Orario Inizio</i></label>
            <input class='form-control' type='time' id='orainizio' name='orainizio'>
        ");
        // Inserimento argomenti trattati nei vari punti all'OdG
        print("
            <div id='textareaodg'>
                <label class='form-label mt-1'><i class='bi bi-file-earmark-medical'> Inserisci argomenti trattati nei punti all'ODG</i></label>
                <textarea class='form-control mt-1' rows=3 name='p1' placeholder='Argomenti 1° punto ODG'></textarea>
                <textarea class='form-control mt-1' rows=3 name='p2' placeholder='Argomenti 2° punto ODG'></textarea>
                <textarea class='form-control mt-1' rows=3 name='p3' placeholder='Argomenti 3° punto ODG'></textarea>
            </div>
            <button type='button' class='btn btn-outline-success mt-2 mb-2' onClick='aggiungiOdg()'><i class='bi bi-plus'></i></button> <br>
        ");
        // Inserimento orario di fine
        print("
            <label class='form-label mt-1' for='orafine'><i class='bi bi-hourglass-bottom'> Orario Fine</i></label>
            <input class='form-control' type='time' id='orafine' name='orafine'>
        ");
        // Inserimento firma segretario
        print("
            <label class='form-laber mt-1'><i class='bi bi-vector-pen'> Firma Segretario</i></label>
            <input class='form-control' type='text' value='" .decodifica_alunno($idalunno, $con) ."' disabled>
            <input type='hidden' name='segretario' value='$idalunno'>
            <input type='hidden' name='modifica' value='NULL'>
        ");
        // Invio verbale
        print("
            <div class='text-center'>
            <input class='btn btn-outline-success mt-3' role='button' type='submit' value='Genera e invia Verbale'>
            </div>
        ");
        print("</form>");
    }else{
        // Modifica verbale già immesso/generato
        print("
            <form class='mb-3' style='max-width: 900px; margin: auto; position: relative;' action='insver2.php' name='verbale' method='POST'>
            <input type='hidden' name='idassemblea' value='$idassemblea'>
        ");
        // Modifica ESCLUSIVAMENTE del verbale interamente e dell'ora fine
        print("
            <label class='form-label mt-1' for='modver'><i class='bi bi-file-post-fill'> Verbale</i></label>
            <textarea class='form-control mt-1' rows=8 id='modver' name='modifica' placeholder='Verbale Assemblea'>$ver</textarea>

            <label class='form-label mt-1' for='orafine'><i class='bi bi-hourglass-bottom'> Orario Fine</i></label>
            <input class='form-control' type='time' id='orafine' name='orafine' value='$orat'>
        ");
        // Firma segretario
        print("
            <label class='form-laber mt-1'><i class='bi bi-vector-pen'> Firma Segretario</i></label>
            <input class='form-control' type='text' value='" .decodifica_alunno($segret, $con) ."' disabled>
        ");
        // Inserimento del verbale modificato
        print("
            <div class='text-center'>
                <input class='btn btn-outline-warning mt-3' role='button' type='submit' value='Modifica Verbale'>
            </div>
        ");
    }
}else{
    print("
        <div class='mb-3' style='max-width: 900px; margin: auto; position: relative;'>
            <div class='alert alert-danger' role='alert'>
                ATTENZIONE! La tua utenza non risulta abilitata come Rappresentante di Classe! <a href='./assricgen.php'><- TORNA INDIETRO</a>
            </div>
        </div>
    ");
}
/*print "<form action='insver2.php' name='verbale' method='POST'>";
print "<CENTER><b>Inserisci verbale assemblea " . data_italiana($data) . "</b></CENTER>";
print "<input type='hidden' name = 'idassemblea' value='$idassemblea'>";
print "<p align='center'><textarea cols=150 rows=20 name='verbale'>$ver</textarea></p>";

print "<p align='center'><b>Segretario</b><input type='text' value='" . decodifica_alunno($idalunno, $con) . "' disabled><input type='hidden' name='segretario' value='$idalunno'></b>";
print "<p align='center'><b>Orario fine</b><input type='time' name='oratermine' value='" . substr($orat, 0, 5) . "' required></b>";

print "</table>";
print "<p align='center'><input type=submit value='Inserisci verbale'></p>";
print "</form>";*/

?>

<script type="text/javascript">
    // Funzione per inserimento nuove textarea
    let counter = 4;

    function aggiungiOdg() {
        if(counter<11){
        // Create a new textbox element
        const newTextArea = document.createElement('textarea');
        newTextArea.className = 'form-control mt-1';
        newTextArea.rows = '3';
        newTextArea.name = 'p' + counter;
        newTextArea.placeholder = 'Argomenti ' + counter +'° punto ODG';

        // Append the new textbox to the container
        const container = document.getElementById('textareaodg');
        container.appendChild(newTextArea);

        // Increment the counter for the next textbox
        counter++;
        }
    }
</script>

<?php
stampa_piede_new("");
mysqli_close($con);
