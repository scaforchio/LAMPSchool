<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2023 Vittorio Lo Mele
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

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Programmazione Badge";
stampa_head_new($titolo, "", "", "MS");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

$query =
    "SELECT
    it_tbl_alunni.idalunno,
    it_tbl_alunni.cognome,
    it_tbl_alunni.nome,
    it_tbl_alunni.datanascita,
    it_tbl_classi.anno,
    it_tbl_classi.sezione,
    it_tbl_classi.specializzazione,
    it_tbl_gruppiritardi.minutiaggiuntivi,
    it_tbl_gruppiritardi.descrizione
FROM
    it_tbl_alunni
LEFT OUTER JOIN it_tbl_classi ON it_tbl_classi.idclasse = it_tbl_alunni.idclasse
LEFT OUTER JOIN it_tbl_gruppiritardi ON it_tbl_gruppiritardi.idgrupporitardo = it_tbl_alunni.idgrupporitardo";

$ris = mysqli_query($con, $query);

?>

<div style="margin-left: 10px; margin-right:10px; margin-bottom: 10px;">
    <h5>Operazioni:</h5>
    <button class="btn btn-outline-secondary" onclick="serialHandler.init()">Connetti Seriale</button>
    <button class="btn btn-outline-secondary" onclick="document.location.reload()">Reset connessione</button>
    <button class="btn btn-outline-secondary" onclick="readd()">Leggi dati carta</button> <br><br>

    <div class="row mb-2">
        <div class="col">
            <label for="programmerState" class="h6">Stato Programmatore:</label>
            <input type="text" id="programmerState" class="form-control" disabled value="DISCONNESSO">
        </div>
        <div class="col">
            <label for="inbound" class="h6">Inbound:</label>
            <input type="text" id="inbound" class="form-control" disabled value="">
        </div>
        <div class="col">
            <label for="outbound" class="h6">Outbound:</label>
            <input type="text" id="outbound" class="form-control" disabled value="">
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="opcode" class="h6">Operazione:</label>
            <input type="text" id="opcode" class="form-control" disabled value="">
        </div>
        <div class="col">
            <label for="matricola" class="h6">Matricola:</label>
            <input type="text" id="matricola" class="form-control" disabled value="">
        </div>
        <div class="col">
            <label for="nome" class="h6">Nome:</label>
            <input type="text" id="nome" class="form-control" disabled value="">
        </div>
        <div class="col">
            <label for="cognome" class="h6">Cognome:</label>
            <input type="text" id="cognome" class="form-control" disabled value="">
        </div>
        <div class="col">
            <label for="datanascita" class="h6">Data di nascita:</label>
            <input type="text" id="datanascita" class="form-control" disabled value="">
        </div>
    </div>
    <span id="errorMessage" style="color: red;"></span><br>
</div>    

<div style="margin-left: 10px; margin-right:10px;">
    <h5>Lista alunni:</h5>
    <table class="table table-striped table-bordered" id="tabelladati">
        <thead>
            <tr class='prima'>
                <td>Cognome</td>
                <td>Nome</td>
                <td>Data di Nascita</td>
                <td>Classe</td>
                <td>Gruppo ritardo</td>
                <td>Prog.</td>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($alunno = mysqli_fetch_assoc($ris)) {
                print("<tr>");
                print("<td>" . $alunno["cognome"] . "</td>");
                print("<td>" . $alunno["nome"] . "</td>");
                print("<td>" . data_italiana($alunno["datanascita"]) . "</td>");
                print("<td>" . $alunno["anno"] . $alunno["sezione"] . " " . $alunno["specializzazione"] . "</td>");
                print("<td>" . $alunno["descrizione"] . " (+" . $alunno["minutiaggiuntivi"] . " minuti)</td>");
                print("<td> <button class='btn btn-outline-secondary' onclick=\"writee(");

                print("'" . $_SESSION['suffisso'] . $alunno['idalunno'] . "',");
                print("'" . $alunno["nome"] . "',");
                print("'" . $alunno["cognome"] . "',");
                print("'" . $alunno["datanascita"] . "'");

                print(")\">Invia al Programmatore</button></td>");
                print("</tr>");
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    // SOURCE FOR THIS SCRIPT: https://github.com/UnJavaScripter/web-serial-example/blob/master/src/serial-handler.ts

    class SerialHandler {
        reader;
        writer;
        isConnected = false;
        encoder = new TextEncoder();
        decoder = new TextDecoder();

        async init() {
            if ('serial' in navigator) {
                try {
                    const port = await (navigator).serial.requestPort();
                    await port.open({
                        baudRate: 115200
                    });

                    this.writer = port.writable.getWriter();
                    this.reader = port.readable.getReader();

                    const signals = await port.getSignals();
                    console.log(signals);
                    this.isConnected = true;
                    $("#programmerState").attr("value", "CONNESSO");
                    $("#programmerState").attr("style", "color: green");
                    $("#errorMessage").html('');
                } catch (err) {
                    $("#errorMessage").html('Errore apertura porta seriale:', err);
                }
            } else {
                $("#errorMessage").html('WebSerial non abilitata nel browser. Visita https://developer.mozilla.org/en-US/docs/Web/API/Web_Serial_API#browser_compatibility per maggiori info.')
            }
        }

        async write(data) {
            const dataArrayBuffer = this.encoder.encode(data);
            return await this.writer.write(dataArrayBuffer);
        }

        async read() {
            try {
                const readerData = await this.reader.read();
                return this.decoder.decode(readerData.value);
            } catch (err) {
                const errorMessage = `impossibile leggere dati: ${err}`;
                $("#errorMessage").html(errorMessage);
                return errorMessage;
            }
        }
    }
    const serialHandler = new SerialHandler();
</script>

<?php import_datatables(); ?>

<script>
    $(document).ready(function() {
        let table = new DataTable('#tabelladati', {
            responsive: true,
            'pageLength': 10,
            columnDefs: [{
                    orderable: true,
                    className: 'reorder',
                    targets: 0
                },
                {
                    orderable: true,
                    className: 'reorder',
                    targets: 1
                },
                {
                    orderable: true,
                    className: 'reorder',
                    targets: 3
                },
                {
                    orderable: true,
                    className: 'reorder',
                    targets: 4
                },
                {
                    orderable: false,
                    targets: '_all'
                },
            ],
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

<script>
    function writee(matricola, nome, cognome, datanascita) {
        if (serialHandler.isConnected) {
            $("#opcode").attr("value", "SCRITTURA PRONTA di");
            $("#matricola").attr("value", matricola);
            $("#nome").attr("value", nome);
            $("#cognome").attr("value", cognome);
            $("#datanascita").attr("value", datanascita);

            let mess = `W|${matricola}|${nome}|${cognome}|${datanascita}`;
            $("#outbound").attr("value", mess);
            serialHandler.write(mess);
            console.log(mess);
            $("#errorMessage").html("");
        } else {
            $("#errorMessage").html("Impossibile scrivere con seriale disconnessa");
        }
    }

    function readd() {

    }
</script>

<?php

stampa_piede_new("");
mysqli_close($con);
