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


   @include ("../php-ini".$_SESSION['suffisso'].".php");
    @require_once("../lib/funzioni.php");
	
	// istruzioni per tornare alla pagina di login 
	////session_start();
    $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
	if ($tipoutente=="")
	   {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
	   }
	
	$titolo="Aggiorna comune";
    $script=""; 
    stampa_head($titolo,"",$script,"M");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");

    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);

    print("<table border=0 width='100%'>
		<tr>
		   <td align ='center' ><strong><font size='+1'>INSERIMENTO COMUNE</font></strong></td>
		</tr>
		</table>");
	print"<br/><br/>";
    print"<center> <table border='0'>";
    print"<form action='reg.php' method='POST'>";
	print"<tr><td><input type ='hidden' name='idtbl_comuni' size='10' value='$idcomune'></td></tr>";
    print"<tr><td><b>Denominazione<font color='#ff0000'>*</b></td><td><input type ='text' name='denominazione' size='20' value='$denominazione' maxlength='20' ></td></tr>";
    print"<tr><td><b>CAP<font color='#ff0000'>*</b></td><td><input type ='text' name='cap' size='6' value='$cap' maxlength='5'></td></tr>";				  	
    print"<tr><td><b>Codistat<font color='#ff0000'>*</b></td><td><input type ='text' name='codistat' size='10' value='$codistat'></td></tr>";
    print"<tr><td><b>Provincia<font color='#ff0000'>*</b></td><td><input type ='text' name='provincia' size='15' value='$provincia'></td></tr>";
    print"<tr><td><b>Sigla provincia<font color='#ff0000'>*</b></td><td><input type ='text' name='siglaprovincia' size='2' maxlength='2' value='$siglaprovincia'></td></tr>";
 	print"<tr><td><b>Regione<font color='#ff0000'>*</b></td><td><input type ='text' name='regione' size='15' value='$regione'></td></tr>";
 	print"<tr><td><b>Stato estero</b></td><td><input type ='text' name='statoestero' size='15' value='$statoestero'></td></tr>";  
 	print"<table>";
	print"<tr><td><center><input type ='submit' value='Inserisci' size='10'></td></tr>"; 
	print"<tr><td><br/></td></tr>"; 
    print"</form>";
	print"</table>";
    print"</table> </center>";
	print("<center> <form name='form8' action='lis_com.php' method='POST'>");
	print("<input type='submit' value='<<Indietro'></form></center>");
	print"<p align='center'><font color='#ff0000'>* </font> (Campi obbligatori)</p>";
 
    mysqli_close($con);
    stampa_piede("");

