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
 $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
    if ($tipoutente=="")
       {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
       } 

$concesso=stringa_html('concesso');
$idassemblea=stringa_html('idassemblea');
$iddocente=$_SESSION['idutente'];
$titolo="Concessione assemblea";
$script=""; 
stampa_head($titolo,"",$script,"SD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='assdoc.php'>Assemblee di classe</a> - $titolo","","$nome_scuola","$comune_scuola");

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore: ".mysqli_error($con));
$query = "SELECT * FROM tbl_assemblee WHERE idassemblea=$idassemblea";
$ris = mysqli_query($con, inspref($query)) or die (mysqli_error($con));
$data = mysqli_fetch_array($ris);
$qmod = "UPDATE tbl_assemblee SET ";
if($concesso==1)
{		
	if($data['docenteconcedente1']==$iddocente and $data['docenteconcedente2']==$iddocente)
	{
		$qmod .= "concesso1=1, concesso2=1";
	}
	else
	{		 
		if($data['docenteconcedente1']==$iddocente)
		{
			$qmod .= "concesso1=1";
		}
		if($data['docenteconcedente2']==$iddocente)
		{
			$qmod .= "concesso2=1";
		}
	}
}
else
{
	if($data['docenteconcedente1']==$iddocente and $data['docenteconcedente2']==$iddocente)
	{
		$qmod .= "concesso1=2, concesso2=2";
	}
	else
	{		 
		if($data['docenteconcedente1']==$iddocente)
		{
			$qmod .= "concesso1=2";
		}
		if($data['docenteconcedente2']==$iddocente)
		{
			$qmod .= "concesso2=2";
		}
	}
	
}
$qmod .= " WHERE idassemblea=$idassemblea";
$rismod = mysqli_query($con, inspref($qmod)) or die (mysqli_error($con). "<br/>". $qmod);             
$ris=mysqli_query($con,inspref($query)) or die ("Errore : ". inspref($query));

print ("<form method='post' action='assdoc.php' id='formdisp'>
			<input type='hidden' name='iddocente' value='".$iddocente."'>
        </form> 
        <SCRIPT language='JavaScript'>
            {
                document.getElementById('formdisp').submit();
            }
        </SCRIPT>  
      
       ");
mysqli_close($con);
stampa_piede("");

