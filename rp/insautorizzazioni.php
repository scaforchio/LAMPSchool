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
//@require_once("../lib/sms/php-send.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$insrit = stringa_html('insrit');

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


$titolo = "Inserimento autorizzazioni entrata in ritardo";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='autorizzaritardo.php'>Autorizza ritardi</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$dest = array();

$destinatari = array();



$pos = 0;
$query = "SELECT idalunno
          FROM tbl_alunni where idclasse<>'0'";
//print inspref($query);
$ris = eseguiQuery($con, $query);
while ($rec = mysqli_fetch_array($ris))
{

    $stralu = "aut" . $rec['idalunno'];
    $strgiu = "giu" . $rec['idalunno'];
    $idalunno = $rec['idalunno'];
    $strritardo = "idritardo" . $idalunno;
    $idritardo = stringa_html($strritardo);
    $aludainv = stringa_html($stralu);
    $aludagiust = stringa_html($strgiu);
    $oraentrata = stringa_html("oraentrata" . $rec['idalunno']);
    // print ($stralu);
    if ($aludainv == "on")
    {
        $oraentrata = stringa_html("oraentrata" . $rec['idalunno']);

        if ($aludagiust == "on")
            $query = "update tbl_ritardi set autorizzato=true,oraentrata='$oraentrata',giustifica=true,
                      iddocentegiust=" . $_SESSION['idutente'] . ",datagiustifica='" . date('Y-m-d') . "'
                  where idritardo=$idritardo";
        else
            $query = "update tbl_ritardi set autorizzato=true,oraentrata='$oraentrata',giustifica=false
                  where idritardo=$idritardo";


        eseguiQuery($con, $query);
    }
}

print ("
         <form method='post' id='formscr' action='autorizzaritardo.php'>

        </form>
        <SCRIPT language='JavaScript'>
           document.getElementById('formscr').submit();
        </SCRIPT>");

stampa_piede("");
mysqli_close($con);



