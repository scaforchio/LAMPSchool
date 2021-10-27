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

// istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}



$titolo = "Inserimento e modifica obiettivi";
$script = "<script src='../lib/js/popupjquery.js'></script>
           <script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri)
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";

stampa_head($titolo, "", $script, "SDP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a>$goback[1] - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$idclasse = stringa_html('idclasse');

print ('
   <form method="post" action="scrutobiettiviintermedio.php" name="obiettivi">
         
         <input type="hidden" name="idclasse" value="' . $nome . '">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="idclasse" NAME="idclasse" ONCHANGE="obiettivi.submit()">');
     print "<option value=''>&nbsp;</option>";
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
$query = "select * from tbl_classi order by anno, specializzazione, sezione";
$ris = eseguiQuery($con, $query);
while ($nom = mysqli_fetch_array($ris))
{
    
    print ($nom["idclasse"]);
    print "'";

    if ($idclasse == $nom["idclasse"])
    {
        print " selected";
    }

    print ">{$nom['anno']}&nbsp;{$nom['sezione']}&nbsp;{$nom['specializzazione']}</option>";
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";

    if ($idclasse == $nom["idclasse"])
    {
        print " selected";
    }

    print ">{$nom['anno']}&nbsp;{$nom['sezione']}&nbsp;{$nom['specializzazione']}</option>";
}

echo('
      </SELECT>
      </td></tr>
');


//
//  Fine visualizzazione della data
//


echo("
      
    </table>
    </form><hr/>
");

if (($idclasse != ""))
{


    $query = "SELECT idalunno FROM tbl_alunni WHERE idclasse=$idclasse ORDER BY cognome, nome, datanascita";

    $ris = eseguiQuery($con, $query);


    echo '<p align="center">
          <font size=4 color="black">Scrutini per obiettivi ' . $classe . "</font>";

    print "<table border=1 align=center>
          <tr class=prima>
          <td><b> N. </b></td>
          <td><b> Alunno </b></td>
          
          <td><b> Scrutinio </b></td></tr>";

    $cont = 0;
    while ($val = mysqli_fetch_array($ris))
    {
        $cont++;
        $idalunno=$val['idalunno'];
        $query = "select * from tbl_valutazioniobiettivi where idalunno=$idalunno and periodo='2'";
        $risvalins = eseguiQuery($con, $query);
        if (mysqli_num_rows($risvalins) == 0)
            $azione = "INSERISCI";
        else
            $azione = "MODIFICA";

        print "
          <tr>
          <td><b> $cont </b></td>
    <td><b> " . estrai_dati_alunno($idalunno, $con) . " </b></td>
          
          
          
          <td><b> <a href='CRUDobiettiviscrutini.php?idalunno=" . $val['idalunno'] . "'>$azione</a> </b></td></tr>";
    }
    print "</table>";
// fine if
}
mysqli_close($con);
stampa_piede("");

