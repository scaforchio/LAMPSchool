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
 
 $descrizione=stringa_html('descrizione');
 $destinatari=stringa_html('destinatari');
 $ricevuta=stringa_html('ricevuta');
 $datainserimento=data_to_db(stringa_html('datainserimento'));

 if ($tipoutente=="")
 {
    header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
    die;
 } 

 $titolo="Inserimento circolare";
 $script="";
 stampa_head($titolo,"",$script,"MSPA");
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
                               (descrizione, idtipodocumento,   datadocumento,   docbin,     docmd5,          docnome,    docsize,                       doctype)
                        values ('$descrizione','1000000005','$datainserimento','".$data."','".$md5data."','".$nome."','".$filedainserire['size'] ."','$tipofile')";
               $result = mysqli_query($con,inspref($queryins)) or die("Errore:".inspref($queryins));
               $iddocumento=mysqli_insert_id($con); 
           }
           else
           {
				  $queryins = "insert into tbl_documenti
                               (descrizione, idtipodocumento,   datadocumento,     docmd5,          docnome,    docsize,                       doctype)
                        values ('$descrizione','1000000005','$datainserimento','".$md5data."','".$nome."','".$filedainserire['size'] ."','$tipofile')";
               $result = mysqli_query($con,inspref($queryins)) or die("Errore:".inspref($queryins));
               
               $iddocumento=mysqli_insert_id($con); 
               crea_file($filedainserire,$md5data);
           }    
           
          // INSERIMENTO CIRCOLARE
          $queryins = "insert into tbl_circolari
                               (iddocumento,descrizione, destinatari,ricevuta, datainserimento)
                        values ('$iddocumento','$descrizione','$destinatari','$ricevuta','$datainserimento')";
          $result = mysqli_query($con,inspref($queryins)) or die("Errore:".inspref($queryins));
          $idcircolare=mysqli_insert_id($con); 
          
          // INSERIMENTO LISTA DI DISTRIBUZIONE
			 if ($destinatari=='A' || $destinatari=='I' || $destinatari=='D')
			 {
				 $dest='';
				
				 if ($destinatari=='A')
				 {
					 $dest="where tipo in ('T')";
				 }
				 if ($destinatari=='D')
				 {
					 $dest="where tipo in ('D','S')";
				 }
				 if ($destinatari=='I')
				 {
					 $dest="where tipo in ('A')";
				 }
				 
				 
				 $query="select idutente from tbl_utenti ".$dest;
				 $ris=mysqli_query($con,inspref($query));
				 while ($rec=mysqli_fetch_array($ris))
				 {
					 $idutente=$rec['idutente'];
					 $queryins="insert into tbl_diffusionecircolari(idcircolare,idutente)
											 values($idcircolare,$idutente)";
					 mysqli_query($con,inspref($queryins));                  
				 }
				 
				 //echo "<br><center><font color='green'>Il file " . basename($filedainserire['name']) . " è stato correttamente inserito nel Database.<br>";
				 
				 print "
						  <form method='post' id='formdoc' action='../circolari/circolari.php'>
						  
						  <input type='hidden' name='destinatari' value='$destinatari'>
						  </form> 
						  <SCRIPT language='JavaScript'>
						  {
								document.getElementById('formdoc').submit();
						  }
						  </SCRIPT>";
			}
			else  // DESTINATARI DA SELEZIONARE
			{
				if ($destinatari=='SA')
				{
					print "
                 <form method='post' id='formalu' action='../circolari/selealunni.php'>
                 <input type='hidden' name='idcircolare' value='$idcircolare'>
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formalu').submit();
                 }
                 </SCRIPT>";
				}
				if ($destinatari=='SD')
				{
					
					print "
                 <form method='post' id='formdoc' action='../circolari/seledocenti.php'>
                 <input type='hidden' name='idcircolare' value='$idcircolare'>
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formdoc').submit();
                 }
                 </SCRIPT>";
				}
				if ($destinatari=='SI')
				{
					print "
                 <form method='post' id='formimp' action='../circolari/seleimpiegati.php'>
                 <input type='hidden' name='idcircolare' value='$idcircolare'>
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formimp').submit();
                 }
                 </SCRIPT>"; 
				}
			}
      }
      else
      {
			echo "<br><center><font color='red'>Il file " . basename($filedainserire['name']) . " non è un file permesso (pdf, jpeg, gif, png)!</font></center><br>";
		}
		
}
else
   print "
                 <form method='post' id='formdoc' action='../circolari/circolari.php'>
                 <input type='hidden' name='destinatari' value='$destinatari'>
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
      if ($_SESSION['suffisso']!="") $suff=$_SESSION['suffisso']."/"; else $suff="";    
      move_uploaded_file( $filedainserire['tmp_name'], "../lampschooldata/$suff$hashmd5" );
      
   }
   catch (Exception $e)
   {
		die ("Errore nel caricamento del file ".$e->getMessage());
	}
		
}


