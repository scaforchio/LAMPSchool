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


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");


// istruzioni per tornare alla pagina di login se non c'� una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idutente = $_SESSION["idutente"];
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
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
/*
  $rissms=array();
  $rissms=verifica_numero_sms_residui($_SESSION['utentesms'],$_SESSION['passsms']);
  $smsresidui=$rissms['classic_sms'];
  $smsresidui=floor($smsresidui*($_SESSION['costosmsclassic']/$_SESSION['costosmsplus']));
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
$ris = eseguiQuery($con, $query);
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
    $res = eseguiQuery($con, $query);

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
           Ora autorizzazione uscita: <input type='text' name='orauscita' maxlength='5' size=5>";
    // INIZIO MODIFICHE
    print " su ";

    print "<select name='tiporichiesta'><option>richiesta personale</option><option>richiesta telefonica</option><option>richiesta scritta</option><option>autorizzazione</option></select>";

    print " ";

    print "<select name='tiporichiedente'>"
            . "<option>del padre</option>"
            . "<option>della madre</option>"
            . "<option>del nonno/nonna</option>"
            . "<option>dell'alunno/a maggiorenne</option>"
            . "<option>del fratello o sorella maggiorenne</option>"
            . "<option>di un delegato dai genitori</option>"
            . "<option>del dirigente o suo delegato</option>"
            . "</select>";
    print "<fieldset value='Dati privati'><legend>Dati privati</legend>";
    print "Cognome e nome richiedente <input type='text' name='richiedente'> , telefono contattato <input type='text' name='recapito'>";


    // FINE MODIFICHE
    print "<br>per <input type='text' name='motivo' maxlength='200' size='80'><br><br>";
    print "</fieldset>";
    // if ($_SESSION['gesttimbrature'] == 'no')
    print "Uscita contestuale ad autorizzazione: <input type='checkbox' name='uscitacont'><br><br>";


    print "<br><br><center><input type='button' value='Seleziona tutti' onclick='checkTutti()'>
           <input type='button' value='Deseleziona tutti' onclick='uncheckTutti()'></center>
           <br><br><center><input type='submit' value='Registra autorizz.'></center><br><br>
           <p align='center'>

           <table align='center' border='1'>
           <tr class='prima'><td>Cognome Nome</td><td>Autorizzazione</td><td>Prec. uscite</td></tr>";

    $query = "select idalunno,cognome, nome, datanascita,firmapropria,autuscita
            from tbl_alunni
            where tbl_alunni.idclasse=$idclasse

            order by cognome, nome, datanascita";

    $ris = eseguiQuery($con, $query);


    while ($rec = mysqli_fetch_array($ris))
    {
        $alunnoassente = false;
        $idalu = $rec['idalunno'];
        $query_ca = "SELECT COUNT(*) FROM `tbl_assenze` WHERE `idalunno` = $idalu AND `data` = CURDATE();";
        $ris_ca = eseguiQuery($con, $query_ca);
        $conteggioassenze = mysqli_fetch_array($ris_ca, MYSQLI_NUM);
        if($conteggioassenze[0] > 0){
            $alunnoassente = true;
        }

        print "<tr>";
        print "     <td>" . $rec['cognome'] . " " . $rec['nome'] . " " . data_italiana($rec['datanascita']);
        if ($rec['firmapropria'])
        {

            print "<small> (Autorizz. a firma propria)</small>";
        }
        if ($rec['autuscita']!="")
        {
            print "<small><br>".$rec['autuscita']."</small>";
        }
        print "</td>";

        if($alunnoassente){
            print "<td align='center'><b>Alunno Assente</b></td>";
        }else{
            print "<td align='center'><input type='checkbox' name='pres" . $rec['idalunno'] . "'></td>";
        }
        
        print "<td align='center'>";

        $seledata = " data <= '" . $_SESSION['fineprimo'] . "' ";
        $queryusc = "select count(*) as numusc from tbl_usciteanticipate where idalunno = '" . $rec["idalunno"] . "' and" . $seledata;
        //print inspref($queryusc);
        $risusc = eseguiQuery($con, $queryusc);

        while ($ass = mysqli_fetch_array($risusc))
        {
            $numuscprimo = $ass['numusc'];
        }
        print "1°=<b>" . $numuscprimo . "</b>";

        $seledata = " data > '" . $_SESSION['fineprimo'] . "' ";
        $queryusc = "select count(*) as numusc from tbl_usciteanticipate where idalunno = '" . $rec["idalunno"] . "' and" . $seledata;
        //print inspref($queryusc);
        $risusc = eseguiQuery($con, $queryusc);
        while ($ass = mysqli_fetch_array($risusc))
        {
            $numuscprimo = $ass['numusc'];
        }
        print " - 2°=<b>" . $numuscprimo . "</b>";


        print "</td>";
        print "</tr>";
    }
    print "</table><br><center><input type='submit' value='Registra autorizz.'></center></p></form>";
}
mysqli_close($con);
stampa_piede("");


