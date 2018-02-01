<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
GNU Affero General Public License come pubblicata 
dalla Free Software Foundation; sia la versione 3, 
sia (a vostra scelta) ogni versione successiva.

Questo programma è distribuito nella speranza che sia utile 
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di 
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE. 
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/


//Programma per la visualizzazione dell'elenco delle tbl_classi

error_log("Sono qui\n", 3, "accessi.log");

$con=mysqli_connect("62.149.150.198","Sql691154","cclm6a4y2t","Sql691154_5");
$ids=$_GET['ids'];
$nos=$_GET['nos'];
$cos=$_GET['cos'];
$ver=$_GET['ver'];
$asc=$_GET['asc'];
error_log(date('y-m-d|H:i:s')." - IDS_ $ids NOS_ $nos COS_ $cos VER_ $ver ASC_ $asc\n", 3, "accessi.log");
$query="select * from tbl_accessidistinti where codice='$ids'";
$ris=mysqli_query($con,$query);
if (mysqli_num_rows($ris)==0)
    $query="insert into tbl_accessidistinti(codice, nomescuola, comune, versione, annoscolastico) values ('$ids','$nos','$cos','$ver','$asc')";
else
    $query="update tbl_accessidistinti set numeroaccessi=numeroaccessi+1, versione='$ver' where codice='$ids'";

mysqli_query($con,$query);


mysqli_close($con);
