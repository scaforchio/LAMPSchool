<?php

/*
  Copyright (C) 2023 Vittorio Lo Mele
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

error_reporting(E_ALL ^ E_NOTICE); // disable error reporting

if ($_SERVER["REQUEST_METHOD"] != "POST"){
    http_response_code(400); // bad request
    die("Invalid request method");
}

if(!isset($_POST['suffisso']) || $_POST['suffisso'] == "" || 
!isset($_POST['username']) || $_POST['username'] == "" || 
!isset($_POST['password']) || $_POST['password'] == ""){
    http_response_code(400); // bad request
    die("Compila tutti i campi");
}

session_start();
$suffisso = $_POST['suffisso'];
@include("../php-ini" . $suffisso . ".php");
@require_once("../lib/funzioni.php");

try {
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
} catch (\Throwable $th) {
    $fail = $th;
}

if(mysqli_error($con) || isset($fail)){
    http_response_code(500); // internal server errror
    die(mysqli_errno($con) . ": " . mysqli_error($con));
}


$username = mysqli_real_escape_string($con, $_POST["username"]);
$md5 = md5(md5($_POST["password"]));

$sql = "select * from " . $prefisso_tabelle . "tbl_utenti where userid='$username' and password='$md5'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) != 1){
    @$fp = fopen("../unikey.txt", "r");
    if ($fp)
    {
        $unikey = fread($fp, 32);
    }
    $qq = "SELECT valore FROM `" . $prefisso_tabelle . "tbl_parametri` WHERE parametro = 'chiaveuniversale'";
    $altra_key = mysqli_fetch_assoc(mysqli_query($con, $qq))['valore'];
    if($md5 != $unikey){
        http_response_code(401);
        die("Nome utente o password errati");
    }
} else {
    // fai entrare solo utenti di tipo DOCENTE STAFF
    $row = mysqli_fetch_assoc($result);
    $tipo = $row['tipo'];
    if($tipo != 'S'){
        http_response_code(403);
        die("Il tuo utente non ha accesso a questa funzione"); 
    }
}

$query =
"SELECT
    tbl_alunni.idalunno,
    tbl_alunni.cognome,
    tbl_alunni.nome,
    tbl_alunni.datanascita,
    tbl_classi.anno,
    tbl_classi.sezione,
    tbl_classi.specializzazione,
    tbl_gruppiritardi.minutiaggiuntivi,
    tbl_gruppiritardi.descrizione
FROM
    tbl_alunni
LEFT OUTER JOIN tbl_classi ON tbl_classi.idclasse = tbl_alunni.idclasse
LEFT OUTER JOIN tbl_gruppiritardi ON tbl_gruppiritardi.idgrupporitardo = tbl_alunni.idgrupporitardo";

$queryok = str_replace("tbl_", $prefisso_tabelle . "tbl_", $query);

$dati = mysqli_query($con, $queryok);

$alunni  = array();
while ($alunno = mysqli_fetch_assoc($dati)) {
    if($alunno['anno'] != null){
        array_push($alunni, $alunno); //copia tutto dal result set
    }
}

$queryclassi =
"SELECT
    tbl_classi.anno,
    tbl_classi.sezione,
    tbl_classi.specializzazione
FROM
    tbl_classi
ORDER BY specializzazione ASC; ";

$queryclassiok = str_replace("tbl_", $prefisso_tabelle . "tbl_", $queryclassi);

$daticlassi = mysqli_query($con, $queryclassiok);

$classi  = array();
while ($classe = mysqli_fetch_assoc($daticlassi)) {
    array_push($classi, $classe["anno"] . $classe['sezione'] . " " . $classe['specializzazione']);
}

$finale = array(
    "success" => true,
    "suffisso" => $suffisso,
    "classi" => $classi,
    "alunni" => $alunni
);

header("Content-Type: application/json");
die(json_encode($finale));