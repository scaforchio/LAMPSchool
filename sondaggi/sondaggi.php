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

$titolo = "Gestione Sondaggi Alunni";

stampa_head_new($titolo, "", "", "S");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

$res = eseguiQuery($con, "SELECT * FROM tbl_sondaggi");
?>

<center>
    <h2>Sondaggi alunni</h2>
    <table class="table table-striped" style="table-layout: auto;">
        <tr>
            <th>Oggetto</th>
            <th>Descrizione</th>
            <th>Risposte</th>
            <th>Azioni</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($res)) { ?>
            <tr>
                <td><?= $row['oggetto']; ?></td>
                <td><?= ellipsis($row['descrizione'], 60) ?></td>
                <td>
                    <?php
                    $tot = eseguiQuery($con, "SELECT COUNT(*) FROM tbl_rispostesondaggi WHERE idsondaggio = " . $row['idsondaggio'])->fetch_array()[0];
                    $risp = eseguiQuery($con, "SELECT COUNT(*) FROM tbl_rispostesondaggi WHERE idsondaggio = " . $row['idsondaggio'] . " AND idopzione != -1")->fetch_array()[0];
                    ?>
                    <?= $risp . " / " . $tot; ?>
                </td>
                <td>
                    <?php if ($row['attivo'] == 1) { ?>
                        <button class="btn btn-sm btn-secondary"
                            onclick="window.location.href='switch.php?id=<?= $row['idsondaggio']; ?>&act=0'">
                            <i class="bi bi-stop"></i> Ferma Sondaggio
                        </button>
                    <?php } else { ?>
                        <button class="btn btn-sm btn-secondary"
                            onclick="window.location.href='switch.php?id=<?= $row['idsondaggio']; ?>&act=1'">
                            <i class="bi bi-play"></i> Avvia Sondaggio 
                        </button>
                    <?php } ?>
                    <button class="btn btn-sm btn-secondary"
                        onclick="window.location.href='risultati.php?id=<?= $row['idsondaggio']; ?>'">
                        <i class="bi bi-bar-chart-line"></i> Risultati
                    </button>
                    <button class="btn btn-sm btn-secondary"
                        onclick="confirm('Sicuro?') ? window.location.href='elimina.php?id=<?= $row['idsondaggio']; ?>' : null">
                        <i class="bi bi-trash"></i> Elimina
                    </button> 
                </td>
            </tr>
        <?php } ?>
    </table>
</center>

<center>
    <button class='btn btn-secondary' type="button" onclick="window.location.href='crea.php'">Crea nuovo sondaggio</button>
</center>

<?php
stampa_piede_new("");
mysqli_close($con);
