<?php
session_start();

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
$tipodoc = stringa_html('tipodoc');
switch ($tipodoc)
{
    case 'pia':
        $titolo = "Inserimento piani lavoro";
        $back = "Gestione piani lavoro";
        $tipodocumento = 1000000001;
        break;
    case 'pro':
        $titolo = "Inserimento programmazioni";
        $back = "Gestione programmi";
        $tipodocumento = 1000000002;
        break;
    case 'rel':
        $titolo = "Inserimento relazioni finali";
        $back = "Gestione relazioni finali";
        $tipodocumento = 1000000003;
        break;
}

$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='../documenti/documprog.php?tipodoc=$tipodoc'>$back</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));
$query = "select idcattedra,tbl_classi.idclasse, anno, sezione, specializzazione, denominazione,tbl_materie.idmateria from tbl_cattnosupp, tbl_classi, tbl_materie where iddocente=$iddocente and tbl_cattnosupp.idclasse=tbl_classi.idclasse and tbl_cattnosupp.idmateria = tbl_materie.idmateria order by anno, sezione, specializzazione, denominazione";

$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    $nomedoc = "file_" . $nom['idclasse'] . "_" . $nom['idmateria'];

    if (isset($_FILES[$nomedoc]))
    {
        inserisci_file($con, $_FILES[$nomedoc], $nom['idclasse'], $nom['idmateria'], $gestionedocumenti,$tipodocumento);
    }
}


stampa_piede();
mysqli_close($con);


function inserisci_file($con, $filedainserire, $idclasse, $idmateria, $gestionedocumenti,$tipodocumento)
{
    // MEMORIZZIAMO NELLA VARIABILE $data IL CONTENUTO DEL FILE
    if ($filedainserire['tmp_name'] != "")
    {
        if (substr($filedainserire['name'], -4, 4) == ".pdf")
        {
            $data = addslashes(fread(fopen($filedainserire['tmp_name'], "rb"), $filedainserire["size"]));
            $md5data = md5($data);
            // SOSTITUISCO GLI SPAZI CON GLI UNDERSCORE PER PROBLEMI CON BROWSER IPAD
            $nome = str_replace(" ", "_", $filedainserire['name']);
            $nome = elimina_apici($nome);
            if ($gestionedocumenti == 'db')
            {

                $queryins = "insert into tbl_documenti(idmateria,idclasse,docbin, docnome, docsize, doctype,docmd5,idtipodocumento)
                              values ('$idmateria','$idclasse','" . $data . "','" . $nome . "','" .
                    $filedainserire['size'] . "','application/pdf','$md5data',$tipodocumento)";
            }
            else
            {
                $queryins = "insert into tbl_documenti(idmateria,idclasse, docnome, docsize, doctype,docmd5,idtipodocumento)
                              values ('$idmateria','$idclasse','" . $nome . "','" .
                    $filedainserire['size'] . "','application/pdf','$md5data',$tipodocumento)";
            }

            $querydel = "delete from tbl_documenti where idmateria=$idmateria and idclasse=$idclasse and idtipodocumento=$tipodocumento";

            $resdel = mysqli_query($con, inspref($querydel)) or die ("Query di cancellazione fallita!");
            $result = mysqli_query($con, inspref($queryins)) or die("Query di inserimento fallita !");
            if ($gestionedocumenti == 'hd')
            {
                crea_file($filedainserire, $md5data);
            }
            // ESITO POSITIVO
            echo "<br><center><font color='green'>Il file " . basename($filedainserire['name']) . " è stato correttamente inserito nel Database.<br>";
        }
        else
        {
            echo "<br><center><font color='red'>Il file " . basename($filedainserire['name']) . " non è un file PDF.</font></center><br>";
        }
    }
}

function crea_file($filedainserire, $hashmd5)

{
    try
    {
        //$cart1=substr($hashmd5,0,2);
        //$cart2=substr($hashmd5,2,2);
        //mkdir("../lampschooldata/$cart1");
        //mkdir("../lampschooldata/$cart1/$cart2");
        if ($_SESSION['suffisso'] != "") $suff = $_SESSION['suffisso'] . "/";
        else $suff = "";
        move_uploaded_file($filedainserire['tmp_name'], "../lampschooldata/$suff$hashmd5");
    } catch (Exception $e)
    {
        die ("Errore nel caricamento del file " . $e->getMessage());
    }

}
  

