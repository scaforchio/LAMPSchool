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

$titolo = "Inserimento nota individuale";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$ins = false;

$gio = stringa_html('gio');
$param_mese = stringa_html('mese');
$mese = substr($param_mese, 0, 2);
$anno = substr($param_mese, 5, 4);

$data = $anno . "-" . $mese . "-" . $gio;
// print $data;
//print $_POST['iddocente'];
$idclasse = stringa_html('idclasse');
$iddocente = stringa_html('iddocente');
// print "Id doc: $iddocente";
$idalunni = stringa_html('idalunno');
$notacl = stringa_html('notacl');
$provvedimenti = stringa_html('provvedimenti');
$idnota = stringa_html('idnota');


if (count($idalunni) == 0 or $idalunni == "")
{
    print "<center><b><br>Nessun alunno selezionato!</b></center>";
}
else
{
    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


    if ($idnota != '')
    {

        $query = "update tbl_notealunno set testo='$notacl',provvedimenti='$provvedimenti',
		          iddocente=$iddocente, data='$data' 
		           where idnotaalunno=$idnota";
        $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query di aggiornamento: " . mysqli_error($con));

        $query = "delete from tbl_noteindalu where idnotaalunno=$idnota";
        $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query di aggiornamento: " . mysqli_error($con));

        foreach ($idalunni as $idalunno)
        {
            $query = "insert into tbl_noteindalu(idalunno,idnotaalunno) values ($idalunno,$idnota)";
            $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query di aggiornamento: " . mysqli_error($con));
        }
        print "<br><center><b>Modifica effettuata!</b></center>";
    }
    else
    {
        $query = "insert into tbl_notealunno(testo,provvedimenti,iddocente,idclasse,data)
		          values('$notacl','$provvedimenti',$iddocente,$idclasse,'$data')";
        //   print inspref($query);
        $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query di inserimento: " . mysqli_error($con));
        $idnota = mysqli_insert_id($con);

        foreach ($idalunni as $idalunno)
        {
            $query = "insert into tbl_noteindalu(idalunno,idnotaalunno) values ($idalunno,$idnota)";
            $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query di inserimento: " . mysqli_error($con));
        }
        print "<br><center><b>Inserimento effettuato!</b></center>";
    }
    mysqli_close($con);
}

if ($_SESSION['regcl'] != "")
{
    $pr = $_SESSION['prove'];
    $cl = $_SESSION['regcl'];
    $ma = $_SESSION['regma'];
    $gi = $_SESSION['reggi'];
    $_SESSION['regcl'] = "";
    $_SESSION['regma'] = "";
    $_SESSION['reggi'] = "";
    print "
        <form method='post' id='formnotind' action='../regclasse/$pr'>
        <input type='hidden' name='gio' value='$gi'>
        <input type='hidden' name='meseanno' value='$ma'>
        <input type='hidden' name='idclasse' value='$cl'>
        </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formnotind').submit();
        }
        </SCRIPT>";
}
else
{
    print ("
   <form method='post' action='noteindmul.php'>
   <p align='center'>");
    print ("
      <input type='hidden' name='idnota' value='$idnota'>
           
      <input type='hidden' name='idclasse' value='$idclasse'>
      <input type='hidden' name='gio' value='$gio'>
      <input type='hidden' name='mese' value='$mese - $anno'>
      <input type='hidden' name='iddocente' value='$iddocente'> 
      <input type='hidden' name='nota' value='$notacl'>
      <input type='hidden' name='provvedimenti' value='$provvedimenti'>      
       ");

    print("   <input type='submit' value='OK' name='b'></p>

     </form>
  ");
}
stampa_piede("");

