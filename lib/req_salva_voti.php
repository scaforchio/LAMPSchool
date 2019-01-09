<?php

/*
 * Utilizzato in inslez.php e inslezgruppi.php
 */
$idal = $id['al'];
$tipivoti = ['S', 'O', 'P'];
foreach ($tipivoti as $tv)
{
    $va = "voto" . strtolower($tv) . $idal;
    $ga = "giudizio" . strtolower($tv) . $idal;
    $votoal = is_stringa_html($va) ? stringa_html($va) : 999;  // Se 999 vuol dire che è un voto medio
    $giudal = stringa_html($ga);
    if ($votoal == 99 && $giudal == '')  // Il giudizio è da cancellare
    {
        $query = "SELECT * FROM tbl_valutazioniintermedie WHERE idalunno=$idal AND idlezione=$idlezione AND tipo='$tv'";

        $rissel = eseguiQuery($con, $query);
        if (mysqli_num_rows($rissel) > 0)
        {
            $query = "delete from tbl_valutazioniintermedie where idalunno=$idal and idlezione=$idlezione and tipo='$tv'";
            $risd = eseguiQuery($con, $query);
        }
    } else
    {

        // Verifico se il voto già c'è
        $query = "select idvalint from tbl_valutazioniintermedie where idalunno=$idal and idlezione=$idlezione and tipo='$tv'";

        $risric = eseguiQuery($con, $query);
        if ($rec = mysqli_fetch_array($risric))
        {
            $idvalint = $rec['idvalint'];
        } else
        {
            $idvalint = 0;
        }
        if ($idvalint != 0)
        {
            if ($votoal != 999)
            {
                $query = "update tbl_valutazioniintermedie set voto=$votoal, giudizio='$giudal' where idalunno=" . $idal . " and idlezione='$idlezione' and tipo='$tv'";
                $risup = eseguiQuery($con, $query);
            } else
            {
                $query = "update tbl_valutazioniintermedie set giudizio='$giudal' where idalunno=" . $idal . " and idlezione='$idlezione' and tipo='$tv'";
                $risup = eseguiQuery($con, $query);
            }
        } else
        {
            // Inserisco voti non già esistenti
            if ($votoal != 999)
            {
                $query = "insert into tbl_valutazioniintermedie(idalunno,idmateria,iddocente,idclasse,idlezione,data,tipo,voto,giudizio)
                      values(" . $idal . ",$idmateria,$iddocente,$idclasse,'$idlezione','$data','$tv',$votoal,'$giudal')";
                $risins = eseguiQuery($con, $query);
            }
        }
    }
}
