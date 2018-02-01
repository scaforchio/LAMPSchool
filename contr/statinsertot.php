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

$titolo = "Statistiche di inserimento dei dati per docente";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


print ("
         <p align='center'>
         <table align='center' border='1'>");

//
//   Leggo il nominativo del docente e lo visualizzo
//

$query = "SELECT iddocente, cognome, nome FROM tbl_docenti ORDER BY cognome, nome";
$ris = mysqli_query($con, inspref($query));


while ($nom = mysqli_fetch_array($ris))
{


    $iddocente = $nom["iddocente"];
    $cognomedoc = $nom["cognome"];
    $nomedoc = $nom["nome"];
    $nominativo = $cognomedoc . " " . $nomedoc;
    if ($iddocente != 1000000000)
    {
        print "<tr><td align='center' colspan='3'><b>";
        print "$nominativo";


        //
        // Ricerco i voti inseriti dal docente
        //

        $query = "SELECT count(*) AS numerovoti, tbl_classi.idclasse, tbl_classi.anno, tbl_classi.sezione, tbl_classi.specializzazione
                  FROM tbl_valutazioniintermedie , tbl_alunni, tbl_classi
                  WHERE tbl_valutazioniintermedie.idalunno = tbl_alunni.idalunno
                  AND tbl_alunni.idclasse = tbl_classi.idclasse
                  AND iddocente = $iddocente;";
        $ris4 = mysqli_query($con, inspref($query));
        $nom4 = mysqli_fetch_array($ris4);
        $numerovotidoc = $nom4["numerovoti"];
        if ($numerovotidoc == "") $numerovotidoc = 0;
        print (" - Voti inseriti: $numerovotidoc");


        //
        // Ricerco le tbl_lezioni inserite dal docente
        //

        $query = "SELECT count( idlezione ) AS numerolezioni
                  FROM tbl_lezioni
                  WHERE iddocente = $iddocente;";
        $ris5 = mysqli_query($con, inspref($query));
        $nom5 = mysqli_fetch_array($ris5);
        $numerolezionidoc = $nom5["numerolezioni"];
        if ($numerolezionidoc == "") $numerolezionidoc = 0;
        print (" - Lezioni inserite: $numerolezionidoc");

        print "</b></td></tr>";


        //
        //  Ricerco le cattedre del docente
        //

        $query = "select tbl_cattnosupp.idmateria, tbl_cattnosupp.idclasse,anno,sezione,specializzazione,denominazione
      from tbl_cattnosupp,tbl_classi,tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria=tbl_materie.idmateria order by anno, sezione, specializzazione";
        $ris1 = mysqli_query($con, inspref($query));
        while ($nom1 = mysqli_fetch_array($ris1))
        {
            $idclasse = $nom1["idclasse"];
            $classe = $nom1["anno"] . " " . $nom1["sezione"] . " " . $nom1["specializzazione"];
            $idmateria = $nom1["idmateria"];
            $materia = $nom1["denominazione"];

            print ("<tr><td>$classe - $materia</td>");

            //
            // Ricerco i voti inseriti per la cattedra
            //

            $query = "SELECT count( * ) AS numerovoti, tbl_classi.idclasse, tbl_classi.anno, tbl_classi.sezione, tbl_classi.specializzazione
                  FROM tbl_valutazioniintermedie , tbl_alunni, tbl_classi
                  WHERE tbl_valutazioniintermedie.idalunno = tbl_alunni.idalunno
                  AND tbl_alunni.idclasse = tbl_classi.idclasse
                  AND tbl_classi.idclasse = $idclasse
                  AND tbl_valutazioniintermedie.idmateria = $idmateria;";
            $ris2 = mysqli_query($con, inspref($query)) or die (mysqli_error($con));
            $nom2 = mysqli_fetch_array($ris2);
            $numerovoti = $nom2["numerovoti"];
            if ($numerovoti == "") $numerovoti = 0;
            print ("<td>Voti: $numerovoti</td>");


            //
            // Ricerco le tbl_lezioni inserite
            //

            $query = "SELECT count( * ) AS numerolezioni
          FROM tbl_lezioni
          WHERE idclasse = $idclasse AND idmateria = $idmateria;";
            $ris3 = mysqli_query($con, inspref($query)) or die (mysqli_error($con));
            $nom3 = mysqli_fetch_array($ris3);
            $numerolezioni = $nom3["numerolezioni"];
            if ($numerolezioni == "") $numerolezioni = 0;
            print ("<td>Lezioni: $numerolezioni</td>");


            print ("</tr>");
        }
    }
}


print ("</td></tr>");


print ("</table>");

$query = "SELECT count( * ) AS numerovoti FROM tbl_valutazioniintermedie";
$ris = mysqli_query($con, inspref($query));
$nom = mysqli_fetch_array($ris);
$numerovotitot = $nom["numerovoti"];

$query = "SELECT count( * ) AS numerolezioni FROM tbl_lezioni";
$ris = mysqli_query($con, inspref($query));
$nom = mysqli_fetch_array($ris);
$numerolezionitot = $nom["numerolezioni"];

print ("<center><b><big><big>Totale voti: $numerovotitot - Totale lezioni: $numerolezionitot<small><small></b></center>");


mysqli_close($con);
stampa_piede("");

