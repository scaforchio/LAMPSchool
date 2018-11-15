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

$docente = stringa_html('docente');

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Visualizzazione orario lezioni di un docente";
$script = "";
stampa_head($titolo, "", $script, "PS");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


print ("
   <form method='post' action='visoraridocenti.php' name='ora'>
   
   <p align='center'>
   <table align='center' border='1'>

      <tr class='prima'>
      <td colspan='2' align='center'><b>Docente</b>");

//  $sqld= "SELECT * FROM tbl_docenti WHERE NOT sostegno ORDER BY cognome, nome";
$sqld = "SELECT * FROM tbl_docenti ORDER BY cognome, nome";
$resd = mysqli_query($con, inspref($sqld));
if (!$resd)
{
    print ("<br/> <br/> <br/> <h2>Impossibile visualizzare i dati </h2>");
} else
{
    print ("<select name='docente' ONCHANGE='ora.submit();'>");
    print ("<option>");
    while ($datal = mysqli_fetch_array($resd))
    {
        print("<option value='");
        print($datal['iddocente']);
        print("'");
        if ($docente == $datal['iddocente'])
        {
            print " selected";
        }
        print ">";
        print($datal['cognome']);
        print("&nbsp;");
        print($datal['nome']);
    }
}
print("</select> </td> </tr>");
print("</table>");
print "</form>";
if ($docente != '')
{

    $nominativo = estrai_dati_docente($docente, $con);
    $maildocente = estrai_mail_docente($docente, $con);



    $query = "SELECT * FROM tbl_ooodocentilezioni, tbl_ooodocenti, tbl_ooolezioni, tbl_ooomaterie, tbl_oooaulelezioni,tbl_oooaule, tbl_oooclassilezioni, tbl_oooclassi 
    WHERE tbl_ooodocentilezioni.idlezione=tbl_ooolezioni.idlezione AND tbl_ooodocentilezioni.iddocente=tbl_ooodocenti.iddocente AND tbl_ooolezioni.idmateria=tbl_ooomaterie.idmateria AND tbl_ooolezioni.idlezione=tbl_oooaulelezioni.idlezione AND tbl_oooaulelezioni.idaula=tbl_oooaule.idaula AND
tbl_oooclassilezioni.idlezione=tbl_ooolezioni.idlezione AND
tbl_oooclassilezioni.idclasse=tbl_oooclassi.idclasse AND
emaildocente='$maildocente'";

    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    if (mysqli_num_rows($ris) == 0)
        print "<br><center><b>ORARIO NON PRESENTE PER DOCENTE CON MAIL $maildocente</b></center><br><br>";
    else
    {

        print "<br><center><b>ORARIO SETTIMANALE DEL DOCENTE $nominativo</b></center><br><br>";

        print "<table border='1' align='center'>";
        print "<tr class='prima'><td>&nbsp</td><td align=center>LUN</td><td align=center>MAR</td><td align=center>MER</td><td align=center>GIO</td><td align=center>VEN</td><td align=center>SAB</td></tr>";

        for ($o = 0; $o < $numeromassimoore; $o++)
        {
            // print "<tr><td>".orainizio($o,1,$con)." - ".orafine($o,1,$con)."</td>";
            $ora = $o + 1;
            print "<tr><td class='prima'><b>$ora</b></td>";
            $oraprec = $o - 1;
            for ($g = 0; $g < $giornilezsett; $g++)
            {
                $query = "SELECT * FROM tbl_ooodocentilezioni, tbl_ooodocenti, tbl_ooolezioni, tbl_ooomaterie, tbl_oooaulelezioni,tbl_oooaule, tbl_oooclassilezioni, tbl_oooclassi 
            WHERE tbl_ooodocentilezioni.idlezione=tbl_ooolezioni.idlezione AND tbl_ooodocentilezioni.iddocente=tbl_ooodocenti.iddocente AND tbl_ooolezioni.idmateria=tbl_ooomaterie.idmateria AND tbl_ooolezioni.idlezione=tbl_oooaulelezioni.idlezione AND tbl_oooaulelezioni.idaula=tbl_oooaule.idaula AND
            tbl_oooclassilezioni.idlezione=tbl_ooolezioni.idlezione AND
            tbl_oooclassilezioni.idclasse=tbl_oooclassi.idclasse AND
            emaildocente='$maildocente' AND
            idgiorno='$g' AND
            (idora='$o' OR (idora='$oraprec' AND durata>60))";

                $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
                if ($rec = mysqli_fetch_array($ris))
                    print ("<td align='center'>" .inspref($query). $rec['nomeclasse'] . "<br>" . $rec['nomemateria'] . "<br>" . $rec['nomeaula'] . "</td>");
                else
                    print "<td></td>";
            }
            print "</tr>";
        }
        print "</table>";
    }
}


mysqli_close($con);
stampa_piede("");



