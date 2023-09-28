<?php

require_once '../lib/req_apertura_sessione.php';

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$daticrud = $_SESSION['daticrud'];
ordina_array_su_campo_sottoarray($daticrud['campi'], 1);
$titolo = $daticrud['titolo'];

// PREPARAZIONE OPZIONE ORDINAMENTO INIZIALE
$stringaopzioniord = "";
//print $daticrud['campiordinamento'];
$campiordinamento = explode(",", $daticrud['campiordinamento']);
foreach ($campiordinamento as $campoord)
{
    $indicecampo = 0;
    $arrord = explode(" ", $campoord);
    $tipoord = $arrord[1];
    $campoord = $arrord[0];
    // print "DATI $campoord ".$tipoord;
    foreach ($daticrud['campi'] as $campo)
    {
        // print "DATI $campoord ".$campo[0];
        // print "DATI $campoord ".$tipoord[1];
        if ($tipoord == '' | $tipoord == 'ASC' | $tipoord == 'asc')
        {
            if ($campoord == $campo[0])
                $stringaopzioniord .= "[$indicecampo,'asc'],";
        } else
        {
            if ($campoord == $campo[0])
                $stringaopzioniord .= "[$indicecampo,'desc'],";
        }
        $indicecampo++;
    }
}
//print "Stringa $stringaopzioniord";
$stringaopzioniord = substr($stringaopzioniord, 0, strlen($stringaopzioniord) - 1);

$stringaopzioniord = "[" . $stringaopzioniord . "]";
//$strcampiordinamento = implode(",", $daticrud['campiordinamento']);


$script = "
<style>
.lscontainer {
    margin-left: 10px;
    margin-right: 10px;
}
</style>";
stampa_head_new($titolo, "", $script, "PMSDA");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);



$strcampi = "";
$strtabelle = $daticrud['tabella'] . ", ";
$strconcat = "";
// Campi senza chiave esterna


foreach ($daticrud['campi'] as $c)
    if ($c[1] != 0)
        $strcampi .= $c[0] . ", ";
$strcampi = substr($strcampi, 0, strlen($strcampi) - 2);

$strcondizione = " and " . $daticrud['condizione'];

// Campi per ordinamento in visualizzazione
// Costruzione query
$query = "select " . $daticrud['campochiave'] . ", $strcampi from " . $daticrud['tabella'] . " " . $daticrud['aliastabella'] . " where true " . $strcondizione;
// print $query;
$ris = eseguiQuery($con, $query);
if ($daticrud['abilitazioneinserimento'] == 1)
//print "<br><center><a href='CRUDmodifica.php?id=0'><b>INSERISCI</b></a></center><br><br>";
?> 
    <button style='margin-left:5px;' class="btn btn-outline-secondary mb-3" onclick="window.location.href = 'CRUDmodifica.php?id=0'">
    INSERISCI</button>
<?php
//print "<table id='tabelladati' class='table table-striped table-bordered' width='" . $daticrud['larghezzatabella'] . "'>";
print "<div style='margin-left:5px; margin-right:5px;'><table id='tabelladati' class='table table-striped table-bordered'>";
// Visualizzazione intestazioni
print "<thead><tr>";
foreach ($daticrud['campi'] as $c)
    if ($c[1] != 0)
        print "<th>$c[6]</th>";
if ($daticrud['abilitazionecancellazione'] == 1 | $daticrud['abilitazionemodifica'] == 1)
    print "<th>Azioni</th>";
print "</tr></thead>";

$dativis = array();

while ($rec = mysqli_fetch_array($ris))
{
    //  print "Numero elementi ".count($daticrud['fk']);
    $datirigavis = array();
    $dati = array();
    // 
    foreach ($daticrud['campi'] as $c)
    {
        $queryesterna = "";
        $numeroalias = 0;

        if ($c[1] != 0)   // Campo da visualizzare nell'elenco
        {

            if ($c[2] == '')  // Campo in tabella principale
            {
                if ($c[8] == 'boolean')
                    $strvis = $rec[$c[0]] == 0 ? "No" : "S&igrave;";
                else
                    $strvis = $rec[$c[0]];
            } else              // Campo in tabella esterna
            {

                $strcampi = "";
                $strvis = "";
                $elcampitabesterna = explode(",", $c[4]);

                if ($c[14] != '')
                {
                    $elcampialias = explode(",", $c[14]);
                    $numeroalias = count($elcampialias);
                } else
                    $numeroalias = 0;
                $numerocampi = count($elcampitabesterna);

                if ($numeroalias > 0 & ($numerocampi != $numeroalias))
                    die("Errore nel numero di campi alias!");

                if ($numeroalias == 0)
                    foreach ($elcampitabesterna as $ctb)
                        $strcampi .= $ctb . ", ";
                else
                {

                    for ($nc = 0; $nc < $numerocampi; $nc++)
                    {
                        $ctb = $elcampitabesterna[$nc];
                        $atb = $elcampialias[$nc];

                        $strcampi .= "$ctb as $atb, ";
                    }
                }

                $strcampi = substr($strcampi, 0, strlen($strcampi) - 2);
                $queryesterna = "select " . $strcampi . " from " . $c[2] . " where true and " . $c[3] . " = '" . $rec[$c[0]] . "'";
                $risest = eseguiQuery($con, $queryesterna);
                $recest = mysqli_fetch_array($risest);

                if ($numeroalias > 0)
                {
                    foreach ($elcampialias as $atb)
                    {
                        $strvis .= $recest[$atb] . " ";
                    }
                } else
                {
                    foreach ($elcampitabesterna as $ctb)
                    {
                        $strvis .= $recest[$ctb] . " ";
                    }
                }
            }
            //
            //print "<td>$strvis</td>";
            $dati[] = $strvis;
        }
        $abilcanc = controlloCanc($con, $daticrud['vincolicanc'], $rec[$daticrud['campochiave']]);
        $id = $rec[$daticrud['campochiave']];
    }
    $datirigavis['dati'] = $dati;
    $datirigavis['abilcanc'] = $abilcanc;
    $datirigavis['id'] = $id;
    $dativis[] = $datirigavis;
}
print "<tbody>";
foreach ($dativis as $riga)
{

    print "<tr>";
    // 
    $rigadati = $riga['dati'];
    foreach ($rigadati as $d)
    {

        print "<td>$d</td>";
    }


    if ($daticrud['abilitazionecancellazione'] == 1 | $daticrud['abilitazionemodifica'] == 1)
    {
        print "<td>";

        if ($daticrud['abilitazionemodifica'] == 1)
            print "<a href='CRUDmodifica.php?id=" . $riga['id'] . "'><img src='../immagini/modifica.png'></a>&nbsp;";
        if ($daticrud['abilitazionecancellazione'] == 1)
            if ($riga['abilcanc'])
                if ($daticrud['confermacancellazione'][0] != 1)
                    print "<a href='CRUDcancellazione.php?id=" . $riga['id'] . "'><img src='../immagini/delete.png'></a>";
                else
                    print "<a href='CRUDconfcanc.php?id=" . $riga['id'] . "'><img src='../immagini/delete.png'></a>";
        print "</td>";
    }
    print "</tr>";
}
print "</tbody>";
print "</table></div>";


import_datatables();

?>
<script> 
           $(document).ready( function () {
                let table = new DataTable('#tabelladati', {
                     'order': <?php echo $stringaopzioniord ?>,
                     'pageLength': 100,
                     'language': {
                                   'search': 'Filtra risultati:',
                                   'zeroRecords': 'Nessun dato da visualizzare',
                                   'info': 'Mostrate righe da _START_ a _END_ di _TOTAL_',
                                    'lengthMenu': 'Visualizzate _MENU_ righe',
                                    
                                            'paginate': {
                                                        'first':    'Prima',
                                                        'previous': 'Prec.',
                                                        'next':     'Succ.',
                                                        'last':     'Ultima'
                                                        }
                                   
                                            
                                }
                 });
            } );
            
            </script>

<?php
stampa_piede_new();

function controlloCanc($con, $vincolicanc, $chiave)
{
    $possibilecanc = true;
    foreach ($vincolicanc as $vincolo)
    {
        $query = "select * from " . $vincolo[0] . " where " . $vincolo[1] . " = '" . $chiave . "'";

        $ris = eseguiQuery($con, $query);
        if (mysqli_num_rows($ris) > 0)
        {
            $possibilecanc = false;
        }
    }
    return $possibilecanc;
}
