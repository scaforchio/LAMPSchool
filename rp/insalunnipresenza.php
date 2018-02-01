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
@require_once("../lib/sms/php-send.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$titolo = "Inserimento presenza forzata";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='selealunnipresenza.php'>Presenze forzate</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$dest = array();

$destinatari = array();

$query = "SELECT idalunno,cognome, nome
        FROM tbl_alunni";

$ris = mysqli_query($con, inspref($query));
$motivo = stringa_html('motivo');
$datainizio = data_to_db(stringa_html('datainizio'));
$datafine = data_to_db(stringa_html('datafine'));
$pos = 0;
while ($rec = mysqli_fetch_array($ris))
{
    $stralu = "pres" . $rec['idalunno'];
    $idalunno=$rec['idalunno'];
    $aludainv = stringa_html($stralu);

    if ($aludainv == "on")
    {

        $data = $datainizio;
        // Inserisco una presenza forzata per ogni giorno compreso tra datainizio e datafine
        do
        {
            if ((!giorno_festa($data,$con)) && (giorno_settimana($data)!="Dom"))
            {
                $query = "insert into tbl_presenzeforzate(idalunno,data,motivo) values ($idalunno,'$data','$motivo')";
                mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
                $query = "delete from tbl_assenze where idalunno=$idalunno and data='$data'";
                mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
            }
            $data=aggiungi_giorni($data,1);
        }
        while($data<=$datafine);
	}
    $pos++;
}


print "<br><br><center><b><font color='green'>Inserimento effettuato!</font></b>";




stampa_piede("");
mysqli_close($con);



