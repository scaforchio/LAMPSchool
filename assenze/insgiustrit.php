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


$titolo="Inserimento giustificazioni ritardi";
$script="<script type='text/javascript'>
 <!--
  var stile = 'top=10, left=10, width=600, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
     function Popup(apri) {
        window.open(apri, '', stile);
     }
 //-->
</script>"; 

stampa_head($titolo,"",$script,"MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 
// stampa_head($titolo,"",$script);

$idalunno = stringa_html('idalunno');
$idclasse = stringa_html('idclasse');
$data = stringa_html('data');
  if (($idalunno!=""))
  {
        
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
     
    $query='select * from tbl_ritardi where idalunno="'.$idalunno.'" and not giustifica order by data ';
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
	if (mysqli_num_rows($ris)>0)
	{
	   while($val=mysqli_fetch_array($ris))
       {
		  $idgiu = stringa_html('giu'.$val['idritardo'])?"on":"off";
          if ($idgiu=="on")
          {
             $query="update tbl_ritardi set giustifica=1, datagiustifica='".date("Y-m-d")."', iddocentegiust=".$_SESSION['idutente']." where idritardo=".$val['idritardo']."";
             $risupd=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con)); 
          } 
		   
	      
	   }
	   	 
	   $giorno=estraigiorno($data);
	   $meseanno=estraimese($data).' - '.estraianno($data);
      print "
        <form method='post' id='formrit' action='../assenze/rit.php'>
        <input type='hidden' name='gio' value='$giorno'>
        <input type='hidden' name='meseanno' value='$meseanno'>
        <input type='hidden' name='cl' value='$idclasse'>
        </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formrit').submit();
        }
        </SCRIPT>";
   }

}
 // fine if
stampa_piede("");  
mysqli_close($con);


