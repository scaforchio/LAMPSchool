<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2024 Vittorio Lo Mele
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

$titolo = "Crea nuovo sondaggio";

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

$classi = eseguiQuery($con, "SELECT idclasse, anno, sezione, specializzazione FROM tbl_classi ORDER BY anno ASC, sezione ASC;");
$seleccont = "";
$seleccont .= "<option value='0'>Seleziona classe...</option>";
$classicont = array();
while ($classe = mysqli_fetch_assoc($classi)) {
    $seleccont .= "<option value='" . $classe['idclasse'] . "'>" . $classe['anno'] . $classe['sezione'] . " " . $classe['specializzazione'] . "</option>";
    array_push($classicont, $classe['idclasse']);
}

if ($_SERVER['REQUEST_METHOD'] == "POST"){
    $oggetto = $con->real_escape_string($_POST['oggetto']);
    $descrizione = $con->real_escape_string($_POST['descrizione']);
    $tutti = $con->real_escape_string($_POST['tutti']);
    unlink($_POST['oggetto']);
    unlink($_POST['descrizione']);

    $opzioni_s = array();
    $classi_s = array();
    $opz = 0;

    foreach ($_POST as $key => $value) {
        if(strpos($key, "opz") !== false){
            array_push(
                $opzioni_s,
                array($opz, $con->real_escape_string($value))
            );
            $opz++;
        }
        if(strpos($key, "cla") !== false && $tutti == 0){
            array_push($classi_s, $con->real_escape_string($value));
        }
    }

    if($tutti == 1){
        $classi_s = $classicont;
    }

    $opzioni_s = json_encode($opzioni_s);
    eseguiQuery($con, "INSERT INTO tbl_sondaggi (oggetto, descrizione, opzioni) VALUES ('$oggetto', '$descrizione', '$opzioni_s')");
    $idsondaggio = mysqli_insert_id($con);

    foreach ($classi_s as $value) {
        $res = eseguiQuery($con, "SELECT idalunno FROM tbl_alunni WHERE idclasse = '$value'");
        while ($row = mysqli_fetch_assoc($res)) {
            $ids = $row['idalunno'];
            eseguiQuery($con, "INSERT INTO tbl_rispostesondaggi (idsondaggio, idutente, idopzione) VALUES ($idsondaggio, $ids, -1)");
        }
    }

    header("location: sondaggi.php");
}

stampa_head_new($titolo, "", "", "S");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$res = eseguiQuery($con, "SELECT * FROM tbl_sondaggi");
?>

<form action="" method="post">
    <center>
        <div style="width: 500px;">
            <h2>Crea sondaggio</h2>
            <br>
            <div class="alert alert-danger" role="alert">
                Attenzione! Una volta creato il sondaggio <b><u>non è piu possibile</u></b> modificarne il contenuto o l'assegnazione alle classi! Verificare con attenzione i dati inseriti.
            </div>

            <div class="mb-3">
                <label for="oggetto" class="form-label">Oggetto</label>
                <input type="text" class="form-control" id="oggetto" name="oggetto" required>
            </div>
            <div class="mb-3">
                <label for="descrizione" class="form-label">Descrizione</label>
                <textarea class="form-control" id="descrizione" name="descrizione" required></textarea>
            </div>

            <b>OPZIONI</b> 
            <button type="button" class="btn btn-sm btn-outline-secondary" style="margin-bottom: 10px;" onclick="aggOpz()">
                <i class="bi bi-plus"></i>
            </button>
            <div id="opzioni">
                <div class="mb-3 d-flex" id='option_group_0'>
                    <input type="text" class="form-control" name="opz0" placeholder="Opzione 1..." required>
                </div>
                <div class="mb-3 d-flex" id='option_group_1'>
                    <input type="text" class="form-control" name="opz1" placeholder="Opzione 2..." required>
                </div>
            </div>

            <b>CLASSI</b> 
            <button type="button" class="btn btn-sm btn-outline-secondary" style="margin-bottom: 10px;" onclick="aggClasse()" id="aggClassBtn">
                <i class="bi bi-plus"></i>
            </button>
            <div class="mb-3">
                <input type="checkbox" id="tutti" name="tutti" value="1">
                <label for="tutti">Tutte le classi</label>
            </div>
            <div id="classi">
                <div class="mb-3 d-flex" id='class_group_0'>
                    <select name="cla0" class="form-control">
                        <?= $seleccont ?>
                    </select>
                    <button type="button" onclick="eliminaClasse(0)" class="btn btn-outline-secondary" style="margin-left: 10px;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <hr style="width: 500px;">
        <button class='btn btn-secondary' type="button" onclick="window.location.href='sondaggi.php'">
            <i class="bi bi-x-octagon"></i> Annulla
        </button>
        <button class='btn btn-secondary' type="submit">
            <i class="bi bi-floppy"></i> Salva
        </button>
    </center>
</form>
<script>
    var opz = 2;
    var clas = 1;

    function aggOpz() {
        var div = document.createElement('div');
        div.className = "mb-3 d-flex";
        div.id = "option_group_" + opz;
        div.innerHTML = "<input type='text' class='form-control' name='opz" + opz + "' placeholder='Opzione ...' required><button type='button' onclick='eliminaOpz(" + opz + ")' class='btn btn-outline-secondary' style='margin-left: 10px;'><i class='bi bi-trash'></i></button>";
        document.getElementById('opzioni').appendChild(div);
        opz++;
    }

    function eliminaOpz(id) {
        document.getElementById('option_group_' + id).remove();
    }

    function aggClasse() {
        var div = document.createElement('div');
        div.className = "mb-3 d-flex";
        div.id = "class_group_" + clas;
        div.innerHTML = "<select name='cla" + clas + "' class='form-control'><?= $seleccont ?></select><button type='button' onclick='eliminaClasse(" + clas + ")' class='btn btn-outline-secondary' style='margin-left: 10px;'><i class='bi bi-trash'></i></button>";
        document.getElementById('classi').appendChild(div);
        clas++;
    }

    function eliminaClasse(id) {
        document.getElementById('class_group_' + id).remove();
    }

    document.getElementById('tutti').addEventListener('change', function() {
        if(this.checked) {
            document.getElementById('classi').style.display = "none";
            document.getElementById('aggClassBtn').style.display = "none";
        } else {
            document.getElementById('classi').style.display = "block";
            document.getElementById('aggClassBtn').style.display = "inline";
        }
    });
</script>

<?php
stampa_piede_new("");
mysqli_close($con);
