<?php

require_once '../lib/req_apertura_sessione.php';

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");

@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$iddocente = stringa_html('iddocente');

$titolo = "Invio mail con credenziali docenti";
$script = "";
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
$idcl = estrai_classe_alunno($idalu, $con);

stampa_head($titolo, "", $script, "SMPA");
if ($_SESSION['tipoutente'] == 'M')
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='alu_rigenera_password.php'>Rigenera password</a> -  $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
else
    stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='../alunni/vis_alu.php?idcla=$idcl'>Elenco alunni</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
$annoscolastico = $_SESSION['annoscol'] . "/" . ($_SESSION['annoscol'] + 1);


if ($idclasse != 0)
{
    $query = "select idclasse,anno,sezione,specializzazione from tbl_classi where idclasse=$idclasse";
    $ris = eseguiQuery($con, $query);

    $val = mysqli_fetch_array($ris);

    print "<center><b>Elenco password per alunni della classe: " . $val['anno'] . $val['sezione'] . " " . $val['specializzazione'] . "</b></center><br/><br/>";
}
print "<form name='stampa'  target='_blank' action='../alunni/alu_stampa_pass_alu.php' method='POST'>";

print "<table align='center' border='1'><tr><td><b>Alunno</b></td><td><b>Utente</b></td><td><b>Password</b></td></tr>";


print ("<br/><center><a href='".$_SESSION['cartellabuffer']."/$nf'><img src='../immagini/csv.png'></a></center>");

stampa_piede("");
mysqli_close($con);



