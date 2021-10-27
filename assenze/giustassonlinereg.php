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

//
//    VISUALIZZAZIONE DELLA SITUAZIONE DELLE ASSENZE E DEI RITARDI
//    PER I GENITORI 
//


require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
//@require_once("../lib/sms/php-send.php");
// require_once '../lib/db / query.php';
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
// $lQuery = LQuery::getIstanza();
//  istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Convalida giustificazione assenze alunni";
$script = "<script>
             
   </script>"
;
stampa_head($titolo, "", $script, "T");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
$_SESSION['nogoback']=true;
$token = stringa_html('token');
$elencoass = stringa_html('elencoass');
$elencousc = stringa_html('elencousc');
$elencorit = stringa_html('elencorit');
$elencodad = stringa_html('elencodad');

//print "token $token ass $elencoass usc $elencousc rit $elencorit dad $elencodad";
$verificatoken = verificaToken($con, $_SESSION['idutente'], 'giust', $token);
//print "Verifica token $verificatoken";
if ($verificatoken == 1)
{
    $arrass = array();
    $arrusc = array();
    $arrrit = array();
    $arrdad = array();

    if (strlen($elencoass) > 0)
        $arrass = explode(",", $elencoass);
    if (strlen($elencousc) > 0)
        $arrusc = explode(",", $elencousc);
    if (strlen($elencorit) > 0)
        $arrrit = explode(",", $elencorit);
    if (strlen($elencodad) > 0)
        $arrdad = explode(",", $elencodad);

    $data = date('Y-m-d');

    foreach ($arrass as $id)
    {
        $query = "update tbl_assenze set giustifica=1,datagiustifica='$data',iddocentegiust=0 where idassenza=$id";

        eseguiQuery($con, $query);
    }

    foreach ($arrusc as $id)
    {
        $query = "update tbl_usciteanticipate set giustifica=1,datagiustifica='$data',iddocentegiust=0 where iduscita=$id";
        eseguiQuery($con, $query);
    }

    foreach ($arrrit as $id)
    {
        $query = "update tbl_ritardi set giustifica=1,datagiustifica='$data',iddocentegiust=0 where idritardo=$id";
        eseguiQuery($con, $query);
    }

    foreach ($arrdad as $id)
    {
        $query = "update tbl_asslezione set giustifica=1,datagiustifica='$data',iddocentegiust=0 where idassenzalezione=$id";
        eseguiQuery($con, $query);
    }
    print "
        <form method='post' id='formass' action='giustassonline.php?suffisso='".$_SESSION['suffisso'].">
        
        </form>
        <SCRIPT language='JavaScript'>
           document.getElementById('formass').submit();
        </SCRIPT>";
}
else
{
    print "<br><br><font color='red'> <b><center>Errore password di convalida ".$verificatoken."</center></b>";
    print "<br><br><center><a href='../login/ele_ges.php?suffisso=".$_SESSION['suffisso']."'>Esci</a><center>";
}
stampa_piede();
