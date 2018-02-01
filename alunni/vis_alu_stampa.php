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
 @require_once("../php-ini".$_SESSION['suffisso'].".php");
 @require_once("../lib/funzioni.php");
 
 // istruzioni per tornare alla pagina di login 
////session_start();
$tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente=="")
{
   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
   die;
}

$titolo="Elenco alunni di una classe";
$script= "
	<script type='text/javascript'>
         <!--
            function printPage()
            {
               if (window.print)
                  window.print();
               else 
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');            }
         //-->
         </script>
";
stampa_head($titolo,"",$script,"MASP");
print ('<body class="stampa" onLoad="printPage()">');

 $n= stringa_html('idcla');
 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
 if (!$con)
   print("<h1>Connessione al server fallita</h1>");
 $db=true;
 if (!$db)
   print"<h1>Connessione nel database fallita</h1>";
  $sq="SELECT * FROM tbl_classi
       WHERE idclasse='$n' " ;
 $res=mysqli_query($con,inspref($sq));
 $dati1=mysqli_fetch_array($res);
 
//imposta la tabella del titolo
	print("<center><font size='6'><b>Classe: ".$dati1['anno']." ".$dati1['sezione']." ".$dati1['specializzazione']."</b></font></center><br>");  
 $sql="SELECT * FROM tbl_alunni,tbl_utenti
       WHERE tbl_alunni.idalunno=tbl_utenti.idutente 
       AND idclasse='$n' ORDER BY cognome,nome";
 $result=mysqli_query($con,inspref($sql));

 print("<table border=1 align='center'>");
 print("<tr>");
 print ("<td><font size='4'><b>N.</b></font></td>");
 print("<td align='center'><font size='4'><b>Cognome</b></font> </td>");
 print("<td align='center'><font size='4'><b>Nome</b></font> </td>");
 print("<td align='center'><b>Data di Nascita </b></td>");
 
 print ("</tr>");
 if (!(mysqli_num_rows($result)>0))  
   { 
	print("<tr bgcolor='#cccccc'><td colspan='7'><center><b>Nessun alunno presente</b></font></td></tr>"); 
  }
 else  
   { 
		$numero=0;
	 while($dati=mysqli_fetch_array($result))
    {
       $numero++;
       print("<tr>");
       print("<td><font size='4'>$numero</font></td>");
       print("<td><font size='4'>".$dati['cognome']."</font></td><td><font size='4'>". $dati['nome'] ."</font></td>");
      
	    print("<td>". data_italiana($dati['datanascita'])."</td>");
	    print("</tr>");
    }
   
   }
    print("</table><br/>");
    
    print ("</font>");
    
    

    mysqli_close($con);
    //stampa_piede("");
    

