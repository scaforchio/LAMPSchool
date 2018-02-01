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
$iddocente = $_SESSION["idutente"];
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$visualizzabili = array("image/jpeg", "application/pdf", "image/pjpeg", "image/gif", "image/png");
$tipodoc = stringa_html('tipodoc');
switch ($tipodoc)
{
    case 'pia':
        $titolo = "Visualizzazione piani lavoro";

        $tipodocumento = 1000000001;
        break;
    case 'pro':
        $titolo = "Visualizzazione programmi";

        $tipodocumento = 1000000002;
        break;
    case 'rel':
        $titolo = "Visualizzazione relazioni finali";

        $tipodocumento = 1000000003;
        break;
}



$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$maxcomp = 20;

$ordinamento = stringa_html('ordinamento');

print "<form name='verpiani' action='verdocumprog.php?tipodoc=$tipodoc' method='post'>";
print "<center>Ordinamento:";
print "<select name='ordinamento'  ONCHANGE='verpiani.submit()'>";
if ($ordinamento == "doc" | $ordinamento == "") $seledoc = ' selected';
else $seledoc = '';
if ($ordinamento == "mat") $selemat = ' selected';
else $selemat = '';
if ($ordinamento == "cla") $selecla = ' selected';
else $selecla = '';
print "<option value='doc'$seledoc>Docenti</option>
       <option value='mat'$selemat>Materie</option>
       <option value='cla'$selecla>Classi</option>";
print "</select></center>";
print "</form>";


print ("
   
   
   <p align='center'>
   <table align='center' border='1'>
   <tr class='prima'>
      <td><b>Cattedra</b></td>
      <td><b>Docenti</b></td>
      <td><b>File</b></td>
      <td><b>Azione</b></td>");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));
switch ($ordinamento)
{
    case "":
    case "doc":
        $query = "SELECT DISTINCT tbl_classi.idclasse, anno, sezione, specializzazione,
        denominazione,tbl_materie.idmateria,tbl_docenti.iddocente,cognome,nome
        FROM tbl_cattnosupp, tbl_classi, tbl_materie, tbl_docenti
        WHERE tbl_cattnosupp.idclasse=tbl_classi.idclasse
        AND tbl_cattnosupp.idmateria = tbl_materie.idmateria
        AND tbl_cattnosupp.iddocente = tbl_docenti.iddocente
        AND tbl_cattnosupp.idalunno = 0
        AND tbl_cattnosupp.iddocente<>1000000000
        ORDER BY cognome,nome,anno, sezione, specializzazione, denominazione";
        break;
    case "cla":
        $query = "SELECT DISTINCT tbl_classi.idclasse, anno, sezione, specializzazione,
        denominazione,tbl_materie.idmateria,tbl_docenti.iddocente,cognome,nome
        FROM tbl_cattnosupp, tbl_classi, tbl_materie, tbl_docenti
        WHERE tbl_cattnosupp.idclasse=tbl_classi.idclasse
        AND tbl_cattnosupp.idmateria = tbl_materie.idmateria
        AND tbl_cattnosupp.iddocente = tbl_docenti.iddocente
        AND tbl_cattnosupp.idalunno = 0
        AND tbl_cattnosupp.iddocente<>1000000000
        ORDER BY anno, sezione, specializzazione, denominazione,cognome,nome";
        break;
    case "mat":
        $query = "SELECT DISTINCT tbl_classi.idclasse, anno, sezione, specializzazione,
        denominazione,tbl_materie.idmateria,tbl_docenti.iddocente,cognome,nome
        FROM tbl_cattnosupp, tbl_classi, tbl_materie, tbl_docenti
        WHERE tbl_cattnosupp.idclasse=tbl_classi.idclasse
        AND tbl_cattnosupp.idmateria = tbl_materie.idmateria
        AND tbl_cattnosupp.iddocente = tbl_docenti.iddocente
        AND tbl_cattnosupp.idalunno = 0
        AND tbl_cattnosupp.iddocente<>1000000000
        ORDER BY denominazione,cognome,nome,anno, sezione, specializzazione ";
        break;
}
// print $query;          
$ris = mysqli_query($con, inspref($query)) or die ("errore:" . inspref($query));
while ($nom = mysqli_fetch_array($ris))
{


    print "<tr><td>" . $nom['anno'] . " " . $nom['sezione'] . " " . $nom['specializzazione'] . " - " . $nom['denominazione'] . "</td>";
    print "<td>" . $nom['cognome'] . " " . $nom['nome'] . "</td>";

    $query = "SELECT iddocumento,docnome,docsize,doctype
        FROM tbl_documenti
        WHERE " . $nom['idmateria'] . " = tbl_documenti.idmateria
        AND " . $nom['idclasse'] . " = tbl_documenti.idclasse
        AND idtipodocumento=$tipodocumento";
    $rispl = mysqli_query($con, inspref($query)) or die ("errore:" . inspref($query));
    if ($recpl = mysqli_fetch_array($rispl))
    {
        print ("<td>");
        echo $recpl["docnome"] . " ";
        echo "<font size=1>(" . $recpl["docsize"] . " bytes)</font></td><td> ";
        echo "<a href='actionsdocum.php?action=download&Id=" . $recpl["iddocumento"] . "' target='_blank'><img src='../immagini/download.jpg' alt='scarica'></a> ";

        if (in_array($recpl["doctype"], $visualizzabili))
        {
            echo "  <a href='actionsdocum.php?action=view&Id=" . $recpl["iddocumento"] . "' ";
            echo "target='_blank'><img src='../immagini/view.jpg' alt='visualizza'></a>  ";
        }
        print "</td>";
    }
    else
    {
        print "<td>&nbsp;</td><td>&nbsp;</td>";
    }
    print "</tr>";


}

print "</table>";

mysqli_close($con);
stampa_piede(""); 

