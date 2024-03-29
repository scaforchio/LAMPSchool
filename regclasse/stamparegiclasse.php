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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
require_once '../lib/funregi.php';

// istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$idclasse = stringa_html('idclasse');

$titolo = "Stampa registro di classe";
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
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));










print ('
         <form method="post" action="stamparegiclasse.php" name="voti">
   
         <p align="center">
         <table align="center">');



//
//   Classi
//
print('
        <tr>
        <td width="50%"><b>Classe</b></td>
        <td width="50%"> 
        <SELECT ID="idclasse" NAME="idclasse" ONCHANGE="voti.submit()"><option value="">&nbsp;</option>');

//  Riempimento combobox delle tbl_classi/materie
//
//  Riempimento combobox delle classi
//
// $query="select idclasse, anno, sezione, specializzazione from tbl_classi order by anno, sezione, specializzazione";
$query = "select idclasse, anno, sezione, specializzazione from tbl_classi 
       order by anno, sezione, specializzazione";


$ris = eseguiQuery($con, $query);
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
//  if ($cattedra==$nom["idcattedra"])
    if ($idclasse == $nom["idclasse"])
        print " selected";
    print ">";
    print ($nom["anno"]);
    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
    print "</option>";
}
print('      </SELECT>
      </td></tr>');

echo('</table>');

print "<center><br><a href=javascript:Popup('stamparegiclassesta.php?idclasse=$idclasse')><img src='../immagini/stampa.png'></a></center>";

//print "<center><input type='submit' value='Stampa registro'></center>";
//       <table align="center">
//       <td>');
//     //     <p align="center"><input type="submit" value="Visualizza voti" name="b"></p>
//echo('</td></table>');
echo('</form><hr>');



mysqli_close($con);
stampa_piede("");

