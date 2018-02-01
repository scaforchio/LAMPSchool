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

// DA SOSTITUIRE CON PARAMETRO
//$memdati='db'; // Oppure 'hd' (Database o HardDisk) Funzionante da estendere a PDL, Prog e Relazioni

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$idcommissione = stringa_html('idcommissione');
$denominazione = stringa_html('denominazione');
$nomepresidente = stringa_html('nomepresidente');
$cognomepresidente = stringa_html('cognomepresidente');
$idsegretario=stringa_html('idsegretario');
$tbl_docenti = stringa_html('docenti');

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}



$titolo = "Inserimento commissione";
$script = "";
stampa_head($titolo,"",$script,"E");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$querydel="delete from tbl_escompcommissioni where idcommissione=$idcommissione";
mysqli_query($con,inspref($querydel));
$querydel="delete from tbl_escommissioni where idescommissione=$idcommissione";
mysqli_query($con,inspref($querydel));
if ($denominazione!='del')
{
    if ($idcommissione!=1000000000)
        $sql = "INSERT INTO tbl_escommissioni(idescommissione,denominazione,nomepresidente,cognomepresidente,idsegretario) values ('$idcommissione','$denominazione','$nomepresidente','$cognomepresidente','$idsegretario')";
    else
        $sql = "INSERT INTO tbl_escommissioni(denominazione,nomepresidente,cognomepresidente,idsegretario) values ('$denominazione','$nomepresidente','$cognomepresidente','$idsegretario')";
    mysqli_query($con, inspref($sql)) or die ("Errore:" . inspref($sql, false));
    $newidcommissione = mysqli_insert_id($con);
    $inseritosegretario=false;
    if ($tbl_docenti)
    {
        foreach ($tbl_docenti as $doc)
        {
            if ($idsegretario==$doc) $inseritosegretario=true;
            $sql = "INSERT INTO tbl_escompcommissioni(idcommissione,iddocente) values ('$newidcommissione','$doc')";
            mysqli_query($con, inspref($sql)) or die ("Errore:" . inspref($sql, false));

        }
    }
    if ((!$inseritosegretario) & ($idsegretario!=0))
    {
        $sql = "INSERT INTO tbl_escompcommissioni(idcommissione,iddocente) values ('$newidcommissione','$idsegretario')";
        mysqli_query($con, inspref($sql)) or die ("Errore:" . inspref($sql, false));
    }
}
else
{
    $newidcommissione='';
}

print "<form method='post' id='formgru' action='../esame3m/commissione.php'>
			  <input type='hidden' name='idescommissione' value='$newidcommissione'>
              <input type='hidden' name='registrata' value='1'>
		 </form> 
		 <SCRIPT language='JavaScript'>
		 {
				document.getElementById('formgru').submit();
		  }
		 </SCRIPT>";


mysqli_close($con);
stampa_piede();


