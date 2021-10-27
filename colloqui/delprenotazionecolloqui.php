<?php

require_once '../lib/req_apertura_sessione.php';

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

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Cancellazione prenotazione colloqui";
$script = "";
stampa_head($titolo, "", $script, "T");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='orario.php'>Orario</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$iddocente = stringa_html('iddoc');
$idalunno = stringa_html('idal');
$slot = stringa_html('slot');
$idgiornatacolloqui=stringa_html('idgiornatacolloqui');


$query = "delete from tbl_slotcolloqui where
          iddocente=$iddocente and idalunno=$idalunno and orainizio='$slot' and $idgiornatacolloqui=idgiornatacolloqui";
$ris = eseguiQuery($con, $query);

print ("

       

        <form method='post' action='../colloqui/prenotazionecolloqui.php' id='formdisp'>
   
         <input type='hidden' name='idgiornatacolloqui' value='$idgiornatacolloqui'>
        </form> 
      
        <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formdisp').submit();
                 }
         </SCRIPT>  
      
      ");
stampa_piede("");

