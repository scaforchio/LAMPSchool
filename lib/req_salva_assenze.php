<?php

/*
 * Utilizzato in inslez.php e inslezgruppi.php
 */
$idal = $id['al'];

$asslez = "oreass" . $idal;

$asslezal = stringa_html($asslez);
$esisteassenza = false;
//print "id al: $idal $asslez $asslezal <br>";
$query = "select * from tbl_asslezione where idalunno=$idal and idlezione=$idlezione";
$ris = eseguiQuery($con, $query);
if (mysqli_num_rows($ris) > 0)
    $esisteassenza = true;

if ($asslezal != 0) {
    $flaguscent = false;
    // VERIFICO SE LA GIORNATA E' DI DAD E SE PER L'ALUNNO CI SONO RITARDI O USCITE
    $query = "select * from tbl_dad where idclasse=$idclasse and datadad='$data'";
    $ris = eseguiQuery($con, $query);
    if (mysqli_num_rows($ris) > 0) {
        $query = "select * from tbl_usciteanticipate where idalunno=$idal and data = '$data'";
        $ris = eseguiQuery($con, $query);
        if (mysqli_num_rows($ris) > 0) {
            $flaguscent = true;
        } else {
            $query = "select * from tbl_ritardi where idalunno=$idal and data = '$data'";
            $ris = eseguiQuery($con, $query);
            if (mysqli_num_rows($ris) > 0)
                $flaguscent = true;
        }
    }

    if (!$esisteassenza) {
        if (!$flaguscent)
            $query = "insert into tbl_asslezione(idalunno, idlezione,idmateria,data,oreassenza,forzata) values ($idal,$idlezione,$idmateria,'$data',$asslezal,true)";
        else
            $query = "insert into tbl_asslezione(idalunno, idlezione,idmateria,data,oreassenza,forzata,giustifica,iddocentegiust,datagiustifica) values ($idal,$idlezione,$idmateria,'$data',$asslezal,true,true,0,'$data')";
    } else
        $query = "update tbl_asslezione set  oreassenza=$asslezal where idalunno=$idal and idlezione=$idlezione";


    eseguiQuery($con, $query);
} else {
    if ($esistelezione) {
        $query = "delete from tbl_asslezione where idalunno=$idal and idlezione=$idlezione";
        eseguiQuery($con, $query);
    }
}
    

