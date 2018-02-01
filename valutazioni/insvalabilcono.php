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

$titolo="Inserimento voto per obiettivi";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 


 $gio = stringa_html('gio');
 $mese = stringa_html('mese');
 $anno = stringa_html('anno');
 $tipo = stringa_html('tipo');
 $materia = stringa_html('materia');
 $iddocente = stringa_html('iddocente');
 $data=$anno."/".$mese."/".$gio;
 $idclasse = stringa_html('cl');
 $arrabilcono = is_stringa_html('abil') ? stringa_html('abil') : array();
 // $arrabilcono = stringa_html('abil'];
 $cattedra = stringa_html('cattedra');
 $idlezione = stringa_html('idlezione');
 $orainizioold = stringa_html('orainizioold');

 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
  
 

 
 $query="select idalunno,cognome,nome from tbl_alunni where idclasse=".$idclasse."";
 $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));

 
while($id=mysqli_fetch_array($ris))            //    <-----------  ttttttt
{
    if (!alunno_certificato_pei($id['idalunno'],$materia,$con))		
    { 
		 $esistenti=false;
		 $presentevoto=false;
		 $idal=$id['idalunno'];
       $query="select idvalint,voto from tbl_valutazioniintermedie where idalunno=".$idal." and idlezione='".$idlezione."' and tipo='$tipo'";
       $ris2=mysqli_query($con,inspref($query)) or die (mysqli_error);
       if (mysqli_num_rows($ris2)>0)
       {
			 
			 // Si preleva l'idvalutazione e si cancellano le valutazioni singole
          $val=mysqli_fetch_array($ris2);
          if ($val['voto']!=99)
			    $presentevoto=true;
          $idvalint=$val['idvalint'];
          $querycanc="delete from tbl_valutazioniabilcono where idvalint=$idvalint";
          $ris3=mysqli_query($con,inspref($querycanc)) or die (mysqli_error);
          $numcancellate=mysqli_affected_rows($con);
          if ($numcancellate>0)
             $esistenti=true;
             
		 }
		 else
		 {
          // Si inserisce il nuovo voto nella tabella tbl_valutazioni intermedie con valore di voto=0
          // tale valore verrà valorizzato dopo aver inserito i voti delle singole abilità e conoscenze
          // con il voto medio risultante
          $query="insert into tbl_valutazioniintermedie(idalunno,idmateria,idlezione,iddocente,idclasse,data,tipo,voto,giudizio)
          values('$idal','$materia','$idlezione','$iddocente','$idclasse','$data','$tipo','0','')";
          $ris2=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
          $idvalint=mysqli_insert_id($con);
		 }
       
       // Si procede con l'inserimento di tutti i voti inseriti
       
       $idmateria=estrai_id_materia($cattedra, $con);
       $idclasse=estrai_id_classe($cattedra, $con);
       $query="select idcompetenza from tbl_competdoc where idmateria='$idmateria' and idclasse='$idclasse'";
 
       $ris3=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));        
       $numvoti=0;
       $totvoti=0;
       while($nom=mysqli_fetch_array($ris3))
       {
	      	$idcompetenza=$nom['idcompetenza'];
		      $query="select idabilita from tbl_abildoc where idcompetenza=".$idcompetenza."";
		      $risab=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));    
		      while($nomab=mysqli_fetch_array($risab))
            { 
			      $idabilita=$nomab['idabilita'];
			      $va="voto".$idal."_".$idabilita;
               $votoab = is_stringa_html($va) ? stringa_html($va) : 99;
               if ($votoab!=99)
               {
				      $numvoti++;
				      $totvoti=$totvoti+$votoab;
				      $query="insert into tbl_valutazioniabilcono(voto,idvalint,idabilita)
                           values('$votoab','$idvalint','$idabilita')";    
                       
                  $risins=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
			     }
		     }	
       }
	
		if ($numvoti!=0)
		{
			 if ($presentevoto & !$esistenti)
			 {
				 print "<br><center><font size=4 color='red'>Valutazione non legata alle competenze già presente per ".$id['cognome']." ".$id['nome']."</font></center>";
				 mysqli_query($con,inspref("delete from tbl_valutazioniabilcono where idvalint=$idvalint")); 
			  }
			  else
			  {  
				  $votomedio=round($totvoti*4/$numvoti)/4;		
				  $query="update tbl_valutazioniintermedie set voto=$votomedio where idvalint=$idvalint";
				  $risupd=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
				  echo "
					  <center>
					  <font size=4><br>
					  Il voto medio risultante per l'alunno ".$id['cognome']." ".$id['nome']." è: <b>".dec_to_mod($votomedio)."</b></font>
					  </center>";
			  }  
		}
		else
		{
			if (!($presentevoto & !$esistenti))
			{
				mysqli_query($con,inspref("update tbl_valutazioniintermedie set voto=99 where giudizio<>'' and idvalint=$idvalint"));
				mysqli_query($con,inspref("delete from tbl_valutazioniintermedie where idvalint=$idvalint and voto<>99"));
				if ($esistenti)
					echo "
					  <p align='center'>
					  <font size=4><br>Valutazioni cancellate per l'alunno ".$id['cognome']." ".$id['nome']."!<br>
					  </font>
					  ";
			 } 
		}
	}
}
        
  //  codice per richiamare il form delle tbl_assenze;
 
  print ("
   <form method='post' action='valabilcono.php'>
   <p align='center'>

 
    <p align='center'><input type=hidden value='$gio' name=gio>
    <p align='center'><input type=hidden value='$mese - $anno' name=mese>
  
    <p align='center'><input type=hidden value='$idal' name=alunno>
    <p align='center'><input type=hidden value='$cattedra' name=cattedra>
    <p align='center'><input type=hidden value='$orainizioold' name=orainizioold>
    <p align='center'><input type=hidden value='$tipo' name=tipo>");
    
   print("<input type='submit' value='OK' name='b'></p>
     </form>");
 
  
     mysqli_close($con);
     stampa_piede(""); 
  

