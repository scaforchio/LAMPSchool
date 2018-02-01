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
$titolo = "Riepilogo argomenti svolti";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";

stampa_head($titolo, "", $script, "LT");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$id_ut_doc = $_SESSION["idutente"];
if($id_ut_doc>2100000000) $id_ut_doc-=2100000000;
$idmateria = stringa_html('idmateria');

print ('
   <form method="post" action="riepargomgen.php" name="argomenti">
   
   <p align="center">
   <table align="center"><tr><td align="center">');
//
//  Riempimento combobox delle cattedre
//
$query = "SELECT DISTINCT tbl_materie.idmateria as idmateria, tbl_alunni.idclasse as idclasse, denominazione
        FROM tbl_alunni, tbl_materie, tbl_cattnosupp, tbl_docenti
        WHERE tbl_alunni.idclasse = tbl_cattnosupp.idclasse
        AND tbl_cattnosupp.iddocente = tbl_docenti.iddocente
        AND tbl_cattnosupp.idmateria = tbl_materie.idmateria
        AND tbl_alunni.idalunno =$id_ut_doc
        AND tbl_docenti.iddocente <>1000000000
        ORDER BY denominazione";

// print inspref($query);   
print "<select name='idmateria' ONCHANGE='argomenti.submit()'><option value=''>&nbsp;</option>";

$ris = mysqli_query($con, inspref($query));

while ($nom = mysqli_fetch_array($ris))
{
    $idclasse = $nom["idclasse"];
    print "<option value='";
    print ($nom["idmateria"]);
    print "'";
    if ($idmateria == $nom["idmateria"])
    {
        print " selected";
    }
    print ">";
    print ($nom["denominazione"]);

}


print("</select></td></tr>");


print("</table></form>");

//  if ($mese=="")
//     $m=0;
//  else
//     $m=$mese; 

//  if ($anno=="") 
//     $a=0;
//  else
//     $a=$anno; 


// print($nome." -   ". $g.$m.$a.$giornosettimana);

//   $idclasse=$nome;
//  $classe="";

if ($idmateria != "")
{
    $query = 'SELECT * FROM tbl_classi WHERE idclasse="' . $idclasse . '" ';
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

    if ($val = mysqli_fetch_array($ris))
    {
        $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
    }

    echo '<center><h3>Argomenti ed attivit&agrave; svolte nella classe ' . $classe . '</h3></center>';

//
//   ESTRAZIONE DATI DELLE LEZIONI
//
    if ($idclasse != "")
    {
        $query = "select * from tbl_lezioni where idclasse=$idclasse and idmateria=$idmateria and (argomenti<>'' or attivita<>'') order by datalezione";

        $rislez = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

        if (mysqli_num_rows($rislez) == 0)
        {
            print  "<center><br><b>Nessun argomento registrato!</b><br></center>";
        }
        else
        {
            print "
                    <table border=2 align='center'>
                        <tr class='prima'>
                            <td width=10%>Data</td>
                            <td width=45%>Argomenti</td>
                            <td width=45%>Attivit&agrave;</td>";

            while ($reclez = mysqli_fetch_array($rislez))
            {
                print "<tr><td>" . data_italiana($reclez['datalezione']) . "</td><td>" . $reclez['argomenti'] . "&nbsp;</td><td>" . $reclez['attivita'] . "&nbsp;</td></tr>";
            }

            print "</table>";

            if (alunno_certificato($id_ut_doc, $con))
            {
                $query = "select * from tbl_lezionicert where idclasse=$idclasse and idmateria=$idmateria and idalunno=$id_ut_doc order by datalezione";

                $rislez = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

                if (mysqli_num_rows($rislez) == 0)
                {
                    print  "<center><br><b>Nessuna attività di sostegno registrata!</b><br></center>";
                }
                else
                {
                    print "<center><br><b>Attività di sostegno</b><br><br></center>
                    <table border=2 align='center'>
                        <tr class='prima'>
                            <td width=10%>Data</td>
                            <td width=45%>Argomenti</td>
                            <td width=45%>Attivit&agrave;</td>";

                    while ($reclez = mysqli_fetch_array($rislez))
                    {
                        print "<tr><td>" . data_italiana($reclez['datalezione']) . "</td><td>" . $reclez['argomenti'] . "&nbsp;</td><td>" . $reclez['attivita'] . "&nbsp;</td></tr>";
                    }

                    print "</table>";
                }

            }
        }
        // VISUALIZZARE ARGOMENTI SOSTEGNO
    }
}

mysqli_close($con);
stampa_piede("");

