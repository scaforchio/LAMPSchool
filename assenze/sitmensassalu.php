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

//
//    VISUALIZZAZIONE DELLA SITUAZIONE DELLE ASSENZE E DEI RITARDI
//    PER I GENITORI 
//


require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
// require_once '../lib/db / query.php';
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
// $lQuery = LQuery::getIstanza();
//  istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$codalunno = stringa_html('idalunno');


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Situazione mensile assenze alunni";
$script = "";
stampa_head($titolo, "", $script, "MSAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);



// Dati utili al disegno di questa pagina
/* $rs0 = $lQuery->selectmax('tbl_assenze', 'data', 'ultimoaggiornamento');
  $rs1 = $lQuery->selectstar('tbl_alunni', 'idalunno=?', array($codalunno));
  $rs2 = $lQuery->selectcount('tbl_assenze', 'numerotblassenze', 'idalunno=?', array($codalunno));
  $rs3 = $lQuery->selectcount('tbl_ritardi', 'numerotblritardi', 'idalunno=?', array($codalunno));
  $rs4 = $lQuery->selectcount('tbl_usciteanticipate', 'numerouscite', 'idalunno=?', array($codalunno));
  $rs5 = $lQuery->selectstar('tbl_assenze', 'idalunno=?', array($codalunno), 'data desc');
  $rs6 = $lQuery->selectstar('tbl_ritardi', 'idalunno=?', array($codalunno), 'data desc');
  $rs7 = $lQuery->selectstar('tbl_usciteanticipate', 'idalunno=?', array($codalunno), 'data desc');
 */


// prelevamento data ultima assenza
// $val0 = $rs0->fetch();
// $ultimoaggiornamento = $val0["ultimoaggiornamento"];
// print "<center><i>Dati aggiornati al ".data_italiana($ultimoaggiornamento).".</i></center>
//print "<table border='1' align='center' width='50%'>";

// prelevamento dati alunno
$query = "SELECT * FROM tbl_alunni LEFT JOIN tbl_classi
         ON tbl_alunni.idclasse=tbl_classi.idclasse
         ORDER BY cognome,nome,anno, sezione, specializzazione";

$ris = eseguiQuery($con, $query);
//print "tttt ".inspref($query);
print "<form name='selealu' action='sitmensassalu.php' method='post'>";
print "<table align='center'>";
print "<tr><td>Alunno</td>";
print "<td>";
print "<select name='idalunno' ONCHANGE='selealu.submit();'><option value=''>&nbsp;</option>";
while ($rec = mysqli_fetch_array($ris))
{
    if ($codalunno == $rec['idalunno'])
    {
        $sele = " selected";
    } else
    {
        $sele = "";
    }
    print ("<option value='" . $rec['idalunno'] . "'$sele>" . $rec['cognome'] . " " . $rec['nome'] . " (" . $rec['datanascita'] . ") - " . $rec['anno'] . " " . $rec['sezione'] . " " . $rec['specializzazione'] . "</option>");
}
print "
 </select> 
 </td>
 
 </tr>";

print "</table></form>";



if ($codalunno != '')
{

   

    print "<br><br><center><b>SITUAZIONE MENSILE<br><br>";
    print "<table border=1 align=center><tr class='prima'><td>Mese</td><td>Ass.</td><td>Rit.</td><td>Usc.</td></tr>";
    for ($i = 9; $i <= 12; $i++)
    {
        print "<tr><td>$i</td>";
        $q="select count(*) as numeroassenze from tbl_assenze where idalunno=$codalunno and month(data)=$i";
        $rs = eseguiQuery($con, $q);
        $rec = mysqli_fetch_array($rs);
        $numev = $rec['numeroassenze'];
        print "<td>$numev</td>";
        $q="select count(*) as numeroritardi from tbl_ritardi where idalunno=$codalunno  and month(data)=$i";
        
        $rs = eseguiQuery($con, $q);
        $rec = mysqli_fetch_array($rs);
        $numev = $rec['numeroritardi'];
        print "<td>$numev</td>";
        $q="select count(*) as numerouscite from tbl_usciteanticipate where idalunno=$codalunno  and month(data)=$i";
        $rs = eseguiQuery($con, $q);
        $rec = mysqli_fetch_array($rs);
        $numev = $rec['numerouscite'];
        print "<td>$numev</td>";
        print "</tr>";
    }

    for ($i = 1; $i <= 6; $i++)
    {
        print "<tr><td>$i</td>";
        $q="select count(*) as numeroassenze from tbl_assenze where idalunno=$codalunno and month(data)=$i";
        $rs = eseguiQuery($con, $q);
        $rec = mysqli_fetch_array($rs);
        $numev = $rec['numeroassenze'];
        print "<td>$numev</td>";
        $q="select count(*) as numeroritardi from tbl_ritardi where idalunno=$codalunno  and month(data)=$i";
        
        $rs = eseguiQuery($con, $q);
        $rec = mysqli_fetch_array($rs);
        $numev = $rec['numeroritardi'];
        print "<td>$numev</td>";
        $q="select count(*) as numerouscite from tbl_usciteanticipate where idalunno=$codalunno  and month(data)=$i";
        $rs = eseguiQuery($con, $q);
        $rec = mysqli_fetch_array($rs);
        $numev = $rec['numerouscite'];
        print "<td>$numev</td>";
        print "</tr>";
    }
    print "</table>";
}

stampa_piede();



