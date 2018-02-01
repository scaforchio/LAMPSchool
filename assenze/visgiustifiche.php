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
$idutente = $_SESSION["idutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Elenco giustificazioni";
$script = "";
stampa_head($titolo, "", $script,"DS");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


//
//    Fine parte iniziale della pagina
//


//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));



print "<br>";


// GIUSTIFICHE ASSENZE
$query = "SELECT * FROM tbl_assenze,tbl_alunni,tbl_classi
            WHERE tbl_assenze.idalunno = tbl_alunni.idalunno
              AND tbl_alunni.idclasse = tbl_classi.idclasse
              AND iddocentegiust=$idutente
              ORDER BY datagiustifica desc";
$ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query, false));

print "<CENTER><b>ASSENZE</b><TABLE BORDER='1'>";
print "<TR class='prima'><TD ALIGN='CENTER'><B>Alunno</B></TD><TD ALIGN='CENTER'><B>Data assenza</B></TD><TD ALIGN='CENTER'><B>Data giustifica</B></TD><TD COLSPAN='2' ALIGN='CENTER'><B>Azioni</B></TD></TR>";
while ($dati = mysqli_fetch_array($ris))
{



    print "<TR class='oddeven'><TD>" . $dati['cognome'] . " " . $dati['nome'] . " " . data_italiana($dati['datanascita']) . " (" . $dati['anno'] . " " . $dati['sezione'] . " " . $dati['specializzazione'] . ")" . "</TD><TD>" . data_italiana($dati['data']) . " " . giorno_settimana($dati['data'])."</TD><TD>" . data_italiana($dati['datagiustifica']) . " " . giorno_settimana($dati['datagiustifica'])."</TD>";
    print "<TD align='center'>";


    print "<A HREF='cancgiustifica.php?idassenza=" . $dati['idassenza'] . "'><img src='../immagini/delete.png' title='Elimina'></A>";

    print "</TD></TR>";
}
print "</CENTER></TABLE>";


// GIUSTIFICHE RITARDI
$query = "SELECT * FROM tbl_ritardi,tbl_alunni,tbl_classi
            WHERE tbl_ritardi.idalunno = tbl_alunni.idalunno
              AND tbl_alunni.idclasse = tbl_classi.idclasse
              AND iddocentegiust=$idutente
              ORDER BY datagiustifica desc";
$ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query, false));

print "<CENTER><b>RITARDI</b><TABLE BORDER='1'>";
print "<TR class='prima'><TD ALIGN='CENTER'><B>Alunno</B></TD><TD ALIGN='CENTER'><B>Data ritardo</B></TD><TD ALIGN='CENTER'><B>Data giustifica</B></TD><TD COLSPAN='2' ALIGN='CENTER'><B>Azioni</B></TD></TR>";
while ($dati = mysqli_fetch_array($ris))
{



    print "<TR class='oddeven'><TD>" . $dati['cognome'] . " " . $dati['nome'] . " " . data_italiana($dati['datanascita']) . " (" . $dati['anno'] . " " . $dati['sezione'] . " " . $dati['specializzazione'] . ")" . "</TD><TD>" . data_italiana($dati['data']) . " " . giorno_settimana($dati['data'])."</TD><TD>" . data_italiana($dati['datagiustifica']) . " " . giorno_settimana($dati['datagiustifica'])."</TD>";
    print "<TD align='center'>";


    print "<A HREF='cancgiustifica.php?idritardo=" . $dati['idritardo'] . "'><img src='../immagini/delete.png' title='Elimina'></A>";

    print "</TD></TR>";
}
print "</CENTER></TABLE>";

// GIUSTIFICHE USCITE ANTICIPATE
if ($giustificauscite=='yes')
{
    $query = "SELECT * FROM tbl_usciteanticipate,tbl_alunni,tbl_classi
            WHERE tbl_usciteanticipate.idalunno = tbl_alunni.idalunno
              AND tbl_alunni.idclasse = tbl_classi.idclasse
              AND iddocentegiust=$idutente
              ORDER BY datagiustifica desc";
    $ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query, false));

    print "<CENTER><b>USCITE ANTICIPATE</b><TABLE BORDER='1'>";
    print "<TR class='prima'><TD ALIGN='CENTER'><B>Alunno</B></TD><TD ALIGN='CENTER'><B>Data uscita</B></TD><TD ALIGN='CENTER'><B>Data giustifica</B></TD><TD COLSPAN='2' ALIGN='CENTER'><B>Azioni</B></TD></TR>";
    while ($dati = mysqli_fetch_array($ris))
    {


        print "<TR class='oddeven'><TD>" . $dati['cognome'] . " " . $dati['nome'] . " " . data_italiana($dati['datanascita']) . " (" . $dati['anno'] . " " . $dati['sezione'] . " " . $dati['specializzazione'] . ")" . "</TD><TD>" . data_italiana($dati['data']) . " " . giorno_settimana($dati['data']) . "</TD><TD>" . data_italiana($dati['datagiustifica']) . " " . giorno_settimana($dati['datagiustifica']) . "</TD>";
        print "<TD align='center'>";


        print "<A HREF='cancgiustifica.php?iduscita=" . $dati['iduscita'] . "'><img src='../immagini/delete.png' title='Elimina'></A>";

        print "</TD></TR>";
    }
    print "</CENTER></TABLE>";
}


stampa_piede("");
mysqli_close($con);
	
	


