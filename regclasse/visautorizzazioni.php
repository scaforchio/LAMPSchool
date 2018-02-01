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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
// require_once '../lib/ db / query.php';

//$lQuery = LQuery::getIstanza();

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$idclasse = stringa_html('idclasse');


$titolo = "Situazione deroghe e autorizzazioni";
$script = "";
stampa_head($titolo, "", $script, "MSPD");


print "<center><B>".decodifica_classe($idclasse,$con)."</B><br></center>";
// prelevamento dati alunno
// $rs = $lQuery->selectstar('tbl_alunni', 'idalunno=?', array($codalunno));
$query = "select * from tbl_alunni where idclasse=$idclasse and not autorizzazioni is null
and (autorizzazioni <> '' or autentrata<>'' or autuscita<>'') order by cognome, nome, datanascita";
$rs = mysqli_query($con, inspref($query));
$esistono=false;
while ($rec = mysqli_fetch_array($rs))
{
    print "<BR><b><i>".$rec['cognome'] . " " . $rec['nome'] . " (" . data_italiana($rec['datanascita']) . ")</b></i><br>";
   // print "<pre>".$rec['autorizzazioni']."</pre><br>";
    print "".nl2br($rec['autorizzazioni'])."<br>".$rec['autuscita']."<br>".$rec['autentrata'];
    print "<br>";
    $esistono=true;
}
if (!$esistono)
    print "<BR><br><b><i><center>Niente da visualizzare!</b></i></center>";
mysqli_close($con);

