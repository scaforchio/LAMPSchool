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
//MODIFICA PER RICARICARE ANCHE IL VOTO DI COMPORTAMENTO - RIGA 63
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Ricaricamento voti ammissione per esami";
$script = "";
stampa_head($titolo, "", $script, "E");
stampa_testata("$titolo", "", "$nome_scuola", "$comune_scuola");


$idclasse = stringa_html('idclasse');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


// Esclusione alunni con esito negativo agli scrutini

$query = "SELECT * FROM tbl_alunni WHERE idclasseesame<>'0' AND idclasse<>'0'";
$ris = eseguiQuery($con,$query);
while ($rec = mysqli_fetch_array($ris)) {
    $idtipoesito = estrai_idtipoesito($rec['idalunno'], $con);
    if ($idtipoesito > 0) {
        if (passaggio($idtipoesito, $con) != 0) {
            $query = "UPDATE tbl_alunni SET idclasseesame=0 WHERE idalunno=" . $rec['idalunno'];
            eseguiQuery($con,$query);
        }
    }
}




// VERIFICO SE E' POSSIBILE IMPORTARE VOTI DI AMMISSIONE

$query = "SELECT * FROM tbl_alunni
                      where tbl_alunni.idclasseesame=$idclasse";
$risalu = eseguiQuery($con,$query);

while ($recalu = mysqli_fetch_array($risalu)) {
    $idalunno = $recalu['idalunno'];

    $votoammissione = 0;
    $inserimento = false;
    $query = "SELECT * FROM tbl_esiti
                      where idalunno=$idalunno";
    $risesito = eseguiQuery($con,$query);
    if ($recesito = mysqli_fetch_array($risesito)) {

        $votoammissione = $recesito['votoammissione'];
    } else {
        $votoammissione = 0;
    }


    $query = "select * from tbl_esesiti where idalunno=$idalunno";
    $risric = eseguiQuery($con,$query);
    if (mysqli_num_rows($risric) > 0) {
        $query = "update tbl_esesiti set votoammissione='$votoammissione' where idalunno=$idalunno";
        eseguiQuery($con,$query);
    } else {
        $query = "insert into tbl_esesiti(idalunno,votoammissione) values ($idalunno,$votoammissione)";
        eseguiQuery($con,$query);
    }
}


print "<form method='post' id='formricprop' action='../esame3m/rieptabesame.php'>";

print "<input type='hidden' name='cl' value='$idclasse'>

        </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formricprop').submit();
        }
        </SCRIPT>";


stampa_piede("");


