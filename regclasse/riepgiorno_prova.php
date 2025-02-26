<?php
// Inclusione dei file necessari
require_once '../lib/req_apertura_sessione.php';
require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
require_once '../lib/funregi.php';

// Controllo della sessione
$tipoutente = $_SESSION["tipoutente"];
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

// Variabili principali
$titolo = "Riepilogo registro di classe giornata";
$script = "<script type='text/javascript'>
           var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
           function Popup(apri) {
              window.open(apri, '', stile);
           }
           </script>";

// Chiamata alle funzioni per la stampa dell'intestazione e della testata
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$iddocente = $_SESSION['idutente'];
$giorno = stringa_html('giorno');
$mese = stringa_html('mese');
$anno = stringa_html('anno');
$idclasse = stringa_html('idclasse');

$_SESSION['prove'] = 'riepgiorno.php';
$_SESSION['regcl'] = $idclasse;
$_SESSION['reggi'] = $giorno;
$_SESSION['regma'] = $mese . "-" . $anno;
$_SESSION['classeregistro'] = $idclasse;

$gioattuale = date('d');
$meseattuale = date('m');
$annoattuale = date('Y');

if ($giorno == '') {
    $giorno = $gioattuale;
}
if ($mese == '') {
    $mese = $meseattuale;
}
if ($anno == '') {
    $anno = $annoattuale;
}

// Connessione al database
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

// Calcolo delle date
$dataoggi = "$anno-$mese-$giorno";
$datadomani = aggiungi_giorni($dataoggi, 1);
$dataieri = aggiungi_giorni($dataoggi, -1);
if (giorno_settimana($dataieri) == "Dom") {
    $dataieri = $_SESSION['giornilezsett'] == 6 ? aggiungi_giorni($dataieri, -1) : aggiungi_giorni($dataieri, -2);
}
if (giorno_settimana($datadomani) == "Dom" || (giorno_settimana($datadomani) == "Sab" && $_SESSION['giornilezsett'] == 5)) {
    $datadomani = $_SESSION['giornilezsett'] == 6 ? aggiungi_giorni($datadomani, 1) : aggiungi_giorni($datadomani, 2);
}
$gioieri = substr($dataieri, 8, 2);
$giodomani = substr($datadomani, 8, 2);
$maieri = substr($dataieri, 5, 2) . " - " . substr($dataieri, 0, 4);
$madomani = substr($datadomani, 5, 2) . " - " . substr($datadomani, 0, 4);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titolo; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><?php echo $titolo; ?></h1>
            <a href="../login/logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white text-center">
                <h2><?php echo $titolo; ?></h2>
            </div>
            <div class="card-body">
                <form method="post" action="riepgiorno.php" name="voti">
                    <div class="row mb-3">
                        <label for="giorno" class="col-sm-2 col-form-label"><b>Giorno</b></label>
                        <div class="col-sm-2">
                            <select class="form-select" name="giorno" id="giorno" required>
                                <?php
                                for ($i = 1; $i <= 31; $i++) {
                                    $selected = ($i == $giorno) ? 'selected' : '';
                                    echo "<option value='$i' $selected>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <label for="mese" class="col-sm-2 col-form-label"><b>Mese</b></label>
                        <div class="col-sm-2">
                            <select class="form-select" name="mese" id="mese" required>
                                <?php
                                for ($i = 1; $i <= 12; $i++) {
                                    $selected = ($i == $mese) ? 'selected' : '';
                                    echo "<option value='$i' $selected>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <label for="anno" class="col-sm-2 col-form-label"><b>Anno</b></label>
                        <div class="col-sm-2">
                            <select class="form-select" name="anno" id="anno" required>
                                <?php
                                for ($i = $annoattuale - 5; $i <= $annoattuale + 5; $i++) {
                                    $selected = ($i == $anno) ? 'selected' : '';
                                    echo "<option value='$i' $selected>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="idclasse" class="col-sm-4 col-form-label"><b>Classe</b></label>
                        <div class="col-sm-8">
                            <select class="form-select" id="idclasse" name="idclasse" required>
                                <option value="">&nbsp;</option>
                                <?php require '../lib/req_aggiungi_classi_a_select.php'; ?>
                            </select>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Visualizza</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <div class="d-inline-block me-3">
                    <?php if ($dataieri >= $_SESSION['datainiziolezioni']): ?>
                        <a href="riepgiorno.php?giorno=<?php echo $gioieri; ?>&mese=<?php echo substr($maieri, 0, 2); ?>&anno=<?php echo substr($maieri, 5, 4); ?>&idclasse=<?php echo $idclasse; ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left-circle"></i> Indietro
                        </a>
                    <?php endif; ?>
                </div>
                <div class="d-inline-block me-3">
                    <a href="riepgiorno.php?giorno=<?php echo $gioattuale; ?>&mese=<?php echo $meseattuale; ?>&anno=<?php echo $annoattuale; ?>&idclasse=<?php echo $idclasse; ?>" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-event"></i> Oggi
                    </a>
                </div>
                <div class="d-inline-block">
                    <?php if ($datadomani <= $_SESSION['datafinelezioni']): ?>
                        <a href="riepgiorno.php?giorno=<?php echo $giodomani; ?>&mese=<?php echo substr($madomani, 0, 2); ?>&anno=<?php echo substr($madomani, 5, 4); ?>&idclasse=<?php echo $idclasse; ?>" class="btn btn-outline-secondary">
                            Avanti <i class="bi bi-arrow-right-circle"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        if (!checkdate($mese, $giorno, $anno)) {
            echo "<div class='alert alert-danger text-center mt-3'><i class='bi bi-exclamation-circle'></i> Data non corretta! Verifica di aver inserito una data valida.</div>";
        } elseif (giorno_settimana($anno . "-" . $mese . "-" . $giorno) == "Dom") {
            echo "<div class='alert alert-warning text-center mt-3'><i class='bi bi-exclamation-triangle'></i> Il giorno selezionato è una domenica! Non ci sono lezioni programmate.</div>";
        } else {
            if ($idclasse != "") {
                $newdate = $anno . "-" . $mese . "-" . $giorno;
                if ($newdate >= $_SESSION['datainiziolezioni'] && $newdate <= $_SESSION['datafinelezioni'] && (!giorno_festa($newdate, $con))) {
                    stampa_reg_classe($newdate, $idclasse, $iddocente, $_SESSION['numeromassimoore'], $con, true, $_SESSION['gestcentrassenze'], $_SESSION['giustificauscite']);
                } elseif (giorno_festa($newdate, $con)) {
                    echo "<div class='alert alert-info text-center mt-3'><i class='bi bi-calendar-x'></i> " . data_italiana($newdate) . " - " . estrai_festa($newdate, $con) . "</div>";
                }
            }
        }
        mysqli_close($con);
        ?>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
