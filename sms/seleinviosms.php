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
@require_once("../lib/sms/php-send.php");
// @require_once("php-send.php");


// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Invio SMS";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$selepep = '';
$selepet = '';
$seletaa = '';
$seletar = '';
$seletat = '';
$tipoass = stringa_html('tipoass');
switch ($tipoass)
{
    case 'A':
        $seletaa = 'selected';
        break;
    case 'R':
        $seletar = 'selected';
        break;
    case 'T':
        $seletat = 'selected';
        break;
}

$periodo = stringa_html('periodo');
switch ($periodo)
{
    case 'T':
        $selepet = 'selected';
        break;
    case 'P':
        $selepep = 'selected';
        break;
}

if ($selepet == '' & $selepep == '')
{
    $selepep = 'selected';
    $tipoass = 'A';
}
if ($seletat == '' & $seletar == '' & $seletaa == '')
{
    $seletaa = 'selected';
    $periodo = 'P';
}


$rissms = verifica_numero_sms_residui($utentesms, $passsms);

$smsresidui = $rissms['classic_sms'];
$smsresidui = floor($smsresidui * ($costosmsclassic / $costosmsplus));

if ($smsresidui > 1000)
{
    $color = 'green';
}
else
{
    if ($smsresidui > 500)
    {
        $color = 'orange';
    }
    else
    {
        $color = 'red';
    }
}
print "<center><b><font color='$color' size='4'>SMS residui: $smsresidui</font></center></b>";
/* foreach ($rissms as $rsms)
       print "<center>".$rsms."<br></center>"; */


print "<br><b><center>Selezione SMS da inviare</center></b><br>";
print "<form action='seleinviosms.php' method='post' name='selesms'>";

print "<table align='center'>";

print "<tr><td>Tipo evento</td>
       <td><select name='tipoass' ONCHANGE='selesms.submit();'>";
print "<option value='T' $seletat>Tutti</option>";
print "<option value='A' $seletaa>Assenze</option>";
if ($numeroritardisms>0)
    print "<option value='R' $seletar>Super. Num. Ritardi</option>";
else
    print "<option value='R' $seletar>Ritardi</option>";
print "</select> </td></tr>";

if ($tipoass != 'R')
{
    print "<tr><td>Periodicità assenza</td>
       <td><select name='periodo' ONCHANGE='selesms.submit();'>";
    print "<option value='T' $selepet>Tutte</option>";
    print "<option value='P' $selepep>Solo primo giorno</option>";
    print "</select> </td></tr>";
}
print "</table>";


print "</form><br>";


$iddest = array();
$dataoggi = date("Y-m-d");

print "<form action='sendsms.php' method='POST'><center>";
if ($smsresidui > 0) print "<input type='submit' value='Invia SMS'><br><br></center>";
print "<table align='center' border='1'>";
print "<tr class='prima'><td>Tipo</td><td>Alunno</td><td>Classe</td><td>Invio</td></tr>";
if ($tipoass == 'A' | $tipoass == 'T')
{
    $query = "select * from tbl_assenze,tbl_alunni,tbl_classi
        where tbl_assenze.idalunno=tbl_alunni.idalunno
        and tbl_alunni.idclasse=tbl_classi.idclasse
        and data='$dataoggi'
        and tbl_assenze.idalunno
        order by anno,sezione,specializzazione,cognome, nome";

    $ris = mysqli_query($con, inspref($query));
    while ($rec = mysqli_fetch_array($ris))
    {
        $idalunno = $rec['idalunno'];
        $idclasse = $rec['idclasse'];
        if ($periodo == 'P')
        {
            $asspre = verifica_ass_pre($idalunno, $dataoggi, $con);
        }
        else
        {
            $asspre = false;
        }

        if (!$asspre)
        {

            // CONTROLLO CHE NON SIA GIA' STATO INVIATO UN SMS

            $sql = "select * from tbl_sms,tbl_testisms
                    where tbl_sms.idtestosms=tbl_testisms.idtestosms
                    and iddestinatario=$idalunno
                    and substr(tbl_testisms.testo,1,7)='\${nome}'
                    and substr(tbl_testisms.dataora,1,10)='$dataoggi'";
            $rissms= mysqli_query($con,inspref($sql));
            $giainviato=false;
            if (mysqli_num_rows($rissms)>0)
                $giainviato=true;
            print "<tr class='oddeven'>";
            print "<td>ASS</td>";
            print "<td>" . estrai_alunno_data($idalunno, $con) . "</td><td>" . decodifica_classe($idclasse, $con) . "</td>";
            $telcel = VerificaCellulare($rec['telcel']);
            if ($telcel != "")
            {
                if (!$giainviato)
                    print "<td align='center'><input type='checkbox' name='ass$idalunno' checked></td>";
                else
                    print "<td align='center'>Inviato!</td>";

            }
            else
            {
                print "<td align='center'>Inserire num. cell.</td>";
            }

            print "</tr>";
        }
    }
}
if ($tipoass == 'R' | $tipoass == 'T')
{
    if ($numeroritardisms=='0')
    {
        // INVIA I MESSAGGI DI RITARDO
        $query = "select * from tbl_ritardi,tbl_alunni,tbl_classi
                    where
                    tbl_ritardi.idalunno=tbl_alunni.idalunno
                    and tbl_alunni.idclasse=tbl_classi.idclasse
                    and data='$dataoggi'
                    order by anno,sezione,specializzazione,cognome, nome";
        $ris = mysqli_query($con, inspref($query));
        while ($rec = mysqli_fetch_array($ris))
        {
            // CONTROLLO CHE NON SIA GIA' STATO INVIATO UN SMS
            $idalunno = $rec['idalunno'];
            $idclasse = $rec['idclasse'];
            $sql = "select * from tbl_sms,tbl_testisms
                    where tbl_sms.idtestosms=tbl_testisms.idtestosms
                    and iddestinatario=$idalunno
                    and substr(tbl_testisms.testo,1,7)='\${nome}'
                    and substr(tbl_testisms.dataora,1,10)='$dataoggi'";
            $rissms = mysqli_query($con, inspref($sql));
            $giainviato = false;
            if (mysqli_num_rows($rissms) > 0)
            {
                $giainviato = true;
            }
            $idalunno = $rec['idalunno'];
            $idclasse = $rec['idclasse'];

            // CONTO I RITARDI
            $query = "SELECT count(*) AS numritardi FROM tbl_ritardi WHERE idalunno=" . $rec['idalunno'];

            $risnumrit = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
            $recnumrit = mysqli_fetch_array($risnumrit);
            $numritardi = $recnumrit['numritardi'];


            print "<tr class='oddeven'>";
            print "<td>RIT <b>($numritardi)</b></td>";
            print "<td>" . estrai_alunno_data($idalunno, $con) . "</td><td>" . decodifica_classe($idclasse, $con) . "</td>";
            $telcel = VerificaCellulare($rec['telcel']);
            if ($telcel != "")
            {
                if (!$giainviato)
                {
                    print "<td align='center'><input type='checkbox' name='rit$idalunno' checked></td>";
                }
                else
                {
                    print "<td align='center'>Inviato!</td>";
                }

            }

            else
            {
                print "<td align='center'>Ins. cell.</td>";
            }
            print "</tr>";
        }
    }
    else
    {
        // INVIA I MESSAGGI DI SUPERAMENTO DEL LIMITE DEI RITARDI

        $query = "select * from tbl_ritardi,tbl_alunni,tbl_classi
                    where
                    tbl_ritardi.idalunno=tbl_alunni.idalunno
                    and tbl_alunni.idclasse=tbl_classi.idclasse
                    and data='$dataoggi'
                    order by anno,sezione,specializzazione,cognome, nome";
        $ris = mysqli_query($con, inspref($query));
        while ($rec = mysqli_fetch_array($ris))
        {
            // CONTROLLO CHE NON SIA GIA' STATO INVIATO UN SMS
            $idalunno = $rec['idalunno'];
            $idclasse = $rec['idclasse'];
            $sql = "select * from tbl_sms,tbl_testisms
                    where tbl_sms.idtestosms=tbl_testisms.idtestosms
                    and iddestinatario=$idalunno
                    and substr(tbl_testisms.testo,1,13)='\${nome} ha su'";
            $rissms = mysqli_query($con, inspref($sql)) or die ("Errore: " .inspref($sql,false) );
            $giainviato = false;
            if (mysqli_num_rows($rissms) > 0)
            {
                $recinv=mysqli_fetch_array($rissms);
                $datainvio=$recinv['dataora'];
                $datainvio=substr($datainvio,0,10);
                $datainvio=data_italiana($datainvio);
                $giainviato = true;

            }
            $idalunno = $rec['idalunno'];
            $idclasse = $rec['idclasse'];

            // CONTO I RITARDI PER QUADRIMESTRE
            $query = "SELECT count(*) AS numritardi FROM tbl_ritardi
                      WHERE data<='$fineprimo'
                      AND idalunno=" . $rec['idalunno'];

            $risnumrit = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
            $recnumrit = mysqli_fetch_array($risnumrit);
            $numritardiprimo = $recnumrit['numritardi'];

            $query = "SELECT count(*) AS numritardi FROM tbl_ritardi
                      WHERE data>'$fineprimo'
                      AND idalunno=" . $rec['idalunno'];

            $risnumrit = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
            $recnumrit = mysqli_fetch_array($risnumrit);
            $numritardisec = $recnumrit['numritardi'];

            if ($dataoggi>$fineprimo)
                $ritconfr=$numritardisec;
            else
                $ritconfr=$numritardiprimo;

            if ($ritconfr>$numeroritardisms)
            {
                print "<tr class='oddeven'>";
                print "<td>RIT [I=<b>$numritardiprimo</b>";
                if (date('Y-m-d') > $fineprimo)
                {
                    print " - II=<b>$numritardisec</b>";
                }
                print "]</td>";
                print "<td>" . estrai_alunno_data($idalunno, $con) . "</td><td>" . decodifica_classe($idclasse, $con) . "</td>";
                $telcel = VerificaCellulare($rec['telcel']);
                if ($telcel != "")
                {
                    if (!$giainviato)
                    {
                        print "<td align='center'><input type='checkbox' name='numrit$idalunno'></td>";
                    }
                    else
                    {
                        print "<td align='center'>Inviato in data $datainvio !</td>";
                    }

                }

                else
                {
                    print "<td align='center'>Ins. cell.</td>";
                }
                print "</tr>";
            }
        }

    }
}
print "</table>";
if ($smsresidui > 0) print "<br><center><input type='submit' value='Invia SMS'></center></form>";


stampa_piede("");
mysqli_close($con);


         


