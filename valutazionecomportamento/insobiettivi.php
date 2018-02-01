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

$titolo = "Inserimento obiettivi di comportamento";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


//$query="delete from tbl_competscol where idmateria=$materia and anno=$anno";
//$ris=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));

for ($no = 1; $no <= 20; $no++)
{
    $sintob = stringa_html("sint$no");
    $obiettivo = stringa_html("est$no");
    $idobiettivo = stringa_html("idob$no");

    if ($sintob != "" & $idobiettivo == "")
    {
        $posins = stringa_html("pos$no");
        if ($posins != 0)
        {
            $query = "update tbl_compob set numeroordine = numeroordine+1 where numeroordine>=$posins";
            $risupd = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

            $query = "insert into tbl_compob(numeroordine, sintob, obiettivo) values($posins,'$sintob', '$obiettivo')";
            $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

        }
        else
        {
            $query = "insert into tbl_compob(numeroordine, sintob, obiettivo) values($no,'$sintob', '$obiettivo')";
            $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

        }

    }
    if ($sintob != "" & $idobiettivo != "")
    {

        $query = "update tbl_compob set sintob='$sintob',obiettivo='$obiettivo' where idobiettivo=$idobiettivo";
        $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

    }
    if ($sintob == "" & $idobiettivo != "")
    {

        $query = "delete from tbl_compob where idobiettivo=$idobiettivo";
        $ris2 = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

    }
}

print "<center><b>Inserimento obiettivi di comportamento effettuato!</b></center>";

//  codice per richiamare il form delle competenze;
print ("
   <form method='post' action='obiettivi.php'>
   <p align='center'>


     <input type='submit' value='OK' name='b'></p>
     </form>
  ");

mysqli_close($con);
stampa_piede("");

