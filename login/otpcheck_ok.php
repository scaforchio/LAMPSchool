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


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$tokeninserito= stringa_html('token');
//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    die("\n<h1> Connessione al server fallita </h1>");
}
//Verifico il token
$query="select token from tbl_utenti where idutente=".$_SESSION['idutente'];
$ris= eseguiQuery($con, $query);
$rec= mysqli_fetch_array($ris);
$token=$rec['token'];
if ($tokeninserito==$token)
{
    $query="update tbl_utenti set token='' where idutente=".$_SESSION['idutente'];
    $ris= eseguiQuery($con, $query);
    $_SESSION['tokenok']=true;
    header("location: ele_ges.php?suffisso=" . $_SESSION['suffisso']);
}
else
    if ($_SESSION['tentativiotp']>3)
        header("location: login.php?suffisso=" . $_SESSION['suffisso']);
    else
        header("location: otpcheck.php?suffisso=" . $_SESSION['suffisso']);
    



