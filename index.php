<?php
/**
 * Pagina principale dell'applicazione web LAMPSchool.
 *
 * @copyright  Copyright (C) 2015 Renato Tamilio. Pietro Tamburrano
 * @license    GNU Affero General Public License versione 3 o successivi; vedete agpl-3.0.txt
 */

// include ("funzioni.php");

$elencoinstallazioni = array();
$elencoinstallazioni = elencafiles(".");

if (isset($_GET['suffisso']))
    $suffisso = $_GET['suffisso'];
else
    $suffisso ='*';




$scuole = array();
$anni = array();
$suffissi = array();
for ($i = 0; $i < count($elencoinstallazioni); $i++)
{
    $fileinclude = $elencoinstallazioni[$i];
    include($fileinclude);

    if ($con = mysqli_connect($db_server, $db_user, $db_password, $db_nome))
    {


        $query = "SELECT valore FROM " . $prefisso_tabelle . "tbl_parametri WHERE parametro='nome_scuola'";
        $ris = mysqli_query($con, $query);
        $rec = mysqli_fetch_array($ris);
        if ($rec['valore'] != "Scuola XYZ")
        {
            $scuole[] = $rec['valore'];
            $suffissi[] = substr($fileinclude, 7, strlen($fileinclude) - 11);

            $query = "SELECT valore FROM " . $prefisso_tabelle . "tbl_parametri WHERE parametro='annoscol'";
            $ris = mysqli_query($con, $query);
            $rec = mysqli_fetch_array($ris);
            $anni[] = $rec['valore'];
        }
    }
}

// ORDINAMENTO
for ($i = 0; $i < count($scuole) - 1; $i++)
    for ($j = $i + 1; $j < count($scuole); $j++)
        if ($scuole[$i] . $anni[$i] > $scuole[$j] . $anni[$j])
        {
            $temp = $scuole[$i];
            $scuole[$i] = $scuole[$j];
            $scuole[$j] = $temp;
            $temp = $anni[$i];
            $anni[$i] = $anni[$j];
            $anni[$j] = $temp;
            $temp = $suffissi[$i];
            $suffissi[$i] = $suffissi[$j];
            $suffissi[$j] = $temp;
        }


mysqli_close($con);

if ($suffisso != "*")
{
    for ($i = 0; $i < count($elencoinstallazioni); $i++)
    {
        if ($suffisso == $suffissi[$i])
        {
            header("location: login/login.php?suffisso=$suffisso");
        }
    }

}


print "<!DOCTYPE html>
<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
<title>Inserimento dati di accesso</title>
</head><body>";
print "<center><big><big>Seleziona scuola - anno</big></big></center><br><br>";



print "<table align='center' border=1><tr><td><b>Scuola</b></td><td align='center'><b>A.S.</b></td><td align='center'><b>Reg.</b></td></tr>";
for ($i = 0; $i < count($scuole); $i++)
{
    print "<tr>";
    print "<td>" . $scuole[$i] . "</td>";
    print "<td align='center'>" . $anni[$i] . "/" . ($anni[$i] + 1) . "</td>";
    print "<td><a href='login/login.php?suffisso=" . $suffissi[$i] . "'><img src='./immagini/lstrasp.gif'></a></td>";
    print "</tr>";
}

print "</table>";
print "</body></html>";
//	


function elencafiles($dirname)
{
    $arrayfiles = array();
    if (file_exists($dirname))
    {

        $handle = opendir($dirname);
        while (false !== ($file = readdir($handle)))
        {
            if (substr($file, 0, 7) == "php-ini" && substr($file, strlen($file) - 4) == ".php")
            {
                array_push($arrayfiles, $file);
            }

        }
        closedir($handle);
    }
    sort($arrayfiles);
    return $arrayfiles;
}



