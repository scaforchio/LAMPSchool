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

$titolo = "SOLA LETTURA OFF";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$query = "SELECT * FROM tbl_parametri WHERE parametro='sola_lettura'";
$ris = mysqli_query($con, inspref($query));
if (mysqli_num_rows($ris) > 0)
{
    $query = "UPDATE tbl_parametri SET valore='no' WHERE parametro='sola_lettura'";
    $query = str_replace("tbl_", $prefisso_tabelle . "tbl_", $query); // NECESSARIO IN QUANTO NON FUNZIONA inspref in modalità solalettura
    mysqli_query($con, $query) or die ("Errore:" . mysqli_error($con));
}
else
{
    $query = "INSERT INTO tbl_parametri(parametro,valore) VALUES ('sola_lettura','no')";
    $query = str_replace("tbl_", $prefisso_tabelle . "tbl_", $query); // NECESSARIO IN QUANTO NON FUNZIONA inspref in modalità solalettura
    mysqli_query($con, $query) or die ("Errore:" . mysqli_error($con));
}


/*
  $query="update tbl_parametri set valore='no' where parametro='sola_lettura'";
  $query=str_replace("tbl_", $prefisso_tabelle. "tbl_", $query);
  mysqli_query($con,$query) or die ("Errore:".mysqli_error($con));
  if(mysqli_affected_rows($con)==0)
  {
      $query="insert into tbl_parametri(parametro,valore) values ('sola_lettura','no')";
      $query=str_replace("tbl_", $prefisso_tabelle. "tbl_", $query);
      mysqli_query($con,$query) or die ("Errore:".mysqli_error($con));
  }
*/
print "<br><center><b>Registro in modalità normale!</b></center>";

mysqli_close($con);

stampa_piede("");

