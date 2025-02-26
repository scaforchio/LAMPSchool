<?php

/**
 * Created by PhpStorm.
 * User: pietro
 * Date: 16/06/15
 * Time: 18.29
 */

/**
 * Stampa il registro di classe per giornata
 *
 * @param string $data
 * @param int $idclasse
 * @param int $iddocente
 * @param int $numoremax
 * @param object $conn Connessione al db
 */

function stampa_reg_classe($data, $idclasse, $iddocente, $numoremax, $conn, $stampacollegamenti = true, $gestcentrassenze = 'no', $giustificauscite = 'no') {
    $gio = substr($data, 8, 2);
    $mese = substr($data, 5, 2) . " - " . substr($data, 0, 4);
    $gotoPage = $_SERVER['PHP_SELF'];

    // Intestazione della tabella
    echo "<div class='container mt-4'>";
    echo "<div class='text-center mb-3'><h5>Classe: " . decodifica_classe($idclasse, $conn) . " - Data: " . data_italiana($data) . " - " . giorno_settimana($data) . "</h5></div>";

    $cattedra = codice_cattedra($iddocente, $idclasse, 0, $conn);
    $elencoalunni = estrai_alunni_classe_data($idclasse, $data, $conn);

    if (strlen($elencoalunni) == 0) {
        echo "<div class='alert alert-warning text-center'>Nessun alunno iscritto in questa data nella classe!</div>";
        return;
    }

    if ($stampacollegamenti && $data <= date('Y-m-d')) {
        if (!is_docente_classe($iddocente, $idclasse, $conn) && !is_docente_sostegno_classe($iddocente, $idclasse, $conn)) {
            echo "<div class='text-center mb-3'><a href='../lezioni/lezsupp.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&meseanno=$mese&cattedra=$cattedra' class='btn btn-outline-primary'>Supplenze</a></div>";
        } else {
            echo "<div class='text-center mb-3'><a href='../lezioni/lez.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&meseanno=$mese' class='btn btn-outline-primary'>Lezioni</a> <a href='../lezioni/lezsupp.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&meseanno=$mese&cattedra=$cattedra' class='btn btn-outline-secondary'>Supplenze</a></div>";
        }
    }

    // Creazione della tabella
    echo "<table class='table table-bordered table-striped'>
            <thead class='table-dark'>
                <tr>
                    <th scope='col' class='text-center'>Ora</th>
                    <th scope='col'>Materia</th>
                    <th scope='col'>Docenti</th>
                    <th scope='col'>Argomenti svolti</th>
                </tr>
            </thead>
            <tbody>";

    $numeroorecomp = calcola_numero_ore($data, $idclasse, $conn);

    for ($no = 1; $no <= $numoremax; $no++) {
        $colore = "text-dark";

        echo "<tr>";
        echo "<td class='text-center'>$no</td>";

        // Query per ora e materia
        $query = "SELECT idlezione, denominazione, tbl_materie.idmateria, idlezionegruppo FROM tbl_lezioni, tbl_materie 
                  WHERE tbl_lezioni.idmateria = tbl_materie.idmateria 
                  AND datalezione = '$data' 
                  AND idclasse = '$idclasse' 
                  AND $no >= orainizio AND $no <= (orainizio + numeroore - 1)";
        $ris = eseguiQuery($conn, $query);
        $numrighe = mysqli_num_rows($ris);

        if ($numrighe > 1) {
            $colore = "text-success";
            while ($rec = mysqli_fetch_array($ris)) {
                if ($rec['idlezionegruppo'] == null) {
                    $colore = "text-danger";
                }
            }
        }
        mysqli_data_seek($ris, 0); // Reset cursore

        echo "<td class='$colore'>";
        while ($rec = mysqli_fetch_array($ris)) {
            $idlezione = $rec['idlezione'];
            if (esiste_cattedra($idlezione, $iddocente, $conn)) {
                echo "<a href='../lezioni/lez.php?goback=$gotoPage&idlezione=$idlezione&provenienza=registro&idlezionegruppo=" . $rec['idlezionegruppo'] . "' class='text-decoration-none $colore'>" . $rec['denominazione'] . "</a><br>";
            } else {
                echo "<span class='$colore'>" . $rec['denominazione'] . "</span><br>";
            }
        }
        echo "</td>";

        // Docenti
        $query = "SELECT cognome, nome FROM tbl_firme, tbl_lezioni, tbl_docenti 
                  WHERE tbl_firme.idlezione = tbl_lezioni.idlezione 
                  AND tbl_firme.iddocente = tbl_docenti.iddocente 
                  AND datalezione = '$data' 
                  AND idclasse = '$idclasse' 
                  AND $no >= orainizio AND $no <= (orainizio + numeroore - 1)";
        $ris = eseguiQuery($conn, $query);
        echo "<td class='$colore'>";
        while ($rec = mysqli_fetch_array($ris)) {
            echo $rec['cognome'] . " " . $rec['nome'] . "<br>";
        }
        echo "</td>";

        // Argomenti
        $query = "SELECT argomenti, attivita, numeroore, orainizio 
                  FROM tbl_lezioni 
                  WHERE datalezione = '$data' 
                  AND idclasse = '$idclasse' 
                  AND $no >= orainizio AND $no <= (orainizio + numeroore - 1)";
        $ris = eseguiQuery($conn, $query);
        echo "<td class='$colore'>";
        while ($rec = mysqli_fetch_array($ris)) {
            if ($no == $rec['orainizio']) {
                echo $rec['argomenti'] . " " . $rec['attivita'] . "<br>";
            }
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";

    // Collegamenti extra
    if ($stampacollegamenti && $data <= date('Y-m-d')) {
        echo "<div class='text-center mt-3'>";
        echo "<a href='../assenze/ass.php?goback=$gotoPage&cl=$idclasse&gio=$gio&meseanno=$mese&idclasse=$idclasse' class='btn btn-outline-info'>Assenze</a> ";
        echo "<a href='../note/notecl.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&mese=$mese' class='btn btn-outline-info'>Note di classe</a> ";
        echo "<a href='../regclasse/annotaz.php?goback=$gotoPage&idclasse=$idclasse&gio=$gio&mese=$mese' class='btn btn-outline-info'>Annotazioni</a>";
        echo "</div>";
    }
    echo "</div>";
}

function esiste_lezione($data, $con) {

    $query = "select idlezione from tbl_lezioni where datalezione='$data'";

    $ris = eseguiQuery($con, $query);

    if (mysqli_num_rows($ris) > 0)
        return true;
    else
        return false;
}

function esiste__assenza($data, $con) {

    $query = "select * from tbl_assenze where data='$data'";

    $ris = eseguiQuery($con, $query);

    if (mysqli_num_rows($ris) > 0)
        return true;
    else
        return false;
}
