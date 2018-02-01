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
 $idgruppo=stringa_html("idgruppo");
 $iddocente=stringa_html("iddocente");
 $idmateria=stringa_html("idmateria");
 $insok=stringa_html("insok");
 
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

stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");


if ($insok)
   print "<br><center>Inserimento effettuato!</center><br>";


// VISUALIZZAZIONE ELENCO ALUNNI

print ("
   <form method='post' action='inslistagruppo.php' name='listadistr'>
   
   <input type='hidden' name='idgruppo' value='$idgruppo'>
   <input type='hidden' name='iddocente' value='$iddocente'>
   <input type='hidden' name='idmateria' value='$idmateria'>
   <br><center><input type='button' value='Seleziona tutti' onclick='checkTutti()'>
   <input type='button' value='Deseleziona tutti' onclick='uncheckTutti()'></center>
   <p align='center'>
   <table align='center' border='1'>
   <tr class='prima'><td>Alunno</td><td>Invio</td></tr>");

    $query="select distinct tbl_alunni.idalunno,tbl_alunni.idclasse,cognome, nome,anno, sezione, specializzazione 
            from tbl_alunni,tbl_classi
            where tbl_alunni.idclasse=tbl_classi.idclasse
            and tbl_classi.idclasse in
            (select distinct idclasse from tbl_cattnosupp
             where iddocente=$iddocente and idmateria=$idmateria)
            order by anno,sezione,specializzazione,cognome,nome";
$ris=mysqli_query($con,inspref($query)) or die ("Errore: ".inspref($query));
while ($rec=mysqli_fetch_array($ris))
{
   
   print "<tr>";
   print "     <td>".$rec['cognome']."&nbsp;".$rec['nome']." - ".$rec['anno']."&nbsp;".$rec['sezione']."&nbsp;".$rec['specializzazione']."</td>";
   print "     <td><input type='checkbox' name='cb".$rec['idalunno']."' value='yes'";
   if (inLista($rec['idalunno'],$idgruppo,$con))
      print "checked='checked'></td>";
   else
      print "></td>";    
   print "</tr>";
}
print "</table><br><center><input type='submit' value='Aggiorna lista alunni'></center></p></form>";


	   
mysqli_close($con);
stampa_piede(""); 

function inLista($idalu,$idgruppo,$conn)
{
	$query="select * from tbl_gruppialunni where idalunno=$idalu and idgruppo=$idgruppo";
	$ris=mysqli_query($conn,inspref($query)) or die("Errore: ".inspref($query)." - ".mysqli_error($ris));
	if (mysqli_num_rows($ris)==0)
	   return false;
	else
	   return true;   
}

