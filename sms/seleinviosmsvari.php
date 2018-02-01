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
 @require_once("../lib/sms/php-send.php");
	
 // istruzioni per tornare alla pagina di login se non c'� una sessione valida
 ////session_start();
 $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
 $idutente=$_SESSION["idutente"];
 $idcircolare=stringa_html("idcircolare");
// $idclasse=stringa_html("idclasse");
 $destinatari='SA';
 
 $anno=stringa_html('anno');
 $sezione=stringa_html('sezione');
 $specializzazione=stringa_html('specializzazione');
 
 
 if ($tipoutente=="")
 {
   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
   die;
 } 

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 
$titolo="Selezione destinatari SMS";

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

$rissms=array();
$rissms=verifica_numero_sms_residui($utentesms,$passsms);
$smsresidui=$rissms['classic_sms'];
$smsresidui=floor($smsresidui*($costosmsclassic/$costosmsplus));
if ($smsresidui>1000)
   $color='green';
else if ($smsresidui>500)
   $color='orange';
else
   $color='red';      
print "<center><b><font color='$color' size='4'>SMS residui: $smsresidui</font></center></b>";

print "<form method='post' action='seleinviosmsvari.php' name='selealu'>";

print "<table align='center'>";

// SELEZIONE SU ANNO
print "   <tr>
      <td width='50%'><p align='center'><b>Anno</b></p></td>
      <td width='50%'>
      <SELECT ID='cl' NAME='anno' ONCHANGE='selealu.submit()'><option value=''>&nbsp;</option>"; 
  
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
      <td width='50%'><p align='center'><b>Sezione</b></p></td>
      <td width='50%'>
      <SELECT ID='cl' NAME='sezione' ONCHANGE='selealu.submit()'><option value=''>&nbsp;</option>"; 
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
      <td width='50%'><p align='center'><b>$plesso_specializzazione</b></p></td>
      <td width='50%'>
      <SELECT NAME='specializzazione' ONCHANGE='selealu.submit()'><option value=''>&nbsp;</option>"; 
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



      
print "</table></form>";






// VISUALIZZAZIONE ELENCO DOCENTI

$sele="";
if ($anno!="")
   $sele.=" and anno='$anno' ";
if ($sezione!="")
   $sele.=" and sezione='$sezione' ";
if ($specializzazione!="")
   $sele.=" and specializzazione='$specializzazione' ";


print ("
   <form method='post' action='sendsmsvari.php' name='listadistr'>
   
   Testo: <input type='text' name='testosms' maxlength='155' size='155'>    
   <br><center><input type='button' value='Seleziona tutti' onclick='checkTutti()'>
   <input type='button' value='Deseleziona tutti' onclick='uncheckTutti()'></center>
   <br><br><center><input type='submit' value='Invia SMS'></center><br><br>
   <p align='center'>
   
   <table align='center' border='1'>
   <tr class='prima'><td>Cognome</td><td>Nome</td><td>Invio</td></tr>");
//if ($idclasse=='')
//    $query="select * from tbl_docenti order by cognome,nome";
//else
    $query="select distinct tbl_alunni.idalunno,cognome, nome, telcel 
            from tbl_alunni,tbl_classi
            where tbl_alunni.idclasse=tbl_classi.idclasse
            $sele
            order by cognome,nome";
$ris=mysqli_query($con,inspref($query)) or die ("Errore: ".inspref($query));
while ($rec=mysqli_fetch_array($ris))
{
   
   print "<tr>";
   print "     <td>".$rec['cognome']."</td>";
   print "     <td>".$rec['nome']."</td>";
   
   if ($rec['telcel']!="")
	 	 print "<td align='center'><input type='checkbox' name='sms".$rec['idalunno']."'>
			     </td>";
	else
	    print "<td align='center'>Ins. cell.</td>";
    
   print "</tr>";
}
print "</table><br><center><input type='submit' value='Invia SMS'></center></p></form>";
	   
mysqli_close($con);
stampa_piede(""); 


