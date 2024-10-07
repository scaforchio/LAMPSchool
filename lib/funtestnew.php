<?php

function stampa_head_new($titolo, $tipo, $script, $abil = "DSPMATL", $contr = true, $token = true, $onload = "")
{
    $_SESSION['tempotrascorso'] = 0;

    if ($contr) {
        controllo_privilegi($abil);
    }
    if ($token) {
        ob_start();
    }
?>

    <!DOCTYPE html>
    <html lang='it'>

    <head data-bs-theme='light'>
        <script src='../lib/js/themepicker.js'></script>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title><?php echo $titolo; ?></title>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <link rel='apple-touch-icon' sizes='180x180' href='../favicons/apple-touch-icon.png'>
        <link rel='icon' type='image/png' sizes='32x32' href='../favicons/favicon-32x32.png'>
        <link rel='icon' type='image/png' sizes='16x16' href='../favicons/favicon-16x16.png'>
        <link rel='manifest' href='../favicons/site.webmanifest'>
        <link rel='mask-icon' href='../favicons/safari-pinned-tab.svg' color='#212529'>
        <link rel='shortcut icon' href='../favicons/favicon.ico'>
        <meta name='msapplication-TileColor' content='#212529'>
        <meta name='msapplication-config' content='../favicons/browserconfig.xml'>
        <meta name='theme-color' content='#212529'>
        <link rel='stylesheet' type='text/css' href='../lib/unico.css' />
        <link rel='stylesheet' href='../vendor/twbs/bootstrap/dist/css/bootstrap.min.css' />
        <link rel='stylesheet' href='../vendor/twbs/bootstrap-icons/font/bootstrap-icons.min.css' />
        <link rel="stylesheet" href="../lib/js/lightbox/css/lightbox.min.css">
        <script src="../vendor/components/jquery/jquery.min.js"></script>
        <style>
            .spa {
                margin-right: 5px;
            }
        </style>
        <?php

        print "<script>window.onload=function(){";
        if (basename($_SERVER['PHP_SELF']) != 'login.php') {
            print "refreshSn();";
        }
        $upddaeseguire = version_compare($_SESSION['versione'], $_SESSION['versioneprecedente'], ">");
        if ($upddaeseguire) print " updatedb();";
        print $onload;
        print "};</script>";

        ?>
        <script>
            var refreshSn = function() {
                var time = 300000; // 5 mins
                setTimeout(
                    function() {
                        $.ajax({
                            url: '../lib/refresh.php',
                            cache: false,
                            complete: function() {
                                refreshSn();
                            }
                        });
                    },
                    time
                );
            }
            var updatedb = function() {

                $.ajax({
                    url: '../lib/updatedb.php?suffisso=<?php echo $_SESSION['suffisso']; ?>',
                    cache: false,

                });

            };

            document.addEventListener("DOMContentLoaded", function(){
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                var popoverList = popoverTriggerList.map(function(element){
                return new bootstrap.Popover(element);
                });
            });
        </script>

        <?php
        print $script;
        print "</head>";
    }

    function stampa_testata_new($funzione, $ct, $ns, $cs, $isProfileSelector = false)
    {
        $annoscolastico = 'A.S. ' . $_SESSION['annoscol'] . " / " . ($_SESSION['annoscol'] + 1);
        $nome = str_replace(".php", "", basename($_SERVER['PHP_SELF']));

        $tipoutente = '';

        if (isset($_SESSION['tipoutente'])) {
            $tipoutente = $_SESSION['tipoutente'];
        }

        $descrizione = "";

        $urlCorrente = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $urlProfili = $_SESSION["oidc_redirect_uri"] . "/login/oidclogin.php?suffisso=" . $_SESSION["suffisso"];

        if ($_SESSION["oidc-step2"]) {
            $descrizione .= "SSO: ";
        }

        if ($isProfileSelector) {
            $descrizione .= $_SESSION["oidc_fullname"];
        } else {
            if ($tipoutente == 'D' | $tipoutente == 'P' | $tipoutente == 'S' | $tipoutente == 'A') // doc pres staff amm
            {
                $descrizione .= $_SESSION['cognome'] . " " . $_SESSION['nome'];
            } elseif ($tipoutente == 'T') {
                $descrizione .= 'Tutore alunno ' . $_SESSION['cognome'] . " " . $_SESSION['nome'];
            } elseif ($tipoutente == 'L') {
                $descrizione .= 'Alunno ' . $_SESSION['cognome'] . " " . $_SESSION['nome'];
            } elseif ($tipoutente == 'M') {
                $descrizione .= 'Admin';
            } elseif ($tipoutente == 'E') {
                $descrizione .= 'ESAMI DI STATO';
            } else {
                if ($_SESSION["oidc-step2"]) {
                    $impacc = $_SESSION["oidc_issuer"] . "/account?referrer=";
                    $impacc .= $_SESSION["oidc_client_id"] . "&referrer_uri=" . urlencode($urlCorrente);
                    $descrizione .= $_SESSION["oidc_fullname"];
                } else {
                    $descrizione .= 'Ospite';
                }
            }
        }

        print "\n<body>";

        if ($nome != 'login') {
            $warn = false;
            $ro = false;
            $devmode = false;
            $unikey = false;

            if (isset($_SESSION['sola_lettura']) && $_SESSION['sola_lettura'] == 'yes') {
                $warn = true;
                $ro = true;
            }

            if (isset($_SESSION['devmode']) && $_SESSION['devmode'] == true) {
                $warn = true;
                $devmode = true;
            }

            if (isset($_SESSION['accessouniversale']) && $_SESSION['accessouniversale'] == true) {
                $warn = true;
                $unikey = true;
            }

        ?>

            <header class="p-3 border-bottom mb-3" id="lsheader">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="funzione"><?php echo $funzione; ?></span>
                    <a href="../login/ele_ges.php" class="funzionemenu fsbig" sidebarjs-toggle><i class="bi bi-arrow-left"></i></a>
                    <div>
                        <div class="dropdown">
                            <a aria-expanded="false" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" href="#">
                                <img alt="foto profilo" class="rounded-circle me-2" height="32" src="//www.gravatar.com/avatar/<?php echo md5($descrizione) ?>/?d=retro" width="32" />
                                <strong><?php echo $descrizione ?></strong>
                            </a>
                            <ul class="dropdown-menu text-small shadow">

                                <li>
                                    <h6 class="dropdown-item">
                                        <i class="bi bi-bank"></i>
                                        <?php echo $_SESSION['nome_scuola'] ?>
                                    </h6>
                                </li>
                                <li>
                                    <h6 class="dropdown-item">
                                        <i class="bi bi-calendar-fill"></i>
                                        A.S. <?php echo $_SESSION['annoscol'] ?> / <?php echo $_SESSION['annoscol'] + 1 ?>
                                    </h6>
                                </li>

                                <?php if ($devmode) { ?>
                                    <li>
                                        <a class="dropdown-item">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <b>Registro in modalità di sviluppo</b>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($ro) { ?>
                                    <li>
                                        <a class="dropdown-item">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <b>Registro in modalità sola lettura</b>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($unikey) { ?>
                                    <li>
                                        <a class="dropdown-item">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <b>Accesso effettuato con chiave universale</b>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($_SESSION['alias']) { ?>
                                    <li>
                                        <a class="dropdown-item">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <b>Alias altro utente attivo</b>
                                        </a>
                                    </li>
                                <?php } ?>

                                <li>
                                    <hr class="dropdown-divider" />
                                </li>

                                <li>
                                    <a class="dropdown-item" target="_blank" href="http://www.lampschool.net/help/help.php?modulo=<?php echo $nome; ?>&tipoutente=<?php echo $tipoutente; ?>">
                                        <i class="bi bi-question-octagon"></i>
                                        Documentazione
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item alt-theme" onclick="flipTheme()">
                                        <i class="bi bi-circle-half"></i>
                                        Cambia Tema
                                    </a>
                                </li>

                                <?php if ($_SESSION['tipoutente'] == "L") { ?>
                                    <li>
                                        <a class="dropdown-item" href="../alunni/matricola.php">
                                            <i class="bi bi-upc-scan"></i>
                                            Matricola
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($devmode) { ?>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick='si()'>
                                            <i class="bi bi-code"></i>
                                            Session Inspector
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($_SESSION["oidc_multiprofile"]) { ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo $urlProfili ?>">
                                            <i class="bi bi-person-lines-fill"></i>
                                            Cambia Profilo
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($_SESSION["oidc-step2"]) { ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo $impacc ?>">
                                            <i class="bi bi-person-fill-gear"></i>
                                            Impostazioni Profilo
                                        </a>
                                    </li>
                                <?php } ?>


                                <li>
                                    <hr class="dropdown-divider" />
                                </li>

                                <li>
                                    <a class="dropdown-item" href="../login/login.php?suffisso=<?php echo get_suffisso() ?>&logout=true">
                                        <i class="bi bi-box-arrow-right"></i>
                                        Esci
                                    </a>
                                </li>

                                <?php if ($_SESSION['alias']) { ?>
                                    <li>
                                        <a class="dropdown-item" href="../contr/cambiautenteritorno.php">
                                            <i class="bi bi-person-x-fill"></i>
                                            Esci da Alias
                                        </a>
                                    </li>
                                <?php } ?>

                            </ul>
                        </div>
                        <div style="margin-right: 8px;"></div>
                    </div>
                </div>
            </header>

        <?php

        }

        print "<main class='lscontainer'>";
    }

    function stampa_testata_ges_new($funzione, $ct, $ns, $cs, $isProfileSelector = false)
    {
        $annoscolastico = 'A.S. ' . $_SESSION['annoscol'] . " / " . ($_SESSION['annoscol'] + 1);
        $nome = str_replace(".php", "", basename($_SERVER['PHP_SELF']));

        $tipoutente = '';

        if (isset($_SESSION['tipoutente'])) {
            $tipoutente = $_SESSION['tipoutente'];
        }

        $descrizione = "";

        $urlCorrente = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $urlProfili = $_SESSION["oidc_redirect_uri"] . "/login/oidclogin.php?suffisso=" . $_SESSION["suffisso"];

        if ($_SESSION["oidc-step2"]) {
            $descrizione .= "SSO: ";
        }

        if ($isProfileSelector) {
            $descrizione .= $_SESSION["oidc_fullname"];
        } else {
            if ($tipoutente == 'D' | $tipoutente == 'P' | $tipoutente == 'S' | $tipoutente == 'A') // doc pres staff amm
            {
                $descrizione .= $_SESSION['cognome'] . " " . $_SESSION['nome'];
            } elseif ($tipoutente == 'T') {
                $descrizione .= 'Tutore alunno ' . $_SESSION['cognome'] . " " . $_SESSION['nome'];
            } elseif ($tipoutente == 'L') {
                $descrizione .= 'Alunno ' . $_SESSION['cognome'] . " " . $_SESSION['nome'];
            } elseif ($tipoutente == 'M') {
                $descrizione .= 'Admin';
            } elseif ($tipoutente == 'E') {
                $descrizione .= 'ESAMI DI STATO';
            } else {
                if ($_SESSION["oidc-step2"]) {
                    $impacc = $_SESSION["oidc_issuer"] . "/account?referrer=";
                    $impacc .= $_SESSION["oidc_client_id"] . "&referrer_uri=" . urlencode($urlCorrente);
                    $descrizione .= $_SESSION["oidc_fullname"];
                } else {
                    $descrizione .= 'Ospite';
                }
            }
        }

        if ($nome != 'login') {
            $warn = false;
            $ro = false;
            $devmode = false;
            $unikey = false;

            if (isset($_SESSION['sola_lettura']) && $_SESSION['sola_lettura'] == 'yes') {
                $warn = true;
                $ro = true;
            }

            if (isset($_SESSION['devmode']) && $_SESSION['devmode'] == true) {
                $warn = true;
                $devmode = true;
            }

            if (isset($_SESSION['accessouniversale']) && $_SESSION['accessouniversale'] == true) {
                $warn = true;
                $unikey = true;
            }

        ?>

            <header class="p-3 border-bottom mb-3" id="lsheader">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="funzione"><?php echo $funzione; ?></span>
                    <span class="funzionemenu fsbig" sidebarjs-toggle><i class="bi bi-list"></i></span>
                    <div>
                        <div class="dropdown">
                            <a aria-expanded="false" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" href="#">
                                <img alt="foto profilo" class="rounded-circle me-2" height="32" src="//www.gravatar.com/avatar/<?php echo md5($descrizione) ?>/?d=retro" width="32" />
                                <strong><?php echo $descrizione ?></strong>
                            </a>
                            <ul class="dropdown-menu text-small shadow">

                                <li>
                                    <h6 class="dropdown-item">
                                        <i class="bi bi-bank"></i>
                                        <?php echo $_SESSION['nome_scuola'] ?>
                                    </h6>
                                </li>
                                <li>
                                    <h6 class="dropdown-item">
                                        <i class="bi bi-calendar-fill"></i>
                                        A.S. <?php echo $_SESSION['annoscol'] ?> / <?php echo $_SESSION['annoscol'] + 1 ?>
                                    </h6>
                                </li>

                                <?php if ($devmode) { ?>
                                    <li>
                                        <a class="dropdown-item">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <b>Registro in modalità di sviluppo</b>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($ro) { ?>
                                    <li>
                                        <a class="dropdown-item">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <b>Registro in modalità sola lettura</b>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($unikey) { ?>
                                    <li>
                                        <a class="dropdown-item">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <b>Accesso effettuato con chiave universale</b>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($_SESSION['alias']) { ?>
                                    <li>
                                        <a class="dropdown-item">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <b>Alias altro utente attivo</b>
                                        </a>
                                    </li>
                                <?php } ?>

                                <li>
                                    <hr class="dropdown-divider" />
                                </li>

                                <li>
                                    <a class="dropdown-item" target="_blank" href="http://www.lampschool.net/help/help.php?modulo=<?php echo $nome; ?>&tipoutente=<?php echo $tipoutente; ?>">
                                        <i class="bi bi-question-octagon"></i>
                                        Documentazione
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item alt-theme" onclick="flipTheme()">
                                        <i class="bi bi-circle-half"></i>
                                        Cambia Tema
                                    </a>
                                </li>

                                <?php if ($_SESSION['tipoutente'] == "L") { ?>
                                    <li>
                                        <a class="dropdown-item" href="../alunni/matricola.php">
                                            <i class="bi bi-upc-scan"></i>
                                            Visualizza Matricola
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($devmode) { ?>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick='si()'>
                                            <i class="bi bi-code"></i>
                                            Session Inspector
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($_SESSION["oidc_multiprofile"]) { ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo $urlProfili ?>">
                                            <i class="bi bi-person-lines-fill"></i>
                                            Cambia Profilo
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($_SESSION["oidc-step2"]) { ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo $impacc ?>">
                                            <i class="bi bi-person-fill-gear"></i>
                                            Impostazioni Profilo
                                        </a>
                                    </li>
                                <?php } ?>


                                <li>
                                    <hr class="dropdown-divider" />
                                </li>

                                <li>
                                    <a class="dropdown-item" href="../login/login.php?suffisso=<?php echo get_suffisso() ?>&logout=true">
                                        <i class="bi bi-box-arrow-right"></i>
                                        Esci
                                    </a>
                                </li>

                                <?php if ($_SESSION['alias']) { ?>
                                    <li>
                                        <a class="dropdown-item" href="../contr/cambiautenteritorno.php">
                                            <i class="bi bi-person-x-fill"></i>
                                            Esci da Alias
                                        </a>
                                    </li>
                                <?php } ?>

                            </ul>
                        </div>
                        <div style="margin-right: 8px;"></div>
                    </div>
                </div>
            </header>

        <?php

        }
    }

    function import_datatables()
    { ?>
        <script type='text/javascript' src='../vendor/components/jquery/jquery.min.js'></script>
        <script type='text/javascript' src='../vendor/datatables.net/datatables.net/js/dataTables.min.js'></script>
        <script type='text/javascript' src='../vendor/datatables.net/datatables.net-bs5/js/dataTables.bootstrap5.min.js'></script>
        <script type='text/javascript' src='../vendor/datatables.net/datatables.net-responsive/js/dataTables.responsive.min.js'></script>
        <script type='text/javascript' src='../vendor/datatables.net/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js'></script>
        <link rel='stylesheet' type='text/css' href='../vendor/datatables.net/datatables.net-bs5/css/dataTables.bootstrap5.min.css' />
        <link rel='stylesheet' type='text/css' href='../vendor/datatables.net/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css' />
        <style>
            .dataTables_length {
                margin-bottom: 10px;
            }

            .dataTables_filter {
                margin-bottom: 10px;
            }
        </style>
    <?php }


    function stampa_piede_new($ver = '', $csrf = false)   // Gestione token disabilitata
    {
        $vers = 'LAMPSchool Ver. ' . $_SESSION['versioneprecedente'];
    ?>
        </main>
        <footer class="d-flex flex-wrap footer-justify align-items-center py-3 border-top">
            <p class="col-md-4 mb-0 text-muted">
                <img src="../favicons/icon.png" alt="icona registro" height="40"></a>
                <?php echo $vers; ?>
            </p>

            <ul class="nav col-md-4 justify-content-end footer2">
                <li class="nav-item hidewhen"><a href="http://lampschool.net" class="nav-link px-2 text-muted">Sito Web</a></li>
                <li class="nav-item hidewhen"><a href="https://github.com/scaforchio/lampschool/" class="nav-link px-2 text-muted">GitHub</a></li>
                <li class="nav-item"><a href="../login/info.php" class="nav-link px-2 text-muted">Crediti</a></li>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="flipTheme()" style="margin-left: 10px;">
                    <i class="bi bi-circle-half" style="margin-right: 0px"></i>
                </button>
            </ul>
        </footer>

        <script src="../vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        </script>
        <script src="../lib/js/lightbox/js/lightbox.min.js"></script>

        <?php
        modal_censimento_new();
        // se devmode attiva inietta script session inspector
        if (isset($_SESSION['devmode']) && $_SESSION['devmode'] == true) {
            // session inspector
            $datisessionejs = json_encode($_SESSION);
        ?>
            <script>
                const SESSION_INSPECTOR_DATA = `<?php echo $datisessionejs ?>`;
                // https://stackoverflow.com/questions/4810841/pretty-print-json-using-javascript
                function syntaxHighlight(json) {
                    if (typeof json != 'string') {
                        json = JSON.stringify(json, undefined, 2);
                    }
                    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
                        var cls = 'number';
                        if (/^"/.test(match)) {
                            if (/:$/.test(match)) {
                                cls = 'key';
                            } else {
                                cls = 'string';
                            }
                        } else if (/true|false/.test(match)) {
                            cls = 'boolean';
                        } else if (/null/.test(match)) {
                            cls = 'null';
                        }
                        return '<span class="' + cls + '">' + match + '</span>';
                    });
                }

                function si() {
                    // genera finestra esterna
                    var obj = JSON.parse(SESSION_INSPECTOR_DATA);
                    var str = JSON.stringify(obj, undefined, 4);
                    var win = window.open("", "LampSchool - Session Inspector", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=780,top=" + (screen.height - 400) + ",left=" + (screen.width - 840));
                    win.document.body.appendChild(document.createElement('style')).innerText = `
                    pre {outline: 1px solid #ccc; padding: 5px; margin: 5px; }
                    .string { color: green; }
                    .number { color: darkorange; }
                    .boolean { color: blue; }
                    .null { color: magenta; }
                    .key { color: red; }
                `;
                    win.document.body.appendChild(document.createElement('pre')).innerHTML = syntaxHighlight(str);
                }
            </script>
        <?php
        }

        print("</body></html>");

        // Gestione del token

        if ($csrf) {
            csrfguard_start();
        }
    }

    function stampa_piede_ges_new($ver = '', $csrf = false)
    {
        $vers = 'LAMPSchool Ver. ' . $_SESSION['versioneprecedente'];
        ?>
        <footer class="d-flex flex-wrap footer-justify align-items-center py-3 border-top">
            <p class="col-md-4 mb-0 text-muted">
                <img src="../favicons/icon.png" alt="icona registro" height="40"></a>
                <?php echo $vers; ?>
            </p>

            <ul class="nav col-md-4 justify-content-end footer2">
                <li class="nav-item hidewhen"><a href="http://lampschool.net" class="nav-link px-2 text-muted">Sito Web</a></li>
                <li class="nav-item hidewhen"><a href="https://github.com/scaforchio/lampschool/" class="nav-link px-2 text-muted">GitHub</a></li>
                <li class="nav-item"><a href="../login/info.php" class="nav-link px-2 text-muted">Crediti</a></li>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="flipTheme()" style="margin-left: 10px;">
                    <i class="bi bi-circle-half" style="margin-right: 0px"></i>
                </button>
            </ul>
        </footer>

        <script src="../vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

        <?php
        // se devmode attiva inietta script session inspector
        if (isset($_SESSION['devmode']) && $_SESSION['devmode'] == true) {
            // session inspector
            $datisessionejs = json_encode($_SESSION);
        ?>
            <script>
                const SESSION_INSPECTOR_DATA = `<?php echo $datisessionejs ?>`;
                // https://stackoverflow.com/questions/4810841/pretty-print-json-using-javascript
                function syntaxHighlight(json) {
                    if (typeof json != 'string') {
                        json = JSON.stringify(json, undefined, 2);
                    }
                    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
                        var cls = 'number';
                        if (/^"/.test(match)) {
                            if (/:$/.test(match)) {
                                cls = 'key';
                            } else {
                                cls = 'string';
                            }
                        } else if (/true|false/.test(match)) {
                            cls = 'boolean';
                        } else if (/null/.test(match)) {
                            cls = 'null';
                        }
                        return '<span class="' + cls + '">' + match + '</span>';
                    });
                }

                function si() {
                    // genera finestra esterna
                    var obj = JSON.parse(SESSION_INSPECTOR_DATA);
                    var str = JSON.stringify(obj, undefined, 4);
                    var win = window.open("", "LampSchool - Session Inspector", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=780,top=" + (screen.height - 400) + ",left=" + (screen.width - 840));
                    win.document.body.appendChild(document.createElement('style')).innerText = `
                    pre {outline: 1px solid #ccc; padding: 5px; margin: 5px; }
                    .string { color: green; }
                    .number { color: darkorange; }
                    .boolean { color: blue; }
                    .null { color: magenta; }
                    .key { color: red; }
                `;
                    win.document.body.appendChild(document.createElement('pre')).innerHTML = syntaxHighlight(str);
                }
            </script>
        <?php
        }

        print("</body></html>");

        // Gestione del token

        if ($csrf) {
            csrfguard_start();
        }
    }

    function alert($title, $sub = "", $severity = "secondary", $icon = "info-circle")
    {
        if ($sub == "") {
            print("<div class='alert alert-$severity' role='alert'> <i class='bi bi-$icon' style='margin-right: 8px;'></i> $title </div>");
        } else {
            print("<div class='alert alert-$severity' role='alert'> <i class='bi bi-$icon' style='margin-right: 8px;'></i> <b>$title</b> <hr> <p class='mb-0'> $sub </p> </div>");
        }
    }

    function modal_censimento()
    { ?>
        <div id="modcens" class="modal-ce">
            <div class="modal-content-ce">
                <span class="close-ce">×</span>
                <br>
                <br>

                <div class="modgen"> Comunicazione dei dati sull'andamento... </div>
                <div class="modgen">Padre: <b id="p1">--</b></div>
                <div class="modgen">Madre: <b id="m1">--</b></div>
                <hr>

                <div class="modgen"> Accesso al Registro Elettronico... </div>
                <div class="modgen">Padre: <b id="p2">--</b></div>
                <div class="modgen">Madre: <b id="m2">--</b></div>
                <hr>

                <div class="modgen"> Comunicazione SMS assenze, ritardi, uscite... </div>
                <div class="modgen">Padre: <b id="p3">--</b></div>
                <div class="modgen">Madre: <b id="m3">--</b></div>
                <hr>

                <div class="modgen"> Partecipazione ai colloqui... </div>
                <div class="modgen">Padre: <b id="p4">--</b></div>
                <div class="modgen">Madre: <b id="m4">--</b></div>
                <hr>

                Se i dati non sono aggiornati ricarica la pagina! <br>
            </div>
        </div>
        <div id="modbar" class="modal-ce">
            <div class="modal-content-ce">
                <span class="close-ce">×</span>
                <br>
                <div><img alt='barcode' id='barcod' src=''/></div> <br>
                <span style="font-size: 23px;" id='barcodtext'></span>
            </div>
        </div>
        <script>
            var modal = document.getElementById("modcens");
            var span = document.getElementsByClassName("close-ce")[0];

            var modal2 = document.getElementById("modbar");
            var span2 = document.getElementsByClassName("close-ce")[1];

            function barcode(text) {
                modal2.style.display = "block";
                document.getElementById("barcod").setAttribute("src", `../lib/genbarcode.php?data=${text}`);
                document.getElementById("barcodtext").innerHTML = text;
            }

            function cens(conf) {
                modal.style.display = "block";
                console.log(conf);

                sww(conf.charAt(0), "1");
                sww(conf.charAt(1), "2");
                sww(conf.charAt(2), "3");
                sww(conf.charAt(3), "4");
            }

            function sww(letter, number){
                switch (letter) {
                    case "N":
                        document.getElementById("p" + number).innerHTML = "NO";
                        document.getElementById("m" + number).innerHTML = "NO";
                        break;

                    case "P":
                        document.getElementById("p" + number).innerHTML = "SI";
                        document.getElementById("m" + number).innerHTML = "NO";
                        break;

                    case "M":
                        document.getElementById("p" + number).innerHTML = "NO";
                        document.getElementById("m" + number).innerHTML = "SI";
                        break;

                    case "E":
                        document.getElementById("p" + number).innerHTML = "SI";
                        document.getElementById("m" + number).innerHTML = "SI";
                        break;
                
                    default:
                        document.getElementById("p" + number).innerHTML = "??";
                        document.getElementById("m" + number).innerHTML = "??";
                }
            }

            span.onclick = function() {
                modal.style.display = "none";
            }

            span2.onclick = function() {
                modal2.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                    modal2.style.display = "none";
                }
            }
        </script>
    <?php }

    function modal_censimento_new() { ?>
        <div class="modal fade" id="censimentoModal" tabindex="-1" aria-labelledby="censimentoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="censimentoModalLabel">
                            Stato censimento
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            Alunno: <b id="modalCensimentoAlunno">--</b> <br>
                            Se i dati non sono aggiornati ricarica la pagina!
                        </p>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Servizio</th>
                                    <th scope="col">Padre</th>
                                    <th scope="col">Madre</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Comunicazione dei dati sull'andamento</td>
                                    <td id="modalCensimentoPadre1">--</td>
                                    <td id="modalCensimentoMadre1">--</td>
                                </tr>
                                <tr>
                                    <td>Accesso al Registro Elettronico</td>
                                    <td id="modalCensimentoPadre2">--</td>
                                    <td id="modalCensimentoMadre2">--</td>
                                </tr>
                                <tr>
                                    <td>Comunicazione SMS assenze, ritardi, uscite</td>
                                    <td id="modalCensimentoPadre3">--</td>
                                    <td id="modalCensimentoMadre3">--</td>
                                </tr>
                                <tr>
                                    <td>Partecipazione ai colloqui</td>
                                    <td id="modalCensimentoPadre4">--</td>
                                    <td id="modalCensimentoMadre4">--</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="noteModalLabel">
                            Visualizza annotazione
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            Alunno: <b id="noteModalAlunno">--</b> <br>
                            <span id="noteModalNota"></span>
                        </p>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="barcodeModalLabel">
                            Codice a barre matricola
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            Alunno: <b id="barcodeModalAlunno">--</b> <br>
                        </p>
                        <center>
                            <img alt='barcode' class="barcode" id='barcodeModalImg' src=''/> <br>
                            <span style="font-size: 23px;" id='barcodeModalText'></span>
                        </center>
                        <br>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var censimentoModal = new bootstrap.Modal($('#censimentoModal'));
            var noteModal = new bootstrap.Modal($('#noteModal'));
            var barcodeModal = new bootstrap.Modal($('#barcodeModal'));

            function note_new(nota, nome) {
                $('#noteModalAlunno').html(nome);
                $('#noteModalNota').html(nota);
                noteModal.show();
            }

            function barcode_new(text, nome) {
                $('#barcodeModalAlunno').html(nome);
                $('#barcodeModalImg').attr("src", `../lib/genbarcode.php?data=${text}`);
                $('#barcodeModalText').html(text);
                barcodeModal.show();
            }

            function cens_new(conf, nome) {
                sww_new(conf.charAt(0), "1");
                sww_new(conf.charAt(1), "2");
                sww_new(conf.charAt(2), "3");
                sww_new(conf.charAt(3), "4");

                $('#modalCensimentoAlunno').html(nome);

                censimentoModal.show();
            }

            function sww_new(letter, number){
                switch (letter) {
                    case "N":
                        document.getElementById("modalCensimentoPadre" + number).innerHTML = "NO";
                        document.getElementById("modalCensimentoMadre" + number).innerHTML = "NO";
                        break;

                    case "P":
                        document.getElementById("modalCensimentoPadre" + number).innerHTML = "SI";
                        document.getElementById("modalCensimentoMadre" + number).innerHTML = "NO";
                        break;

                    case "M":
                        document.getElementById("modalCensimentoPadre" + number).innerHTML = "NO";
                        document.getElementById("modalCensimentoMadre" + number).innerHTML = "SI";
                        break;

                    case "E":
                        document.getElementById("modalCensimentoPadre" + number).innerHTML = "SI";
                        document.getElementById("modalCensimentoMadre" + number).innerHTML = "SI";
                        break;
                
                    default:
                        document.getElementById("modalCensimentoPadre" + number).innerHTML = "??";
                        document.getElementById("modalCensimentoMadre" + number).innerHTML = "??";
                }
            }
        </script>
    <?php }

    function mod_cens_stili()
    {
        print("
    .modal-ce {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content-ce {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 20%; 
    }

    .close-ce {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .modgen {
        border: 1px solid black;
        margin-bottom: 5px;
    }

    .button-eme {
        display: block;
        text-align: center;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        line-height: 21px;
        color: black;
        background-color: #00ff0087; 
        width: 100%;
    }

    .close-ce:hover,
    .close-ce:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }");
}

function insCodiceClientModalAnnuario() { ?>
<div class="modal fade" id="fotoAnnuarioModal" tabindex="-1" aria-labelledby="fotoAnnuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="fotoAnnuarioLabel">
                    Gestione foto annuario
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="fam_form">
                    <p>
                        <b>Tipo Foto:</b> <span id="fam_tipoFoto">--</span> <br>
                        <b>Caricando per:</b> <span id="fam_caricandoPer" >--</span> <br>
                        <hr>
                        Seleziona foto: <input class="form-control mb-2 mt-2" type="file" id="fam_file" accept="image/*" />
                        Didascalia</b>: <input class="form-control mt-2" type="text" id="fam_didascalia" />
                    </p>
                    <div style="display: flex; flex-direction: row; justify-content: end;">
                        <button type="button" class="btn btn-outline-secondary" onclick="salvaFotoAnnuario()" id="tasto_salvataggio">
                            <i class="bi bi-floppy"></i>
                            Salva Foto
                        </button>
                        <div class="spinner-border text-secondary" role="status" id="fidget_spinner">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div id="fam_view">
                    <img src="" id="fam_img" class="img-fluid mb-3" />
                    <center>
                        <i id="fam_dida"></i>
                    </center>
                    <hr>
                    <div style="display: flex; flex-direction: row; justify-content: end;">
                        <button type="button" class="btn btn-outline-danger" onclick="cancellaFotoAnnuario()" id="tasto_eliminazione">
                            <i class="bi bi-trash"></i>
                            Elimina Foto
                        </button>
                        <div class="spinner-border text-secondary" role="status" id="fidget_spinner2">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div id="fam_err" class="alert alert-danger">
                    <b>Codice errore:</b> <span id="fam_err_code">--</span> <br>
                    <b>Messaggio:</b> <span id="fam_err_msg">--</span>
                </div>
                <div id="fam_succ" class="alert alert-success">
                    Operazione completata con successo!
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    let UPLOAD_DATA = {};
    let DELETION_ID = {};

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    function modal_foto_annuario(idd, tipo, nome) {
        $("#fam_form").hide();
        $("#fam_view").hide();
        $("#fam_err").hide();
        $("#fam_succ").hide();
        $("#fidget_spinner").hide();
        $("#fidget_spinner2").hide();

        $.post("../annuario/metadata.php", 
            { 
                id: idd,
                tipoFoto: tipo
            })
            .done(function(response) {
                $("#fam_view").show();
                $("#fam_img").attr("src", "../annuario/get_pic.php?hash=" + response.data.hash);
                $("#fam_dida").html(response.data.didascalia);
                $("#fotoAnnuarioModal").modal("show");
                DELETION_ID = response.data.id;
            })
            .fail(function(error) {
                if(error.status == 404) {
                    $("#fam_form").show();
                    $("#fam_tipoFoto").html(capitalizeFirstLetter(tipo));
                    $("#fam_caricandoPer").html(nome);
                    $("#fotoAnnuarioModal").modal("show");

                    $("#fam_file").val("");
                    $("#fam_didascalia").val("");

                    UPLOAD_DATA = {
                        id: idd,
                        tipoFoto: tipo
                    };
                } else {
                    $("#fam_err_code").html(error.status);
                    $("#fam_err_msg").html(error.responseJSON.data);
                    $("#fam_err").show();
                    $("#fotoAnnuarioModal").modal("show");
                }
            });
    }

    function salvaFotoAnnuario() {
        let file = $("#fam_file")[0].files[0];
        let didascalia = $("#fam_didascalia").val();

        if(!file) {
            alert("Devi selezionare un file da caricare!");
            return;
        }

        $("#fidget_spinner").show();
        $("#tasto_salvataggio").hide();

        let formData = new FormData();
        formData.append("image", file);
        formData.append("caption", didascalia);
        formData.append("id", UPLOAD_DATA.id);
        formData.append("tipoFoto", UPLOAD_DATA.tipoFoto);

        $.ajax({
            url: "../annuario/upload.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $("#fidget_spinner").hide();
                $("#tasto_salvataggio").show();
                $("#fam_succ").show();
                $("#fam_form").hide();
                $("#fam_view").hide();
            },
            error: function(error) {
                $("#fidget_spinner").hide();
                $("#tasto_salvataggio").show();
                $("#fam_err_code").html(error.status);
                $("#fam_err_msg").html(error.responseJSON.data);
                $("#fam_err").show();
                $("#fam_form").hide();
                $("#fam_view").hide();
            }
        });
    }

    function cancellaFotoAnnuario() {
        if(!confirm("Sei sicuro di voler eliminare questa foto?")) {
            return;
        }

        $("#fidget_spinner2").show();
        $("#tasto_eliminazione").hide();

        $.post("../annuario/delete.php", 
            { 
                id: DELETION_ID
            })
            .done(function(response) {
                $("#fidget_spinner2").hide();
                $("#tasto_eliminazione").show();
                $("#fam_succ").show();
                $("#fam_form").hide();
                $("#fam_view").hide();
            })
            .fail(function(error) {
                $("#fidget_spinner2").hide();
                $("#tasto_eliminazione").show();
                $("#fam_err_code").html(error.status);
                $("#fam_err_msg").html(error.responseJSON.data);
                $("#fam_err").show();
                $("#fam_form").hide();
                $("#fam_view").hide();
            });
    }

    function foto_classe(idclasse, nome) {
        modal_foto_annuario(idclasse, "classe", nome);
    }

    function foto_alunno(idalunno, nome) {
        modal_foto_annuario(idalunno, "alunno", nome);
    }
</script>
<?php }
