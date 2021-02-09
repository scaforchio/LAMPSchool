<?php

session_start();

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Inserimento giornata colloqui";
$script = "";
stampa_head($titolo, "", $script, "MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Inserimento giornata colloqui", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$dataCorrente = date("Y-m-d");

print " <center>
		<form id='giornata' action='salvadata.php' method='get'>
		<input type='date' name='data' min='$dataCorrente' required> <br>
		<h3> ORA INIZIO: </h3> <input type='time' name='oraInizio'  required>
		<h3> ORA FINE: </h3> <input type='time' name='oraFine'  required>
		<h3> DURATA COLLOQUIO: </h3> <select name='durata' form='giornata' value=' ' required>
										<option value='5'> 5 minuti </option> <br/>
                                                                                <option value='6'> 6 minuti </option> <br/>
										<option value='10'> 10 minuti </option> <br/>
                                                                                <option value='12'> 12 minuti </option> <br/>
										<option value='15'> 15 minuti </option> <br/>
										<option value='20'> 20 minuti </option> <br/>
										<option value='30'> 30 minuti </option> <br/>
									</select>
		<br><br><br><br>
		<input type='submit' value='Salva'>
		</form>
		</center>";

stampa_piede("");
mysqli_close($con);