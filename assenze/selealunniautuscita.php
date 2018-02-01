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
$idutente = $_SESSION["idutente"];
$idcircolare = stringa_html("idcircolare");
// $idclasse=stringa_html("idclasse");
$destinatari = 'SP';

$idclasse = stringa_html('idclasse');

$motivo = stringa_html('motivo');

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$titolo = "Autorizzazione uscita anticipata";

$script = "<script>
function checkTutti() 
{
   with (document.listadistr) 
   {
      for (var i=0; i < elements.length; i++) 
      {
         if (elements[i].type == 'checkbox')
            elements[i].checked = true;
      }
   }
}
function uncheckTutti() 
{
   with (document.listadistr) 
   {
      for (var i=0; i < elements.length; i++) 
      {
         if (elements[i].type == 'checkbox')
            elements[i].checked = false;
      }
   }
}

$(document).ready(function(){

				 $('input[name^=\"orauscita\"]').datetimepicker({
						formatTime: 'H:i',
						format: 'H:i',
						step: 5,
                        datepicker:false
					});
			 });


</script>
";
stampa_head($titolo, "", $script, "PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
/*
  $rissms=array();
  $rissms=verifica_numero_sms_residui($utentesms,$passsms);
  $smsresidui=$rissms['classic_sms'];
  $smsresidui=floor($smsresidui*($costosmsclassic/$costosmsplus));
  if ($smsresidui>1000)
  $color='green';
  else if ($smsresidui>500)
  $color='orange';
  else
  $color='red';
  print "<center><b><font color='$color' size='4'>SMS residui: $smsresidui</font></center></b>";
 */

print "<br>";
print "<form method='post' action='selealunniautuscita.php' name='selealu'>";

print "<table align='center'>";

// SELEZIONE SU ANNO
print "   <tr>
      <td width='50%'><b>Classe</b></td>
      <td width='50%'>
      <SELECT ID='idclasse' NAME='idclasse' ONCHANGE='selealu.submit()'><option value=''>&nbsp;</option>";

// Riempimento combo box tbl_classi
$query = "SELECT * FROM tbl_classi ORDER BY anno, sezione, specializzazione";
$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
    if ($idclasse == $nom["idclasse"])
    {
        print " selected";
    }
    print ">";
    print ($nom["anno"] . " " . $nom["sezione"] . " " . $nom["specializzazione"]);
}

print("
      </SELECT>
      </td></tr>");




print "</table></form>";


// VISUALIZZAZIONE ELENCO DOCENTI



if ($idclasse != "")
{
    $query = "select testo,cognome,nome
                 from tbl_annotazioni,tbl_docenti
                 where tbl_annotazioni.iddocente=tbl_docenti.iddocente
                 and (testo like '%possono uscire alle%'
                 or testo like '%può uscire alle%')
                 and data = '" . date('Y-m-d') . "'
                 and idclasse=$idclasse";
    $res = mysqli_query($con, inspref($query)) or die(mysqli_error($conn) . inspref($query));

    print "<br><fieldset value='Autorizzazioni già concesse'><legend>Autorizzazioni gi&agrave; concesse</legend><small>";
    while ($rec = mysqli_fetch_array($res))
    {
        print "" . $rec['testo'] . "<br>(<i>" . $rec['cognome'] . " " . $rec['nome'] . "</i>)<br>";
    }
    print "</small></fieldset>";
    $valore = date("h:m");
    print "
           <form method='post' action='insaautorizzuscita.php' name='listadistr'>
           <center>
           <input type='hidden' name='idclasse' value='$idclasse'><br>
           Ora autorizzazione uscita: <input type='text' name='orauscita' maxlength='5' size=5><br><br>
           Motivo: <input type='text' name='motivo' maxlength='200' size='80'><br><br>";
    if ($gesttimbrature == 'no')
        print "Uscita contestuale ad autorizzazione: <input type='checkbox' name='uscitacont'><br><br>";


    print "<br><br><center><input type='button' value='Seleziona tutti' onclick='checkTutti()'>
           <input type='button' value='Deseleziona tutti' onclick='uncheckTutti()'></center>
           <br><br><center><input type='submit' value='Registra autorizz.'></center><br><br>
           <p align='center'>

           <table align='center' border='1'>
           <tr class='prima'><td>Cognome Nome</td><td>Autorizzazione</td><td>Prec. uscite</td></tr>";

    $query = "select idalunno,cognome, nome, datanascita,firmapropria
            from tbl_alunni
            where tbl_alunni.idclasse=$idclasse

            order by cognome, nome, datanascita";
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    while ($rec = mysqli_fetch_array($ris))
    {

        print "<tr>";
        print "     <td>" . $rec['cognome'] . " " . $rec['nome'] . " " . data_italiana($rec['datanascita']);
        if ($rec['firmapropria'])
            print "<small> (Autorizz. a firma propria)</small>";
        print "</td>";


        print "<td align='center'><input type='checkbox' name='pres" . $rec['idalunno'] . "'></td>";

        print "<td align='center'>";

        $seledata = " data <= '" . $fineprimo . "' ";
        $queryusc = "select count(*) as numusc from tbl_usciteanticipate where idalunno = '" . $rec["idalunno"] ."' and". $seledata;
        //print inspref($queryusc);
        $risusc = mysqli_query($con, inspref($queryusc)) or die("Errore nella query: " . mysqli_error($con));
        
        while ($ass = mysqli_fetch_array($risusc))
        {
            $numuscprimo = $ass['numusc'];
        }
        print "1°=<b>".$numuscprimo."</b>";

        $seledata = " data > '" . $fineprimo . "' ";
        $queryusc = "select count(*) as numusc from tbl_usciteanticipate where idalunno = '" . $rec["idalunno"] ."' and". $seledata;
        //print inspref($queryusc);
        $risusc = mysqli_query($con, inspref($queryusc)) or die("Errore nella query: " . mysqli_error($con));
        while ($ass = mysqli_fetch_array($risusc))
        {
            $numuscprimo = $ass['numusc'];
        }
        print " - 2°=<b>".$numuscprimo."</b>";

        
        print "</td>";
        print "</tr>";
    }
    print "</table><br><center><input type='submit' value='Registra autorizz.'></center></p></form>";
}
mysqli_close($con);
stampa_piede("");


