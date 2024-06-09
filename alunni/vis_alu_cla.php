<?php

require_once '../lib/req_apertura_sessione.php';

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

//Visualizzazione classi
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");
// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Elenco classi";
$script = "";
stampa_head_new($titolo, "", $script, "MASP");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Elenco classi", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<h1> Connessione server fallita </h1>");
}
$db = true;
if (!$db)
{
    print("<h1> Connessione db fallita </h1>");
}
$sql = "SELECT * FROM tbl_classi ORDER BY specializzazione, sezione, anno";
$res = eseguiQuery($con, $sql);

print "<br><form method='POST' action='vis_alu.php' name='alunni'>";
print "<center>";
print "<div class='mb-2'>Seleziona classe: </div>";
print "<div style='max-width: 350px'> <select class='form-select' name='idcla' ONCHANGE='alunni.submit()'><option value=''>&nbsp;</option></div>";
while ($dati = mysqli_fetch_array($res))
{
    print("<option value='" . $dati['idclasse'] . "'> " . $dati['anno'] . " " . $dati['sezione'] . " " . $dati['specializzazione'] . "  </option>");
}
print "<option value='0'>Senza classe</option>";
print "</select>";
print "</form>";

?> 
<center>
    <br> oppure <br> <br>
    <a class='btn btn-outline-secondary mb-3' href='vis_alu_ins.php'>
        <i class='bi bi-person-plus'></i>
        Inserisci nuovo alunno senza classe
    </a>
    <a class='btn btn-outline-secondary' href='vis_alu_ricerca.php'>
        <i class='bi bi-search'></i>
        Ricerca alunni
    </a> <br>
</center>
<br/>
<?php

mysqli_close($con);
stampa_piede_new("");


