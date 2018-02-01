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


$titolo="Inserimento e modifica tipo programmazione alunni certificati";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 
$idalunno = stringa_html('idalunno');
$iddocente = $_SESSION['idutente'];
$sostegno = $_SESSION['sostegno'];
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

print ('
   
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Alunno</b></p></td>
      <td width="50%"><form method="post" action="seletipoprogr.php" name="tipoprogr">
      <SELECT ID="alunno" NAME="idalunno" ONCHANGE="tipoprogr.submit()">
      <option value="">&nbsp;  '); 
	  
  
// Riempimento combo box alunni

if (!$sostegno)
    $query="select idalunno,cognome,nome,datanascita,idclasse 
            from tbl_alunni
            where certificato
            and idclasse in (select idclasse from tbl_cattnosupp where iddocente=$iddocente)
            order by cognome, nome,datanascita";
else
   $query="select idalunno,cognome,nome,datanascita,idclasse 
            from tbl_alunni
            where certificato
            and idalunno in (select idalunno from tbl_cattnosupp where iddocente=$iddocente)
            order by cognome, nome,datanascita";         
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{
   print "<option value='";
   print ($nom["idalunno"]);
   print "'";
   if ($idalunno==$nom["idalunno"])
	print " selected";
   print ">";
   print ($nom["cognome"]);
   print "&nbsp;"; 
   print($nom["nome"]); 
   print "&nbsp;(";
   print($nom["datanascita"]);
   print ") - ";
   print(decodifica_classe($nom["idclasse"],$con));
}
        
echo('
      </SELECT></form>
      </td></tr></table>');



if ($idalunno!="")
{

   $idclasse=estrai_classe_alunno($idalunno,$con);
   
   if (!$sostegno)
    $query="select tbl_cattnosupp.idmateria, denominazione 
           from tbl_cattnosupp,tbl_materie
           where tbl_cattnosupp.idmateria=tbl_materie.idmateria
           and idclasse=$idclasse
           and iddocente=$iddocente
           order by denominazione";
   else
    $query="select distinct(tbl_cattnosupp.idmateria), denominazione 
           from tbl_cattnosupp,tbl_materie
           where tbl_cattnosupp.idmateria=tbl_materie.idmateria
           and idclasse=$idclasse order by denominazione";
         //  >>> and iddocente=$iddocente <<<      DA AGGIUNGERE SE DOCENTE DI SOSTEGNO PUO' INTERVENIRE SOLO SU MATERIE DELLA SUA CATTEDRA
   $ris=mysqli_query($con,inspref($query));
   
   print "<form method='post' action='instipoprogr.php' name='instipoprogr'>";
   print "<table border=1 align=center>
          <tr class='prima'><td>Materia</td><td>Tipo programmazione</td></tr>";
   $contmat=0;
   while ($rec=mysqli_fetch_array($ris))
   {
		$contmat++;
		print "<tr><td>".$rec['denominazione']."<input type='hidden' name='mat$contmat' value='".$rec['idmateria']."'></td><td>";
		
		$tipoprog=estrai_tipo_prog($idalunno,$rec['idmateria'],$con);
		$selV="";$selN="";$selO="";$selP="";
		if($tipoprog=="")
		   $selV=" selected";
		if($tipoprog=="N")
		   $selN=" selected";
		if($tipoprog=="O")
		   $selO=" selected";
		if($tipoprog=="P")
		   $selP=" selected";         
		
		print "<select name='tipo$contmat'>";
		print "<option value=''$selV>&nbsp;</option>";
		print "<option value='N'$selN>Normale</option>";
		print "<option value='O'$selO>Obiettivi minimi</option>";
		print "<option value='P'$selP>Personalizzata</option>";
		print "</select>";
		print "</td></tr>";
	}
   print "</table><br><br>";
   print "<input type='hidden' name='nummat' value='$contmat'>
          <input type='hidden' name='idalunno' value='$idalunno'>
          <center><input type='submit' value='Inserisci tipo programmazione'></center>
          </form>";
}           

// fine if
 

mysqli_close($con);
stampa_piede(""); 

