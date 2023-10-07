<?php

require_once '../lib/req_apertura_sessione.php';

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

//
//    VISUALIZZAZIONE DELLE VALUTAZIONI 
//    PER I GENITORI 
//


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

//  istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Visualizzazione voti alunno";
$script = "";
stampa_head_new($titolo, "", $script, "TL");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$idalunno = $_SESSION['idstudente'];
$idclasse = "";
$cambiamentoclasse = false;
$ultima_data_in_secondo = true;

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

function datatostr($int){
    switch ($int) {
        case '1':
            return "Gennaio";
        case '2':
            return "Febbraio";
        case '3':
            return "Marzo";
        case '4':
            return "Aprile";
        case '5':
            return "Maggio";
        case '6':
            return "Giugno";
        case '7':
            return "Luglio";
        case '8':
            return "Agosto";
        case '9':
            return "Settembre";
        case '10':
            return "Ottobre";
        case '11':
            return "Novembre";
        case '12':
            return "Dicembre";
        default:
            return "Default";
    }
}

// prelevamento dati alunno

$query = "select * from tbl_alunni where idalunno=$idalunno";
$ris = eseguiQuery($con, $query);

if ($val = mysqli_fetch_array($ris))
{
    echo '<center><b>Voti dell\'Alunno: ' . $val["cognome"] . ' ' . $val["nome"] . '</b></center><br/>';
    $querymediasg = "select avg(voto) as votomedio from tbl_valutazioniintermedie where idalunno=$idalunno and voto<99";
    $rismediasg = eseguiQuery($con, $querymediasg);
    $recmediasg = mysqli_fetch_array($rismediasg);
    //arrotondiamo a due cifre decimali
    $mediacalcsg = round(
        floatval(
            $recmediasg['votomedio']
        ),
        2,
        PHP_ROUND_HALF_UP
    );
    // Verifichiamo il valore della media e impostiamo il colore del testo a ROSSO/ARANCIO/VERDE o DEFAULT (nero) per Nessun Voto
    if($mediacalcsg < 5 && $mediacalcsg > 0)
    {
        $mediatextcolor = "#eb4034";
    }
    elseif ($mediacalcsg >=5 && $mediacalcsg <6){
        $mediatextcolor = "#ebac34";
    }
    elseif ($mediacalcsg >=6){
        $mediatextcolor = "#05ac50";
    }
    else{
        $mediatextcolor = "#000";
    }
    /*  */
    ?>
    <html> <body> <h3 style="color: <?php echo $mediatextcolor; ?>;"> <center> MEDIA GLOBALE: <?php echo $mediacalcsg; ?> <center></h3> </body> </html>
    <?php
    echo '<center>Le medie calcolate sono <b>aritmetiche</b> e potrebbero non<br>corrispondere al voto in pagella.</center><br>';
    $idclasse = $val['idclasse'];
}

// verifica se siamo ancora nel primo per disabilitare split
$splitattivo = false;
if(date("Y-m-d") > $_SESSION['fineprimo']){
    $splitattivo = true;
}

// prelevamento voti discipline
$query = "select * from tbl_valutazioniintermedie, tbl_materie
          where tbl_valutazioniintermedie.idmateria=tbl_materie.idmateria
          and idalunno=$idalunno
          order by denominazione, data desc";
$ris = eseguiQuery($con, $query);
// print $query;
if (mysqli_num_rows($ris) > 0)
{
    print ("<div style='margin-left: 10px; margin-right: 10px;'>
            <table border=1 align=center class='table table-striped table-bordered'> 
            <thead style='font-weight: bold;'> <tr class='prima'> 
                        <td data-priority='1' align=center>Data e Tipo Valutazione</td>  
                        <td data-priority='2' align=center>Voto</td> 
                        <td data-priority='3' class='not-mobile'>Giudizio</td> 
            </tr> </thead><tbody>");
    
            //<td align=center>Tipo valutazione</td>
    $mat = "";
    while ($val = mysqli_fetch_array($ris))
    {
        $materia = $val['denominazione'];
        $data_y = date_format(date_create($val['data']), "Y");
        $data_m = datatostr(date_format(date_create($val['data']), "n"));
        $data_d = date_format(date_create($val['data']), "j");
        $data = "$data_d $data_m $data_y";
        $tipo = $val['tipo'];
        if ($tipo == 'O')
            $tipo = 'Orale';
        if ($tipo == 'S')
            $tipo = 'Scritta';
        if ($tipo == 'P')
            $tipo = 'Pratica';

        $voto = dec_to_mod($val['voto']);

        $giudizio = $val['giudizio'];
        if ($giudizio == "" | substr($giudizio, 0, 1) == "(")
            $giudizio = "&nbsp;";


        if ($materia != $mat)
        {
            $mat = $materia;
            $matupp = strtoupper($materia);
            
                // SEPARATORE
	            //print("<tr style='border-left: 1px solid white; border-right: 1px solid white'>
		        // <td colspan=4 style='color: white; font-size: 16px;'>-</td></tr>");
            print("<tr class=prima><td colspan=4 align=center><b>$matupp</b></td></tr>");
            //facciamo l'avg() di tutti i voti per la determinata materia per il determinato alunno
            $idmateria = $val["idmateria"];
            $querymedia = "select avg(voto) as votomedio from tbl_valutazioniintermedie where idalunno=$idalunno and idmateria=$idmateria and voto<99";
            $rismedia = eseguiQuery($con, $querymedia);
            $recmedia = mysqli_fetch_array($rismedia);
            //arrotondiamo a due cifre decimali
            $mediacalc = round(
                floatval(
                    $recmedia['votomedio']
                ),
                2,
                PHP_ROUND_HALF_UP
            );
            //stampiamo il valore subito dopo il nome della materia
            print("<tr style=\"background-color: #cfcfcf\"><td colspan=4 align=center>");
            //se la media è inferiore a 5 stampa il valore in rosso
            if ($mediacalc < 5)
            {
                $coloreglobale = "#eb4034";
            } else if ($mediacalc >= 5 && $mediacalc < 6) {
                $coloreglobale = "#ebac34";
            } else
            {
                $coloreglobale = "#05ac50";
            }
            //print("Media: $colini$mediacalc$colfin</td></tr>");

            // CONTROLLO PRIMO QUADRIMESTRE
            $fpdt = new DateTime($_SESSION['fineprimo']);
            $fpdt->modify("+1 day");
            $iniziosecondo = date_format($fpdt,"Y-m-d");
            $querymediaprimo = "select avg(voto) as votomedio from tbl_valutazioniintermedie where idalunno=$idalunno and idmateria=$idmateria and voto<99 and data < '$iniziosecondo' ";
            $rismediaprimo = eseguiQuery($con, $querymediaprimo);
            $recmediaprimo = mysqli_fetch_array($rismediaprimo);
            //arrotondiamo a due cifre decimali
            $mc_primo = round(
                floatval(
                    $recmediaprimo['votomedio']
                ),
                2,
                PHP_ROUND_HALF_UP
            );
            if ($mc_primo < 5)
            {
                $coloreprimo = "#eb4034";
            } else if ($mc_primo >= 5 && $mc_primo < 6) {
                $coloreprimo = "#ebac34";
            } else
            {
                $coloreprimo = "#05ac50";
            }
            if($mc_primo == 0){
                $mc_primo = "-";
                $coloreprimo = "#cfcfcf";
            }

            // CONTROLLO SECONDO QUADRIMESTRE
            $fp = $_SESSION['fineprimo'];
            $querymediasecondo= "select avg(voto) as votomedio from tbl_valutazioniintermedie where idalunno=$idalunno and idmateria=$idmateria and voto<99 and data > '$fp' ";
            $rismediasecondo = eseguiQuery($con, $querymediasecondo);
            $recmediasecondo = mysqli_fetch_array($rismediasecondo);
            //arrotondiamo a due cifre decimali
            $mc_secondo = round(
                floatval(
                    $recmediasecondo['votomedio']
                ),
                2,
                PHP_ROUND_HALF_UP
            );

            if ($mc_secondo < 5)
            {
                $coloresecondo = "#eb4034";
            } else if ($mc_secondo >= 5 && $mc_secondo < 6) {
                $coloresecondo = "#ebac34";
            } else
            {
                $coloresecondo = "#05ac50";
            }
            if($mc_secondo == 0){
                $mc_secondo = "-";
                $coloresecondo = "#cfcfcf";
            }
            ?>
                <table style="width: 100%;" border="1">
                    <tbody>
                        <tr>
                            <td style="text-align: center; background-color: <?php echo $coloreglobale; ?>;">Media Globale: <b><?php echo $mediacalc; ?></b></td>
                            <td style="text-align: center; background-color: <?php echo $coloreprimo; ?>;">Media 1˚ Quadr.: <b><?php echo $mc_primo; ?></b></td>
                            <td style="text-align: center; background-color: <?php echo $coloresecondo; ?>;">Media 2˚ Quadr.: <b><?php echo $mc_secondo; ?></b></td>
                        </tr>
                    </tbody>
                </table>
            <?php
            print("</td></tr>");
        }

        if ($voto != "&nbsp;&nbsp;" | $giudizio != "&nbsp;")
        {
            $colore = 'white';
            if ($val['idclasse'] != $idclasse)
            {
                $colore = 'grey';
                $cambiamentoclasse = true;
            }
            if($splitattivo && $ultima_data_in_secondo && ($val['data'] < $iniziosecondo)){ ?>
                <tr style="background: #cfcfcf">
                    <td colspan=4 style="font-size: 1px;">
                        <center style="color: #cfcfcf">-<center>
                    </td>
                </tr>
            <?php }
            print("<tr style='background: $colore; text-align: center; $bordo'>");
            print("<td style='text-align: center;'>$data | <b>$tipo</b></td>");
            // print("<td style=\"text-align: center;\">$tipo</td>");
            if ($val['voto'] < 5) // is_numeric($val['votoscritto']))
            {
                // voto negativo stile rosso
                $stilecasella = 'style="color: #eb4034; text-align: center; font-weight: bold;"';
            } else if ($val['voto'] >= 5 && $val['voto'] < 6) {
                // voto tra 5 e 6 - arancio
                $stilecasella = 'style="color: #ebac34; text-align: center; font-weight: bold;"';
            } else
            {
                // voto valido quindi verde
                if ($val['voto']!=99)
                {
                    $stilecasella = 'style="color: #05ac50; text-align: center; font-weight: bold;"'; 
                }      
                else
                {
                    //solo giudizio quindi grigio
                    $stilecasella = 'style="background: #cfcfcf; text-align: center; font-weight: bold;"'; 
                }
            }
            print("<td $stilecasella>$voto</td>");
            print("<td>$giudizio</td>");
            print("</tr>");
        }
        $ultima_data_in_secondo = $val['data'] > $_SESSION['fineprimo'];
    }
    print ("</tbody></table></div><br/>");
    // CALCOLO IL VOTO MEDIO DI COMPORTAMENTO
    if ($_SESSION['visvotocomp'] == 'yes')
    {
        $query = "select avg(voto) as votomedio from tbl_valutazionicomp where idalunno=$idalunno";
        $rismedio = eseguiQuery($con, $query);
        $recmedio = mysqli_fetch_array($rismedio);

        $votomedio = $recmedio['votomedio'];
        if ($votomedio != "")
        {
            $votm = round($votomedio * 4) / 4;
            $votom = dec_to_mod($votm);
            print "<b><center>Attuale voto medio per il comportamento: <big>$votom</big></center></b><br>";
        } else
            print "<b><center>Nessun voto di comportamento registrato!</center></b><br>";
    }

    if ($cambiamentoclasse)
    {
        print ("<center><font color='grey'>Le valutazioni con sfondo grigio sono state attribuite in una classe diversa da quella di attuale appartenenza.</font></center>");
    }
} else
{
    print("<br/><big><big><center>Non ci sono voti registrati!</center><small><small><br/>");
}

mysqli_close($con);
stampa_piede_new("");
