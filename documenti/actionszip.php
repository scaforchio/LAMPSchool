<?php session_start();


/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
GNU Affero General Public License come pubblicata 
dalla Free Software Foundation; sia la versione 3, 
sia (a vostra scelta) ogni versione successiva.

Questo programma é distribuito nella speranza che sia utile 
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di 
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE. 
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$iddocente = $_SESSION["idutente"];
$Id = stringa_html('Id');

function file_zip($elenco_file, $elenco_nomi, $nome_file)
{
    $zip = new ZipArchive();
    $file = $nome_file;

    if ($zip->open($file, ZIPARCHIVE::CREATE) === TRUE)
    {
        for ($i = 0; $i < count($elenco_file); $i++)
        {
            $zip->addFile($elenco_file[$i], $elenco_nomi[$i]);
        }
        $zip->close();
    }
    else
    {
        echo "Errore nella creazione del'archivio";
    }
}


