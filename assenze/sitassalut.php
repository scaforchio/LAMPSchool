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
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
// $lQuery = LQuery::getIstanza();
//  istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Situazione assenze alunni";
$script = "";
stampa_head_new($titolo, "", $script, "MSPDLT");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$codalunno = $_SESSION['idstudente'];
$idclasse= estrai_classe_alunno($codalunno, $con);

$rs1 = eseguiQuery($con,"select * from tbl_alunni where idalunno=$codalunno");
$rs2 = eseguiQuery($con,"select count(*) as numerotblassenze from tbl_assenze where idalunno=$codalunno");
$rs3 = eseguiQuery($con,"select count(*) as numerotblritardi from tbl_ritardi where idalunno=$codalunno");
$rs4 = eseguiQuery($con,"select count(*) as numerouscite from tbl_usciteanticipate where idalunno=$codalunno");
$rs5 = eseguiQuery($con,"select * from tbl_assenze where idalunno=$codalunno order by data desc");
$rs6 = eseguiQuery($con,"select * from tbl_ritardi where idalunno=$codalunno order by data desc");
$rs7 = eseguiQuery($con,"select * from tbl_usciteanticipate where idalunno=$codalunno order by data desc");
$query="select sum(oreassenza) as numerooreassdad from tbl_asslezione where idalunno=$codalunno and data not in (select data from tbl_assenze where idalunno=$codalunno) and data in (select datadad from tbl_dad where idclasse=$idclasse)";
$rs8 = eseguiQuery($con,$query);
$rs9 = eseguiQuery($con,"select * from tbl_asslezione where idalunno=$codalunno "
                . " and data not in (select data from tbl_assenze where idalunno=$codalunno)"
                    . " and data in (select datadad from tbl_dad where idclasse=$idclasse)"
                . " order by data ");

print "<table border='1' align='center' class='table table-striped table-bordered' width='50%'>";

// prelevamento dati alunno

if ($rs1)
{

    if ($val1 = mysqli_fetch_array($rs1))
        echo ' 
 <tr>
  <td colspan="3"><b>Alunno: ' . $val1["cognome"] . ' ' . $val1["nome"] . '</b></td>
 </tr>';
}

// conteggio tbl_assenze

if ($val2 = mysqli_fetch_array($rs2))
    echo ' 
 <tr>
  <td colspan="3"><b>Assenze: ' . $val2["numerotblassenze"] . '</b></td>
 </tr>';

// conteggio tbl_ritardi

if ($rs3)
{

    if ($val3 = mysqli_fetch_array($rs3))
        echo ' 
 <tr>
  <td colspan="3"><b>Ritardi: ' . $val3["numerotblritardi"] . '</b></td>
 </tr>';
}

// conteggio uscite anticipate

if ($val4 = mysqli_fetch_array($rs4))
    echo ' 
 <tr>
  <td colspan="3"><b>Uscite anticipate: ' . $val4["numerouscite"] . '</b></td>
 </tr>';

// conteggio ore assenza dad
// conteggio uscite anticipate

if ($val8 = mysqli_fetch_array($rs8))
    echo ' 
 <tr>
  <td colspan="3"><b>Ore assenza in DAD: ' . $val8["numerooreassdad"] . '</b></td>
 </tr>';

print "
 <tr align=center style='font-style: italic;'><td width='33%'>Assenze</td><td width='33%'>Ritardi</td><td width='33%'>Uscite</td></tr>";

// elenco tbl_assenze
echo "
 <tr><td valign='top'>";

if ($rs5)
{

    while ($val5 = mysqli_fetch_array($rs5))
    {
        $data = $val5["data"];
        $giustificata = "";
        if($val5["giustifica"] == 1){
            $giustificata = "<span style=\"color: green;\"> (Giustificata) </span>";
        }else{
            $giustificata = "<span style=\"color: red;\" > (NON GIUSTIFICATA)</span>";
        }
        echo ' ' . data_italiana($data) . ' ' . giorno_settimana($data) . " | " . $giustificata . '<br/>';
    }
}
echo "</td>";

// elenco tbl_ritardi
echo "<td valign='top'>";

if ($rs6)
{

    while ($val6 = mysqli_fetch_array($rs6))
    {
        $data = $val6["data"];
        if($val6["giustifica"] == 1){
            $giustificato = "<span style=\"color: green;\"> (Giustificato) </span>";
        }else{
            $giustificato = "<span style=\"color: red;\" > (NON GIUSTIFICATO)</span>";
        }
        echo ' ' . data_italiana($data) . ' ' . giorno_settimana($data) . " | " . $giustificato . '<br/>';
    }
}
echo "</td>";

// elenco uscite
echo "<td valign='top'>";

if ($rs7)
{

    while ($val7 = mysqli_fetch_array($rs7))
    {
        $data = $val7["data"];
        echo ' ' . data_italiana($data) . ' ' . giorno_settimana($data) . '<br/> ';
        $query = "select * from tbl_autorizzazioniuscite where idalunno=$codalunno and data='$data'";
        $ris = eseguiQuery($con, $query);
        if ($rec = mysqli_fetch_array($ris))
            print "<small>" . $rec['testoautorizzazione'] . "</small><br>";
    }
}
echo "
     </td>
    </tr>";

if ($rs9)
{
    print "<tr><td colspan=3>";
    print "<center><b>Assenze orarie a lezioni</b></center><br>";
    while ($val9 = mysqli_fetch_array($rs9))
    {
        
        $data = $val9["data"];
        echo ' ' . giorno_settimana($data)  . ' ' .data_italiana($data)  . ' ';
        print "Ore assenza: ".$val9['oreassenza'];
        print " Materia: ". decodifica_materia(estrai_materia_lezione($val9['idlezione'], $con),$con);
        $giust = ($val9['giustifica']==1)?"Giust.":"Non giust.";
        print " ($giust)<br>";
        $query = "select * from tbl_autorizzazioniuscite where idalunno=$codalunno and data='$data'";
        $ris = eseguiQuery($con, $query);
        if ($rec = mysqli_fetch_array($ris))
            print "<small>" . $rec['testoautorizzazione'] . "</small><br>";
    }
    print "</td></tr>";
}


echo "   </table>";

stampa_piede_new();



