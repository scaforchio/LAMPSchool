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

$idclasse=stringa_html('idclasse');
$motivo=stringa_html('motivo');
$ora=stringa_html('orauscita');
// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$titolo = "Inserimento autorizzazione uscita";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='selealunniautuscita.php'>Autorizzazioni uscita</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$dest = array();

$destinatari = array();

$query = "SELECT idalunno,cognome, nome
        FROM tbl_alunni
        where idclasse=$idclasse";

$ris = mysqli_query($con, inspref($query));
$motivo = stringa_html('motivo');
$datainizio = data_to_db(stringa_html('datainizio'));
$datafine = data_to_db(stringa_html('datafine'));
$pos = 0;
$annotazione="";
$elencoalunni="";
while ($rec = mysqli_fetch_array($ris))
{
    $stralu = "pres" . $rec['idalunno'];
    $idalunno=$rec['idalunno'];
    $aludainv = stringa_html($stralu);

    if ($aludainv == "on")
    {

        $data = $datainizio;
        // Inserisco una presenza forzata per ogni giorno compreso tra datainizio e datafine

        $alunno=estrai_dati_alunno_rid($idalunno,$con);
        $elencoalunni.=$alunno.", ";
        $pos++;

        if (stringa_html("uscitacont")=='on')
        {
            $data=date('Y-m-d');

            $sql="insert into tbl_usciteanticipate(idalunno,data,orauscita,giustifica) values ($idalunno,'$data','$ora',true)";
            mysqli_query($con,inspref($sql)) or die ("Errore".inspref($sql,false));
            //ricalcola_uscite($con, $idalunno, $data, $data);
            elimina_assenze_lezione($con, $idalunno, $data);
            inserisci_assenze_per_ritardi_uscite($con, $idalunno, $data);

        }

	}

}

if ($pos>0)
{
    $elencoalunni=substr($elencoalunni,0,strlen($elencoalunni)-2);
    if ($pos>1)
    {
        $inizio="";
        $mezzo=" possono uscire alle ";
    }
    else
    {
        $inizio="";
        $mezzo=" può uscire alle ";
    }
    if ($motivo!="")
        $fine=" $motivo.";
    else
        $fine=".";
    $annotazione=$inizio.$elencoalunni.$mezzo.$ora.$fine;

    $sql="insert into tbl_annotazioni(idclasse,iddocente,data,testo) values ($idclasse,".$_SESSION['idutente'].",'".date('Y-m-d')."','$annotazione')";
    mysqli_query($con,inspref($sql)) or die ("Errore".inspref($sql,false));

    print "<br><br><center><b><font color='green'>Inserimento effettuato!</font></b>";
}
else
{
    print "<br><br><center><b><font color='red'>Nessuna autorizzazione inserita!</font></b>";
}






stampa_piede("");
mysqli_close($con);



