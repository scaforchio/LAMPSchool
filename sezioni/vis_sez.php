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

// Programma per la visualizzazione dell'elenco delle tbl_classi.

require_once '../php-ini'.$_SESSION['suffisso'].'.php';
require_once '../lib/funzioni.php';
// require_once '../lib/ db / query.php';
$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));
    
 
// $lQuery = LQuery::getIstanza();

// istruzioni per tornare alla pagina di login 
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']); 
    die;
}	
	
$titolo = "Elenco sezioni";
$script = ""; 
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$rs = mysqli_query($con,inspref("select * from tbl_sezioni order by denominazione")); //selectstar('tbl_sezioni', '', null, 'denominazione');

if ($rs) {
    print "<CENTER><TABLE BORDER='1'>";	
    print "<TR class='prima' border=1><TD ALIGN='CENTER'><B>Sezione</B></TD><TD ALIGN='CENTER'><B>Azioni</B></TD></TR>";
    
    //foreach ($rs as $row) {
    while ($row=mysqli_fetch_array($rs)){
        print "<TR class='oddeven'>";
        print "<TD>". $row['denominazione']. "</TD>";
        print "<TD><A HREF='mod_sez.php?idsez=". 
                $row['idsezione']. "'><img src='../immagini/edit.png' title='Modifica'></A>&nbsp;
                   <A HREF='eli_sez.php?idsez=". 
                $row['idsezione']."'><img src='../immagini/delete.png' title='Elimina'></A>";
        print "</TD>";
        print "</TR>";	
    }
    print "</TABLE></CENTER>";
} else {
    print "Query fallita";
}

print "<br/><CENTER><form name='form2' action='nuo_sez.php' method='POST'>";
print "<input type='submit' name='nuovasezione' value='Nuova sezione'>";
print "</form></CENTER>";   
mysqli_close($con);
stampa_piede("");

