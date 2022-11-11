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
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Inserimento richiesta appuntamento";
$script = "";
stampa_head($titolo, "", $script, "T");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='orario.php'>Orario</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
$iddocente = stringa_html('iddocente');
$idclasse = stringa_html('idclasse');

$giorno = stringa_html('giorno');

$idalunno = $_SESSION['idutente'];
$appuntamento=explode("|",$giorno);
//$data = $substr($giorno, 0, 10);
//$idoraricevimento = substr($giorno, 11);
$data=$appuntamento[0];
$idoraricevimento=$appuntamento[1];
$tiporicevimento =$appuntamento[2];
$query = "select * from tbl_orericevimento,tbl_orario "
        . "where tbl_orericevimento.idorario=tbl_orario.idorario and idoraricevimento=$idoraricevimento";
//print $query;
$ris = eseguiQuery($con, $query);
$rec = mysqli_fetch_array($ris);
$inizio = $rec['inizio'];
$fine = $rec['fine'];
$numerocolloqui = numero_colloqui_docente($iddocente, $idoraricevimento, $data, $con);
$note = "Appuntamento ore " . proponi_orario($inizio, $fine, $iddocente, $numerocolloqui, $con);
if ($tiporicevimento=="ol")
    $note.=" (ONLINE)";
//print "$data $idoraricevimento $inizio $fine";die();
if ($data != "") {
    $query = "insert into tbl_prenotazioni(idalunno,data,idoraricevimento,conferma,note)
        values ($idalunno,'$data',$idoraricevimento,1,'$note')";
    $ris = eseguiQuery($con, $query);
}

print ("<form method='post' action='../colloqui/richiestaappuntamento.php' id='formdisp'>
   
        <input type='hidden' name='iddocente' value='$iddocente'>
        <input type='hidden' name='idclasse' value='$idclasse'>
        </form> 
      
        <SCRIPT language='JavaScript'>
            {
                document.getElementById('formdisp').submit();
            }
        </SCRIPT>  
      
       ");
stampa_piede("");

function proponi_orario($inizio, $fine, $iddocente, $sequenzaudienza, $conn) {
    $query = "select nummaxcolloqui from tbl_docenti where iddocente=$iddocente";
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);
    $nummaxcolloqui = $rec['nummaxcolloqui'];
    // print "Inizio $inizio Fine $fine";
    $oraini = substr($inizio, 0, 2);
    $minini = substr($inizio, 3, 2);
    $minutiiniziali = $oraini * 60 + $minini;
    $orafin = substr($fine, 0, 2);
    $minfin = substr($fine, 3, 2);
    $minutifinali = $orafin * 60 + $minfin;
    $durata = $minutifinali - $minutiiniziali;
    $durataudienza = floor($durata / $nummaxcolloqui);
    $minutiinizioudienza = $durataudienza * ($sequenzaudienza);
    $oraappuntamento = aggiungi_minuti($inizio, $minutiinizioudienza);
    return $oraappuntamento;
}
