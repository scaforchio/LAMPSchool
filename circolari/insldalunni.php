<?php

session_start();

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

$idcircolare = stringa_html('idcircolare');

$tipo = stringa_html('tipo');
$titolo = "Aggiornamento lista di distribuzione alunni";
$script = "";
stampa_head($titolo, "", $script, "MSPA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


// ELIMINO LA PRECEDENTE LISTA DI DISTRIBUZIONE
// $query="delete from tbl_diffusionecircolari where idcircolare=$idcircolare";
// eseguiQuery($con,$query); 
// VERIFICO PER OGNI ALUNNO SE E' DA INSERIRE NELLA LISTA DI DISTRIBUZIONE
$query = "select idalunno from tbl_alunni";
$ris = eseguiQuery($con, $query);
while ($rec = mysqli_fetch_array($ris))
{
    $nomecb = "cb" . $rec['idalunno'];
    $ins = stringa_html($nomecb);
    // print "tttt ".$nomecb." - ".$ins;
    if ($tipo == 'A' || $tipo == 'SA')
        $query = "select * from tbl_diffusionecircolari where idcircolare=$idcircolare and idutente=" . $rec['idalunno'];
    else
        $query = "select * from tbl_diffusionecircolari where idcircolare=$idcircolare and idutente=" . ($rec['idalunno'] + 2100000000);

    $ris2 = eseguiQuery($con, $query);
    if (mysqli_num_rows($ris2) == 0)
    {
        if ($ins == 'yes')
        {
            if ($tipo == 'L' || $tipo == 'SL')
                $query = "insert into tbl_diffusionecircolari(idutente,idcircolare) 
		        values (" . ($rec['idalunno'] + 2100000000) . ",$idcircolare)";
            else
                $query = "insert into tbl_diffusionecircolari(idutente,idcircolare) 
		        values (" . $rec['idalunno'] . ",$idcircolare)";
            eseguiQuery($con, $query);
        }
    }
    else
    {
        if ($ins != 'yes')
        {
            if ($tipo == 'L' || $tipo == 'SL')
                $query = "delete from tbl_diffusionecircolari where idcircolare=$idcircolare and idutente=" . ($rec['idalunno'] + 2100000000);
            else
                $query = "delete from tbl_diffusionecircolari where idcircolare=$idcircolare and idutente=" . $rec['idalunno'];
            eseguiQuery($con, $query);
        }
    }
}

print "
                 <form method='post' id='formalu' action='../circolari/circolari.php'>
                 <input type='hidden' name='destinatari' value='$tipo'>
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formalu').submit();
                 }
                 </SCRIPT>";

mysqli_close($con);
stampa_piede();


