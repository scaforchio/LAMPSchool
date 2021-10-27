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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
//require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();
// istruzioni per tornare alla pagina di login se non c'è una sessione valida


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Visualizzazione orario lezioni";
$script = "";
stampa_head($titolo, "", $script, "PSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$nominativo = estrai_dati_docente($_SESSION['idutente'], $con);
$maildocente = estrai_mail_docente($_SESSION['idutente'], $con);



$query = "SELECT * FROM tbl_ooodocentilezioni, tbl_ooodocenti, tbl_ooolezioni, tbl_ooomaterie, tbl_oooaulelezioni,tbl_oooaule, tbl_oooclassilezioni, tbl_oooclassi 
    WHERE tbl_ooodocentilezioni.idlezione=tbl_ooolezioni.idlezione AND tbl_ooodocentilezioni.iddocente=tbl_ooodocenti.iddocente AND tbl_ooolezioni.idmateria=tbl_ooomaterie.idmateria AND tbl_ooolezioni.idlezione=tbl_oooaulelezioni.idlezione AND tbl_oooaulelezioni.idaula=tbl_oooaule.idaula AND
tbl_oooclassilezioni.idlezione=tbl_ooolezioni.idlezione AND
tbl_oooclassilezioni.idclasse=tbl_oooclassi.idclasse AND
emaildocente='$maildocente'";

$ris = eseguiQuery($con, $query);
if (mysqli_num_rows($ris) == 0)
    print "<br><center><b>ORARIO NON PRESENTE PER DOCENTE CON MAIL $maildocente</b></center><br><br>";
else
{

    $lezioni = array();
    while ($rec = mysqli_fetch_array($ris))
    {
        $lezioni[$rec['idgiorno'] . " " . $rec['idora']]['nomemateria'] = $rec['nomemateria'];
        $lezioni[$rec['idgiorno'] . " " . $rec['idora']]['nomeclasse'] = $rec['nomeclasse'];
        $lezioni[$rec['idgiorno'] . " " . $rec['idora']]['nomeaula'] = $rec['nomeaula'];
        if ($rec['durata'] > 60)
        {
            $lezioni[$rec['idgiorno'] . " " . ($rec['idora'] + 1)]['nomemateria'] = $rec['nomemateria'];
            $lezioni[$rec['idgiorno'] . " " . ($rec['idora'] + 1)]['nomeclasse'] = $rec['nomeclasse'];
            $lezioni[$rec['idgiorno'] . " " . ($rec['idora'] + 1)]['nomeaula'] = $rec['nomeaula'];
        }
        if ($rec['durata'] > 120)
        {
            $lezioni[$rec['idgiorno'] . " " . ($rec['idora'] + 2)]['nomemateria'] = $rec['nomemateria'];
            $lezioni[$rec['idgiorno'] . " " . ($rec['idora'] + 2)]['nomeclasse'] = $rec['nomeclasse'];
            $lezioni[$rec['idgiorno'] . " " . ($rec['idora'] + 2)]['nomeaula'] = $rec['nomeaula'];
        }
    }
    /* foreach ($lezioni as $lez)
      {
      print $lez['nomemateria'];
      } */
    print "<br><center><b>ORARIO SETTIMANALE DEL DOCENTE $nominativo</b></center><br><br>";

    print "<table border='1' align='center'>";
    print "<tr class='prima'><td>&nbsp</td><td align=center>LUN</td><td align=center>MAR</td><td align=center>MER</td><td align=center>GIO</td><td align=center>VEN</td><td align=center>SAB</td></tr>";

    for ($o = 0; $o < $_SESSION['numeromassimoore']; $o++)
    {
        // print "<tr><td>".orainizio($o,1,$con)." - ".orafine($o,1,$con)."</td>";
        $ora = $o + 1;
        print "<tr><td class='prima'><b>$ora</b></td>";
        $oraprec = $o - 1;
        for ($g = 0; $g < $_SESSION['giornilezsett']; $g++)
        {
            //           
            $orasett = $g . " " . $o;
            if (isset($lezioni[$orasett]))
                print ("<td align='center'>" . $lezioni[$orasett]['nomeclasse'] . "<br>" . $lezioni[$orasett]['nomemateria'] . "<br>" . $lezioni[$orasett]['nomeaula'] . "</td>");
            else
                print "<td></td>";
        }
        print "</tr>";
    }
    print "</table>";
}


mysqli_close($con);
stampa_piede("");



