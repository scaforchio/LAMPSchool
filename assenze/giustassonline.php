<?php

require_once '../lib/req_apertura_sessione.php';
$_SESSION['nogoback']=false;
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

$titolo = "Giustificazione assenze alunni";
$script = "
        <script>
        function verificacb()
        {
	   var tot=document.getElementsByTagName('input');
	   
            for(cont=0;cont<tot.length;cont++){
                if (tot[cont].checked == true)
                   {
                        
                        document.getElementById('subnp').disabled=false;
                        return;
                   }
            }
            
	   document.getElementById('subnp').disabled=true;
        }
        </script>";
       
stampa_head_new($titolo, "", $script, "T");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$codalunno = $_SESSION['idstudente'];
$idclasse = estrai_classe_alunno($codalunno, $con);

$rs1 = eseguiQuery($con, "select * from tbl_alunni where idalunno=$codalunno");
$rs5 = eseguiQuery($con, "select * from tbl_assenze where idalunno=$codalunno and giustifica=0 order by data desc");
$rs6 = eseguiQuery($con, "select * from tbl_ritardi where idalunno=$codalunno and giustifica=0 order by data desc");
$rs7 = eseguiQuery($con, "select * from tbl_usciteanticipate where idalunno=$codalunno and giustifica=0 order by data desc");
$rs9 = eseguiQuery($con, "select * from tbl_asslezione where idalunno=$codalunno  and giustifica=0 "
        . " and data not in (select data from tbl_assenze where idalunno=$codalunno)"
        . " and data in (select datadad from tbl_dad where idclasse=$idclasse)"
        . " order by data ");

$idutente = str_replace("gen", "", $_SESSION['userid']);

print("<div class='container'>");

if ($rs1)
{
    $val1 = mysqli_fetch_array($rs1);
    //print "<center>Alunno: <b>" . $val1["cognome"] . " " . $val1["nome"] . "</b></center><br>";
    print "<form action='giustassonlineok.php' name='giustass'>";
    $assdagiust = false;
    if (mysqli_num_rows($rs5) > 0)
    {
        $assdagiust = true;
        print "<table class='table table-striped table-bordered' align='center'><tr class='prima'><td colspan=2><b>GIUSTIFICAZIONE ASSENZE</b></td></tr>";
        
        while ($rec = mysqli_fetch_array($rs5))
        {print "<tr><td align='left'>";
            $data = $rec["data"];
            $idass = $rec["idassenza"];
            print " " . data_italiana($data) . " " . giorno_settimana($data);
            print "</td><td><input type='checkbox' name='cbass$idass' onchange='verificacb();'>";
            print "<br/>";
            print "</td></tr>";
        }
        print "</table><br>";
    }

    if (mysqli_num_rows($rs6) > 0)
    {
        $assdagiust = true;
        print "<table class='table table-striped table-bordered' align='center'><tr class='prima'><td colspan=2><b>GIUSTIFICAZIONE RITARDI</b></td></tr>";
       
        while ($rec = mysqli_fetch_array($rs6))
        {
            print "<tr><td align='left'>";
            $data = $rec["data"];
            $idass = $rec["idritardo"];
            print " " . data_italiana($data) . " " . giorno_settimana($data);
            print "</td><td><input type='checkbox' name='cbrit$idass' onchange='verificacb();'>";
            print "<br/>";
            print "</td></tr>";
        }
        print "</table><br>";
    }

    if (mysqli_num_rows($rs7) > 0)
    {
        $assdagiust = true;
        print "<table class='table table-striped table-bordered' align='center'><tr class='prima'><td colspan=2><b>GIUSTIFICAZIONE USCITE</b></td></tr>";
        
        while ($rec = mysqli_fetch_array($rs6))
        {
            print "<tr><td align='left'>";
            $data = $rec["data"];
            $idass = $rec["iduscitaanticipata"];
            print " " . data_italiana($data) . " " . giorno_settimana($data);
            print "</td><td><input type='checkbox' name='cbusc$idass' onchange='verificacb();'>";
            print "<br/>";
            print "</td></tr>";
        }
        print "</table><br>";
    }

    if (mysqli_num_rows($rs9) > 0)
    {
        $assdagiust = true;
        print "<table class='table table-striped table-bordered' align='center'><tr class='prima'><td colspan=2><b>GIUSTIFICAZIONE ASSENZE ORARIE</b></td></tr>";
        
        while ($rec = mysqli_fetch_array($rs9))
        {
            print "<tr><td align='left'>";
            $data = $rec["data"];
            $idass = $rec["idassenzalezione"];
            print " " . data_italiana($data) . " " . giorno_settimana($data);
            print " (Ore assenza: " . $rec['oreassenza'];
            print " Materia: " . decodifica_materia(estrai_materia_lezione($rec['idlezione'], $con), $con) . ")";
            print "</td><td><input type='checkbox' name='cbdad$idass' onchange='verificacb();'>";
            print "<br/>";
            print "</td></tr>";
        }
        print "</table><br>";        
    }
    
    if ($assdagiust)
    {
        $contasms = 0;
        $telcel = $val1['telcel'];
        if($_SESSION["protogiustonline"] == "sms"){
            if ($telcel != '')
            {
                if (strpos($telcel, "+") != FALSE)
                {
                    $dest = array();
                    $destinatarialunno = array();
                    $destinatarialunno = explode("+", $telcel);
                    foreach ($destinatarialunno as $destalu)
                    {
                        $dest[] = "39" . trim($destalu); // .$rec['telcel'];

                        $contasms++;
                    }
                } else if (strpos($telcel, ",") != FALSE)
                {
                    $dest = array();
                    $destinatarialunno = array();
                    $destinatarialunno = explode(",", $telcel);
                    foreach ($destinatarialunno as $destalu)
                    {
                        $dest[] = "39" . trim($destalu); // .$rec['telcel'];

                        $contasms++;
                    }
                } else
                {
                    $dest = array();
                    $destinatarialunno = array();
                    $destinatarialunno[] = $telcel;
                    foreach ($destinatarialunno as $destalu)
                    {
                        $dest[] = "39" . trim($destalu); // .$rec['telcel'];

                        $contasms++;
                    }
                }
                print "<center>Cellulare per invio OTP: <select name='telcel'>";

                foreach ($dest as $destinatario)
                {
                    print "<option value='$destinatario'>$destinatario</option>";
                }
                print "</select></center>";
                print "<center><input class='btn btn-sm btn-outline-secondary' type='submit' id='subnp' value='Giustifica' name='subnp' disabled></center>";
            } else {
                alert("Non ci sono numeri di cellulare per invio OTP");
            }
        } else {
            $cod = mysqli_fetch_assoc(eseguiQuery($con, "SELECT totpgiustass FROM tbl_alunni WHERE idtutore = $idutente"))['totpgiustass'];
            if($cod != null && $cod != ""){
                print("<input type='hidden' name='totp' value='true'>");
                print"<center><input class='btn btn-sm btn-outline-secondary' type='submit' id='subnp' value='Giustifica TOTP' name='subnp' disabled></center>";
            }
        } 
    } else {
        alert("Non ci sono assenze da giustificare!");
    }
}

print("</div>");

stampa_piede_new();



