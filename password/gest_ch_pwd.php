<?php

session_start();
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
////session_start();
$tipoutente = $_SESSION["tipoutente"];
$userid = $_SESSION["userid"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

/* Programma per la conferma del login. */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

$titolo = "Cambiamento password";
$script = "";
stampa_head($titolo, "", $script, "SDMAPL");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$ute = stringa_html('utente');
$pas = stringa_html('npass');
$pas2 = stringa_html('rnpass');

//print(" ute $ute pas $pas pas2 $pas2");	   
//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    //imposta la tabella del titolo
    print("<table border=0 width='100%'>
		<tr>
		   <td align ='center' ><strong><font size='+1'> Connessione fallita </font></strong></td>
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

//Esecuzione query
$sql = "select * from tbl_utenti where userid='$ute'";
// print inspref($sql);
$result = eseguiQuery($con, $sql);

if (mysqli_num_rows($result) <= 0)
{
    print ("L'utente non risulta presente: verificare.");
    print "<center> <form action='../login/ele_ges.php' method='POST'>";
    print "<input type='submit' name='Home' value='HOME'>";
    print "</form> </center>";
} else
{
    if ($pas != $pas2)
    {
        print ("Le password inserite sono diverse tra loro!");
        print "<center> <form action='../login/ele_ges.php' method='POST'>";
        print "<input type='submit' name='Home' value='HOME'>";
        print "</form> </center>";
    } else
    {
        // $query="update tbl_utenti set password = md5('$pas') where userid='$ute'";
        print "PASSWORD $pas";
        $query = "update tbl_utenti set passprecedenti=concat(passprecedenti,password,'|'),password = md5(md5('" . $pas . "')) where userid='" . $ute . "'";
        // print $query;
        $result = eseguiQuery($con, $query);
        if (mysqli_affected_rows($con) == 1)
        {
            print ("<center><br/>Password cambiata correttamente.<br/></center>");
            if ($_SESSION['tokenservizimoodle'] != '' & substr($ute, 0, 2) == 'al')
            {
                $idmoodle = getIdMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $ute);
                print "IDMOODLE $idmoodle";
                cambiaPasswordMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $idmoodle, $ute, $pas);
            }
        } else
        {
            print ("<center><br/>Password uguale a quella presente!<br/></center>");
        }
    }
}
mysqli_close($con);
stampa_piede("");


