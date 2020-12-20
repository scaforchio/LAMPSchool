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

//
//    VISUALIZZAZIONE DELLA SITUAZIONE DELLE ASSENZE E DEI RITARDI
//    PER I GENITORI 
//


require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
//@require_once("../lib/sms/php-send.php");
// require_once '../lib/db / query.php';
verificaGoBack();
$_SESSION['nogoback']=true;
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
// $lQuery = LQuery::getIstanza();
//  istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Convalida giustificazione assenze alunni";
$script = "<script>
             function verifica()
             {
                
                var tk=document.getElementById('token').value;
               
                   if (tk.length==6)
                   {
                       document.getElementById('subnp').disabled=false;
                   }
                   else
                   {
                       document.getElementById('subnp').disabled=true;
                   }
                
             }
   </script>"
;
stampa_head($titolo, "", $script, "T");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$codalunno = $_SESSION['idstudente'];
$idclasse = estrai_classe_alunno($codalunno, $con);

$telcel = stringa_html('telcel');

$elencoass = "";
$elencorit = "";
$elencousc = "";
$elencodad = "";

if ($telcel != "")
{

    // if (!inviaSMS($telcel, $otp, $codalunno, $con))
    // if (false) 
    //print $telcel;
    //if (!inserisciToken($con, 6, $_SESSION['idutente'], 'giust', 'sms', $telcel))
    
    //if (!inserisciToken($con, 6, $_SESSION['idutente'], 'giust', 'telegram', '19******2',1))
    if (!inserisciToken($con, 6, $_SESSION['idutente'], 'giust', 'sms', $telcel,1))        
        print "Impossibile inviare OTP";
    else
    {


        $rs1 = eseguiQuery($con, "select * from tbl_alunni where idalunno=$codalunno");
        $rs5 = eseguiQuery($con, "select * from tbl_assenze where idalunno=$codalunno and not giustifica order by data desc");
        $rs6 = eseguiQuery($con, "select * from tbl_ritardi where idalunno=$codalunno and not giustifica order by data desc");
        $rs7 = eseguiQuery($con, "select * from tbl_usciteanticipate where idalunno=$codalunno and not giustifica order by data desc");
        $rs9 = eseguiQuery($con, "select * from tbl_asslezione where idalunno=$codalunno  and not giustifica "
                . " and data not in (select data from tbl_assenze where idalunno=$codalunno)"
                . " and data in (select datadad from tbl_dad where idclasse=$idclasse)"
                . " order by data ");


        if (mysqli_num_rows($rs5) > 0)
        {
            while ($val = mysqli_fetch_array($rs5))
            {
                $idass = $val['idassenza'];
                $idgiu = stringa_html("cbass$idass") ? 'on' : 'off';
                if ($idgiu == "on")
                {
                    $elencoass .= $idass . ",";
                }
            }
            if (strlen($elencoass) > 0)
                $elencoass = substr($elencoass, 0, strlen($elencoass) - 1);
            //     print "<br>$elencoass";
        }
        if (mysqli_num_rows($rs6) > 0)
        {
            while ($val = mysqli_fetch_array($rs6))
            {
                $idass = $val['idritardo'];
                $idgiu = stringa_html("cbrit$idass") ? 'on' : 'off';
                if ($idgiu == "on")
                {
                    $elencorit .= $idass . ",";
                }
            }
            if (strlen($elencorit) > 0)
                $elencorit = substr($elencorit, 0, strlen($elencorit) - 1);
            //      print "<br>$elencorit";
        }
        if (mysqli_num_rows($rs7) > 0)
        {
            while ($val = mysqli_fetch_array($rs7))
            {
                $idass = $val['iduscitaanticipata'];
                $idgiu = stringa_html("cbusc$idass") ? 'on' : 'off';
                if ($idgiu == "on")
                {
                    $elencousc .= $idass . ",";
                }
            }
            if (strlen($elencousc) > 0)
                $elencousc = substr($elencousc, 0, strlen($elencousc) - 1);
            //     print "<br>$elencousc";
        }
        if (mysqli_num_rows($rs9) > 0)
        {
            while ($val = mysqli_fetch_array($rs9))
            {
                $idass = $val['idassenzalezione'];
                $idgiu = stringa_html("cbdad$idass") ? 'on' : 'off';
                if ($idgiu == "on")
                {
                    $elencodad .= $idass . ",";
                }
            }
            if (strlen($elencodad) > 0)
                $elencodad = substr($elencodad, 0, strlen($elencodad) - 1);
        }
        // print "<br>$elencodad";
        print "<center>";
        print "<form action='giustassonlinereg.php' method='post'>";
        print "<input type='hidden' name='elencoass' value='$elencoass'>";
        print "<input type='hidden' name='elencorit' value='$elencorit'>";
        print "<input type='hidden' name='elencousc' value='$elencousc'>";
        print "<input type='hidden' name='elencodad' value='$elencodad'>";
        print "<br>Codice di verifica (6 cifre, inviata tramite SMS) <input type='text' size='6' maxlength='6' name='token' id='token' Onkeyup='verifica();'>";
        print "<br><br><input type='submit' id='subnp' value='Conferma giustifica' disabled>";
        print "</center>";
    }
}


stampa_piede();
