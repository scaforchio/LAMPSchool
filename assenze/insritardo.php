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

$titolo = "Inserimento ritardi";
$script = "";
stampa_head($titolo, "", $script,"MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$gio = stringa_html('gio');
$mese = stringa_html('mese');
$anno = stringa_html('anno');
$data = $anno . "/" . $mese . "/" . $gio;

$idclasse = stringa_html('cl');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$query = "SELECT idalunno AS al FROM tbl_alunni WHERE idalunno IN (" . estrai_alunni_classe_data($idclasse, $data, $con) . ")";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con)." " .inspref($query,false));

while ($id = mysqli_fetch_array($ris))
{
    // $idal = stringa_html('rit'.$id['al'])?"on":"off";


    $ritardopresente=false;
    $cambiamento=false;
    $idalunno = $id['al'];
    $numeroore = stringa_html('numeroore' . $id['al']);
    $oraentrata = stringa_html('oraentrata' . $id['al']);
    $query = 'SELECT * FROM tbl_ritardi WHERE idalunno=' . $id['al'] . ' AND data="' . $data . '"';
    $rissel = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con)." " .inspref($query,false));
    if (mysqli_num_rows($rissel) > 0)
    {
        $ritardopresente=true;
        $rec=mysqli_fetch_array($rissel);
        $idritardo=$rec['idritardo'];
    }
    if ($numeroore != 0 | checktime($oraentrata))
    {
        if (!$ritardopresente)
        {
            if ($tipoutente=='D')
                $query = "insert into tbl_ritardi(idalunno,data,oraentrata,numeroore) values('$idalunno','$data','$oraentrata','$numeroore')";
            if ($tipoutente=='P' | $tipoutente=='S')
                $query = "insert into tbl_ritardi(idalunno,data,oraentrata,numeroore,autorizzato) values('$idalunno','$data','$oraentrata','$numeroore',true)";
            $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query inserimento: " . mysqli_error($con) . " " . inspref($query, false));
            $query = "delete from tbl_assenze where idalunno='$idalunno' and data='$data'";
            $ris3 = mysqli_query($con, inspref($query)) or die ("Errore nella query canc. ass.: " . mysqli_error($con) . " " . inspref($query, false));
        }
        else
        {
            $query = "update tbl_ritardi set oraentrata='$oraentrata', numeroore='$numeroore' where idritardo=$idritardo";
            mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query,false));
        }
        $cambiamento=true;
        // inserisci_assenze_per_ritardi($con,$idalunno,$data,$numeroore);

    }
    else
    {
        if (!checktime($oraentrata) & $oraentrata != "")
        {
            print "<br><center>Controllare orario per " . decodifica_alunno($id['al'], $con) . "</center>";
        }
        if ($ritardopresente)
        {
            $query = "DELETE FROM tbl_ritardi WHERE idritardo=$idritardo";
            $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con)." " .inspref($query,false));
            $cambiamento=true;
        }
    }
    if ($cambiamento)
    {
        //ricalcola_ritardi($con, $idalunno, $data);
        elimina_assenze_lezione($con, $idalunno, $data);
        inserisci_assenze_per_ritardi_uscite($con, $idalunno, $data);
    }
    //  ricalcola_uscite($con,$idalunno,$data,$data);
    //  ricalcola_assenze($con,$idalunno,$data,$data);
}


/*
while ($id = mysqli_fetch_array($ris))
{
    // $idal = stringa_html('rit'.$id['al'])?"on":"off";
    $idalunno = $id['al'];
    $numeroore = stringa_html('numeroore' . $id['al']);
    $oraentrata = stringa_html('oraentrata' . $id['al']);
    $query = 'SELECT * FROM tbl_ritardi WHERE idalunno=' . $id['al'] . ' AND data="' . $data . '"';
    $rissel = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con)." " .inspref($query,false));
    if (mysqli_num_rows($rissel) > 0)
    {
        $query = 'DELETE FROM tbl_ritardi WHERE idalunno=' . $id['al'] . ' AND data="' . $data . '"';
        $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con)." " .inspref($query,false));
    }
    if ($numeroore != 0 | checktime($oraentrata))
    {
        $query = "insert into tbl_ritardi(idalunno,data,oraentrata,numeroore) values('$idalunno','$data','$oraentrata','$numeroore')";
        $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query inserimento: " . mysqli_error($con)." " .inspref($query,false));
        $query = "delete from tbl_assenze where idalunno='$idalunno' and data='$data'";
        $ris3 = mysqli_query($con, inspref($query)) or die ("Errore nella query canc. ass.: " . mysqli_error($con)." " .inspref($query,false));


        // inserisci_assenze_per_ritardi($con,$idalunno,$data,$numeroore);

    }
    else
    {
        if (!checktime($oraentrata) & $oraentrata != "")
        {
            print "<br><center>Controllare orario per " . decodifica_alunno($id['al'], $con) . "</center>";
        }
    }
    if (checktime($oraentrata))
        ricalcola_ritardi($con,$idalunno,$data);
    elimina_assenze_lezione($con,$idalunno,$data);
    inserisci_assenze_per_ritardi_uscite($con,$idalunno,$data);
  //  ricalcola_uscite($con,$idalunno,$data,$data);
  //  ricalcola_assenze($con,$idalunno,$data,$data);
}
*/
// ricalcola_assenze_lezioni_classe($con,$idclasse,$data);

if ($_SESSION['regcl'] != "")
{
    $pr = $_SESSION['prove'];
    $cl = $_SESSION['regcl'];
    $ma = $_SESSION['regma'];
    $gi = $_SESSION['reggi'];
    $_SESSION['regcl'] = "";
    $_SESSION['regma'] = "";
    $_SESSION['reggi'] = "";
    // header("location: ../regclasse/riepgiorno.php?gio=$gi&meseanno=$ma&idclasse=$cl");
    print "
        <form method='post' id='formrit' action='../regclasse/$pr'>
        <input type='hidden' name='gio' value='$gi'>
        <input type='hidden' name='meseanno' value='$ma'>
        <input type='hidden' name='idclasse' value='$cl'>
        </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formrit').submit();
        }
        </SCRIPT>";
}
else
{
    echo '
           <p align="center">
           <font size=4 color="black">I dati sono stati inseriti correttamente</font>
         ';


    //  codice per richiamare il form dei tbl_ritardi;
    print ('
   <form method="post" action="rit.php">
   <p align="center">
    <p align="center">');
    print "
      <input type='hidden' name='cl' value='$idclasse'>
      <input type='hidden' name='gio' value='$gio'>
	   <input type='hidden' name='meseanno' value='$mese - $anno'>
	   ";
    print('
   
   	   <input type="submit" value="OK" name="b"></p>
     </form>
  ');
}
stampa_piede("");

function checktime($ora)
{
    $contr = true;
    if (substr($ora, 0, 2) < "00" | substr($ora, 0, 2) > "23")
    {
        return false;
    }
    if (substr($ora, 3, 2) < "00" | substr($ora, 0, 2) > "59")
    {
        return false;
    }
    if (substr($ora, 2, 1) != ":")
    {
        return false;
    }
    return $contr;
}


