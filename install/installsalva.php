<?php session_start();
/**
 * Nuova installazione, salvataggio dei parametri globali di LAMPSchool nella tabella tbl_parametri
 * il file newphp-ini.php viene copiato nel path iniziale del sito e diventa php-ini.php
 *
 * @copyright  Copyright (C) 2015 Angelo Scarnà, Renato Tamilio
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */


require_once '../lib/funzioni.php';
require_once 'funzioni_install.php';

$json = leggeFileJSON('../lampschool.json');
$titolo = $json['titolo'] . ' ' . $json['versione'] . ' Installazione';
////session_start();

stampa_head('Installazione Lampschool', '', getCssJavascript(), "", false);
stampa_testata_installer($titolo, '', '');
$par_suffisso_installazione = stringa_html('par_suffisso_installazione');
$newfile = 'newphp-ini.php';
$configfile = '../php-ini' . $par_suffisso_installazione . '.php';

// Per sicurezza si fa un backup del file php-ini.php se esiste e lo cancella
if (file_exists($configfile))
{
    copy($configfile, 'php-ini' . $suffisso_installazione . '.php.bkp');
}


// Copia il newphp-ini.php nella cartella principale cambiando il suo nome in php-ini.php
if (!copy($newfile, $configfile))
{
    print "<p class='importante' align='center'>il file $configfile non &egrave; stato copiato correttamente</p>";

}
else
{
    // CREAZIONE SOTTOCARTELLE SUFFISSO

    mkdir("../abc/$par_suffisso_installazione", 0700);

    mkdir("../lampschooldata/$par_suffisso_installazione", 0700);
    copy("../abc/index.html", "../abc/$par_suffisso_installazione/index.html");
    copy("../abc/firmadirigente.png", "../abc/$par_suffisso_installazione/firmadirigente.png");
    copy("../abc/testata.jpg", "../abc/$par_suffisso_installazione/testata.jpg");
    copy("../abc/timbro.png", "../abc/$par_suffisso_installazione/timbro.png");
  //  copy("../abc/*", "../abc/$par_suffisso_installazione");
    copy("../lampschooldata/index.html", "../lampschooldata/$par_suffisso_installazione/index.html");
    copy("../css/stile.css", "../css/stile$par_suffisso_installazione.css");
}

$par_db_server = stringa_html('par_db_server');
$par_db_nome = stringa_html('par_db_nome');
$par_db_user = stringa_html('par_db_user');
$par_db_password = stringa_html('par_db_password');
$par_prefisso_tabelle = stringa_html('par_prefisso_tabelle');
$par_nomescuola = stringa_html('par_nomescuola');
$par_passwordadmin = stringa_html('par_passwordadmin');

$erroredb = false;
$file = $json['database']['installazione'];
$credenziali = array(
    'server' => $par_db_server,
    'nomedb' => $par_db_nome,
    'user' => $par_db_user,
    'password' => $par_db_password,
    'prefisso' => $par_prefisso_tabelle
);

esecuzioneFile($file, $credenziali);

//if (!$erroredb) {
//    $erroredb = inizializzaParametri(true, $credenziali,$json['versione']);
//}

$con=mysqli_connect($par_db_server,$par_db_user,$par_db_password,$par_db_nome);
$query="update $par_prefisso_tabelle"."tbl_parametri set valore='".$par_nomescuola."' where parametro='nome_scuola'";
mysqli_query($con,$query) or die("Errore in settaggio nome scuola");
if ($par_passwordadmin!="")
{
    $query = "update $par_prefisso_tabelle" . "tbl_utenti set password=md5(md5('" . $par_passwordadmin . "')) where idutente=0";
    mysqli_query($con, $query) or die("Errore in impostazione password");
}
mysqli_close($con);
if ($erroredb)
{
    $stile . "importante";
}
else
{
    $stile = "importanteverde";
}

$messaggioFinale = "<div class='$stile' align='center'>Installazione di LAMPSchool alla versione " . $json['versione'];

if ($erroredb)
{
    print $messaggioFinale . " NON effettuata correttamente :-(</div>";
}
else
{
    if ($par_passwordadmin!="")
        $passadm=$par_passwordadmin;
    else
        $passadm="admin";
    print $messaggioFinale . " completata :-)<br>
                              Ricordarsi di:<br><br>
                               - effettuare l'impostazione dei parametri;<br>
                               - cancellare la cartella /install;<br>
                               - cambiare la password di amministratore.<br><br>
                              Per il primo accesso usare 'adminlamp' - '$passadm'.
                              </div>";
}
print "
<form method='post' id='formInstall'>
 <input type='hidden' name='par_db_server' value='$par_db_server'>
 <input type='hidden' name='par_db_nome' value='$par_db_nome'>
 <input type='hidden' name='par_db_user' value='$par_db_user'>
 <input type='hidden' name='par_db_password' value='$par_db_password'>
 <input type='hidden' name='par_prefisso_tabelle' value='$par_prefisso_tabelle'>
 <input type='hidden' name='par_nomescuola' value='$par_nomescuola'>
 <input type='hidden' name='par_passwordadmin' value='$par_passwordadmin'>
 <input type='hidden' name='par_suffisso_installazione' value='$par_suffisso_installazione'>
</form>";

stampaPulsanti('../index', '', 'Login');

stampa_piede($json['versione'], false);


