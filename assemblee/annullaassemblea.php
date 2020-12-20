<?php

session_start();

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

//
//    VISUALIZZAZIONE DELLE ASSEMBLEE DI CLASSE PER I GENITORI
//	  E
//	  RICHIESTA DI ASSEMBLEE DI CLASSE PER GLI ALUNNI 
//


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

//  istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$iddocente = $_SESSION['idutente'];

$titolo = "Annullamento assemblea";
$script = "";
stampa_head($titolo, "", $script, "SP");
$idassemblea = stringa_html('idassemblea');
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='assricgen.php?idclasse=$idclasse'>Assemblee di classe</a> - <a href='ricgen.php?idclasse=$idclasse'>Richiesta assemblea di classe</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

//print $dataassemblea;
//$gg = substr($dataass,0,2);
//$mm = substr($dataass,3,2);
//$aaaa = substr($dataass,6,4);
//$dataassemblea = $aaaa."-".$mm."-".$gg;

$assq = "select * from tbl_assemblee where idassemblea=$idassemblea";
$ris = eseguiQuery($con, $assq);
$rec = mysqli_fetch_array($ris);

$idclasse = $rec['idclasse'];
$dataass = $rec['dataassemblea'];

// INSERIMENTO ASSEMBLEA NON AUTORIZZATA CON MOTIVAZIONE "Richiesta spostameto"

$assq = "update tbl_assemblee set autorizzato=2, docenteautorizzante=$iddocente,note='Richiesta annullata!' where idassemblea=$idassemblea";
eseguiQuery($con, $assq);
if (mysqli_affected_rows($con) == 1)
    print "<br><br><center>ASSEMBLEA ANNULLATA";

// CANCELLO ANNOTAZIONE DI AUTORIZZAZIONE

$assq = "delete from tbl_annotazioni where idclasse=$idclasse and data='$dataass' and testo LIKE '%si autorizza assemblea%'";
// print inspref($assq);
eseguiQuery($con, $assq);
if (mysqli_affected_rows($con) == 1)
    print "<br><br><center>ANNOTAZIONE CANCELLATA";






print ("<form method='post' action='visionaverbali.php' id='formdisp'>
		<br><input type='submit' value='Ritorna a elenco'>	
        </form> 
        
      
       ");
/* <SCRIPT language='JavaScript'>
  {
  document.getElementById('formdisp').submit();
  }
  </SCRIPT> */


mysqli_close($con);
stampa_piede("");
