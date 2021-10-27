<?php

require_once '../lib/req_apertura_sessione.php';
@require_once("../lib/funzioni.php");
$suffisso = stringa_html('suffisso');
@require_once("../php-ini" . $suffisso . ".php");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$query = "select * from tbl_timbrature order by idtimbratura desc";
$ris = eseguiQuery($con,$query);
print "<table border=1><tr><td>Codice</td><td>Tipo</td><td>Ora</td><td>Data</td><td>Ora inserimento</td><td>Forzata</td></tr>";
while ($rec = mysqli_fetch_array($ris))
{
    print "<tr><td>" . $rec['idalunno'] . "</td><td>" . $rec['tipotimbratura'] . "</td><td>" . substr($rec['oratimbratura'], 0, 5) . "</td><td>" . data_italiana($rec['datatimbratura']) . "</td><td>" . $rec['ultimamodifica'] . "</td><td>" . $rec['forzata'] . "</td></tr>";
}
print "</table>";
//}

mysqli_close($con);



