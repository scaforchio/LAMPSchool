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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
//require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();
// istruzioni per tornare alla pagina di login se non c'è una sessione valida


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Richiesta astensione dal lavoro - registrazione";
$script = "";
stampa_head($titolo, "", $script, "MSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$nominativo = estrai_dati_docente($_SESSION['idutente'], $con);

/*
$cooldown_query = "SELECT COUNT(*) FROM `tbl_richiesteferie` WHERE `iddocente` = " . $_SESSION['idutente'] . " AND `oraultmod` > DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
$cooldown_res = eseguiQuery($con, $cooldown_query);
$cooldown = mysqli_fetch_array($cooldown_res, MYSQLI_NUM);
if($cooldown[0] > 0){
    print "Errore!<br>Puoi inviare solo una richiesta ogni 5 minuti! <br><br>";
    mysqli_close($con);
    stampa_piede("");
    die;
}
*/
$to = $_SESSION['indirizzomailassenze'];

// NON USATA LA FUNZIONE stringa_html per evitare modifica dei tag html 
$subject = $_POST['subject'];
$testomail = $_POST['testomail'];
$numerogiorni = stringa_html('numerogiorni');
$orepermessobreve = stringa_html('orepermessobreve');
if ($numerogiorni == '')
    $numerogiorni = 0;
if ($orepermessobreve == '')
    $orepermessobreve = 0;
$query = "insert into tbl_richiesteferie(iddocente, subject, testomail,numerogiorni,orepermessobreve,orariorichiesta) values ('$iddocente','$subject','$testomail',$numerogiorni,$orepermessobreve,'".date('H:i')."')";
eseguiQuery($con, $query);
$idrichiesta = mysqli_insert_id($con);

print "RICHIESTA CORRETTAMENTE REGISTRATA! <br><br>";
print "<big><b>NUMERO RICEVUTA: " . $_SESSION['suffisso'] . $idrichiesta . "</b><small>";

/*
 * 
 * 
 * 
 * La Mail viene inviata solo dopo la conferma del dirigente se non si tratta di malattia
 */
if ((strpos($testomail, "Malattia (") != 0)|($_SESSION['mailpermdopoauto']=='no'))
{
    if (invia_mail($to, $subject, $testomail))
    {
        print "<br>OK! Mail correttamente inviata alla scuola.<br><br>";
        $query = "update tbl_richiesteferie set erroremail=false where idrichiestaferie=$idrichiesta";
        eseguiQuery($con, $query);
       // print "<big><b>NUMERO RICEVUTA: " . $_SESSION['suffisso'] . $idrichiesta . "</b><small>";
    } else
    {
        print "<br>Errore nell'invio della mail!";
        $query = "update tbl_richiesteferie set erroremail=true where idrichiestaferie=$idrichiesta";
        eseguiQuery($con, $query);
        // print "<big><b>NUMERO RICEVUTA: " . $_SESSION['suffisso'] . $idrichiesta . "</b><small>";
    }
    eseguiQuery($con, $query);
}


mysqli_close($con);
stampa_piede("");
