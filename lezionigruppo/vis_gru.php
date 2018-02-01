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


/*Programma per la visualizzazione dell'elenco dei gruppi di alunni per lezioni*/

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Elenco gruppi";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


//
//    Fine parte iniziale della pagina
//


//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

//Connessione al database
$DB = true;
if (!$DB)
{
    print("<h1> Connessione al database fallita </h1>");
    exit;
}

//Esecuzione query
$query = "SELECT * FROM tbl_gruppi ORDER BY descrizione";
if (!($ris = mysqli_query($con, inspref($query))))
{
    print "\nQuery fallita";
}
else
{
    print "<CENTER><TABLE BORDER='1'>";
    print "<TR class='prima'><TD ALIGN='CENTER'><B>Gruppo</B></TD><TD COLSPAN='2' ALIGN='CENTER'><B>Azioni</B></TD></TR>";
    while ($dati = mysqli_fetch_array($ris))
    {
        print "<TR class='oddeven'><TD>" . $dati['descrizione'] . "</TD>";
        print "<TD><A HREF='mod_gru.php?idgruppo=" . $dati['idgruppo'] . "'><img src='../immagini/edit.png' title='Modifica'></A>";

        if (poss_canc_gru($dati['idgruppo'], $con))
        {
            print "&nbsp;<A HREF='eli_gru.php?idgruppo=" . $dati['idgruppo'] . "'><img src='../immagini/delete.png' title='Elimina'></A>";
        }
        print "<a href='selealunnigruppo.php?idgruppo=" . $dati['idgruppo'] . "&iddocente=" . $dati['iddocente'] . "&idmateria=" . $dati['idmateria'] . "'><img src='../immagini/tabella.png' alt='Elenco alunni'></a>";
        print "</TD></TR>";
    }
    print "</CENTER></TABLE>";
}

print "<br/><CENTER><form name='form2' action='gruppo.php' method='POST'>";
print "<input type='submit' name='nuovogruppo' value='Nuovo gruppo'>";
print "</form></CENTER>";

stampa_piede("");
mysqli_close($con);
	
	


