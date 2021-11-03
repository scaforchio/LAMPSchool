<?php

require_once '../lib/req_apertura_sessione.php';

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

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$titolo = "Riepilogo argomenti svolti (per data)";
$script = "";
stampa_head($titolo, "", $script, "LT");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));



$id_ut_doc = $_SESSION["idutente"];
if ($id_ut_doc > 2100000000)
    $id_ut_doc -= 2100000000;

//creiamo la matrice di associazione delle materie con la denominazione
//viene utilizzata durate la stampa della tabella per evitare query "spreca risorse"
$query = "SELECT DISTINCT tbl_materie.idmateria as idmateria, tbl_alunni.idclasse as idclasse, denominazione
        FROM tbl_alunni, tbl_materie, tbl_cattnosupp, tbl_docenti
        WHERE tbl_alunni.idclasse = tbl_cattnosupp.idclasse
        AND tbl_cattnosupp.iddocente = tbl_docenti.iddocente
        AND tbl_cattnosupp.idmateria = tbl_materie.idmateria
        AND tbl_alunni.idalunno =$id_ut_doc
        AND tbl_docenti.iddocente <>1000000000
        ORDER BY denominazione";
$ris = eseguiQuery($con, $query);
$matassoclist = array();

while ($nom = mysqli_fetch_array($ris)) {
    $matassoclist[$nom["idmateria"]] = $nom["denominazione"];
}

//creiamo la matrice di associazione degli ID docenti ai nomi e cognomi
//per evitare query "spreca risorse" durante la stampa della tabella
$querydoc = "SELECT `iddocente`, `cognome`, `nome` FROM `tbl_docenti`;";
$risdoc = eseguiQuery($con, $querydoc);
$docassoclist = array();

while ($doc = mysqli_fetch_array($risdoc)) {
    $docassoclist[$doc["iddocente"]] = $doc["cognome"] . " " . $doc["nome"];
}

$giorno = '';
$meseanno = '';
$anno = '';
$mese = '';

$giorno = stringa_html('gio');
$meseanno = stringa_html('meseanno');


// Le variabili di sessione servono agli altri programmmi a stabilire che si proviene
// dal registro per poter fare automaticamente ritorno qui.

$_SESSION['prove'] = 'riepargomgendata.php';
$_SESSION['regma'] = $meseanno;
$_SESSION['reggi'] = $giorno;

$anno = substr($meseanno, 5, 4);
$mese = substr($meseanno, 0, 2);

$giornosettimana = "";

if ($giorno == '') {
    $giorno = date('d');
}
if ($mese == '') {
    $mese = date('m');
}
if ($anno == '') {
    $anno = date('Y');
}

print('
         <form method="post" action="riepargomgendata.php" name="argomenti">
   
         <p align="center">
         <table align="center">');

print(' <tr>
         <td width="50%"><b>Data (gg/mm/aaaa)</b></td>');


//
//   Inizio visualizzazione della data
//


echo ('   <td width="50%">');
echo ('   <select name="gio" ONCHANGE="argomenti.submit()">');
require '../lib/req_aggiungi_giorni_a_select.php';

echo ("</select>");

echo ('   <select name="meseanno" ONCHANGE="argomenti.submit()">');
require '../lib/req_aggiungi_mesi_a_select.php';
echo ("</select>");


//
//  Fine visualizzazione della data
//


echo ("        
      </td></tr>");


echo ('</table>');
echo ('</form>');

$dataoggi = "$anno-$mese-$giorno";
$datadomani = aggiungi_giorni($dataoggi, 1);
$dataieri = aggiungi_giorni($dataoggi, -1);
if (giorno_settimana($dataieri) == "Dom") {
    if ($_SESSION['giornilezsett'] == 6)
        $dataieri = aggiungi_giorni($dataieri, -1);
    else
        $dataieri = aggiungi_giorni($dataieri, -2);
}
if (giorno_settimana($datadomani) == "Dom" | (giorno_settimana($datadomani) == "Sab" & $_SESSION['giornilezsett'] == 5)) {

    if ($_SESSION['giornilezsett'] == 6)
        $datadomani = aggiungi_giorni($datadomani, +1);
    else
        $datadomani = aggiungi_giorni($datadomani, +2);
}
$gioieri = substr($dataieri, 8, 2);
$giodomani = substr($datadomani, 8, 2);
$maieri = substr($dataieri, 5, 2) . " - " . substr($dataieri, 0, 4);
$madomani = substr($datadomani, 5, 2) . " - " . substr($datadomani, 0, 4);

print "<br><center>";
if ($dataieri >= $_SESSION['datainiziolezioni'])
    print("<a href='riepargomgendata.php?gio=$gioieri&meseanno=$maieri'><img src='../immagini/indietro.png'></a>");
print("&nbsp;&nbsp;&nbsp;");
if ($datadomani <= $_SESSION['datafinelezioni'])
    print("<a href='riepargomgendata.php?gio=$giodomani&meseanno=$madomani'><img src='../immagini/avanti.png'></a>");
print "</center>";

echo ('<hr>');

if ($dataieri >= $_SESSION['datainiziolezioni'] && $datadomani <= $_SESSION['datafinelezioni']) {
    $queryclasse = "SELECT idclasse FROM tbl_alunni WHERE idalunno = $id_ut_doc;";
    $risclasse = eseguiQuery($con, $queryclasse);
    $idclasse = mysqli_fetch_array($risclasse)["idclasse"];
    $query = 'SELECT * FROM tbl_classi WHERE idclasse="' . $idclasse . '" ';
    $ris = eseguiQuery($con, $query);

    if ($val = mysqli_fetch_array($ris)) {
        $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
    }

    echo '<center><h3>Argomenti ed attivit&agrave; svolte nella classe ' . $classe . '</h3></center>';

    //
    //   ESTRAZIONE DATI DELLE LEZIONI
    //
    if ($idclasse != "") {
        //calcoliamo la data di oggi prelevando il valore sotto forma di stringa che ci da il...
        //...selettore dei giorni e trasformiamolo in un valore leggibile dal DB
        $query = "select * from tbl_lezioni where idclasse='$idclasse' and datalezione='$dataoggi' and (argomenti<>'' or attivita<>'')";
        $rislez = eseguiQuery($con, $query);

        if (mysqli_num_rows($rislez) == 0) {
            print "<center><br><b>Nessum argomento registrato oggi!</b><br></center>";
        } else {
            print "
                    <table border=2 align='center'>
                        <tr class='prima'>
                            <td width=15%>Docente</td>    
                            <td width=15%>Materia</td>
                            <td width=35%>Argomenti</td>
                            <td width=35%>Attivit&agrave;</td>";

            while ($reclez = mysqli_fetch_array($rislez)) {
                //se non è lezione di gruppo stampa direttamente
                if ($reclez['idlezionegruppo'] == NULL || $reclez['idlezionegruppo'] == 0) {
                    stampa_lez(
                        $con,
                        $docassoclist,
                        $reclez['idlezione'], 
                        $matassoclist[$reclez['idmateria']],
                        $reclez['argomenti'],
                        $reclez['attivita']
                    );
                } else {
                    // VERIFICO SE ALUNNO APPARTIENE A GRUPPO
                    if (verifica_alunno_lezionegruppo($id_ut_doc, $reclez['idlezionegruppo'], $con)){
                        stampa_lez(
                            $con,
                            $docassoclist,
                            $reclez['idlezione'], 
                            $matassoclist[$reclez['idmateria']],
                            $reclez['argomenti'],
                            $reclez['attivita']
                        );
                    }
                }
            }
            print "</table>";
            
            // VISUALIZZARE ARGOMENTI SOSTEGNO
            if (alunno_certificato($id_ut_doc, $con)) {
                $query = "select * from tbl_lezionicert where idclasse='$idclasse' and datalezione='$dataoggi' and idalunno='$id_ut_doc' order by datalezione";

                $rislez = eseguiQuery($con, $query);

                if (mysqli_num_rows($rislez) == 0) {
                    print "<center><br><b>Nessuna attività di sostegno registrata!</b><br></center>";
                } else {
                    print "<center><br><b>Attività di sostegno</b><br><br></center>
                    <table border=2 align='center'>
                        <tr class='prima'>
                            <td width=15%>Docente</td>    
                            <td width=15%>Materia</td>
                            <td width=35%>Argomenti</td>
                            <td width=35%>Attivit&agrave;</td>";

                    while ($reclez = mysqli_fetch_array($rislez)) {
                        stampa_lez(
                            $con,
                            $docassoclist,
                            $reclez['idlezione'], 
                            $matassoclist[$reclez['idmateria']],
                            $reclez['argomenti'],
                            $reclez['attivita']
                        );
                    }

                    print "</table>";
                }
            }
        }
    }
}

mysqli_close($con);

stampa_piede("");

function stampa_lez($con, $docassoclist, $idlezione, $materia, $argomenti, $attvità)
{
    print "<tr>";
    //selezioniamo tutti i docenti che hanno firmato quella lezione
    $lezquery = "SELECT `iddocente` FROM `tbl_firme` WHERE `idlezione` = $idlezione;";
    $risdocs = eseguiQuery($con, $lezquery);
    if(mysqli_num_rows($risdocs) == 0){
        print "<td>Nessuna firma</td>";
    }else{
        //stampa tutti i docenti corrispondenti alla lezione
        print "<td>";
        $f = true;
        while($docente = mysqli_fetch_array($risdocs)){
            //stampa la virgola solo se ci sono più docenti
            if(!$f)
                print ", ";
            $f = false;
            print $docassoclist[$docente['iddocente']];
        }
        print "</td>";
    }
    //stampa il resto della tabella
    //i dati sono safe perchè sanificati in input, non serve stringa_html()
    print "<td>" . $materia . "</td><td>" . $argomenti . "&nbsp;</td><td>" . $attvità . "&nbsp;</td></tr>";
}
