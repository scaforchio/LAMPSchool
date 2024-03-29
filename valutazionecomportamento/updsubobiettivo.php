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
$iddocente = $_SESSION["idutente"];
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

//
//    Parte iniziale della pagina
//

$titolo = "Modifica sub-obiettivo di comportamento";
$script = "";

stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='./modisubobiettivo.php'>Scelta sub obiettivo</a> - $titolo", "", $_SESSION['nome_scuola'], $comune_scuola);



$idsubob = stringa_html('abil');



$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));



if ($idsubob != "")
{

    print ("
       <form method='post' action='insupdsubobiettivo.php' name='valabil'>
   
      <p align='center'>
      <table align='center'>");

    //
    //   Leggo i dati della voce da modificare
    //

   
      $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

    $query = "select * from tbl_compsubob where idsubob=$idsubob";

    $ris = eseguiQuery($con, $query);




    if ($nom = mysqli_fetch_array($ris))
    {
        $sintesi = $nom["sintsubob"];
        $descri = $nom["subob"];
    }

    print("    
                <tr>
                 <td><b>Sintesi</b></td>

                 <td>
                   <INPUT TYPE='text' VALUE='$sintesi' name='sintesi' maxlength='80' size='80'>
                   <input type='hidden' value='$idsubob' name='idabil'>
                 </td></tr>");

    print("    
                <tr>
                <td><b>Descrizione</b></td>

                <td>
                <textarea name='descrizione' cols='80' rows='10'>$descri</textarea>
          
               </td></tr>");



    print("</table>");

    print "<center><input type='submit' value='Modifica sub-obiettivo di comportamento'></center>";
    print("</form>");
} else
{

    print "<center><font size=4 color=red>Selezionare una voce da modificare!</font>
	       <form action='modisubobiettivo.php' method='post'>

	       <input type='submit' value='OK!'></form></center>";
}

mysqli_close($con);
stampa_piede("");



