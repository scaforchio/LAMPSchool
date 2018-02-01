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

   /*Programma per la visualizzazione del menu principale.*/
@require_once("../php-ini".$_SESSION['suffisso'].".php");
@require_once("../lib/funzioni.php");
	
// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente=="")
{
   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
   die;
} 

$titolo="Download documenti allegati al PEI";
$script=""; 
$idalunno=stringa_html('idalunno');
$iddocente=$_SESSION['idutente'];
$modo=stringa_html('modo');
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");

$con = mysqli_connect($db_server,$db_user,$db_password,$db_nome);

if (!$con)
{
   die("<h1> Connessione al server fallita </h1>");
}
   



// include '../lib/PDFMerger/PDFMerger.php';

// $pdf = new PDFMerger;

//$pdf->addPDF('samplepdfs/one.pdf', '1, 3, 4')
//	->addPDF('samplepdfs/two.pdf', '1-2')
//	->addPDF('samplepdfs/three.pdf', 'all')
//	->merge('file', 'samplepdfs/TEST2.pdf');
	
	//REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options
	//You do not need to give a file path for browser, string, or download - just the name.


   //  $_SESSION['versione']=$versione;
   //Connessione al server SQL



// SELEZIONE ALUNNO CERTIFICATO

print "<form action='scarica_doc_pei.php' method='post' name='alu' id='alu'>";
print "<input type='hidden' name='modo' value='$modo'>";
print "<table width=100%><tr><td width=50% align=center>Alunno</td><td width=50%>";

print "<select name='idalunno' ONCHANGE='alu.submit()'><option value=''>&nbsp;</option>";

if ($modo=='sost')
$query="select idalunno,cognome,nome,datanascita,idclasse 
            from tbl_alunni
            where certificato
            and idalunno in (select idalunno from tbl_cattnosupp where iddocente=$iddocente)
            order by cognome, nome,datanascita";

else
$query="select idalunno,cognome, nome, datanascita,anno,sezione,specializzazione from
        tbl_alunni, tbl_classi
        where
        tbl_alunni.idclasse=tbl_classi.idclasse
        and certificato";
$ris=mysqli_query($con,inspref($query)) or die("Errore: ". inspref($query));
//print inspref($query);
while ($rec=mysqli_fetch_array($ris))
{
	print "<option value='".$rec['idalunno']."' ";
	if ($rec['idalunno']==$idalunno)
	{
	   print "selected";
	   $nomefile="doc_pei_".$rec['cognome']."_".$rec['nome'];
	}
	print ">";
	print $rec['cognome']." ". $rec['nome']." ".$rec['datanascita']." - ". $rec['anno']." ".$rec['sezione']." ".$rec['specializzazione'];
	print "</option>";
	
}

print "</select>";


print "</td></tr></table></form>";





if ($idalunno!="")
{
	$query="select iddocumento from tbl_documenti
	        where idalunno=$idalunno
	        and pei";
	$ris=mysqli_query($con,inspref($query));
	if (mysqli_num_rows($ris)>0)
	{         
		print "<form action ='../documenti/doc_zip.php' method='post' target='_blank'>";
		print "<input type='hidden' name='idalunno' value='$idalunno'>";
		print "<input type='hidden' name='ritorno' value='scarica_doc_pei.php'>";
		print "<input type='hidden' name='nomefile' value='$nomefile'>";
		print "<br><center><input type='submit' value='Scarica documenti'></center>";
		print "</form>";
	}
	else
	{
		print "<br><center><b>Nessun documento da scaricare!</b></center>";
	}
}

mysqli_close($con);
stampa_piede();

