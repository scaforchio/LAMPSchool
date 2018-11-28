<?php
print "qui";
$strcampi = "";
// Campi senza chiave esterna
print "qui";
foreach ($daticrud['campi'] as $c)
    print "<br>pppp".$c[0];
      //  $strcampi .= $daticrud['tabella'] . "." . $c . ", ";



/*


foreach ($daticrud['elencocampi'] as $c)
    if (substr($c, 0, 3) != "fk_")
        $strcampi .= $daticrud['tabella'] . "." . $c . ", ";
// Campi con chiave esterna
foreach ($daticrud['fk'] as $fk)
    foreach ($fk[2] as $ce)
        $strcampi .= $fk[0] . "." . $ce . ", ";
$strcampi = substr($strcampi, 0, strlen($strcampi) - 2);
// Tabelle nella from
$strtabelle = $daticrud['tabella'];
foreach ($daticrud['fk'] as $fk)
    $strtabelle .= ", " . $fk[0];
// Condizione di congiunzione tabelle
$strconcat = "";
foreach ($daticrud['fk'] as $fk)
    $strconcat .= "and " . $daticrud['tabella'] . "." . $fk[3] . "=" . $fk[0] . "." . $fk[1] . " ";
// Condizione di congiunzione tabelle
$strcondizione = " and " . $daticrud['condizione'];

// Campi per ordinamento in visualizzazione
$strcampiordinamento = implode(",", $daticrud['campiordinamento']);
// Costruzione query
$query = "select " . $daticrud['campochiave'] . ", $strcampi from " . $strtabelle . " where true " . $strconcat . " " . $strcondizione . " order by $strcampiordinamento";
// print $query;
$ris = mysqli_query($con, $query) or die("Errore " . $query . " ERR " . mysqli_error($con));

print "<br><center><a href='CRUDinserimento.php'><b>INSERISCI NUOVO</b></a></center><br><br>";
print "<table align='center' border='1'>";
// Visualizzazione intestazioni
print "<tr class='prima'>";
foreach ($daticrud['intestazioni'] as $int)
    print "<td><b>$int</b></td>";
print "<td>Azioni</td>";
print "</tr>";


while ($rec = mysqli_fetch_array($ris))
{
    //  print "Numero elementi ".count($daticrud['fk']);
    print "<tr>";
    // 
    foreach ($daticrud['elencocampi'] as $campo)
    {

        if (substr($campo, 0, 3) != "fk_")
        {
            //$nomecampo = $daticrud['tabella'] . "." . $campo;
            $nomecampo = $campo;
            $strvis = $rec[$nomecampo];
        } else
        {

            //    $strvis = $rec[$campo];
            $numerochiave = substr($campo, 3, 1);
            $strvis = "";
            foreach ($daticrud['fk'][$numerochiave][2] as $ce)
            {
                //$nomecampo = $daticrud['fk'][$numerochiave][0].".".$ce;
                $nomecampo = $ce;
                $strvis .= $rec[$nomecampo] . " ";
            }
        }
        print "<td>$strvis</td>";
    }

    print "<td>";
    print "<a href='CRUDmodifica.php?id=" . $rec[$daticrud['campochiave']] . "'><img src='../immagini/modifica.png'></a>&nbsp;";
    print "<a href='CRUDelimina.php?id=" . $rec[$daticrud['campochiave']] . "'><img src='../immagini/delete.png'></a>";

    print "</td>";
    print "</tr>";
}
print "</table><br>";

*/

?>