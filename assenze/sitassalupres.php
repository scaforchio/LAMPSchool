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
  
//
//    VISUALIZZAZIONE DELLA SITUAZIONE DELLE ASSENZE E DEI RITARDI
//    PER I GENITORI 
//


require_once '../php-ini'.$_SESSION['suffisso'].'.php';
require_once '../lib/funzioni.php';
// require_once '../lib/db / query.php';
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
// $lQuery = LQuery::getIstanza();

//  istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
    die;
} 

$titolo = "Situazione assenze alunni";
$script = ""; 
stampa_head($titolo, "", $script,"MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$idalunno = stringa_html('idalunno');

   
 $query="select * from tbl_alunni left join tbl_classi
         on tbl_alunni.idclasse=tbl_classi.idclasse
         order by cognome,nome,anno, sezione, specializzazione";      
         
 $ris=mysqli_query($con,inspref($query));   
 //print "tttt ".inspref($query);     
 print "<form name='selealu' action='visvalpre.php' method='post'>";
 print "<table align='center'>";
 print "<tr><td>Alunno</td>";
 print "<td>";
 print "<select name='idalunno' ONCHANGE='selealu.submit();'><option value=''>&nbsp;</option>";
 while ($rec=mysqli_fetch_array($ris))
 {
	 if ($idalunno==$rec['idalunno'])
	    $sele=" selected";
	 else
	    $sele="";   
	 print ("<option value='".$rec['idalunno']."'$sele>".$rec['cognome']." ".$rec['nome']." (".$rec['datanascita'].") - ".$rec['anno']." ".$rec['sezione']." ".$rec['specializzazione']."</option>");
 }
 print "
 </select> 
 </td>
 
 </tr>
 
 </table></form>";     






// Dati utili al disegno di questa pagina

if ($idalunno != "")
{
	$rs1 = mysqli_query($con,inspref("select * from tbl_alunni where idalunno=$idalunno"));
	$rs2 = mysqli_query($con,inspref("select count(*) as numerotblassenze from tbl_assenze where idalunno=$idalunno"));
	$rs3 = mysqli_query($con,inspref("select count(*) as numerotblritardi from tbl_ritardi where idalunno=$idalunno"));
	$rs4 = mysqli_query($con,inspref("select count(*) as numerouscite from tbl_usciteanticipate where idalunno=$idalunno"));
	$rs5 = mysqli_query($con,inspref("select * from tbl_assenze where idalunno=$idalunno order by data desc"));
	$rs6 = mysqli_query($con,inspref("select * from tbl_ritardi where idalunno=$idalunno order by data desc"));
	$rs7 = mysqli_query($con,inspref("select * from tbl_usciteanticipate where idalunno=$idalunno order by data desc"));


	// print "<center><i>Dati aggiornati al ".data_italiana($ultimoaggiornamento).".</i></center>
	print "<table border='1' align='center' width='50%'>";

	// prelevamento dati alunno

	if ($rs1) {
		 
		 if ($val1 = mysqli_fetch_array($rs1))
			  echo ' 
	 <tr>
	  <td colspan="3"><b>Alunno: '. $val1["cognome"]. ' '. $val1["nome"]. '</b></td>
	 </tr>';
	}

	// conteggio tbl_assenze

	if ($val2 = mysqli_fetch_array($rs2))
		 echo ' 
	 <tr>
	  <td colspan="3"><b>Assenze: '. $val2["numerotblassenze"]. '</b></td>
	 </tr>';

	// conteggio tbl_ritardi

	if ($rs3) {
		 
		 if ($val3 = mysqli_fetch_array($rs3))
			  echo ' 
	 <tr>
	  <td colspan="3"><b>Ritardi: '. $val3["numerotblritardi"]. '</b></td>
	 </tr>';
	}

	// conteggio uscite anticipate

	if ($val4 = mysqli_fetch_array($rs4))
		 echo ' 
	 <tr>
	  <td colspan="3"><b>Uscite anticipate: '. $val4["numerouscite"]. '</b></td>
	 </tr>';

	print "
	 <tr><td width='33%'>Assenze</td><td width='33%'>Ritardi</td><td width='33%'>Uscite</td></tr>";

	// elenco tbl_assenze
	echo "
	 <tr><td valign='top'>"; 

	if ($rs5) {
		 
		 while ($val5 = mysqli_fetch_array($rs5)) {
			  $data = $val5["data"];
			  echo ' '. data_italiana($data). ' '. giorno_settimana($data). '<br/>';
		 }
	}
	echo "</td>";

	// elenco tbl_ritardi
	echo "<td valign='top'>";

	if ($rs6) {
	  
		 while($val6 = mysqli_fetch_array($rs6))
		 {
			  $data = $val6["data"];
			  echo ' '. data_italiana($data). ' '. giorno_settimana($data). '<br/>';
		 }
	}
	echo "</td>";

	// elenco uscite
	echo "<td valign='top'>";

	if ($rs7) {
		 
		 while ($val7 = mysqli_fetch_array($rs7))
		 {
			  $data = $val7["data"];
			  echo ' '.data_italiana($data).' '.giorno_settimana($data).'<br/> ';
		 }
	}
	echo '
		  </td>
		 </tr>
		</table>';
	}
stampa_piede();

