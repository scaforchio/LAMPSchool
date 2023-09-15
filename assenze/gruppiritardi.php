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

/* Forza sincronizzazione utenze con active directory */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
@require_once("../lib/admqtt.php");

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
    if (!$con) {
        http_response_code(500);
        die("Connessione al server fallita");
        exit;
    }

    if ($_POST["action"] == "delete"){
        $iddd = mysqli_real_escape_string($con, $_POST["idgrupporitardo"]);
        eseguiQuery($con, "DELETE FROM tbl_gruppiritardi WHERE idgrupporitardo = $iddd");
        eseguiQuery($con, "UPDATE `tbl_alunni` SET `idgrupporitardo` = '1' WHERE idgrupporitardo = $iddd");
        die("ok");
    }

    if ($_POST["action"] == "edit"){
        $iddd = mysqli_real_escape_string($con, $_POST["idgrupporitardo"]);
        $minutiaggiuntivi = mysqli_real_escape_string($con, $_POST["minutiaggiuntivi"]);
        eseguiQuery($con, "UPDATE `tbl_gruppiritardi` SET `minutiaggiuntivi` = $minutiaggiuntivi WHERE idgrupporitardo = $iddd");
        die("ok");
    }

    if ($_POST["action"] == "add"){
        $descrizione = mysqli_real_escape_string($con, $_POST["descrizione"]);
        $minutiaggiuntivi = mysqli_real_escape_string($con, $_POST["minutiaggiuntivi"]);
        eseguiQuery($con, "INSERT INTO `tbl_gruppiritardi` (`idgrupporitardo`, `minutiaggiuntivi`, `descrizione`) VALUES (NULL, $minutiaggiuntivi, '$descrizione')");
        header("Location: gruppiritardi.php");
        die();
    }

    header("Location: gruppiritardi.php");
    die();
    
}

$titolo = "GRUPPI RITARDI";
$script = "<link rel='stylesheet' type='text/css' href='../lib/js/toastr.min.css'/>
           <script type='text/javascript' src='../lib/js/toastr.min.js'></script>";


stampa_head($titolo, "", $script, "M");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

$gruppi = eseguiQuery($con, "SELECT * FROM `tbl_gruppiritardi` WHERE idgrupporitardo != 1");

?>

<br>
<br>
<br>

<center>
    <span>Aggiunta gruppo</span> <br>
    <form action="" method="post" style="margin-top: 8px;">
        <input type="text" name="descrizione" id="descrizione" placeholder="Descrizione">
        <input type="number" name="minutiaggiuntivi" id="minutiaggiuntivi" placeholder="Minuti Aggiuntivi" min="0">
        <input type="hidden" name="action" value="add">
        <button>Aggiungi</button>
    </form>
</center>

<br>
<br>

<center>
<table border="1">
    <thead>
        <tr class="prima">
            <td>Descrizione</td>
            <td>Minuti Aggiuntivi</td>
            <td>Azione</td>
        </tr>
    </thead>
    <tbody>
        <?php while ($gruppo = mysqli_fetch_assoc($gruppi)) { ?>
            <tr id="row_<?php echo $gruppo['idgrupporitardo'] ?>">
                <td><?php echo $gruppo['descrizione'] ?></td>
                <td>
                    <input type="number" min="0" id="num_t_<?php echo $gruppo['idgrupporitardo'] ?>" disabled value="<?php echo $gruppo['minutiaggiuntivi'] ?>">
                </td>
                <td>
                    <button id="btn_m_<?php echo $gruppo['idgrupporitardo'] ?>" onclick="btnm(<?php echo $gruppo['idgrupporitardo'] ?>)">Modifica</button>
                    <button id="btn_e_<?php echo $gruppo['idgrupporitardo'] ?>" onclick="btne(<?php echo $gruppo['idgrupporitardo'] ?>)">Elimina</button>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
</center>

<script>

function btnm(id) {
    if($("#num_t_" + id).prop("disabled")){
        // abilita modifica
        $("#btn_m_" + id).html("Salva");
        $("#num_t_" + id).prop("disabled", false);
    }else{
        // salva modifica
        $.ajax({
            type: 'POST',
            url: 'gruppiritardi.php',
            data: {
                'action': "edit",
                'idgrupporitardo': id,
                'minutiaggiuntivi': $("#num_t_" + id).val()
            },
            success: function (data) {
                toastr.success("Modifica effettuata con successo");
                $("#btn_m_" + id).html("Modifica");
                $("#num_t_" + id).prop("disabled", true);
            },
            error: function (data) {
                toastr.error("Errore " + data.responseText);
            }
        });
    }
}

function btne(id) {
    let r = confirm("Eliminando il gruppo tutti gli alunni ad esso associato verranno spostati in quello predefinito! Continuare?");
    $.ajax({
        type: 'POST',
        url: 'gruppiritardi.php',
        data: {
            'action': "delete",
            'idgrupporitardo': id
        },
        success: function (data) {
            toastr.success('Modifiche applicate correttamente!');
            $("#row_" + id).remove();
        },
        error: function (data) {
            toastr.error("Errore " + data.responseText);
        }
    });
}

</script>

<?php

stampa_piede("");
mysqli_close($con);
