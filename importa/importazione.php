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

/*Programma per la visualizzazione del menu principale.*/

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");


// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idesterno = "";
if ($tipoutente == "")
{
    header("location: login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$oldserver = stringa_html('oldserver');
$olddb = stringa_html('olddb');
$olduser = stringa_html('olduser');
$oldpwd = stringa_html('oldpwd');
$oldpref = stringa_html('oldpref');

$titolo = "IMPORT DATI";
$script = "";
stampa_head($titolo, "", $script, "M");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

// SVUOTO LA CARTELLA BUFFER DAI VECCHI FILE SQL E CSV
//  svuota_cartella("$cartellabuffer/", ".sql");
// svuota_cartella("$cartellabuffer/", ".csv");
// svuota_cartella("$cartellabuffer/", ".txt");

$arrbkp = is_stringa_html('tabbkp') ? stringa_html('tabbkp') : array();
$tabelle = array();
foreach ($arrbkp as $ambito)
{
    if ($ambito == 'ana')
    {
        $tabelle[] = 'tbl_docenti';
      //  $tabelle[] = 'tbl_tutori';
        $tabelle[] = 'tbl_alunni';
        $tabelle[] = 'tbl_amministrativi';
        $tabelle[] = 'tbl_utenti';

    }
    if ($ambito == 'pro')
    {
        $tabelle[] = 'tbl_abildoc';
        $tabelle[] = 'tbl_abilscol';
        $tabelle[] = 'tbl_competdoc';
        $tabelle[] = 'tbl_competscol';
        $tabelle[] = 'tbl_compob';
        $tabelle[] = 'tbl_compsubob';

    }

    if ($ambito == 'tab')
    {
        $tabelle[] = 'tbl_classi';
        $tabelle[] = 'tbl_sezioni';
        $tabelle[] = 'tbl_specializzazioni';
        $tabelle[] = 'tbl_esiti';
        $tabelle[] = 'tbl_tipiesiti';
        $tabelle[] = 'tbl_materie';
        $tabelle[] = 'tbl_testi';
        $tabelle[] = 'tbl_festivita';
        $tabelle[] = 'tbl_orario';
        $tabelle[] = 'tbl_tipidocumenti';
    }
    if ($ambito == 'par')
    {
        $tabelle[] = 'tbl_parametri';
    }

}


// APERTURA CONNESSIONI AI DUE DATABASE
if ($oldserver == "") $oldserver = $db_server;
if ($olduser == "") $olduser = $db_user;
if ($oldpwd == "") $oldpwd = $db_password;
if ($olddb == "") $olddb = $db_nome;
$conold = mysqli_connect($oldserver, $olduser, $oldpwd, $olddb) or die ("Errore durante la connessione al database origine: " . mysqli_error($con));
// $conold = mysqli_connect($db_server, $db_user, $db_password, $olddb) or die ("Errore durante la connessione al database origine: " . mysqli_error($con));
$connew = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione al database destinazione: " . mysqli_error($con));
// Per evitare che l'inserimento di chiavi a zero vengano reimpostate a 1
mysqli_query($connew, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'") or die("Errore impostazione NO_AUTO_VALUE_ON_ZERO");


/*
 * Importazione tabelle
 */


$tabs = array();


// CANCELLO TUTTI I DATI PRESENTI NELLE TABELLE
foreach ($tabelle as $tab)
{


    $tabold = $oldpref . $tab;
    $tabnew = inspref($tab);
    // Svuoto nuova tabella
    if ($tab != 'tbl_utenti' & $tab != 'tbl_parametri')
    {
        $sqldrop = "delete from $tabnew where 1=1";
        mysqli_query($connew, $sqldrop) or die("Errore:" . $sqldrop);
    }
    else
    {
        if ($tab == 'tbl_utenti')
        {
            $sqldrop = "delete from $tabnew where idutente>0";
            mysqli_query($connew, $sqldrop) or die("Errore:" . $sqldrop);
        }
    }


}

// INSERISCO I DATI DALLE TABELLE DEL VECCHIO ANNO

$tabelle = array_reverse($tabelle);   // L'array reverse è necessario per problemi di chiavi esterne

foreach ($tabelle as $tab)
{
    if ($tab != 'tbl_parametri')
    {
        $tabold = $oldpref . $tab;

        $tabnew = inspref($tab);
        // Estraggo i valori dalla tabella
        if ($tab != 'tbl_utenti')
        {
            $sqlestrai = "select * from $tabold";
        }
        else
        {
            $sqlestrai = "select * from $tabold where idutente>0";
        }
        $resestrai = mysqli_query($conold, $sqlestrai) or die("Errore:" . $sqlestrai);


        $queryinserisci = "insert into $tabnew(";

        $finfo = mysqli_fetch_fields($resestrai);

        $campo = "";

        foreach ($finfo as $val)
        {
            $campo = $val->name;
            $queryinserisci = $queryinserisci . $campo . ",";
        }
        // elimino ultima virgola
        $queryinserisci = substr($queryinserisci, 0, strlen($queryinserisci) - 1);
        $queryinserisci = $queryinserisci . ") values (";
        while ($rec = mysqli_fetch_array($resestrai))
        {
            $queryins = $queryinserisci;
            foreach ($finfo as $val)
            {
                $campo = $val->name;
                $queryins = $queryins . "'" . elimina_apici($rec[$campo]) . "',";
            }
            $queryins = substr($queryins, 0, strlen($queryins) - 1);
            $queryins = $queryins . ")";

            mysqli_query($connew, $queryins) or die("Errore:" . $queryins);

        }
        if ($tab == 'tbl_classi')
        {
            $queryduplclassi = "CREATE TABLE IF NOT EXISTS tbl_classiold LIKE tbl_classi";
            mysqli_query($connew, inspref($queryduplclassi)) or die("Errore:" . inspref($queryduplclassi));
            $queryduplclassi = "TRUNCATE tbl_classiold";
            mysqli_query($connew, inspref($queryduplclassi)) or die("Errore:" . inspref($queryduplclassi));
            $queryduplclassi = "INSERT INTO tbl_classiold SELECT * FROM tbl_classi";
            mysqli_query($connew, inspref($queryduplclassi)) or die("Errore:" . inspref($queryduplclassi));
        }
        print "<center><big>Importazione tabella $tab</big></center><br>";
    }
    else
    {
        $elenco = "'versioneprecedente','annoscol','fineprimo','finesecondo','datafinelezioni','datainiziolezioni','nomefilelog','sola_lettura'";
        $queryestraipar = "select * from $oldpref" . "tbl_parametri where parametro not in ($elenco)";
        $rispar = mysqli_query($conold, $queryestraipar);
        while ($recoldpar = mysqli_fetch_array($rispar))
        {
            $nomeparametro = $recoldpar['parametro'];
            $aggiornaparametro = "UPDATE tbl_parametri SET valore='" . $recoldpar['valore'] . "' WHERE parametro='" . $recoldpar['parametro'] . "'";
            mysqli_query($connew, inspref($aggiornaparametro));
        }
        print "<center><big>Importazione PARAMETRI</big></center><br>";
    }
    // IMPORTAZIONE PARAMETRI

}


echo "<br><center><form action='../login/ele_ges.php'><input type='submit' value='OK'></form></center><br>";

stampa_piede("");
