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
                    <span><?php echo $funzione; ?></span>
                    <div>
                        <div class="dropdown">
                            <a aria-expanded="false" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" href="#">
                                <img alt="foto profilo" class="rounded-circle me-2" height="32" src="//www.gravatar.com/avatar/<?php echo md5($descrizione) ?>/?d=retro" width="32" />
                                <strong><?php echo $descrizione ?></strong>
                            </a>
                            <ul class="dropdown-menu text-small shadow">

                                <li>
                                    <a class="dropdown-item">
                                        <i class="bi bi-bank"></i>
                                        <?php echo $_SESSION['nome_scuola'] ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item">
                                        <i class="bi bi-calendar-fill"></i>
                                        A.S. <?php echo $_SESSION['annoscol']."/".$_SESSION['annoscol']+1 ?>
                                    </a>
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
                                    <a class="dropdown-item">
                                        <i class="bi bi-bank"></i>
                                        <?php echo $_SESSION['nome_scuola'] ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item">
                                        <i class="bi bi-calendar-fill"></i>
                                        A.S. <?php echo $_SESSION['annoscol']."/".$_SESSION['annoscol']+1 ?>
                                    </a>
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

function import_datatables(){ ?>
    <script type='text/javascript' src='../vendor/components/jquery/jquery.min.js'></script>
    <script type='text/javascript' src='../vendor/datatables.net/datatables.net/js/jquery.dataTables.min.js'></script>
    <script type='text/javascript' src='../vendor/datatables.net/datatables.net-bs5/js/dataTables.bootstrap5.min.js'></script>
    <script type='text/javascript' src='../vendor/datatables.net/datatables.net-responsive/js/dataTables.responsive.min.js'></script>
    <script type='text/javascript' src='../vendor/datatables.net/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js'></script>
    <link rel='stylesheet' type='text/css' href='../vendor/datatables.net/datatables.net-bs5/css/dataTables.bootstrap5.min.css'/>
    <link rel='stylesheet' type='text/css' href='../vendor/datatables.net/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'/>
    <style>
        .dataTables_length{
            margin-bottom: 10px;
        }
        .dataTables_filter{
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