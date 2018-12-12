<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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