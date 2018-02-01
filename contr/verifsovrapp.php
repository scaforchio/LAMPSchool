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
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Verifica sovrapposizioni di lezioni";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
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
$classe1 = "";
$materia1 = "";
$docente1 = "";
$data2 = '';
$cla2 = '';
$orain2 = 0;
$durata2 = 0;
$mat1 = 0;
$doc2 = 0;
$per2 = "";
$classe2 = "";
$materia2 = "";
$docente2 = "";/*
 $query="SELECT tbl_lezioni.idclasse, anno, sezione, specializzazione, tbl_lezioni.iddocente, cognome, nome, tbl_lezioni.idmateria, denominazione, datalezione, orainizio, numeroore
        FROM tbl_lezioni,tbl_docenti,tbl_materie,tbl_classi
        WHERE tbl_lezioni.idmateria=tbl_materie.idmateria AND
         tbl_lezioni.idclasse=tbl_classi.idclasse AND
         tbl_lezioni.iddocente=tbl_docenti.iddocente AND
         idlezionegruppo IS NULL
        ORDER BY anno,sezione,specializzazione, datalezione, orainizio"; */

$query = "SELECT tbl_lezioni.idclasse, anno, sezione, specializzazione, tbl_firme.iddocente, cognome, nome,sostegno, tbl_lezioni.idmateria, denominazione, datalezione, orainizio, numeroore
        FROM tbl_firme,tbl_lezioni,tbl_docenti,tbl_materie,tbl_classi
        WHERE tbl_firme.idlezione=tbl_lezioni.idlezione AND
        tbl_lezioni.idmateria=tbl_materie.idmateria AND
         tbl_lezioni.idclasse=tbl_classi.idclasse AND
         tbl_firme.iddocente=tbl_docenti.iddocente AND
         idlezionegruppo IS NULL
        ORDER BY anno,sezione,specializzazione, datalezione, orainizio";

$ris = mysqli_query($con, inspref($query)) or die (mysqli_error($con));
if (mysqli_num_rows($ris) > 0)
{
    print "<br><br><center><b>Sovrapposizioni nelle classi!</b></center><br><br>";
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
        $classe2 = $rec['anno'] . " " . $rec['sezione'] . " " . $rec['specializzazione'];
        $docente2 = $rec['cognome'] . " " . $rec['nome'];
        $sostegno2 = $rec['sostegno'];
        $materia2 = $rec['denominazione'];
        $per2 = $orain2 . ">" . ($orain2 + $durata2 - 1);
        if ($data2 == $data1 & $cla2 == $cla1 & $mat2 != $mat1)
        {
            if ($orain2 < ($orain1 + $durata1))
            {
                // 24/01/2017 ESCLUDO LA VISUALIZZAZIONE SE SI TRATTA DI COMPRESENZA DI SUPPLENTE CON INSEGNANTE DI SOSTEGNO
                if (!(($sostegno1 & $mat2==0 )|($sostegno2 & $mat1==0 )))
                    print "<tr><td> $giorno " . data_italiana($data2) . "</td><td><b>" . $classe2 . "</b></td><td>" . $materia1 . "</td><td>" . $docente1 . "</td><td>$per1</td><td>" . $materia2 . "</td><td>" . $docente2 . "</td><td>$per2</td></tr>";
            }
        }
        $data1 = $data2;
        $orain1 = $orain2;
        $durata1 = $durata2;
        $cla1 = $cla2;
        $mat1 = $mat2;
        $doc1 = $doc2;
        $per1 = $per2;
        $classe1 = $classe2;
        $docente1 = $docente2;
        $sostegno1 = $sostegno2;
        $materia1 = $materia2;
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
$classe1 = "";
$materia1 = "";
$docente1 = "";
$data2 = '';
$cla2 = '';
$orain2 = 0;
$durata2 = 0;
$mat1 = 0;
$doc2 = 0;
$per2 = "";
$classe2 = "";
$materia2 = "";
$docente2 = "";
/*
$query="SELECT tbl_lezioni.idclasse, anno, sezione, specializzazione, tbl_lezioni.iddocente, cognome, nome, tbl_lezioni.idmateria, denominazione, datalezione, orainizio, numeroore
        FROM tbl_lezioni,tbl_docenti,tbl_materie,tbl_classi
        WHERE tbl_lezioni.idmateria=tbl_materie.idmateria AND
         tbl_lezioni.idclasse=tbl_classi.idclasse AND
         tbl_lezioni.iddocente=tbl_docenti.iddocente AND
         idlezionegruppo IS NULL
        ORDER BY cognome,nome, datalezione, orainizio";  */

$query = "SELECT tbl_lezioni.idclasse, anno, sezione, specializzazione, tbl_firme.iddocente, cognome, nome, tbl_lezioni.idmateria, denominazione, datalezione, orainizio, numeroore
        FROM tbl_firme,tbl_lezioni,tbl_docenti,tbl_materie,tbl_classi
        WHERE tbl_firme.idlezione=tbl_lezioni.idlezione AND
        tbl_lezioni.idmateria=tbl_materie.idmateria AND
         tbl_lezioni.idclasse=tbl_classi.idclasse AND
         tbl_firme.iddocente=tbl_docenti.iddocente AND
         idlezionegruppo IS NULL
        ORDER BY cognome,nome, datalezione, orainizio";
$ris = mysqli_query($con, inspref($query)) or die (mysqli_error($con));
if (mysqli_num_rows($ris) > 0)
{
    print "<br><br><center><b>Sovrapposizioni firme docenti!</b></center><br><br>";
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
        $classe2 = $rec['anno'] . " " . $rec['sezione'] . " " . $rec['specializzazione'];
        $docente2 = $rec['cognome'] . " " . $rec['nome'];
        $materia2 = $rec['denominazione'];
        $per2 = $orain2 . ">" . ($orain2 + $durata2 - 1);
        if ($data2 == $data1 & $doc2 == $doc1)
        {
            if ($orain2 < ($orain1 + $durata1))
            {
                print "<tr><td> $giorno " . data_italiana($data2) . "</td><td><b>" . $docente1 . "</b></td><td>" . $classe1 . "</td><td>" . $materia1 . "</td><td>$per1</td><td>" . $classe2 . "</td><td>" . $materia2 . "</td><td>$per2</td></tr>";
            }
        }
        $data1 = $data2;
        $orain1 = $orain2;
        $durata1 = $durata2;
        $cla1 = $cla2;
        $mat1 = $mat2;
        $doc1 = $doc2;
        $per1 = $per2;
        $classe1 = $classe2;
        $docente1 = $docente2;
        $materia1 = $materia2;
    }
    print "</table>";
}
else
{
    print "<br><br><center><b>Nessuna sovrapposizione nelle classi!</b></center><br><br>";
}


mysqli_close($con);
stampa_piede("");

