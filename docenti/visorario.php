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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
//require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();
// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();

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
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$nominativo = estrai_dati_docente($_SESSION['idutente'], $con);
$maildocente = estrai_mail_docente($_SESSION['idutente'], $con);



$query = "SELECT * FROM tbl_ooodocentilezioni, tbl_ooodocenti, tbl_ooolezioni, tbl_ooomaterie, tbl_oooaulelezioni,tbl_oooaule, tbl_oooclassilezioni, tbl_oooclassi 
    WHERE tbl_ooodocentilezioni.idlezione=tbl_ooolezioni.idlezione AND tbl_ooodocentilezioni.iddocente=tbl_ooodocenti.iddocente AND tbl_ooolezioni.idmateria=tbl_ooomaterie.idmateria AND tbl_ooolezioni.idlezione=tbl_oooaulelezioni.idlezione AND tbl_oooaulelezioni.idaula=tbl_oooaule.idaula AND
tbl_oooclassilezioni.idlezione=tbl_ooolezioni.idlezione AND
tbl_oooclassilezioni.idclasse=tbl_oooclassi.idclasse AND
emaildocente='$maildocente'";

$ris = eseguiQuery($con,$query);
if (mysqli_num_rows($ris) == 0)
    print "<br><center><b>ORARIO NON PRESENTE PER DOCENTE CON MAIL $maildocente</b></center><br><br>";
else
{

    print "<br><center><b>ORARIO SETTIMANALE DEL DOCENTE $nominativo</b></center><br><br>";
    
    print "<table border='1' align='center'>";
    print "<tr class='prima'><td>&nbsp</td><td align=center>LUN</td><td align=center>MAR</td><td align=center>MER</td><td align=center>GIO</td><td align=center>VEN</td><td align=center>SAB</td></tr>";
    
    for ($o=0;$o<$numeromassimoore;$o++)
    {
       // print "<tr><td>".orainizio($o,1,$con)." - ".orafine($o,1,$con)."</td>";
        $ora=$o+1;
        print "<tr><td class='prima'><b>$ora</b></td>";   
        $oraprec=$o-1;
    for ($g=0;$g<$giornilezsett;$g++)
    {
        $query="SELECT * FROM tbl_ooodocentilezioni, tbl_ooodocenti, tbl_ooolezioni, tbl_ooomaterie, tbl_oooaulelezioni,tbl_oooaule, tbl_oooclassilezioni, tbl_oooclassi 
            WHERE tbl_ooodocentilezioni.idlezione=tbl_ooolezioni.idlezione AND tbl_ooodocentilezioni.iddocente=tbl_ooodocenti.iddocente AND tbl_ooolezioni.idmateria=tbl_ooomaterie.idmateria AND tbl_ooolezioni.idlezione=tbl_oooaulelezioni.idlezione AND tbl_oooaulelezioni.idaula=tbl_oooaule.idaula AND
            tbl_oooclassilezioni.idlezione=tbl_ooolezioni.idlezione AND
            tbl_oooclassilezioni.idclasse=tbl_oooclassi.idclasse AND
            emaildocente='$maildocente' AND
            idgiorno='$g' AND
            (idora='$o' OR (idora='$oraprec' AND durata>60))";
            
        $ris=eseguiQuery($con,$query);
        if($rec=mysqli_fetch_array($ris))
            print ("<td align='center'>".$rec['nomeclasse']."<br>".$rec['nomemateria']."<br>".$rec['nomeaula']."</td>");
        else
            print "<td></td>";
            
        
    }
       print "</tr>";
    }
    print "</table>";
    /*print "<table border='1' align='center'>";
    print "<tr class='prima'><td>Prot.</td><td>Docente</td><td>Periodo</td></tr>";
// TTTT
    $query = "select * from tbl_richiesteferie where concessione=1 order by idrichiestaferie desc";
    $ris = eseguiQuery($con,$query);
    while ($rec = mysqli_fetch_array($ris))
    {
        print "<tr>";
        $prot = $rec['idrichiestaferie'];
        print "<td>$prot</td>";
        print "<td>" . estrai_dati_docente($rec['iddocente'], $con) . "</td>";
        // PREPARAZIONE STRINGA SINTETICA RICHIESTA
        $periodo = $rec['subject'];
        //$posperiodo = strpos($testocompleto,"", $testocompleto)
        //str_replace("");
        print "<td><small><small>$periodo<big><big></td>";


        print "</tr>";
    }

    print "</table>";
    print "<br>"; */
}


mysqli_close($con);
stampa_piede("");



