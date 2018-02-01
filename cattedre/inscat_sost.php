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


/*
     Programma per l'inserimento delle tbl_cattnosupp
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

$titolo = "Aggiornamento cattedre docente di sostegno";
$script = "";
$maxcattedre = 10;
stampa_head($titolo, "", $script, "MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='cat_sost.php'>Gestione cattedre</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$sql = "DELETE FROM tbl_cattnosupp WHERE iddocente = '" . stringa_html('docente') . "' AND idalunno<>0";
$ris = mysqli_query($con, inspref($sql));
if (mysqli_affected_rows($con) == 0)
{
    print("\n<FONT SIZE='+2'> <CENTER>Vecchi dati non presenti! </CENTER></FONT>");
}
else
{
    print("\n<FONT SIZE='+2'> <CENTER>Vecchi dati cancellati!</CENTER> </FONT>");
}

print("<br/>");
$doc = stringa_html('docente');

if ($doc != "")
{

    for ($i = 1; $i <= $maxcattedre; $i++)
    {

        $stralu = "alu$i";
        $strmat = "materie$i";
        $alu = stringa_html($stralu);

        if ($alu != "")
        {

            $tbl_materie = stringa_html($strmat);
            if ($tbl_materie)
            {
                foreach ($tbl_materie as $mat)
                {
                    $sql = "INSERT INTO tbl_cattnosupp(iddocente, idmateria, idclasse,idalunno) values ('$doc','$mat','" . estrai_classe_alunno($alu, $con) . "','$alu')";
                    if ($ris = mysqli_query($con, inspref($sql)))
                    {
                        print("Inserita materia " . decodifica_materia($mat, $con) . " per alunno " . estrai_alunno_data($alu, $con) . ".<br/>");
                    }
                    else
                    {
                        print("Errore in inserimento!");
                    }
                }
            }
        }
    }
    print("<big><center>Inserimento completato!");
}
else
{
    print("<big><center>Nessun dato da inserire!");
}


// print "<Center>Inserite $cont cattedre!</Center>!";

// INSERISCO LE CATTEDRE PER LE SUPPLENZE
$querydel = "DELETE FROM tbl_cattsupp WHERE 1=1";
mysqli_query($con, inspref($querydel)) or die (inspref($querydel));
// print inspref($querydel);
$querysupp = "INSERT INTO tbl_cattsupp(iddocente,idmateria,idclasse)
SELECT iddocente, 0, idclasse
FROM tbl_docenti, tbl_classi";

// print inspref($querysupp);
mysqli_query($con, inspref($querysupp)) or die (inspref($querysupp));


mysqli_close($con);
stampa_piede("");

