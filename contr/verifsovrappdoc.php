<?php session_start();

/*
Copyright (C) 2015 Pietro Tamburrano
Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della 
GNU Affero General Public License come pubblicata 
dalla Free Software Foundation; sia la versione 3, 
sia (a vostra scelta) ogni versione successiva.

Questo programma é distribuito nella speranza che sia utile 
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di 
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE. 
Vedere la GNU Affero General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU Affero General Public License
in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
*/

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");


// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Verifica sovrapposizioni di lezioni";
$script = "";
stampa_head($titolo, "", $script, "PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


// VERIFICO SOVRAPPOSIZIONI NELLE CLASSI

$data1 = '';
$cla1 = '';
$orain1 = 0;
$durata1 = 0;
$mat1 = 0;
$doc1 = 0;
$per1 = "";
$data2 = '';
$cla2 = '';
$orain2 = 0;
$durata2 = 0;
$mat1 = 0;
$doc2 = 0;
$per2 = "";
$query = "SELECT idclasse, tbl_firme.iddocente, idmateria, datalezione, orainizio, numeroore
        FROM tbl_firme,tbl_lezioni
        WHERE tbl_firme.idlezione=tbl_lezioni.idlezione 
        AND idlezionegruppo IS NULL
        ORDER BY idclasse, datalezione, orainizio";
$ris = mysqli_query($con, inspref($query)) or die (mysqli_error($con));
if (mysqli_num_rows($ris) > 0)
{
    print "<br><br><center><b>Sovrapposizioni nella stessa classe!</b></center><br><br>";
    print "<table align=center border=1><tr class='prima'><td>Data</td><td>Classe</td><td>Materia 1</td><td>Docente 1</td><td>Per.1</td><td>Materia 2</td><td>Docente 2</td><td>Per.2</td></tr>";
    while ($rec = mysqli_fetch_array($ris))
    {
        $data2 = $rec['datalezione'];
        $giorno = giorno_settimana($data2);
        $orain2 = $rec['orainizio'];
        $durata2 = $rec['numeroore'];
        $cla2 = $rec['idclasse'];
        $mat2 = $rec['idmateria'];
        $doc2 = $rec['iddocente'];
        $sostegno2 = is_docente_sostegno_classe($doc2, $cla2, $con);
        $per2 = $orain2 . ">" . ($orain2 + $durata2 - 1);
        if ($data2 == $data1 & $cla2 == $cla1 & $mat2 != $mat1)
        {
            if ($orain2 < ($orain1 + $durata1))
            {
                // 24/1/2017 Verifico che non si tratti di cpompresenza tra docente di sostegno e supplente
                if (!(($sostegno1 & $mat2 == 0) | ($sostegno2 & $mat1 == 0)))
                {
                    if ($doc1 == $iddocente | $doc2 == $iddocente)
                    {
                        print "<tr><td> $giorno " . data_italiana($data2) . "</td><td>" . decodifica_classe($cla2, $con) . "</td><td>" . decodifica_materia($mat1, $con) . "</td><td>" . estrai_dati_docente($doc1, $con) . "</td><td>$per1</td><td>" . decodifica_materia($mat2, $con) . "</td><td>" . estrai_dati_docente($doc2, $con) . "</td><td>$per2</td></tr>";
                    }
                }
            }
        }
        $data1 = $data2;
        $orain1 = $orain2;
        $durata1 = $durata2;
        $cla1 = $cla2;
        $mat1 = $mat2;
        $doc1 = $doc2;
        $sostegno1 = is_docente_sostegno_classe($doc1, $cla1, $con);
        $per1 = $per2;
    }
    print "</table>";
}
else
{
    print "<br><br><center><b>Nessuna sovrapposizione nelle classi!</b></center><br><br>";
}

// VERIFICO SOVRAPPOSIZIONI NEI DOCENTI
$data1 = '';
$cla1 = '';
$orain1 = 0;
$durata1 = 0;
$mat1 = 0;
$doc1 = 0;
$per1 = "";
$data2 = '';
$cla2 = '';
$orain2 = 0;
$durata2 = 0;
$mat1 = 0;
$doc2 = 0;
$per2 = "";
$query = "SELECT idclasse, tbl_firme.iddocente, idmateria, datalezione, orainizio, numeroore
        FROM tbl_firme,tbl_lezioni
        WHERE tbl_firme.idlezione=tbl_lezioni.idlezione 
        AND idlezionegruppo IS NULL
        ORDER BY iddocente, datalezione, orainizio";
$ris = mysqli_query($con, inspref($query)) or die (mysqli_error($con));
if (mysqli_num_rows($ris) > 0)
{
    print "<br><br><center><b>Sovrapposizioni in classi diverse!</b></center><br><br>";
    print "<table align=center border=1><tr class='prima'><td>Data</td><td>Docente</td><td>Classe 1</td><td>Materia 1</td><td>Per.1</td><td>Classe 2</td><td>Materia 2</td><td>Per.2</td></tr>";
    while ($rec = mysqli_fetch_array($ris))
    {
        $data2 = $rec['datalezione'];
        $giorno = giorno_settimana($data2);
        $orain2 = $rec['orainizio'];
        $durata2 = $rec['numeroore'];
        $cla2 = $rec['idclasse'];
        $mat2 = $rec['idmateria'];
        $doc2 = $rec['iddocente'];
        $per2 = $orain2 . ">" . ($orain2 + $durata2 - 1);
        if ($data2 == $data1 & $doc2 == $doc1)
        {
            if ($orain2 < ($orain1 + $durata1))
            {
                if ($doc1 == $iddocente | $doc2 == $iddocente)
                {
                    print "<tr><td> $giorno " . data_italiana($data2) . "</td><td>" . estrai_dati_docente($doc1, $con) . "</td><td>" . decodifica_classe($cla1, $con) . "</td><td>" . decodifica_materia($mat1, $con) . "</td><td>$per1</td><td>" . decodifica_classe($cla2, $con) . "</td><td>" . decodifica_materia($mat2, $con) . "</td><td>$per2</td></tr>";
                }
            }
        }
        $data1 = $data2;
        $orain1 = $orain2;
        $durata1 = $durata2;
        $cla1 = $cla2;
        $mat1 = $mat2;
        $doc1 = $doc2;
        $per1 = $per2;
    }
    print "</table>";
}
else
{
    print "<br><br><center><b>Nessuna sovrapposizione nelle classi!</b></center><br><br>";
}

mysqli_close($con);
stampa_piede("");

