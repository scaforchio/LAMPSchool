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

/*Programma per la visualizzazione dell'elenco delle tbl_materie.*/

require_once '../php-ini'.$_SESSION['suffisso'].'.php';
require_once '../lib/funzioni.php';

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente=="")
{
    header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']);
    die;
}

$titolo="Elenco tipi di esito scrutini";
$script="";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");

print("<br/><br/>");

//Connessione al server SQL
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome);
if(!$con)
{
    print("<h1> Connessione al server fallita </h1>");
    exit;
}


//Esecuzione query
$query="SELECT * FROM tbl_tipiesiti ORDER BY passaggio";  // 0=supplenza, -1=comportamento

if (!($ris=mysqli_query($con,inspref($query))))
{
    print "Query fallita";
}
else
{
    print "<CENTER><TABLE BORDER='1'>";
    print "<TR class='prima'><TD ALIGN='CENTER'><B>Descrizione</B></TD><TD>Passaggio</TD><TD COLSPAN='2' ALIGN='CENTER'><B>Azioni</B></TD></TR>";

    while($dati=mysqli_fetch_array($ris))
    {
        print "<TR class='oddeven'><TD>".$dati['descrizione']."</TD><TD>".decod_passaggio($dati['passaggio'])."</TD>";
        print "<TD><A HREF='mod_tipoesito.php?idtipoesito=". $dati['idtipoesito']. "'><img src='../immagini/edit.png' title='Modifica'></A>";
        /*  ELIMINAZIONE TIPO ESITO
        $query="select * from tbl_esiti where esito=".$dati['idtipoesito'];
        $ris2=mysqli_query($con,inspref($query)) or die("Errore: ".inspref($query,false));
        if (mysqli_num_rows($ris2)==0)
            print "<A HREF='del_tipoesito.php?idtipoesito=". $dati['idtipoesito']. "'><img src='../immagini/delete.png' title='Elimina'></A>";
        else
            print "&nbsp;";
        */
        print "</TD></TR>";
    }
    print "</CENTER></TABLE>";
}



mysqli_close($con);

stampa_piede("");