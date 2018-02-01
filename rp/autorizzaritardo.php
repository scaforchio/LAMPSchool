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


$idclasse = stringa_html('idclasse');
$idalunno = stringa_html('idalunno');


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

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

$titolo = "Autorizzazione entrate in ritardo";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }

         //-->
         </script><script src='../lib/js/popupjquery.js'></script>
         <script>

$(document).ready(function(){

				 $('input[name^=\"oraentrata\"]').datetimepicker({
						formatTime: 'H:i',
						format: 'H:i',
						step: 5,
                        datepicker:false
					});
			 });


</script>";

stampa_head($titolo,"",$script,"SDMAP");

stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$query = "SELECT * FROM tbl_ritardi, tbl_alunni, tbl_classi
	        WHERE tbl_ritardi.idalunno=tbl_alunni.idalunno
	        AND tbl_alunni.idclasse = tbl_classi.idclasse
	        AND data='" . date('Y-m-d') . "'
	        AND NOT autorizzato
	        ORDER BY cognome,nome";

$ris = mysqli_query($con, inspref($query)) or die("Errore:" . inspref($query));




if (mysqli_num_rows($ris) > 0)
{
    print "<form action='insautorizzazioni.php' method='post'>";
    print "<br><table align='center' border='1'><tr class='prima'><td>Alunno</td><td>Classe</td><td>Ora</td><td>Autorizz.</td><td>Giustif.</td><td>Precedenti</td></tr>";
    while ($rec = mysqli_fetch_array($ris))
    {

        $idalunno=$rec['idalunno'];
/*
        $queryprec="select count(*) as ritardiprec from tbl_ritardi where idalunno=$idalunno and data<'".date('Y-m-d')."'";
        $risprec=mysqli_query($con,inspref($queryprec)) or die("Errore:".inspref($queryprec,false));
        $recprec=mysqli_fetch_array($risprec);
        $numeroritardiprecedenti=$recprec['ritardiprec']; */

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



        $queryprec="select count(*) as ritardiprec from tbl_ritardi where idalunno=$idalunno and data<'".date('Y-m-d')."' and not giustifica";
        $risprec=mysqli_query($con,inspref($queryprec)) or die("Errore:".inspref($queryprec,false));
        $recprec=mysqli_fetch_array($risprec);
        $numeroritardiprecsenzagiust=$recprec['ritardiprec'];

        if (date('Y-m-d') > $fineprimo)
            $numritardisec--;
        else
            $numritardiprimo--;
        print "<tr><td>" . $rec['cognome'] . " " . $rec['nome'];
        if ($rec['firmapropria'])
            print "<small><br>(Autorizz. firma propria)";
        print "</td>
                   <td>" . $rec['anno'] . " " . $rec['sezione'] . " " . $rec['specializzazione'] . "
                   <td><input type='text' name='oraentrata".$rec['idalunno']."' value='" .  substr($rec['oraentrata'],0,5) . "' maxlength='5' size=5></td>
                   <td align='center'><input type='checkbox' name='aut" . $rec['idalunno'] . "'><input type='hidden' name='idritardo".$rec['idalunno']."' value='".$rec['idritardo']."'></td>
                   <td align='center'><input type='checkbox' name='giu" . $rec['idalunno'] . "'></td>
                   <td align='center'>1°Q=<b>$numritardiprimo</b>";
                   if (date('Y-m-d') > $fineprimo)
                   {
                       print " - 2°Q=<b>$numritardisec</b>";
                   }

         print " <font color='red'>($numeroritardiprecsenzagiust)";

        print "<a href='../assenze/sitassalu.php?alunno=$idalunno' class='popupjq'><img src='../immagini/tabella.png'></a></td></tr>";
    }
    print "</table>";
    print "<br><center><input type='submit' value='Autorizza entrata'></center>";
    print "</form>";
}
else
{

    print("<center><b><br>Nessun ritardo da autorizzare!</b></center>");
}


mysqli_close($con);
stampa_piede(""); 

