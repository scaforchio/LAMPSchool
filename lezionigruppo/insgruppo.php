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

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();

// DA SOSTITUIRE CON PARAMETRO
//$memdati='db'; // Oppure 'hd' (Database o HardDisk) Funzionante da estendere a PDL, Prog e Relazioni

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$descrizione = stringa_html('descrizione');
$iddocente = stringa_html('iddocente');
$idmateria = stringa_html('idmateria');

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Inserimento gruppo alunni";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$queryins = "insert into tbl_gruppi(descrizione, iddocente, idmateria)  values ('$descrizione','$iddocente','$idmateria')";
$result = mysqli_query($con, inspref($queryins)) or die("Errore:" . inspref($queryins));

$idgruppo = mysqli_insert_id($con);

print "<form method='post' id='formgru' action='../lezionigruppo/selealunnigruppo.php'>
			  <input type='hidden' name='idgruppo' value='$idgruppo'>
			  <input type='hidden' name='iddocente' value='$iddocente'>
			  <input type='hidden' name='idmateria' value='$idmateria'>
		 </form> 
		 <SCRIPT language='JavaScript'>
		 {
				document.getElementById('formgru').submit();
		  }
		 </SCRIPT>";


mysqli_close($con);
stampa_piede();


