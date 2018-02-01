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
	
 // istruzioni per tornare alla pagina di login se non c'è una sessione valida
 ////session_start();
 $tipoutente=$_SESSION["tipoutente"];
 
  //prende la variabile presente nella sessione
    if ($tipoutente=="")
       {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
	   die;
       } 

// stampa_head("Situazione assenze per classe","","");

// print ("<body  onLoad='JavaScript:printPage()' >");

$script= "<script>
            function printPage()
            {
               if (window.print)
                  window.print();
               else 
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');
            }
         </script>"; 
  
  stampa_head($titolo,"",$script,"MSPD");
 //   print $script;         

    print ("<font size='3'><center>$nome_scuola</center><br/>");
    print ("<font size='3'><center>$comune_scuola</center><br/>");

print ('<body class="stampa" onLoad="printPage()">');

    $nome = stringa_html('classe');
    $datainizio=stringa_html('datainizio');
    $datafine=stringa_html('datafine');


    $idclasse=$nome;
    $classe="";
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
     
   
    $query='select * from tbl_classi where idclasse="'.$idclasse.'" ';
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    if($val=mysqli_fetch_array($ris))
       $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];

    $query='select * from tbl_alunni where idclasse="'.$idclasse.'" order by cognome,nome,datanascita';
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));

    $c=mysqli_fetch_array($ris);
    if ($c==NULL) {echo '
                    <p align="center">
		    <font size=4 color="black">Nessun alunno presente nella classe '.$nome.'</font>
                   '; exit;
                  }
    echo '<p align="center">
          <font size=4 color="black">Assenze della classe '.$classe. '</font>
          
          <table border=2 align="center">';
            
    echo'
          <tr class=prima>
          
          <td><font size=1><b> Cognome </b></td>
          <td><font size=1><b> Nome  </b></td>
          <td><font size=1><b> Data di nascita </b></td>';
    print ("<td><font size=1><b><center>Ass</td><td><b><font size=1><center>Rit</td><td><b><font size=1><center>Usc</td></tr>");
                
     
 
    $query='select * from tbl_alunni where idclasse="'.$idclasse.'" order by cognome,nome,datanascita';
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    while($val=mysqli_fetch_array($ris))
    {
      echo ' 
             <tr>
                <td><font size=1><b> '.$val["cognome"].' </b></td>
                <td><font size=1><b> '.$val["nome"].'    </b></td>
                <td><font size=1><b> '.data_italiana($val["datanascita"]).' </b></td>
                ';
      
      $seledata="";
      if ($datainizio!="")
         $seledata=$seledata." and data >= '".data_to_db($datainizio)."' ";
      
      if ($datafine!="")
         $seledata=$seledata." and data <= '".data_to_db($datafine)."' ";
          
      
      $queryass='select count(*) as numass from tbl_assenze where idalunno = '.$val["idalunno"].$seledata;
      $queryrit='select count(*) as numrit from tbl_ritardi where idalunno = '.$val["idalunno"].$seledata;
      $queryusc='select count(*) as numusc from tbl_usciteanticipate where idalunno = '.$val["idalunno"].$seledata;
 
      $risass=mysqli_query($con,inspref($queryass)) or die ("Errore nella query: ". mysqli_error($con));
      $risrit=mysqli_query($con,inspref($queryrit)) or die ("Errore nella query: ". mysqli_error($con));      
      $risusc=mysqli_query($con,inspref($queryusc)) or die ("Errore nella query: ". mysqli_error($con));   
      while ($ass=mysqli_fetch_array($risass))
      {
       
         $numass=$ass['numass'];
      }  
      while ($rit=mysqli_fetch_array($risrit))
      {
         $numrit=$rit['numrit'];
      }
      while ($usc=mysqli_fetch_array($risusc))
      {
        
         $numusc=$usc['numusc'];
      }
      
      print "<td><center>$numass</td><td><center>$numrit</td><td><center>$numusc</td></tr>";      


         
      
    }

    echo'</table>';
 
  
   
  
 // fine if
// stampa_piede("");  
mysqli_close($con);


