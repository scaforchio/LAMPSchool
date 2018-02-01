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
//print "Sono qui $numeropassword";
$arrut=stringa_html("arrut");
$arrid=stringa_html("arrid");
$arrpw=stringa_html("arrpw");
$arr_idalu=explode("|",$arrid);
$arr_utalu=explode("|",$arrut);
$arr_pwalu=explode("|",$arrpw);
//print $arrut;
/*
 *   NON si è potuto usare per gli alunni lo stesso sistema dei docenti per il limite
 *   di alcuni hosting al valore max_input_vars che è impostato a 1000. Questo impedisce di inviare
 *   più di 1000 campi in un form
 * 
$arr_idalu=array();
$arr_utalu=array();
$arr_pwalu=array();

for ($i=1;$i<=$numeropassword;$i++)
{
	//  Estrarre pass, utente, iddocente da POST e creare tre array
	$idalu="idalu".$i;
	$utalu="utalu".$i;
	$pwalu="pwalu".$i;
	
	$arr_idalu[]=stringa_html($idalu);
	$arr_utalu[]=stringa_html($utalu);
	$arr_pwalu[]=stringa_html($pwalu);
}
*/ 
//  Richiamare funzione di stampa passando gli array come parametri


stampa_pass_alunno($arr_idalu,$arr_utalu,$arr_pwalu);


function stampa_pass_alunno($arridalu,$arrutalu,$arrpwalu)
{
	@require("../php-ini".$_SESSION['suffisso'].".php");
	require_once("../lib/fpdf/fpdf.php");
	$schede=new FPDF();
   
   $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
    
   
   $dirigente=estrai_dirigente($con);
   
   
   $cont=0;
   foreach ($arridalu as $idalu)
   {
	   $schede->AddPage();
      if ($_SESSION['suffisso']!="") $suff=$_SESSION['suffisso']."/"; else $suff="";    
      $schede->Image('../abc/'.$suff.'testata.jpg',NULL,NULL,190,43);
      
      $alunno = estrai_dati_alunno($idalu, $con);
      $schede->SetFont('Times','B',12);
      $posY=70;
      $schede->SetXY(105,$posY);
      $int=estrai_testo("alupassalu00",$con);
      $schede->Cell(95,8,converti_utf8($int),NULL,1,"L");
      $posY+=5;
      $schede->SetXY(105,$posY);
      $schede->Cell(95,8,converti_utf8($alunno),NULL,1,"L");
      $idcla=estrai_classe_alunno($idalu,$con);
      if ($idcla!=0)
      {
         $posY+=5;
         $schede->SetXY(105,$posY);
         $schede->Cell(95,8,converti_utf8("Classe: ".decodifica_classe($idcla,$con)),NULL,1,"L");
	   }
      $posY+=20;
      
      $schede->SetXY(10,$posY);
      $schede->SetFont('Times','',12);
    //  $comunicazione="    Con le credenziali qui fornite potrà accedere all'area riservata ai genitori del Registro Online LAMPSchool per l'A.S. $annoscol-".($annoscol+1).". In tale area potrà visualizzare i dati relativi al percorso scolastico di suo figlio: assenze, ritardi, uscite anticipate, valutazioni, note, comunicazioni della  scuola, pagelle, argomenti delle lezioni, ecc.";
    //  
    //  $posY+=30;
      $comunicazione=estrai_testo("alupassalu01",$con);
      $schede->MultiCell(190,8,converti_utf8($comunicazione));
      //$schede->write(6,converti_utf8($comunicazione));
      $posY=$schede->GetY();
      $posY+=10;
      $schede->SetXY(10,$posY);
      $schede->SetFont('Times','B',12);
      $schede->Cell(190,8,converti_utf8("Utente: ".$arrutalu[$cont]),NULL,1,"C");
      $posY+=5;
      $schede->SetXY(10,$posY);
      $schede->SetFont('Times','B',12);
      $schede->Cell(190,8,converti_utf8("Password: ".$arrpwalu[$cont]),NULL,1,"C");
      
      $posY+=10;
      $schede->SetXY(10,$posY);
      $schede->SetFont('Times','',12);
      $comunicazione=estrai_testo("alupassalu02",$con);
      $schede->write(6,converti_utf8($comunicazione));
      $posY=$schede->GetY();
      $posY+=10;
      $schede->SetXY(10,$posY);
      $schede->SetFont('Times','',12);
      
      
      
      $schede->SetXY(10,$posY);
      $schede->SetFont('Times','',12);
      $comunicazione="$comune_scuola, ".date('d')."/".date('m')."/".date('Y');
      $schede->MultiCell(190,8,converti_utf8($comunicazione));
      $posY+=20;
      $schede->SetXY(105,$posY);
      $schede->SetFont('Times','',12);
      $schede->Cell(95,8,converti_utf8("Il dirigente scolastico"),NULL,1,"C");
      $posY+=5;
      $schede->SetXY(105,$posY);
      $schede->SetFont('Times','B',12);
      $schede->Cell(95,8,converti_utf8($dirigente),NULL,1,"C");
      
      
      $cont++;   
   }
   $nomefile="PWDAlunni.pdf";
   
   $schede->Output($nomefile,"I");
   
   
   mysqli_close($con);
   
  }




