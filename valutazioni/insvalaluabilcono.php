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

$titolo="Inserimento voti per obiettivi";
$script=""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

 $gio = stringa_html('gio');
 $mese = stringa_html('mese');
 $anno = stringa_html('anno');
 $materia = stringa_html('materia');
 $tipo = stringa_html('tipo');
 $iddocente = stringa_html('iddocente');
 $idlezione = stringa_html('idlezione');
 $orainizioold = stringa_html('orainizioold');
 $data=$anno."/".$mese."/".$gio;  
 $idclasse = stringa_html('cl');
 //$arrabilcono = stringa_html('abil');
 $cattedra = stringa_html('cattedra');
 $idal = stringa_html('alunno');
 $modo = stringa_html('modo');
 
 $con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
  
 
 $pei=0;
 
 $esistenti=false;
 $query="select idvalint from tbl_valutazioniintermedie where idalunno=".$idal." and idlezione='".$idlezione."' and tipo='$tipo'";
 $ris2=mysqli_query($con,inspref($query)) or die (mysqli_error);
 if (mysqli_num_rows($ris2)>0)
 {
    // Si preleva l'idvalutazione e si cancellano le valutazioni singole
    $val=mysqli_fetch_array($ris2);
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
   //  print inspref($query);        
     $ris2=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
     $idvalint=mysqli_insert_id($con);
 }
       
 
 $idmateria=estrai_id_materia($cattedra, $con);
 $idclasse=estrai_id_classe($cattedra, $con);
 
 $pei=alunno_certificato_pei($idal,$idmateria,$con);
 if (!$pei )
    $query="select idcompetenza from tbl_competdoc where idmateria = $idmateria and idclasse = $idclasse";
 else
    $query="select idcompetenza from tbl_competalu where idmateria = $idmateria and idalunno = $idal";
 $ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));        
 $numvoti=0;
 $totvoti=0;
 while($nom=mysqli_fetch_array($ris))
    {
		$idcompetenza=$nom['idcompetenza'];
		if (!$pei)
		   {
				$obmin=" ";
				if (alunno_certificato_ob_min($idal, $idmateria,$con)) $obmin=" and obminimi ";
		      $query="select idabilita from tbl_abildoc where idcompetenza=".$idcompetenza." $obmin";
			}
		else
		   $query="select idabilita from tbl_abilalu where idcompetenza=".$idcompetenza."";
		// print "tttt ".inspref($query);
		$risab=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));    
		while($nomab=mysqli_fetch_array($risab))
        { 
			   $idabilita=$nomab['idabilita'];
			   $va="voto".$idabilita;
			   $votoab = stringa_html($va);
            
           // print "tttt $votoab $va <br>";
            if ($votoab!=99)
            {
				   $numvoti++;
				   $totvoti=$totvoti+$votoab;
				  // print "tttt $numvoti  $totvoti <br>";
				   if (!$pei)
				       $query="insert into tbl_valutazioniabilcono(voto,idvalint,idabilita)
                           values('$votoab','$idvalint','$idabilita')";    
               else
                   $query="insert into tbl_valutazioniabilcono(voto,idvalint,idabilita,pei)
                           values('$votoab','$idvalint','$idabilita',1)";        
               $risins=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
			  }
		  }	
     }
	
	if ($numvoti>0)
	{
	   $votomedio=round($totvoti*4/$numvoti)/4;
	   if (!$pei)
	      $query="update tbl_valutazioniintermedie set voto=$votomedio where idvalint=$idvalint";
      else
         $query="update tbl_valutazioniintermedie set voto=$votomedio, pei=1 where idvalint=$idvalint";
      $risupd=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
      echo '
           <p align="center">
           <font size=4 color="black">I dati sono stati inseriti correttamente<br><br>
           Il voto medio risultante è: <b>'.dec_to_mod($votomedio).'</b></font>
         '; 
	}		
	else
	{
		mysqli_query($con,inspref("update tbl_valutazioniintermedie set voto=99 where giudizio<>'' and idvalint=$idvalint"));
	   mysqli_query($con,inspref("delete from tbl_valutazioniintermedie where idvalint=$idvalint and voto<>99"));
	   if ($esistenti)
	      echo '
              <p align="center">
              <font size=4 color="black">Le valutazioni sono state cancellate!<br><br>
              </font>
           '; 
	}
     
 
 
        
  //  codice per richiamare il form delle tbl_assenze;
 
  print ("
   <form method='post' action='valaluabilcono.php'>
   <p align='center'>

    <input type=hidden value='$modo' name=modo>
    <p align='center'><input type=hidden value='$gio' name=gio>
    <p align='center'><input type=hidden value='$mese - $anno' name=mese>
  
    <p align='center'><input type=hidden value='$idal' name=alunno>
    <p align='center'><input type=hidden value='$cattedra' name=cattedra>
    <p align='center'><input type=hidden value='$idlezione' name=idlezione>
    <p align='center'><input type=hidden value='$orainizioold' name=orainizioold>
    <p align='center'><input type=hidden value='$tipo' name=tipo>");
    
   print("<input type='submit' value='OK' name='b'></p>
     </form>");
  mysqli_close($con);
  stampa_piede(""); 

