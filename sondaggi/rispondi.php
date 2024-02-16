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

$idstudente = $_SESSION['idutente'] - 2100000000;

$titolo = "Risposta al sondaggio";
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

$idsondaggio = $con->real_escape_string($_GET['id']);
$autorizzato = eseguiQuery($con, "SELECT idrisposta FROM tbl_rispostesondaggi WHERE idsondaggio = $idsondaggio AND idutente = $idstudente AND idopzione = -1");

if ($autorizzato->num_rows != 1){
    print("<div class='alert alert-danger' role='alert'>Non sei autorizzato a rispondere!</div>");
    stampa_piede_new("");
    exit;
}

$idrisposta = $autorizzato->fetch_assoc()['idrisposta'];
$sondaggio = eseguiQuery($con, "SELECT * FROM tbl_sondaggi WHERE idsondaggio = $idsondaggio")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == "POST"){
    $idsondaggio = $con->real_escape_string($_POST['idsondaggio']);
    $opzione = $con->real_escape_string($_POST['risposta']);
    $query = "UPDATE `tbl_rispostesondaggi` SET `idopzione` = '$opzione' WHERE `idrisposta` = $idrisposta";
    eseguiQuery($con, $query);
    header("location: ../login/ele_ges.php");
}

stampa_head_new($titolo, "", "", "L");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
?>

<form action="" method="post" class="row justify-content-center">
    <div class="col col-xs-12 col-sm-12 col-md-10 col-lg-3">
    <center><h2>Risposta al sondaggio</h2></center>
            <br>
            <div class="alert alert-danger" role="alert">
                Attenzione! È possibile rispondere al sondaggio <b><u>una sola volta</u></b>. Assicurati di aver letto attentamente le opzioni e di aver scelto quella che ritieni più opportuna.
            </div>
        <br>
        <b>Oggetto:</b> <?= $sondaggio['oggetto']; ?>
        <br><br>
        <b>Descrizione:</b> <br>
        <?= $sondaggio['descrizione']; ?><br><br>
        <input type="hidden" name="idsondaggio" value="<?= $idsondaggio; ?>">
        <?php 
        $opzioni = json_decode($sondaggio['opzioni']);
        foreach ($opzioni as $opzione) { ?>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="risposta" id="asd<?= $opzione[0]; ?>" value="<?= $opzione[0] ?>" required>
                <label class="form-check-label" for="asd<?= $opzione[0]; ?>"><?= $opzione[1]; ?></label>
            </div>
        <?php } ?>
        <br>
        <center><button class='btn btn-secondary' type="button" onclick="window.location.href='../login/ele_ges.php'">
            <i class="bi bi-x-octagon"></i> Annulla
        </button>
        <button class='btn btn-secondary' type="submit">
            <i class="bi bi-floppy"></i> Salva
        </button></center>
    </div>
</form>

<?php
stampa_piede_new("");
mysqli_close($con);
