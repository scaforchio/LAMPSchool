<?php

require_once '../lib/req_apertura_sessione.php';

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");

@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idamministrativo = stringa_html('idamm');

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
else if($tipoutente !== "M")
{
    header("location: ../segreteria/vis_imp.php");
    die;
}



$titolo = "Invio mail con credenziali Amministrativo";
$script = "";
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
$idcl = estrai_classe_alunno($idalu, $con);

stampa_head($titolo, "", $script, "SMPA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='../segreteria/vis_imp.php'>Segreteria</a> -  $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$query = "SELECT userid FROM tbl_utenti WHERE idutente = ".$idamministrativo.";";
$res = eseguiQuery($con,$query);
$userid = mysqli_fetch_assoc($res)["userid"];
$newPwd = creapassword();

$query = "UPDATE tbl_utenti SET password = md5('" . md5($newPwd) . "') WHERE idutente = ".$idamministrativo.";";
$res = eseguiQuery($con,$query);

if($res !== false)
{
    print("<center>
    <h2>La mail contiene le seguenti credenziali: </h2>
    <h3>username: $userid</h3>
    <h3>password: $newPwd</h3>");

//TODO: stampare mail intera grazie al generatore di mail da template
//TODO: mandare mial con funzione apposita

}
else
{
    print("<h2 style='color:red' >errore nel cambiamento della password</h2>");
}



print("<a href='../docenti/vis_doc.php' class='button'>Torna alla lista dei docenti</a>
       </center>");


stampa_piede("");
mysqli_close($con);



