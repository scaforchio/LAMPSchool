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

$titolo = "Inserimento proposte per valutazione obiettivi";
$script = "";
stampa_head($titolo, "", $script, "SDP");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
$idmateria = stringa_html('idmateria');
$idalunno = stringa_html('idalunno');
$idclasse = stringa_html('idclasse');

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='obproposteint.php?idclasse=$idclasse'>PROPOSTE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

// Cancello le vecchie proposte

$querydel = "delete from tbl_valutazioniobiettivi where idalunno=$idalunno and periodo=1 and idobiettivo in (select idobiettivo from tbl_obiettivi where idclasse=$idclasse and idmateria=$idmateria)";

eseguiQuery($con, $querydel);

// Inserisco le nuove proposte per ogni competenza presente

$query = "select * from tbl_obiettivi where idclasse=$idclasse and idmateria=$idmateria";

$ris = eseguiQuery($con, $query);

while ($rec = mysqli_fetch_array($ris))
{
    $queryins = "";
    $idobiettivo = $rec['idobiettivo'];
    $campo = "idlivelloob_" . $idobiettivo;
    $giud = stringa_html($campo);
    
    if ($giud != '')
    {
        $queryins = "insert into tbl_valutazioniobiettivi(idalunno,idobiettivo,idlivelloobiettivo,periodo) values ($idalunno, $idobiettivo,$giud,1)";
        eseguiQuery($con, $queryins);
    }
}



print "<br><br><center><big>Inserimento effettuato!</big>";
print ('
			<form method="post" action="obproposteint.php">
			<p align="center">');

    // Se la lezione non è stata cancellata si passa il codice
    
        print ('<p align="center"><input type=hidden value=' . $idclasse . ' name=idclasse>');
        print ('<p align="center"><input type=hidden value=' . $idalunno . ' name=idalunno>');
        print ('<p align="center"><input type=hidden value=' . $idmateria . ' name=idmateria>');
    print('<input type="submit" value="OK" name="b"></p></form>');


mysqli_close($con);
stampa_piede("");

