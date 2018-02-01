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

$titolo="Elenco genarale alunni";
$script=""; 
stampa_head($titolo,"",$script,"MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 
 $modo=stringa_html('modo');
 $n= stringa_html('idcla');
 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
 if (!$con)
   print("<h1>Connessione al server fallita</h1>");
 
 $sql="SELECT * FROM tbl_alunni,tbl_utenti,tbl_classi
       WHERE tbl_alunni.idalunno=tbl_utenti.idutente
       AND tbl_alunni.idclasse=tbl_classi.idclasse
       ORDER by specializzazione,anno,sezione,cognome,nome,datanascita";
 $result=mysqli_query($con,inspref($sql));
 print"<center>";
 print("<table border=1>");
 print("<tr class='prima'>");
 print("<td align='center'><b> Cognome</b> </td>");
 print("<td align='center'><b> Nome</b> </td>");
 print("<td align='center'> <b>Data di Nascita </b></td>");
 print("<td align='center'><b>Id. Utente</b> </td>");
 print("<td align='center'><b>Telefono</b> </td>");
 print("<td align='center' ><b> E-mail</b> </td>") ;
 print("<td align='center' ><b> Cert.</b> </td>") ;
 print("<td align='center' ><b> Note</b> </td>") ;
 print("<td align='center' ><b> Classe</b> </td>") ;
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
      
	   print("<td>". data_italiana($dati['datanascita'])."</td>");
	   print("<td>".$dati['userid']."</td>");
      if ($dati['telefono']) 
	      {print("<td>".$dati['telefono'] ."</td>");}
	   else
	      {print("<td>".$dati['telcel'] ."</td>");}
      print("<td><a href='MAILTO:".$dati['email']."'>".$dati['email']."</A></td>");
      if ($dati['certificato']) 
	      print("<td><img src='../immagini/apply_small.png'></td>");
	   else
	      print("<td>&nbsp;</td>");
      print("<td>".$dati['note']."</td>");
	   print("<td>".decodifica_classe($dati['idclasse'],$con)."</td>"); 
	   
       print("</tr>");
     }
   
   }
    print("</table><br/>");
    

    mysqli_close($con);
    stampa_piede("");
    

