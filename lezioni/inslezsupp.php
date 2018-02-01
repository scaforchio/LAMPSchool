<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo 
e/o modificarlo secondo i termini della 
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


$titolo="Inserimento lezione";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

 $ins=false;
 $gio=stringa_html('gio');
 $mese=stringa_html('mese');
 $anno=stringa_html('anno');
 $codlez=stringa_html('codlezione');
 $materia=stringa_html('materia');
 $iddocente=stringa_html('iddocente');
 $data=$anno."-".$mese."-".$gio;  
 $idclasse=stringa_html('cl');
 $argomenti=elimina_apici(stringa_html('argomenti')); 
 $attivita=elimina_apici(stringa_html('attivita')); 
 $numeroore=stringa_html('orelezione');
 $orainizio=stringa_html('orainizio');
 $provenienza=stringa_html('provenienza');


 //print "Numero ore".$numeroore;
 //print $codlez;
 
 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
  
 

 // INSERIMENTO, CANCELLAZIONE O UPDATE DATI LEZIONE   DA RIVEDERE PER INSERIMENTO PRESENZA
$ope='';
if ($codlez!='')
{
   
    if ((($argomenti != "") | ($attivita != "")) | ($numeroore != ""))
    { 
       $ope='U'; 
       $query="update tbl_lezioni set numeroore='$numeroore',orainizio='$orainizio',argomenti='$argomenti',attivita='$attivita' where idlezione=$codlez";
    }
    else
    {
	   $ope='D';
       $query="delete from tbl_lezioni where idlezione=$codlez";
    }
}
else
{   
    $ope='I';
    $query="insert into tbl_lezioni(idclasse,datalezione,iddocente,idmateria,numeroore,orainizio,argomenti,attivita) values ('$idclasse','$data','$iddocente','$materia','$numeroore','$orainizio','".elimina_apici($argomenti)."','".elimina_apici($attivita)."')";
}
if ($ope=='I')
{
    $ris3=mysqli_query($con,inspref($query)) or die ("Errore nella query di inserimento: ". mysqli_error($con)); 
    $codlez=mysqli_insert_id($con); 
   
    // Inserimento firma
    $queryinsfirma="insert into tbl_firme(idlezione,iddocente) values ('$codlez','$iddocente')";
    $ris4=mysqli_query($con,inspref($queryinsfirma)) or die ("Errore nella query di inserimento: ". mysqli_error($con)); 
     print "<center><b>Inserimento effettuato!</b></center>";
}
if ($ope=='U')
{
	
	$ris3=mysqli_query($con,inspref($query)) or die ("Errore nella query di aggiornamento: ". mysqli_error($con)); 
   
   $querycercafirma="select * from tbl_firme where idlezione='$codlez' and iddocente='$iddocente'";
   // print inspref($querycercafirma);
   $resfirma=mysqli_query($con,inspref($querycercafirma)) or die(mysqli_error($con));
   if (mysqli_num_rows($resfirma)==1)
   {
		
		// Aggiorno il timestamp della firma
		$queryinsfirma="delete from tbl_firme where iddocente=$iddocente and idlezione=$codlez";
		$ris4=mysqli_query($con,inspref($queryinsfirma)) or die ("Errore nella query di inserimento: ". mysqli_error($con)); 
      $queryinsfirma="insert into tbl_firme(idlezione,iddocente) values ('$codlez','$iddocente')";
      $ris4=mysqli_query($con,inspref($queryinsfirma)) or die ("Errore nella query di inserimento: ". mysqli_error($con)); 
	}
   else
   {
      $queryinsfirma="insert into tbl_firme(idlezione,iddocente) values ('$codlez','$iddocente')";
      $ris4=mysqli_query($con,inspref($queryinsfirma)) or die ("Errore nella query di inserimento: ". mysqli_error($con)); 
   }
   print "<center><b>Aggiornamento effettuato!</b></center>"; 
}
if ($ope=='D')
{
	$ris3=mysqli_query($con,inspref($query)) or die ("Errore nella query di cancellazione: ". mysqli_error($con)); 
    print "<center><b>Cancellazione effettuata!</b></center>";
}

if ($_SESSION['regcl']!="")
    {
		 $pr=$_SESSION['prove'];
		 $cl=$_SESSION['regcl'];
		 $ma=$_SESSION['regma'];
		 $gi=$_SESSION['reggi'];
		 $_SESSION['regcl']="";
		 $_SESSION['regma']="";
		 $_SESSION['reggi']="";
        print "
        <form method='post' id='formlezsup' action='../regclasse/$pr'>
        <input type='hidden' name='gio' value='$gi'>
        <input type='hidden' name='meseanno' value='$ma'>
        <input type='hidden' name='idclasse' value='$cl'>
        </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formlezsup').submit();
        }
        </SCRIPT>";
	 }   
    else
    {

    echo "<p align='center'>";  
 
 
 
  //  codice per richiamare il form delle tbl_lezioni;
  //  tttt se si viene dal riepilogo ritornare al riepilogo passando l'idlezione  
  print ('
   <form action="lezsupp.php" method="POST">
   <p align="center">');

  // Se la lezione non è stata cancellata si passa il codice 
   if ($ope!='D')
     print ('<p align="center"><input type=hidden value='.$codlez.' name=idlezione>');

   print('<input type="submit" value="OK" name="b"></p></form>');
}
stampa_piede(""); 

