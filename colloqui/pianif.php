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



@require_once("../php-ini".$_SESSION['suffisso'].".php");
@require_once("../lib/funzioni.php");

require_once("pianifunz.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$iddocente=stringa_html('iddocente');

if ($tipoutente=="")
{
	header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']);
	die;
}

$titolo="Associazione aule-docenti per colloqui";
$script="<script>
			function startDrag(event)
			{
				event.dataTransfer.setData('id', event.target.id);
				event.dataTransfer.setData('value', event.target.innerHTML);
			}

			function startDrop(event,idaula)
			{
				iddoc = event.dataTransfer.getData('id');
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						document.getElementById('principale').innerHTML = xmlhttp.responseText;
					}
				};
				xmlhttp.open('POST','pianifadddoc.php',true);
				xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xmlhttp.send('idaula='+idaula+'&iddoc='+iddoc);
			}

			function eliminaDoc(event)
			{
				document.getElementById('cestino').src = './chiuso.jpg';
				iddoc = event.dataTransfer.getData('id');
				xmlhttp = new XMLHttpRequest();

				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						document.getElementById('principale').innerHTML = xmlhttp.responseText;
					}
				};
				xmlhttp.open('POST','pianifeliminadoc.php',true);
				xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xmlhttp.send('iddoc='+iddoc);
			}
			function inDrop(event,id)
			{
				event.preventDefault();
				if(id=='cestino')
				{
					document.getElementById('cestino').src = './aperto.jpg';
				}
			}
			function overDrop(event)
			{
				event.preventDefault();
			}
		</script>";
stampa_head($titolo,"",$script,"TDSPM");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
stampaTabella($con);


stampa_piede("");

