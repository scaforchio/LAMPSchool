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

$suff=$_SESSION['suffisso']."/";
if ($suff=="/") $suff="";
// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idutente = $_SESSION["idutente"];
$userid = $_SESSION['userid'];

$nuovoutente=stringa_html('nuovoutente');
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Cambiamento utente";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$query = "select * from tbl_utenti where  userid='$nuovoutente'";

$ris=mysqli_query($con,inspref($query)) or die("Errore: ". inspref($query,false));

if (mysqli_num_rows($ris)==1)

{
    // print "Data: $dataultimamodifica - Ora: $dataodierna";
    // print "Diff: $giornidiff";



    $rec=mysqli_fetch_array($ris);
    $nuovotipo=$rec['tipo'];
    $nuovoutente=$rec['idutente'];
    $nuovouserid=$rec['userid']; // $nuovouserid=$nuovoutente;

    $_SESSION['alias']=true;
    $_SESSION['tipoorig']=$tipoutente;
    $_SESSION['idorig']=$idutente;
    $_SESSION['useridorig']=$userid;
    $_SESSION['tipoutente']=$nuovotipo;
    $_SESSION['idutente']=$nuovoutente;
    $_SESSION['userid']=$nuovouserid;

    if ($_SESSION['tipoutente'] == 'T')
    {
       // $sql = "SELECT * FROM tbl_tutori WHERE idutente='" . $_SESSION['idutente'] . "'";
        $sql = "SELECT * FROM tbl_alunni WHERE idutente='" . $_SESSION['idutente'] . "'";
        $ris = mysqli_query($con, inspref($sql)) or die ("Errore nella query: " . mysqli_error($con) . inspref($query));

        if ($val = mysqli_fetch_array($ris))
        {
            $_SESSION['idstudente'] = $val["idalunno"];
            $_SESSION['cognome'] = $val["cognome"];
            $_SESSION['nome'] = $val["nome"];
        }
    }

    if ($_SESSION['tipoutente'] == 'D' | $_SESSION['tipoutente'] == 'S' | $_SESSION['tipoutente'] == 'P')
    {
        $sql = "SELECT * FROM tbl_docenti WHERE idutente='" . $_SESSION['idutente'] . "'";
        $ris = mysqli_query($con, inspref($sql)) or die ("Errore nella query: " . mysqli_error($con) . inspref($query));

        if ($val = mysqli_fetch_array($ris))
        {
            $_SESSION['cognome'] = $val["cognome"];
            $_SESSION['nome'] = $val["nome"];
        }
    }

    if ($_SESSION['tipoutente'] == 'A')
    {
        $sql = "SELECT * FROM tbl_amministrativi WHERE idutente='" . $_SESSION['idutente'] . "'";
        $ris = mysqli_query($con, inspref($sql)) or die ("Errore nella query: " . mysqli_error($con) . inspref($query));

        if ($val = mysqli_fetch_array($ris))
        {
            $_SESSION['cognome'] = $val["cognome"];
            $_SESSION['nome'] = $val["nome"];
        }
    }

    inserisci_log($_SESSION['useridorig'] . "§" . date('m-d|H:i:s') . "§" . $_SESSION['indirizzoip'] . "§Aliasing in " . $_SESSION['userid'] );

    print "<br><b><center>Aliasing effettuato! Tornare a pagina principale.";
}
else
{
    print "<br><b><center>Utente inesistente!";
}



mysqli_close($con);
stampa_piede("");

