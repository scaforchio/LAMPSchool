<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2015 Pietro Tamburrano
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma è distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */
@include '../php-ini' . $_SESSION['suffisso'] . '.php';
@include '../lib/funzioni.php';

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

//TODO: Verificare funzionamento dopo eliminazione del 30/05/2015 della gestione degli errori

$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head("Crea utenze per gli alunni", "", $script, "MASP");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Carica Archivio Alunni da CSV", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
$suffisso = $_SESSION['suffisso'];
$query = "select * from tbl_utenti where idutente>2100000000";
$ris = eseguiQuery($con, $query);
if (mysqli_num_rows($ris) > 0)
    print "<br><br><br><center><b>Alunni già presenti!</center></b>";
else
{
    $query = "insert into tbl_utenti(idutente,userid,password,tipo)
               SELECT tbl_utenti.idutente+2100000000,CONCAT('al','$suffisso',tbl_utenti.idutente),md5(md5(CONCAT('al','$suffisso',tbl_utenti.idutente))),'L' "
            . " FROM tbl_utenti,tbl_alunni WHERE tbl_utenti.idutente=tbl_alunni.idalunno and tbl_utenti.idutente<1000000000";
    //print inspref($query);
    eseguiQuery($con, $query);
    

    print "<br><br><br><center><b>Utenze per gli alunni create correttamente!</center></b>";
}

mysqli_close($con);
stampa_piede("");


