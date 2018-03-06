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
$titolo = "AGGIUNGI ALUNNI A GRUPPO GLOBALE";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);


// creaGruppoGlobaleMoodle($tokenservizimoodle,$urlmoodle, "5ainf2017", "5ainf2017");
AggiungiGruppoClasse($con, $tokenservizimoodle, $urlmoodle, 28,$annoscol);
stampa_piede("");

function AggiungiGruppoClasse($con,$token,$urlmoodle,$idclasse,$annoscol)
{
    $annocl=decodifica_anno_classe($idclasse, $con);
    $sezicl=decodifica_classe_sezione($idclasse, $con);
    $speccl= substr(decodifica_classe_spec($idclasse, $con),0,3);
    $identgruppo= strtolower($annocl.$sezicl.$speccl.$annoscol);
    $queryalunni="select idalunno from tbl_alunni where idclasse='$idclasse'";
    $res=mysqli_query($con, inspref($queryalunni)) or die("Errore $queryalunni");
    while ($rec=mysqli_fetch_array($res))
    {
        $idalunno=$rec['idalunno'];
        $username= costruisciUsernameMoodle($idalunno);
        aggiungiUtenteAGruppoGlobale($token, $urlmoodle, $identgruppo, $username);

        
    }
}