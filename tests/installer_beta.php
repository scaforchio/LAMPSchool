<?php

require dirname(__DIR__).'/vendor/autoload.php';

use LampSchool\Tests\Support\Installer;

$installer = new Installer(
    'mysql', # change me..
    'dev-lamp', # change me..
    'dev-lamp', # change me..
    'dev-lamp', # change me..
    'beta',
    '',
    'beta',
    'beta',
    'istituto beta'
);

echo "Inizio installazione LAMPSchool...\n";
$installer->run();
echo "Installazione terminata...\n";
