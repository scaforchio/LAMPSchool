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

//$rs = $lQuery->query($query);
$rs = mysqli_query($con, $query) or die ("Errore:" . $query);

if ($rs)
{
    print "<CENTER><TABLE BORDER='1'>";
    print "<TR class='prima' border=1><TD><B>Tabelle</B></TD><TD><B>Indici</B></TD><TD><B>Colonne</B></TD></TR>";

    while ($row = mysqli_fetch_array($rs))
    {

        /*
       * VERIFICO CHE LA TABELLA SIA DEL TIPO: <prefisso>tbl_
       */
        $preftabcercata = $prefisso_tabelle . "tbl_";
        $preftabtrovata = substr($row['table_name'], 0, strlen($preftabcercata));
        if ($preftabcercata == $preftabtrovata)
        {
            print "<TR class='oddeven'>";
            print "<TD>" . $row['table_name'] . "</TD>";
            print "<TD>" . $row['index_name'] . "</TD>";
            print "<TD>" . $row['colonne'] . "</TD>";
            print "</TR>";
            if ($row['index_name'] != "PRIMARY" & $row['index_name'] != "idvalint" & $row['index_name'] != "idcompetenza")
            {

                $query = "ALTER TABLE " . $row['table_name'] . " DROP INDEX `" . $row['index_name'] . "`";
                //  print $query;
                //
                // NON USO inspref PER EVITARE CHE VENGA INSERITO NUOVAMENTE IL PREFISSO
                //
                mysqli_query($con, $query) or die ("Errore:" . $query);

            }
        }
    }
    print "</TABLE></CENTER>";
}
else
{
    print "Query fallita";
}


$query = "ALTER TABLE tbl_lezioni ADD INDEX datalezione(datalezione)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
$query = "ALTER TABLE tbl_lezioni ADD INDEX idclasse(idclasse)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
$query = "ALTER TABLE tbl_lezioni ADD UNIQUE uk_principale (idclasse, idmateria, datalezione, orainizio, numeroore) COMMENT 'Evita doppi inserimenti'";
mysqli_query($con, inspref($query)) or print ("<b><center>Non è stato possibile ricostruire l'indice univoco sulle lezioni! Ci sono lezioni duplicate: verificare, risolvere e ritentare!</center></b>");
$query = "ALTER TABLE tbl_proposte ADD INDEX idmateria(idmateria)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
$query = "ALTER TABLE tbl_proposte ADD INDEX periodo(periodo)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
$query = "ALTER TABLE tbl_proposte ADD INDEX idalunno(idalunno)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
$query = "ALTER TABLE tbl_asslezione ADD INDEX idalunno(idalunno)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
$query = "ALTER TABLE tbl_asslezione ADD INDEX idlezione(idlezione)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
$query = "ALTER TABLE tbl_valutazioniintermedie ADD INDEX idalunno(idalunno)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
$query = "ALTER TABLE tbl_valutazioniintermedie ADD INDEX idlezione(idlezione)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
$query = "ALTER TABLE tbl_alunni ADD INDEX idclasse(idclasse)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
$query = "ALTER TABLE tbl_cattnosupp ADD INDEX idclasse(idclasse)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
$query = "ALTER TABLE tbl_cattnosupp ADD INDEX iddocente(iddocente)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
$query = "ALTER TABLE tbl_cattnosupp ADD INDEX idmateria(idmateria)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
$query = "ALTER TABLE tbl_esiti ADD INDEX idalunno(idalunno)";
mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
print "<br/>";

stampa_piede("");

