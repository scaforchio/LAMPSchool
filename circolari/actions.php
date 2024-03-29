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

// istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$iddocente = $_SESSION["idutente"];
$Id = stringa_html('Id');
$Circ = stringa_html('Circ');
$Ute = stringa_html('Ute');


// CONNESSIONE AL DATABASE
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
if ($Circ != "")
{
    $dataoggi = data_to_db(date('d/m/Y'));
//    $querylett = "update tbl_diffusionecircolari
//					set datalettura='$dataoggi'
//					where idcircolare=$Circ
//					and idutente=$Ute
//					and (isnull(datalettura) or datalettura<'2000-01-01')";
//					
    $querylett = "update tbl_diffusionecircolari
					set datalettura='$dataoggi'
					where idcircolare=$Circ
					and idutente=$Ute
					and (isnull(datalettura) or datalettura<'2000-01-01')";
    //  die("tttt".inspref($querylett));
    eseguiQuery($con, $querylett);
}

if (!isset($_GET))
    $_GET = $HTTP_GET_VARS;

if ($_GET["action"] && $_GET["Id"] && is_numeric($_GET["Id"]))
{

    $query = "select docbin, docnome, doctype,docmd5 from tbl_documenti where iddocumento = '$Id'";

    $select = eseguiQuery($con, $query);

    $result = mysqli_fetch_array($select);

    $data = $result["docbin"];
    $name = $result["docnome"];
    $type = $result["doctype"];
    $hashmd5 = $result['docmd5'];

    switch ($_GET["action"])
    {

        case "view" :


            if (strlen($data) > 0)  // Il documento è nel database altrimenti è su disco
            {
                header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
                header("Content-Type: $type");
                header("Content-Disposition: inline; filename=" . $name);
                echo $data;
            } else
            {

                //$cart1=substr($hashmd5,0,2);
                //$cart2=substr($hashmd5,2,2);
                if ($_SESSION['suffisso'] != "")
                    $suff = $_SESSION['suffisso'] . "/";
                else
                    $suff = "";
                $origine = "../lampschooldata/$suff$hashmd5";
                $destinazione = $_SESSION['cartellabuffer']."/$name";
                copy($origine, $destinazione);
                header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
                header("Content-Type: $type");
                header("Content-Disposition: inline; filename=" . $name);
                readfile($destinazione);
            }
            break;


        case "download" :



            if (strlen($data) > 0)   // Il documento è nel database altrimenti è su disco
            {
                header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
                header("Content-Type: $type");
                header("Content-Disposition: attachment; filename=$name");
                echo $data;
            } else
            {
                //$cart1=substr($hashmd5,0,2);
                //$cart2=substr($hashmd5,2,2);
                if ($_SESSION['suffisso'] != "")
                    $suff = $_SESSION['suffisso'] . "/";
                else
                    $suff = "";
                $origine = "../lampschooldata/$suff$hashmd5";
                $destinazione = $_SESSION['cartellabuffer']."/$name";
                // print $origine. " ".$destinazione;
                copy($origine, $destinazione);
                header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
                header("Content-Type: $type");
                header("Content-Disposition: attachment; filename=" . $name);
                readfile($destinazione);
            }
            //}


            break;

        default :

            // DEFAULT CASE, NESSUNA AZIONE

            break;
    } // endswitch
// CHIUDIAMO LA CONNESSIONE
}
mysqli_close($con);
// stampa_piede("");

