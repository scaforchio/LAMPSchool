<?php
require '../vendor/autoload.php';

header("Content-Type: image/svg+xml");
$generator = new Picqer\Barcode\BarcodeGeneratorSVG();
echo $generator->getBarcode(isset($_GET["data"]) ? $_GET["data"] : "NULL" , $generator::TYPE_CODE_128, 3, 100);