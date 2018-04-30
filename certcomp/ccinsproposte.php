<?php

session_start();

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

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Inserimento proposte per certificazione competenze";
$script = "";
stampa_head($titolo, "", $script, "SDP");






$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
$iddocente = stringa_html('iddocente');
$idalunno = stringa_html('idalunno');
$idclasse = estrai_classe_alunno($idalunno, $con);
$livscuola = stringa_html('livscuola');
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='ccproposte.php?idclasse=$idclasse'>PROPOSTE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

// Cancello le vecchie proposte

$querydel = "delete from tbl_certcompproposte where idalunno=$idalunno and iddocente=$iddocente";

mysqli_query($con, inspref($querydel)) or die("Errore nella query: " . mysqli_error($con));

// Inserisco le nuove proposte per ogni competenza presente

$query = "select * from tbl_certcompcompetenze where livscuola='$livscuola' and valido";

$ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));

while ($rec = mysqli_fetch_array($ris)) {
    $queryins = "";
    if ($rec['compcheuropea'] == "") {
        $campo = "txtcmp_" . $rec['idccc'];
        $giud = stringa_html($campo);
        if ($giud != '')
            $queryins = "insert into tbl_certcompproposte(idalunno,iddocente,idccc,giud) "
                    . "values ($idalunno,$iddocente,'" . $rec[idccc] . "','$giud')";
    } else {
        $campo = "selcmp_" . $rec['idccc'];
        $live = stringa_html($campo);
        if ($live != "0")
            $queryins = "insert into tbl_certcompproposte(idalunno,iddocente,idccc,idccl) "
                    . "values ($idalunno,$iddocente,'" . $rec[idccc] . "','" . $live . "')";
    }
    if ($queryins != "")
        mysqli_query($con, inspref($queryins)) or die("Errore nella query: " . mysqli_error($con) . " " . inspref($queryins));
}

print "<br><br><center><big>Inserimento effettuato!</big>";

mysqli_close($con);
stampa_piede("");

