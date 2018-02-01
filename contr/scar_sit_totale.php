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
$iddocente = $_SESSION["idutente"];
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Scaricamento Situazione Registri";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";

stampa_head($titolo, "", $script,"PMSD");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


print "<center><br>Lasciare tutto selezionato per scaricare la situazione completa</center><br>";


$query = "SELECT idcattedra, cognome, nome, tbl_cattnosupp.idmateria, tbl_classi.idclasse,tbl_classi.anno,tbl_classi.sezione,tbl_classi.specializzazione, tbl_materie.denominazione
    FROM tbl_cattnosupp,tbl_classi,tbl_materie,tbl_docenti
    WHERE tbl_cattnosupp.idclasse=tbl_classi.idclasse
    AND tbl_cattnosupp.idmateria=tbl_materie.idmateria
    AND tbl_cattnosupp.iddocente=tbl_docenti.iddocente
    AND tbl_cattnosupp.iddocente<>1000000000
    ORDER BY cognome, nome, denominazione, anno, specializzazione, sezione";
// print inspref($query);
$ris = mysqli_query($con, inspref($query));

print "<form name='espreg' action='scar_sit_totale_esport.php' method='POST'>";
print "<center>";
print "<br>Tabelloni:";
print "<input type='checkbox' name='tab' checked> ";
print "Argomenti:";
print "<input type='checkbox' name='arg' checked> ";
print "Note:";
print "<input type='checkbox' name='not' checked>";
print "<br/><br/><select multiple name='cattedre[]' size=20>";

while ($lez = mysqli_fetch_array($ris))
{
    $ann = $lez['anno'];
    $sez = $lez['sezione'];
    $spe = $lez['specializzazione'];
    $mat = $lez['denominazione'];
    $cat = $lez['idcattedra'];
    $cog = $lez['cognome'];
    $nom = $lez['nome'];
    print "<option selected value='$cat'>$cog $nom - $mat - $ann $sez $spe</option>";
}
print "</select>";
print "<br><br>";
print "<input type='submit' value='Esporta dati registri'>";
print "</center>";
print "</form>";


mysqli_close($con);
stampa_piede("");

