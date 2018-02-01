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
require_once("../lib/fpdf/fpdf.php");
	
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
    
// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();


$tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente=="")
{
   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
   die;
} 


$numeropassword = stringa_html('numpass');



$arr_iddoc=array();
$arr_utdoc=array();
$arr_pwdoc=array();

for ($i=1;$i<=$numeropassword;$i++)
{
	//  Estrarre pass, utente, iddocente da POST e creare tre array
	$iddoc="iddoc".$i;
	$utdoc="utdoc".$i;
	$pwdoc="pwdoc".$i;
	
	$arr_iddoc[]=stringa_html($iddoc);
	$arr_utdoc[]=stringa_html($utdoc);
	$arr_pwdoc[]=stringa_html($pwdoc);
}
//  Richiamare funzione di stampa passando gli array come parametri
stampa_pass_docente($arr_iddoc,$arr_utdoc,$arr_pwdoc);


function stampa_pass_docente($arriddoc,$arrutdoc,$arrpwdoc)
{
	@require("../php-ini".$_SESSION['suffisso'].".php");
	require_once("../lib/fpdf/fpdf.php"); 
	$schede=new FPDF();
  // $schede->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
  // $schede->SetFont('DejaVu','',14);
   $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
    
   
   $dirigente=estrai_dirigente($con);
   
   
   $cont=0;
   foreach ($arriddoc as $iddoc)
   {
	   $schede->AddPage();
      if ($_SESSION['suffisso']!="") $suff=$_SESSION['suffisso']."/"; else $suff="";    
      $schede->Image('../abc/'.$suff.'testata.jpg',NULL,NULL,190,43);
      
      $docente = estrai_dati_docente($iddoc, $con);
      $schede->SetFont('Times','B',10);
      $posY=70;
      $schede->SetXY(105,$posY);
      $int=estrai_testo("passdoc00",$con);
      $schede->Cell(95,8,converti_utf8($int)." ".converti_utf8($docente),NULL,1,"L");
      $posY+=20;
      
      $schede->SetXY(10,$posY);
      $schede->SetFont('Times','',10);
      $comunicazione=estrai_testo("passdoc01",$con);
      $schede->write(4,converti_utf8($comunicazione)); 
      $posY=$schede->GetY();
      $posY+=20;
      $schede->SetXY(10,$posY);
      $schede->SetFont('Times','B',10);
      $schede->Cell(190,8,converti_utf8("Utente: ".$arrutdoc[$cont]),NULL,1,"C");
      $posY+=5;
      $schede->SetXY(10,$posY);
      $schede->SetFont('Times','B',10);
      $schede->Cell(190,8,converti_utf8("Password: ".$arrpwdoc[$cont]),NULL,1,"C");
      
      $posY+=10;
      $schede->SetXY(10,$posY);
      $schede->SetFont('Times','',10);
      
      $comunicazione=estrai_testo("passdoc02",$con);
      $schede->write(4,converti_utf8($comunicazione)); 
     
      $posY=$schede->GetY();
      $posY+=20;
      $schede->SetXY(10,$posY);
      $schede->SetFont('Times','',10);
      $comunicazione="Distinti saluti";
      $schede->MultiCell(190,8,converti_utf8($comunicazione));
      $posY+=10;
      
      
      
      $schede->SetXY(10,$posY);
      $schede->SetFont('Times','',10);
      $comunicazione="$comune_scuola, ".date('d')."/".date('m')."/".date('Y');
      $schede->MultiCell(190,8,converti_utf8($comunicazione));
      $posY+=20;
      
      $posY+=20;
      $schede->SetXY(105,$posY);
      $schede->SetFont('Times','',10);
      $schede->Cell(95,8,converti_utf8("Il dirigente scolastico"),NULL,1,"C");
      $posY+=5;
      $schede->SetXY(105,$posY);
      $schede->SetFont('Times','B',10);
      $schede->Cell(95,8,converti_utf8($dirigente),NULL,1,"C");
      
      
      $cont++;   
   }
   $nomefile="PWDDocenti.pdf";
   
   $schede->Output($nomefile,"I");
   
   
   mysqli_close($con);
   
  }

