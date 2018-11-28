<?php

$strcampi = "";
$strtabelle = $daticrud['tabella'] . ", ";
$strconcat = "";
// Campi senza chiave esterna




foreach ($daticrud['campi'] as $c)
    if ($c[1] != 0)
        if ($c[2] == '')
            $strcampi .= $daticrud['tabella'] . "." . $c[0] . ", ";
        else
        {
            $strtabelle .= $c[2] . ", ";  // TTTT Prevedere la possibilità di utilizzo della stessa tabella più volte
            $strconcat .= "and " . $daticrud['tabella'] . "." . $c[0] . "=" . $c[2] . "." . $c[3] . " ";
            $elcampitabesterna = explode(",", $c[4]);
            foreach ($elcampitabesterna as $ctb)
                $strcampi .= $c[2] . "." . $ctb . ", ";
        }

$strcampi = substr($strcampi, 0, strlen($strcampi) - 2);
$strtabelle = substr($strtabelle, 0, strlen($strtabelle) - 2);

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
foreach ($daticrud['campi'] as $c)
    if ($c[1] != 0)
        print "<td><b>$c[6]</b></td>";
print "<td>Azioni</td>";
print "</tr>";


while ($rec = mysqli_fetch_array($ris))
{
    //  print "Numero elementi ".count($daticrud['fk']);
    print "<tr>";
    // 
    foreach ($daticrud['campi'] as $c)
    {
        if ($c[1] != 0)
        {
            if ($c[2] == '')
            {
                
                $strvis = $rec[$c[0]];
            } else
            {
                $elcampitabesterna = explode(",", $c[4]);
                //    $strvis = $rec[$campo];
                $numerochiave = substr($campo, 3, 1);
                $strvis = "";
                
                foreach ($elcampitabesterna as $ctb)
                {
                    //$nomecampo = $daticrud['fk'][$numerochiave][0].".".$ce;

                    $strvis .= $rec[$ctb] . " ";
                }
            }
            print "<td>$strvis</td>";
        }
    }

    print "<td>";
    print "<a href='CRUDmodifica.php?id=" . $rec[$daticrud['campochiave']] . "'><img src='../immagini/modifica.png'></a>&nbsp;";
    print "<a href='CRUDelimina.php?id=" . $rec[$daticrud['campochiave']] . "'><img src='../immagini/delete.png'></a>";

    print "</td>";
    print "</tr>";
}
print "</table><br>";

