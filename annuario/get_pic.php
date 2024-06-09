<?php
/*
  Copyright (C) 2024 Vittorio Lo Mele
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma è distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */

$NG_API_ABIL = "SKIP";
$NG_API_JSON = true;

require_once '../lib/ng_api_init.php';

$path = "";

if (isset($_GET['hash']) && $_GET['hash'] != "") {
    if (file_exists('./storage/a_' . $_GET['hash'])) {
        $path = './storage/a_' . $_GET['hash'];
    } else {
        $path = './storage/default.webp';
    }
} else {
    $path = './storage/default.webp';
}

header('Content-Type: image/webp');
readfile($path);
mysqli_close($con);