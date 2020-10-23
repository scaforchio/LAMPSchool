<?php

/*
 * Utilizzato in inslez.php e inslezgruppi.php
 */
$idal = $id['al'];

$asslez = "oreass" . $idal;

$asslezal = stringa_html($asslez);  // Se 999 vuol dire che è un voto medio

//print "id al: $idal $asslez $asslezal <br>";
$query = "delete from tbl_asslezione where idalunno=$idal and idlezione=$idlezione";
eseguiQuery($con, $query);
if ($asslezal != 0)
{
    $query = "insert into tbl_asslezione(idalunno, idlezione,oreassenza,forzata) values ($idal,$idlezione,$asslezal,true)";
    eseguiQuery($con,$query);
}
       
    

