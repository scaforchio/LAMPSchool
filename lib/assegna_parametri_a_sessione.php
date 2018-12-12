<?php

$sql = "SELECT parametro,valore FROM " . $prefisso_tabelle . "tbl_parametri where parametro<>'versione'";
$result = eseguiQuery($con, $sql, false);
$variabili = "";
while ($rec = mysqli_fetch_array($result))
    $_SESSION[$rec['parametro']] = $rec['valore'];