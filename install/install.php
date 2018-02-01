<?php session_start();
/**
 * Nuova installazione di LAMPSchool
 *
 * @copyright  Copyright (C) 2014-2016 Renato Tamilio, Pietro Tamburrano
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */

if (file_exists('../php-ini.php'))
{
    @require_once '../php-ini.php';
}
else
{
    $versioneprecedente = "";
}

require_once '../lib/funzioni.php';
require_once 'funzioni_install.php';

$json = leggeFileJSON('../lampschool.json');
$titolo = 'Installazione di ' . $json['titolo'] . ' ' . $json['versione'];

$_SESSION['versioneprecedente'] = $versioneprecedente;


stampa_head('Installazione Lampschool', '', getCssJavascript(), "", false);
stampa_testata_installer($titolo, '', '');


print "
              <form id='formInstall' method='post'>
              </form>";
stampaPulsanti('', 'installprerequisiti', '', 'Installa');

stampa_piede('', false);

