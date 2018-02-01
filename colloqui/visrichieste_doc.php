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



@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$iddocente = $_SESSION['idutente'];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Gestione richieste colloqui";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


$query = "select tbl_prenotazioni.idoraricevimento as idoraric,cognome, nome,datanascita,idclasse, inizio, fine,tbl_prenotazioni.note as note,tbl_prenotazioni.valido, data, idprenotazione, conferma from tbl_prenotazioni,tbl_orericevimento,tbl_alunni,tbl_orario
        where tbl_prenotazioni.idoraricevimento=tbl_orericevimento.idoraricevimento
        and tbl_prenotazioni.idalunno =  tbl_alunni.idalunno
        and tbl_orericevimento.idorario = tbl_orario.idorario
        and iddocente=$iddocente
        and tbl_orericevimento.valido = 1   
        order by data desc";

$ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
print "<table border=1 align=center>
       <tr class='prima'>
           <td>Alunno</td>
           <td>Data e ora</td>
           <td>Risposta</td>
       </tr>";

while ($rec = mysqli_fetch_array($ris))
{

    $dataoggi = date('Y-m-d');
    $alunno = $rec['cognome'] . " " . $rec['nome'] . "(" . data_italiana($rec['datanascita']) . ") - " . decodifica_classe($rec['idclasse'], $con);
    $data = giorno_settimana($rec['data']) . " " . data_italiana($rec['data']) . " " . $rec['inizio'] . "-" . $rec['fine'];
    $note = $rec['note'];
    $idprenotazione = $rec['idprenotazione'];
    $idoraric=$rec['idoraric'];
    $risp = $rec['conferma'];
    $numerocolloqui= numero_colloqui_docente($iddocente, $idoraric, $dataoggi, $con);
    if ($risp == 1)
       // $note = "Appuntamento ore ".proponi_orario($rec['inizio'], $rec['fine'], $iddocente,$numerocolloqui+1, $con);
         $note = "Appuntamento ore ".proponi_orario($rec['inizio'], $rec['fine'], $iddocente,$numerocolloqui, $con);
    $valido = $rec['valido'];
    if ($risp == 1)
        $sele1 = ' selected';
    else
        $sele1 = '';
    if ($risp == 2)
        $sele2 = ' selected';
    else
        $sele2 = '';
    if ($risp == 3)
        $sele3 = ' selected';
    else
        $sele3 = '';
    print "<tr";
    if ($dataoggi < $rec['data'])
        print " class='green'";
    if ($dataoggi == $rec['data'])
        print " class='yellow'";
    if ($dataoggi > $rec['data'])
        print " class='red'";


    print ">";
    print " <td>$alunno</td>
           <td>$data</td>";
    if ($valido)
    {
        if ($dataoggi < $rec['data'])
            print " <td><form action='registrarisposta.php' method='post'>
                   <input type='hidden' name='idprenotazione' value='$idprenotazione'>
                   <select name='risposta'><option value='1'$sele1>In sosp.</option><option value='2'$sele2>Sì</option><option value='3'$sele3>No</option></select>
                   <input type='text' name='note' value='$note' maxlength='255' size='50'>
                   <input type='submit' value='Invia risposta'>
                   </form>
               </td>";
        else
            print " <td><form action='registrarisposta.php' method='post'>
                   <input type='hidden' name='idprenotazione' value='$idprenotazione'>
                   <select name='risposta' disabled><option value='1'$sele1>In sosp.</option><option value='2'$sele2>Sì</option><option value='3'$sele3>No</option></select>
                   <input type='text' name='note' value='$note' maxlength='255' size='50'>
                   
                   </form>
               </td>";
    }
    else
        print " <td><b>Prenotazione cancellata!</b></td>";
    print "</tr>";
}

print "</table>";



mysqli_close($con);
stampa_piede("");

function proponi_orario($inizio, $fine, $iddocente, $sequenzaudienza, $conn)
{
    $query = "select nummaxcolloqui from tbl_docenti where iddocente=$iddocente";
    $ris = mysqli_query($conn, inspref($query));
    $rec = mysqli_fetch_array($ris);
    $nummaxcolloqui = $rec['nummaxcolloqui'];
   // print "Inizio $inizio Fine $fine";
    $oraini = substr($inizio, 0, 2);
    $minini = substr($inizio, 3, 2);
    $minutiiniziali = $oraini * 60 + $minini;
    $orafin = substr($fine, 0, 2);
    $minfin = substr($fine, 3, 2);
    $minutifinali = $orafin * 60 + $minfin;
    $durata = $minutifinali-$minutiiniziali;
    $durataudienza=floor($durata/$nummaxcolloqui);
    $minutiinizioudienza=$durataudienza*($sequenzaudienza);
    $oraappuntamento= aggiungi_minuti($inizio, $minutiinizioudienza);
    return $oraappuntamento;
    
}
