<?php

for ($g = 1; $g <= 31; $g++)
{
    if ($g < 10)
    {
        $gs = '0' . $g;
    } else
    {
        $gs = '' . $g;
    }
    if ($gs == $giorno)
    {
        echo("<option selected>$gs</option>");
    } else
    {
        echo("<option>$gs</option>");
    }
}