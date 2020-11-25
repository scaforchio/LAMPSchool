<?php

session_start();

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


if (isset($_GET['suffisso']))
    $suffisso = $_GET['suffisso'];
else
    $suffisso = "";


@require_once("../php-ini" . $suffisso . ".php");
@require_once("../lib/funzioni.php");


$utente = stringa_html('utente');


$_SESSION["prefisso"] = $prefisso_tabelle;
$_SESSION["annoscol"] = $annoscol;
$_SESSION["suffisso"] = $suffisso;
$_SESSION["versioneprecedente"]=$versioneprecedente;
$_SESSION["indirizzomailfrom"]=$indirizzomailfrom;
$_SESSION["nomefilelog"] = $nomefilelog;
$_SESSION["alias"] = false;

$json = leggeFileJSON('../lampschool.json');
$_SESSION['versione'] = $json['versione'];


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore: " . mysqli_error($con));


$token = mt_rand(100000, 999999);
$date = new DateTime();
$ts = $date->getTimestamp();



$query = "update tbl_utenti
            set tokenresetpwd='$token',
                oracreazionetoken=$ts,
                numutilizzitoken=0    
            where userid='$utente'";
$ris = eseguiQuery($con, $query);
if (mysqli_affected_rows($con)==0)
    header("location: richresetpwd.php?suffisso=$suffisso&messaggio=err1");
if (substr($utente, 0,2)=='al')
    header("location: richresetpwd.php?suffisso=$suffisso&messaggio=err5");   
$query="select * from tbl_utenti where userid='$utente'";
$ris= eseguiQuery($con, $query);
$rec= mysqli_fetch_array($ris);
$idutente=$rec['idutente'];

invia_OTP($token,$idutente,$con,$suffisso);
$titolo = "Reset PWD";
$script = "<script>
             function verifica()
             {
                var p1=document.getElementById('np1').value;
                var p2=document.getElementById('np2').value;
                var tk=document.getElementById('token').value;
               
                   if (p1.length>6 & p1==p2 & tk>99999 & tk<1000000)
                   {
                       document.getElementById('subnp').disabled=false;
                   }
                   else
                   {
                       document.getElementById('subnp').disabled=true;
                   }
                
             }
   </script>"
        ;
stampa_head($titolo, "", $script, "",false);
stampa_testata("Reset password", "", "$nome_scuola", "$comune_scuola");


print ("<form method='post' action='resetpwdok.php?suffisso=$suffisso' id='formdisp'>");
   
print "<center><br>Nuova password (min. 7 caratteri)<input type='text' name='newpass1' id='np1' Onkeyup='verifica();'>
                         <br>Ripeti nuova password (min. 7 caratteri) <input type='text' name='newpass2' id='np2'  Onkeyup='verifica();'>
                         <br>OTP (6 cifre, inviata tramite mail) <input type='text' size='6' maxlength='6' name='token' id='token' Onkeyup='verifica();'>
                         <input type='hidden' value='$utente' name='utente'>";
print "<br><br><input type='submit' id='subnp' disabled>";
print ("</form>"); 
    
mysqli_close($con);
stampa_piede("");

function invia_OTP($token, $idutente,$con,$suffisso)
{
  //  print "idute".$idutente;die();
    if ($idutente<1000000000)
    {
        $idalunno=$idutente;
        $query="select * from tbl_alunni where idalunno=$idalunno";
       // print inspref($query);die();
        $ris= eseguiQuery($con, $query);
        $rec=mysqli_fetch_array($ris);
        $email=$rec['email'];
        $email2=$rec['email2'];
        
        $oggetto="OTP per cambio password LAMPSchool ".$token;
        $testo="Si comunica il codice numerico per il cambio della password: $token";
        if ($email!='')
            invia_mail ($email, $oggetto, $testo);
        if ($email2!='')
            invia_mail ($email2, $oggetto, $testo);
        if ($email=='' & $email2=='')
            header("location: richresetpwd.php?suffisso=$suffisso&messaggio=err6");   
            
    }   
    if ($idutente>=1000000000)
    {
        $iddocente=$idutente;
        $query="select * from tbl_docenti where iddocente=$iddocente";
     //   print $query; die();
        $ris= eseguiQuery($con, $query);
        $rec=mysqli_fetch_array($ris);
        $email=$rec['email'];
        
        
        $oggetto="OTP per cambio password LAMPSchool ".$token;
        $testo="Si comunica il codice numerico per il cambio della password: $token";
        
        if ($email!='')
            invia_mail ($email, $oggetto, $testo);
        else
            header("location: richresetpwd.php?suffisso=$suffisso&messaggio=err6");   
    }   
    
   /* if (substr())
    $query= */
}