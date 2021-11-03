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
stampa_head($titolo, "", $script, "TL");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$idalunno = $_SESSION['idstudente'];
$idclasse = "";
$cambiamentoclasse = false;


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


// prelevamento dati alunno

$query = "select * from tbl_alunni where idalunno=$idalunno";
$ris = eseguiQuery($con, $query);

if ($val = mysqli_fetch_array($ris))
{
    echo '<center><b>Voti dell\'Alunno: ' . $val["cognome"] . ' ' . $val["nome"] . '</b></center><br/>';
    echo '<center>Le medie calcolate sono <b>aritmetiche</b> e potrebbero non<br>corrispondere al voto in pagella.</center><br>';
    $idclasse = $val['idclasse'];
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
    print ("<table border=1 align=center><tr><td>Data</td><td align=center>Tipo<br/>valutazione</td><td align=center>Voto</td><td>Giudizio</td></tr>");
    $mat = "";
    while ($val = mysqli_fetch_array($ris))
    {
        $materia = $val['denominazione'];
        $data = data_italiana($val['data']);
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
            print("<tr class=prima><td colspan=4 align=center>$materia</td></tr>");
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
            //se la media è inferiore a 6 stampa il valore in rosso
            if ($mediacalc < 6)
            {
                $colini = "<font color=red><b>";
                $colfin = "</font>";
            } else
            {
                $colini = "";
                $colfin = "";
            }
            print("Media: $colini$mediacalc$colfin</td></tr>");
        }

        if ($voto != "&nbsp;&nbsp;" | $giudizio != "&nbsp;")
        {
            $colore = 'white';
            if ($val['idclasse'] != $idclasse)
            {
                $colore = 'grey';
                $cambiamentoclasse = true;
            }
            print('<tr style="background: $colore; text-align: center;">');
            print("<td>$data</td>");
            print("<td style=\"text-align: center;\">$tipo</td>");
            if ($val['voto'] < 6) // is_numeric($val['votoscritto']))
            {
                $stilecasella = 'style="background: #eb4034; text-align: center;"';
            } else
            {
                if ($val['voto']!=99)
                    $stilecasella = 'style="background: #05ac50; text-align: center;"'; 
                else
                    $stilecasella = 'text-align: center;"'; 
            }
            print("<td $stilecasella>$voto</td>");
            print("<td>$giudizio</td>");
            print("</tr>");
        }
    }
    print ("</table><br/>");
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
stampa_piede("");
