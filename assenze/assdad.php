<?php

session_start();

/*
  Copyright (C) 2015 Pietro Tamburrano
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo modificarlo
 * secondo i termini della 
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


// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idutente = $_SESSION['idutente'];

$classeregistro = $_SESSION['classeregistro'];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


// preparazione del link per tornare indietro nel registro di classe
$goback = goBackRiepilogoRegistro($con);

$titolo = "Visualizza situazione assenze DAD";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri)
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head($titolo, "", $script, "SDP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a>$goback[1] - $titolo", "", "$nome_scuola", "$comune_scuola");


$gio = stringa_html('gio');
$meseanno = stringa_html('meseanno');

$anno = substr($meseanno, 5, 4);
$mese = substr($meseanno, 0, 2);
$data = $anno . "-" . $mese . "-" . $gio;
$idclasse = stringa_html('idclasse');

$alunni = estrai_alunni_classe_data($idclasse, $data, $con);
$query = "select * from tbl_asslezione, tbl_materie, tbl_lezioni, tbl_alunni
              where 
              tbl_asslezione.idmateria=tbl_materie.idmateria
              and tbl_asslezione.idlezione=tbl_lezioni.idlezione
              and tbl_asslezione.idalunno=tbl_alunni.idalunno
              and tbl_asslezione.idalunno in ($alunni)
              and data='$data'
              order by tbl_lezioni.orainizio";
//print inspref($query);
$ris = eseguiQuery($con, $query);
if (mysqli_num_rows($ris) > 0)
{
    print "<table border='1' align='center'><tr class='prima'><td>Ore lezione (da - a)</td><td>Materia</td><td>Alunno</td><td>Ore assenza</td></tr>";
    
    while ($nom = mysqli_fetch_array($ris))
    {
        $orafine=$nom['orainizio']+$nom['numeroore']-1;
        print "<tr><td align='center'>" . $nom['orainizio'] . " - $orafine</td><td>" . $nom['denominazione'] . "</td><td>" . $nom['cognome'] . " " . $nom['nome'] . "</td><td>" . $nom['oreassenza'] . "</td></tr>";
    }
    print "</table>";
}
else
{
    print "<br><br><br><center><b>Non ci sono assenze!</b></center>";
    
}
    

mysqli_close($con);
stampa_piede("");
