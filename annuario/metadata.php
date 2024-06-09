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
        ($_POST['tipoFoto'] == "alunno" ||
        $_POST['tipoFoto'] == "classe") &&
        isset($_POST['id'])
    ) {
        $idFoto = 0;
        if ($_POST['tipoFoto'] == "alunno") {
            $idFoto = mysqli_fetch_assoc(eseguiQuery($con, "SELECT idfotoannuario FROM tbl_alunni WHERE idalunno = " . $_POST['id']))['idfotoannuario'];
        } else {
            $idFoto = mysqli_fetch_assoc(eseguiQuery($con, "SELECT idfotoannuario FROM tbl_classi WHERE idclasse = " . $_POST['id']))['idfotoannuario'];
        }
    
        $foto = mysqli_fetch_assoc(eseguiQuery($con, "SELECT * FROM tbl_fotoannuario WHERE id_foto = $idFoto"));

        if(!$foto) {
            error_response(404, "Foto non trovata");
        } else {
            $data = [
                'id' => $foto['id_foto'],
                'didascalia' => $foto['didascalia'],
                'hash' => $foto['hash']
            ];

            json_response(200, $data);
        }
    
    } else {
        error_response(400, "Richiesta non valida");
    }
} else {
    error_response(405, "Method Not Allowed");
}

mysqli_close($con);