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
	
 // istruzioni per tornare alla pagina di login se non c'è una sessione valida
 ////session_start();
 $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
 $idutente=$_SESSION["idutente"];
 $idcircolare=stringa_html("idcircolare");
// $idclasse=stringa_html("idclasse");
 $destinatari=stringa_html("tipo");
 
 $anno=stringa_html('anno');
 $sezione=stringa_html('sezione');
 $specializzazione=stringa_html('specializzazione');
 $idmateria=stringa_html('idmateria');
 
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

print "<form method='post' action='seledocenti.php' name='seledoc'>";
print "<input type='hidden' name='idcircolare' value='$idcircolare'>
       <input type='hidden' name='tipo' value='$destinatari'>";         

print "<table align='center'>";

// SELEZIONE SU ANNO
print "   <tr>
      <td width='50%'><center><b>Anno</b></p></td>
      <td width='50%'>
      <SELECT ID='cl' NAME='anno' ONCHANGE='seledoc.submit()'><option value=''>&nbsp;</option>"; 
  
// Riempimento combo box tbl_classi
$query="select distinct anno from tbl_classi order by anno";
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
   print "<option value='";
   print ($nom["anno"]);
   print "'";
   if ($anno==$nom["anno"])
	print " selected";
   print ">";
   print ($nom["anno"]);
   
}
        
print("
      </SELECT>
      </td></tr>");

// SELEZIONE SU SEZIONE
print "   <tr>
      <td width='50%'><center><b>Sezione</b></p></td>
      <td width='50%'>
      <SELECT ID='cl' NAME='sezione' ONCHANGE='seledoc.submit()'><option value=''>&nbsp;</option>"; 
$query="select distinct sezione from tbl_classi order by sezione";
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
   print "<option value='";
   print ($nom["sezione"]);
   print "'";
   if ($sezione==$nom["sezione"])
	print " selected";
   print ">";
   print ($nom["sezione"]);
}
        
print("
      </SELECT>
      </td></tr>");

// SELEZIONE SU SPECIALIZZAZIONE
print "   <tr>
      <td width='50%'><center><b>$plesso_specializzazione</b></p></td>
      <td width='50%'>
      <SELECT NAME='specializzazione' ONCHANGE='seledoc.submit()'><option value=''>&nbsp;</option>"; 
$query="select distinct specializzazione from tbl_classi order by specializzazione";
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
   print "<option value='";
   print ($nom["specializzazione"]);
   print "'";
   if ($specializzazione==$nom["specializzazione"])
	print " selected";
   print ">";
   print ($nom["specializzazione"]);
   
}
        
print("
      </SELECT>
      </td></tr>");

// SELEZIONE SU MATERIA
print "   <tr>
      <td width='50%'><center><b>Materia</b></p></td>
      <td width='50%'>
      <SELECT NAME='idmateria' ONCHANGE='seledoc.submit()'><option value=''>&nbsp;</option>"; 
$query="select distinct idmateria,denominazione from tbl_materie order by denominazione";
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
   print "<option value='";
   print ($nom["idmateria"]);
   print "'";
   if ($idmateria==$nom["idmateria"])
	print " selected";
   print ">";
   print ($nom["denominazione"]);
   
}
        
print("
      </SELECT>
      </td></tr>");


      
print "</table></form>";






// VISUALIZZAZIONE ELENCO DOCENTI

$sele="";
if ($anno!="")
   $sele.=" and anno='$anno' ";
if ($sezione!="")
   $sele.=" and sezione='$sezione' ";
if ($specializzazione!="")
   $sele.=" and specializzazione='$specializzazione' ";
if ($idmateria!="")
   $sele.=" and idmateria='$idmateria' ";


print ("
   <form method='post' action='inslddocenti.php' name='listadistr'>
   
   <input type='hidden' name='idcircolare' value='$idcircolare'>
   <input type='hidden' name='tipo' value='$destinatari'>
   <br><center><input type='button' value='Seleziona tutti' onclick='checkTutti()'>
   <input type='button' value='Deseleziona tutti' onclick='uncheckTutti()'></center>
   <p align='center'>
   <table align='center' border='1'>
   <tr class='prima'><td>Cognome</td><td>Nome</td><td>Invio</td></tr>");
//if ($idclasse=='')
//    $query="select * from tbl_docenti order by cognome,nome";
//else
    $query="select distinct tbl_docenti.iddocente,cognome, nome 
            from tbl_cattnosupp,tbl_docenti,tbl_classi
            where tbl_cattnosupp.iddocente=tbl_docenti.iddocente
            and tbl_cattnosupp.idclasse=tbl_classi.idclasse
            and tbl_docenti.iddocente<>1000000000
            $sele
            order by cognome,nome";
$ris=mysqli_query($con,inspref($query));
while ($rec=mysqli_fetch_array($ris))
{
   
   print "<tr>";
   print "     <td>".$rec['cognome']."</td>";
   print "     <td>".$rec['nome']."</td>";
   print "     <td><input type='checkbox' name='cb".$rec['iddocente']."' value='yes'";
   if (inLista($rec['iddocente'],$idcircolare,$con))
      print "checked='checked'></td>";
   else
      print "></td>";    
   print "</tr>";
}
print "</table><br><center><input type='submit' value='Aggiorna lista di distribuzione'></center></p></form>";


	   
mysqli_close($con);
stampa_piede(""); 

function inLista($iddoc,$idcirc,$conn)
{
	$query="select * from tbl_diffusionecircolari where idutente=$iddoc and idcircolare=$idcirc";
	$ris=mysqli_query($conn,inspref($query)) or die("Errore: ".inspref($query)." - ".mysqli_error($ris));
	if (mysqli_num_rows($ris)==0)
	   return false;
	else
	   return true;   
}

