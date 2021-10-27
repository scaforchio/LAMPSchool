<?php

require_once '../lib/req_apertura_sessione.php';

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

// DA SOSTITUIRE CON PARAMETRO
//$memdati='db'; // Oppure 'hd' (Database o HardDisk) Funzionante da estendere a PDL, Prog e Relazioni

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$idcircolare = stringa_html('idcircolare');

$tipo = stringa_html('tipo');

$titolo = "Aggiornamento lista di distribuzione docenti";
$script = "";
stampa_head($titolo, "", $script, "MSPA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


// ELIMINO LA PRECEDENTE LISTA DI DISTRIBUZIONE
// VERIFICO PER OGNI DOCENTE SE E' DA INSERIRE NELLA LISTA DI DISTRIBUZIONE
$query = "select iddocente from tbl_docenti";
$ris = eseguiQuery($con, $query);
while ($rec = mysqli_fetch_array($ris))
{

    $nomecb = "cb" . $rec['iddocente'];
    $ins = stringa_html($nomecb);
    // print "tttt ".$nomecb." - ".$ins;
    $query = "select * from tbl_diffusionecircolari where idcircolare=$idcircolare and idutente=" . $rec['iddocente'];
    $ris2 = eseguiQuery($con, $query);
    if (mysqli_num_rows($ris2) == 0)
    {
        if ($ins == 'yes')
        {
            $query = "insert into tbl_diffusionecircolari(idutente,idcircolare) 
		        values (" . $rec['iddocente'] . ",$idcircolare)";
            eseguiQuery($con, $query);
        }
    } else
    {
        if ($ins != 'yes')
        {
            $query = "delete from tbl_diffusionecircolari where idcircolare=$idcircolare and idutente=" . $rec['iddocente'];
            eseguiQuery($con, $query);
        }
    }
}

print "
                 <form method='post' id='formdoc' action='../circolari/circolari.php'>
                 <input type='hidden' name='destinatari' value='$tipo'>
                 </form> 
                 <SCRIPT language='JavaScript'>
                 {
                     document.getElementById('formdoc').submit();
                 }
                 </SCRIPT>";

mysqli_close($con);
stampa_piede();


