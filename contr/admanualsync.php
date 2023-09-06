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

$titolo = "Forza sincronizzazione AD";

stampa_head($titolo, "", "", "M");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

$query = "SELECT * FROM `tbl_utenti` WHERE `tipo` = 'L' OR `tipo` = 'S' OR `tipo` = 'D'";
$ris = eseguiQuery($con, $query);

$numero = mysqli_num_rows($ris);
$coda = array();

print('<div style="margin-left: 20px; font-family: monospace, monospace;">');

while ($row = mysqli_fetch_assoc($ris)){

    $utentee = $row['userid'];
    $wifi = $row['wifi'];
    $gruppo = "";
    $nome = "";
    $cognome = "";

    if($row['tipo'] == 'L'){
        $gruppo = $_SESSION['adgroup_alunni'];
        // cerca studente 
        $idstudente = $row["idutente"] - 2100000000;
        $querystu = "SELECT `cognome`, `nome` FROM `tbl_alunni` WHERE `idalunno` = $idstudente";
        $risstu = eseguiQuery($con, $querystu);
        if(mysqli_num_rows($risstu) != 1){
            print("<span style='color: red;'>Corrispondenza anagrafica non trovata o duplicata per alunno <b>$utentee</b></span><br>");
            continue;
        }
        $stu = mysqli_fetch_assoc($risstu);
        $nome = $stu['nome'];
        $cognome = $stu['cognome'];
    }else {
        // cerca docente
        $gruppo = $_SESSION['adgroup_docenti'];
        $iddocente = $row["idutente"];
        $querydoc = "SELECT `cognome`, `nome` FROM `tbl_docenti` WHERE `iddocente` = $iddocente";
        $risdoc = eseguiQuery($con, $querydoc);
        if(mysqli_num_rows($risdoc) != 1){
            print("<span style='color: red;'>Corrispondenza anagrafica non trovata o duplicata per docente <b>$utentee</b></span><br>");
            continue;
        }
        $doc = mysqli_fetch_assoc($risdoc);
        $nome = $doc['nome'];
        $cognome = $doc['cognome'];
    }

    queueCreateUpdateOperation(
        $coda, 
        $utentee,
        $nome,
        $cognome,
        $wifi,
        $gruppo
    );

    print("Aggiunto in coda utente <b>$utentee</b> corr. anagrafica <b>$nome $cognome</b> con gruppo <b>$gruppo</b> e accesso wifi <b>$wifi</b><br>");
}

print("<hr>");

$bh = $_SESSION['broker_host'];
$bp = $_SESSION['broker_port'];
$bu = $_SESSION['broker_user'];
$bt = $_SESSION['broker_topic'];
print("Invio coda al broker <b>$bu@$bh:$bp</b> su topic <b>$bt</b><br>");

try {
    sendQueueToBroker($coda, $bh, $bp, $bu, $_SESSION['broker_pass'], $bt);
    print("<span style='color: green;'>Coda inviata con successo al broker, riferirsi al sync.log dei server di destinazione!<br>");
} catch (Exception $e) {
    $message = $e->getMessage();
    print("<span style='color: red;'>Operazione non riuscita: <br><div style='margin-left: 20px;'>$message</div></span><br>");
}

print("</div>");

stampa_piede("");
mysqli_close($con);
