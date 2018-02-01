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

$dataammoniz = date('Y-m-d');

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$datalimiteinferiore=giorno_lezione_passata(date('Y-m-d'),$maxritardogiust,$con);
$titolo = "Inserimento ammonizioni";
$script = "";
stampa_head($titolo, "", $script,"PMASD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");



$query = "SELECT idalunno,nome,cognome FROM tbl_alunni WHERE idclasse<>0";

$ris = mysqli_query($con, inspref($query)) or die("Errore:".inspref($query,false));


while ($rec = mysqli_fetch_array($ris))
{
    $idalunno = $rec['idalunno'];
    $datialunno = $rec['cognome']." ".$rec['nome'];

    $strass = "ammass" . $idalunno;

    $aludaamm = stringa_html($strass) ? "on":"off";


    if ($aludaamm == "on")
    {
        $query = "SELECT idassenza,data FROM tbl_assenze WHERE NOT giustifica AND data< '$datalimiteinferiore'
            AND dataammonizione IS NULL AND idalunno=$idalunno ORDER BY data";
        $risass = mysqli_query($con, inspref($query)) or die("Errore:".inspref($query,false));

        $elenco="";
        while ($recass = mysqli_fetch_array($risass))
        {
            $elenco.=substr(data_italiana($recass['data']),0,5).", ";
        }
         print $elenco;
         if (strlen($elenco)>7)
                  $elenco=substr($elenco,0,strlen($elenco)-9)." e ".substr($elenco,strlen($elenco)-7,6);
        $query = "UPDATE tbl_assenze SET dataammonizione='".date('Y-m-d')."' WHERE NOT giustifica AND data< '$datalimiteinferiore'
            AND dataammonizione IS NULL AND idalunno=$idalunno";
        mysqli_query($con, inspref($query)) or die("Errore:".inspref($query,false));
        $elenco=substr($elenco,0,strlen($elenco)-1);
        $idclasse=estrai_classe_alunno($idalunno,$con);
        $iddocente=$_SESSION['idutente'];
        if (strlen($elenco)>7)
            $numero="alle assenze";
        else
            $numero="all'assenza";

        $testo=elimina_apici("Con riferimento $numero del $elenco l'alunno $datialunno non ha portato la giustifica nei termini consentiti.");
        $provvedimenti=elimina_apici(estrai_testo_modificato("ammonizmancgiust","[alunno]",$datialunno,$con));
        $query = "INSERT INTO tbl_notealunno(idclasse,data,iddocente,testo,provvedimenti) values('$idclasse','$dataammoniz','$iddocente','$testo','$provvedimenti')";
        mysqli_query($con, inspref($query)) or die("Errore:".inspref($query,false));
        $numnota=mysqli_insert_id($con);
        $query = "INSERT INTO tbl_noteindalu(idnotaalunno,idalunno) values('$numnota','$idalunno')";
        mysqli_query($con, inspref($query)) or die("Errore:".inspref($query,false));

    }

    $strass = "ammrit" . $idalunno;
    $aludaamm = stringa_html($strass) ? "on":"off";


    if ($aludaamm == "on")
    {

        $query = "SELECT idritardo,data FROM tbl_ritardi WHERE NOT giustifica AND data< '$datalimiteinferiore'
            AND dataammonizione IS NULL AND idalunno=$idalunno ORDER BY data";
        $risass = mysqli_query($con, inspref($query)) or die("Errore:".inspref($query,false));
        $elenco="";
        while ($recass = mysqli_fetch_array($risass))
        {
            $elenco.=substr(data_italiana($recass['data']),0,5).", ";
        }

        $query = "UPDATE tbl_ritardi SET dataammonizione='".date('Y.-m-d')."' WHERE NOT giustifica AND data< '$datalimiteinferiore'
            AND dataammonizione IS NULL AND idalunno=$idalunno";
        mysqli_query($con, inspref($query)) or die("Errore:".inspref($query,false));
        $elenco=substr($elenco,0,strlen($elenco)-1);
        if (strlen($elenco)>7)
            $elenco=substr($elenco,0,strlen($elenco)-9)." e ".substr($elenco,strlen($elenco)-7,6);

        $idclasse=estrai_classe_alunno($idalunno,$con);
        $iddocente=$_SESSION['idutente'];
        if (strlen($elenco)>7)
            $numero="ai ritardi";
        else
            $numero="al ritardo";

        $testo=elimina_apici("Con riferimento $numero del $elenco l'alunno $datialunno non ha portato la giustifica nei termini consentiti.");
        $provvedimenti=elimina_apici(estrai_testo_modificato("ammonizmancgiust","[alunno]",$datialunno,$con));
        $query = "INSERT INTO tbl_notealunno(idclasse,data,iddocente,testo,provvedimenti) values('$idclasse','$dataammoniz','$iddocente','$testo','$provvedimenti')";
        mysqli_query($con, inspref($query)) or die("Errore:".inspref($query,false));
        $numnota=mysqli_insert_id($con);
        $query = "INSERT INTO tbl_noteindalu(idnotaalunno,idalunno) values('$numnota','$idalunno')";
        mysqli_query($con, inspref($query)) or die("Errore:".inspref($query,false));

    }

}

print "
        <form method='post' id='formass' action='../assenze/sitgiustifiche.php'>

        </form>
        <SCRIPT language='JavaScript'>
           document.getElementById('formass').submit();
        </SCRIPT>";


stampa_piede("");
mysqli_close($con);



