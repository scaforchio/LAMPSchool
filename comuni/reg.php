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
   
   // istruzioni per tornare alla pagina di login 
	////session_start();
    $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
	if ($tipoutente=="")
	   {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
	   }
   
   // Funzione che controlla la presenza di numeri nella stringa
	function controlla_stringa($stringa)
	{
	$l=strlen($stringa);
		for ($i=0;$i<=$l-1;$i++)
			{
			$car=substr($stringa,$i,1);
				if (is_numeric($car))
				{ 
				return 1;
				break;
				}
			}
	 }  
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("connessione non riuscita"); 
    $db=true or die ("connessione al db fallita"); 
//controlla se il campo denominazione � vuoto
    print"<center>";
	print("<table border=0 width='100%'>
		<tr>
		   <td align ='center' ><strong><font size='+1'> CONFERMA INSERIMENTO COMUNE </font></strong></td>
		</tr>
		</table> <br/><br/>"); 
	$a=0;
    $de=$denominazione;
		if ($de=="")
	{
	$mess= $mess."Inserire obbligatoriamente la denominazione <br/>"; 
		$a=1;
		
	}
	else
	{
		//controlla se la denominazione � formata da sole lettere 
		if (controlla_stringa($de)==1)
		{
			$a=1;
	    $mess= $mess."Inserire solo lettere per la denominazione del comune<br/>"; 
		}  
	}
//controlla se il campo del cap � vuoto
    $cp=$cap;
	if ($cp=="")
		{
			$mess= $mess." Inserire obbligatoriamente il CAP del comune <br/> "; 
			$a=1;
		}
	else
	{	 
	//controlla se il cap � formato da soli numeri
		if(!(is_numeric (trim($cp))===true))
		{
	    	$a=1;
	    $mess= $mess."Inserire solo numeri per il CAP del comune<br/>";
		}
	}
//Controlla il cod istat
	$cod=$codistat;
	if ($cod=="")
	    {
		  $mess=$mess."Inserire obbligatoriamente il codice istat del comune<br/> "; 
		  $a=1;
		 }
	else
	{
		if(!(is_numeric (trim($cod))===true))
			{
			$a=1;
			$mess=$mess."Iserire solo numeri per il codice istat del comune<br/>";
			}
	}
		   
//controlla se il campo della sigla provincia � vuoto	
		$sp=$siglaprovincia;	if ($sp=="")

		{
			$mess= $mess." Inserire obbligatoriamente la sigla della provincia <br/>";
			$a=1; 
		}
	else
	{
	//controlla se la sigla della provincia � composta da sole lettere
		if (controlla_stringa($sp)==1)
		{
			$a=1;
			$mess= $mess."Inserire solo lettere per la sigla della provincia<br/>";
		} 
	}	
//controlla se il campo della provincia � vuoto
	$provi=$provincia;
	if ($provi=="")
		{
			$mess= $mess." Inserire obbligatoriamente la provincia <br/>"; 
			$a=1;
		}
	else
	{
	//controlla se la provincia � composta da sole lettere
		if (controlla_stringa($provi)==1)
		{
			$a=1;
			$mess= $mess."Inserire solo lettere per la provincia<br/>";
		}	
	}	
//controlla se il campo della regione � vuoto
	  $rg=$regione;
	  if ($rg=="")
		{
			$mess= $mess." Inserire obbligatoriamente la regione <br/>"; 
			$a=1;
		}
		//controlla se la regione � formata da sole lettere
		if (controlla_stringa($rg)==1)
		{
			$a=1;
	    $mess= $mess."Inserire solo lettere per la regione<br/>";
		}
//controlla se lo stato estero � formata da sole lettere
	$se=$statoestero;
	if (controlla_stringa($se)==1)
	{
		$a=1;
	$mess= $mess."Inserire solo lettere per lo stato estero";
	}
	if(!($a=="1"))
	{
$sql  = "INSERT INTO tbl_comuni (denominazione,cap,codistat,provincia,siglaprovincia,regione,statoestero) values (";
$sql .= "'". stringa_html('denominazione'). "',";
$sql .= "'". stringa_html('cap'). "',";
$sql .= "'". stringa_html('codistat'). "',";
$sql .= "'". stringa_html('provincia'). "',";
$sql .= "'". stringa_html('siglaprovincia'). "',";
$sql .= "'". stringa_html('regione'). "',";
$sql .= "'". stringa_html('statoestero'). "')";
    
           if (!($result=mysqli_query($con,inspref($sql))))
		   {
               print"<center>COMUNE NON INSERITO ALLA LISTA";
		   }
         else
		 {
		 	print"<center>NUOVO COMUNE CORRETAMENTE INSERITO";
			print("<form name='form7' action='lis_com.php' method='POST'>");
		    print("<input type='submit' value='<<Indietro'>"); 
			print("</form> </center>");
		 }
	}
	else
	{
     print"$mess";
	 print"<form name='form9' action='lis_com.php' method='POST'>";
			 print("<input type ='hidden' name='idcom' value='$idtbl_comuni'>");
             print("<input type ='hidden' name='denominazione' value='$denominazione'>");
             print("<input type ='hidden' name='cap' value='$cap'>");				  	
             print("<input type ='hidden' name='codistat' value='$codistat'>");
             print("<input type ='hidden' name='provincia' value='$provincia'>");
             print("<input type ='hidden' name='siglaprovincia' value='$siglaprovincia'>");
 			 print("<input type ='hidden' name='regione' value='$regione'>");
 			 print("<input type ='hidden' name='statoestero' value='$statoestero'>");  
 			 print("<input type ='submit' value='<< Indietro'>"); 
	print("</form>"); 
	print"</center>";
}
  mysqli_close($con);

