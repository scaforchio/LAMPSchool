<?php

for ($m = 9; $m <= 12; $m++)
{
    if ($m < 10)
    {
        $ms = "0" . $m;
    } else
    {
        $ms = '' . $m;
    }
    if ($ms == $mese)
    {
        echo("<option selected>$ms - ".$_SESSION['annoscol']."</option>");
    } else
    {
        echo("<option>$ms - ".$_SESSION['annoscol']."</option>");
    }
}
$annoscolsucc = $_SESSION['annoscol'] + 1;
for ($m = 1; $m <= 8; $m++)
{
    if ($m < 10)
    {
        $ms = '0' . $m;
    } else
    {
        $ms = '' . $m;
    }
    if ($ms == $mese)
    {
        echo("<option selected>$ms - $annoscolsucc</option>");
    } else
    {
        echo("<option>$ms - $annoscolsucc</option>");
    }
}
