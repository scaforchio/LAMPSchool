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


$iddoc = stringa_html('iddoc');


print ('<form method="POST" action="statinser.php" name="stat">


         <p align="center">
         <table align="center">');

//
//   Leggo il nominativo del docente e lo visualizzo
//

$query = "SELECT iddocente, cognome, nome FROM tbl_docenti";
$ris = mysqli_query($con, inspref($query));


print("    
             <tr>
              <td><b>Docente</b></td>

          <td><select name='iddoc' ONCHANGE='stat.submit()'>");

while ($nom = mysqli_fetch_array($ris))
{
    $iddocente = $nom["iddocente"];
    $cognomedoc = $nom["cognome"];
    $nomedoc = $nom["nome"];
    $nominativo = $cognomedoc . " " . $nomedoc;
    if ($iddocente != $iddoc)
    {
        print "<option value=$iddocente>$nominativo";
    }
    else
    {
        print "<option value=$iddocente selected>$nominativo";
    }
}

print ("</td></tr>");


echo('</form></td>
   
       </table>');


mysqli_close($con);
stampa_piede("");

