<?php session_start();
/**
 * Elenco degli indici del database
 *
 * @copyright  Copyright (C) 2014 Renato Tamilio
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
//require_once '../lib/ db / query.php';

//$lQuery = LQuery::getIstanza();

// istruzioni per tornare alla pagina di login 
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$titolo = "AGGIORNA PASS ALUNNI";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);


$queryalunni="select idalunno, codfiscale from tbl_alunni where idclasse<>0
         and idalunno not in (select rappresentante1 from tbl_classi)
         and idalunno not in (select rappresentante2 from tbl_classi)
         ";
$risalu=mysqli_query($con,inspref($queryalunni));
$cont=0;
while ($recalu=mysqli_fetch_array($risalu))
{
    $cont++;
    $codfisc=$recalu['codfiscale'];
    $pass=md5(md5($codfisc));
    $utente=$recalu['idalunno']+2100000000;
    $querymod="update tbl_utenti set password='$pass' where idutente=$utente";
    mysqli_query($con,inspref($querymod)) or die("Errore: ".$querymod);
    print "$cont - Cambiata password $codfisc <br>";
}




stampa_piede("");

