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

$visualizzabili = array("image/jpeg", "application/pdf", "image/pjpeg", "image/gif", "image/png");
$cattedra = stringa_html('cattedra');
$tipodoc = stringa_html('tipodoc');
switch ($tipodoc)
{
    case 'pia':
        $titolo = "Gestione piani lavoro";
        $tipodocumento = 1000000001;
        break;
    case 'pro':
        $titolo = "Gestione programmazioni";
        $tipodocumento = 1000000002;
        break;
    case 'rel':
        $titolo = "Gestione relazioni finali";
        $tipodocumento = 1000000003;
        break;
}
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$maxcomp = 20;


$idmateria = "";
$idclasse = "";

print ("
   
   <form action='insdocumprog.php?tipodoc=$tipodoc' method='POST' enctype='multipart/form-data'>
   <p align='center'>
   <table align='center' border='1'>
   <tr class='prima'>
      <td><b>Cattedra</b></td>
      <td><b>File da caricare</b></td>
      <td><b>File caricato</b></td>
      <td><b>Azione</b></td>");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));
$query = "select idcattedra,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione,tbl_materie.idmateria,tbl_cattnosupp.idalunno from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria order by anno, sezione, specializzazione, denominazione";

$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{


    print "<tr><td>" . decodifica_materia($nom['idmateria'], $con) . " " . decodifica_classe($nom['idclasse'], $con) . "</td>";
    $query = "select iddocumento,idmateria,idclasse,docnome,docsize,doctype from tbl_documenti where idmateria=" . $nom['idmateria'] . " and idclasse=" . $nom['idclasse'] . " and idtipodocumento=$tipodocumento";

    $risdoc = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    if ($val = mysqli_fetch_array($risdoc))
    {
        if ($nom['idalunno']==0)
           print ("<td><center>MODIFICA FILE<br/><input type=file name='file_" . $nom['idclasse'] . "_" . $nom['idmateria'] . "' value='Modifica'>  </td><td>");
        else
            print ("<td>&nbsp; </td><td>");
        echo $val["docnome"];
        echo "<font size=1>(" . $val["docsize"] . " bytes)</font></td><td>";
        echo "<a href='actionsdocum.php?action=download&Id=" . $val["iddocumento"] . "' target='_blank'><img src='../immagini/download.jpg' alt='scarica'></a>";

        if (in_array($val["doctype"], $visualizzabili))
        {
            echo "  <a href='actionsdocum.php?action=view&Id=" . $val["iddocumento"] . "' ";
            echo "target='_blank'><img src='../immagini/view.jpg' alt='visualizza'></a>  ";
        }
        if ($nom['idalunno']==0)
            echo " <a href='cancdocumprog.php?tipodoc=$tipodoc&id=" . $val["iddocumento"] . "'><img src='../immagini/delete.png' alt='cancella'></a>";
    }
    else
    {
        if ($nom['idalunno']==0)
            print ("<td><center>CARICA FILE<br/><input type=file name='file_" . $nom['idclasse'] . "_" . $nom['idmateria'] . "' value='Carica'> <td></td><td></td>");
        else
            print ("<td><td></td><td></td>");

    }
    print "</td></tr>";


}

print "<tr><td></td><td><center><br><input type='submit' value='Invia file selezionati'></center></td><td></td><td></td></tr>";
print "</table>";

print "</form>";

mysqli_close($con);
stampa_piede(""); 

