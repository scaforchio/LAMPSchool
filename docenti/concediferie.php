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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
//require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();
// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
//$iddocente = $_SESSION["idutente"];


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Concessione ferie";
$script = "";
stampa_head($titolo, "", $script, "P");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$nominativodirigente = estrai_dati_docente(1000000000, $con);
$prot = stringa_html('prot');
$conc = stringa_html('conc');

if ($conc == 0)
    $esito = "<br><br><b>Vista la domanda: NON SI CONCEDE!<br><br><center>Il Dirigente Scolastico<br>$nominativodirigente</b>";
if ($conc == 1)
    $esito = "<br><br><b>Vista la domanda: SI CONCEDE!<br><br><center>Il Dirigente Scolastico<br>$nominativodirigente</b>";

$query = "update tbl_richiesteferie set concessione=$conc, testomail=concat(testomail,'" . $esito . "') where idrichiestaferie=$prot";
mysqli_query($con, inspref($query)) or die("Errore $query");

$query = "select * from tbl_richiesteferie where idrichiestaferie=$prot";
$ris = mysqli_query($con, inspref($query)) or die("Errore $query");
$rec = mysqli_fetch_array($ris);

$iddocente = $rec['iddocente'];
$subject = $rec['subject'];
$testomail = $rec['testomail'];

$query = "select email from tbl_docenti where iddocente=$iddocente";
$risemail = mysqli_query($con, inspref($query)) or die("Errore $query");
$recemail = mysqli_fetch_array($risemail);
$indirizzomaildocente = $recemail['email'];

if ($conc != 2)
{
    $esitomailprotocollo = invia_mail($indirizzomailassenze, $subject, $testomail);
    $esitomaildocente = invia_mail($indirizzomaildocente, $subject, $testomail);
    if (($conc == 1) && $mailresponsabilesostituzioni!='')
    {
        $esitomailresponsabile=invia_mail($mailresponsabilesostituzioni, $subject, "Richiesta in oggetto specificata accolta dal D.S.");
        //print "Esito $esitomailresponsabile";
        //die();
    }
    if ($esitomailprotocollo & $esitomaildocente)
        print "<form method='post' id='formlez' action='esamerichferie.php'>
       <input type='submit' value='OK'>
       </form>
       <SCRIPT language='JavaScript'>
	  {
	      document.getElementById('formlez').submit();
	  }
       </SCRIPT>";
    else
    {
        if (!$esitomailprotocollo)
            print "Errore nell'invio della mail al protocollo.";
        if (!$esitomaildocente)
            print "Errore nell'invio della mail al docente.";
        
        print "<br><br><form method='post' id='formlez' action='esamerichferie.php'>
       <center><input type='submit' value='OK'></center>
       </form>";
    }
}
else
{
    $testomail = "In merito alla richiesta di astensione dal lavoro in oggetto la S.V. è pregata di recarsi dal D.S. per chiarimenti.<br><br>Distinti saluti.<br><br><center>IL DIRIGENTE SCOLASTICO</center>";
    if (invia_mail($indirizzomaildocente, $subject, $testomail))
        print "<form method='post' id='formlez' action='esamerichferie.php'>
       <input type='submit' value='OK'>
       </form>
       <SCRIPT language='JavaScript'>
	  {
	      document.getElementById('formlez').submit();
	  }
       </SCRIPT>";
    else
        print "Errore nell'invio della mail al docente.<br><br><form method='post' id='formlez' action='esamerichferie.php'>
       <center><input type='submit' value='OK'></center>
       </form>";
}
mysqli_close($con);
stampa_piede("");
