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

$NG_API_ABIL = "MASP";
$NG_API_JSON = true;

require_once '../lib/ng_api_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_FILES['image']) && 
        $_FILES['image']['error'] === UPLOAD_ERR_OK &&
        isset($_POST['tipoFoto']) &&
        isset($_POST['id']) &&
        (
            $_POST['tipoFoto'] == "alunno"
            || $_POST['tipoFoto'] == "classe"
        )
    ){

        $tmpFilePath = $_FILES['image']['tmp_name'];
        $caption = $con->real_escape_string($_POST['caption']);
        $hash = md5_file($tmpFilePath);
        $defImgPath = './storage/a_' . $hash;

        if (file_exists($defImgPath)) {
            unlink($defImgPath);
        }

        if (!move_uploaded_file($tmpFilePath, $defImgPath)) {
            error_response(500, "Errore durante il salvataggio dell'immagine");
        }

        $image = imagecreatefromstring(file_get_contents($defImgPath));

        if ($image === false) {
            unlink($defImgPath);
            error_response(500, "Errore durante il caricamento dell'immagine");
        }

        unlink($defImgPath);
        
        if (!imagewebp($image, $defImgPath)) {
            error_response(500, "Errore durante la conversione dell'immagine");
        }

        // salva immagine a db
        $res = eseguiQuery($con, "INSERT INTO tbl_fotoannuario (hash, didascalia) VALUES ('$hash', '$caption')");
        $last_insert_id = mysqli_insert_id($con);
        $id = $con->real_escape_string($_POST['id']);
        if ($_POST['tipoFoto'] == "alunno") {
            $res = $res && eseguiQuery($con, "UPDATE tbl_alunni SET idfotoannuario = $last_insert_id WHERE idalunno = $id");
        } else {
            $res = $res &&  eseguiQuery($con, "UPDATE tbl_classi SET idfotoannuario = $last_insert_id WHERE idclasse = $id");
        }

        if ($res == true) {
            json_response(200, "Immagine caricata con successo");
        } else {
            unlink($defImgPath);
            error_response(500, "Errore durante il salvataggio su database");
        }
    } else {
        error_response(400, "Richiesta non valida");
    }
} else {
    error_response(405, "Method Not Allowed");
}

mysqli_close($con);