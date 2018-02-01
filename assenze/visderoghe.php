<?php session_start();

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


/*Programma per la visualizzazione dell'elenco delle tbl_classi.*/

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idalunno = stringa_html('idalunno');
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Elenco certificazioni per assenze";
$script = "";
stampa_head($titolo, "", $script,"MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


//
//    Fine parte iniziale della pagina
//


//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$query = "SELECT * FROM tbl_alunni LEFT JOIN tbl_classi
         ON tbl_alunni.idclasse=tbl_classi.idclasse
         ORDER BY cognome,nome,anno, sezione, specializzazione";
if ($tipoutente=="D")
    $query = "SELECT * FROM tbl_alunni LEFT JOIN tbl_classi
         ON tbl_alunni.idclasse=tbl_classi.idclasse
         WHERE tbl_alunni.idclasse IN (select distinct tbl_classi.idclasse from tbl_classi
                                       where idcoordinatore=".$_SESSION['idutente'].")
         ORDER BY cognome,nome,anno, sezione, specializzazione
         ";
$ris = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query, false));

print "<form name='selealu' action='visderoghe.php' method='post'>";
print "<table align='center'>";
print "<tr><td>Alunno</td>";
print "<td>";
print "<select name='idalunno' ONCHANGE='selealu.submit();'><option value=''>Tutti</option>";
while ($rec = mysqli_fetch_array($ris))
{
    if ($idalunno == $rec['idalunno'])
    {
        $sele = " selected";
    }
    else
    {
        $sele = "";
    }
    print ("<option value='" . $rec['idalunno'] . "'$sele>" . $rec['cognome'] . " " . $rec['nome'] . " (" . $rec['datanascita'] . ") - " . $rec['anno'] . " " . $rec['sezione'] . " " . $rec['specializzazione'] . "</option>");
}
print "
 </select>
 </td>

 </tr>

 </table></form><br><br>";

if ($idalunno != "")
{
    $selealunno = " AND tbl_deroghe.idalunno=$idalunno ";
}
else
{
    $selealunno = " ";
}
//Esecuzione query
$query = "SELECT * FROM tbl_deroghe,tbl_alunni,tbl_classi
            WHERE tbl_deroghe.idalunno = tbl_alunni.idalunno
              AND tbl_alunni.idclasse = tbl_classi.idclasse
              $selealunno
              ORDER BY cognome,nome,anno,sezione,specializzazione,data";
if ($tipoutente=='D')
    $query = "SELECT * FROM tbl_deroghe,tbl_alunni,tbl_classi
            WHERE tbl_deroghe.idalunno = tbl_alunni.idalunno
              AND tbl_alunni.idclasse = tbl_classi.idclasse
              $selealunno
              AND tbl_alunni.idclasse IN (select distinct tbl_classi.idclasse from tbl_classi
                                       where idcoordinatore=".$_SESSION['idutente'].")
              ORDER BY cognome,nome,anno,sezione,specializzazione,data";
$ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query, false));

print "<CENTER><TABLE BORDER='1'>";
print "<TR class='prima'><TD ALIGN='CENTER'><B>Alunno</B></TD><TD ALIGN='CENTER'><B>Data (ore)</B></TD><TD ALIGN='CENTER'><B>Motivo</B></TD></TD><TD COLSPAN='2' ALIGN='CENTER'><B>Azioni</B></TD></TR>";
while ($dati = mysqli_fetch_array($ris))
{

    if ($dati['numeroore'] > 0)
    {
        $numeroore = "(" . $dati['numeroore'] . ")";
    }
    else
    {
        $numeroore = "";
    }

    print "<TR class='oddeven'><TD>" . $dati['cognome'] . " " . $dati['nome'] . " " . data_italiana($dati['datanascita']) . " (" . $dati['anno'] . " " . $dati['sezione'] . " " . $dati['specializzazione'] . ")" . "</TD><TD>" . data_italiana($dati['data']) . " " . giorno_settimana($dati['data']) . " $numeroore</TD><TD>" . $dati['motivo'] . "</TD>";
    print "<TD align='center'>";


    print "<A HREF='delderoghe.php?idcer=" . $dati['idderoga'] . "&idalunno=$idalunno'><img src='../immagini/delete.png' title='Elimina'></A>";

    print "</TD></TR>";
}
print "</CENTER></TABLE>";


stampa_piede("");
mysqli_close($con);
	
	


