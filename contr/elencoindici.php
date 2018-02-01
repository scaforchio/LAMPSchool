<?php session_start();
/**
 * Elenco degli indici del database
 *
 * @copyright  Copyright (C) 2014 Renato Tamilio
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
//require_once '../lib/ db / query.php';

//$lQuery = LQuery::getIstanza();

// istruzioni per tornare alla pagina di login 
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$titolo = "Elenco degli indici del database";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

$query = "SELECT
table_name,
index_name,
GROUP_CONCAT(column_name ORDER BY seq_in_index) AS colonne
FROM information_schema.statistics
WHERE table_schema = '$db_nome'
GROUP BY 1,2";

$rs = mysqli_query($con, inspref($query)); //$lQuery->query($query);

if ($rs)
{
    print "<CENTER><TABLE BORDER='1'>";
    print "<TR class='prima' border=1><TD><B>Tabelle</B></TD><TD><B>Indici</B></TD><TD><B>Colonne</B></TD></TR>";

    //foreach ($rs as $row) {
    while ($row = mysqli_fetch_array($rs))
    {
        $preftabcercata = $prefisso_tabelle . "tbl_";
        $preftabtrovata = substr($row['table_name'], 0, strlen($preftabcercata));
        if ($preftabcercata == $preftabtrovata && $row['index_name'] != 'PRIMARY')
        {
            print "<TR class='oddeven'>";
            print "<TD>" . $row['table_name'] . "</TD>";
            print "<TD>" . $row['index_name'] . "</TD>";
            print "<TD>" . $row['colonne'] . "</TD>";
            print "</TR>";
        }
    }
    print "</TABLE></CENTER>";
}
else
{
    print "Query fallita";
}

$query = "SELECT i.TABLE_NAME, i.CONSTRAINT_NAME, k.REFERENCED_TABLE_NAME, k.REFERENCED_COLUMN_NAME 
FROM information_schema.TABLE_CONSTRAINTS i 
LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME 
WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY' 
AND i.TABLE_SCHEMA = '$db_nome'";

$rs = mysqli_query($con, inspref($query));//$rs = $lQuery->query($query);

if ($rs)
{
    print "<CENTER><p><b>ELENCO DELLE CHIAVI ESTERNE</b></p><TABLE BORDER='1'>";
    print "<TR class='prima' border=1><TD><B>Tabelle</B></TD><TD><B>Nomi chiavi esterne</B></TD><TD><B>Tabelle di<br/>riferimento</B></TD><TD><B>Colonne</B></TD></TR>";

    // foreach ($rs as $row) {
    while ($row = mysqli_fetch_array($rs))
    {
        $preftabcercata = $prefisso_tabelle . "tbl_";
        $preftabtrovata = substr($row['TABLE_NAME'], 0, strlen($preftabcercata));
        if ($preftabcercata == $preftabtrovata)
        {
            print "<TR class='oddeven'>";
            print "<TD>" . $row['TABLE_NAME'] . "</TD>";
            print "<TD>" . $row['CONSTRAINT_NAME'] . "</TD>";
            print "<TD>" . $row['REFERENCED_TABLE_NAME'] . "</TD>";
            print "<TD>" . $row['REFERENCED_COLUMN_NAME'] . "</TD>";
            print "</TR>";
        }
    }
    print "</TABLE></CENTER>";
}
else
{
    print "Query fallita";
}


print "<br/>";

stampa_piede("");

