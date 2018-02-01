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

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Inserimento diario di classe";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$ins = false;

$gio = stringa_html('gio');
$mese = stringa_html('mese');
$anno = stringa_html('anno');
$data = $anno . "-" . $mese . "-" . $gio;
// print $data;
$idclasse = stringa_html('idclasse');
$iddocente = stringa_html('iddocente');

$notacl = stringa_html('notacl');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$query = "delete from tbl_diariocl where iddocente=$iddocente and data='$data' and idclasse=$idclasse";
$ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query di cancellazione: " . mysqli_error($con));
$numerorighe = mysqli_affected_rows($con);

if (($notacl != ""))
{
    $ins = true;
    $query = "insert into tbl_diariocl(data,idclasse,iddocente,testo) values ('$data',$idclasse,$iddocente,'" . elimina_apici($notacl) . "')";
    $ris3 = mysqli_query($con, inspref($query)) or die ("Errore nella query di inserimento: " . mysqli_error($con));
    $idoss = mysqli_insert_id($con);
}
if ($ins)
{
    if ($numerorighe == 1)
    {
        print "<center><b>Variazione effettuata!</b></center>";
    }
    else
    {
        print "<center><b>Inserimento effettuato!</b></center>";
    }
}
else
{
    if ($numerorighe == 1)
    {
        print "<center><b>Cancellazione effettuata!</b></center>";
    }
    else
    {
        print "<center><b>Nessuna variazione apportata!</b></center>";
    }
}
//  codice per richiamare il form delle note;
print ("
   <form method='post' action='diariocl.php'>
   <p align='center'>

      <input type='hidden' name='idnota' value='$idoss'>");
 print ("      <input type='hidden' name='gio' value='$gio'>
       <input type='hidden' name='mese' value='$mese - $anno'>
           <input type='hidden' name='idclasse' value='$idclasse'>");
/*           <input type='hidden' name='idalunno' value='$idalunno'>
*/
print("   <input type='submit' value='OK' name='b'></p>

     </form>
  ");

mysqli_close($con);
stampa_piede("");

