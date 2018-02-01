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


	/*Programma per la visualizzazione dell'elenco delle tbl_classi.*/

	@require_once("../php-ini".$_SESSION['suffisso'].".php");
	@require_once("../lib/funzioni.php");
	
	// istruzioni per tornare alla pagina di login 
	////session_start();
    $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
	if ($tipoutente=="")
	   {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
	   }	
	
	$titolo="Elenco entrate posticipate delle classi";
    $script=""; 
    stampa_head($titolo,"",$script,"MSP");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

//
//    Fine parte iniziale della pagina
//



	

	
	//Connessione al server SQL
	$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);

	//Esecuzione query
	$query="SELECT * FROM tbl_entrateclassi,tbl_classi
            WHERE tbl_entrateclassi.idclasse=tbl_classi.idclasse
            ORDER BY data desc,ora desc,anno, sezione, specializzazione";
	if (!($ris=mysqli_query($con,inspref($query)))) 
	{
		print "\nQuery fallita";
	}	
	else
	{
		print "<br/><CENTER><form name='form2' action='nuo_ritcla.php' method='POST'>";
		print "<input type='submit' name='nuoentrpost' value='Nuova entrata posticipata'>";
		print "</form></CENTER><br/><br/>";
		print "<CENTER><TABLE BORDER='1'>";	
		print "<TR class='prima'><TD ALIGN='CENTER'><B>Classe</B></TD><TD ALIGN='CENTER'><B>Data</B></TD><TD ALIGN='CENTER'><B>Ora</B></TD><TD COLSPAN='2' ALIGN='CENTER'><B>Azioni</B></TD></TR>";
		while($dati=mysqli_fetch_array($ris)) 
		{

			print "<TR class='oddeven'><TD>".$dati['anno']." ".$dati['sezione']." ".$dati['specializzazione']."</TD><TD>".data_italiana($dati['data'])."</TD><TD>".substr($dati['ora'],0,5)."</TD>";
            print "<TD>"; // <A HREF='mod_ritcla.php?idritcla=". $dati['identrataclasse']. "'><img src='../immagini/edit.png' title='Modifica'></A>";
            
			print "<A HREF='eli_ritcla.php?idritcla=". $dati['identrataclasse']."'><img src='../immagini/delete.png' title='Elimina'></A>";

            print "</TD></TR>";	
		} 
		print "</CENTER></TABLE>";
	}
	

	
	stampa_piede("");
	mysqli_close($con);
	
	


