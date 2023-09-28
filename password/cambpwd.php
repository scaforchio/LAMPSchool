<?php

require_once '../lib/req_apertura_sessione.php';
/*
  Copyright (C) 2013 Pietro Tamburrano
  Copyright (C) 2013 Pietro Tamburrano, Vittorio Lo Mele
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


/* Programma per il cambiamento password. */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"];
$userid = $_SESSION["userid"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
	header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
	die;
}
$titolo = "Cambiamento propria password";
stampa_head_new($titolo, "", $script, "TDSAPML");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con) {
	print("\n<h1> Connessione al server fallita </h1>");
	exit;
};

/*
print("<center>");
// print "<form name='form1' action='ch_pwd.php' method='POST'>";

print "<table border='0'>";
print "<tr> <td> Utente </td> <td> <input type='text' name='ut' id='ut' value='$userid' disabled> <input type='hidden' name='utente' value='$userid'></td> </tr>";
print "<tr> <td> Vecchia password </td> <td> <input type='password' name='passwor' id='passwor'> </td> </tr>";
print "<tr> <td> Nuova password </td> <td> <input type='password' name='npas' id='npas'>
	             <div id='result'><div id='bar'></div></div>
                
                 </td> </tr>";
print "<tr> <td> Ripeti nuova password&nbsp;</td> <td> <input type='password' name='rnpas' id='rnpas'> </td> </tr>";
print "</table>";

print "<form name='form1' action='ch_pwd.php' method='POST'>";
print "<input type='hidden' name='ute' id='ute' value='$userid'>";
print "<input type='hidden' name='password' id='password'>";
print "<input type='hidden' name='npass' id='npass'>";
print "<input type='hidden' name='rnpass' id='rnpass'>";
print '<center><br/><input type="submit" name="OK" id="OK" value="OK" disabled onclick="document.getElementById(\'password\').value=hex_md5(document.getElementById(\'passwor\').value);document.getElementById(\'ute\').value=document.getElementById(\'ut\').value;document.getElementById(\'npass\').value=document.getElementById(\'npas\').value;document.getElementById(\'rnpass\').value=document.getElementById(\'rnpas\').value" >';
print "</form>";
print "<br/>";
*/


?>

<div class="container-form-pre">
	<div class="container-form">
		<div class="mb-3">
			<label for="vecchia" class="form-label">Vecchia Password</label>
			<input type="password" class="form-control" id="vecchia">
		</div>
		<div class="mb-3">
			<label for="nuova" class="form-label">Nuova Password</label>
			<input type="password" class="form-control" id="nuova">
		</div>
		<div class="mb-3">
			<label for="conferma" class="form-label">Conferma Password</label>
			<input type="password" class="form-control" id="conferma">
		</div>
		<button onclick="submitb()" id="bottone" class="btn btn-outline-secondary mb-3 w-100" disabled>Cambia Password</button>

		<div class='alert alert-danger' role='alert' id="status"> 
			<i id='a1' class='bi bi-x' style='margin-right: 4px;'></i> Lunga almeno 9 caratteri <br>
			<i id='a2' class='bi bi-x' style='margin-right: 4px;'></i> Contiene una lettera maiuscola <br>
			<i id='a3' class='bi bi-x' style='margin-right: 4px;'></i> Contiene un numero <br>
			<i id='a4' class='bi bi-x' style='margin-right: 4px;'></i> Contiene un simbolo <br>
			<i id='a5' class='bi bi-x' style='margin-right: 4px;'></i> Le password corrispondono <br>
		</div>
	</div>
</div>

<form action="ch_pwd.php" method="POST">
	<input type='hidden' name='ute' id='ute'>
	<input type='hidden' name='password' id='password'>
	<input type='hidden' name='npass' id='npass'>
	<input type='hidden' name='rnpass' id='rnpass'>
</form>

<script src="../lib/js/crypto.js"></script>

<script>

	let globStatus = false;
	let s1 = false;
	let s2 = false; 
	let s3 = false;
	let s4 = false;
	let s5 = false;

	function controlla() {
		if(s1 && s2 && s3 && s4 && s5){
			globStatus = true;
			document.querySelector(`#status`).classList.remove("alert-danger");
			document.querySelector(`#status`).classList.add("alert-success");
			document.querySelector(`#bottone`).disabled = false;
		}else{
			globStatus = false;
			document.querySelector(`#status`).classList.add("alert-danger");
			document.querySelector(`#status`).classList.remove("alert-success");
			document.querySelector(`#bottone`).disabled = true;
		}
	}

	function submitb() {
		if(!globStatus) return;

		var form = document.createElement("form");
		var ute = document.createElement("input"); 
		var old = document.createElement("input"); 
		var neww = document.createElement("input");  
		var confirm = document.createElement("input");  

		form.method = "POST";
		form.action = "ch_pwd.php";   

		ute.value = "<?php echo $userid; ?>";
		ute.name = "ute";
		form.appendChild(ute);  

		old.value = hex_md5(document.querySelector('#vecchia').value);
		old.name = "password";
		form.appendChild(old);  

		neww.value = document.querySelector('#nuova').value;
		neww.name = "npass";
		form.appendChild(neww);  

		confirm.value = document.querySelector('#conferma').value;
		confirm.name = "rnpass";
		form.appendChild(confirm);  

		document.body.appendChild(form);

		form.submit();
	}

	function setIcon(id, status) {
		document.querySelector(`#${id}`).classList.remove("bi-check-all");
		document.querySelector(`#${id}`).classList.remove("bi-x");
		if(status){
			document.querySelector(`#${id}`).classList.add("bi-check-all");
		}else{
			document.querySelector(`#${id}`).classList.add("bi-x");
		}
	}

	document.querySelector('#nuova').addEventListener('input', () => {
		// testa la password
		let password = document.querySelector('#nuova').value;
		let conferma = document.querySelector('#conferma').value;
		
		//lunghezza
		if(password.length < 9){
			s1 = false;
			setIcon("a1", false);
		}else{
			s1 = true;
			setIcon("a1", true);
		}

		// maiuscola
		if(!/[A-Z]/.test(password)){
			s2 = false;
			setIcon("a2", false);
		}else{
			s2 = true;
			setIcon("a2", true);
		}

		//numero
		if(!/[0-9]/.test(password)){
			s3 = false;
			setIcon("a3", false);
		}else{
			s3 = true;
			setIcon("a3", true);
		}

		//simbolo
		if(!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/.test(password)){
			s4 = false;
			setIcon("a4", false);
		}else{
			s4 = true;
			setIcon("a4", true);
		}
		
		if(password != conferma && password != ""){
			s5 = false;
			setIcon("a5", false);
		}else{
			s5 = true;
			setIcon("a5", true);
		}

		controlla();
		
	});

	document.querySelector('#conferma').addEventListener('input', () => {
		// testa la password
		let password = document.querySelector('#nuova').value;
		let conferma = document.querySelector('#conferma').value;

		if(password != conferma && password != ""){
			s5 = false;
			setIcon("a5", false);
		}else{
			s5 = true;
			setIcon("a5", true);
		}

		controlla();
	});
</script>

<?php
mysqli_close($con);
stampa_piede_new("");
