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


$titolo="Situazione ore di assenza al ".data_italiana($dataassora);

stampa_head($titolo,"","","MSPD");



$nome = stringa_html('cl');
$but = stringa_html('visass');

$oreass=1;



$menu='<a href="../login/ele_ges.php">PAGINA PRINCIPALE</a> - SITUAZIONE ORE DI ASSENZA AL '.data_italiana($dataassora);
stampa_testata("$menu","","$nome_scuola","$comune_scuola");

print ('
   <form method="post" action="sitoreasscla.php" name="tbl_assenze">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="cl" ONCHANGE="tbl_assenze.submit()"> <option>&nbsp '); 
	  
  
       
          $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
           
          $query="select idclasse,anno,sezione,specializzazione from tbl_classi order by specializzazione, sezione, anno";
          $ris=mysqli_query($con,inspref($query));
          while($nom=mysqli_fetch_array($ris))
	  {
            print "<option value='";
            print ($nom["idclasse"]);
            print "'";
			if ($nome==$nom["idclasse"])
			   print " selected";
			print ">";
            print ($nom["anno"]);
            print "&nbsp;"; 
            print($nom["sezione"]); 
            print "&nbsp;";
            print($nom["specializzazione"]);
          }
        
   echo('
      </SELECT>
      </td></tr>');

      
echo('
    </table>
 
    <table align="center">
      <td>');
    //    <p align="center"><input type="submit" value="Visualizza tbl_assenze" name="b"></p>
echo('     </form></td>
   
</table><hr>
 
    ');
   
  

  // print($nome." -   ". $g.$m.$a.$giornosettimana);
  
    $idclasse=$nome;
    $classe="";
    $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
     
   
    $query='select * from tbl_classi where idclasse="'.$idclasse.'" ';
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    if($val=mysqli_fetch_array($ris))
       {
           $classe=$val["anno"]." ".$val["sezione"]." ".$val["specializzazione"];
           $anno=$val["anno"];
       }  

    $query='select * from tbl_alunni where idclasse="'.$idclasse.'" order by cognome,nome,datanascita';
    $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));

    $c=mysqli_fetch_array($ris);
    if ($c==NULL) {echo '
                    <p align="center">
		    <font size=4 color="black">Nessun alunno presente nella classe '.$nome.'</font>
                   '; exit;
                  }
    echo '<p align="center">
          <font size=4 color="black">Ore assenza della classe '.$classe.'</font>
          
          <table border=2 align="center">';
            
    echo'
          <tr class=prima>
          
          <td><font size=1><b> Cognome </b></td>
          <td><font size=1><b> Nome  </b></td>
          <td><font size=1><b> Data di nascita </b></td>
          <td><font size=1><b> Ore ass. inserite </b></td>
          <td><font size=1><b> Ore ass. stimate<sup>*</sup></b></td>
          <td><font size=1><b> Anomalia<sup>**</sup> </b></td> 
          <td><font size=1><b> Rischio<sup>***</sup></b></td></tr> ';
            
 
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
      

      // Codice per ricerca ore tbl_assenze 
      $queryoreass="SELECT sum(numeroore) as totore FROM `oreassenza` WHERE idalunno = ".$val["idalunno"]." AND DATA = '".$dataassora."'";
      $queryass="select count(*) as numass from tbl_assenze where idalunno = '".$val["idalunno"]."' and data <= '".$dataassora."'";
      $queryrit="select count(*) as numrit from tbl_ritardi where idalunno = '".$val["idalunno"]."' and data <=  '".$dataassora."'";
      $queryusc="select count(*) as numusc from tbl_usciteanticipate where idalunno = '".$val["idalunno"]."' and data <=  '".$dataassora."'";
      
      $risoreass=mysqli_query($con,inspref($queryoreass)) or die ("Errore nella query: ". mysqli_error($con));
      $risass=mysqli_query($con,inspref($queryass)) or die ("Errore nella query: ". mysqli_error($con));
      $risrit=mysqli_query($con,inspref($queryrit)) or die ("Errore nella query: ". mysqli_error($con));      
      $risusc=mysqli_query($con,inspref($queryusc)) or die ("Errore nella query: ". mysqli_error($con));   
      
      $oreass=mysqli_fetch_array($risoreass);
      $oass=$oreass['totore'];

      $ass=mysqli_fetch_array($risass);
      $nass=$ass['numass'];

      $rit=mysqli_fetch_array($risrit);
      $nrit=$rit['numrit'];

      $usc=mysqli_fetch_array($risusc);
      $nusc=$usc['numusc'];
      if($anno==5)
         $oremass=round(1188*($percentuale/100)*($percrischio/100));
      else
         $oremass=round(1056*($percentuale/100)*($percrischio/100));
      
      if ($anno==5)
         $orestimate=round($nass*6+$nrit*1+$nusc*2); 
      else
         $orestimate=round($nass*5.33+$nrit*1+$nusc*2);

      print"<td>$oass</td><td>$orestimate</td><td>";
      
      if ($orestimate>$oass & $oass!=0 ) 
         $scarto=$orestimate/$oass;
      else
         if ($orestimate!=0)
            $scarto=$oass/$orestimate;
         else
            $scarto=1; 
      
      if ($scarto>1.40)
         print("<center><img src='../immagini/inte.gif' width=20 height=20></center></td><td>");
      else
         print("&nbsp;</td><td>");
      
      if ($oass>$oremass)
         print "<center><img src='../immagini/alert.png' width=20 height=20></center>";


      print"</td></tr>";


	}  
      
    echo'</table>';
    if ($anno==5)
       print "<center><sup>*</sup><font size=1> (Assenze x 6) + (tbl_ritardi x 1) + (Uscite x 2)</font></center>";
    else
       print "<center><sup>*</sup><font size=1> (Assenze x 5.33) + (tbl_ritardi x 1) + (Uscite x 2)</font></center>";

    print "<br/><center><sup>**</sup><font size=1> Differenza tra stima e ore inserite maggiore del 40%</font></center>";
    
    print "<br/><center><sup>***</sup><font size=1> Ore assenza inserite maggiore del $percrischio% del $percentuale% delle ore complessive ($oremass ore)</font></center>";
    
    
 // fine if
  
mysqli_close($con);
stampa_piede(""); 

