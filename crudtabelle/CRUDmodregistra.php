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

/* Programma per la visualizzazione dell'elenco delle tbl_classi. */

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

$id = stringa_html('id');

$daticrud = $_SESSION['daticrud'];
$titolo = "Modifica record in tabella " . $daticrud['aliastabella'];
$script = "";
stampa_head($titolo, "", $script, "MAPSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='CRUD.php'>ELENCO</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

ordina_array_su_campo_sottoarray($daticrud['campi'], 7);
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore connessione!");

// COSTRUZIONE QUERY DI INSERIMENTO

$elencocampivalori="";

$valori= stringa_html('campo');

$cont=0;
foreach ($daticrud['campi'] as $c)
{
    $elencocampivalori.=$c[0]." = '".$valori[$cont]."', ";
    $cont++;
}
$elencocampivalori = substr($elencocampivalori, 0, strlen($elencocampivalori) - 2);
$queryupd="update ".$daticrud['tabella']." set $elencocampivalori where ".$daticrud['campochiave']." = '$id'";
print $queryupd;


 
eseguiQuery($con,$queryupd);
inserisci_log($_SESSION['userid'] . "§" . date('m-d|H:i:s') . "§" . $_SESSION['indirizzoip'] . "§" . $queryupd . "");
   
header("location: ../crudtabelle/CRUD.php?suffisso=".$_SESSION['suffisso']);

stampa_piede("");
mysqli_close($con);


