<?php

require_once '../lib/req_apertura_sessione.php';
/*
  Copyright (C) 2015 Pietro Tamburrano
  Copyright (C) 2013 Pietro Tamburrano, Vittorio Lo Mele
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma è distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");


$tipoutente = $_SESSION["tipoutente"];
$userid = $_SESSION["userid"]; //prende la variabile presente nella sessione
$idutente = $_SESSION['idutente'];
// print "USERID $idutente";
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Cambiamento password";
$script = "";
stampa_head_new($titolo, "", $script, "TDSPAML");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

/* Programma per il cambiamento password. */

//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

if (!$con)
{
//imposta la tabella del titolo
    print("<table border='0' width='100%'>
		<tr>
		   <td align ='center'><strong><font size='+1'> Connessione fallita </font></strong></td>
		</tr>
		</table> <br/><br/>");

    print("\n<h1> Connessione al server fallita </h1>");
    exit;
}

//Connessione al database
$DB = true;
if (!$DB)
{
    print "NOME DATABASE:" . $db_nome;
    print("\n<h1> Connessione al database fallita </h1>");
    exit;
}

$ute = stringa_html('ute');
$pwd = stringa_html('password');

$npass = stringa_html('npass');
$rnpass = stringa_html('rnpass');


//Esecuzione query
$sql = "SELECT * FROM tbl_utenti WHERE userid='" . $ute . "' AND  password=md5('" . $pwd . "')";

$result = eseguiQuery($con, $sql);
if (mysqli_num_rows($result) <= 0)
{
    alert("Nome utente e password non risultano presenti: verificare.", "", "warning", "question-octagon");
} else
{
    $rec = mysqli_fetch_array($result);
    $passwordprecedenti = $rec['passprecedenti'];
    if ($npass != $rnpass | $npass == $pwd)
    {
        alert("La password inserita è diversa dalla conferma o coincide con la vecchia password!", "", "danger", "exclamation-octagon");
    } else
    {
        $penultimapassword = substr($passwordprecedenti, strlen($passwordprecedenti) - 33, 32);
        if ($penultimapassword == md5(md5($npass))) // CONTROLLO CHE NON SIA STATA USATA DI RECENTE
        {
            alert("La password inserita è stata usata di recente!", "", "danger", "exclamation-octagon");
        } else
        {
            $query = "UPDATE tbl_utenti SET password = '" . md5(md5($npass)) . "',passprecedenti=concat(passprecedenti,md5('" . $pwd . "'),'|') WHERE userid='" . $ute . "'";

            $result = eseguiQuery($con, $query);

            if (mysqli_affected_rows($con) == 1)
            {
                if ($tipoutente == 'L' & $_SESSION['tokenservizimoodle'] != '')
                {
                    $idmoodle = getIdMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $ute);
                    cambiaPasswordMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $idmoodle, $ute, $npass);
                    alert("Password cambiata correttamente anche per l'elearning per utente $ute ($idmoodle)", "", "success");
                } else if (($tipoutente == 'D' | $tipoutente == 'S') & $_SESSION['tokenservizimoodle'] != '')
                {
                    $ndocente = $idutente - 1000000000;
                    $ute = "doc" . $_SESSION['suffisso'] . $ndocente;
                    $idmoodle = getIdMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $ute);
                    cambiaPasswordMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $idmoodle, $ute, $npass);
                    alert("Password cambiata correttamente anche per l'elearning per utente $ute ($idmoodle)", "", "success");
                } else
                alert("Password cambiata correttamente", "", "success");
            }
            else
            {
                alert("Errore nel database, contattare il sistemista", "", "danger", "exclamation-octagon");
            }
        }
    }
}
mysqli_close($con);
stampa_piede_new("");

