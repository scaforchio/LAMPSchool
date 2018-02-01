<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
GNU Affero General Public License come pubblicata 
dalla Free Software Foundation; sia la versione 3, 
sia (a vostra scelta) ogni versione successiva.

Questo programma é distribuito nella speranza che sia utile 
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di 
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE. 
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Gestione obiettivi di comportamento";

$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$maxob = 10;

$materia = stringa_html('materia');
$anno = stringa_html('anno');


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$query = "select * from tbl_compob order by numeroordine";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
print "<p align='center'>
          <font size=4 color='black'>Obiettivi </font>
          <form method='post' action='insobiettivi.php'>
          <table border=1 align='center'>";
$numord = 0;
while ($val = mysqli_fetch_array($ris))
{
    $numord++;
    $sintob = $val["sintob"];
    $obiettivo = $val["obiettivo"];
    $idobiettivo = $val["idobiettivo"];
    print "<tr><td>$numord</td>
               <td>
                   SINTESI: <input type=text name=sint$numord value='$sintob' maxlength=80 size=80><br/>
                   <input type=hidden name=idob$numord value='$idobiettivo'>
                   <textarea cols=80 rows=3 name=est$numord>" . $val['obiettivo'] . "</textarea></td>";
    print "<td>&nbsp;</td>";
    print "</tr>";
}
for ($no = $numord + 1; $no <= $maxob; $no++)
{
    print "<tr><td>$no</td><td>SINTESI: <input type=text name=sint$no value='' maxlength=80 size=80><br/><textarea cols=80 rows=3 name=est$no></textarea></td><td>";
    print "Pos. ins. <select name='pos$no'>";
    print "<option value='0'>&nbsp;</option>";
    for ($i = 1; $i <= $numord; $i++)
        print "<option value='$i'>$i</option>";
    print "</select>";
    print "</td></tr>";
}
print "<tr><td colspan=3 align=center><input type='submit' value='Registra obiettivi'></tr></table>";

print "</form>";


mysqli_close($con);
stampa_piede("");

