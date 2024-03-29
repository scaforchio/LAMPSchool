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
$iddocente = $_SESSION["idutente"];
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Importazione programma scolastico per alunno";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$maxcomp = 20;

$idcattedra = stringa_html('idcattedra');
$idalunno = stringa_html('idalunno');
$idmateria = stringa_html('idmateria');
$idclasse = stringa_html('idclasse');
$progrimp = stringa_html('progrimp');
$completa = stringa_html('completa');
$obmin = "";
if ($completa == 'o')
    $obmin = "obminimi and ";
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));



/*
 * Controllo che non ci siano già valutazioni inserite legate ad abilità e conoscenze
 * in tal caso non permetto l'importazione
 *  
 */
/*
  $query="select count(*) as numerovoti
  from tbl_valutazioniabilcono,tbl_valutazioniintermedie
  where tbl_valutazioniabilcono.idvalint = tbl_valutazioniintermedie.idvalint
  and tbl_valutazioniintermedie.iddocente = '$iddocente'";


  // print (inspref($query));



  $ris=eseguiQuery($con,$query);
  $nom=mysqli_fetch_array($ris);
  if ($nom['numerovoti']>0)
  {
  print("<center>Non è possibile importare la programmazione scolastica in quanto sono già presenti voti associati alla programmazione del docente!</center>");
  }


  else
  {
 */


//
//  ELIMINO TUTTE LE ABILITA' DELL'ALUNNO
//

$query = "select idcompetenza from tbl_competalu where idmateria=$idmateria and idclasse=$idclasse and idalunno=$idalunno";
$riscompdoc = eseguiQuery($con, $query);
while ($nomcompdoc = mysqli_fetch_array($riscompdoc))
{
    $idcompdoc = $nomcompdoc["idcompetenza"];
    $query = "delete from tbl_abilalu where idcompetenza=$idcompdoc";
    eseguiQuery($con, $query);
}
//   print "<center><b>Eliminate abilit&aacute; e conoscenze per cattedra $idcattedra</b></center>";
//
//  Elimino tutte le competenze già inserite dal docente
//

$query = "delete from tbl_competalu where idmateria=$idmateria and idclasse=$idclasse and idalunno=$idalunno";
eseguiQuery($con, $query);
//  print "<center><b>Eliminate competenze per cattedra $idcattedra</b></center>";
//
//  Importo dalla programmazione scolastica tutte le competenze e le abilità
//
if ($progrimp == 'i')
    $query = "select *,$idclasse as idclasse from tbl_competscol where idmateria=$idmateria and anno='" . decodifica_anno_classe($idclasse, $con) . "'";
if ($progrimp == 'd')
    $query = "select *," . decodifica_anno_classe($idclasse, $con) . " as anno from tbl_competdoc where idmateria=$idmateria and idclasse='$idclasse'";

// print inspref($query); 
$riscomp = eseguiQuery($con, $query);
while ($nomcomp = mysqli_fetch_array($riscomp))
{
    $anno = $nomcomp["anno"];
    $idclasse = $nomcomp["idclasse"];
    $sintcomp = $nomcomp["sintcomp"];
    $numordcomp = $nomcomp["numeroordine"];
    $competenza = $nomcomp["competenza"];
    $idcompetenza = $nomcomp["idcompetenza"];
    $query = "insert into tbl_competalu(idmateria, idclasse,idalunno, numeroordine, sintcomp, competenza) values ($idmateria, $idclasse, $idalunno, $numordcomp,'$sintcomp','$competenza')";
    eseguiQuery($con, $query);
    // Rilevo l'id dell'inserimento effettuato
    // print "<center><b>Importata competenza $sintcomp classe $idclasse materia $idmateria</b></center>";
    $ultimoidcompetenza = mysqli_insert_id($con);
    if ($progrimp == 'i')
        $query = "select * from tbl_abilscol where $obmin idcompetenza=$idcompetenza";
    if ($progrimp == 'd')
        $query = "select * from tbl_abildoc where $obmin idcompetenza=$idcompetenza";



    $risabil = eseguiQuery($con, $query);
    while ($nomabil = mysqli_fetch_array($risabil))
    {
        $sintabil = $nomabil["sintabilcono"];
        $numordabil = $nomabil["numeroordine"];
        $abilcono = $nomabil["abilcono"];
        $obminimi = $nomabil["obminimi"];
        $abil_cono = $nomabil["abil_cono"];

        $query = "insert into tbl_abilalu(idcompetenza, numeroordine, sintabilcono, abilcono, obminimi, abil_cono)
							  values ($ultimoidcompetenza,$numordabil,'$sintabil','$abilcono',$obminimi,'$abil_cono')";
        eseguiQuery($con, $query);
        // print "<center><b>Importata abilit&aacute; e/o conoscenza $sintabil per classe $idclasse materia $idmateria</b></center>";
    }
}


print "
        <form method='post' id='compalu' action='compalu.php'>
        <input type='hidden' name='idcattedra' value='$idcattedra'>
        </form>
        <SCRIPT language='JavaScript'>
           document.getElementById('compalu').submit();
        </SCRIPT>";

mysqli_close($con);
stampa_piede("");

