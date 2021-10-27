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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
// require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

// istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$idclasse = stringa_html('idclasse');



$titolo = "Elenco docenti della classe";
$script = "";
stampa_head($titolo, "", $script, "MSPD");


print "<center><B>Elenco docenti della " . decodifica_classe($idclasse, $con) . "</B><br><br></center>";
// prelevamento dati alunno
// $rs = $lQuery->selectstar('tbl_alunni', 'idalunno=?', array($codalunno));
$query = "select distinct tbl_cattnosupp.iddocente, cognome, nome, telcel, email,collegamentowebex from tbl_cattnosupp,tbl_docenti
        where tbl_cattnosupp.iddocente=tbl_docenti.iddocente
        and idclasse=$idclasse and tbl_cattnosupp.iddocente<>1000000000
        order by cognome, nome";
//print inspref($query);
$ris = eseguiQuery($con, $query);
$esistono = false;
if (mysqli_num_rows($ris) > 0)
{
    if (verifica_classe_coordinata($_SESSION['idutente'], $idclasse, $con))
    {
        print "<table align='center' border='1'><tr class='prima'><td>Cognome</td><td>Nome</td><td>Cellulare</td><td>Email</td><td>Coll. DAD</td></tr>";

        while ($rec = mysqli_fetch_array($ris))
        {
            print "<tr><td>" . $rec['cognome'] . "</td><td>" . $rec['nome'] . "</td><td>" . $rec['telcel'] . "</td><td> " . $rec['email'] . "</td><td><a href='" . $rec['collegamentowebex'] ."'>". $rec['collegamentowebex'] ."</a></td></tr>";
        }
    } else
    {
        print "<table align='center' border='1'><tr class='prima'><td>Cognome</td><td>Nome</td><td>Coll. DAD</td></tr>";

        while ($rec = mysqli_fetch_array($ris))
        {
            print "<tr><td>" . $rec['cognome'] . "</td><td>" . $rec['nome'] . "</td><td><a href='" . $rec['collegamentowebex'] ."'>". $rec['collegamentowebex'] ."</a></td></tr>";
        }
    }
}

print "</table>";

print "<BR><br><b><i><center>Nessun docente presente!</b></i></center>";
mysqli_close($con);

