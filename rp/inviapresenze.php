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
 
@require_once("../lib/funzioni.php");
$suffisso=stringa_html('suffisso');
@require_once("../php-ini".$suffisso.".php");
if ($suffisso!="") $suff=$suffisso."/"; else $suff="";

inserisci_log("LAMPSchool§".date('m-d|H:i:s')."§" .IndirizzoIpReale() . "§Trasmissione richiesta", $nomefilelog."rp",$suff);

if ($con=mysqli_connect($db_server,$db_user,$db_password,$db_nome))
{
    print ("trasmetti");
    //$_SESSION['abilitata']='yes';
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Trasmissione abilitata", $nomefilelog."rp",$suff);
}
else
{
    //$_SESSION['abilitata']='no';
    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Trasmissione non abilitata ".mysqli_error($con)."\n", $nomefilelog."rp",$suff);
    die ("Errore durante la connessione!");
}

  


