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


/*
     INSERIMENTO DELLE CATTEDRE
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

$docente = stringa_html('docente');

//
//    Parte iniziale della pagina
//

$titolo = "Gestione cattedra docente";
$script = "";
stampa_head($titolo, "", $script,"MPAS");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

//
//    Fine parte iniziale della pagina
//

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

print ("
   <form method='post' action='cat.php' name='cat'>
   
   <p align='center'>
   <table align='center' border='1'>

      <tr class='prima'>
      <td colspan='2' align='center'><b>Docente</b>");

//  $sqld= "SELECT * FROM tbl_docenti WHERE NOT sostegno ORDER BY cognome, nome";
$sqld = "SELECT * FROM tbl_docenti ORDER BY cognome, nome";
$resd = mysqli_query($con, inspref($sqld));
if (!$resd)
{
    print ("<br/> <br/> <br/> <h2>Impossibile visualizzare i dati </h2>");
}
else
{
    print ("<select name='docente' ONCHANGE='cat.submit();'>");
    print ("<option>");
    while ($datal = mysqli_fetch_array($resd))
    {
        print("<option value='");
        print($datal['iddocente']);
        print("'");
        if ($docente == $datal['iddocente'])
        {
            print " selected";
        }
        print ">";
        print($datal['cognome']);
        print("&nbsp;");
        print($datal['nome']);
    }

}
print("</select> </td> </tr>");
print("</table>");
print "</form>";
if ($docente != '')
{
    print "<form method='post' action='inscat.php'>
               <input type='hidden' name='docente' value='$docente'>";
    $query = "select distinct idmateria from tbl_cattnosupp
                where iddocente=$docente
                and idalunno=0
                ";
    $rismat = mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
    $arrmat = array();
    while ($recmat = mysqli_fetch_array($rismat))
    {
        $arrmat[] = $recmat['idmateria'];
    }

    print "
        <p align='center'><input type='submit' value='Inserisci cattedre' name='b'></p>
     ";
    print "<table align='center' border=1>";
    print("<tr class='prima'><td width='50%' align=center><b>Materia</b></td>
                   <td width='50%' align=center><b>Classi</b></td>
               </tr>");


    for ($i = 1; $i <= 15; $i++)
    {
        $arrcla = array();
        if (isset($arrmat[$i - 1]))
        {
            $query = "select distinct idclasse from tbl_cattnosupp
                      where iddocente=$docente
                      and idmateria=" . $arrmat[$i - 1] . "
                      and idalunno=0";
            // print "tttt $query";
            $riscla = mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));

            while ($reccla = mysqli_fetch_array($riscla))
            {
                $arrcla[] = $reccla['idclasse'];
            }
        }
        print("<tr><td width='50%'>");
        //   Selezione materia
        print("<select name='mat" . $i . "'>");
        print("<option>");
        print("<option value='ALL'>TUTTE");
        $query = "SELECT idmateria,denominazione FROM tbl_materie WHERE idmateria>0 ORDER BY denominazione";
        $ris = mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
        while ($nom = mysqli_fetch_array($ris))
        {
            print "<option value=";
            print ($nom["idmateria"]);

            if (isset($arrmat[$i - 1])&& $nom['idmateria'] == $arrmat[$i - 1])
            {
                print " selected";
            }
            print ">";
            print ($nom["denominazione"]);
        }
        print("</select>");

        print("</td>");
        print("<td width='50%'>");
        //   Selezione tbl_classi
        print ("<select multiple size=8 name='classe" . $i . "[]'>");
        $query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY specializzazione,anno,sezione";
        $ris = mysqli_query($con, inspref($query));
        while ($nom = mysqli_fetch_array($ris))
        {
            print "<option value='";
            print ($nom["idclasse"]);
            print "'";
            // VEDO SE LA CLASSE FA PARTE DELLA CATTEDRA
            $clacatt = false;
            foreach ($arrcla as $cla)
            {
                if ($cla == $nom['idclasse'])
                {
                    $clacatt = true;
                }
            }

            if ($clacatt) print " selected";
            print ">";
            print ($nom["anno"]);
            print "&nbsp;";
            print($nom["sezione"]);
            print "&nbsp;";
            print($nom["specializzazione"]);
        }
        print("</select>");
        print("</td></tr>");
    }


    echo('
      
    </table>
 
    <table align="center">
      <tr><td><font color="red">
        ATTENZIONE! L&acute;inserimento canceller&agrave; i vecchi dati per il docente!
        </font>
        </td>
      </tr>
      <tr>   
      <td>
        <p align="center"><input type="submit" value="Inserisci cattedra" name="b"></p>
     </td>
     </tr> 
   
</table></form><hr>
 
    ');
}
mysqli_close($con);
stampa_piede("");

