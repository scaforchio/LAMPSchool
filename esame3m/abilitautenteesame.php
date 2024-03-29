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


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


$titolo = "Abilitazione utente esame";

$script = "<script type='text/javascript'>
         <!--
            function printPage()
            {
               if (window.print)
                  window.print();
               else
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');            }
         //-->
         </script>";
stampa_head($titolo, "", $script, "M");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$_SESSION['passwordesame'] = creapassword();
$query = "update tbl_parametri set valore=md5(md5('".$_SESSION['passwordesame']."')) where parametro='passwordesame' ";
eseguiQuery($con, $query);
//
//   IMPOSTAZIONE CLASSE D'ESAME PER TUTTI GLI ALUNNI DELLE CLASSI TERMINALI
//

if ($_SESSION['livello_scuola'] == '2')
{
    $ricercaterze = " AND anno='3' ";
} else
{
    $ricercaterze = " AND anno='8' ";
}

// VERIFICO CHE CI SIANO GLI SCRUTINI CHIUSI PER TUTTE LE
// CLASSI

$query = "select * from tbl_classi "
        . "where true $ricercaterze";

$ris = eseguiQuery($con, $query);

$classiterzemedie = mysqli_num_rows($ris);

$query = "select * from tbl_scrutini,tbl_classi "
        . "where tbl_scrutini.idclasse=tbl_classi.idclasse "
        . "and tbl_scrutini.periodo=2 "
        . "and tbl_scrutini.stato='C' "
        . $ricercaterze;

$ris = eseguiQuery($con, $query);

$classiconscrutiniochiuso = mysqli_num_rows($ris);


if ($classiconscrutiniochiuso == $classiterzemedie)
{
    // ELIMINO EVENTUALI ALUNNI CON IMPOSTATA CLASSE ESAME DELL'ANNO PRECEDENTE
    $query = "UPDATE tbl_alunni SET idclasseesame=0 WHERE 1=1";

    eseguiQuery($con, $query);

    $query = "UPDATE tbl_alunni SET idclasseesame=idclasse where idclasse in
             (SELECT DISTINCT tbl_classi.idclasse FROM tbl_classi
              WHERE 1=1 $ricercaterze
              ORDER BY idclasse)";

    eseguiQuery($con, $query);


// Esclusione alunni con esito negativo agli scrutini

    $query = "SELECT * FROM tbl_alunni WHERE idclasseesame<>'0' AND idclasse<>'0'";
    $ris = eseguiQuery($con, $query);
    while ($rec = mysqli_fetch_array($ris))
    {
        $idtipoesito = estrai_idtipoesito($rec['idalunno'], $con);
        if ($idtipoesito > 0)
        {
            if (passaggio($idtipoesito, $con) != 0)
            {
                $query = "UPDATE tbl_alunni SET idclasseesame=0 WHERE idalunno=" . $rec['idalunno'];
                eseguiQuery($con, $query);
            }
        }
    }


    print "<br><br><center>Accedere per gestione esami con:<br><br>Utente: <b>esamidistato</b><br><br>Password: <b>".$_SESSION['passwordesame']."</b><br><br>";

    print "<center><img src='../immagini/stampa.png' onClick='printPage();'</center>";
} else
{
    print "<center><br><br><b>GLI SCRUTINI NON SONO COMPLETI!</b><br>"
            . "Classi terze: $classiterzemedie - Scrutini chiusi: $classiconscrutiniochiuso</CENTER>";
}

mysqli_close($con);
stampa_piede("");

