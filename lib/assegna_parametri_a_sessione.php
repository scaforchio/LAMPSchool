<?php



$sql = "SELECT parametro,valore FROM ". $prefisso_tabelle. "tbl_parametri where parametro<>'versione'";
$result = mysqli_query($con, $sql);
$variabili="";
while ($rec = mysqli_fetch_array($result))
   $_SESSION[$rec['parametro']] = $rec['valore'];