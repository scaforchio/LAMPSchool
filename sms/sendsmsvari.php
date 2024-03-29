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


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


$titolo = "Invio SMS";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$dest = array();

$destinatari = array();

$query = "select idalunno,cognome, nome, telcel
        from tbl_alunni";

$ris = eseguiQuery($con, $query);
$testosms = stringa_html('testosms');
$pos = 0;
while ($rec = mysqli_fetch_array($ris))
{
    $stralu = "sms" . $rec['idalunno'];

    $aludainv = stringa_html($stralu);

    if ($aludainv == "on")
    {
        $destinatarialunno = array();
        $destinatarialunno = explode(",", $rec['telcel']);
        foreach ($destinatarialunno as $destalu)
        {

            $dest[] = $rec['idalunno'];
            $destinatari[] = "39" . trim($destalu);
        }
    }
    $pos++;
}

$messaggio = str_replace("’", "'", $testosms);


$result = skebbyGatewaySendSMS($_SESSION['utentesms'], $_SESSION['passsms'], $destinatari, $messaggio, SMS_TYPE_CLASSIC_PLUS, '', $_SESSION['testatasms'], $_SESSION['suffisso']);

// print "Risultato invio: $result";


if ($result['status'] == "success")
{
    $query = "insert into tbl_testisms(testo, idinvio, idutente) 
	        values ('$testosms','" . $result['id'] . "','" . $_SESSION['idutente'] . "')";
    eseguiQuery($con, $query);
    $idtestosms = mysqli_insert_id($con);
    for ($i = 0; $i < count($dest); $i++)
    {
        $query = "insert into tbl_sms(tipo,iddestinatario,idinvio,celldestinatario, idtestosms) 
	           values ('alu'," . $dest[$i] . ",'" . $result['id'] . "','" . $destinatari[$i] . "',$idtestosms)";
        eseguiQuery($con, $query);
    }
    print "<br><br><center><b><font color='green'>SMS correttamente inviati!</font></b>";
} else
{
    print "<br><br><center><b><font color='red'>Problemi con l'invio!</font></b><br>";
    foreach ($result as $codice => $ris)
    {
        print $codice . "=>" . $ris . "<br>";
    }
}


stampa_piede("");
mysqli_close($con);



