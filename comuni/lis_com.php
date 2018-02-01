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

	$titolo="Elenco comuni";
    $script=""; 
    stampa_head($titolo,"",$script,"M");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");

    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome); 
    $db=true; 
    $sql='SELECT * from tbl_comuni ORDER BY denominazione';

    if (!($result=mysqli_query($con,inspref($sql))))
	{
        print ("Query fallita");
	}      
    else
	{
        print "<center><table border='1' width='40%' height='30%'>";
        print "<tr class='prima'><td align=center><b>DENOMINAZIONE</b></td><td align=center colspan=2><b>AZIONI</b></td></tr>";
	  
        while ($data=mysqli_fetch_array($result))
        {
            print ("<tr bgcolor='#cccccc'> <td> <a href='vis_com.php?idcom=".$data['idcomune']."'> ".$data['denominazione']." </a> </td>
		      <td> <a href='mod_com.php?idcom=".$data['idcomune']."'><center> Modifica </a> </td>
		      <td> <a href='eli_com.php?idcom=".$data['idcomune']."'><center>Elimina </a> </td> </tr> ");       
        }
	}
    print "</table><br/>";
	print" <form  name='form6' action='agg_com.php' method='POST'>";
	print "<input type='submit' value='Inserisci'><br/><br/>";
    print"</form>";	  
	
    //tasto indietro	
	print "<form action='../login/ele_ges.php' method='POST'>";
    print "<input type='submit' name='indietro' value='<< Indietro'/>";
    print "</form>";

    mysqli_close($con);
    stampa_piede("");

