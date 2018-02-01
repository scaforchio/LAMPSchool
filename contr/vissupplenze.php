<?php session_start();


/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
GNU Affero General Public License come pubblicata 
dalla Free Software Foundation; sia la versione 3, 
sia (a vostra scelta) ogni versione successiva.

Questo programma è distribuito nella speranza che sia utile 
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di 
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE. 
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/

/*programma per la visualizzazione dei tbl_docenti
riceve in ingresso idcomnasc e idcomres Da in uscita iddocente*/
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"];
$idutente = $_SESSION["idutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Visualizzazione supplenze";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$iddocente = stringa_html('iddocente');

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

//
// SELEZIONE DOCENTE
//

print ' <form method="post" name="lezioni" action="vissupplenze.php">
   
   <p align="center">
   <table align="center">

      <tr>
      <td colspan="2" align="center"><b>Docente</b>';

$sqld = "SELECT * FROM tbl_docenti ORDER BY cognome, nome";
$resd = mysqli_query($con, inspref($sqld)) or die (mysqli_error($con));
if ($resd)
{
    print ("<select name='iddocente' ONCHANGE='lezioni.submit()'>");
    print ("<option value=''>&nbsp;");
    while ($datal = mysqli_fetch_array($resd))
    {
        if ($iddocente == $datal['iddocente'])
        {
            print("<option value='");
            print($datal['iddocente']);
            print("' selected> ");
            print($datal['cognome']);
            print("&nbsp;");
            print($datal['nome']);
        }
        else
        {
            print("<option value='");
            print($datal['iddocente']);
            print("'> ");
            print($datal['cognome']);
            print("&nbsp;");
            print($datal['nome']);
        }
    }

}
print("</select> </td> </tr></table></form><br>");
if ($iddocente != '')
{
    $sql = "SELECT * FROM tbl_firme,tbl_lezioni ,tbl_classi,tbl_materie
       WHERE 
       tbl_firme.idlezione=tbl_lezioni.idlezione
       AND tbl_lezioni.idclasse=tbl_classi.idclasse
       
       AND tbl_lezioni.idmateria=tbl_materie.idmateria
       AND tbl_firme.iddocente=$iddocente
       AND tbl_lezioni.idmateria=0
       ORDER BY datalezione,orainizio";
}
else
{
   /* $sql = "SELECT * FROM tbl_firme,tbl_lezioni ,tbl_classi,tbl_materie
       WHERE 
       tbl_firme.idlezione=tbl_lezioni.idlezione
       AND tbl_lezioni.idclasse=tbl_classi.idclasse
       
       AND tbl_lezioni.idmateria=tbl_materie.idmateria
       AND tbl_lezioni.idmateria=0
       ORDER BY datalezione,orainizio"; */

       $sql = "SELECT sum(numeroore) as totoresupp, tbl_firme.iddocente, cognome, nome FROM tbl_firme,tbl_lezioni ,tbl_classi,tbl_materie,tbl_docenti
               WHERE tbl_firme.idlezione=tbl_lezioni.idlezione
               AND tbl_firme.iddocente=tbl_docenti.iddocente
               AND tbl_lezioni.idclasse=tbl_classi.idclasse
               AND tbl_lezioni.idmateria=tbl_materie.idmateria
               AND tbl_lezioni.idmateria=0
               GROUP BY tbl_firme.iddocente
               ORDER BY cognome, nome";


}
$result = mysqli_query($con, inspref($sql)) or die(mysqli_error($con));

if ($iddocente!=0)
{
    $totaleore = 0;
    print("<CENTER><table border=1>");
    print("<tr class='prima'><td><center><b> Data lezione</b></td>");
    print("<td><b>Docente</b></td>");
    print("<td><center><b> Classe e materia</b></td>");

    print("<td align='center'><b>Periodo</b> </td>");
    print("<td><center><b>Num. ore</b> </td></tr></b>");
    print("</tr>");
    $w = mysqli_num_rows($result);
    if ($w > 0)
    {


        while ($Data = mysqli_fetch_array($result))
        {
            $dl = data_italiana($Data['datalezione']);
            $cm = $Data['anno'] . " " . $Data['sezione'] . " " . $Data['specializzazione'] . " - " . $Data['denominazione'];
            $pe = $Data['orainizio'] . "->" . ($Data['orainizio'] - 1 + $Data['numeroore']);
            $idlez = $Data['idlezione'];
            $docente = estrai_dati_docente($Data['iddocente'], $con);
            $numeroore = $Data['numeroore'];
            print("<tr><td>$dl</td><td>$docente</td><td>$cm</td><td>$pe</td><td>$numeroore</td>");
            $totaleore += $numeroore;
//	 print("<td><center><a href='mod_lez.php?a=$idlez'> Modifica</a></td>");
//	 print("<td><center><a href='can_lez.php?a=$idlez'> Elimina</a></td></tr>");
        }
    }
    else
    {
        print("<tr BGCOLOR='#cccccc'><td colspan='11'> <center>Nessuna supplenza trovata</center></td></tr>");
    }
    print("</TABLE><br/>");
    print("<b>Totale ore di supplenza: $totaleore</b>");

    print("</CENTER>");
}
else
{
    $totaleore = 0;
    print("<CENTER><table border=1>");
    print("<tr class='prima'>");
    print("<td><b>Docente</b></td>");
    print("<td><center><b> Numero ore</b></td>");

    print("</tr>");
    $w = mysqli_num_rows($result);
    if ($w > 0)
    {


        while ($Data = mysqli_fetch_array($result))
        {

            $docente = $Data['cognome']." ".$Data['nome'];
            $numeroore = $Data['totoresupp'];
            print("<tr><td>$docente</td><td align='center'>$numeroore</td>");
            $totaleore += $numeroore;
//	 print("<td><center><a href='mod_lez.php?a=$idlez'> Modifica</a></td>");
//	 print("<td><center><a href='can_lez.php?a=$idlez'> Elimina</a></td></tr>");
        }
    }
    else
    {
        print("<tr BGCOLOR='#cccccc'><td colspan='11'> <center>Nessuna supplenza trovata</center></td></tr>");
    }
    print("</TABLE><br/>");
    print("<b>Totale ore di supplenza: $totaleore</b>");

    print("</CENTER>");


}

mysqli_close($con);
stampa_piede("");

