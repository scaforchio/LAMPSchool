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
$script = "<link rel='stylesheet' type='text/css' href='../lib/js/datatables/datatables.min.css'/>
           <script type='text/javascript' src='../lib/js/datatables/datatables.min.js'></script>
           <script> 
           $(document).ready( function () {
                 $('#tabelladati').DataTable({
                     'pageLength': 10,
                     columnDefs: [
                        { orderable: true, className: 'reorder', targets: 0 },
                        { orderable: true, className: 'reorder', targets: 1 },
                        { orderable: true, className: 'reorder', targets: 3 },
                        { orderable: true, className: 'reorder', targets: 4 },
                        { orderable: false, targets: '_all' },
                        { 'width': '10%', 'targets': 5 }
                     ],
                     'language': {
                                   'search': 'Filtra risultati:',
                                   'zeroRecords': 'Nessun dato da visualizzare',
                                   'info': 'Mostrate righe da _START_ a _END_ di _TOTAL_',
                                    'lengthMenu': 'Visualizzate _MENU_ righe',
                                            'paginate': {
                                                        'first':    'Prima',
                                                        'previous': 'Prec.',
                                                        'next':     'Succ.',
                                                        'last':     'Ultima'
                                                        }
                                   
                                            
                                }
                 });
                 $('.modal_caricamento').hide();
            } );
            </script>";
stampa_head($titolo, "", $script, "MS");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

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

<div class="programmatore">
    <button>Connetti Seriale</button>
</div>


<div class="modal_caricamento"></div>
<center>
    <table border="1" id="tabelladati">
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
                print("<td>" . $alunno["anno"] . $alunno["sezione"] . " " . $alunno["specializzazione"]. "</td>");
                print("<td>" . $alunno["descrizione"] . " (+" . $alunno["minutiaggiuntivi"]. " minuti)</td>");
                print("<td> <button class='button' onclick='applicaModifiche(\"" . $utente["idalunno"] ."\")'>Invia al Programmatore</button></td>");
                print("</tr>");
            }
            ?>
        </tbody>
    </table>
</center>
<?php

stampa_piede("");
mysqli_close($con);
