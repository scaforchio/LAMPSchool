<?php

// Caricamento parametri
/*
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

$sql = "SELECT parametro,valore FROM " . $prefisso_tabelle . "tbl_parametri where parametro<>'versione'";
$result = mysqli_query($con, $sql);

while ($rec = mysqli_fetch_array($result))
{
    $variabile = $rec['parametro'];
    $valore=$rec['valore'];
    $$variabile=$valore;
}

mysqli_close($con);
*/