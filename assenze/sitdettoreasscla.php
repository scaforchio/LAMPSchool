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

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Situazione ore di assenza al " . data_italiana($dataassora);

stampa_head($titolo, "", "", "MSPD");



$nome = stringa_html('cl');
$but = stringa_html('visass');

$oreass = 1;



$menu = '<a href="../login/ele_ges.php">PAGINA PRINCIPALE</a> - SITUAZIONE DETTAGLIATA ORE DI ASSENZA AL ' . data_italiana($dataassora);
stampa_testata("$menu", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

print ('
   <form method="post" action="sitdettoreasscla.php" name="tbl_assenze">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="cl" ONCHANGE="tbl_assenze.submit()"> <option>&nbsp ');



$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$query = "select idclasse,anno,sezione,specializzazione from tbl_classi order by specializzazione, sezione, anno";
$ris = eseguiQuery($con, $query);
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
    if ($nome == $nom["idclasse"])
        print " selected";
    print ">";
    print ($nom["anno"]);
    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
}

echo('
      </SELECT>
      </td></tr>');


echo('
    </table>
 
    <table align="center">
      <td>');
//    <p align="center"><input type="submit" value="Visualizza tbl_assenze" name="b"></p>
echo('     </form></td>
   
</table><hr>
 
    ');



// print($nome." -   ". $g.$m.$a.$giornosettimana);

$idclasse = $nome;
$classe = "";
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


$query = 'select * from tbl_classi where idclasse="' . $idclasse . '" ';
$ris = eseguiQuery($con, $query);
if ($val = mysqli_fetch_array($ris))
{
    $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
    $anno = $val["anno"];
}

$query = 'select * from tbl_alunni where idclasse="' . $idclasse . '" order by cognome,nome,datanascita';
$ris = eseguiQuery($con, $query);

$c = mysqli_fetch_array($ris);
if ($c == NULL)
{
    echo '
                    <p align="center">
		    <font size=4 color="black">Nessun alunno presente nella classe ' . $nome . '</font>
                   ';
    exit;
}
echo '<p align="center">
          <font size=4 color="black">Ore assenza della classe ' . $classe . '</font>
          
          <table border=2 align="center">';

echo'
          <tr class=prima>
          
          <td><font size=1><b> Cognome </b></td>
          <td><font size=1><b> Nome  </b></td>
          <td><font size=1><b> Data di nascita </b></td>';


$query = "SELECT DISTINCT (denominazione)
             FROM oreassenza, tbl_materie
             WHERE oreassenza.idmateria = tbl_materie.idmateria
             AND idclasse =" . $idclasse . " AND DATA = '2011-03-31'
             ORDER BY tbl_materie.denominazione";
$ris = eseguiQuery($con, $query);
while ($val = mysqli_fetch_array($ris))
{
    print"<td width=50 align='center'><font size='1'>" . $val['denominazione'] . "</font></td>";
}
print "<td width=50 align='center'>TOTALE</td></tr>";


$query = 'select * from tbl_alunni where idclasse="' . $idclasse . '" order by cognome,nome,datanascita';
$ris = eseguiQuery($con, $query);
while ($val = mysqli_fetch_array($ris))
{
    echo ' 
             <tr>
                <td><font size=1><b> ' . $val["cognome"] . ' </b></td>
                <td><font size=1><b> ' . $val["nome"] . '    </b></td>
                <td><font size=1><b> ' . data_italiana($val["datanascita"]) . ' </b></td>
                
                ';
    $totore = 0;

    // Codice per ricerca ore tbl_assenze 
    $queryoreass = "SELECT * FROM oreassenza, tbl_materie 
                    WHERE oreassenza.idmateria=tbl_materie.idmateria AND 
                    idalunno = " . $val["idalunno"] . " AND DATA = '" . $dataassora . "' ORDER BY tbl_materie.denominazione";


    $risoreass = eseguiQuery($con, $queryoreass);
    while ($val2 = mysqli_fetch_array($risoreass))
    {
        $totore = $totore + $val2['numeroore'];
        print("<td align='center'><font size=1>" . $val2['numeroore'] . "</font></td>");
    }
    print("<td align='center'><font size=1><b>" . $totore . "</b></font></td></tr>");
}

mysqli_close($con);
stampa_piede("");

