<?php

session_start();

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
require_once("../lib/fpdf/fpdf.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$idutentealunno = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}



$titolo = "Collegamenti videolezioni docenti";
$script = "";
stampa_head($titolo, "", $script, "L");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


$idclasse = estrai_classe_alunno($idutentealunno-2100000000, $con);

$nomeclasse = decodifica_classe($idclasse, $con);


print "<br><br><b><center>Docenti classe $nomeclasse </b></center>" ;


$query = "select distinct cognome, nome, collegamentowebex from tbl_cattnosupp,tbl_docenti
               where tbl_cattnosupp.iddocente=tbl_docenti.iddocente
               and tbl_cattnosupp.idclasse=$idclasse
               
               and tbl_cattnosupp.iddocente!=1000000000
               order by cognome, nome";


$ris= eseguiQuery($con, $query);

print "<center>";
print "<table border=1>";
print "<tr class='prima'><td>Docente</td><td>Coll. WebEx</td></tr>";
while ($rec= mysqli_fetch_array($ris))
{
    print "<tr>";
    print "<td>".$rec['cognome']." ".$rec['nome']."</td>";
    print "<td align='center'>";
    if ($rec['collegamentowebex']!='')
        print "<a href='".$rec['collegamentowebex']."' target='_BLANK'><img src='../immagini/webex.ico'></a>";
    else
        print "&nbsp;";
    print "</td>";
    print "</tr>";
}
print "</table>";
/*

$condclassi = $condclassi = " true ";
if ($docente != "")
{
    $strclassi = "";
    $query = "select idclasse from tbl_classi where idcoordinatore=$docente";
    $ris = eseguiQuery($con, $query);
    while ($rec = mysqli_fetch_array($ris))
    {
        $strclassi .= $rec['idclasse'] . ",";
    }
    if ($strclassi != "")
        $condclassi = " idclasse in (" . substr($strclassi, 0, strlen($strclassi) - 1) . ")";
}


$queryclasse = "SELECT idclasse,idcoordinatore FROM tbl_classi WHERE $condclassi ORDER BY anno, specializzazione, sezione";
$riscla = eseguiQuery($con,$queryclasse);

while ($reccla = mysqli_fetch_array($riscla))
{
    $schede->AddPage();

    $idclasse = $reccla['idclasse'];
    $daticoordinatore = estrai_dati_docente($reccla['idcoordinatore'], $con);
    if ($_SESSION['suffisso'] == "")
        $suff = "";
    else
        $suff = $_SESSION['suffisso'] . "/";
    $schede->Image("../abc/$suff" . "testata.jpg", 20, 10, 172, 25);


    $denclasse = converti_utf8("Classe: " . decodifica_classe($idclasse, $con));
    // print $denclasse;
    $schede->SetFont('arial', '', 16);
    $schede->setXY(20, 40);
    $schede->Cell(172, 8, $denclasse, 0, 0, "C");
    $schede->setXY(20, 50);
    $intest = converti_utf8("Firme per presa visione programma svolto");
    $schede->Cell(172, 8, $intest, NULL, 1, "C");
    $schede->setXY(20, 60);
    $schede->SetFont('arial', '', 10);
    $intest = converti_utf8("MATERIA");
    $schede->Cell(76, 8, $intest, "TBLR", 1, "C");
    $schede->setXY(96, 60);
    $intest = converti_utf8("ALUNNI");
    $schede->Cell(96, 8, $intest, "TBLR", 1, "C");


    $query = "select distinct denominazione,tbl_materie.idmateria from tbl_cattnosupp,tbl_materie
               where tbl_cattnosupp.idmateria=tbl_materie.idmateria
               and tbl_cattnosupp.idclasse=$idclasse
               and tbl_cattnosupp.iddocente!=1000000000
               and idalunno=0
               order by denominazione";


    $ris = eseguiQuery($con, $query);
    $posY = 68;
    while ($nom = mysqli_fetch_array($ris))
    {


        $schede->setXY(20, $posY);
        $schede->SetFont('arial', 'B', 10);

        $intest = converti_utf8($nom['denominazione']);
        $schede->Cell(76, 7, $intest, "TLR", 1, "");
        $docenti = "";
        if ($idclasse != "")
        {
            //print "<br>";
            $query = "select tbl_cattnosupp.iddocente from tbl_cattnosupp,tbl_docenti
                        where idclasse=$idclasse and idmateria=" . $nom['idmateria'] .
                    " and idalunno=0 and tbl_cattnosupp.iddocente<>1000000000 and tbl_cattnosupp.iddocente=tbl_docenti.iddocente order by cognome";
            // print inspref($query);
            $rismat = eseguiQuery($con, $query);
            $docenti = "";
            while ($recmat = mysqli_fetch_array($rismat))
            {
                $docenti .= converti_utf8(estrai_dati_docente($recmat['iddocente'], $con) . "  ");
            }
        }
        $posY += 7;
        $schede->SetFont('arial', '', 8);
        $schede->setXY(20, $posY);
        $schede->Cell(76, 7, $docenti, "BLR", 1, "");
        $posY -= 7;

        $schede->setXY(96, $posY);
        $schede->Cell(96, 7, "", "TBLR", 1, "");
        $posY += 7;
        $schede->setXY(96, $posY);
        $schede->Cell(96, 7, "", "TBLR", 1, "");
        $posY += 7;
    }

    $posY += 4;
    $schede->setXY(96, $posY);
    $schede->Cell(96, 7, "Il coordinatore di classe", "", 1, "C");
    $posY += 4;
    $schede->setXY(96, $posY);
    $schede->Cell(96, 7, converti_utf8("(" . $daticoordinatore . ")"), "", 1, "C");
    $posY += 7;
    $schede->setXY(96, $posY);
    $schede->Cell(96, 7, "____________________________________", "", 1, "C");
}
$schede->Output("firme.pdf", "I");*/
mysqli_close($con);
stampa_piede("",false); 


