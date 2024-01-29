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

/* Lista compleanni per il giorno corrente */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
@require_once("../lib/admqtt.php");

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Compleanni di oggi";

stampa_head_new($titolo, "", "", "PMS");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

$qdocenti = "SELECT nome, cognome FROM `tbl_docenti` WHERE MONTH(datanascita) = MONTH(CURRENT_DATE()) AND DAY(datanascita) = DAY(CURRENT_DATE())";
$rdocenti = eseguiQuery($con, $qdocenti);

$qalunni = "SELECT nome, cognome, anno, sezione, specializzazione FROM `tbl_alunni`, `tbl_classi` WHERE `tbl_alunni`.`idclasse` = `tbl_classi`.`idclasse` AND MONTH(datanascita) = MONTH(CURRENT_DATE()) AND DAY(datanascita) = DAY(CURRENT_DATE());";
$ralunni = eseguiQuery($con, $qalunni);

$qamm = "SELECT nome, cognome FROM `tbl_amministrativi` WHERE MONTH(datanascita) = MONTH(CURRENT_DATE()) AND DAY(datanascita) = DAY(CURRENT_DATE())";
$ramm = eseguiQuery($con, $qamm);

?>
<center>
    <div style="width: 400px;">
        <h3>Compleanni di oggi</h3>

        <br><h4>Studenti</h4>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <td><b>Cognome e Nome</b></td>
                    <td><b>Classe</b></td>
                </tr>
            </thead>
            <tbody>
                <?php
                if(mysqli_num_rows($ralunni) == 0 || !$ralunni){
                    print("<td colspan='2'>Nessun compleanno in data odierna</td>");
                }else{
                    while ($alunno = mysqli_fetch_assoc($ralunni)) {
                        print("<td>" . $alunno['cognome'] . " " . $alunno['nome'] . "</td><td>" . $alunno['anno'] . " " . $alunno['sezione'] . " " . $alunno['specializzazione'] . "</td>");
                    }
                }
            ?>
            </tbody>
        </table>

        <br><h4>Docenti</h4>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <td><b>Cognome e Nome</b></td>
                </tr>
            </thead>
            <tbody>
            <?php
                if(mysqli_num_rows($rdocenti) == 0 || !$rdocenti){
                    print("<td colspan='2'>Nessun compleanno in data odierna</td>");
                }else{
                    while ($docente = mysqli_fetch_assoc($rdocenti)) {
                        print("<td>" . $docente['cognome'] . " " . $docente['nome'] . "</td>");
                    }
                }
            ?>
            </tbody>
        </table>

        <br><h4>Impiegati di segreteria</h4>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <td><b>Cognome e Nome</b></td>
                </tr>
            </thead>
            <tbody>
            <?php
                if(mysqli_num_rows($ramm) == 0 || !$ramm){
                    print("<td colspan='2'>Nessun compleanno in data odierna</td>");
                }else{
                    while ($amm = mysqli_fetch_assoc($ramm)) {
                        print("<td>" . $amm['cognome'] . " " . $amm['nome'] . "</td>");
                    }
                }
            ?>
            </tbody>
        </table>
    </div>

</center>
<?php

stampa_piede_new("");
mysqli_close($con);
