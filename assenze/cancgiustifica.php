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


$titolo = "Cancellazione giustificazioni";
$script = "<script type='text/javascript'>
 <!--
  var stile = 'top=10, left=10, width=600, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
     function Popup(apri) {
        window.open(apri, '', stile);
     }
 //-->
</script>";

stampa_head($titolo, "", $script, "SPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

// stampa_head($titolo,"",$script);

$idassenza = stringa_html('idassenza');
$idritardo = stringa_html('idritardo');
$iduscita = stringa_html('iduscita');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

if ($idassenza != '')
{
    $query = "UPDATE tbl_assenze SET giustifica=0, datagiustifica=NULL, iddocentegiust=0 WHERE idassenza=$idassenza";
    $risupd = eseguiQuery($con, $query);
}
if ($idritardo != '')
{
    $query = "UPDATE tbl_ritardi SET giustifica=0, datagiustifica=NULL, iddocentegiust=0 WHERE idritardo=$idritardo";
    $risupd = eseguiQuery($con, $query);
}
if ($iduscita != '')
{
    $query = "UPDATE tbl_usciteanticipate SET giustifica=0, datagiustifica=NULL, iddocentegiust=0 WHERE iduscita=$iduscita";
    $risupd = eseguiQuery($con, $query);
}

print "
			  <form method='post' id='formlez' action='../assenze/visgiustifiche.php'>

			  </form>
			  <SCRIPT language='JavaScript'>
			  {
				  document.getElementById('formlez').submit();
			  }
			  </SCRIPT>
         ";


// fine if
stampa_piede("");
mysqli_close($con);


