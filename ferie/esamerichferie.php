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

$titolo = "Esame richieste ferie";
$script = "";
stampa_head($titolo, "", $script, "PSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$nominativo = estrai_dati_docente($_SESSION['idutente'], $con);
if ($tipoutente == 'P')
{
    print "<br><center><b>ESAME RICHIESTE ASTENSIONE DAL LAVORO</b></center><br><br>";
    print "<table border='1' align='center'>";
    print "<tr class='prima'><td>Prot.</td><td>Docente</td><td>Richiesta</td><td>Concessione</td></tr>";
    // TTTT
    $query = "select * from tbl_richiesteferie where isnull(concessione) or concessione=2 order by idrichiestaferie desc";
    $ris = mysqli_query($con, inspref($query)) or die("Errore: $query <br>".mysqli_error($con));
    while ($rec = mysqli_fetch_array($ris))
    {
        print "<tr>";
        $prot = $rec['idrichiestaferie'];
        print "<td>$prot</td>";
        
        $totaleore=calcolaOrePermesso($rec['iddocente'],$con);
        $giorniferie = calcolaGiorniFerie($rec['iddocente'],$con);
        $giorniperm = calcolaGiorniPermesso($rec['iddocente'],$con);
        
        
        
        print "<td>" . estrai_dati_docente($rec['iddocente'], $con) ."<small><br><br>PRECEDENTI:<br>Ore perm.: $totaleore <br>Ferie: $giorniferie <br>Perm.: $giorniperm <big></td>";
        require "../lib/req_prepara_stringa_sintetica_richiesta.php";
        
    }
    $query = "select * from tbl_richiesteferie where concessione=0 or concessione=1 order by idrichiestaferie desc";
   
    $ris = mysqli_query($con, inspref($query)) or die("Errore: $query");
    while ($rec = mysqli_fetch_array($ris))
    {
        print "<tr>";
        $prot = $rec['idrichiestaferie'];
        print "<td>$prot</td>";
        print "<td>" . estrai_dati_docente($rec['iddocente'], $con) . "</td>";
        require "../lib/req_prepara_stringa_sintetica_richiesta.php";
        
    }
    print "</table>";
    print "<br>";
}
else
{
    print "<br><center><b>ESITO PROPRIE RICHIESTE ASTENSIONE DAL LAVORO</b></center><br><br>";
    print "<table border='1' align='center'>";
    print "<tr class='prima'><td>Prot.</td><td>Docente</td><td>Richiesta</td><td>Concessione</td></tr>";
    $query = "select * from tbl_richiesteferie where iddocente=$iddocente and (concessione<>9 or isnull(concessione)) order by idrichiestaferie desc";
    $ris = mysqli_query($con, inspref($query)) or die("Errore: $query");
    while ($rec = mysqli_fetch_array($ris))
    {
        print "<tr>";
        $prot = $rec['idrichiestaferie'];
        print "<td>$prot</td>";
        print "<td>" . estrai_dati_docente($rec['iddocente'], $con) . "</td>";
        // PREPARAZIONE STRINGA SINTETICA RICHIESTA
        $testocompleto = $rec['testomail'];
        //$posperiodo = strpos($testocompleto,"", $testocompleto)
        //str_replace("");
        print "<td><small><small>$testocompleto<big><big></td>";
        $concesso = $rec['concessione'];
        print "<td align='center' valign='middle'>";
        if ($concesso == NULL)
            print "Non ancora esaminata!<br><br><a href='annullarichiestaferie.php?prot=$prot'>ANNULLA</a></td>";
        else
        if ($concesso == 1)
            print "Accettata!</td>";
        else
        if ($concesso == 0)
            print "Rifiutata!</td>";
        else
            print "Recarsi dal preside per chiarimenti!</td>";

        print "</tr>";
    }
    print "</table>";
    print "<br>";
}


mysqli_close($con);
stampa_piede("");
