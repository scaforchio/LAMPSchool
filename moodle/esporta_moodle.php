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
$idclasse = 0;
$idclasse = stringa_html('idclasse');

$idalu = stringa_html('idalu');

$titolo = "Esportazione dati per Moodle";
$script = "";


stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> -  $titolo", "", "$nome_scuola", "$comune_scuola");

$annoscolastico = $annoscol . "/" . ($annoscol + 1);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

print "<center><b>Preparazione file per esportazione Moodle</b></center><br/><br/>";
//	print "<form target='_blank' name='stampa' action='../alunni/stampa_pass_alu.php' method='POST'>";
//	print "<table align='center' border='1'><tr><td><b>Classe</b></td><td><b>Alunno</b></td><td><b>Utente</b></td><td><b>Password</b></td></tr>";

// ESPORTAZIONE ALUNNI CON GRUPPI GLOBALI CLASSI
// Per la creazione dei gruppi globali in Moodle
// procedere con delle insert in mdl_cohorts
// Esempio (valido in Moodle 2.6):
//
/* INSERT INTO `mdl_cohort` (`id`, `contextid`, `name`, `idnumber`, `description`, `descriptionformat`, `component`, `timecreated`, `timemodified`) VALUES
(1, 1, 'itt_1_a_bio', 'itt_1_a_bio', '', 1, '', 1410620693, 1410676971),
(2, 1, 'itt_1_b_bio', 'itt_1_b_bio', '', 1, '', 1410620693, 1410620693),
(3, 1, 'itt_1_c_bio', 'itt_1_c_bio', '', 1, '', 1410620693, 1410620693),
(4, 1, 'itt_1_d_bio', 'itt_1_d_bio', '', 1, '', 1410620693, 1410620693),
(5, 1, 'itt_1_a_ele', 'itt_1_a_ele', '', 1, '', 1410620693, 1410620693),
(6, 1, 'itt_1_b_ele', 'itt_1_b_ele', '', 1, '', 1410620693, 1410620693),
(7, 1, 'itt_1_a_inf', 'itt_1_a_inf', '', 1, '', 1410620693, 1410620693),
(8, 1, 'itt_1_b_inf', 'itt_1_b_inf', '', 1, '', 1410620693, 1410620693),
(9, 1, 'itt_1_c_inf', 'itt_1_c_inf', '', 1, '', 1410620693, 1410620693);

I numeri finali sono ricavabili dopo aver inserito da moodle il primo
I dati di name e idnumber sono composti da suffisso_anno_sezione_substr(specializzazione,0,3) tutti sottoposti a lowercase.
 
*/

$query = "SELECT * FROM tbl_classi
	        ORDER BY anno, sezione,specializzazione";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));


$nfgru = "gruppi_" . $annoscol . $_SESSION['suffisso'] . ".txt";
$nomefilegruppi = "$cartellabuffer/" . $nfgru;
$fp = fopen($nomefilegruppi, 'w');

$querygruppi= "INSERT INTO mdl_cohort (contextid, name, idnumber, description, descriptionformat, component, timecreated, timemodified) VALUES ";

while ($val = mysqli_fetch_array($ris))
{
    $gruppo = $_SESSION['suffisso'] . "_" . $val['anno'] . "_" . $val['sezione'] . "_" . substr($val['specializzazione'], 0, 3)."_".$annoscol;
    $gruppo = strtolower($gruppo);
    $datacre = new DateTime();
    $datacreazione= $datacre->getTimestamp();
    $valore = "(1,'$gruppo','$gruppo','',1,'',$datacreazione,$datacreazione),";

   $querygruppi.=$valore;

}
// SOSTITUIRE ULTIMO CARATTERE CON ;
$querygruppi=substr($querygruppi,0,strlen($querygruppi)-1).";";
fwrite($fp,$querygruppi);
fclose($fp);




$query = "SELECT * FROM tbl_alunni,tbl_classi
	        WHERE tbl_alunni.idclasse=tbl_classi.idclasse
	        ORDER BY anno, sezione,specializzazione,cognome,nome";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));


$nf = "alunni_" . $annoscol . $_SESSION['suffisso'] . ".csv";
$nomefilegruppi = "$cartellabuffer/" . $nf;
$fp = fopen($nomefilegruppi, 'w');

fputcsv($fp, array("username", "password", "firstname", "lastname", "email", "cohort1"), ";");
while ($val = mysqli_fetch_array($ris))
{
    $utente = "al" . $_SESSION['suffisso'] . $val['idalunno'];
    $pass = creapassword();
    $nome = $val['nome'];
    $cognome = $val['cognome'];
    $email = $utente . "@idm.it";
    $gruppo = $_SESSION['suffisso'] . "_" . $val['anno'] . "_" . $val['sezione'] . "_" . substr($val['specializzazione'], 0, 3)."_".$annoscol;
    $gruppo = strtolower($gruppo);

    fputcsv($fp, array($utente, $pass, $nome, $cognome, $email, $gruppo), ";");

}
fclose($fp);


//
//  Esportazione docenti
//
$query = "SELECT * FROM tbl_docenti
	        ORDER BY cognome,nome";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));


$nfdoc = "docenti_" . $annoscol . $_SESSION['suffisso'] . ".csv";
$nomefilegruppi = "$cartellabuffer/" . $nfdoc;
$fpdoc = fopen($nomefilegruppi, 'w');

fputcsv($fpdoc, array("username", "password", "firstname", "lastname", "email", "cohort1"), ";");
while ($val = mysqli_fetch_array($ris))
{
    $utente = "doc" . $_SESSION['suffisso'] . ($val['iddocente'] - 1000000000);
    $pass = creapassword();
    $nome = $val['nome'];
    $cognome = $val['cognome'];
    if ($val['email'] == "")
    {
        $email = $utente . "@idm.it";
    }
    else
    {
        $email = $val['email'];
    }
    $gruppo = "docenti_" . substr($_SESSION['suffisso'], 0, 3);
    $gruppo = strtolower($gruppo);

    fputcsv($fpdoc, array($utente, $pass, $nome, $cognome, $email, $gruppo), ";");

}
fclose($fpdoc);


// }
print ("<br/><center>Gruppi globali:<a href='$cartellabuffer/$nfgru'><img src='../immagini/csv.png'></a></center>");
print ("<br/><center>Alunni:<a href='$cartellabuffer/$nf'><img src='../immagini/csv.png'></a></center>");
print ("<br/><center>Docenti:<a href='$cartellabuffer/$nfdoc'><img src='../immagini/csv.png'></a></center>");

stampa_piede("");
mysqli_close($con);



