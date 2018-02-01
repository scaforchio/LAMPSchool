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
 
 $idgruppo=stringa_html('idgruppo');
 $iddocente=stringa_html('iddocente');
 $idmateria=stringa_html('idmateria');
 $titolo="Aggiornamento lista alunni di un gruppo";
 $script="";
 stampa_head($titolo,"",$script,"SDMAP");
 stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));


// ELIMINO LA PRECEDENTE LISTA DI DISTRIBUZIONE
// $query="delete from tbl_diffusionecircolari where idcircolare=$idcircolare";
// mysqli_query($con,inspref($query)) or die("ERRORE: ".inspref($query)."-".mysqli_error($con)); 

// VERIFICO PER OGNI ALUNNO SE E' DA INSERIRE NEL GRUPPO
$query="select idalunno from tbl_alunni";
$ris=mysqli_query($con,inspref($query)) or die("ERRORE: ".inspref($query)."-".mysqli_error($con)); 
while ($rec=mysqli_fetch_array($ris))
{
	$nomecb="cb".$rec['idalunno'];
	$ins=stringa_html($nomecb);
	// print "tttt ".$nomecb." - ".$ins;
   $query="select * from tbl_gruppialunni where idgruppo=$idgruppo and idalunno=".$rec['idalunno'];
   $ris2= mysqli_query($con,inspref($query)) or die("ERRORE: ".inspref($query)."-".mysqli_error($con)); 
   if (mysqli_num_rows($ris2)==0)
   { 
      if ($ins=='yes')
      {
		   $query="insert into tbl_gruppialunni(idalunno,idgruppo) 
		        values (".$rec['idalunno'].",$idgruppo)";
         mysqli_query($con,inspref($query)) or die("ERRORE: ".inspref($query)."-".mysqli_error($con)); 
	   }
	}
	else
	{
		if ($ins!='yes')
      {
		   $query="delete from tbl_gruppialunni where idgruppo=$idgruppo and idalunno=".$rec['idalunno'];
         mysqli_query($con,inspref($query)) or die("ERRORE: ".inspref($query)."-".mysqli_error($con)); 
	   }
	}
}

print "
                 <form method='post' id='formalu' action='../lezionigruppo/vis_gru.php'>
                 <input type='hidden' name='idgruppo' value='$idgruppo'>
                 <input type='hidden' name='iddocente' value='$iddocente'>
                 <input type='hidden' name='idmateria' value='$idmateria'>
                 <input type='hidden' name='insok' value='1'>
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formalu').submit();
                 }
                 </SCRIPT>";
                 
mysqli_close($con);
stampa_piede();


