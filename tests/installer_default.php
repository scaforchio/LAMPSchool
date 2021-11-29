<?php

require dirname(__DIR__).'/vendor/autoload.php';

use LampSchool\Tests\Support\Installer;

$installer = new Installer(
    'mysql', # change me..
    'dev-lamp', # change me..
    'dev-lamp', # change me..
    'dev-lamp', # change me..
    '',
    '',
    'admin',
    '',
    'istituto default'
);


echo "Inizio installazione LAMPSchool...\n\n";

try {
    $installer->run();
}catch (\Exception $exception){
    echo $exception->getMessage() . "\n\n";

}

echo "Installazione terminata...\n\n";
