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

$titolo = "Cancellazione circolare";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='../documenti/pianilavoro.php'>Gestione piani di lavoro</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);



$idcircolare = stringa_html('idcircolare');
$destinatari = stringa_html('destinatari');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
// LEGGO l'id del documento 
$query = "select iddocumento from tbl_circolari where idcircolare=$idcircolare";
$ris = eseguiQuery($con, $query);
$rec = mysqli_fetch_array($ris);
$iddocumento = $rec['iddocumento'];

// CANCELLO IL DOCUMENTO
$querycanc = "delete from tbl_documenti where iddocumento=$iddocumento";
$riscanc = eseguiQuery($con, $querycanc);

// CANCELLO LA LISTA DI DISTRIBUZIONE
$querycanc = "delete from tbl_diffusionecircolari where idcircolare=$idcircolare";
$riscanc = eseguiQuery($con, $querycanc);

// CANCELLO LA CIRCOLARE
$querycanc = "delete from tbl_circolari where idcircolare=$idcircolare";
$riscanc = eseguiQuery($con, $querycanc);


// print "<center><b><br>Cancellazione effettuata!<br></b></center> ";   

print "
                 <form method='post' id='formcancdoc' action='../circolari/circolari.php'>
                 
                 <input type='hidden' name='destinatari' value='$destinatari'>
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formcancdoc').submit();
                 }
                 </SCRIPT>";
/*
  print ("
  <form method='post' action='documenti.php?idclasse=$idclasse&idalunno=$idalunno'>
  <p align='center'>");


  print("   <input type='submit' value='OK' name='b'></p>

  </form>
  "); */
mysqli_close($con);
stampa_piede("");

