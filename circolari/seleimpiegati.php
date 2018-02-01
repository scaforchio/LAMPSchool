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
 $idutente=$_SESSION["idutente"];
 $idcircolare=stringa_html("idcircolare");
 $destinatari=stringa_html("tipo");
 
 if ($tipoutente=="")
 {
   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
   die;
 } 

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 
$titolo="Selezione destinatari circolare";

$script="<script>
function checkTutti() 
{
   with (document.listadistr) 
   {
      for (var i=0; i < elements.length; i++) 
      {
         if (elements[i].type == 'checkbox')
            elements[i].checked = true;
      }
   }
}
function uncheckTutti() 
{
   with (document.listadistr) 
   {
      for (var i=0; i < elements.length; i++) 
      {
         if (elements[i].type == 'checkbox')
            elements[i].checked = false;
      }
   }
} 
</script>
"; 
stampa_head($titolo,"",$script,"MSPA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

print ("
   <form method='post' action='insldimpiegati.php' name='listadistr'>
   
   <input type='hidden' name='idcircolare' value='$idcircolare'>
   <input type='hidden' name='tipo' value='$destinatari'>
   <br><center><input type='button' value='Seleziona tutti' onclick='checkTutti()'>
   <input type='button' value='Deseleziona tutti' onclick='uncheckTutti()'></center>
   <p align='center'>
   <table align='center' border='1'>
   <tr class='prima'><td>Cognome</td><td>Nome</td><td>Invio</td></tr>");

$query="select * from tbl_amministrativi order by cognome,nome";
$ris=mysqli_query($con,inspref($query));
while ($rec=mysqli_fetch_array($ris))
{
   
   print "<tr>";
   print "     <td>".$rec['cognome']."</td>";
   print "     <td>".$rec['nome']."</td>";
   print "     <td><input type='checkbox' name='cb".$rec['idamministrativo']."' value='yes'";
   if (inLista($rec['idamministrativo'],$idcircolare,$con))
      print "checked='checked'></td>";
   else
      print "></td>";    
   print "</tr>";
}
print "</table><br><center><input type='submit' value='Aggiorna lista di distribuzione'></center></p></form>";


	   
mysqli_close($con);
stampa_piede(""); 

function inLista($idamm,$idcirc,$conn)
{
	$query="select * from tbl_diffusionecircolari where idutente=$idamm and idcircolare=$idcirc";
	$ris=mysqli_query($conn,inspref($query)) or die("Errore: ".inspref($query)." - ".mysqli_error($ris));
	if (mysqli_num_rows($ris)==0)
	   return false;
	else
	   return true;   
}

