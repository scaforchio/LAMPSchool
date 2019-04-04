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
$iddocente = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Sincronizzazione corsi Moodle";
$script = "";
stampa_head($titolo, "", $script, "SMPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));





$query = "SELECT tbl_cattnosupp.idmateria,tbl_classi.idclasse,
idcattedra,tbl_docenti.iddocente,cognome,nome,sigla,tbl_classi.anno,tbl_classi.sezione,tbl_classi.specializzazione,tbl_materie.denominazione
 FROM 
tbl_cattnosupp,tbl_classi,tbl_materie,tbl_docenti
 WHERE 
tbl_cattnosupp.idclasse=tbl_classi.idclasse 
and tbl_cattnosupp.idmateria=tbl_materie.idmateria 
and tbl_cattnosupp.iddocente=tbl_docenti.iddocente 
and tbl_cattnosupp.iddocente<>1000000000
GROUP BY tbl_cattnosupp.idmateria,tbl_classi.idclasse
 ORDER BY 
    anno,specializzazione,sezione,denominazione";

$ris = eseguiQuery($con, $query);
$corsi = getCorsiMoodle($tokenservizimoodle, $urlmoodle);
// print "Corsi: $corsi";
if (mysqli_num_rows($ris) > 0)
{


    while ($lez = mysqli_fetch_array($ris))
    {
        $ann = $lez['anno'];
        $sez = $lez['sezione'];
        $spe = $lez['specializzazione'];
        $mat = $lez['denominazione'];
        $idmat = $lez['idmateria'];
        $idcla = $lez['idclasse'];
        $sigmat = $lez['sigla'];
        $specsigla = substr($spe, 0, 3);
        $siglacorso = $sigmat . $ann . $sez . $specsigla . $_SESSION['annoscol'];

        $presente = strstr($corsi, $siglacorso);
        if ($presente)
        {
            print "<br>Sincronizzazione corso classe $idcla materia $idmat";
            sincronizzaCorsoMoodle($idcla, $idmat, $con, $tokenservizimoodle, $urlmoodle, $nome_scuola, $annoscol);
        }
    }
}

mysqli_close($con);
stampa_piede("");

