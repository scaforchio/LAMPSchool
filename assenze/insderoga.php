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


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


$titolo = "Inserimento giustificazione assenza";
$script = "";
stampa_head($titolo, "", $script, "MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='deroghe.php'>Assenze da non conteggiare</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$idalunno = stringa_html('idalunno');


$motivo = stringa_html('motivo');
$datainizio = data_to_db(stringa_html('datainizio'));
$datafine = data_to_db(stringa_html('datafine'));


$oregiorni = array();    // Array contenente il numero di ore della deroga per ogni giorno della settimana
for ($i = 1; $i <= $giornilezsett; $i++)
{
    array_push($oregiorni, stringa_html("ore$i"));
}

if ($datainizio < $datainiziolezioni)  // Per evitare errori di inserimento 
    $data = $datainiziolezioni;
else
    $data = $datainizio;
if ($datafine > $datafinelezioni)  // Per evitare errori di inserimento 
    $datafine = $datafinelezioni;

// Inserisco una presenza forzata per ogni giorno compreso tra datainizio e datafine
do
{
    $numeroore = $oregiorni[numero_giorno_settimana($data) - 1];
    $strgi = 'giorno' . numero_giorno_settimana($data);

    $vergiornosett = stringa_html($strgi) ? "on" : "off";

    if ((!giorno_festa($data, $con)) && ($vergiornosett == "on"))
    {
        $query = "insert into tbl_deroghe(idalunno,data,motivo,numeroore) values ($idalunno,'$data','$motivo',0)";
        eseguiQuery($con, $query);
    } else

    if ((!giorno_festa($data, $con)) && (giorno_settimana($data) != "Dom") && ($numeroore > 0))
    {
        $query = "insert into tbl_deroghe(idalunno,data,motivo,numeroore) values ($idalunno,'$data','$motivo','$numeroore')";
        eseguiQuery($con, $query);
    }
    $data = aggiungi_giorni($data, 1);
} while ($data <= $datafine);



print "<br><br><center><b><font color='green'>Inserimento effettuato!</font></b>";


stampa_piede("");
mysqli_close($con);



