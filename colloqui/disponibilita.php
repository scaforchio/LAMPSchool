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
	
 // istruzioni per tornare alla pagina di login se non c'� una sessione valida
 ////session_start();
 $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
 
 $iddocente=stringa_html('iddocente');
 
    if ($tipoutente=="")
       {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
       } 

    $titolo="Disponibilità ricevimento genitori";
    $script="";
    stampa_head($titolo,"",$script,"TDSPM");
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 


$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

$query="select iddocente,cognome,nome from tbl_docenti order by cognome,nome";
$ris=mysqli_query($con,inspref($query));

print "<form action='disponibilita.php' method='post' name='dispo'>";
print "<table border=1 align='center'>";

print "      <tr class='prima'>
      <td colspan='2' align='center'><b>Docente</b>";
      
    //  $sqld= "SELECT * FROM tbl_docenti WHERE NOT sostegno ORDER BY cognome, nome";
      $query="select iddocente,cognome,nome from tbl_docenti order by cognome,nome";
      $ris=mysqli_query($con,inspref($query));
      
	      print ("<select name='iddocente' ONCHANGE='dispo.submit()'>");
         print ("<option>");
         while ($datal=mysqli_fetch_array($ris))
         {
	         print("<option value='");
	         print $datal['iddocente']."'";
	         if ($iddocente==$datal['iddocente'])
	            print " selected";
	         print("> ");
	         print($datal['cognome']);
	         print("&nbsp;");
	         print($datal['nome']);	
         }
	    
	
	print("</select> </td> </tr></table>");

print "</form>";



if ($iddocente!="")
{
   print "<center>Orari:</center>";
   $query="select * from tbl_orericevimento,tbl_orario 
           where tbl_orericevimento.idorario=tbl_orario.idorario
           and tbl_orericevimento.valido
           and iddocente=$iddocente";
   $ris=mysqli_query($con,inspref($query)) or die ("Errore: ".inspref($query));
   print "<table border=1 align=center><tr class='prima'><td>Orario</td><td>Note</td><td>Canc.</td></tr>";
   while ($rec=mysqli_fetch_array($ris))
   {
		$descrric=giornodanum($rec['giorno'])." - ".substr($rec['inizio'],0,5)."-".substr($rec['fine'],0,5);
      print "<tr><td><b>$descrric</b></td><td>".$rec['note']."</td><td><a href='cancdisponibilita.php?idoraricevimento=".$rec['idoraricevimento']."&iddocente=$iddocente'><img src='../immagini/delete.png'></a></td></tr> ";
	}
	print "</table>";
	
	print "<form action='insdisponibilita.php' method='post'>
	       <input type='hidden' value='$iddocente' name='iddocente'>";
	print "<center><br><select name='idorario'><option value=''>&nbsp;</option>";
	$query = "select * from tbl_orario
	          where idorario NOT IN 
	          (select idorario from tbl_orericevimento where iddocente='$iddocente' and valido)
	          and valido 
	          order by giorno, ora";
	$ris = mysqli_query($con,inspref($query)) or die ("Errore: ".inspref($query));
	while ($rec=mysqli_fetch_array($ris))
	{
		print "<option value='".$rec['idorario']."'>";
		print giornodanum($rec['giorno'])." - ".$rec['ora']." (".substr($rec['inizio'],0,5)."-".substr($rec['fine'],0,5).")";
		print "</option>";
	}
	print "</select>";
	print " Note: <input type='text' name='note' value='' maxlength='30' size='30'>";
	print "<br><input type='submit' value='Aggiungi'></center>";       
	print "</form>";
}

stampa_piede("");
mysqli_close($con);
 


