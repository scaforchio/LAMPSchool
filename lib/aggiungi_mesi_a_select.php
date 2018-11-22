<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
        echo("<option selected>$ms - $annoscol</option>");
    } else
    {
        echo("<option>$ms - $annoscol</option>");
    }
}
$annoscolsucc = $annoscol + 1;
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
