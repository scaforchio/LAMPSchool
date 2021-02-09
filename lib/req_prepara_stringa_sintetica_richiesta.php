<?php

$testocompleto = $rec['testomail'];

print "<td><small><small>$testocompleto<big><big></td>";
$concesso = $rec['concessione'];
$annullata = $rec['annullata'];
print "<td align='center' valign='middle'>";
if ($concesso == NULL & strpos($testocompleto, "Malattia (") == 0)
    print "<a href='concediferie.php?prot=$prot&conc=1'>Concedi</a><br><br><a href='concediferie.php?prot=$prot&conc=3'>Concedi per motivi di servizio</a><br><br><a href='./concediferie.php?prot=$prot&conc=0'>Nega</a><br><br><a href='./concediferie.php?prot=$prot&conc=2'>Chiedi chiarimenti</a></td>";
else
if ($concesso == NULL & strpos($testocompleto, "Malattia (") != 0)
    print "<a href='concediferie.php?prot=$prot&conc=1'>Presa visione</a></td>";
else
if ($concesso == 2)
    print "<a href='concediferie.php?prot=$prot&conc=1'>Concedi</a><br><br><a href='concediferie.php?prot=$prot&conc=3'>Concedi per motivi di servizio</a><br><br><a href='./concediferie.php?prot=$prot&conc=0'>Nega</a><br><br>In attesa di chiarimenti!</td>";
else
if ($concesso == 1)
{
    print "<img src='../immagini/apply.png'>";
    if (!$annullata)
        print "<br><br><a href='annullarichiestaferie.php?prot=$prot&mf=yes'>ANNULLA PER MANCATA FRUIZIONE</a></td>";
    else
        print "<br><br><b>ANNULLATA PER MANCATA FRUIZIONE!</b></td>";
} else
if ($concesso == 9)
    print "ANNULLATA</td>";
else
    print "<img src='../immagini/cancel.png'></td>";

print "</tr>";
