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


if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$idcircolare = stringa_html('idcircolare');
$titolo = "Controllo liste distribuzione circolari";

$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head($titolo, "", $script,"MSAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$visualizzabili = array("image/jpeg", "application/pdf", "image/pjpeg", "image/gif", "image/png");


//
//  SELEZIONE CIRCOLARE
//
$ricevuta = 0;
$dest = '';

print ("
   <form method='post' action='listadistr.php' name='listadistr'>
   
   <p align='center'>
   <table align='center'>
   <tr>
      <td width='50%'><center><b>Circolare</b></td>
      <td width='50%'>
      <SELECT NAME='idcircolare' ONCHANGE='listadistr.submit()'>
      <option value=''>&nbsp;</option>");
$query = "SELECT * FROM tbl_circolari ORDER BY datainserimento DESC";
$ris = mysqli_query($con, inspref($query));
while ($rec = mysqli_fetch_array($ris))
{

    print "<option value='" . $rec['idcircolare'] . "'";
    if ($idcircolare == $rec['idcircolare'])
    {
        print " selected";
        $ricevuta = $rec['ricevuta'];
        $dest = trim($rec['destinatari']);
    }
    print ">" . $rec['datainserimento'] . " - " . $rec['descrizione'] . " - " . decod_dest($rec['destinatari']) . "</option>";
}
print "   
	   </SELECT>
      </td></tr>";
print "</table></form>";

if ($idcircolare != "")
{
    print "<br><table border=1 align='center'>";
    print "<tr class='prima'><td>Destinatario</td><td>Data lettura</td>";
    if ($ricevuta)
    {
        print "<td>Data conferma lettura</td>";
    }
    print "</tr>";


    if ($dest == 'D' | $dest == 'SD')
    {
        $query = "select * from tbl_diffusionecircolari,tbl_docenti
               where tbl_diffusionecircolari.idutente=tbl_docenti.iddocente
               and idcircolare=$idcircolare
               order by cognome,nome";
    }
    if ($dest == 'A' | $dest == 'SA')
    {
        $query = "select * from tbl_diffusionecircolari,tbl_alunni,tbl_classi
               where tbl_diffusionecircolari.idutente=tbl_alunni.idalunno
               and idcircolare=$idcircolare
               and tbl_alunni.idclasse=tbl_classi.idclasse
               order by anno,sezione,specializzazione,cognome,nome";
    }
    if ($dest == 'I' | $dest == 'SI')
    {
        $query = "select * from tbl_diffusionecircolari,tbl_amministrativi
               where tbl_diffusionecircolari.idutente=tbl_amministrativi.idamministrativo
               and idcircolare=$idcircolare
               order by cognome,nome";
    }
    // print "tttt $dest";
    // print inspref($query);
    $ris = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query) . " " . mysqli_error($con));
    while ($rec = mysqli_fetch_array($ris))
    {

        print ("<tr><td>" . $rec['cognome'] . "&nbsp;" . $rec['nome']);
        if ($dest == 'A' | $dest == 'SA')
        {
            print (" - " . decodifica_classe(estrai_classe_alunno($rec['idalunno'], $con), $con) . " - " . data_italiana($rec['datanascita']));
        }
        print "</td>";
        if ($rec['datalettura'] != '0000-00-00')
        {
            print ("<td>" . data_italiana($rec['datalettura']) . "</td>");
        }
        else
        {
            print ("<td>&nbsp;</td>");
        }
        if ($ricevuta)
        {
            if ($rec['dataconfermalettura'] != '0000-00-00')
            {
                print ("<td>" . data_italiana($rec['dataconfermalettura']) . "</td>");
            }
            else
            {
                print ("<td>&nbsp;</td>");
            }
        }
        print "</tr>";
    }
    print "</table>";
}

print"<br/><center><a href=javascript:Popup('listadistrstampa.php?idcircolare=$idcircolare')>Stampa lista di distribuzione</a><br/>";


mysqli_close($con);
stampa_piede("");

function decod_dest($tipodest)
{
    //if ($tipodest=='O')
    //   return "Tutti";
    if ($tipodest == 'D')
    {
        return "Tutti i docenti";
    }
    if ($tipodest == 'A')
    {
        return "Tutti gli alunni";
    }
    if ($tipodest == 'I')
    {
        return "Tutti gli impiegati";
    }
    if ($tipodest == 'SD')
    {
        return "Selezione docenti";
    }
    if ($tipodest == 'SA')
    {
        return "Selezione alunni";
    }
    if ($tipodest == 'SI')
    {
        return "Selezione impiegati";
    }
}

