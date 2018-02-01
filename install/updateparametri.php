<?php
/**
 * Aggiornamento dei parametri globali di LAMPSchool
 * i dati di accesso alla base dati sono scritti nel file newphp-ini.php
 * tutti gli altri parametri saranno memorizzati nella tabella tbl_parametri
 *
 * @copyright  Copyright (C) 2014 Renato Tamilio
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */

// require_once '../php-ini.php';
require_once '../lib/funzioni.php';
require_once 'funzioni_install.php';

// Necessario solo per la Ver.1.9 per errata impostazione del parametro versioneprecedente nell'aggiornamento
// alla 1.8.1

//


$json = leggeFileJSON('../lampschool.json');
$titolo = $json['titolo'] . ' ' . $json['versione'] . ' Aggiornamento';

stampa_head('Aggiornamento Lampschool', '', getCssJavascript(), "", false);
stampa_testata_installer($titolo, '', '');


/*
    Se i controlli vanno a buon fine, scrive il nuovo file di configurazione ed aggiorna il database
*/

$newfile = 'newphp-ini.php';


$elencoinstallazioni = array();
$elencoinstallazioni = elencafiles("../");

for ($i = 0; $i < count($elencoinstallazioni); $i++)
{
    $fileinclude = $elencoinstallazioni[$i];


    $configfile = '../' . $fileinclude;
    aggiorna($fileinclude, $configfile,$newfile,$json);

}
print "<form method='post' id='formInstall'></form>";
stampaPulsanti('', '../index', '', 'Login');


function aggiorna($fileinclude, $configfile,$newfile,$json)
{
// Preparazione del file newphp-ini.php che ha come modello il file php-ini.php
    require_once "../".$fileinclude;


    $str = file_get_contents('php-ini.php');
    $str = str_replace("{DBHOST}", "$db_server", $str);
    $str = str_replace("{DBNAME}", "$db_nome", $str);
    $str = str_replace("{DBUSER}", "$db_user", $str);
    $str = str_replace("{DBPWD}", "$db_password", $str);
    $str = str_replace("{DBPREFIX}", "$prefisso_tabelle", $str);
    file_put_contents($newfile, $str);

// Per sicurezza si fa un backup del file php-ini.php se esiste e lo cancella
    if (file_exists($configfile))
    {
        copy($configfile, $fileinclude . '.bkp');
    }

// Copia il newphp-ini.php nella cartella principale cambiando il suo nome in php-ini.php
    if (!copy($newfile, $configfile))
    {
        print "<p class='importante' align='center'>il file $configfile non &egrave; stato copiato correttamente</p>";
    }

    $credenziali = array(
        'server' => $db_server,
        'nomedb' => $db_nome,
        'user' => $db_user,
        'password' => $db_password,
        'prefisso' => $prefisso_tabelle
    );

    $erroredb = esecuzionePHP($credenziali, $json['versione']);
    $erroredb = false;
    $sqlupdate = $json['database']['aggiornamento'];
    $noncompresi = array('.', '..');
    $files = array_diff(scandir($sqlupdate), $noncompresi);

// Esecuzione dei file sql dalla versione attuale alla versione indicata nel file json

    foreach ($files as $filesql)
    {
        if (is_file($sqlupdate . $filesql))
        {
            $posizioneSql = stripos($filesql, '.sql');
            if ($posizioneSql > 0)
            {
                $versioneFile = substr($filesql, 0, $posizioneSql);
                $daEseguire = version_compare($versioneFile, $versioneprecedente, ">");
                if ($daEseguire)
                {
                    $erroredb = esecuzioneFile($sqlupdate . $filesql, $credenziali);
                    if ($erroredb)
                    { // Esce dal ciclo
                        print "Errore in esecuzione file $sqlupdate.$filesql!";
                        break;
                    }
                }
            }
        }
    }

    $suffisso=substr($fileinclude,7,strlen($fileinclude) - 11);

    if ($erroredb)
    {
        $stile . "importante";
    }
    else
    {
        $stile = "importanteverde";
    }

    $messaggioFinale = "<div class='$stile' align='center'>Aggiornamento di LAMPSchool per <b>" . $suffisso . "</b> alla versione " . $json['versione'];

    if ($erroredb)
    {
        print $messaggioFinale . " NON effettuato correttamente :-(</div>";
    }
    else
    {
        print $messaggioFinale . " completato :-)</div>";
    }
}


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

