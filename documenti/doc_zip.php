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
/*
$titolo="Zippa documenti";
$script=""; 

//stampa_head($titolo,"",$script);
//stampa_testata("$titolo","","$nome_scuola","$comune_scuola");
*/

$nomefile=stringa_html('nomefile');
$idalunno=stringa_html('idalunno');
$con = mysqli_connect($db_server,$db_user,$db_password,$db_nome);

if (!$con)
{
   die("<h1> Connessione al server fallita </h1>");
}

$stringa_where="";   
//$nomefile="doc_";
if ($idalunno!="")
{
   $stringa_where.=" and idalunno=$idalunno ";
  // $nomefile.= $idalunno;
}
$query="select * from tbl_documenti
        where true 
        $stringa_where"; // doctype='application/pdf'";
        
$ris=mysqli_query($con,inspref($query));


$posifiles=array();
$nomifiles=array();

while ($rec=mysqli_fetch_array($ris))
{
	
	if (strlen($rec['docbin'])==0)
	{
		if ($_SESSION['suffisso']!="") $suff=$_SESSION['suffisso']."/"; else $suff="";    
		$posifiles[]="../lampschooldata/$suff".$rec['docmd5'];
		$nomifiles[]=$rec['docnome'];
	}
	else
	{
		$numbyte=file_put_contents($cartellabuffer."/".$rec['docmd5'],$rec['docbin']);
		$posifiles[]=$cartellabuffer."/".$rec['docmd5'];
		$nomifiles[]=$rec['docnome'];
	}    
	 
}

file_zip($posifiles,$nomifiles,$cartellabuffer."/"."$nomefile.zip");
mysqli_close($con);
header("location: ".$cartellabuffer."/"."$nomefile.zip");  



//stampa_piede();




/*
while ($rec=mysqli_fetch_array($ris))
{
	if ($rec['doctype']=='application/pdf')
	{
		if (strlen($rec['docbin'])==0)
			$pdf->addPDF('../lampschooldata/'.$rec['docmd5'],'all');
		else
			{
				// estraggo il file nel buffer, faccio l'append e cancello il file
				 
				$numbyte=file_put_contents($cartellabuffer."/".$rec['docmd5'],$rec['docbin']);
				// print "tttt $numbyte";
				$pdf->addPDF($cartellabuffer."/".$rec['docmd5'],'all');
				// unlink($cartellabuffer."/".$rec['docmd5']);
			}    
	}
	else  // SI TRATTA DI UNA IMMAGINE DA CONVERTIRE IN PDF
	{
		if (strlen($rec['docbin'])==0)
			crea_pdf_da_img($rec['docmd5'],'../lampschooldata/'.$rec['docmd5'],estrai_tipo_immagine($rec['doctype']));
		else
			{
				// estraggo il file nel buffer e faccio l'append
				 
				$numbyte=file_put_contents($cartellabuffer."/".$rec['docmd5'],$rec['docbin']);
				//print "tttt $numbyte";
				$esito=crea_pdf_da_img($rec['docmd5'],$cartellabuffer."/".$rec['docmd5'],estrai_tipo_immagine($rec['doctype']));
				//print "tttt $esito";
			}
			   
		$pdf->addPDF($cartellabuffer."/".$rec['docmd5']."_pdf",'all');
	}	 
}

$pdf->merge('download', 'pei.pdf');
*/


/*
 function crea_pdf_da_img($nomefile,$percorso,$tipo)
{
	//print "tttt 1 <br>";
	@require("../php-ini".$_SESSION['suffisso'].".php");
	// @include("../lib/fpdf/fpdf.php");
	//print "tttt 2 <br>";
	$pagimm=new FPDF('P','pt');
   //print "tttt 3 <br>";   
	
   //print "tttt 4 <br>";   
   // $pagimm->Image($percorso,0,0,0,0,'png');
   list($width, $height, $type, $attr)=getimagesize($percorso);
  // print "tttt: $width";
  // print "tttt: $height";
   //$larghezza=40;
   
   if ($width/$height>1.25)
   {
		$orie="L";
		$maxwidth=800;
	}
      
   else
   {
		$orie="P";
		$maxwidth=600;
	}
   $pagimm->AddPage($orie);
   
   if ($width>$maxwidth)
      $larghezza=$maxwidth;
   
   if ($larghezza!=$maxwidth)
      $posx=($maxwidth-$width)/2;
   // print "tttt $posx";         
   $pagimm->Image($percorso,$posx,30,$larghezza,0,$tipo);
   //print "tttt 5 <br>"; 
   $pagimm->Output($cartellabuffer."/".$nomefile."_pdf","F"); 
	return 1;
}

function estrai_tipo_immagine($tipo)
{
	switch ($tipo)
	{
		case "image/jpeg":return "jpg";
		case "image/png":return "png";
		case "image/gif":return "gif";
	}
	return "errore";
}
*/
function file_zip($elenco_file,$elenco_nomi,$nome_file)
{
	$zip = new ZipArchive();
   $file= $nome_file;

   if ($zip->open($file, ZIPARCHIVE::CREATE)===TRUE) 
   {
      for ($i=0;$i<count($elenco_file);$i++)
      {
			$zip->addFile($elenco_file[$i],$elenco_nomi[$i]); 
		}	
      $zip->close();
   }
   else 
      echo "Errore nella creazione del'archivio";
}


