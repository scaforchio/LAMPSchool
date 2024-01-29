<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2015 Pietro Tamburrano
  Copyright (C) 2023 Michele Sacco - Flowopia Network [Rielaborazione sezione assemblee per adeguamento nuova UI]
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

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$titolo = "Inserimento verbale";
$script = "";
stampa_head_new($titolo, "", $script, "L");
//$idclasse = stringa_html('idclasse');
$idalunno = $_SESSION['idstudente'];
$idclasse = estrai_classe_alunno($idalunno, $con);
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='assricgen.php?idclasse=$idclasse'>Visualizza assemblee</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

// Recupero variabili da form con POST
$idassemblea = stringa_html('idassemblea');
$data = stringa_html('dataass');
$odg = stringa_html('odg');
$orainizio = stringa_html('orainizio');
// Recupero punti ODG
$puntiodg = [stringa_html('p1'), stringa_html('p2'), stringa_html('p3'), stringa_html('p4'), stringa_html('p5'), stringa_html('p6'), stringa_html('p7'), stringa_html('p8'), stringa_html('p9'), stringa_html('p10')];
$oratermine = stringa_html('orafine');
$segretario = stringa_html('segretario');
$modifica = stringa_html('modifica');

// Controllo se esiste verbale == modifica verbale (non inserimento)
$query = "SELECT * FROM tbl_assemblee WHERE idassemblea=$idassemblea";
$ris3 = eseguiQuery($con, $query);
$verbass = mysqli_fetch_array($ris3);
// Controllo se idalunno = rapp
$query = "SELECT * FROM tbl_classi WHERE idclasse=$idclasse";
$ris = eseguiQuery($con, $query);
$rdc = mysqli_fetch_array($ris);
$rappdc1 = $rdc['rappresentante1'];
$rappdc2 = $rdc['rappresentante2'];
if($idalunno == $rappdc1 || $idalunno == $rappdc2){
  
  if($verbass['verbale'] == NULL){
    // Assemblaggio verbale
    // Stampa argomenti punti ODG
    $printpunti = "";
    for($i = 0; $i<10; $i++){
      if($puntiodg[$i] != ""){
        $t = $i+1;
        $temp = $puntiodg[$i];
        $printpunti = $printpunti ."Si analizza il $t. punto all'odg: \n$temp \n";
      }
    }

    $verbale = "Il giorno $data, alle ore $orainizio, la classe si è riunita in assemblea per trattare gli argomenti del seguente $odg \n" .$printpunti ."\nLetto e approvato il seguente verbale,";
    $verbale = $con->real_escape_string($verbale);
    $query = "UPDATE tbl_assemblee SET verbale='$verbale', alunnosegretario='$segretario', oratermine='$oratermine:00' WHERE idassemblea=$idassemblea";
    $ris2 = eseguiQuery($con, $query);
    header("Location: assricgen.php?idclasse=$idclasse");
  }else{
    // Modifica verbale già esistente
    $modifica = $con->real_escape_string($modifica);
    $query = "UPDATE tbl_assemblee SET verbale= '$modifica' , oratermine='$oratermine' WHERE idassemblea=$idassemblea";
    $ris4 = eseguiQuery($con, $query);
    header("Location: assricgen.php?idclasse=$idclasse");
  }
}

stampa_piede_new("");
mysqli_close($con);
