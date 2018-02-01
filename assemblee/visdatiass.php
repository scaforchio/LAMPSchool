<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
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

//
//    VISUALIZZAZIONE DELLE ASSEMBLEE DI CLASSE PER I GENITORI
//	  E
//	  RICHIESTA DI ASSEMBLEE DI CLASSE PER GLI ALUNNI 
//


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

//  istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Visualizza Assemblea";
$script = "";
stampa_head($titolo,"",$script,"SDLP");
print "\n<body>";

$dato = stringa_html('dato');
$idassemblea = stringa_html('idass');
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$ass = "SELECT * FROM tbl_assemblee WHERE idassemblea=$idassemblea";
$ris = mysqli_query($con, inspref($ass));
if(!$ris)
{
	print "ERRORE ".mysqli_error($con). " <br/> ".$ass;
}
else
{
	$data = mysqli_fetch_array($ris);
	print "<table border='1' width='98%'>";
	print "<tr class='prima'>
			<td height='30'>ASSEMBLEA ".data_italiana($data['dataassemblea'])."</td>
		   </tr>";
	if($dato=="odg")
	{
		print "<tr class='prima'>
				<td height='20'><center><i>Ordine del Giorno</i></center></td>
			   </tr>";
		print "<tr>
				<td>".nl2br($data['odg'])."</td>
			   </tr>";
	}
        if($dato=="note")
	{
		print "<tr class='prima'>
				<td height='20'><center><i>Note di autorizzazione</i></center></td>
			   </tr>";
		print "<tr>
				<td>".nl2br($data['note'])."</td>
			   </tr>";
	}
	if($dato=="ver")
	{
		print "<tr class='prima'>
				<td height='20'><center><i>Verbale</i></center></td>
			   </tr>";
		if($data['consegna_verbale']==1)
		{
			print "<tr>
					<td>".nl2br($data['verbale'])."</td>
				   </tr>";
		}
		else
		{
			print "<tr>
					<td align='center' height='80'>VERBALE NON ANCORA CONSEGNATO!</td>
				   </tr>";
		}
	}
	print "</table>";
}
