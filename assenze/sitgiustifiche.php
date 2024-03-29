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
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Visualizzazione mancate giustifiche";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri)
               {
                  window.open(apri, '', stile);
               }

         //-->
         </script>
         <script>
function checkTutti()
{
   with (document.listaammoniz)
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
   with (document.listaammoniz)
   {
      for (var i=0; i < elements.length; i++)
      {
         if (elements[i].type == 'checkbox')
            elements[i].checked = false;
      }
   }
}
</script>";
stampa_head($titolo, "", $script, "MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
//$giornianticipo=0-$_SESSION['maxritardogiust'];
$datalimiteinferiore = giorno_lezione_passata(date('Y-m-d'), $_SESSION['maxritardogiust'], $con);
$query = "SELECT count(*) as nang,cognome, nome,tbl_alunni.idalunno,dataammonizione FROM tbl_assenze,tbl_alunni,tbl_classi WHERE (isnull(giustifica) or giustifica=0) AND data< '$datalimiteinferiore'
            AND tbl_assenze.idalunno=tbl_alunni.idalunno AND tbl_alunni.idclasse=tbl_classi.idclasse
            AND tbl_alunni.idclasse<>0
            AND tbl_assenze.idalunno NOT IN (select idalunno from tbl_assenze where data>='$datalimiteinferiore')
            AND tbl_assenze.idalunno NOT IN (select idalunno from tbl_presenzeforzate where data>='$datalimiteinferiore')    
            GROUP BY tbl_alunni.idalunno,dataammonizione
            ORDER BY cognome, nome, tbl_alunni.idalunno, data desc";


$ris = eseguiQuery($con, $query);
//print inspref($query,false);
print "<form name='listaammoniz' action='insammonizioni.php' method='post'>";
print "<center><input type='submit' value='Inserisci ammonizioni'></center><br><br>";
print "<br><center><input type='button' value='Seleziona tutti' onclick='checkTutti()'>
   <input type='button' value='Deseleziona tutti' onclick='uncheckTutti()'></center><br>";
//print "<center><small><br><b> ATTENZIONE: sono visualizzati in questo elenco solo gli alunni che non hanno avuto assenze dal " . data_italiana($datalimiteinferiore) . " in poi.</b><br><br></small></center>";

if (mysqli_num_rows($ris) > 0)
{

    print "<center><b>ASSENZE ANTECEDENTI AL " . data_italiana($datalimiteinferiore) . " NON GIUSTIFICATE<br>"
            . "di alunni non assenti dal " . data_italiana($datalimiteinferiore) . "</b></center>";
    print "<table align='center' border='1'>
			   <tr class='prima'>
				   <td>Alunno</td>
				   <td align='center'>Numero</td>
				   <td align='center'>Situaz. ass.</td>
				   <td align='center'>Ammoniz.</td>

			   </tr>
		   ";
    while ($val = mysqli_fetch_array($ris))
    {
        print "<tr><td>" .
                estrai_dati_alunno($val['idalunno'], $con) . " (" . $val['idalunno'] . ") - " . decodifica_classe(estrai_classe_alunno($val['idalunno'], $con), $con) .
                "</td><td align='center'>" . $val['nang'] . "</td>";
        print "<td align='center'>";
        $dataammonizione = $val['dataammonizione'];
        // Cerco assenze da considerare
        if ($dataammonizione == NULL)
            $query = "SELECT * from tbl_assenze where idalunno=" . $val['idalunno'] . " AND dataammonizione IS NULL AND (isnull(giustifica) or giustifica=0) "
                    . "        AND idalunno NOT IN (select idalunno from tbl_assenze where data>='$datalimiteinferiore')"
                    . "        AND idalunno NOT IN (select idalunno from tbl_presenzeforzate where data>='$datalimiteinferiore') "
                    . "        order by data desc";
        else
            $query = "SELECT * from tbl_assenze where idalunno=" . $val['idalunno'] . " AND dataammonizione ='$dataammonizione' AND (isnull(giustifica) or giustifica=0) "
                    . " AND idalunno NOT IN (select idalunno from tbl_assenze where data>='$datalimiteinferiore')"
                    . " AND idalunno NOT IN (select idalunno from tbl_presenzeforzate where data>='$datalimiteinferiore') "
                    . "order by data desc";
        $risasse = eseguiQuery($con, $query);
        while ($recasse = mysqli_fetch_array($risasse))
        {
            if ($recasse['data'] < $datalimiteinferiore)
                print "<font color='red'>" . data_italiana($recasse['data']) . "</font><br>";
            else
                print "<font color='green'>" . data_italiana($recasse['data']) . "</font><br>";
        }

        //print "<a href=javascript:Popup('sitassalu.php?alunno=".$val['idalunno'];
        //print"')><img src='../immagini/tabella.png'></a>";



        print "</td>";
        if ($dataammonizione == NULL)
            print "<td align='center'><input type='checkbox' name='ammass" . $val['idalunno'] . "' value='yes'></td>";
        else
            print "<td align='center'>" . data_italiana($dataammonizione) . "</td>";
        print "</tr>";
    }
    print "</table>";
}
// fine if

/*
  $query = "SELECT count(*) as nrng,cognome, nome,tbl_alunni.idalunno,dataammonizione FROM tbl_ritardi,tbl_alunni,tbl_classi "
  . "WHERE (isnull(giustifica) or giustifica=0) AND data< '$datalimiteinferiore'
  AND tbl_ritardi.idalunno=tbl_alunni.idalunno AND tbl_alunni.idclasse=tbl_classi.idclasse
  AND tbl_alunni.idclasse<>0
  AND tbl_ritardi.idalunno NOT IN (select idalunno from tbl_assenze where data<='$datalimiteinferiore')
  GROUP BY tbl_alunni.idalunno,dataammonizione
  ORDER BY anno,specializzazione,sezione,cognome, nome, tbl_alunni.idalunno, data desc";
 */
$query = "SELECT count(*) as nrng,cognome, nome,tbl_alunni.idalunno,dataammonizione FROM tbl_ritardi,tbl_alunni,tbl_classi "
        . "WHERE (isnull(giustifica) or giustifica=0) AND data< '$datalimiteinferiore'
            AND tbl_ritardi.idalunno=tbl_alunni.idalunno AND tbl_alunni.idclasse=tbl_classi.idclasse
            AND tbl_alunni.idclasse<>0
            AND tbl_ritardi.idalunno NOT IN (select idalunno from tbl_assenze where data='" . date('y-m-d') . "')
            AND tbl_ritardi.idalunno NOT IN (select idalunno from tbl_presenzeforzate where data='" . date('y-m-d') . "')
            GROUP BY tbl_alunni.idalunno,dataammonizione
            ORDER BY cognome, nome, tbl_alunni.idalunno, data desc
            ";
$ris = eseguiQuery($con, $query);
if (mysqli_num_rows($ris) > 0)
{

    print "<br>";
    print "<center><b>RITARDI ANTECEDENTI AL " . data_italiana($datalimiteinferiore) . " NON GIUSTIFICATI<br>"
            . "di alunni non assenti oggi.</b></center>";
    print "<table align='center' border='1'>
			   <tr class='prima'>
				   <td>Alunno</td>
				   <td align='center'>Numero</td>
				   <td align='center'>Situaz. rit.</td>
				   <td align='center'>Ammoniz.</td>

			   </tr>
		   ";
    while ($val = mysqli_fetch_array($ris))
    {
        print "<tr><td>" . estrai_dati_alunno($val['idalunno'], $con) . " (" . $val['idalunno'] . ") - " .
                decodifica_classe(estrai_classe_alunno($val['idalunno'], $con), $con) .
                "</td><td align='center'>" . $val['nrng'] .
                "</td>";
        print "<td align='center'>";
        $dataammonizione = $val['dataammonizione'];
        // Cerco ritardi da considerare
        /*  if ($dataammonizione == NULL)
          $query = "SELECT * from tbl_ritardi where idalunno=" . $val['idalunno'] . " AND dataammonizione IS NULL AND (isnull(giustifica) or giustifica=0) "
          . "AND idalunno NOT IN (select idalunno from tbl_assenze where data>='$datalimiteinferiore')"
          . "order by data desc";
          else
          $query = "SELECT * from tbl_ritardi where idalunno=" . $val['idalunno'] . " AND dataammonizione ='$dataammonizione' AND (isnull(giustifica) or giustifica=0) "
          . "AND idalunno NOT IN (select idalunno from tbl_assenze where data>='$datalimiteinferiore')"
          . "order by data desc";
         * 
         */
        if ($dataammonizione == NULL)
            $query = "SELECT * from tbl_ritardi where idalunno=" . $val['idalunno'] . " AND dataammonizione IS NULL AND (isnull(giustifica) or giustifica=0) "
                    . "AND data< '$datalimiteinferiore' AND idalunno NOT IN (select idalunno from tbl_assenze where data='" . date('y-m-d') . "') "
                    . "AND idalunno NOT IN (select idalunno from tbl_presenzeforzate where data='" . date('y-m-d') . "') "
                    . "order by data desc";
        else
            $query = "SELECT * from tbl_ritardi where idalunno=" . $val['idalunno'] . " AND dataammonizione ='$dataammonizione' AND (isnull(giustifica) or giustifica=0) "
                    . "AND data< '$datalimiteinferiore' AND idalunno NOT IN (select idalunno from tbl_assenze where data='" . date('y-m-d') . "')"
                    . "AND idalunno NOT IN (select idalunno from tbl_presenzeforzate where data='" . date('y-m-d') . "') "
                    . "order by data desc";
        $risasse = eseguiQuery($con, $query);
        while ($recasse = mysqli_fetch_array($risasse))
        {
            if ($recasse['data'] < $datalimiteinferiore)
                print "<font color='red'>" . data_italiana($recasse['data']) . "</font><br>";
            else
                print "<font color='green'>" . data_italiana($recasse['data']) . "</font><br>";
        }

        //print "<a href=javascript:Popup('sitassalu.php?alunno=".$val['idalunno'];
        //print"')><img src='../immagini/tabella.png'></a>";



        print "</td>";
        // print "<td align='center'><a href=javascript:Popup('sitassalu.php?alunno=".$val['idalunno']."')><img src='../immagini/tabella.png'></a></td>";


        if ($dataammonizione == NULL)
            print "<td align='center'><input type='checkbox' name='ammrit" . $val['idalunno'] . "' value='yes'></td>";
        else
            print "<td align='center'>" . data_italiana($dataammonizione) . "</td>";
        print "</tr>";
    }
    print "</table>";
}
print "<br><center><input type='submit' value='Inserisci ammonizioni'></center><br>";
print "</form>";

mysqli_close($con);
stampa_piede("");

