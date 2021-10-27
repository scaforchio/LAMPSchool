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


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Creazione corsi Moodle classe";
$script = "";
stampa_head($titolo, "", $script, "MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$ordinamento = stringa_html('ordinamento');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

print "<center><br><b>Classi</b></center><br>";

$query = "SELECT anno, sezione, specializzazione, idclasse from tbl_classi
            ORDER BY anno,specializzazione,sezione";

$ris = eseguiQuery($con, $query);
$corsi = getCorsiMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle']);
// print "Corsi: $corsi";
if (mysqli_num_rows($ris) > 0)
{
    print "<table border=1 align=center>";
    print "<tr class='prima'><td>Classe</td><td>Creaz.</td></tr>";

    while ($lez = mysqli_fetch_array($ris))
    {
        $ann = $lez['anno'];
        $sez = $lez['sezione'];
        $spe = $lez['specializzazione'];
        $idcla = $lez['idclasse'];
            
            print "<tr class='oddeven'><td>$ann $sez $spe</td><td><a href='creacorsiclasse.php?idclasse=$idcla'><img src='../immagini/create.png'></a></td></tr>";
    }
    print "</table>";
}
else
{
    print '<p>Non ci sono classi.</p>';
}
mysqli_close($con);
stampa_piede("");

