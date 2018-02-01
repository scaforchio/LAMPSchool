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

$titolo = "Situazione scrutini";
$script = '';

stampa_head($titolo,"",$script,"SDMAP");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$periodo = stringa_html('periodo');

$id_ut_doc = $_SESSION["idutente"];

//if ($giorno=='')
//   $giorno=date('d');
//if ($mese=='')
//   $mese=date('m');
//if ($anno=='')
//   $anno=date('Y');


print ('
         <form method="post" action="sitscrutini.php" name="voti">
   
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
echo("<option value=''>&nbsp;</option>");
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
if ($livello_scuola=='4')
{
    if ($periodo == '9')
    {
        echo("<option selected value='9'>Scrutini integrativi</option>");
    }
    else
    {
        echo("<option value='9'>Scrutini integrativi</option>");
    }

}

echo("</select>");
echo("</td></tr>");


//
//  Fine visualizzazione del quadrimestre
//

print ("</table></form>");
//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

if ($periodo!="")
{
//Esecuzione query
    $query = "SELECT * FROM tbl_scrutini,tbl_classi WHERE tbl_scrutini.idclasse=tbl_classi.idclasse and periodo=$periodo ORDER BY anno, sezione, specializzazione";  // 0=supplenza, -1=comportamento

    if (!($ris = mysqli_query($con, inspref($query))))
    {
        print "Query fallita";
    }
    else
    {
        print "<br>";
        print "<CENTER><TABLE BORDER='1'>";
        print "<TR class='prima'><TD ALIGN='CENTER'><B>Classe</B></TD><TD>Data</TD><TD>Stato</TD><TD COLSPAN='1' ALIGN='CENTER'><B>Azione</B></TD></TR>";

        while ($dati = mysqli_fetch_array($ris))
        {
            print "<TR class='oddeven'><TD>" . $dati['anno'] . " " . $dati['sezione'] . " " . $dati['specializzazione'] . "</TD>";
            print "<TD>" . data_italiana($dati['datascrutinio']) . "</TD>";
            print "<TD align='CENTER'><B>" . $dati['stato'] . "</B></TD>";
            if ($dati['stato'] == "C")
            {
                print "<TD align='CENTER'><A HREF='cambiastato.php?periodo=$periodo&idscrutinio=" . $dati['idscrutinio'] . "&nuovostato=A'>APRI</A>";
            }
            if ($dati['stato'] == "A")
            {
                print "<TD align='CENTER'><A HREF='cambiastato.php?periodo=$periodo&idscrutinio=" . $dati['idscrutinio'] . "&nuovostato=C'>CHIUDI</A>";
            }


            print "</TD></TR>";
        }
        print "</CENTER></TABLE>";
    }
}
mysqli_close($con);


stampa_piede("");


