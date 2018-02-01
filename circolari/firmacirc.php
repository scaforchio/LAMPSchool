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
	
 // istruzioni per tornare alla pagina di login se non c'è una sessione valida
 ////session_start();
 $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
 
 if ($tipoutente=="")
 {
   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']);
   die;
 }
 $idutente=stringa_html("idutente");
 $idcircolare=stringa_html("idcircolare");
// CONNESSIONE AL DATABASE
$titolo="Firma circolare";
$script="";
stampa_head($titolo,"",$script,"MSPDAT");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");
 

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

$dataoggi=data_to_db(date('d/m/Y'));
$querylett="update tbl_diffusionecircolari
            set dataconfermalettura='$dataoggi'
            where idcircolare=$idcircolare
            and idutente=$idutente";
        //    print "tttt".inspref($querylett);
mysqli_query($con,inspref($querylett)) or die ("Errore:".inspref($querylett));

$querylett="update tbl_diffusionecircolari
            set datalettura='$dataoggi'
            where idcircolare=$idcircolare
            and idutente=$idutente
            and datalettura='0000-00-00'";
        //    print "tttt".inspref($querylett);
mysqli_query($con,inspref($querylett)) or die ("Errore:".inspref($querylett));

print "
                 <form method='post' id='formcancdoc' action='../circolari/viscircolari.php'>
                                  
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formcancdoc').submit();
                 }
                 </SCRIPT>";


// CHIUDIAMO LA CONNESSIONE
mysqli_close($con);
stampa_piede(""); 
 //endif


