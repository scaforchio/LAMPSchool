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
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$note = stringa_html('note');
$autorizza = stringa_html('autorizza');
$idassemblea = stringa_html('idassemblea');
$iddocente = stringa_html('iddocente');
$idclasse = stringa_html('idclasse');
$titolo = "Variazione autorizzazione";
$script = "";
stampa_head($titolo, "", $script, "SP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='assstaff.php?iddocente=$iddocente'>A</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore: " . mysqli_error($con));
$query = "UPDATE tbl_assemblee
          SET autorizzato=$autorizza, docenteautorizzante=$iddocente, note='$note'
          WHERE idassemblea=$idassemblea";
$ris = mysqli_query($con, inspref($query)) or die("Errore : " . inspref($query));

if ($autorizza == 1)
{
    $a = "SELECT * FROM tbl_assemblee WHERE idassemblea=$idassemblea";
    $ris2 = mysqli_query($con, inspref($a)) or die("Errore : " . inspref($a));
    $data = mysqli_fetch_array($ris2);


    if ($data['orainizio']==$data['orafine'])
        $mess .= "Vista la regolare richiesta si autorizza assemblea di classe nella " . $data['orainizio'] . "^ ora di lezione.";
    else
       $mess .= "Vista la regolare richiesta si autorizza assemblea di classe dalla " . $data['orainizio'] . "^ ora alla " . $data['orafine'] . "^ ora di lezione.";

    $ann = "INSERT INTO tbl_annotazioni(idclasse,iddocente,data,testo) 
		VALUES ($idclasse,$iddocente,'" . $data['dataassemblea'] . "','$mess')";
    mysqli_query($con,inspref($ann)) or die("Errore : " . inspref($ann));
}

if ($ris)
{
    header("Location: ./assstaff.php?iddocente=$iddocente");
}
else
{
    print "<center><big>Autorizzazione non inserita correttamente!</big></center>";
}
mysqli_close($con);
stampa_piede("");

