<?php

/**
 * Stampa l'intestazione html con css e javascript comuni all'applicativo
 *
 * @param string $titolo il titolo della pagina
 * @param string $tipo (non usato)
 * @param string $script javascript da aggiungere eventualmente
 */
function stampa_head($titolo, $tipo, $script, $abil = "DSPMATL", $contr = true, $token = true)
{
    if ($contr)
    {
        controllo_privilegi($abil);

    }
// Per la gestione del token
// memorizza nel buffer tutto il codice inviati al client
    if ($token)
    {
        ob_start();
    }

    print "<!DOCTYPE html>
           <html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
            <title>$titolo</title>
            <link rel='stylesheet' type='text/css' href='../css/stile" . get_suffisso() . ".css' />
            <link rel='icon' href='../immagini/favicon.ico?v=2' />
            <link rel='stylesheet' type='text/css' href='../lib/js/jquery-ui-1.10.3.smoothness.css' />
            <link rel='stylesheet' type='text/css' href='../lib/js/datetimepicker/jquery.datetimepicker.css'/>

            <script src='../lib/js/jquery-1.10.2.min.js'></script>
            <script src='../lib/js/jquery-ui-1.10.3.custom.min.js'></script>
            <script src='../lib/js/datetimepicker/jquery.datetimepicker.js'></script>";
    //<script src='ui.datepicker-it.js'></script>";
    print $script;
    print "
</head>";
}


/*
* Per restringere l'accesso ad una determinata pagina si può:
* 1 - aggiungere come quarto parametro della funzione stampa_head() una stringa contenente i profili abilitati
*     Es. "MA"=Admin e Impiegati -  "DS"=Docenti e Staff
*     M - Adimn
*     A - Impiegato
*     T - Genitore o tutor
*     D - Docente
*     S - Staff
*     P - Preside
*
* In mancanza del parametro o con il quinto parametro a false il controllo non verrà effettuato. Quest'ultimo caso è
* utilizzato nella funzione di login dove il tipo di utente non è ancora conosciuto.
*
* 2 - aggiungere alla pagina il richiamo di controllo_privilegi con un parametro stringa come specificato sopra
*
*
*
*/


/**
 * Funzione che stampa l'intestazione della pagina
 *
 * @param string $funzione
 * @param string $ct
 * @param string $ns
 * @param string $cs
 */
function stampa_testata($funzione, $ct, $ns, $cs)
{ //,$ab="DSPM") {


    $annoscolastico = 'A.S. ' . $_SESSION['annoscol'] . " / " . ($_SESSION['annoscol'] + 1);
    $nome = str_replace(".php", "", basename($_SERVER['PHP_SELF']));

    $tipoutente = '';

    if (isset($_SESSION['tipoutente']))
    {
        $tipoutente = $_SESSION['tipoutente'];
    }


    $descrizione = '';

    if ($tipoutente == 'D' | $tipoutente == 'P' | $tipoutente == 'S' | $tipoutente == 'A')
    {
        $descrizione .= $_SESSION['cognome'] . " " . $_SESSION['nome'];
    }
    elseif ($tipoutente == 'T')
    {
        $descrizione .= 'Tutore alunno ' . $_SESSION['cognome'] . " " . $_SESSION['nome'];
    }
    elseif ($tipoutente == 'L')
    {
        $descrizione .= 'Alunno ' . $_SESSION['cognome'] . " " . $_SESSION['nome'];
    }
    elseif ($tipoutente == 'M')
    {
        $descrizione .= 'Admin';
    }
    elseif ($tipoutente == 'E')
    {
        $descrizione .= 'ESAMI DI STATO';
    }
    else
    {
        $descrizione .= 'Ospite';
    }


    if ($_SESSION['alias'])
    {
        $descrizione .= " (<a href='../contr/cambiautenteritorno.php'>Esci da ALIAS</a>)";
    }

    print "\n<body><div class='contenuto'>";

    if ($nome != 'login')
    {
        print "<div class='logout' align='right'>$descrizione <a href='../login/login.php?suffisso=" . get_suffisso() . "'><img src='../immagini/logout.png' title='Logout'></a></div>\n";
    }
    print "<div id='testata'>";
    $label = "";


    if (isset($_SESSION['sola_lettura']) && $_SESSION['sola_lettura'] == 'yes')
    {
        $label = " <br><font color='red'>(SOLA LETTURA)</font>";
    }

    print "<div class='titolo'>REGISTRO ON LINE$label</div>\n";
    print "<div class='sottotitolo'>$ns<br/>$annoscolastico</div></div>\n";
    print "<div id='help'><a href='http://www.lampschool.net/help/help.php?modulo=$nome&tipoutente=$tipoutente' target='_blank'><img src='../immagini/help.png' title='HELP'></a></div>\n";
    print "<div id='funzione'>$funzione</div><br/>\n";

}

/**
 * Stampa il pedice della pagina
 *
 * @param string $ver se viene valorizzato
 * @param boolean $csrf gestione CSRF abilitata
 */

// function stampa_piede($ver = '', $csrf = true) // Gestione token abilitata
function stampa_piede($ver = '', $csrf = false)   // Gestione token disabilitata
{
    $vers = 'LAMPSchool ';

    if (strlen($ver) > 0)
    {
        $vers .= 'Ver. ' . $ver;
    }
    else
    {
        if (isset($_SESSION['versione']))
        {
            $vers .= 'Ver. ' . $_SESSION['versione'];
        }
    }

    print("
   <br/></div>
   <div id='piede'>
      <a href='../login/info.php' target='_blank'><img src='../immagini/lstrasp.gif'></a>
      $vers
      <a href='http://www.gnu.org/licenses/agpl-3.0.txt' target='_blank'><img src='../immagini/agplv3.png'></a>
   </div>
</body>
</html>");

    // Gestione del token

    if ($csrf)
    {
        csrfguard_start();
    }
}


/**
 * Conversione in html del valore del parametro presente nel GET e/o nel POST
 * Prevenzione da un attacco XSS e SQL injection
 *
 * @param string $parametro contenuto nel metodo della form GET e/o POST
 * @param boolean $doppiapici se true nel metodo elimina_apici sostituisce anche il carattere "
 * @param string $metodo il valore 'P' per $_POST, 'G' $_GET, ''
 * @return string valore con le entità html
 */
function stringa_html($parametro = '', $doppiapici = true, $metodo = '')
{
    $stringa = '';
    $matrice = '';

    
    if ($metodo == 'G')
    {
        $matrice = $_GET;
    }
    elseif ($metodo == 'P')
    {
        $matrice = $_POST;
    }
    else
    {
        $matrice = array_merge($_POST, $_GET);
    }

    if (count($matrice))
    {

        if (isset($matrice[$parametro]))
        {
            // vengono convertiti il carattere ' (per evitare SQL injection) e "

            if (!is_array($matrice[$parametro]))
            {
                $stringa = elimina_apici($matrice[$parametro], $doppiapici);
            }
            else
            {
                // Per la gestione dei parametri di tipo SELECT html

                foreach ($matrice[$parametro] as $key => $value)
                {
                    $stringa = elimina_apici($matrice[$parametro], $doppiapici);
                }
                $stringa = $matrice[$parametro]; // restituisce la matrice
            }
        }
    }

    return $stringa;
}

/**
 * Conversione in html del valore del parametro presente nel GET
 * Prevenzione da un attacco XSS e SQL injection
 *
 * @param string $parametro contenuto nel metodo GET della form
 * @param boolean $doppiapici se true nel metodo elimina_apici sostituisce anche il carattere "
 * @return string valore con le entità html
 */
function stringa_get_html($parametro = '', $doppiapici = true)
{
    return stringa_html($parametro, $doppiapici, 'G');
}

/**
 * Conversione in html del valore del parametro presente nel POST
 * Prevenzione da un attacco XSS e SQL injection
 *
 * @param string $parametro contenuto nel metodo POST della form
 * @param boolean $doppiapici se true nel metodo elimina_apici sostituisce anche il carattere "
 * @return string valore con le entità html
 */
function stringa_post_html($parametro = '', $doppiapici = true)
{
    return stringa_html($parametro, $doppiapici, 'P');
}

/**
 * true se il valore del parametro è diverso da stringa ''
 *
 * @param stringa $parametro
 * @return boolean
 */
function is_stringa_html($parametro = '')
{
    $valore = FALSE;
    $tmp = stringa_html($parametro);

    if (!is_array($tmp))
    {
        $valore = strlen($tmp) > 0;
    }
    else
    {
        $valore = count($tmp);
    }

    return $valore;
}

/**
 * Genera un token di 128 bit
 *
 * @param string $unique_form_name
 * @return string il token codificato
 */
function csrfguard_generate_token($unique_form_name, $tokenSize = 128)
{
    if (function_exists("hash_algos") and in_array("sha512", hash_algos()))
    {
        $token = hash("sha512", mt_rand(0, mt_getrandmax()));
    }
    else
    {
        $token = ' ';

        for ($i = 0; $i < $tokenSize; ++$i)
        {
            $r = mt_rand(0, 35);

            if ($r < 26)
            {
                $c = chr(ord('a') + $r);
            }
            else
            {
                $c = chr(ord('0') + $r - 26);
            }
            $token .= $c;
        }
    }
    $_SESSION[$unique_form_name] = $token;

    return $token;
}

/**
 * Controllo della validità del token
 *
 * @param string $unique_form_name
 * @param string $token_value
 * @return boolean
 */
function csrfguard_validate_token($unique_form_name, $token_value)
{
    $token = $_SESSION[$unique_form_name];

    if ($token === false)
    {
        return true;
    }
    elseif ($token === $token_value)
    {
        $result = true;
    }
    else
    {
        $result = false;
    }
    $_SESSION[$unique_form_name] = ' ';
    unset($_SESSION[$unique_form_name]);

    // Elimina gli altri token in sessione

    foreach ($_SESSION as $k => $v)
    {

        if (strpos($k, "token") !== false)
        {
            $_SESSION[$k] = ' ';
            unset($_SESSION[$k]);
        }
    }

    return $result;
}

/**
 * Inserisce dopo il tag form i campi hidden tokenId e token
 *
 * @param string $form_data_html
 * @return string
 */
function csrfguard_replace_forms($form_data_html, $arrayToken)
{
    $pattern = "/<form(.*?)>/is";
    $count = preg_match_all($pattern, $form_data_html, $matches, PREG_SET_ORDER);

    if (is_array($matches))
    {
        foreach ($matches as $m)
        {
            if (strpos($m[1], "nocsrf") !== false)
            {
                continue;
            }
            $name = $arrayToken[0];
            $token = $arrayToken[1];
            $form_data_html = str_replace($m[0], "<form$m[1]>
                                                    <input type='hidden' name='tokenId' value='$name' />
                                                    <input type='hidden' name='token' value='$token' />", $form_data_html);
        }
    }
    return $form_data_html;
}

/**
 * Inserisce dopo il nome della pagina .php i campi hidden tokenId e token
 *
 * @param string $link_data_html
 * @return string
 */
function csrfguard_replace_links($link_data_html, $arrayToken)
{
    $pattern = "/<a href='[^']*'/is";
    $count = preg_match_all($pattern, $link_data_html, $matches, PREG_SET_ORDER);

    if (is_array($matches))
    {
        foreach ($matches as $m)
        {
            if (strpos($m[0], "login.php") !== false
                or strpos($m[0], "help.php") !== false
                or strpos($m[0], "info.php") !== false
                or strpos($m[0], ".php") == false
            )
            {
                continue;
            }

            $name = $arrayToken[0];
            $token = $arrayToken[1];
            $lenLink = strlen($m[0]);
            $oldLink = substr($m[0], 0, $lenLink - 1);
            $newLink = "$oldLink?tokenId={$name}&token={$token}'";

            if (strpos($m[0], ".php?") !== false)
            {
                $newLink = "$oldLink&tokenId={$name}&token={$token}'";
            }

            $link_data_html = str_replace($m[0], $newLink, $link_data_html);
        }
    }
    return $link_data_html;
}

/**
 * Inserimento degli elementi per la gestione del token
 */
function csrfguard_inject()
{
    $data = ob_get_clean();
    $arrayToken = generate_link_token(); // un solo token per la pagina corrente
    $data = csrfguard_replace_forms($data, $arrayToken);
    echo csrfguard_replace_links($data, $arrayToken);
}

/**
 * Gestione del token solo se il metodo della form è di tipo POST
 */
function csrfguard_start()
{

    $pagina = str_replace(".php", "", basename($_SERVER['PHP_SELF']));

    if ($pagina != 'login')
    {
        $name = stringa_html('tokenId');
        $token = stringa_html('token');

        if (strlen($name) == 0 or strlen($token) == 0)
        {
            header('location: ../login/login.php?messaggio=Token non trovato&suffisso=' . get_suffisso());
            die;
        }

        if (!csrfguard_validate_token($name, $token))
        {
            header('location: ../login/login.php?messaggio=Token non corretto&suffisso=' . get_suffisso());
            die;
        }
    }
    ob_start();
    register_shutdown_function('csrfguard_inject');
    csrfguard_inject();
}

/**
 * Generazione del token per il form (<form/>)
 *
 * @return array contiene il nome e il valore del token
 */
function generate_form_token()
{
    $tokenId = "token" . mt_rand(0, mt_getrandmax());
    $token = csrfguard_generate_token($tokenId);

    return array($tokenId, $token);
}

/**
 * Generazione del token per il link (<a href/>)
 *
 * @param int dimensione del token
 * @return string il token codificato
 */
function generate_link_token($length = 32)
{
    static $chars = '0123456789abcdef';
    $max = strlen($chars) - 1;
    $token = '';
    $name = session_name();

    for ($i = 0; $i < $length; ++$i)
    {
        $token .= $chars[(rand(0, $max))];
    }
    $tokenId = "token" . mt_rand(0, mt_getrandmax());
    $token = md5($token . $name);
    $_SESSION[$tokenId] = $token;

    return array($tokenId, $token);
}

/**
 * Restituisce il link per tornare indietro nel riepilogo registro di classe
 *
 * @return string Link con i parametri
 */
function goBackRiepilogoRegistro($conn = null, $label = 'Riepilogo registro', $sost = '')
{
    $goback = stringa_html('goback');
    $linkIntero = $goback;

    if (strlen($goback) > 0)
    {
        $gbIdclasse = stringa_html('idclasse');

        if (strlen(stringa_html('idclasse')) == 0)
        {
            $gbIdclasse = stringa_html('cl');
        }
        $gbGiorno = stringa_html('gio');
        $gbMeseanno = stringa_html('meseanno');
        if (strlen(stringa_html('meseanno')) == 0)
        {
            $gbMeseanno = stringa_html('mese');
        }
        if (strlen(stringa_html('idlezione') > 0))  // Ricavo i dati nel caso si abbia l'id della lezione
        {
            // ricavo i dati dalla lezione
            if ($sost == '')
            {
                $query = "SELECT idclasse,datalezione FROM tbl_lezioni WHERE idlezione=" . stringa_html('idlezione');
                $ris = mysqli_query($conn, inspref($query)) or die("Errore: " . inspref($query, false));
                $rec = mysqli_fetch_array($ris);
                $gbIdclasse = $rec['idclasse'];
                $gbGiorno = substr($rec['datalezione'], 8, 2);
                $gbMeseanno = substr($rec['datalezione'], 5, 2) . " - " . substr($rec['datalezione'], 0, 4);
            }
            else
            {
                $query = "SELECT idclasse,datalezione FROM tbl_lezionicert WHERE idlezione=" . stringa_html('idlezione');
                $ris = mysqli_query($conn, inspref($query)) or die("Errore: " . inspref($query, false));
                $rec = mysqli_fetch_array($ris);
                $gbIdclasse = $rec['idclasse'];
                $gbGiorno = substr($rec['datalezione'], 8, 2);
                $gbMeseanno = substr($rec['datalezione'], 5, 2) . " - " . substr($rec['datalezione'], 0, 4);
            }

        }
        // print "tttt $gbIdclasse $gbGiorno $gbMeseanno";
        $linkIntero = " - <a href='$goback?cl=$gbIdclasse&idclasse=$gbIdclasse&gio=$gbGiorno&meseanno=$gbMeseanno'>$label</a> ";
    }
    return array($goback, $linkIntero);
}

function get_suffisso()
{
    if (isset($_SESSION['suffisso']))
    {
        return $_SESSION['suffisso'];
    }
    else
    {
        return '';
    }
}

function controllo_privilegi($abil)
{
    if (isset($_SESSION['tipoutente']))
    {
        $tipoutente = $_SESSION['tipoutente'];
    }
    if (!strstr($abil, $tipoutente))
    {
        header("location: ../login/login.php?suffisso=" . get_suffisso() . "&messaggio=Accesso non consentito alla funzione richiesta");
        die;
    }

}


/**
 * Sostituisce il prefisso tbl_ con il prefisso scelto al momento dell'installazione
 * Inserisce nel log i comandi
 *
 * @param string $comando query da eseguire
 * @return string
 */
function inspref($comando, $log = true)
{
    global $db_server;
    global $db_nome;
    global $db_user;
    global $db_password;
    global $prefisso_tabelle;

    $comando = str_replace("tbl_", $prefisso_tabelle . "tbl_", $comando);
    if ($log)
    {
        if (isset($_SESSION['log']))
        {
            if ($_SESSION['log'] == "yes")
            {
                $comandoreg = $comando;
                $cmd = strtolower(substr($comandoreg, 0, 4));

                if ($cmd == "inse" | $cmd == "upda" | $cmd == "dele" | $cmd == "INSE" | $cmd == "UPDA" | $cmd == "DELE")
                {
                    // Non effettuo registrazione completa per caricamento file pianilavoro
                    if (startsWith($comandoreg, "INSERT INTO " . $prefisso_tabelle . "tbl_documenti"))
                    {
                        $comandoreg = "Inserimento documento in db";
                    }
                    if (get_suffisso() != "")
                    {
                        $suff = get_suffisso() . "/";
                    }
                    else $suff = "";

                    $comandoreg = elimina_spazi($comandoreg);
                    inserisci_log($_SESSION['userid'] . "§" . date('m-d|H:i:s') . "§" . $_SESSION['indirizzoip'] . "§" . $comandoreg . "");
                    //$query = "insert into ". $prefisso_tabelle. "tbl_logacc(utente,dataacc,comando) values ('".
                    //           $_SESSION['userid']. "','". date('m/d - H:i:s'). "','". elimina_apici($comandoreg). "')";
                    //$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore di connessione");
                    //$ris = mysqli_query($con,$query) or die("Errore in inserimento log!");
                    //mysqli_close($con);
                }
            }

            if ($_SESSION['log'] == "all")
            {
                $comandoreg = $comando;
                $cmd = strtolower(substr($comandoreg, 0, 4));

                if ($cmd == "inse" | $cmd == "upda" | $cmd == "dele" | $cmd == "sele"
                    | $cmd == "INSE" | $cmd == "UPDA" | $cmd == "DELE" | $cmd == "SELE"
                )
                {
                    // Non effettuo registrazione completa per caricamento documenti in db
                    if (startsWith($comandoreg, "INSERT INTO " . $prefisso_tabelle . "tbl_documenti") && ($gestionedocumenti == 'db'))
                    {
                        $comandoreg = "Inserimento documento in db";
                    }
                    if (get_suffisso() != "")
                    {
                        $suff = get_suffisso() . "/";
                    }
                    else $suff = "";

                    $comandoreg = elimina_spazi($comandoreg);
                    inserisci_log($_SESSION['userid'] . "§" . date('m-d|H:i:s') . "§" . $_SESSION['indirizzoip'] . "§" . $comandoreg . "");
                    // $query = "insert into ". $prefisso_tabelle. "tbl_logacc(utente, dataacc, comando) values ('".
                    //            $_SESSION['userid']. "','". date('m/d - H:i:s'). "','". elimina_apici($comandoreg). "')";
                    //	$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore di connessione");
                    // $ris = mysqli_query($con, $query) or die("Errore in inserimento log!");
                    // mysqli_close($con);
                }
            }
        }
    }
    // VERIFICO SE IL REGISTRO E' IN MODALITA' SOLA LETTURA
    //$cmd = strtolower(substr($comando, 0, 4));
    //if ($cmd == "inse" | $cmd == "upda" | $cmd == "dele")
    if (stripos(" insedeleupda", substr($comando, 0, 4)) > 0)
    {

        if (isset($_SESSION['sola_lettura']) && ($_SESSION['sola_lettura'] == 'yes'))
        {
            $comando = "select 'ciao!'";
        }
    }


    return $comando;
}

/**
 * Inserisce nel log i comandi
 *
 * @param string $testo
 *
 */
function inserisci_log($testo, $nflog = "", $suff = "")
{
    // I parametri nflog e suff devono essere passati alla funzione in caso di utilizzo
    // in programmi sessionless (inserimento timbrature, accesso da app android, visualizzazione
    // programmi svolti, confrema invio SMS ecc.)


    $nomefilelog = ($nflog == "") ? $_SESSION['nomefilelog'] : $nflog;
    if ($suff == "")
    {
        if (get_suffisso() != "")
        {
            $suff = get_suffisso() . "/";
        }
        else $suff = "";
    }

    // print "../lampschooldata/" . $suff . "00$nomefilelog.log";
    error_log($testo . "\n", 3, "../lampschooldata/" . $suff . "0000$nomefilelog". date("Ymd") .".log");


}


/**
 * Legge il contenuto del file richiesto
 *
 * @param string $url del file json
 * @return oggetto JSON
 */
function leggeFileJSON($url)
{
    $content = file_get_contents($url);
    $json = json_decode($content, true);

    return $json;
}

/**
 * Controllo l'esistenza di una nuova versione di LAMPSchool
 * prerequisiti:
 * il file locale lampschool.json, il file remoto e $_SESSION['versione']
 *
 * @return boolean true se la versione indicata nel file recuperato dall'indirizzo
 * contenuto nel file remoto è maggiore di quello in locale
 */
function controlloNuovaVersione()
{
    $jsonlocale = leggeFileJSON('../lampschool.json');
    $urlaggiornamento = $jsonlocale['urlaggiornamento'];

    $jsonremoto = leggeFileJSON($urlaggiornamento);
    $versioneremota = $jsonremoto['versione'];
    $esito = version_compare($versioneremota, $_SESSION['versione'], ">");

    return array("esito" => $esito,
        "versione" => $jsonremoto['versione']);
}

/**
 * Recupera l'attuale versione installata
 *
 * @return string La versione LampSchool attualmente installata nel database,
 *         o boolean false in caso di errore
 */
function current_version($conn)
{
    $query = "SELECT * FROM tbl_parametri WHERE parametro='versioneprecedente'";
    $resp = mysqli_query($conn, inspref($query));
    if ($resp == false || mysqli_num_rows($resp) == 0)
    {
        return false;
    }
    else
    {
        $row = mysqli_fetch_array($resp);
        return $row['valore'];
    }
}