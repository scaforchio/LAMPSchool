<?php session_start();
/**
 * Aggiornamento di LAMPSchool per installazioni multiple
 *
 * @copyright  Copyright (C) 2014-2016 Renato Tamilio, Pietro Tamburrano
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */


$lista = array();

if (!($dp = opendir("../")))
{
    die("Non riesco a esplorare il contenuto");
}

while ($file = readdir($dp))
{

    $lista[] = $file;

}

sort($lista);

$numfileini = 0;

$nomeprimoini = "";
foreach ($lista as $nome)
{

    if (substr($nome, 0, 7) == "php-ini" && substr($nome, strlen($nome) - 4, 4) == ".php")
    {
        $numfileini++;
        if ($numfileini == 1)
        {
            $nomeprimoini = $nome;
        }
    }

}


if ($numfileini > 0)
{

    @require_once "../" . $nomeprimoini;
}
else
{
    $versioneprecedente = "";
}

require_once '../lib/funzioni.php';
require_once 'funzioni_install.php';

$json = leggeFileJSON('../lampschool.json');
$titolo = 'Installazione di ' . $json['titolo'] . ' ' . $json['versione'];
$_SESSION['versione']=$json['versione'];
$_SESSION['versioneprecedente'] = $versioneprecedente;


stampa_head('Installazione Lampschool', '', getCssJavascript(), "", false);
stampa_testata_installer($titolo, '', '');


if ($json['versione'] == $versioneprecedente)
{
    print "
   <center>
   <h3 class='importante'>La versione installata è già aggiornata.</h3>
   </center>
   <form id='formInstall' method='post'>
   </form>";
}
else
{
    if (isset($nome_scuola))
    {
        print "
              <center>
              <h3 class='importante'>Si consiglia di fare un backup del sito e del database prima di procedere.</h3>
              </center>
              <form id='formInstall' method='post'>
              </form>";
        stampaPulsanti('', 'updateparametri', '', 'Aggiorna');
    }
    else
    {
        print "
              <form id='formInstall' method='post'>
              </form>";
        stampaPulsanti('', 'installprerequisiti', '', 'Installa');
    }
}
stampa_piede('', false);

