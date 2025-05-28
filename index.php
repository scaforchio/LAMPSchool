<?php

/**
 * Pagina principale dell'applicazione web LAMPSchool.
 *
 * @copyright  Copyright (C) 2015 Renato Tamilio. Pietro Tamburrano
 * @copyright  Copyright (C) 2023 Renato Tamilio, Pietro Tamburrano, Vittorio Lo Mele
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */
// include ("funzioni.php");

$elencoinstallazioni = array();
$elencoinstallazioni = elencafiles(".");

if (isset($_GET['suffisso']))
    $suffisso = $_GET['suffisso'];
else
    $suffisso = '*';

$scuole = array();
$anni = array();
$suffissi = array();

if(count($elencoinstallazioni) < 1){
    // se non ci sono installazioni, fai il redirect alla pagina di installazione
    header("Location: install/");
}

for ($i = 0; $i < count($elencoinstallazioni); $i++)
{
    $fileinclude = $elencoinstallazioni[$i];
    include($fileinclude);

    if ($con = mysqli_connect($db_server, $db_user, $db_password, $db_nome))
    {


        $query = "SELECT valore FROM " . $prefisso_tabelle . "tbl_parametri WHERE parametro='nome_scuola'";
        $ris = mysqli_query($con, $query) or die("Errore " . $query);
        $rec = mysqli_fetch_array($ris);
        if ($rec['valore'] != "Scuola XYZ")
        {
            $scuole[] = $rec['valore'];
            $suffissi[] = substr($fileinclude, 7, strlen($fileinclude) - 11);

            $query = "SELECT valore FROM " . $prefisso_tabelle . "tbl_parametri WHERE parametro='annoscol'";
            $ris = mysqli_query($con, $query) or die("Errore " . $query);
            $rec = mysqli_fetch_array($ris);
            $anni[] = $rec['valore'];
        }
    }
}

// ORDINAMENTO
for ($i = 0; $i < count($scuole) - 1; $i++)
    for ($j = $i + 1; $j < count($scuole); $j++)
        if ($scuole[$i] . $anni[$i] > $scuole[$j] . $anni[$j])
        {
            $temp = $scuole[$i];
            $scuole[$i] = $scuole[$j];
            $scuole[$j] = $temp;
            $temp = $anni[$i];
            $anni[$i] = $anni[$j];
            $anni[$j] = $temp;
            $temp = $suffissi[$i];
            $suffissi[$i] = $suffissi[$j];
            $suffissi[$j] = $temp;
        }


mysqli_close($con);

if ($suffisso != "*")
{
    for ($i = 0; $i < count($elencoinstallazioni); $i++)
    {
        if ($suffisso == $suffissi[$i])
        {
            header("location: login/login.php?suffisso=$suffisso");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="it" data-bs-theme="light">

<head>
    <script src="lib/js/themepicker.js"></script>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Inserimento dati di accesso</title>
    <link href="index.css" rel="stylesheet">
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
    <link rel="mask-icon" href="favicons/safari-pinned-tab.svg" color="#212529">
    <link rel="shortcut icon" href="favicons/favicon.ico">
    <meta name="msapplication-TileColor" content="#212529">
    <meta name="msapplication-config" content="favicons/browserconfig.xml">
    <meta name="theme-color" content="#212529">
</head>

<body>
    <main class="container">
        <div class="row justify-content-center" style="margin-top: 8%">
            <div class="col col-md-auto">
                <div class="card" >
                    <div class="card-body">
                        <h5 class="card-title" style="margin-bottom: 15px">
                            Seleziona la scuola
                        </h5>

                        <?php if (count($scuole) == 0) { ?>
                            <div class="alert alert-danger" role="alert">
                                Nessuna scuola installata
                            </div>
                        <?php } ?>

                        <?php if ($_GET['s'] == "1") { ?>
                            <div class="alert alert-danger" role="alert">
                                Sessione scaduta! Rieffettuare il login...
                            </div>
                        <?php } ?>

                        <?php for ($i = 0; $i < count($scuole); $i++) { ?>
                            <div class="card selector">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col col-auto">
                                            <img src="favicons/icon.png" alt="icona registro" width="40" height="40" />
                                        </div>

                                        <div class="col" style="padding-left: 0px">
                                            <p class="lh-80 del-margin">
                                                <span class="fs-5"><?php echo $scuole[$i]; ?></span>
                                                <br />
                                                <span class="fs-6 grey">A.S. <?php echo $anni[$i]; ?> / <?php echo $anni[$i] + 1; ?></span>
                                            </p>
                                        </div>

                                        <div class="col col-auto text-center">
                                            <button type="button" class="btn btn-outline-secondary" onclick="document.location.href = 'login/login.php?suffisso=<?php echo $suffissi[$i] ?>'">
                                                <i class="bi bi-door-open" style="margin-right: 0px"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="d-flex flex-wrap align-items-center py-3 border-top footer-justify">
        <p class="col col-auto mb-0 text-muted">
            <img src="favicons/icon.png" alt="icona registro" height="40">
            LampSchool ver. 2023
        </p> 
        <ul class="nav col col-auto justify-content-end footer2">
          
          <li class="nav-item hidewhen"><a href="https://github.com/scaforchio/lampschool/" class="nav-link px-2 text-muted">GitHub</a></li>
          <li class="nav-item"><a href="../login/info.php" class="nav-link px-2 text-muted">Crediti</a></li>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="flipTheme()" style="margin-left: 10px;">
            <i class="bi bi-circle-half" style="margin-right: 0px"></i>
          </button>
        </ul>
    </footer>
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php

//	


function elencafiles($dirname)
{
    $arrayfiles = array();
    if (file_exists($dirname))
    {

        $handle = opendir($dirname);
        while (false !== ($file = readdir($handle)))
        {
            if (substr($file, 0, 7) == "php-ini" && substr($file, strlen($file) - 4) == ".php")
            {
                array_push($arrayfiles, $file);
            }
        }
        closedir($handle);
    }
    sort($arrayfiles);
    return $arrayfiles;
}
