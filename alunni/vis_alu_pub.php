<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
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

/*programma per la visualizzazione di un componente scelto di una classe con parametro in 
  ingresso "idcla" e parametro in uscita "idal" */
 //connessione al server
// @require_once("../php-ini".$_SESSION['suffisso'].".php");
$suffisso=$_GET['suffisso'];
if ($suffisso=="")
   $suffisso=$_POST['suffisso'];
$_SESSION['suffisso']=$suffisso;
@require_once("../lib/funzioni.php");
 
@require_once("../php-ini".$suffisso.".php");
 // istruzioni per tornare alla pagina di login 
////session_start();
//$tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
//if ($tipoutente=="")
//{
//   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
//   die;
//}
 $_SESSION["annoscol"]=$annoscol; //prende la variabile presente nella sessione
 $_SESSION['versione']=$versioneprecedente;	
 $_SESSION['log']=$logcompleto;	
$titolo="Elenco alunni per classe";
$script="<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>"; 
stampa_head($titolo,"",$script,"MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 
 $idcla= stringa_html('idcla');
 
 
 
 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
 if (!$con)
   print("<h1>Connessione al server fallita</h1>");
 $db=true;
 if (!$db)
   print"<h1>Connessione nel database fallita</h1>";
 
 // SELEZIONE CLASSE
 
 $sql="SELECT * FROM tbl_classi ORDER BY anno,specializzazione, sezione";
	if (!($res=mysqli_query($con,inspref($sql))))
	{
	 print ("Query fallita"); 
	}
	else
	{ 
		
		
	    print "<form method='POST' action='vis_alu_pub.php' name='alunni'>";
	    print "<center>";
	    print "<input type='hidden' name='suffisso' value='$suffisso'>";
	    print "<b>Seleziona classe:</b> <select name='idcla' ONCHANGE='alunni.submit()'><option value=''>&nbsp;</option>";
	    while ($dati=mysqli_fetch_array($res))
       {
        // print("<tr> <td> <font size='3'> <a href='vis_alu.php?idcla=".$dati['idclasse']."'> ".$dati['anno']." ".$dati['sezione']." ".$dati['specializzazione']." </a> </font> </td> </tr>");
         if ($idcla==$dati['idclasse'])
            print("<option value='".$dati['idclasse']."' selected> ".$dati['anno']." ".$dati['sezione']." ".$dati['specializzazione']."  </option>");
         else
            print("<option value='".$dati['idclasse']."'> ".$dati['anno']." ".$dati['sezione']." ".$dati['specializzazione']."  </option>");   
        }
        
       print "</select>";
     }
 print "</form>";
 
 
 
 

if ($idcla!="")
{ 
 
  $sq="SELECT * FROM tbl_classi
       WHERE idclasse='$idcla' " ;
 $res=mysqli_query($con,inspref($sq));
 $dati1=mysqli_fetch_array($res);
 
//imposta la tabella del titolo
	print("<table width='100%'>
		<tr>
		   <td align ='center' bgcolor='white'><strong><font size='+1'><br>Alunni della ".$dati1['anno']." ".$dati1['sezione']." ".$dati1['specializzazione']."</font></strong></td>
		</tr>
		</table> <br/>");  
 $sql="SELECT * FROM tbl_alunni,tbl_utenti
       WHERE tbl_alunni.idalunno=tbl_utenti.idutente 
       AND idclasse='$idcla' ORDER BY cognome,nome";
 $result=mysqli_query($con,inspref($sql));
 print"<center>";
 print("<table border=1>");
 print("<tr class='prima'>");
 print("<td align='center'><b> Cognome</b> </td>");
 print("<td align='center'><b> Nome</b> </td>");
 
 print ("</tr>");
 if (!(mysqli_num_rows($result)>0))  
   { 
	print("<tr bgcolor='#cccccc'><td colspan='7'><center><b>Nessun alunno presente</b></td></tr>"); 
  }
 else  
   { 
	 while($dati=mysqli_fetch_array($result))
      {
       //comunicazione tra le tabelle tbl_alunni,tbl_comuni,tbl_tutori per il passaggio dei valori
       print("<tr class='oddeven'>");
       print("<td>".$dati['cognome']."</td><td>". $dati['nome'] ."</td>");
      
	   
       print("</tr>");
     }
   
   }
    print("</table><br/>");
 }   
    mysqli_close($con);
    stampa_piede("",false);
    

