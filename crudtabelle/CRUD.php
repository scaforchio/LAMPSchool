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
$titolo = "TEST CRUD";
$script = "";
stampa_head($titolo, "", $script, "PMSDA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

$daticrud=$_SESSION['daticrud'];
ordina_array_su_campo_sottoarray($daticrud['campi'], 1);

$strcampi = "";
$strtabelle = $daticrud['tabella'] . ", ";
$strconcat = "";
// Campi senza chiave esterna




foreach ($daticrud['campi'] as $c)
    if ($c[1] != 0)
        if ($c[2] == '')
            $strcampi .= $daticrud['tabella'] . "." . $c[0] . ", ";
        else
        {
            $strtabelle .= $c[2] . ", ";  // TTTT Prevedere la possibilità di utilizzo della stessa tabella più volte
            $strconcat .= "and " . $daticrud['tabella'] . "." . $c[0] . "=" . $c[2] . "." . $c[3] . " ";
            $elcampitabesterna = explode(",", $c[4]);
            foreach ($elcampitabesterna as $ctb)
                $strcampi .= $c[2] . "." . $ctb . ", ";
        }

$strcampi = substr($strcampi, 0, strlen($strcampi) - 2);
$strtabelle = substr($strtabelle, 0, strlen($strtabelle) - 2);

$strcondizione = " and " . $daticrud['condizione'];

// Campi per ordinamento in visualizzazione
$strcampiordinamento = implode(",", $daticrud['campiordinamento']);
// Costruzione query
$query = "select " . $daticrud['campochiave'] . ", $strcampi from " . $strtabelle . " where true " . $strconcat . " " . $strcondizione . " order by $strcampiordinamento";
// print $query;
$ris = mysqli_query($con, $query) or die("Errore " . $query . " ERR " . mysqli_error($con));

print "<br><center><a href='CRUDinserimento.php'><b>INSERISCI NUOVO</b></a></center><br><br>";
print "<table align='center' border='1'>";
// Visualizzazione intestazioni
print "<tr class='prima'>";
foreach ($daticrud['campi'] as $c)
    if ($c[1] != 0)
        print "<td><b>$c[6]</b></td>";
print "<td>Azioni</td>";
print "</tr>";


while ($rec = mysqli_fetch_array($ris))
{
    //  print "Numero elementi ".count($daticrud['fk']);
    print "<tr>";
    // 
    foreach ($daticrud['campi'] as $c)
    {
        if ($c[1] != 0)
        {
            if ($c[2] == '')
            {
                
                $strvis = $rec[$c[0]];
            } else
            {
                $elcampitabesterna = explode(",", $c[4]);
                //    $strvis = $rec[$campo];
                $numerochiave = substr($campo, 3, 1);
                $strvis = "";
                
                foreach ($elcampitabesterna as $ctb)
                {
                    //$nomecampo = $daticrud['fk'][$numerochiave][0].".".$ce;

                    $strvis .= $rec[$ctb] . " ";
                }
            }
            print "<td>$strvis</td>";
        }
    }

    print "<td>";
    if ($daticrud['abilitazionemodifica']==1)
        print "<a href='CRUDmodifica.php?id=" . $rec[$daticrud['campochiave']] . "'><img src='../immagini/modifica.png'></a>&nbsp;";
    if (controlloCanc($con,$daticrud['vincolicanc'],$rec[$daticrud['campochiave']]))
        if ($daticrud['confermacancellazione'][0]!=1)
            print "<a href='CRUDcancellazione.php?id=" . $rec[$daticrud['campochiave']] . "'><img src='../immagini/delete.png'></a>";
        else
            print "<a href='CRUDconfcanc.php?id=" . $rec[$daticrud['campochiave']] . "'><img src='../immagini/delete.png'></a>";
    print "</td>";
    print "</tr>";
}
print "</table><br>";


stampa_piede();


function controlloCanc($con,$vincolicanc,$chiave)
{
    $possibilecanc=true;
    foreach($vincolicanc as $vincolo)
    {
        $query="select * from ".$vincolo[0]." where ".$vincolo[1]." = '".$chiave."'";
        //print $query;
        $ris=mysqli_query($con,$query) or die("Errore: ".$query);
        if (mysqli_num_rows($ris)>0)
        {
            $possibilecanc=false;
           //break;
        }
    }
    return $possibilecanc;
}