<?php

session_start();

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

//Visualizzazione classi
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Gestione giornate colloqui";
$script = "";
stampa_head($titolo, "", $script, "MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Gestione giornate colloqui", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$query = "SELECT * FROM tbl_giornatacolloqui"
        . " where data>'" . date('Y-m-d') . "'"
        . " order by data";

$result = eseguiQuery($con, $query);

print " <center>
        <table border=1 align='center'>
        <tr class='prima'>
        <td align='center'>Data</td>
        <td align='center'>Elimina</td>
        <td align='center'>Modifica classi</td>
        </tr>";

while ($row = mysqli_fetch_array($result))
{
    $formatodata = substr($row['data'], 8, 2) . '/' . substr($row['data'], 5, 2) . '/' . substr($row['data'], 0, 4);
    $idgiornata = $row['idgiornatacolloqui'];

    print "<tr>
                  <td align='left'> $formatodata </td>";
    if (!verificaPresenzaAppuntamenti($con, $row['idgiornatacolloqui']))
        print "        <td align='center'> <a href= './eliminagiornata.php?idgiornata=$idgiornata'> <img src='../immagini/delete.png'> </a> </td>
                  <td align='center'> <a href= './salvaclassicoll.php?idgiornata=$idgiornata'> <img src='../immagini/edit.png'> <a/> </td>";
    else
        print "<td>&nbsp;</td><td>&nbsp;</td>";
    print "</tr>";
};

print " </table>
        <br>
        <a href= './insdatacoll.php'><input type='submit' value='Nuova'></a>
        </center>";

stampa_piede("");
mysqli_close($con);

function verificaPresenzaAppuntamenti($con, $idgiornatacolloqui)
{
    $query="select * from tbl_slotcolloqui where idgiornatacolloqui=$idgiornatacolloqui";
    $ris=eseguiQuery($con,$query);
    $numcoll=mysqli_num_rows($ris);
    
    if ($numcoll==0)
        return false;
    else
        return true;
}