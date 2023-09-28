<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2015 Pietro Tamburrano
  Copyright (C) 2023 Pietro Tamburrano, Vittorio Lo Mele
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
$titolo = "Riepilogo argomenti svolti (per materia)";
$script = "
<style>
.lscontainer {
    margin-left: 10px;
    margin-right: 10px;
}
</style>";
stampa_head_new($titolo, "", $script, "LT");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


$id_ut_doc = $_SESSION["idutente"];
if ($id_ut_doc > 2100000000)
    $id_ut_doc -= 2100000000;
$idmateria = stringa_html('idmateria');

print ('<form method="post" action="riepargomgen.php" class="container-form-pre" name="argomenti">');
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
print "<select style='width:400px;' class='form-select form-select-sm mb-2' name='idmateria' ONCHANGE='argomenti.submit()'><option value=''>Seleziona una materia...</option>";

$ris = eseguiQuery($con, $query);

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

print("</select></form>");

if ($idmateria != "")
{
    $query = 'SELECT * FROM tbl_classi WHERE idclasse="' . $idclasse . '" ';
    $ris = eseguiQuery($con, $query);

    if ($val = mysqli_fetch_array($ris))
    {
        $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
    }

    if ($idclasse != "")
    {
        $query = "select * from tbl_lezioni where idclasse='$idclasse' and idmateria='$idmateria' and (argomenti<>'' or attivita<>'') order by datalezione";

        $rislez = eseguiQuery($con, $query);

        if (mysqli_num_rows($rislez) == 0)
        {
            alert("Nessun argomento registrato");
        } else
        {
        ?>
            <center class="mb-2"><b>Attività svolte</b></center>
            <div style='margin-left:5px; margin-right:5px; margin-bottom: 10px;'>
            <table class='table table-striped table-bordered' id='tabelladati' width='100%' >
            <thead>
                <tr>
                    <td>Data</td>
                    <td class='not-mobile'>Argomenti</td>
                    <td class='max-mobile'>Argomenti</td>
                    <td class='not-mobile'>Attivit&agrave;</td>
                </tr>
            </thead>
            <tbody>
        <?php
            while ($reclez = mysqli_fetch_array($rislez))
            {
                if ($reclez['idlezionegruppo']==NULL || $reclez['idlezionegruppo']==0 )
                    print "<tr><td data-sort='" . data_dt($reclez['datalezione'])  . "'>" . data_italiana($reclez['datalezione']) . "</td><td>" . $reclez['argomenti'] . "</td><td>" . shorten($reclez['argomenti'], 15) . "</td><td>" . $reclez['attivita'] . "</td></tr>";
                else
                {
                    // VERIFICO SE ALUNNO APPARTIENE A GRUPPO
                    if (verifica_alunno_lezionegruppo($id_ut_doc, $reclez['idlezionegruppo'], $con))
                         print "<tr><td data-sort='" . data_dt($reclez['datalezione'])  . "'>" . data_italiana($reclez['datalezione']) . "</td><td>" . $reclez['argomenti'] . "</td><td>" . shorten($reclez['argomenti'], 15) . "</td><td>" . $reclez['attivita'] . "</td></tr>";   
                }
                
            }

            
            print "</tbody></table></div>";

            if (alunno_certificato($id_ut_doc, $con))
            {
                $query = "select * from tbl_lezionicert where idclasse='$idclasse' and idmateria='$idmateria' and idalunno='$id_ut_doc' order by datalezione";

                $rislez = eseguiQuery($con, $query);

                if (mysqli_num_rows($rislez) == 0)
                {
                    alert("Nessuna attività di sostegno registrata!");
                } else
                {
                    print "<center><b>Attività di sostegno</b><br></center>";
                    ?>
            <div style='margin-left:5px; margin-right:5px; margin-bottom: 10px;'>
            <table class='table table-striped table-bordered' id='tabelladati2' width='100%' >
            <thead>
                <tr>
                    <td>Data</td>
                    <td class='not-mobile'>Argomenti</td>
                    <td class='max-mobile'>Argomenti</td>
                    <td class='not-mobile'>Attivit&agrave;</td>
                </tr>
            </thead>
            <tbody>
        <?php
                    while ($reclez = mysqli_fetch_array($rislez))
                    {
                        print "<tr><td data-sort='" . data_dt($reclez['datalezione'])  . "'>" . data_italiana($reclez['datalezione']) . "</td><td>" . $reclez['argomenti'] . "</td><td>" . shorten($reclez['argomenti'], 15) . "</td><td>" . $reclez['attivita'] . "</td><td>" . $reclez['attivita'] . "</td></tr>";
                    }

                    print "</tbody></table></div>";
                }
            }
        }
        // VISUALIZZARE ARGOMENTI SOSTEGNO
    }
}

import_datatables();

?>

<script>
    $(document).ready(function() {
        let table = new DataTable('#tabelladati', {
            responsive: true,
            pageLength: 10,
            scrollX: true,
            order: [[0, 'desc']],
            'language': {
                'search': 'Filtra risultati:',
                'zeroRecords': 'Nessun dato da visualizzare',
                'info': 'Mostrate righe da _START_ a _END_ di _TOTAL_',
                'lengthMenu': 'Visualizzate _MENU_ righe',
                'paginate': {
                    'first': 'Prima',
                    'previous': 'Prec.',
                    'next': 'Succ.',
                    'last': 'Ultima'
                }
            }
        });
    });

    $(document).ready(function() {
        let table = new DataTable('#tabelladati2', {
            responsive: true,
            pageLength: 10,
            scrollX: true,
            order: [[0, 'desc']],
            'language': {
                'search': 'Filtra risultati:',
                'zeroRecords': 'Nessun dato da visualizzare',
                'info': 'Mostrate righe da _START_ a _END_ di _TOTAL_',
                'lengthMenu': 'Visualizzate _MENU_ righe',
                'paginate': {
                    'first': 'Prima',
                    'previous': 'Prec.',
                    'next': 'Succ.',
                    'last': 'Ultima'
                }
            }
        });
    });
</script>

<?php

mysqli_close($con);
stampa_piede_new("");

