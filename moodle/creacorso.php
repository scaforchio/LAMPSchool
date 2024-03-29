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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
//require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();
// istruzioni per tornare alla pagina di login se non c'è una sessione valida


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Creazione corso Moodle";
$script = "";
stampa_head($titolo, "", $script, "MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$idclasse = stringa_html("idclasse");
$idmateria = stringa_html("idmateria");


$query = "select * from tbl_classi where idclasse=$idclasse";
$ris = eseguiQuery($con, $query);
$rec = mysqli_fetch_array($ris);
//$siglacategoria=$rec['idmoodle'];
$anno = $rec['anno'];
$sezione = $rec['sezione'];
$specializzazione = $rec['specializzazione'];
$specsigla = substr($specializzazione, 0, 3);
$annoinizio = $_SESSION['annoscol'];
//$siglacategoria.=$annoinizio;
$query = "select * from tbl_materie where idmateria=$idmateria";
$ris = eseguiQuery($con, $query);
$rec = mysqli_fetch_array($ris);
$nomemateria = $rec['denominazione'];
$siglamateria = $rec['sigla'];

// Modifica per cambiamento organizzazione categorie

$siglacategoria0 = $_SESSION['suffisso'];
$siglacategoria1 = $siglacategoria0 . $siglamateria;
$siglacategoria2 = $siglacategoria1 . $anno;
$siglacategoria3 = $siglacategoria2 . $annoinizio;

// Verifico ed eventualmente creo categoria livello 1
print "$siglacategoria0 $siglacategoria1 $siglacategoria2 $siglacategoria3 <br>";
$idcategoria0 = getCategoriaMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $siglacategoria0);


if ($idcategoria0 == -1)
    $idcategoria0 = creaCategoriaMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $_SESSION['nome_scuola'], $siglacategoria0, 0);

$idcategoria1 = getCategoriaMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $siglacategoria1);

if ($idcategoria1 == -1)
    $idcategoria1 = creaCategoriaMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $nomemateria, $siglacategoria1, $idcategoria0);

// Verifico ed eventualmente creo categoria livello 2
$idcategoria2 = getCategoriaMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $siglacategoria2);

if ($idcategoria2 == -1)
    $idcategoria2 = creaCategoriaMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], "$nomemateria - Anno $anno", $siglacategoria2, $idcategoria1);

// Verifico ed eventualmente creo categoria livello 3
$idcategoria3 = getCategoriaMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $siglacategoria3);

if ($idcategoria3 == -1)
    $idcategoria3 = creaCategoriaMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], "$nomemateria - Anno $anno - A.S. $annoinizio", $siglacategoria3, $idcategoria2);


// Fine modificha per cambiamento organizzazione categorie





$nomecorso = $nomemateria . " " . $anno . " " . $sezione . " " . $specializzazione . " " . $annoinizio;
$siglacorso = $siglamateria . $anno . $sezione . $specsigla . $annoinizio;
print " $nomecorso $siglacorso <br>";
//print "<br><br>$siglacorso    $siglacategoria    $nomecorso";
print "<br><br>$siglacorso    $siglacategoria3    $nomecorso";
//$idcategoria = getCategoriaMoodle($_SESSION['tokenservizimoodle'],$_SESSION['urlmoodle'],$siglacategoria);
print "<br><br>$idcategoria3";

//$idcorso = creaCorsoMoodle($_SESSION['tokenservizimoodle'],$_SESSION['urlmoodle'],$nomecorso,$siglacorso,$idcategoria);
$idcorso = creaCorsoMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $nomecorso, $siglacorso, $idcategoria3);
print "<br>Corso creato con id: $idcorso";


$query = "select * from tbl_cattnosupp where idmateria = $idmateria and idclasse = $idclasse and idalunno=0 and iddocente<>1000000000";
$ris = eseguiQuery($con, $query);
while ($rec = mysqli_fetch_array($ris))
{
    //$usernamedocente="doc".$_SESSION['suffisso'].($rec["iddocente"]-1000000000);
    $usernamedocente = costruisciUsernameMoodle($rec['iddocente']);
    print "<br>Docente: $usernamedocente";
    $identutente = getIdMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $usernamedocente);

    iscriviUtenteMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $idcorso, $identutente, 3);
}

$query = "select * from tbl_alunni where idclasse = $idclasse";
$ris = eseguiQuery($con, $query);
while ($rec = mysqli_fetch_array($ris))
{
    //$usernamealunno="al".$_SESSION['suffisso'].($rec["idalunno"]);
    $usernamealunno = costruisciUsernameMoodle($rec['idalunno']);
    print "<br>Alunno: $usernamealunno";
    $identalunno = getIdMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $usernamealunno);

    iscriviUtenteMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $idcorso, $identalunno, 5);
}


print "	  <form method='post' id='formlez' action='creacorsimoodle.php'>
              <input type='submit' value='Indietro'>
			  </form>
			  ";


mysqli_close($con);
stampa_piede("");
