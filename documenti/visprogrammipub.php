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
$suffisso = $_GET['suffisso'];
if ($suffisso == "")
{
    $suffisso = $_POST['suffisso'];
}
$_SESSION['suffisso'] = $suffisso;
// @require_once("../php-ini".$_SESSION['suffisso'].".php");
@require_once("../lib/funzioni.php");

@require_once("../php-ini" . $suffisso . ".php");

////session_start();
$_SESSION["annoscol"] = $annoscol; //prende la variabile presente nella sessione
$_SESSION['versione'] = $versioneprecedente;
// istruzioni per tornare alla pagina di login se non c'� una sessione valida

if (!isset($_SESSION["tipoutente"]))
{
    $_SESSION["tipoutente"] = "O";
}

$visualizzabili = array("image/jpeg", "application/pdf", "image/pjpeg", "image/gif", "image/png");

$titolo = "Visualizzazione programmi svolti";
$script = "";
stampa_head($titolo, "", $script, "ASDTMO", false);
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
$classe = stringa_html("classe");

// scelta classe
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


print ("
         <form method='post' action='visprogrammipub.php' name='programmi'>
         <input type='hidden' name='suffisso' value='$suffisso'>
         <p align='center'>
         <table align='center'>");
print("
        <tr>
        <td width='50%'><b>Classe</b></p></td>
        <td width='50%'>
        <SELECT NAME='classe' ONCHANGE='programmi.submit()'><option value=''></option>  ");

//
//  Riempimento combobox delle tbl_classi
//
$query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY anno,sezione,specializzazione";
$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
    if ($classe == $nom["idclasse"])
    {
        print " selected";
    }
    print ">";
    print ($nom["anno"]);
    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
}

print ("</select></td></tr></table></form>");


// visualizzazione programmi
//$maxcomp=20;
if ($classe != "")
{
    print ("
		<p align='center'>
		<table align='center' border='1'>
		<tr class='prima'>
			<td><b>Classe</b></td>
			<td><b>Materia</b></td>
			<td><b>Azione</b></td>");

    $query = "select distinct tbl_classi.idclasse, anno, sezione, specializzazione,
			  denominazione,tbl_materie.idmateria,
			  iddocumento,docnome,docsize,doctype 
			  from tbl_cattnosupp, tbl_classi, tbl_materie, tbl_documenti 
			  where tbl_cattnosupp.idclasse=tbl_classi.idclasse 
			  and tbl_cattnosupp.idmateria = tbl_materie.idmateria 
			  and tbl_cattnosupp.idclasse = tbl_documenti.idclasse
			  and tbl_cattnosupp.idmateria = tbl_documenti.idmateria
			  and tbl_cattnosupp.iddocente<>1000000000
			  and tbl_documenti.idtipodocumento=1000000002
			  and tbl_classi.idclasse = $classe
			  order by anno, specializzazione,sezione, denominazione";
    //print inspref($query);
    $ris = mysqli_query($con, inspref($query));
    while ($nom = mysqli_fetch_array($ris))
    {


        print "<tr><td>" . $nom['anno'] . " " . $nom['sezione'] . " " . $nom['specializzazione'] . "</td>";
        print "<td>" . $nom['denominazione'] . "</td>";

        print ("<td> ");
        echo "<a href='actionsdocum.php?action=download&Id=" . $nom["iddocumento"] . "' target='_blank'><img src='../immagini/download.jpg' alt='scarica'></a> ";

        if (in_array($nom["doctype"], $visualizzabili))
        {
            echo "  <a href='actionsdocum.php?action=view&Id=" . $nom["iddocumento"] . "' ";
            echo "target='_blank'><img src='../immagini/view.jpg' alt='visualizza'></a>  ";
        }

        print "</td>";
        print"</tr>";


    }

    print "</table>";
}
mysqli_close($con);
stampa_piede("", false);


