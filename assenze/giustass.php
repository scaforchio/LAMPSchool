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


$titolo = "Inserimento giustificazioni assenze";
$script = "<script type='text/javascript'>
 <!--
  var stile = 'top=10, left=10, width=600, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
     function Popup(apri) {
        window.open(apri, '', stile);
     }
 //-->
</script>";

stampa_head($titolo, "", $script,"SPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

// stampa_head($titolo,"",$script);

$idclasse = stringa_html('idclasse');
$data = stringa_html('data');


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$query = "SELECT idalunno FROM tbl_alunni WHERE idalunno IN (" . estrai_alunni_classe_data($idclasse, $data, $con) . ")  ORDER BY cognome, nome, datanascita";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

while ($recalu = mysqli_fetch_array($ris))
{
    $idalunno = $recalu['idalunno'];
    $query = 'SELECT * FROM tbl_assenze WHERE idalunno="' . $idalunno . '" AND NOT giustifica ORDER BY data ';
    $risass = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    if (mysqli_num_rows($risass) > 0)
    {
        while ($val = mysqli_fetch_array($risass))
        {
            $idgiu = stringa_html('giu' . $val['idassenza']) ? "on" : "off";
            if ($idgiu == "on")
            {
                $query = "UPDATE tbl_assenze SET giustifica=1, datagiustifica='" . $data . "', iddocentegiust=" . $_SESSION['idutente'] . " WHERE idassenza=" . $val['idassenza'] . "";
                mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            }
        }
    }
    $giorno = estraigiorno($data);
    $meseanno = estraimese($data) . ' - ' . estraianno($data);

}


$query = "SELECT idalunno FROM tbl_alunni WHERE idalunno IN (" . estrai_alunni_classe_data($idclasse, $data, $con) . ")  ORDER BY cognome, nome, datanascita";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

while ($recalu = mysqli_fetch_array($ris))
{
    $idalunno = $recalu['idalunno'];
    $query = 'SELECT * FROM tbl_ritardi WHERE idalunno="' . $idalunno . '" AND NOT giustifica ORDER BY data ';

    $risass = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    if (mysqli_num_rows($risass) > 0)
    {

        while ($val = mysqli_fetch_array($risass))
        {
            $idgiu = stringa_html('giurit' . $val['idritardo']) ? "on" : "off";

            if ($idgiu == "on")
            {
                $query = "UPDATE tbl_ritardi SET giustifica=1, datagiustifica='" . $data . "', iddocentegiust=" . $_SESSION['idutente'] . " WHERE idritardo=" . $val['idritardo'] . "";
                // die ("$query");
                mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
            }
        }
    }
    $giorno = estraigiorno($data);
    $meseanno = estraimese($data) . ' - ' . estraianno($data);

}


if ($giustificauscite=='yes')
{
    $query = "SELECT idalunno FROM tbl_alunni WHERE idalunno IN (" . estrai_alunni_classe_data($idclasse, $data, $con) . ")  ORDER BY cognome, nome, datanascita";
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

    while ($recalu = mysqli_fetch_array($ris))
    {
        $idalunno = $recalu['idalunno'];
        $query = 'SELECT * FROM tbl_usciteanticipate WHERE idalunno="' . $idalunno . '" AND NOT giustifica ORDER BY data ';

        $risass = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        if (mysqli_num_rows($risass) > 0)
        {

            while ($val = mysqli_fetch_array($risass))
            {
                $idgiu = stringa_html('giuusc' . $val['iduscita']) ? "on" : "off";

                if ($idgiu == "on")
                {
                    $query = "UPDATE tbl_usciteanticipate SET giustifica=1, datagiustifica='" . $data . "', iddocentegiust=" . $_SESSION['idutente'] . " WHERE iduscita=" . $val['iduscita'] . "";
                    // die ("$query");
                    mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
                }
            }
        }
        $giorno = estraigiorno($data);
        $meseanno = estraimese($data) . ' - ' . estraianno($data);

    }
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
			  <form method='post' id='formlez' action='../regclasse/$pr'>
			  <input type='hidden' name='gio' value='$gi'>
			  <input type='hidden' name='meseanno' value='$ma'>
			  <input type='hidden' name='idclasse' value='$cl'>
			  </form>
			  <SCRIPT language='JavaScript'>
			  {
				  document.getElementById('formlez').submit();
			  }
			  </SCRIPT>
         ";

}

// fine if
stampa_piede("");
mysqli_close($con);


