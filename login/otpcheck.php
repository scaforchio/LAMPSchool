<?php

session_start();
$_SESSION['tentativiotp'] ++;
if ($_SESSION['tentativiotp'] > 3)
    header("location: login.php?suffisso=" . $_SESSION['suffisso']);
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


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$_SESSION['tokenok'] = false;
if ($tipoutente == "")
{
    header("location: login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
$titolo = "VERIFICA OTP";
$script = "";
stampa_head($titolo, "", $script, "MPDSLAT");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


print "<form action='otpcheck_ok.php' method='POST'>";


if ($_SESSION['tentativiotp'] == 1)
    $messaggio = "";
else if ($_SESSION['tentativiotp'] == 2)
    $messaggio = "Secondo tentativo!";
else if ($_SESSION['tentativiotp'] == 3)
    $messaggio = "Ultimo tentativo!!";

if ($_SESSION['modoinviotoken'] == 'T')
{
    $query="select token from tbl_utenti where idutente=".$_SESSION['idutente'];
    $ris= eseguiQuery($con, $query);
    $rec= mysqli_fetch_array($ris);
    $token=$rec['token'];
    $messaggio.="<br>Inserire le cifre presenti nelle celle <b> ";
    //print $_SESSION['schematoken']; die;
    for ($i = 0; $i < 5; $i++)
    {
        for ($j = 0; $j < 10; $j++)
        {
            if (substr($_SESSION['schematoken'],10*$i+$j,1)==substr($token,$i,1))
            {
                $riga=$i+1;
                $colo=daNumALet($j);
                $messaggio.=" $riga$colo";
            }
        }
    }
    $messaggio.="</b><br><br> ";
}
else if ($_SESSION['modoinviotoken'] == 'G') {
    $query="select token from tbl_utenti where idutente=".$_SESSION['idutente'];
    $ris= eseguiQuery($con, $query);
    $rec= mysqli_fetch_array($ris);
    $token=$rec['token'];
    $messaggio.="<br>Inserire il codice inviato via Telegram. ";
}
else if ($_SESSION['modoinviotoken'] == 'S') {
    $query="select token from tbl_utenti where idutente=".$_SESSION['idutente'];
    $ris= eseguiQuery($con, $query);
    $rec= mysqli_fetch_array($ris);
    $token=$rec['token'];
    $messaggio.="<br>Inserire il codice inviato via SMS. ";
}
else if ($_SESSION['modoinviotoken'] == 'M') {
    $query="select token from tbl_utenti where idutente=".$_SESSION['idutente'];
    $ris= eseguiQuery($con, $query);
    $rec= mysqli_fetch_array($ris);
    $token=$rec['token'];
    $messaggio.="<br>Inserire il codice inviato via EMail. ";
}
print "<CENTER>";
print"<table border='0'>";
print "<br><br>$messaggio<br>";
// print "<tr><td ALIGN='CENTER'><b>".$dati['parametro']."</b></td></tr>";
print "<tr><td ALIGN='CENTER'><b>Codice OTP (5 cifre)</b></td></tr>";
print "<tr><td ALIGN='CENTER'><input type='text' name ='token' size=5 maxlength=5 minlength=5 pattern='[0-9][0-9][0-9][0-9][0-9]'><br><br></td></tr>";
print "<tr><td ALIGN='CENTER'><input type='submit' value='Verifica OTP'></td></tr>";
print "</table></form>";



stampa_piede("");

function daNumALet($col)
{
    if ($col==0) return "A";
    if ($col==1) return "B";
    if ($col==2) return "C";
    if ($col==3) return "D";
    if ($col==4) return "E";
    if ($col==5) return "F";
    if ($col==6) return "G";
    if ($col==7) return "H";
    if ($col==8) return "I";
    if ($col==9) return "L";


}
