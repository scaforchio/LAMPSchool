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


/*programma per la modifica dei tbl_docenti
riceve in ingresso i dati del docente*/


// istruzioni per tornare alla pagina di login 
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");


$titolo = "Modifica lezione";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_lez.php'>ELENCO LEZIONI</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<H1>connessione al server mysql fallita</H1>");
    exit;
}
$DB = true;
if (!$DB)
{
    print("<H1>connessione al database fallita</H1>");
    exit;
}

$err = 0;

$idlezione = stringa_html('idlezione');
$iddocente = stringa_html('iddocente');
$giorno = stringa_html('giorno');
$meseanno = stringa_html('meseanno');
$periodo = stringa_html('periodo');
$mese = substr($meseanno, 0, 2);
$anno = substr($meseanno, 5, 4);
$dataitaliana = $giorno . "/" . $mese . "/" . $anno;
$datadb = $anno . "-" . $mese . "-" . $giorno;

$possep = strpos($periodo, "-");

$inilez = substr($periodo, 0, $possep);
$finlez = substr($periodo, $possep + 1);

$numeroore = $finlez - $inilez + 1;

// VERIFICO SOVRAPPOSIZIONI DI FIRME DI DOCENTI DIVERSI NELLA STESSA CLASSE
// PER MATERIE DIVERSE

$query = "select idclasse,idmateria from tbl_lezioni
         where idlezione='$idlezione'";
// print inspref($query);
$ris = mysqli_query($con, inspref($query)) or die (mysqli_error($con));
$val = mysqli_fetch_array($ris);
$idclasse = $val['idclasse'];
$idmateria = $val['idclasse'];
// Creo un array per verificare le ore già impegnate da lezioni
$oredisp = array();
$oredisp[] = 9;
for ($i = 1; $i <= $numeromassimoore; $i++)
    $oredisp[] = 0;

$query = "select orainizio,numeroore from tbl_lezioni
	       where datalezione='$datadb' 
	        and idclasse='$idclasse'
	        and idlezione<>'$idlezione'";
// print inspref($query);
$rislezcla = mysqli_query($con, inspref($query)) or die (mysqli_error($con));
while ($vallezcla = mysqli_fetch_array($rislezcla))
{
    $inizio = $vallezcla['orainizio'];
    $fine = $vallezcla['orainizio'] + $vallezcla['numeroore'] - 1;
    // print "<br>".$idlezione." ".$inizio." ".$fine." ".$inilez." ".$finlez;
    for ($i = $inizio; $i <= $fine; $i++)
    {
        $oredisp[$i] = 1;
    }

    if (occupata($oredisp, $inilez, $finlez))
    {
        print ("C'&egrave; gi&agrave; presente una lezione nelle ore indicate nella stessa classe!");
    }

}


// VERIFICO SOVRAPPOSIZIONI DI FIRME DELLO STESSO DOCENTE
/*   DA RIVEDERE
$query="select iddocente from tbl_firme 
         where idlezione='$idlezione' and iddocente";
        // print inspref($query);
$ris=mysqli_query($con,inspref($query)) or die (mysqli_error($con));
while ($val=mysqli_fetch_array($ris))
{
	$iddoc=$val['iddocente'];
	
	
   $oredisp=array();
   $oredisp[]=9;
   for($i=1;$i<=$numeromassimoore;$i++)
      $oredisp[]=0;
	$query = "select orainizio,numeroore from tbl_firme,tbl_lezioni
	        where tbl_firme.idlezione=tbl_lezioni.idlezione
	        and datalezione='$datadb' 
	        and tbl_firme.iddocente='$iddoc'
	        and tbl_firme.idlezione<>$idlezione";
	// print inspref($query);        
	$rislezdoc=mysqli_query($con,inspref($query)) or die (mysqli_error($con));
	while ($vallezdoc=mysqli_fetch_array($rislezdoc))
	{  // Creo un array per verificare le ore già impegnate da lezioni
      
		$inizio=$vallezdoc['orainizio'];
		$fine=$vallezdoc['orainizio']+$vallezdoc['numeroore']-1; 
		// print "<br>".$idlezione." ".$inizio." ".$fine." ".$inilez." ".$finlez;   
		for($i=$inizio;$i<=$fine;$i++)
		{
			$oredisp[$i]=1;
		}
		   
		if (occupata($oredisp,$inilez,$finlez))
		   print ("Sovrapposizione di lezioni per il docente: ".estrai_dati_docente($iddocente, $con).".");
	}    
      
}

*/


if (!checkdate($mese, $giorno, $anno))
{
    print ("<br><b>Data non valida!</b><br>");
}
else
{
    if (giorno_settimana($datadb) == "Dom")
    {
        print ("<br><b>Il giorno selezionato &egrave; una domenica!</b><br>");
    }
//else if (($anno.$mese.$giorno)>date("Ymd"))
//   print ("<Center> <big><big>Data selezionata maggiore della data odierna!<small><small> </center>");   
    else
    {

        $query = "update tbl_lezioni
	        set datalezione='$datadb',
	            numeroore='$numeroore',
	            orainizio='$inilez'
	        where idlezione=$idlezione";
        mysqli_query($con, inspref($query)) or die(mysqli_error($con));
        $query = "update tbl_asslezione
	        set oreassenza='$numeroore'
	        where oreassenza>'$numeroore'
	        and idlezione=$idlezione";

        mysqli_query($con, inspref($query)) or die(mysqli_error($con));

    }
}

$idclasse = estrai_classe_lezione($idlezione, $con);
$datalezione = estrai_data_lezione($idlezione, $con);
ricalcola_assenze_lezioni_classe($con, $idclasse, $datalezione);

print "<center><b>Modifica effettuata</b></center>";
print " <form method='post' action='vis_lez.php'>
          <input type='hidden' name='iddocente' value='$iddocente'>
          <p align='center'><input type='submit' value='OK' name='vis'></p>
        </form>";

stampa_piede("");
mysqli_close($con);

function occupata($oredisp, $i, $f)
{
    $occ = false;
    for ($k = $i; $k <= $f; $k++)
        if ($oredisp[$k] == 1)
        {
            $occ = true;
        }
    return $occ;
}


