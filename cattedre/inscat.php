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

$titolo = "Aggiornamento cattedre docente";
$script = "";
stampa_head($titolo, "", $script, "MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='cat.php'>Gestione cattedre</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$sql = "DELETE FROM tbl_cattnosupp WHERE iddocente = '" . stringa_html('docente') . "' AND idalunno=0";
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
    for ($i = 1; $i <= 15; $i++)
    {

        $strmat = "mat$i";
        $strcla = "classe$i";

        $mat = stringa_html($strmat);

        if ($mat != "")
        {
            if ($mat == "ALL")
            {
                $sql = "SELECT idmateria FROM tbl_materie WHERE idmateria>0 ORDER BY idmateria";
                $ris = mysqli_query($con, inspref($sql));
                while ($materia = mysqli_fetch_array($ris))
                {

                    $tbl_classi = stringa_html($strcla);
                    if ($tbl_classi)
                    {
                        foreach ($tbl_classi as $cla)
                        {
                            $sql = "INSERT INTO tbl_cattnosupp(iddocente, idmateria, idclasse) values ('$doc','" . $materia["idmateria"] . "','$cla')";
                            if ($ris2 = mysqli_query($con, inspref($sql)))
                            {
                                print("Inserita materia " . decodifica_materia($materia["idmateria"], $con) . " per classe " . decodifica_classe($cla, $con) . ".<br/>");
                            }
                            else
                            {
                                print("Errore in inserimento!");
                            }
                        }
                    }
                }
            }
            else
            {
                $tbl_classi = stringa_html($strcla);
                if ($tbl_classi)
                {
                    foreach ($tbl_classi as $cla)
                    {
                        $sql = "INSERT INTO tbl_cattnosupp(iddocente, idmateria, idclasse) values ('$doc','$mat','$cla')";
                        if ($ris = mysqli_query($con, inspref($sql)))
                        {
                            print("Inserita materia " . decodifica_materia($mat, $con) . " per classe " . decodifica_classe($cla, $con) . ".<br/>");
                        }
                        else
                        {
                            print("Errore in inserimento!");
                        }
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
//
//  AGGIORNAMENTO CATTEDRE PER PRESIDE
//
/*
$cont=0; 

$sql="DELETE FROM tbl_cattnosupp WHERE iddocente = 1000000000 and idalunno=0";
 $ris=mysqli_query($con,inspref($sql));
 
 
$sql="select distinct idmateria, idclasse from tbl_cattnosupp";
$ris=mysqli_query($con,inspref($sql));
while ($catt=mysqli_fetch_array($ris))
{
	 $cont++;
     $sql="INSERT INTO tbl_cattnosupp(iddocente, idmateria, idclasse) values (1000000000,'".$catt["idmateria"]."','".$catt["idclasse"]."')";
     if(!$ris2=mysqli_query($con,inspref($sql)))
     {
          print("Errore in inserimento cattedre preside!");
     }
     
}   
*/

$querydel = "DELETE FROM tbl_cattnosupp WHERE iddocente = 1000000000 AND idalunno=0";
mysqli_query($con, inspref($querydel)) or die (inspref($querydel));

$querypres = "INSERT INTO tbl_cattnosupp(iddocente,idmateria,idclasse)
SELECT DISTINCT 1000000000, idmateria, idclasse
FROM tbl_cattnosupp";

mysqli_query($con, inspref($querypres)) or die (inspref($querypres));


// INSERISCO LE CATTEDRE PER LE SUPPLENZE
$querydel = "DELETE FROM tbl_cattsupp WHERE 1=1";
mysqli_query($con, inspref($querydel)) or die (inspref($querydel));
// print inspref($querydel);
$querysupp = "INSERT INTO tbl_cattsupp(iddocente,idmateria,idclasse)
SELECT iddocente, 0, idclasse
FROM tbl_docenti, tbl_classi";

// print inspref($querysupp);
mysqli_query($con, inspref($querysupp)) or die (inspref($querysupp));


// print "<Center>Inserite $cont cattedre!</Center>!";


mysqli_close($con);
stampa_piede("");

