<?php

print "<optgroup label='Proprie classi'>";
$query = "select idclasse, anno, sezione, specializzazione from tbl_classi
        where idclasse in (select distinct idclasse from tbl_cattnosupp where iddocente=$iddocente) order by anno, sezione, specializzazione";

$ris = eseguiQuery($con, $query);
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
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
$ris = eseguiQuery($con, $query);
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
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
