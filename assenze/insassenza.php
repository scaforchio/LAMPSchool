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

$titolo = "Inserimento assenze";
$script = "";
stampa_head($titolo, "", $script,"MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$gio = stringa_html('gio');
$mese = stringa_html('mese');
$anno = stringa_html('anno');
$data = $anno . "-" . $mese . "-" . $gio;
//print $data;
$idclasse = stringa_html('cl');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$query = "SELECT idalunno AS al FROM tbl_alunni WHERE idalunno in (".estrai_alunni_classe_data($idclasse,$data,$con).")";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));


while ($id = mysqli_fetch_array($ris))
{
    $cambiamento=false;
    $idal = stringa_html('ass' . $id['al']) ? "on" : "off";
    if ($idal == "off")
    {
        $query = 'SELECT * FROM tbl_assenze WHERE idalunno=' . $id['al'] . ' AND data="' . $data . '"';
        $rissel = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        if (mysqli_num_rows($rissel) > 0)
        {
            $query = 'DELETE FROM tbl_assenze WHERE idalunno=' . $id['al'] . ' AND data="' . $data . '"';
            $risdel = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            // elimina_assenze_lezione($con,$id['al'],$data);
            $cambiamento=true;
        }
    }
    if ($idal == "on")
    {
        $query = 'SELECT * FROM tbl_assenze WHERE idalunno=' . $id['al'] . ' AND data="' . $data . '"';
        $riscer = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        if (mysqli_num_rows($riscer) == 0)
        {
            $query = 'INSERT INTO tbl_assenze(idalunno,data) VALUES(' . $id['al'] . ',"' . $data . '")';
            $risins = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            $cambiamento=true;
        }


    }
    if ($cambiamento)
    {
        elimina_assenze_lezione($con, $id['al'], $data);
        inserisci_assenze_per_ritardi_uscite($con, $id['al'], $data);
    }
   // ricalcola_uscite($con,$id['al'],$data,$data);
   // ricalcola_assenze($con,$id['al'],$data,$data);
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
        <form method='post' id='formass' action='../regclasse/$pr'>
        <input type='hidden' name='gio' value='$gi'>
        <input type='hidden' name='meseanno' value='$ma'>
        <input type='hidden' name='idclasse' value='$cl'>
        </form>
        <SCRIPT language='JavaScript'>
           document.getElementById('formass').submit();
        </SCRIPT>";
}
else
{
    echo '
           <p align="center">
           <font size=4 color="black">I dati sono stati inseriti correttamente</font>
         ';


    //  codice per richiamare il form delle tbl_assenze;
    print ('
   <form method="post" action="ass.php">
   <p align="center">

      <input type="hidden" name="cl" value="' . $idclasse . '">
       <input type="hidden" name="gio" value="' . $gio . '">
	   <input type="hidden" name="meseanno" value="' . $mese . ' - ' . $anno . '">');

    print('
          <input type="submit" value="OK" name="b"></p>
     </form>
  ');
}
stampa_piede("");

