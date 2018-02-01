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


$titolo="Inserimento e modifica assenze";
$script="<script type='text/javascript'>
 <!--
  var stile = 'top=10, left=10, width=600, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
     function Popup(apri) {
        window.open(apri, '', stile);
     }
 //-->
</script>"; 

stampa_head($titolo,"",$script,"MASP");
$menu="<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - ATTRIBUZIONE CLASSE";
stampa_testata("$menu","","$nome_scuola","$comune_scuola");

print ('
   <form method="post" action="ins_attr_classe.php" name="tbl_assenze">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe da attribuire</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="cl"> <option>&nbsp '); 
	  
  
       
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
 
$query="select idclasse,anno,sezione,specializzazione from tbl_classi order by specializzazione, sezione, anno";
$ris=mysqli_query($con,inspref($query));
   while($nom=mysqli_fetch_array($ris))
   {
            print "<option value='";
            print ($nom["idclasse"]);
            print "'";
			print ">";
            print ($nom["anno"]);
            print "&nbsp;"; 
            print($nom["sezione"]); 
            print "&nbsp;";
            print($nom["specializzazione"]);
   }
        
   echo('
      </SELECT>
      </td></tr>

      </table>');
      
    $query='select * from tbl_alunni where idclasse=0 order by cognome,nome,datanascita';
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    if (mysqli_num_rows($ris)==0)
    {
	   echo "<p align='center'>
		    <font size=4 color='black'>Nessun alunno senza classe presente</font></p></form>";
	   
    }
    else
    {
    echo '<p align="center">
          <font size=4 color="black">Alunni senza classe </font></p>
          
          <table border=2 align="center">';
    echo'
          <tr class=prima>
          
          <td><b> Cognome </b></td>
          <td><b> Nome  </b></td>
          <td><b> Data di nascita </b></td>
          
          <td><b> Attr. Classe  </b></td>
          
          </tr>
        ';
 
    
    while($val=mysqli_fetch_array($ris))
    {
      echo ' 
             <tr>
                <td><b> '.$val["cognome"].' </b></td>
                <td><b> '.$val["nome"].'    </b></td>
                <td><b> '.data_italiana($val["datanascita"]).' </b></td>
                <td><center>   <input type=checkbox name="attr'.$val["idalunno"].'"';


      

      print "></center></td>";
      

      print"</tr>";
  
    }
    echo'</table>';
 
    echo'
  
          <table align="center">
          <tr> </tr>
          </table>
          <p align="center"><input type=submit name=b value="Attribuisci classe">
          
          </form>
         ';

   
 }
 // fine if
  
mysqli_close($con);
stampa_piede(""); 

