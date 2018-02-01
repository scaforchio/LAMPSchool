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


 //print $data;
 $idclasse=stringa_html('cl');
 
$titolo="Attribuzione classe ad alunni";
$script=""; 
stampa_head($titolo,"",$script,"MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Attribuzione classi ad alunni","","$nome_scuola","$comune_scuola");

 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
  
 

 
 $query="select idalunno as al from tbl_alunni where idclasse='0'";
 $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));

 
    while($id=mysqli_fetch_array($ris))
    {
       $idal= is_stringa_html('attr'.$id['al'])?"on":"off";
       
       if ($idal=="on") 
       {
          $query="update tbl_alunni set idclasse=$idclasse where idalunno=".$id['al'];
          
          $ris2=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con)); 
       }
    }
    echo '
           <p align="center">
           <font size=4 color="black">I dati sono stati inseriti correttamente</font>
         ';  
 
 
        
  
  print ('
   <form method="post" action="attr_classe.php">
   <p align="center">');

   print(' 
          <input type="submit" value="OK" name="b"></p>
     </form>
  ');

   stampa_piede("");
   mysqli_close($con);

