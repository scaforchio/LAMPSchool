<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

print "<optgroup label='Proprie classi'>";
// $query="select idclasse, anno, sezione, specializzazione from tbl_classi order by anno, sezione, specializzazione";
$query = "select idclasse, anno, sezione, specializzazione from tbl_classi
        where idclasse in (select distinct idclasse from tbl_cattnosupp where iddocente=$iddocente) order by anno, sezione, specializzazione";


$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
//  if ($cattedra==$nom["idcattedra"])
    if ($idclasse == $nom["idclasse"])
    {
        print " selected";
    }
    print ">";
    print ($nom["anno"]);
    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
    print "</option>";
}
print "</optgroup>";
print "<optgroup label='Altre classi'>";
$query = "select idclasse, anno, sezione, specializzazione from tbl_classi
        where idclasse not in (select distinct idclasse from tbl_cattnosupp where iddocente=$iddocente) order by anno, sezione, specializzazione
        ";
$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
//  if ($cattedra==$nom["idcattedra"])
    if ($idclasse == $nom["idclasse"])
    {
        print " selected";
    }
    print ">";
    print ($nom["anno"]);
    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
    print "</option>";
}
print "</optgroup>";
