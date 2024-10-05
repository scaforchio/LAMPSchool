<?php

require_once '../lib/req_apertura_sessione.php';
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


/* Programma per la gestione di subutenze */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"];
$userid = $_SESSION["userid"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
	header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
	die;
}
$titolo = "Gestione utenze secondarie";

stampa_head_new($titolo, "", $script, "SM");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
	print("\n<h1> Connessione al server fallita </h1>");
	exit;
};

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['azione'])) {

	$successo = false;
	$messaggio = "";
	$generata = creapassword();

	$azione = $_POST['azione'];
	if ($azione == "crea") {
		$idutente = $con->real_escape_string($_POST['idutente']);
		$desc = $con->real_escape_string($_POST['desc']);
		$pwd = password_hash($generata, PASSWORD_DEFAULT);
		$sql = "INSERT INTO tbl_passwordalt (userid, hash, descrizione) VALUES ('$idutente', '$pwd', '$desc')";
		if(!eseguiQuery($con, $sql)) {
			$successo = false;
			$messaggio = "Errore durante la creazione dell'utenza secondaria: " . $con->error;
		} else {
			$successo = true;
			$messaggio = "Utenza secondaria creata con successo! Nome utente: $idutente, password: $generata";
		}
	}
	if ($azione == "rigenera") {
		$id = $_POST['id'];
		$pwd = password_hash($generata, PASSWORD_DEFAULT);
		$sql = "UPDATE tbl_passwordalt SET hash = '$pwd' WHERE id = $id";
		if(!eseguiQuery($con, $sql)) {
			$successo = false;
			$messaggio = "Errore durante la rigenerazione della password: " . $con->error;
		} else {
			$successo = true;
			$messaggio = "Password rigenerata con successo! Nuova password: $generata";
		}
	}
	if ($azione == "elimina") {
		$id = $con->real_escape_string($_POST['id']);
		$sql = "DELETE FROM tbl_passwordalt WHERE id = $id";
		if(!eseguiQuery($con, $sql)) {
			$successo = false;
			$messaggio = "Errore durante l'eliminazione dell'utenza secondaria: " . $con->error;
		} else {
			$successo = true;
			$messaggio = "Utenza secondaria eliminata con successo!";
		}
	}

	?>

	<div class="alert alert-<?= $successo ? "success" : "danger" ?> alert-dismissible fade show" role="alert">
		<?= $messaggio ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>

	<?php
}

?>

<div style="margin-left: 5px; margin-right: 5px;">
<form action="" method="post">
	<b>Aggiungi credenziale secondaria:</b>
	<div class="row g-2 mt-2">
		<div class="col">
			<input type="hidden" name="azione" value="crea">
			<div class="form-floating">
				<input type="text" class="form-control" id="idutente" name="idutente" value="" required="">
				<label for="idutente">Nome utente primario</label>
			</div>
		</div>
		<div class="col">
			<div class="form-floating">
				<input type="text" class="form-control" id="desc" name="desc" value="" required="">
				<label for="desc">Descrizione utenza secondaria</label>
			</div>
		</div>
		<div class="col col-auto">
			<button type="submit" class="btn btn-outline-secondary h-100">
				<i class="bi bi-plus"></i>
				Crea
			</button>
		</div>
	</div>
</form>

<br>

<b>Lista utenze secondarie esistenti:</b>

<table class="table table-striped table-bordered" id="tabella">
	<thead>
		<tr>
			<th>Nome utente</th>
			<th>Descrizione</th>
			<th>Azioni</th>
		</tr>
	</thead>
	
	<tbody>
		<?php
		$result = eseguiQuery($con, "SELECT * FROM tbl_passwordalt");
		while ($row = mysqli_fetch_array($result)) { ?>
			<tr>
				<td><?= $row['userid'] ?></td>
				<td><?= $row['descrizione'] ?></td>
				<td>
					<div class="row g-2">
						<div class="col col-auto">
							<form action="" method="post">
								<input type="hidden" name="azione" value="rigenera">
								<input type="hidden" name="id" value="<?= $row['id'] ?>">
								<button type="submit" class="btn btn-outline-secondary">
									RIGENERA <i class="bi bi-key"></i>
								</button>
							</form>
						</div>
						<div class="col col-auto">
							<form action="" method="post">
								<input type="hidden" name="azione" value="elimina">
								<input type="hidden" name="id" value="<?= $row['id'] ?>">
								<button type="submit" class="btn btn-outline-danger">
									<i class="bi bi-trash"></i>
								</button>
							</form>
						</div>
					</div>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>
</div>

<?php import_datatables(); ?>

<script>
	$(document).ready(function() {
		$('#tabella').DataTable();
	});
</script>

<?php
mysqli_close($con);
stampa_piede_new("");
