<?php

function importa_proposte($con, $idalunno, $livscuola) {
    $inserimentoeffettuato = false;
    $query = "select * from tbl_certcompcompetenze where livscuola=$livscuola";
    $ris = mysqli_query($con, inspref($query)) or die("Errore:" . mysqli_error($con) . " " . inspref($query));
    while ($rec = mysqli_fetch_array($ris)) {
        $tipo = 'media';
        $tipocomp = $rec['idccc'];
        if ($rec['compcheuropea'] == '')
            $tipo = 'concat';

        if ($tipo == 'media') {
            $querymedia = "select avg(indicatorenumerico) as media from tbl_certcompproposte,tbl_certcomplivelli where
                        tbl_certcompproposte.idccl=tbl_certcomplivelli.idccl
                        and tbl_certcompproposte.idccc=$tipocomp
                        and tbl_certcompproposte.idalunno=$idalunno";
        } else {
            $querymedia = "select group_concat(DISTINCT giud SEPARATOR ' ') as media from tbl_certcompproposte where
                        tbl_certcompproposte.idccc=$tipocomp
                        and tbl_certcompproposte.idalunno=$idalunno";
        }

        $rismedia = mysqli_query($con, inspref($querymedia)) or die("Errore:" . mysqli_error($con) . " " . inspref($query));
        if ($recmedia = mysqli_fetch_array($rismedia)) {

            $media = $recmedia['media'];
            if ($tipo == 'media')
                $mediaprop = round($media);
        }
        $queryins = '';
        if ($tipo == 'media') {
            if ($media != 0) {
                $idlivmedio = cerca_livello_da_valore($con, $mediaprop, $livscuola);
                $queryins = "insert into tbl_certcompvalutazioni(idccc,idalunno,idccl) values($tipocomp,$idalunno,$idlivmedio)";
            }
        } else {

            if ($media != "") {

                $queryins = "insert into tbl_certcompvalutazioni(idccc,idalunno,giud) values($tipocomp,$idalunno,'$media')";
            }
        }
        if ($queryins != "") {
            mysqli_query($con, inspref($queryins)) or die("Errore:" . mysqli_error($con) . " " . inspref($queryins));
            $inserimentoeffettuato = true;
        }
    }
    return $inserimentoeffettuato;
}

function cerca_livello_da_valore($con, $valore, $livello) {
    $query = "select idccl from tbl_certcomplivelli where livscuola=$livello and indicatorenumerico=$valore";
    $ris = mysqli_query($con, inspref($query)) or die("Errore:" . mysqli_error($con) . " " . inspref($query));
    if (mysqli_num_rows($ris) > 0) {
        $rec = mysqli_fetch_array($ris);
        return $rec['idccl'];
    } else
        return 0;
}
function cerca_competenza_ch_europea($con,$codcomp)
        {
    $query = "select * from tbl_certcompcompetenze where idccc=$codcomp";
    $ris = mysqli_query($con, inspref($query)) or die("Errore:" . mysqli_error($con) . " " . inspref($query));
    $rec=mysqli_fetch_array($ris);
        return $rec['compcheuropea'];
   
}
function decodifica_livello_certcomp($con,$codlivello)
{
    $query = "select livello from tbl_certcomplivelli where idccl='$codlivello'";
    $ris = mysqli_query($con, inspref($query)) or die("Errore:" . mysqli_error($con) . " " . inspref($query));
    if (mysqli_num_rows($ris) > 0) {
        $rec = mysqli_fetch_array($ris);
        return $rec['livello'];
    } else
        return "";
}


function cerca_livello_comp($con, $idalunno, $idcompetenza) {
    $query = "select * from tbl_certcompvalutazioni
            where idalunno=$idalunno and idccc=$idcompetenza";
    // print inspref($query);
    $ris = mysqli_query($con, inspref($query));
    if (mysqli_num_rows($ris) > 0) {
        $rec = mysqli_fetch_array($ris);
        return $rec['idccl'];
    } else
        return 0;
}



function cerca_giudizio_comp($con, $idalunno, $idcompetenza) {
    $query = "select * from tbl_certcompvalutazioni
            where idalunno=$idalunno and idccc=$idcompetenza";
    // print inspref($query);
    $ris = mysqli_query($con, inspref($query));
    if (mysqli_num_rows($ris) > 0) {
        $rec = mysqli_fetch_array($ris);
        return $rec['giud'];
    } else
        return "";
}

function cerca_livello_prop($con, $idalunno,$iddocente, $idcompetenza) {
    $query = "select * from tbl_certcompproposte
            where idalunno=$idalunno and iddocente=$iddocente and idccc=$idcompetenza";
    // print inspref($query);
    $ris = mysqli_query($con, inspref($query));
    if (mysqli_num_rows($ris) > 0) {
        $rec = mysqli_fetch_array($ris);
        return $rec['idccl'];
    } else
        return 0;
}



function cerca_giudizio_prop($con, $idalunno,$iddocente, $idcompetenza) {
    $query = "select * from tbl_certcompproposte
            where idalunno=$idalunno and iddocente=$iddocente and idccc=$idcompetenza";
    // print inspref($query);
    $ris = mysqli_query($con, inspref($query));
    if (mysqli_num_rows($ris) > 0) {
        $rec = mysqli_fetch_array($ris);
        return $rec['giud'];
    } else
        return "";
}


function cerca_livello_da_classe($con, $annoclasse, $livello_scuola) {
    $query = "select * from tbl_certcompvalutazioni
            where idalunno=$idalunno and idccc=$idcompetenza";
    // print inspref($query);
    $ris = mysqli_query($con, inspref($query));
    if (mysqli_num_rows($ris) > 0) {
        $rec = mysqli_fetch_array($ris);
        return $rec['giud'];
    } else
        return "";
}