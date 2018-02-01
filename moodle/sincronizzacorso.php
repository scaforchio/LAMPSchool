<?php session_start();

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

require_once '../php-ini'.$_SESSION['suffisso'].'.php';
require_once '../lib/funzioni.php';
//require_once '../lib/ db / query.php';

//$lQuery = LQuery::getIstanza();

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']);
    die;
}

$titolo = "Creazione corso Moodle";
$script = "";
stampa_head($titolo, "", $script,"SMP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));

$idclasse=stringa_html("idclasse");
$idmateria=stringa_html("idmateria");


$query="select * from tbl_classi where idclasse=$idclasse";
$ris=mysqli_query($con,inspref($query)) or die("Errore ".inspref($query,false));
$rec=mysqli_fetch_array($ris);
$siglacategoria=$rec['idmoodle'];
$anno=$rec['anno'];
$sezione=$rec['sezione'];
$specializzazione=$rec['specializzazione'];
$specsigla=substr($specializzazione,0,3);
$annoinizio=$annoscol;
$query="select * from tbl_materie where idmateria=$idmateria";
$ris=mysqli_query($con,inspref($query)) or die("Errore ".inspref($query,false));
$rec=mysqli_fetch_array($ris);
$nomemateria=$rec['denominazione'];
$siglamateria=$rec['sigla'];

$nomecorso=$nomemateria." ".$anno." ".$sezione." ".$specializzazione." ".$annoinizio;
$siglacorso=$siglamateria.$anno.$sezione.$specsigla.$annoinizio;

$idcorso=getIdCorsoMoodle($tokenservizimoodle,$urlmoodle,$siglacorso);
print "<br><br>$siglacorso    $siglacategoria    $nomecorso  $idcorso";

// CREO CATEGORIA E SPOSTO CORSO 

$siglacategoria0 = $_SESSION['suffisso'];
$siglacategoria1 = $siglacategoria0.$siglamateria;
$siglacategoria2 = $siglacategoria1.$anno;
$siglacategoria3 = $siglacategoria2.$annoinizio;

// Verifico ed eventualmente creo categoria livello 1

$idcategoria0=getCategoriaMoodle($tokenservizimoodle,$urlmoodle,$siglacategoria0);


if ($idcategoria0==-1)
    $idcategoria0=creaCategoriaMoodle($tokenservizimoodle,$urlmoodle,$nome_scuola,$siglacategoria0,0);

$idcategoria1=getCategoriaMoodle($tokenservizimoodle,$urlmoodle,$siglacategoria1);

if ($idcategoria1==-1)
    $idcategoria1=creaCategoriaMoodle($tokenservizimoodle,$urlmoodle,$nomemateria,$siglacategoria1,$idcategoria0);

// Verifico ed eventualmente creo categoria livello 2
$idcategoria2=getCategoriaMoodle($tokenservizimoodle,$urlmoodle,$siglacategoria2);

if ($idcategoria2==-1)
    $idcategoria2=creaCategoriaMoodle($tokenservizimoodle,$urlmoodle,"$nomemateria - Anno $anno",$siglacategoria2,$idcategoria1);

// Verifico ed eventualmente creo categoria livello 3
$idcategoria3=getCategoriaMoodle($tokenservizimoodle,$urlmoodle,$siglacategoria3);

if ($idcategoria3==-1)
    $idcategoria3=creaCategoriaMoodle($tokenservizimoodle,$urlmoodle,"$nomemateria - Anno $anno - A.S. $annoinizio",$siglacategoria3,$idcategoria2);

// SPOSTO CORSO


$risposta= aggiornaCategoriaCorso($tokenservizimoodle,$urlmoodle,$idcorso,$idcategoria3);
print "Esito spostamento: $risposta <br>";
// Eliminazione iscrizioni al corso



$utentiiscritti=getUtentiCorsoMoodle($tokenservizimoodle,$urlmoodle,$idcorso);

print $utentiiscritti;

foreach ($utentiiscritti as $utenteiscritto)
{
    $idutenteiscritto=$utenteiscritto->id;
    disiscriviUtenteMoodle($tokenservizimoodle,$urlmoodle,$idcorso,$idutenteiscritto);
}



// Iscrizione nuovi alunni e docenti al corso



$query="select * from tbl_cattnosupp where idmateria = $idmateria and idclasse = $idclasse and idalunno=0 and iddocente<>1000000000";
$ris=mysqli_query($con,inspref($query)) or die("Errore ".inspref($query,false));
while ($rec=mysqli_fetch_array($ris))
{
    // $usernamedocente="doc".$_SESSION['suffisso'].($rec["iddocente"]-1000000000);
    $usernamedocente=costruisciUsernameMoodle($rec['iddocente']);
    print "<br>Docente: $usernamedocente";
    $identutente=getIdMoodle($tokenservizimoodle,$urlmoodle,$usernamedocente);

    iscriviUtenteMoodle($tokenservizimoodle,$urlmoodle,$idcorso,$identutente,3);


}

$query="select * from tbl_alunni where idclasse = $idclasse";
$ris=mysqli_query($con,inspref($query)) or die("Errore ".inspref($query,false));
while ($rec=mysqli_fetch_array($ris))
{
    // $usernamealunno="al".$_SESSION['suffisso'].($rec["idalunno"]);
    $usernamealunno=costruisciUsernameMoodle($rec['idalunno']);
    print "<br>Alunno: $usernamealunno";
    $identalunno=getIdMoodle($tokenservizimoodle,$urlmoodle,$usernamealunno);

    iscriviUtenteMoodle($tokenservizimoodle,$urlmoodle,$idcorso,$identalunno,5);
}


print "	  <form method='post' id='formlez' action='creacorsimoodle.php'>
              <input type='submit' value='Indietro'>
			  </form>
			  ";


mysqli_close($con);
stampa_piede("");