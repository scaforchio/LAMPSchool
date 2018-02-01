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
 
 if ($tipoutente=="")
 {
   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']);
   die;
 }
 $iddocente=$_SESSION["idutente"];

// CONNESSIONE AL DATABASE
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

if(!isset($_GET)) $_GET = $HTTP_GET_VARS;

if($_GET["action"] && $_GET["Id"] && is_numeric($_GET["Id"])) {

$type="application/pdf";
switch($_GET["action"]) {

// VISUALIZZAZIONE
case "view" :

   $query = "select docbin, docnome, doctype,docmd5 from tbl_documenti where iddocumento = '". $_GET["Id"] . "'";
   
   $select = mysqli_query($con,inspref($query)) or die("Query fallita !");

   $result = mysqli_fetch_array($select);

   $data = $result["docbin"];
   $name = $result["docnome"];
   $type = $result["doctype"];
   $hashmd5 = $result["docmd5"];
   if (strlen($data)>0)
   {
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT" );
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
		header("Cache-Control: no-store, no-cache, must-revalidate" );
		header("Cache-Control: post-check=0, pre-check=0", false );
		header("Pragma: no-cache" ); 
		header("Content-Type: application/pdf");
		header("Content-Disposition: inline; filename=".$name);
		echo $data;
   }
   else
   {
		if ($_SESSION['suffisso']!="") $suff=$_SESSION['suffisso']."/"; else $suff="";    
		$origine="../lampschooldata/$suff$hashmd5";
      $destinazione="$cartellabuffer/$name";
      copy($origine,$destinazione);
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT" );
      header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
      header("Cache-Control: no-store, no-cache, must-revalidate" );
      header("Cache-Control: post-check=0, pre-check=0", false );
      header("Pragma: no-cache" ); 
      header("Content-Type: $type");
      header("Content-Disposition: inline; filename=".$name);
		readfile($destinazione);
	}
   break;

   // DOWNLOAD
case "download" :

   $query = "select docbin, docnome, doctype, docmd5 from tbl_documenti where iddocumento = '" . $_GET["Id"] . "'";
   
   $select = mysqli_query($con,inspref($query)) or die("Query fallita !");

   $result = mysqli_fetch_array($select);

   $data = $result["docbin"];
   $name = $result["docnome"];
   $type = $result["doctype"];
   $hashmd5 = $result["docmd5"];
   // RIVEDERE TTTTT
   // SE IL BROWSER È INTERNET EXPLORER
   // if(ereg("MSIE ([0-9].[0-9]{1,2})", $_SERVER["HTTP_USER_AGENT"])) {

   //header("Content-Type: application/octetstream");
   //header("Content-Type: application/pdf");
   //header("Content-Disposition: inline; filename=$name");
   //header("Expires: 0");
   //header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
   //header("Pragma: public");

   //} 
   //else 
   //{

   if (strlen($data)>0)
   {
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT" );
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
		header("Cache-Control: no-store, no-cache, must-revalidate" );
		header("Cache-Control: post-check=0, pre-check=0", false );
		header("Pragma: no-cache" ); 
		header("Content-Type: application/pdf");
		header("Content-Disposition: attachment; filename=$name");
   //}

   echo $data;
	}
	else
	{
		if ($_SESSION['suffisso']!="") $suff=$_SESSION['suffisso']."/"; else $suff="";    
		$origine="../lampschooldata/$suff$hashmd5";
      $destinazione="$cartellabuffer/$name";
      
      copy($origine,$destinazione);
      header("Expires: Sat, 26 Jul 1997 05:00:00 GMT" );
      header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
      header("Cache-Control: no-store, no-cache, must-revalidate" );
      header("Cache-Control: post-check=0, pre-check=0", false );
      header("Pragma: no-cache" ); 
      header("Content-Type: $type");
      header("Content-Disposition: attachment; filename=".$name);
      readfile($destinazione);
	}

   break;

default :

   // DEFAULT CASE, NESSUNA AZIONE

   break;

} // endswitch

// CHIUDIAMO LA CONNESSIONE
mysqli_close($con);

} //endif


