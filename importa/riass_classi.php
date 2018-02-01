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

/*Programma per la visualizzazione del menu principale.*/

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");


// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione


if ($tipoutente == "")
{
    header("location: login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "TRASFERIMENTO CLASSE ALUNNI";
$script = "";
stampa_head($titolo, "", $script,"MA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$numeroclassi = stringa_html('numclassi');
$elimterm = stringa_html('terminali');
$termcond = stringa_html('termcond');

//
//  Elimino alunni promossi in classe terminale
//

if ($termcond == 0)
{
    $queryselalu = "select tbl_esiti.idalunno,passaggio from tbl_esiti,tbl_alunni,tbl_tipiesiti,tbl_classi
                 where tbl_esiti.idalunno=tbl_alunni.idalunno
                 and tbl_esiti.esito=tbl_tipiesiti.idtipoesito
                 and tbl_alunni.idclasse=tbl_classi.idclasse
                 and passaggio=0
                 and tbl_classi.anno=$numeroanni";
}
else
{
    $queryselalu = "select idalunno from tbl_alunni,tbl_classi
                       where tbl_alunni.idclasse=tbl_classi.idclasse
                       and tbl_classi.anno=$numeroanni";
}
$ris = mysqli_query($con, inspref($queryselalu));
$numalunnicanc = 0;
while ($rec = mysqli_fetch_array($ris))
{
    $codicealunno = $rec['idalunno'];
    if ($elimterm == 0)
    {
        $queryriass = "delete from tbl_alunni where idalunno=$codicealunno";
    }
    else
    {
        $queryriass = "update tbl_alunni set idclasse=0 where idalunno=$codicealunno";
    }
    mysqli_query($con, inspref($queryriass)) or die("Errore nella cancellazione alunno!");
    if ($elimterm == 0)
    {
        $queryriass = "delete from tbl_utenti where idutente=$codicealunno";
        mysqli_query($con, inspref($queryriass)) or die("Errore nella cancellazione utente!");
        //$queryriass = "delete from tbl_tutori where idalunno=$codicealunno";
        //mysqli_query($con, inspref($queryriass)) or die("Errore nella cancellazione tutore!");
    }
    $numalunnicanc++;
}
if ($elimterm == 0)
{
    echo "Eliminati <b>$numalunnicanc</b> alunni da classi terminali<br>";
}
else
{
    echo "Eliminata associazione a classe terminale a <b>$numalunnicanc</b> alunni<br>";
}

// RIASSEGNO CLASSI

for ($nc = $numeroclassi; $nc > 0; $nc--)
{
    $post_clpart = "part$nc";
    $post_clarr = "dest$nc";
    $post_tutti = "tutti$nc";
    $classepartenza = stringa_html($post_clpart);
    $classearrivo = stringa_html($post_clarr);
    $trasftutti = stringa_html($post_tutti);
    if ($trasftutti == 0)
    {
       /* $queryselalu = "select tbl_esiti.idalunno,passaggio from tbl_esiti,tbl_alunni,tbl_tipiesiti
                       where tbl_esiti.idalunno=tbl_alunni.idalunno
                       and tbl_esiti.esito=tbl_tipiesiti.idtipoesito
                       and passaggio=0
                       and tbl_alunni.idclasse=$classepartenza"; */


        $queryselalu="select tbl_esiti.idalunno,esito,integrativo
                       from tbl_esiti,tbl_alunni
                       where
                            tbl_esiti.idalunno=tbl_alunni.idalunno
                            and ((esito in (select idtipoesito from tbl_tipiesiti where passaggio=0))
                            or (integrativo in (select idtipoesito from tbl_tipiesiti where passaggio=0)))
                       and tbl_alunni.idclasse=$classepartenza";

    }
    else
    {
        $queryselalu = "select idalunno from tbl_alunni
                       where idclasse=$classepartenza";
    }
    $ris = mysqli_query($con, inspref($queryselalu));
    $numalunnitrasf = 0;
    while ($rec = mysqli_fetch_array($ris))
    {
        $codicealunno = $rec['idalunno'];
        $queryriass = "update tbl_alunni set idclasse='$classearrivo' where idalunno=$codicealunno";
        mysqli_query($con, inspref($queryriass)) or die("Errore nel passaggio di classe!");
        $numalunnitrasf++;
    }
    if ($classearrivo != 0)
    {
        echo "Trasferiti <b>$numalunnitrasf</b> alunni da classe <b>" . decodifica_classe($classepartenza, $con) . "</b> a classe <b>" . decodifica_classe($classearrivo, $con) . "</b><br>";
    }
    else
    {
        echo "Eliminata associazione classe a <b>$numalunnitrasf</b> alunni da classe <b>" . decodifica_classe($classepartenza, $con) . "</b><br>";
    }


}
// ELIMINO ASSOCIAZIONE A CLASSE ESAME

$query = "update tbl_alunni set idclasseesame=0 where 1=1";
mysqli_query($con, inspref($query)) or die("Errore nell'eliminazione classe esame!");


// SVUOTO TABELLA ESITI
$query = "truncate tbl_esiti";
mysqli_query($con, inspref($query)) or die("Errore nel troncamento tabella esiti!");

// ELIMINO TABELLA VECCHIE CLASSI
$query = "DROP TABLE tbl_classiold";
mysqli_query($con, inspref($query)) or die("Errore nell'eliminazione tabella vecchie classi!");


stampa_piede("");
