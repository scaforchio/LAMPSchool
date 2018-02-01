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
$idclasse = stringa_html('idclasse');
$idalunno = stringa_html('idalunno');



$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


//
//    Parte iniziale della pagina
//

$titolo = "Inserimento scheda d'esame alunno";
$script = "";

// PRELIEVO DATI DA INSERIRE

$votoammissione=stringa_html('votoamm');
$votom1=stringa_html('votom1');
$votom2=stringa_html('votom2');
$votom3=stringa_html('votom3');
$votom4=stringa_html('votom4');
$votom5=stringa_html('votom5');
$votom6=stringa_html('votom6');
$votom7=stringa_html('votom7');
$votom8=stringa_html('votom8');
$votom9=stringa_html('votom9');
$votoorale=stringa_html('votocoll');
$mediascramm=stringa_html('mediaamsch');
$mediafinale=stringa_html('mediafinah');
$votofinale=stringa_html('vtfinah');
$scarto=stringa_html('scartoh');
$consorientcons=stringa_html('consorientcons');
$provasceltam1=stringa_html('provasceltam1');
$provasceltam2=stringa_html('provasceltam2');
$provasceltam3=stringa_html('provasceltam3');
$provasceltam4=stringa_html('provasceltam4');
$provasceltam5=stringa_html('provasceltam5');
$provasceltam6=stringa_html('provasceltam6');
$provasceltam7=stringa_html('provasceltam7');
$provasceltam8=stringa_html('provasceltam8');
$provasceltam9=stringa_html('provasceltam9');
$criteri1=stringa_html('criteri1');
$criteri2=stringa_html('criteri2');
$criteri3=stringa_html('criteri3');
$criteri4=stringa_html('criteri4');
$criteri5=stringa_html('criteri5');
$criteri6=stringa_html('criteri6');
$criteri7=stringa_html('criteri7');
$criteri8=stringa_html('criteri8');
$criteri9=stringa_html('criteri9');
$votopniita=stringa_html('votopniita');
$votopnimat=stringa_html('votopnimat');
$datacolloquio=data_to_db(stringa_html('datacolloquio'));
$tracciacolloquio=stringa_html('tracciacolloquio');
$giudiziocolloquio=stringa_html('giudiziocolloquio');
$giudiziocomplessivo=stringa_html('giudiziocomplessivo');
$consorientcomm=stringa_html('consorientcomm');
$lode=stringa_html('lode');
$unanimita=stringa_html('unanimita');
$ammissioneterza=stringa_html('ammissioneterza');

if ($lode=='on')
    $lode='1';
else
    $lode='0';
if ($unanimita=='on')
        $unanimita='1';
else
        $unanimita='0';
if ($ammissioneterza=='on')
        $ammissioneterza='1';
else
        $ammissioneterza='0';
stampa_head($titolo, "", $script, "E");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$query="update tbl_esesiti set
        votoammissione='$votoammissione',
        votom1='$votom1',
        votom2='$votom2',
        votom3='$votom3',
        votom4='$votom4',
        votom5='$votom5',
        votom6='$votom6',
        votom7='$votom7',
        votom8='$votom8',
        votom9='$votom9',
        criteri1='$criteri1',
        criteri2='$criteri2',
        criteri3='$criteri3',
        criteri4='$criteri4',
        criteri5='$criteri5',
        criteri6='$criteri6',
        criteri7='$criteri7',
        criteri8='$criteri8',
        criteri9='$criteri9',
        provasceltam1='$provasceltam1',
        provasceltam2='$provasceltam2',
        provasceltam3='$provasceltam3',
        provasceltam4='$provasceltam4',
        provasceltam5='$provasceltam5',
        provasceltam6='$provasceltam6',
        provasceltam7='$provasceltam7',
        provasceltam8='$provasceltam8',
        provasceltam9='$provasceltam9',
        votoorale='$votoorale',
        mediascramm='$mediascramm',
        mediafinale='$mediafinale',
        votofinale='$mediafinale',
        scarto='$scarto',
        votopniita='$votopniita',
        votopnimat='$votopnimat',
        datacolloquio='$datacolloquio',
        tracciacolloquio='$tracciacolloquio',
        giudiziocolloquio='$giudiziocolloquio',
        giudiziocomplessivo='$giudiziocomplessivo',
        consorientcons='$consorientcons',
        consorientcomm='$consorientcomm',
        lode='$lode',
        unanimita='$unanimita',
        ammissioneterza='$ammissioneterza'
        where idalunno=$idalunno";
mysqli_query($con,inspref($query));





print "
        <form method='post' id='formscr' action='../esame3m/rieptabesame.php'>
        <input type='hidden' name='cl' value='$idclasse'>


        </form>
        <SCRIPT language='JavaScript'>
           document.getElementById('formscr').submit();
        </SCRIPT>";
// fclose($fp);
mysqli_close($con);

stampa_piede("");

