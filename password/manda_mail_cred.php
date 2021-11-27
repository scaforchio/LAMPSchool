<?php

require_once '../lib/req_apertura_sessione.php';

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");

@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idalunno = stringa_html('idalu');
$idamministrativo = stringa_html('idamm');
$iddocente = stringa_html('iddoc');

$email = "email";
$titolo = "Invio mail con credenziali";

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
else if($idalunno !== "")
{
    $tutor = stringa_html('tutor');
    $titolo = "Invio mail ".($tutor == 1 ? "al tutor" : "all'alunno")." con credenziali";
    $prevPagPath = "../alunni/vis_alu.php";
    $id = $idalunno;
    $DBtable = "tbl_alunni";
    $nomeCampoId = "idalunno";
    $email = "email".($tutor == 1 ? 2 : "");
    $categoria = "Alunni";
}
else if($idamministrativo !== "")
{
    $titolo = "Invio mail con credenziali amministrativo";
    $prevPagPath = "../segreteria/vis_imp.php";
    $id = $idamministrativo;
    $DBtable = "tbl_amministrativi";
    $nomeCampoId = "idamministrativo";
    $categoria = "Ammistrativo";     
}
else if($iddocente !== "")
{
    $titolo = "Invio mail con credenziali docenti";
    $prevPagPath = "../docenti/vis_doc.php";
    $id = $iddocente;
    $DBtable = "tbl_docenti";
    $nomeCampoId = "iddocente";   
    $categoria = "Docenti";
}
else
{
    header("location: ../login/ele_ges.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

if($tipoutente !== "M")
{
    header("location: $prevPagPath");
    die;
}

$script = "";
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
$idcl = estrai_classe_alunno($idalu, $con);

stampa_head($titolo, "", $script, "SMPA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='$prevPagPath'>$categoria</a> -  $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$queryPerUsername = "SELECT userid FROM tbl_utenti WHERE idutente = ".$id.";";
$res = eseguiQuery($con,$queryPerUsername);
$username = mysqli_fetch_assoc($res)["userid"];

$queryPerEmail = "SELECT $email FROM $DBtable WHERE $nomeCampoId = ".$id.";";
$res = eseguiQuery($con,$queryPerEmail);
$email = mysqli_fetch_assoc($res)[$email];

$newPwd = creapassword();

$queryAggPass = "UPDATE tbl_utenti SET password = md5('" . md5($newPwd) . "') WHERE idutente = ".$id.";";
$res = eseguiQuery($con,$queryAggPass);

if($res !== false)
{
    $loginPath = __URL__."login/login.php?suffisso=" . $_SESSION['suffisso'];
    print("<center>
            <h2>La mail contiene le seguenti credenziali: </h2>
            <h3>username: $username</h3>
            <h3>password: $newPwd</h3>");

    $msg = "Ecco le nuove credenziali per entrare nel tuo registro elettronico.<br>
    
            Nome utente:<br>
            $username<br><br>
            
            Password:<br>
            $newPwd<br><br>
            
            Indirizzo di accesso:<br>
            $loginPath<br><br>
            
            Se ci sono domande contatta la segreteria.<br><br>
            
            Per favore dopo il primo accesso cambia questa password. Non condividere questa password.<br>
            $email<br>";

    print($msg);
    invia_mail($email,"Nuova password registro elettronico",$msg);
}
else
{
    print("<h2 style='color:red' >errore nel cambiamento della password</h2>");
}



print("<a href='$prevPagPath' class='button'>Torna alla lista pagina precedente</a>
       </center>");


stampa_piede("");
mysqli_close($con);



