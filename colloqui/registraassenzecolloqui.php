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
$data = stringa_html('data');

print "Data $data";
if($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

//caricamento $arrayiddoc
$query = "SELECT DISTINCT cognome,nome,tbl_docenti.iddocente
          FROM tbl_cattnosupp,tbl_docenti, tbl_colloquiclasse, tbl_classi, tbl_giornatacolloqui
          WHERE tbl_cattnosupp.iddocente=tbl_docenti.iddocente
          AND tbl_cattnosupp.idclasse=tbl_classi.idclasse
          AND tbl_colloquiclasse.idclasse=tbl_classi.idclasse
          AND tbl_colloquiclasse.idgiornatacolloqui=tbl_giornatacolloqui.idgiornatacolloqui
          AND tbl_cattnosupp.iddocente!=1000000000
          AND tbl_giornatacolloqui.data='$data'
          ORDER BY cognome,nome";

$risultato = eseguiQuery($con, $query);

$arrayiddoc = [];

while ($row = mysqli_fetch_array($risultato))
    $arrayiddoc[] = $row['iddocente'];


//caricamento docenti assenti
$query = "SELECT doc.iddocente
          FROM tbl_docenti AS doc
          WHERE EXISTS(SELECT *
                        FROM tbl_assenzedocenticolloqui, tbl_giornatacolloqui
                        WHERE tbl_assenzedocenticolloqui.iddocente=doc.iddocente
                        AND tbl_assenzedocenticolloqui.idgiornatacolloqui=tbl_giornatacolloqui.idgiornatacolloqui
                        AND tbl_giornatacolloqui.data='$data')";

$risultato = eseguiQuery($con, $query);

$docassenti = [];

while($row = mysqli_fetch_array($risultato))
    $docassenti[] = $row['iddocente'];



//estrazione idgiornatacolloqui
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$query = "SELECT DISTINCT idgiornatacolloqui
          FROM tbl_giornatacolloqui
          WHERE tbl_giornatacolloqui.data='$data'";

$risultato = eseguiQuery($con, $query);

$row = mysqli_fetch_array($risultato);

$idgiornatacolloqui = $row['idgiornatacolloqui'];


//salva nell'array le checkbox selezionate
$arrayidselez = [];

if(!empty($_POST['docenti']))
{
    foreach($_POST['docenti'] as $checked)
    {
      $arrayidselez[] = intval($checked);
    }
}

$arrayidnonselez = array_diff($arrayiddoc, $arrayidselez);

//generazione array checkbox non selezionate
$appoggio = [];
foreach($arrayidnonselez as $elemento)
{
  $appoggio[] = intval($elemento);
}
$arrayidnonselez = $appoggio;

foreach($arrayidselez as $iddocente)
{
  if(!in_array($iddocente, $docassenti))
  {
    $query = "INSERT INTO tbl_assenzedocenticolloqui (idgiornatacolloqui, iddocente) VALUES
             ($idgiornatacolloqui, $iddocente)";
    $risultato = eseguiQuery($con, $query);
    $query = "DELETE FROM tbl_slotcolloqui WHERE iddocente=$iddocente and idgiornatacolloqui=$idgiornatacolloqui";
    eseguiQuery($con, $query);
    
  }
}

foreach($arrayidnonselez as $iddocente)
{
  if(in_array($iddocente, $docassenti))
  {
    $query = "DELETE FROM tbl_assenzedocenticolloqui
              WHERE tbl_assenzedocenticolloqui.iddocente=$iddocente
              AND tbl_assenzedocenticolloqui.idgiornatacolloqui=$idgiornatacolloqui";

    $risultato = eseguiQuery($con, $query);
  }
}

header("location: ../colloqui/gestassenzecolloqui.php?data=$data");
