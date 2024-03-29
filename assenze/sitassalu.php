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
// require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

// istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Situazione assenze alunno";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri)
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head($titolo, "", $script, "MSPD");
print "
<body>";

$codalunno = stringa_html('alunno');

// prelevamento dati alunno
// $rs = $lQuery->selectstar('tbl_alunni', 'idalunno=?', array($codalunno));
$query = "select * from tbl_alunni where idalunno=$codalunno";
$rs = eseguiQuery($con, $query);
if ($rs)
{
    $val = mysqli_fetch_array($rs); // ->fetch();

    echo '
<table border="1" align="center">
 <tr>
  <td colspan="3"><b>Alunno: ' . $val["cognome"] . ' ' . $val["nome"] . ' (' . decodifica_classe(estrai_classe_alunno($codalunno, $con), $con) . ')</b></td>
 </tr>';
}

// conteggio assenze
$query1 = "select count(*) as numeroassenze from tbl_assenze where idalunno='$codalunno'";
$ris1 = eseguiQuery($con, $query1); //$lQuery->query($query1);

if ($val1 = mysqli_fetch_array($ris1)) //->fetch())
{
// conteggio assenze non giustificate
    $query2 = "select count(*) as numeroassenze from tbl_assenze where idalunno='$codalunno' and (isnull(giustifica) or giustifica=0)";
    $ris2 = eseguiQuery($con, $query2); //$lQuery->query($query2);
    $val2 = mysqli_fetch_array($ris2); //$ris2->fetch();
    echo ' 
 <tr>
  <td colspan="3"><b>Assenze: ' . $val1["numeroassenze"] . ' <font color="red">(' . $val2["numeroassenze"] . ')</font></b></td>
 </tr>
';
}

// conteggio ritardi
$query3 = "select count(*) as numeroritardi from tbl_ritardi where idalunno='$codalunno'";
$ris3 = eseguiQuery($con, $query3); //$lQuery->query($query3);

if ($val3 = mysqli_fetch_array($ris3)) //$ris3->fetch())
{
// conteggio assenze non giustificate
    //  if ($_SESSION['giustifica_ritardi']=='yes')
    //  {
    $query4 = "select count(*) as numeroritardi from tbl_ritardi where idalunno='$codalunno' and (isnull(giustifica) or giustifica=0)";
    $ris4 = eseguiQuery($con, $query4);
    $val4 = mysqli_fetch_array($ris4);
    $numritnongiust = $val4['numeroritardi'];
    //  }
    //  else
    //      $numritnongiust=0;
    echo '
 <tr>
  <td colspan="3"><b>Ritardi: ' . $val3["numeroritardi"] . ' <font color="red">(' . $numritnongiust . ')</font></b></td>
 </tr>';
}

// conteggio uscite anticipate
$query5 = "select count(*) as numerouscite from tbl_usciteanticipate where idalunno='$codalunno'";
$ris5 = eseguiQuery($con, $query5);

if ($val5 = mysqli_fetch_array($ris5))
{
    echo '
 <tr>
  <td colspan="3"><b>Uscite anticipate: ' . $val5["numerouscite"] . '</b></td>
 </tr>';
}
//print "<tr><td width='33%'>Assenze</td><td width='33%'>Ritardi</td><td width='33%'>Uscite</td></tr>";
print "<tr><td>Assenze</td><td>Ritardi</td><td>Uscite</td></tr>";
print "<tr>";

// elenco tbl_assenze
echo "<td valign=top>";
$query6 = "select * from tbl_assenze where idalunno='$codalunno' order by data desc";
$ris6 = eseguiQuery($con, $query6);

while ($val6 = mysqli_fetch_array($ris6))
{
    $giustifica = $val6['giustifica'];

    if ($giustifica)
    {

        $data = $val6["data"];
        echo ' ' . data_italiana($data);
        if ($val6['dataammonizione'] != NULL)
            echo " (Amm. " . data_italiana($val6['dataammonizione']) . ")";

        print " Giust. il " . data_italiana($val6['datagiustifica']) . " da " . estrai_dati_docente($val6['iddocentegiust'], $con);

        echo '<br>';
    }
    else
    {
        $data = $val6["data"];
        echo '<font color="red"> ' . data_italiana($data);
        if ($val6['dataammonizione'] != NULL)
            echo " (Amm. " . data_italiana($val6['dataammonizione']) . ")";
        echo '</font><br/>';
    }
}
echo "</td>";

// elenco tbl_ritardi
echo "<td valign=top>";
$query7 = "select * from tbl_ritardi where idalunno='$codalunno' order by data desc";
$ris7 = eseguiQuery($con, $query7);

while ($val7 = mysqli_fetch_array($ris7))
{
    $giustifica = $val7['giustifica'];
    //  if ($_SESSION['giustifica_ritardi']=='no')
    //      $giustifica=true;
    if ($giustifica)
    {
        $data = $val7["data"];
        echo ' ' . data_italiana($data);
        if ($val7['dataammonizione'] != NULL)
            echo " (Amm. " . data_italiana($val7['dataammonizione']) . ")";
        print " Giust. il " . data_italiana($val7['datagiustifica']) . " da " . estrai_dati_docente($val7['iddocentegiust'], $con);

        echo '<br>';
    }
    else
    {
        $data = $val7["data"];
        echo '<font color="red"> ' . data_italiana($data);
        if ($val7['dataammonizione'] != NULL)
            echo " (Amm. " . data_italiana($val7['dataammonizione']) . ")";
        echo '</font><br/>';
    }
}
echo "</td>";

// elenco uscite
echo "<td valign=top>";
$query8 = "select * from tbl_usciteanticipate where idalunno='$codalunno' order by data desc";
$ris8 = eseguiQuery($con, $query8);

while ($val8 = mysqli_fetch_array($ris8))
{
    $data = $val8["data"];
    echo ' ' . data_italiana($data) . '<br/>';
    $query = "select * from tbl_autorizzazioniuscite where idalunno=$codalunno and data='$data'";
    $ris = eseguiQuery($con, $query);
    if ($rec = mysqli_fetch_array($ris))
        print "<small>" . $rec['testoautorizzazione'] . "</small><br>";
}
echo '
    </td>
   </tr>
  </table>';

print "
    <form method='post' action='stasitassalu.php' target='_blank' name='staass'>
    <table align='center'>
      <td>
         <input type='hidden' name='alunno' value='$codalunno'>

        <p align='center'><input type='submit' value='Stampa' name='b' onclick='Popup(stasitassalu.php)'></p>
     </form></td>

</table><hr>

    ";


echo '
 </body>
</html>';

