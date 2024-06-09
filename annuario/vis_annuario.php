<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2024 Vittorio Lo Mele
  Questo programma è un software libero; potete 
  redistribuirlo e/o modificarlo secondo i termini della
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
}

$titolo = "Visualizza annuario";
$script = "";
stampa_head_new($titolo, "", $script, "L");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> -  $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
    print("<h1>Connessione al server fallita</h1>");
}

$idcla = trim(stringa_html('idcla'));

if($idcla != "") {
    // ottieni dati classe
    $query = "SELECT anno, sezione, specializzazione, idfotoannuario FROM tbl_classi WHERE idclasse = $idcla";
    $ris = eseguiQuery($con, $query);

    $riga = mysqli_fetch_assoc($ris);
    $asp = $riga['anno'] . " " . $riga['sezione'] . " " . $riga['specializzazione'];
    ?> <center>
        <br>
        <h3>Annuario classe <?= $asp ?></h3>
        <br>
    </center> <?php

    if ($riga['idfotoannuario'] != 0) {
        // ottieni foto classe
        $query = "SELECT hash, didascalia FROM tbl_fotoannuario WHERE id_foto = " . $riga['idfotoannuario'];
        $ris = eseguiQuery($con, $query);
        $riga = mysqli_fetch_assoc($ris);
        $didascalia = $riga['didascalia'];
        $path = "../annuario/storage/a_" . $riga['hash'];

        ?>
        <center>
            <div class="card card-annuario">
                <a href="<?= $path; ?>" data-lightbox="annuario" data-title="<?= $asp ?>">
                    <img src="<?= $path ?>" class="card-img-top" alt="Foto annuario">
                </a>
                <?php if ($didascalia != "") { ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= $didascalia ?></h5>
                    </div>
                <?php } ?>
            </div>
        </center>
        <br>
        <hr>
        <br>
        <?php
    }

    // ottieni alunni
    $query = "SELECT idfotoannuario, nome, cognome FROM tbl_alunni WHERE idclasse = $idcla AND idfotoannuario != 0 ORDER BY cognome, nome";
    $ris = eseguiQuery($con, $query);

    ?>
    <div class="row justify-items-center" style="margin-left: 24px;">
    <?php

    while ($riga = mysqli_fetch_assoc($ris)) {
        $idfoto = $riga['idfotoannuario'];
        $nome = $riga['nome'];
        $cognome = $riga['cognome'];

        // ottieni foto
        $query = "SELECT hash, didascalia FROM tbl_fotoannuario WHERE id_foto = $idfoto";
        $ris2 = eseguiQuery($con, $query);
        $riga2 = mysqli_fetch_assoc($ris2);
        $didascalia = $riga2['didascalia'];
        $path = "../annuario/storage/a_" . $riga2['hash'];

        $nc = $nome . " " . $cognome;

        ?>
        <div class="col">
            <div class="card mb-4" style="width: 18rem;">
                <a href="<?= $path ?>" data-lightbox="annuario" data-title="<?= $nc ?>">
                    <img src="<?= $path ?>" class="card-img-top" alt="Foto annuario">
                </a>
                <div class="card-body">
                    <h5 class="card-title"><?= $nc ?></h5>
                    <?php if ($didascalia != "") { ?>
                        <p class="card-text"><?= $didascalia ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
    }

    ?>
    </div>
    <br>
    <br>
    <center>
    <a href="vis_annuario.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
        Torna alla lista delle classi
    </a>
    </center>
    <br>
    <br>
    <?php

} else {
    // seleziona tutte le classi che hanno un idfotoannuario diverso da 0 o non nullo e inseriscile in un array
    $query = "SELECT idclasse, anno, sezione, specializzazione FROM tbl_classi WHERE idfotoannuario != 0  ORDER BY anno, sezione, specializzazione";
    $classi = array();
    $ris = eseguiQuery($con, $query);
    while ($riga = mysqli_fetch_assoc($ris)) {
        array_push(
            $classi, 
            array(
                "id" => $riga['idclasse'],
                "text" => $riga['anno'] . " " . $riga['sezione'] . " " . $riga['specializzazione'],
            )
        );
    }

    // seleziona ogni alunno di ogni classe, verifica che abbia una foto
    // se l'id classe non è già presente nell'array cerca la classe e inseriscila comunque

    $query = "SELECT idclasse, nome, cognome FROM tbl_alunni WHERE idfotoannuario != 0";
    $ris = eseguiQuery($con, $query);

    while ($riga = mysqli_fetch_assoc($ris)) {
        $idcla = $riga['idclasse'];
        $trovato = false;
        foreach ($classi as $classe) {
            if ($classe['id'] == $idcla) {
                $trovato = true;
                break;
            }
        }

        if (!$trovato) {
            $query = "SELECT anno, sezione, specializzazione FROM tbl_classi WHERE idclasse = $idcla";
            $ris2 = eseguiQuery($con, $query);
            $riga2 = mysqli_fetch_assoc($ris2);
            array_push(
                $classi, 
                array(
                    "id" => $idcla,
                    "text" => $riga2['anno'] . " " . $riga2['sezione'] . " " . $riga2['specializzazione'],
                )
            );
        }
    }

    ?>
    <div class="row ml-3">
        <div class="row ml-3" style="
            margin-left: 0px;
            margin-right: 0px;
            margin-top: 5px;
        ">
        <?php
        foreach ($classi as $classe) {
            ?>
            <div class="col col-auto">
                <a href="vis_annuario.php?idcla=<?= $classe['id']; ?>" class="btn btn-outline-secondary btn-lg btn-block">
                    <?= $classe['text']; ?>
                </a>
            </div>
            <?php
        }
        ?>
        </div>
    </div>
    <?php
}

mysqli_close($con);
stampa_piede_new("");
