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
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();


// DEFINIZIONE ARRAY PER MEMORIZZAZZIONE IN CSV
$listamaterie = array();
$listamaterie[] = "Alunno";


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


//
//    Parte iniziale della pagina
//

$titolo = "Riepilogo proposte di voto";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";

stampa_head($titolo,"",$script,"SDMAP");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$nome = stringa_html('cl');   // 24/12/2008

$periodo = stringa_html('periodo');
//$anno = stringa_html('anno');


$idclasse = stringa_html('cl');
$id_ut_doc = $_SESSION["idutente"];

//if ($giorno=='')
//   $giorno=date('d');
//if ($mese=='')
//   $mese=date('m');
//if ($anno=='')
//   $anno=date('Y');


print ('
         <form method="post" action="riepproposte.php" name="voti">
   
         <p align="center">
         <table align="center">');


//
//   Inizio visualizzazione del combo box del periodo
//
if ($numeroperiodi == 2)
{
    print('<tr><td width="50%"><b>Quadrimestre</b></td>');
}
else
{
    print('<tr><td width="50%"><b>Trimestre</b></td>');
}

echo('   <td width="50%">');
echo('   <select name="periodo" ONCHANGE="voti.submit()">');

if ($periodo == '1')
{
    echo("<option selected value='1'>Primo</option>");
}
else
{
    echo("<option value='1'>Primo</option>");
}
if ($periodo == '2')
{
    echo("<option selected value='2'>Secondo</option>");
}
else
{
    echo("<option value='2'>Secondo</option>");
}

if ($numeroperiodi == 3)
{
    if ($periodo == '3')
    {
        echo("<option selected value='3'>Terzo</option>");
    }
    else
    {
        echo("<option value='3'>Terzo</option>");
    }
}


echo("</select>");
echo("</td></tr>");


//
//  Fine visualizzazione del quadrimestre
//


//
//   Classi
//

print('
        <tr>
        <td width="50%"><b>Classe</b></p></td>
        <td width="50%">
        <SELECT ID="cl" NAME="cl" ONCHANGE="voti.submit()"><option value=""></option>  ');

//
//  Riempimento combobox delle tbl_classi
//
// $query="select distinct tbl_classi.idclasse,anno,sezione,specializzazione from tbl_classi order by anno,sezione,specializzazione";
if ($tipoutente == "S" | $tipoutente == "P")
{
    $query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY anno,sezione,specializzazione";
}
else
{
    $query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi
           WHERE idcoordinatore=" . $_SESSION['idutente'] . " ORDER BY anno,sezione,specializzazione";
}
$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
    if ($idclasse == $nom["idclasse"])
    {
        print " selected";
    }
    print ">";
    print ($nom["anno"]);
    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
}

print ("</select></td></tr></table></form>");

if ($nome != "")
{

// APERTURA FILE CSV PER MEMORIZZAZIONE PROPOSTE
//$nf=session_id().".csv";
//$nomefile="$cartellabuffer/".$nf;
//$fp = fopen($nomefile, 'w');


    $query = "SELECT distinct tbl_materie.idmateria,sigla FROM tbl_cattnosupp,tbl_materie
WHERE tbl_cattnosupp.idmateria=tbl_materie.idmateria
and tbl_cattnosupp.idclasse=$idclasse
and tbl_cattnosupp.iddocente <> 1000000000
order by tbl_materie.sigla";
    $ris = mysqli_query($con, inspref($query));
    if (mysqli_num_rows($ris) > 0)
    {
        print ("<table align='center' border='1'><tr class='prima' align='center'><td>Alunno</td>");
        while ($nom = mysqli_fetch_array($ris))
        {
            print ("<td>");
            print ($nom["sigla"]);
            $codmat[] = $nom["idmateria"];
            $sigmat[] = $nom["sigla"];
            $listamaterie[] = $nom["sigla"];
            print ("</td>");
        }
//print("<td><b>MEDIA</b></td></tr>");

//$lm=$listamaterie;
//fputcsv($fp, $lm,";");


        $per = $periodo;

        if ($periodo == "1")
        {
            $datarif = $fineprimo;
        }
        if ($periodo == "2" & $numeroperiodi == 2)
        {
            $datarif = $datafinelezioni;
        }
        if ($periodo == "2" & $numeroperiodi == 3)
        {
            $datarif = $finesecondo;
        }
        if ($periodo == "3")
        {
            $datarif = $datafinelezioni;
        }



        $numeroalunno = 0;

        $elencoalunni = estrai_alunni_classe_data($idclasse, $datarif, $con);

        $query = "select * from tbl_alunni where idalunno in ($elencoalunni) order by cognome,nome,datanascita";
        $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
        while ($val = mysqli_fetch_array($ris))
        {
            $listavoti = array();
            // $esiste_voto=false;
            $idalunno = $val["idalunno"];
            $numeroalunno++;
            if ($numeroalunno % 2 == 0)
            {
                $colore = '#FFFFFF';
            }
            else
            {
                $colore = '#C0C0C0';
            }
            echo "<tr bgcolor=$colore>";
            if (!alunno_certificato($val['idalunno'], $con))
            {
                $cert = "";
            }
            else
            {
                $cert = "<img src='../immagini/apply_small.png'>";
            }

            echo "      <td><b>" . $val["cognome"] . " " . $val["nome"] . " " . data_italiana($val["datanascita"]) . " $cert";
            echo " </b></td>";
            $listavoti[] = $val["cognome"] . " " . $val["nome"] . " " . data_italiana($val["datanascita"]);
            $contavoti = 0;
            $sommavoti = 0;
            foreach ($codmat as $cm)
            {
                /*  $query="SELECT unico FROM tbl_proposte
                          WHERE idalunno=$idalunno
                          and idmateria=$cm
                          and unico <> '99'
                          and periodo='$per'";  */
                print "<td align='center'>";
                $query = "SELECT unico FROM tbl_proposte
                      WHERE idalunno=$idalunno
                      and idmateria=$cm
                      and periodo='$per'";
                $rismedia = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
                if ($valmedia = mysqli_fetch_array($rismedia))
                {
                    $outuni = $valmedia["unico"];
                    // $outscr=$valmedia["scritto"];
                    // $outora=$valmedia["orale"];
                    // $outpra=$valmedia["pratico"];

                    if ($outuni != "99")
                    {
                        print dec_to_vot($outuni) . "<sub> </sub>";

                    }
                    /*
                     if ($outscr!="99" & $outscr!=NULL)
                        {
                          print dec_to_vot($outscr)."<sub>S</sub>";

                        }
                     if ($outora!="99" & $outora!=NUL)
                        {
                          print dec_to_vot($outora)."<sub>O</sub>";

                        }
                     if ($outpra!="99" & $outpra!=NUL)
                        {
                          print dec_to_vot($outpra)."<sub>P</sub>";

                        }
                        */


                }
                else
                {
                    print " -- ";

                }
                print "</td>";

            }
            // $outmedia=number_format ( $sommavoti/$contavoti,2);
            // print "<td><b><center>$outmedia</center></b></td>";
            print"</tr>";
            // $lv=$listavoti;
            //fputcsv($fp, $lv,";");
        }
        print "</table>";
//print ("<br/><center><a href='$cartellabuffer/$nf'><img src='../immagini/csv.png'></a></center>");
    }
    else
    {
        print("<center><b><br>Nessun dato presente!</b></center>");
    }

// fclose($fp);
    mysqli_close($con);


}

stampa_piede(""); 

