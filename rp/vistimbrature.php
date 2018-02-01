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


$idclasse = stringa_html('idclasse');
$idalunno = stringa_html('idalunno');
if (stringa_html('idalunnotext') != '')
    $idalunno = stringa_html('idalunnotext');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();


$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


//
//    Parte iniziale della pagina
//

$titolo = "Visualizzazione timbrature alunno";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";

stampa_head($titolo, "", $script, "SDMAP");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


print ("
         <form method='post' action='vistimbrature.php' name='timbrature'>
   
         <p align='center'>
         <table align='center'>");



/*
  //
  //   Classi
  //

  print("
  <tr>
  <td width='50%'><b>Classe</b></p></td>
  <td width='50%'>
  <SELECT ID='cl' NAME='idclasse' ONCHANGE='timbrature.submit()'><option value=''></option>  ");

  //
  //  Riempimento combobox delle tbl_classi
  //
  $query = "SELECT DISTINCT tbl_classi.idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY anno,sezione,specializzazione";
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
  print ($nom["anno"]);
  print "&nbsp;";
  print($nom["sezione"]);
  print "&nbsp;";
  print($nom["specializzazione"]);
  }


  print ("</select></td></tr>");


  //
  //   Alunni
  //

  if ($idclasse != "")
  {
 * 
 * 
 */


// Elenco alunni
print "<tr><td><b>Seleziona alunno&nbsp;&nbsp;&nbsp;</b></td><td>";
$query = "select idalunno,cognome,nome,datanascita,tbl_alunni.idclasse from tbl_alunni,tbl_classi "
        . "where tbl_alunni.idclasse=tbl_classi.idclasse "
        . "and tbl_alunni.idclasse<>0 "
        . "order by cognome, nome, datanascita";
$ris = mysqli_query($con, inspref($query));
echo("<select name='idalunno' ONCHANGE='timbrature.submit()'><option value=''>&nbsp");
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idalunno"]);
    print "'";
    if ($idalunno == $nom["idalunno"])
        print " selected";
    print ">";
    print ($nom["cognome"]);
    print "&nbsp;";
    print($nom["nome"]);
    print "&nbsp;&nbsp;";
    print(data_italiana($nom["datanascita"]));
    print "&nbsp;&nbsp;";
    print(decodifica_classe($nom["idclasse"], $con));
    print "&nbsp;&nbsp;";
    print("($idalunno)");
}
echo "</select></td></tr>";
//}
// Elenco alunni
print "<tr><td><b> Cerca alunno&nbsp;&nbsp;&nbsp;</b></td><td>";

print("<input type='text' name='idalunnotext'><input type='submit' value='Cerca'>");
print "</td></tr>";

print("</table></form>");


if ($idalunno != "")
{
    $query = "select * from tbl_timbrature
	        where idalunno=$idalunno
	        order by datatimbratura desc, oratimbratura desc";

    $ris = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query));


    if (mysqli_num_rows($ris) > 0)
    {

        print "<br><table align='center' border='1'><tr class='prima'><td>Data</td><td>Ora</td><td>Tipo</td><td>Ora ricezione</td><td>Forz.</td></tr>";
        while ($rec = mysqli_fetch_array($ris))
        {
            $forz = $rec['forzata'] ? '*' : '';
            print "<tr><td>" . data_italiana($rec['datatimbratura']) . "</td><td>" . substr($rec['oratimbratura'], 0, 5) . "</td><td align='center'>" . $rec['tipotimbratura'] . "</td><td>" . $rec['ultimamodifica'] . "</td><td align='center'>" . $forz . "</td></tr>";
        }
        print "</table>";
    }
    else
    {

        print("<center><b><br>Nessuna timbratura presente!</b></center>");
    }
}

mysqli_close($con);
stampa_piede("");

