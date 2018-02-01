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
$maxcattedre=10;
$titolo = "Gestione cattedra docente di sostegno";
$script = "";
stampa_head($titolo, "", $script, "MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

//
//    Fine parte iniziale della pagina
//

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

print ("
   <form method='post' action='cat_sost.php' name='cat_sost'>
   
   <p align='center'>
   <table align='center' border='1'>

      <tr class='prima'>
      <td colspan='2' align='center'><b>Docente</b>");

$sqld = "SELECT * FROM tbl_docenti WHERE sostegno ORDER BY cognome, nome";
$resd = mysqli_query($con, inspref($sqld));
if (!$resd)
{
    print ("<br/> <br/> <br/> <h2>a Impossibile visualizzare i dati </h2>");
}
else
{
    print ("<select name='docente' ONCHANGE='cat_sost.submit();'>");
    print ("<option>");
    while ($datal = mysqli_fetch_array($resd))
    {
        print("<option value='");
        print($datal['iddocente']);
        print "'";
        if ($docente == $datal['iddocente'])
        {
            print " selected";
        }
        print(">");
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

    print "<form method='post' action='inscat_sost.php'>
               <input type='hidden' name='docente' value='$docente'>
               <table align='center' border='1'>";
    $query = "select distinct idalunno from tbl_cattnosupp
                where iddocente=$docente
                and idalunno<>0";
    $risalu = mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));
    $arralu = array();

    print("<tr class='prima'><td width='50%' align=center><b>Alunno</b></td>
                   <td width='50%' align=center><b>Materia</b></td>
               </tr>");

    while ($recalu = mysqli_fetch_array($risalu))
    {
        $arralu[] = $recalu['idalunno'];
    }


    for ($i = 1; $i <= $maxcattedre; $i++)
    {

        $arrmat = array();
        if (isset($arralu[$i - 1]))
        {
            $query = "select distinct idmateria from tbl_cattnosupp
                      where iddocente=$docente
                      and idalunno=" . $arralu[$i - 1];
            // print "tttt $query";
            $rismat = mysqli_query($con, inspref($query)) or die ("Errore:" . inspref($query));

            while ($recmat = mysqli_fetch_array($rismat))
            {
                $arrmat[] = $recmat['idmateria'];
            }
        }

        print("<tr><td width='50%'>");
        //   Selezione materia
        print("<select name='alu$i'><option value='0'>&nbsp;</option>");

        $query = "SELECT idalunno,cognome,nome,datanascita FROM tbl_alunni WHERE certificato ORDER BY cognome,nome";
        $ris = mysqli_query($con, inspref($query));
        while ($nom = mysqli_fetch_array($ris))
        {
            print "<option value='";
            print ($nom["idalunno"]);
            print "'";
            if ($nom['idalunno'] == $arralu[$i - 1])
            {
                print " selected";
            }
            print ">";
            print ($nom["cognome"]);
            print "&nbsp;";
            print($nom["nome"]);
            print "&nbsp;(";
            print(data_italiana($nom["datanascita"]));
            print ") - " . decodifica_classe(estrai_classe_alunno($nom["idalunno"], $con), $con);
        }
        print("</select>");
        print("</td>");
        print("<td>");
        print ("<select multiple size=8 name='materie" . $i . "[]'>");
        $query = "SELECT idmateria,denominazione FROM tbl_materie WHERE idmateria>0 ORDER BY denominazione";
        $ris = mysqli_query($con, inspref($query));
        while ($nom = mysqli_fetch_array($ris))
        {
            print "<option value='";
            print ($nom["idmateria"]);
            print "'";
            $matcatt = false;
            foreach ($arrmat as $mat)
            {
                if ($mat == $nom['idmateria'])
                {
                    $matcatt = true;
                }
            }
            if ($matcatt) print " selected";
            print ">";
            print ($nom["denominazione"]);

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
     </form></td>
     </tr> 
   
</table><hr>
 
    ');
}
mysqli_close($con);
stampa_piede(""); 

