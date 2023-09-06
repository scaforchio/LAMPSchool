<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2022 Vittorio Lo Mele
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

/* Programma per la modifica dei bindings tra utenti locali ed oidc */

function stampaSelect($uid, $selected)
{
    $s = "<select id='amselector" . $uid . "'>";
    $s .= "<option value='d'";
    if ($selected == "d") {
        $s .= " selected";
    }
    $s .= ">No</option>";
    $s .= "<option value='e'";
    if ($selected == "e") {
        $s .= " selected";
    }
    $s .= ">Si</option>";
    $s .= "<option value='x'";
    if ($selected == "x") {
        $s .= " selected";
    }
    $s .= ">Forzato</option>";
    $s .= "</select>";
    return $s;
}

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Modifica bindings utenti Locali<->OIDC";
$script = "<link rel='stylesheet' type='text/css' href='../lib/js/datatables/datatables.min.css'/>
        <link rel='stylesheet' type='text/css' href='../css/modal.css'/>
        <link rel='stylesheet' type='text/css' href='../css/toastr.min.css'/>
           <script type='text/javascript' src='../lib/js/datatables/datatables.min.js'></script>
           <script type='text/javascript' src='../lib/js/toastr.min.js'></script>
           <script> 
           $(document).ready( function () {
                 $('#tabelladati').DataTable({
                     'pageLength': 100,
                     columnDefs: [
                        { orderable: true, className: 'reorder', targets: 0 },
                        { orderable: true, className: 'reorder', targets: 1 },
                        { orderable: true, className: 'reorder', targets: 2 },
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

            function applicaModifiche(uid){
                let oidc_uid = $('#uuidfield' + uid).val();
                let oidc_am = $('#amselector' + uid).val();
                
                //richiesta crud

                $.ajax({
                    type: 'POST',
                    url: 'CRUDoidc.php',
                    data: {
                        'uid': uid,
                        'oidc_uid': oidc_uid,
                        'oidc_am': oidc_am,
                    },
                    success: function (data) {
                        toastr.success('Modifiche applicate correttamente!');
                    },
                    error: function (data) {
                        toastr.error(data.responseText);
                    }
                });

            }
            
            </script>";
stampa_head($titolo, "", $script, "M");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

$query = "SELECT `idutente`, `userid`, `tipo`, `oidc_uid`, `oidc_authmode` FROM `tbl_utenti`";
$ris = eseguiQuery($con, $query);

$tipiUtenti = array(
    "D" => "Docente",
    "P" => "Preside",
    "S" => "Staff",
    "A" => "Amministrativo",
    "L" => "Alunno",
    "T" => "Tutore",
    "M" => "Amministratore",
    "E" => "Esami di stato"
);

?>
<div class="modal_caricamento"></div>
<center>
    <table border="1" id="tabelladati">
        <thead>
            <tr class='prima'>
                <td>ID Utente</td>
                <td>Nome Utente</td>
                <td>Tipo profilo</td>
                <td>UUID OIDC Associati</td>
                <td>Tipo di accesso OIDC</td>
                <td>Applica modifiche</td>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($utente = mysqli_fetch_array($ris, MYSQLI_ASSOC)) {
                print("<tr>");
                print("<td>" . $utente["idutente"] . "</td>");
                print("<td>" . $utente["userid"] . "</td>");
                print("<td>" . $tipiUtenti[$utente["tipo"]] . "</td>");
                print("<td> <input type='text' id='uuidfield" . $utente["userid"] .  "' size='74' value='" . $utente["oidc_uid"] . "'> </td>");
                print("<td> " . stampaSelect($utente["userid"], $utente["oidc_authmode"]) . "</td>");
                print("<td> <button class='button' onclick='applicaModifiche(\"" . $utente["userid"] ."\")'>Applica</button></td>");
                print("</tr>");
            }
            ?>
        </tbody>
    </table>
</center>
<?php

stampa_piede("");
mysqli_close($con);
