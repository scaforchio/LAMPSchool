<?php

session_start();

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';

//require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();
// istruzioni per tornare alla pagina di login 
////session_start();

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$daticrud = $_SESSION['daticrud'];
$titolo = $daticrud['titolo'];
$script = "";
stampa_head($titolo, "", $script, "PMSDA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

ordina_array_su_campo_sottoarray($daticrud['campi'], 1);

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
$strcampiordinamento = implode(",", $daticrud['campiordinamento']);
// Costruzione query
$query = "select " . $daticrud['campochiave'] . ", $strcampi from " . $daticrud['tabella'] . " where true " . $strcondizione;
// print $query;
$ris = mysqli_query($con, $query) or die("Errore " . $query . " ERR " . mysqli_error($con));
if ($daticrud['abilitazioneinserimento'] == 1)
    print "<br><center><a href='CRUDmodifica.php?id=0'><b>INSERISCI NUOVO</b></a></center><br><br>";

print "<table align='center' border='1'>";
// Visualizzazione intestazioni
print "<tr class='prima'>";
foreach ($daticrud['campi'] as $c)
    if ($c[1] != 0)
        print "<td><b>$c[6]</b></td>";
if ($daticrud['abilitazionecancellazione'] == 1 | $daticrud['abilitazionemodifica'] == 1)
    print "<td>Azioni</td>";
print "</tr>";

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
            }
            else              // Campo in tabella esterna
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
                $risest = mysqli_query($con, $queryesterna) or die("Errore: " . $queryesterna);
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
print "</table><br>";


stampa_piede();

function controlloCanc($con, $vincolicanc, $chiave)
{
    $possibilecanc = true;
    foreach ($vincolicanc as $vincolo)
    {
        $query = "select * from " . $vincolo[0] . " where " . $vincolo[1] . " = '" . $chiave . "'";

        $ris = mysqli_query($con, $query) or die("Errore: " . $query);
        if (mysqli_num_rows($ris) > 0)
        {
            $possibilecanc = false;
        }
    }
    return $possibilecanc;
}
