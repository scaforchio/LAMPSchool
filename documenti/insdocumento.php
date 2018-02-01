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
 
 // DA SOSTITUIRE CON PARAMETRO
 //$memdati='db'; // Oppure 'hd' (Database o HardDisk) Funzionante da estendere a PDL, Prog e Relazioni
 
 $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
 
 $iddocente=$_SESSION["idutente"];
 $tipo=stringa_html('tipo');
 $idclasse=stringa_html('idclasse');
 $idcattedra=stringa_html('idcattedra');
 $idalunno=stringa_html('idalunno');
 $idmateria=stringa_html('idmateria');
 $descrizione=stringa_html('descrizione');
 $idtipodocumento=stringa_html('idtipodocumento');
 $datadocumento=stringa_html('datadocumento');
 $pei=(stringa_html('pei')=='yes')?1:0;
 
 $datadocumento= data_to_db($datadocumento);
 
    if ($tipoutente=="")
       {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
       } 

 $titolo="Inserimento documento alunno";
 $script="";
 stampa_head($titolo,"",$script,"SDMAP");
 stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));


$filedainserire="";
if (isset($_FILES['filedocumento']))
   $filedainserire=$_FILES['filedocumento'];


if ($filedainserire['tmp_name']!="")
{
	   $estensione=substr($filedainserire['name'],-4,4);
	   $filepermesso=(substr($filedainserire['name'],-4,4)==".pdf") || (substr($filedainserire['name'],-4,4)==".jpg") || (substr($filedainserire['name'],-4,4)==".png") || (substr($filedainserire['name'],-4,4)==".gif") || (substr($filedainserire['name'],-5,5)==".jpeg") || (substr($filedainserire['name'],-4,4)==".PDF") || (substr($filedainserire['name'],-4,4)==".JPG") || (substr($filedainserire['name'],-4,4)==".PNG") || (substr($filedainserire['name'],-4,4)==".GIF") || (substr($filedainserire['name'],-5,5)==".JPEG");
		$tipofile='';
		switch ($estensione)
		{
			case '.pdf':
			   $tipofile='application/pdf'; break;
		   case '.jpg':
			   $tipofile='image/jpeg'; break;
			case 'jpeg':
			   $tipofile='image/jpeg'; break; 
			case '.gif':
			   $tipofile='image/gif'; break;     
			case '.png':
			   $tipofile='image/png'; break;        
			case '.PDF':
			   $tipofile='application/pdf'; break;
		   case '.JPG':
			   $tipofile='image/jpeg'; break;
			case 'JPEG':
			   $tipofile='image/jpeg'; break; 
			case '.GIF':
			   $tipofile='image/gif'; break;     
			case '.PNG':
			   $tipofile='image/png'; break;        
		}
		if ($filepermesso)
		{
		     $data = addslashes(fread(fopen($filedainserire['tmp_name'], "rb"), $filedainserire["size"]));
		     // SOSTITUISCO GLI SPAZI CON GLI UNDERSCORE PER PROBLEMI CON BROWSER IPAD
		     $nome = str_replace(" ","_",$filedainserire['name']);
		     $nome = elimina_apici($nome);
		     $md5data=md5($data);
		     if ($gestionedocumenti=='db')
		     {
               $queryins = "insert into tbl_documenti
                               (idmateria,   descrizione,   idclasse,   idalunno,   iddocente,   idtipodocumento, pei,   datadocumento,   docbin,     docmd5,          docnome,    docsize,                       doctype)
                        values ('$idmateria','$descrizione','$idclasse','$idalunno','$iddocente','$idtipodocumento',$pei,'$datadocumento','".$data."','".$md5data."','".$nome."','".$filedainserire['size'] ."','$tipofile')";
               $result = mysqli_query($con,inspref($queryins)) or die("Errore:".inspref($queryins));
           }
           else
           {
				  $queryins = "insert into tbl_documenti
                               (idmateria,   descrizione,   idclasse,   idalunno,   iddocente,   idtipodocumento, pei,  datadocumento,     docmd5,          docnome,    docsize,                       doctype)
                        values ('$idmateria','$descrizione','$idclasse','$idalunno','$iddocente','$idtipodocumento',$pei,'$datadocumento','".$md5data."','".$nome."','".$filedainserire['size'] ."','$tipofile')";
               $result = mysqli_query($con,inspref($queryins)) or die("Errore:".inspref($queryins));
           
               crea_file($filedainserire,$md5data);
           }    
          // ESITO POSITIVO
          
          //echo "<br><center><font color='green'>Il file " . basename($filedainserire['name']) . " è stato correttamente inserito nel Database.<br>";
          
          print "
                 <form method='post' id='formdoc' action='../documenti/documenti.php'>
                 <input type='hidden' name='idclasse' value='$idclasse'>
                 <input type='hidden' name='idalunno' value='$idalunno'>
                 <input type='hidden' name='tipo' value='$tipo'>
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formdoc').submit();
                 }
                 </SCRIPT>";
      }
      else
      {
			echo "<br><center><font color='red'>Il file " . basename($filedainserire['name']) . " non è un file permesso (pdf, jpeg, gif, png)!</font></center><br>";
		}
		
}
else
   print "
                 <form method='post' id='formdoc' action='../documenti/documenti.php'>
                 <input type='hidden' name='idclasse' value='$idclasse'>
                 <input type='hidden' name='idalunno' value='$idalunno'>
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formdoc').submit();
                 }
                 </SCRIPT>";
                 
mysqli_close($con);
stampa_piede();

        
function crea_file($filedainserire,$hashmd5)

{
   try
   {
      //$cart1=substr($hashmd5,0,2);
      //$cart2=substr($hashmd5,2,2);
      //mkdir("../lampschooldata/$cart1");
      //mkdir("../lampschooldata/$cart1/$cart2");
      if ($_SESSION['suffisso']!="") $suff=$_SESSION['suffisso']."/"; else $suff="";    
      move_uploaded_file( $filedainserire['tmp_name'], "../lampschooldata/$suff$hashmd5" );
      
   }
   catch (Exception $e)
   {
		die ("Errore nel caricamento del file ".$e->getMessage());
	}
		
}


