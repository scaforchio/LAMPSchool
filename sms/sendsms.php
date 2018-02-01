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
@require_once("../lib/sms/php-send.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$titolo = "Invio SMS";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$dataoggi = date('d/m/Y');

/*
 * 
 * INVIO GLI SMS PER LE ASSENZE
 */


$destinatari = array();

$query = "SELECT idalunno,cognome, nome, telcel
        FROM tbl_alunni";

$ris = mysqli_query($con, inspref($query));
$invio = false;
$iddest = array();
$contasmsass = 0;
while ($rec = mysqli_fetch_array($ris))
{
    $stralu = "ass" . $rec['idalunno'];

    $aludainv = stringa_html($stralu);

    if ($aludainv == "on")
    {

        $dest = array();
        $destinatarialunno = array();
        $destinatarialunno = explode(",", $rec['telcel']);
        foreach ($destinatarialunno as $destalu)
        {
            $dest['recipient'] = "39" . trim($destalu); // .$rec['telcel'];
            $dest['nome'] = $rec['nome'] . " " . $rec['cognome'];
            $iddest[] = $rec['idalunno'];

            $destinatari[] = $dest;
            $contasmsass++;
            $invio = true;
        }
    }

}

if ($invio)
{
    $messaggio = '${nome} risulta assente oggi ' . $dataoggi;
    $result = skebbyGatewaySendSMSParam($utentesms, $passsms, $destinatari, $messaggio, SMS_TYPE_CLASSIC_PLUS, '', $testatasms, $_SESSION['suffisso']);
    if ($result['status']== "success")
    {
        $query = "insert into tbl_testisms(testo, idinvio, idutente)
				  values ('$messaggio','" . $result['id'] . "','" . $_SESSION['idutente'] . "')";
        mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
        $idtestosms = mysqli_insert_id($con);
        for ($i = 0; $i < count($destinatari); $i++)
        {
            $query = "insert into tbl_sms(tipo,iddestinatario,idinvio,celldestinatario, idtestosms)
					  values ('ass'," . $iddest[$i] . ",'" . $result['id'] . "','" . $destinatari[$i]['recipient'] . "',$idtestosms)";
            mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
        }
        print "<br><br><center><b><font color='green'>$contasmsass SMS assenze correttamente inviati!</font></b>";

    }
    else
    {
        print "<br><br><center><b><font color='red'>Problemi con l'invio degli SMS per le assenze!</font></b>";
        foreach ($result as $codice => $ris)
        {
            print $codice . "=>" . $ris . "<br>";
        }
    }
}

//
// 
// INVIO GLI SMS PER I RITARDI
// 
// 


$destinatari = array();
$iddest = array();
$query = "SELECT idalunno,cognome, nome, telcel
        FROM tbl_alunni";

$ris = mysqli_query($con, inspref($query));
$testosms = stringa_html('testosms');
$contasmsrit = 0;
$invio = false;
while ($rec = mysqli_fetch_array($ris))
{
    $stralu = "rit" . $rec['idalunno'];

    $aludainv = stringa_html($stralu);

    if ($aludainv == "on")
    {
        $dest = array();
        $destinatarialunno = array();
        $destinatarialunno = explode(",", $rec['telcel']);
        foreach ($destinatarialunno as $destalu)
        {
            $dest['recipient'] = "39" . trim($destalu); // .$rec['telcel'];
            $dest['nome'] = $rec['nome'] . " " . $rec['cognome'];
            $iddest[] = $rec['idalunno'];

            $destinatari[] = $dest;
            $contasmsrit++;
            $invio = true;
        }
    }

}

if ($invio)
{
    $messaggio = '${nome} risulta in ritardo oggi ' . $dataoggi;

    $result = skebbyGatewaySendSMSParam($utentesms, $passsms, $destinatari, $messaggio, SMS_TYPE_CLASSIC_PLUS, '', 'ISIS LDM', $_SESSION['suffisso']);

    if ($result['status'] == "success")
    {
        $query = "insert into tbl_testisms(testo, idinvio, idutente)
				  values ('$messaggio','" . $result['id'] . "','" . $_SESSION['idutente'] . "')";
        mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
        $idtestosms = mysqli_insert_id($con);
        for ($i = 0; $i < count($destinatari); $i++)
        {
            $query = "insert into tbl_sms(tipo,iddestinatario,idinvio,celldestinatario, idtestosms)
					  values ('rit'," . $iddest[$i] . ",'" . $result['id'] . "','" . $destinatari[$i]['recipient'] . "',$idtestosms)";
            mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
        }
        print "<br><br><center><b><font color='green'>$contasmsrit SMS ritardi correttamente inviati!</font></b>";

    }
    else
    {
        print "<br><br><center><b><font color='red'>Problemi con l'invio degli SMS per i ritardi!</font></b>";
        foreach ($result as $codice => $ris)
        {
            print $codice . "=>" . $ris . "<br>";
        }
    }
}


//
//
// INVIO GLI SMS PER COMUNICAZIONE NUMERO RITARDI
//
//


$destinatari = array();
$iddest = array();
$query = "SELECT idalunno,cognome, nome, telcel
        FROM tbl_alunni";

$ris = mysqli_query($con, inspref($query));
$testosms = stringa_html('testosms');
$contasmsrit = 0;
$invio = false;
while ($rec = mysqli_fetch_array($ris))
{
    $stralu = "numrit" . $rec['idalunno'];

    $aludainv = stringa_html($stralu);

    if ($aludainv == "on")
    {
        $dest = array();
        $destinatarialunno = array();
        $destinatarialunno = explode(",", $rec['telcel']);
        foreach ($destinatarialunno as $destalu)
        {
            $dest['recipient'] = "39" . trim($destalu); // .$rec['telcel'];
            $dest['nome'] = $rec['nome'] . " " . $rec['cognome'];
            $iddest[] = $rec['idalunno'];

            $destinatari[] = $dest;
            $contasmsrit++;
            $invio = true;
        }
    }

}

if ($invio)
{
    $messaggio = '${nome} ha superato il numero dei ritardi. Al prossimo ritardo deve essere accompagnato da un genitore.';

    $result = skebbyGatewaySendSMSParam($utentesms, $passsms, $destinatari, $messaggio, SMS_TYPE_CLASSIC_PLUS, '', 'ISIS LDM', $_SESSION['suffisso']);

    if ($result['status'] == "success")
    {
        $query = "insert into tbl_testisms(testo, idinvio, idutente)
				  values ('$messaggio','" . $result['id'] . "','" . $_SESSION['idutente'] . "')";
        mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
        $idtestosms = mysqli_insert_id($con);
        for ($i = 0; $i < count($destinatari); $i++)
        {
            $query = "insert into tbl_sms(tipo,iddestinatario,idinvio,celldestinatario, idtestosms)
					  values ('rit'," . $iddest[$i] . ",'" . $result['id'] . "','" . $destinatari[$i]['recipient'] . "',$idtestosms)";
            mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
        }
        print "<br><br><center><b><font color='green'>$contasmsrit SMS ritardi correttamente inviati!</font></b>";

    }
    else
    {
        print "<br><br><center><b><font color='red'>Problemi con l'invio degli SMS per comunicazione ritardi!</font></b>";
        foreach ($result as $codice => $ris)
        {
            print $codice . "=>" . $ris . "<br>";
        }
    }
}

stampa_piede("");
mysqli_close($con);



