<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
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

//Programma per la visualizzazione dell'elenco delle tbl_classi

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Registrazione nuova entrata posticipata classe";
$script = "";
stampa_head($titolo, "", $script, "MSA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_ritcla.php'>ELENCO CLASSI</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("\n<h1> Connessione al server fallita </h1>");
    exit;
};

//Connessione al database
$DB = true;
if (!$DB)
{
    print("\n<h1> Connessione al database fallita </h1>");
    exit;
};

//Esecuzione controlli
$errore = 0;
$mes = '';
$idclasse = stringa_html('idclasse');
$data = stringa_html('data');
$ora = stringa_html('ora');


//Esecuzione query finale
$sql = "INSERT INTO tbl_entrateclassi (idclasse, data, ora) VALUES ";
$sql .= "('$idclasse','".data_to_db($data)."','$ora')";

if (!($ris = mysqli_query($con, inspref($sql))))
{
    print("\n<FONT SIZE='+2'> <CENTER>Inserimento non eseguito </CENTER></FONT>");
}
else
{
    // print("\n<FONT SIZE='+2'> <CENTER>Inserimento eseguito</CENTER> </FONT>");
    print "
                 <form method='post' id='formdoc' action='./vis_ritcla.php'>
                 
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formdoc').submit();
                 }
                 </SCRIPT>";
}



stampa_piede("");
mysqli_close($con);

function displayFormIndietro()
{
    print "<CENTER><FORM ACTION='nuo_ritcla.php' method='POST'>";
    print "<INPUT TYPE='SUBMIT' VALUE='<< Indietro'>";
    print "</FORM></CENTER>";
}


