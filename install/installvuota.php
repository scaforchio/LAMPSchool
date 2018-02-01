<?php session_start();

/*
Copyright (C) 2015 Angelo Scarnà
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
  		
	@require_once("../lib/funzioni.php");
	

    ////session_start();
    $titolo="Installazione del registro elettronico";
    
   $script=""; 
    
    stampa_head($titolo,"",$script,"",false);
    stampa_testata_installer("Installazione del registro elettronico","","","");
 	
 	$par_db_server=isset($_POST['par_db_server'])?$_POST['par_db_server']:"";
   $par_db_nome=isset($_POST['par_db_nome'])?$_POST['par_db_nome']:"";
   $par_prefisso_tabelle=isset($_POST['par_prefisso_tabelle'])?$_POST['par_prefisso_tabelle']:"";
   $par_db_user=isset($_POST['par_db_user'])?$_POST['par_db_user']:"";
   $par_db_password=isset($_POST['par_db_password'])?$_POST['par_db_password']:"";
 	
   
?>
<center>
  <form method="post" action="installvuota.php">
  <table border=0 bgcolor='white' align="top">
  <tr>
  <td COLSPAN="2">
  <center>
  <label><font size="5">Variabili database.<br/> Verifica la connettività al database.</font></label><br/><br/>
  </center>
   </td>
   </tr>
  <tr>
  <td>
   <center>
	<label for="par_db_server"><font size="2">Indirizzo server</font></label>
   </center>
   </td>
   <td>
   <center>
	<input type="text" name="par_db_server" size="30" value="<?php print $par_db_server; ?>" >
   </center>
   </td>
   </tr>
   <tr>
   <td>
   <center>
   <label for="par_db_nome"><font size="2">Nome database</font></label>
   </center>
   </td>
   <td>
   <center>
   <input type="text" name="par_db_nome" size="30" value="<?php print $par_db_nome; ?>">
   </center>
   </td>
   </tr>
   <tr>
   <td>
   <center>
   <label for="prefisso_tabelle"><font size="2">Prefisso tabelle</font></label>
   </center>
   </td>
   <td>
   <center>
   <input type="text" name="par_prefisso_tabelle" size="30" value="<?php print $par_prefisso_tabelle; ?>">
   </center>
   </td>
   </tr>
      <tr>
          <td>
              <center>
                  <label for="prefisso_tabelle"><font size="2">Suffisso installazione</font></label>
              </center>
          </td>
          <td>
              <center>
                  <input type="text" name="par_suffisso_installazione" size="30" value="<?php print $par_suffisso_installazione; ?>">
              </center>
          </td>
      </tr>
   <tr>
   <td>
   <center>
   <label for="par_db_user"><font size="2">Nome utente</font></label>
   </center>
   </td>
   <td>
   <center>
   <input type="text" name="par_db_user" size="30" value="<?php print $par_db_user; ?>">
   </center>
   </td>
   </tr>
   <tr>
   <td>
   <center>
   <label for="par_db_password"><font size="2">Password</font></label>
   </center>
   </td>
   <td>
   <center>
   <input type="password" name="par_db_password" size="30" value="<?php print $par_db_password; ?>">
   </center>
   </td>
   </tr>
   <tr>
   <td COLSPAN="2">
   <center>
      <br/>
   <input type="submit" name="submit" value="Verifica!">
   </center>
   </td>
   </tr>
  </table>
  </form>
</center>
<?php 
 if (isset($_POST['submit']) && $_POST['submit']=="Verifica!")
 {
 //   $par_db_server=isset($_POST['par_db_server'])?$_POST['par_db_server']:"";
 //  $par_db_nome=isset($_POST['par_db_nome'])?$_POST['par_db_nome']:"";
 //  $prefisso_tabelle=isset($_POST['prefisso_tabelle'])?$_POST['prefisso_tabelle']:"";
 //  $par_db_user=isset($_POST['par_db_user'])?$_POST['par_db_user']:"";
 //  $par_db_password=isset($_POST['par_db_password'])?$_POST['par_db_password']:"";


   if ( empty($par_db_server) || empty($par_db_user) || empty($par_db_nome) )
   {
      echo "<center><font color=red>Inserisci i dati e clicca su Verifica</font></center>";
   } 
   else 
   {
		
	
		$err=check_db($par_db_server,$par_db_user,$par_db_password,$par_db_nome,$par_prefisso_tabelle);

     

     if($err==1 | $err==2)
     { 
        print("\n<h1> Connessione al server fallita o database inesistente!</h1>");
        exit;
	  }
	  else
        if($err==3)
        {
           print("\n<center><p>Le tabelle specificate non esistono. Clicca su [Crea nuova installazione]...\n");
           print("<center>
                 <form action='step_2.php' method='post'>
                 <input type='hidden' name='par_db_nome' value='$par_db_nome'>
                 <input type='hidden' name='par_db_server' value='$par_db_server'>
                 <input type='hidden' name='par_prefisso_tabelle' value='$par_prefisso_tabelle'>
                 <input type='hidden' name='par_suffisso_installazione' value='$par_suffisso_installazione'>
                 <input type='hidden' name='par_db_user' value='$par_db_user'>
                 <input type='hidden' name='par_db_password' value='$par_db_password'>
                 <input type='hidden' name='tipo_installazione' value='nuova'>
                 <input type='submit' value='Crea nuova installazione'>
                 </form>
  	             
   	         </center>");
   	         
        }
        else
        {
           print("\n<center><p>Esiste già una installazione con i dati forniti. Clicca su [aggiorna installazione]...\n");
           print("<center>
                 <form action='step_2.php' method='post'>
                 <input type='hidden' name='par_db_nome' value='$par_db_nome'>
                 <input type='hidden' name='par_db_server' value='$par_db_server'>
                 <input type='hidden' name='par_prefisso_tabelle' value='$par_prefisso_tabelle'>
                 <input type='hidden' name='par_suffisso_installazione' value='$par_suffisso_installazione'>
                 <input type='hidden' name='par_db_user' value='$par_db_user'>
                 <input type='hidden' name='par_db_password' value='$par_db_password'>
                 <input type='hidden' name='tipo_installazione' value='agg'>
                 <input type='submit' value='Aggiorna installazione'>
                 </form>
  	             
   	         </center>");
   	         
        }
     
      
   }
}
  
stampa_piede('1.3', FALSE);
?>
