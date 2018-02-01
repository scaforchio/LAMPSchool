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

$titolo = "Inserimento uscita anticipata";
$script = "";
stampa_head($titolo, "", $script,"MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - -titolo", "", "$nome_scuola", "$comune_scuola");


$gio = stringa_html('gio');
$mese = stringa_html('mese');
$anno = stringa_html('anno');
$data = $anno . "-" . $mese . "-" . $gio;

$idclasse = stringa_html('cl');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con)." " .inspref($query,false));


$query = "SELECT idalunno AS al FROM tbl_alunni WHERE idalunno in (".estrai_alunni_classe_data($idclasse,$data,$con).")";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));


while ($id = mysqli_fetch_array($ris))
{
    // $idal = stringa_html('usc'.$id['al'])?"on":"off";
    /*$cambiamento=false;
    $idalunno = $id['al'];
    $numeroore = stringa_html('numeroore' . $id['al']);
    $orauscita = stringa_html('orauscita' . $id['al']);
    $query = 'SELECT * FROM tbl_usciteanticipate WHERE idalunno=' . $id['al'] . ' AND data="' . $data . '"';
    $rissel = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con)." " .inspref($query,false));
    if (mysqli_num_rows($rissel) > 0)
    {
        $query = 'DELETE FROM tbl_usciteanticipate WHERE idalunno=' . $id['al'] . ' AND data="' . $data . '"';
        $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con)." " .inspref($query,false));
        $cambiamento=true;
    }
    if ($numeroore != 0 | checktime($orauscita))
    {
        $query = "insert into tbl_usciteanticipate(idalunno,data,orauscita,numeroore) values('$idalunno','$data','$orauscita','$numeroore')";
        $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con)." " .inspref($query,false));
        $cambiamento=true;

    }
    else
    {
        if (!checktime($orauscita) & $orauscita != "")
        {
            print "<br><center>Controllare orario per " . decodifica_alunno($id['al'], $con) . "</center>";
        }
    }*/
    $uscitapresente=false;
    $cambiamento=false;
    $idalunno = $id['al'];
    $numeroore = stringa_html('numeroore' . $id['al']);
    $orauscita = stringa_html('orauscita' . $id['al']);
    $query = 'SELECT * FROM tbl_usciteanticipate WHERE idalunno=' . $id['al'] . ' AND data="' . $data . '"';
    $rissel = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con)." " .inspref($query,false));
    if (mysqli_num_rows($rissel) > 0)
    {
        $uscitapresente=true;
        $rec=mysqli_fetch_array($rissel);
        $iduscita=$rec['iduscita'];
    }
    if ($numeroore != 0 | checktime($orauscita))
    {
        if (!$uscitapresente)
        {
            if ($giustificauscite=='yes')  // Se non viene gestita la giustifica si imposta automaticamente a true
                $query = "insert into tbl_usciteanticipate(idalunno,data,orauscita,numeroore,giustifica) values('$idalunno','$data','$orauscita','$numeroore',false)";
            else
                $query = "insert into tbl_usciteanticipate(idalunno,data,orauscita,numeroore,giustifica) values('$idalunno','$data','$orauscita','$numeroore',true)";
            $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query inserimento: " . mysqli_error($con) . " " . inspref($query, false));
            $query = "delete from tbl_assenze where idalunno='$idalunno' and data='$data'";
            $ris3 = mysqli_query($con, inspref($query)) or die ("Errore nella query canc. ass.: " . mysqli_error($con) . " " . inspref($query, false));
        }
        else
        {
            $query = "update tbl_usciteanticipate set orauscita='$orauscita', numeroore='$numeroore' where iduscita=$iduscita";
            mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query,false));
        }
        $cambiamento=true;


    }
    else
    {
        if (!checktime($orauscita) & $orauscita != "")
        {
            print "<br><center>Controllare orario per " . decodifica_alunno($id['al'], $con) . "</center>";
        }
        if ($uscitapresente)
        {
            $query = "DELETE FROM tbl_usciteanticipate WHERE iduscita=$iduscita";
            $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con)." " .inspref($query,false));
            $cambiamento=true;
        }
    }
    if ($cambiamento)
    {
        //ricalcola_uscite($con, $idalunno, $data, $data);
        elimina_assenze_lezione($con, $idalunno, $data);
        inserisci_assenze_per_ritardi_uscite($con, $idalunno, $data);
    }

   // ricalcola_assenze($con,$idalunno,$data,$data);
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
        <form method='post' id='formusc' action='../regclasse/$pr'>
        <input type='hidden' name='gio' value='$gi'>
        <input type='hidden' name='meseanno' value='$ma'>
        <input type='hidden' name='idclasse' value='$cl'>
        </form>
        <SCRIPT language='JavaScript'>
        {
           document.getElementById('formusc').submit();
        }
        </SCRIPT>";
}
else
{

    echo '
           <p align="center">
           <font size=4 color="black">I dati sono stati inseriti correttamente</font>
         ';


    //  codice per richiamare il form delle uscite;
    print ('
   <form method="post" action="usc.php">
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
    if (substr($ora, 3, 2) < "00" | substr($ora, 3, 2) > "59")
    {
        return false;
    }
    if (substr($ora, 2, 1) != ":")
    {
        return false;
    }
    return $contr;
}

